<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SiPintuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OAuthController extends Controller
{
    protected SiPintuService $siPintu;

    public function __construct(SiPintuService $siPintu)
    {
        $this->siPintu = $siPintu;
    }

    /**
     * Handle OAuth callback from SiPintu SSO
     * GET /oauth/callback
     */
    public function callback(Request $request)
    {
        // 1. Cek apakah ada error dari server OAuth SiPintu
        if ($request->has('error')) {
            $errorDesc = $request->query('error_description', $request->query('error'));
            Log::warning('SiPintu SSO callback returned error: ' . $errorDesc);
            return redirect()->route('login')->withErrors([
                'nis' => 'Login SSO SiPintu dibatalkan atau ditolak: ' . $errorDesc,
            ]);
        }

        // 2. Ambil authorization code
        $code = $request->query('code');
        if (empty($code)) {
            return redirect()->route('login')->withErrors([
                'nis' => 'Authorization code SiPintu tidak ditemukan pada callback.',
            ]);
        }

        // 3. Pencegahan Authorization Code Reuse / Replay Attack
        $codeHash = 'sso_code_' . hash('sha256', $code);
        $alreadyUsed = false;
        try {
            if (Cache::has($codeHash)) {
                $alreadyUsed = true;
            } else {
                Cache::put($codeHash, true, now()->addMinutes(10));
            }
        } catch (\Throwable $e) {
            try {
                if (Cache::store('file')->has($codeHash)) {
                    $alreadyUsed = true;
                } else {
                    Cache::store('file')->put($codeHash, true, now()->addMinutes(10));
                }
            } catch (\Throwable $ex) {
                // Abaikan jika cache offline, server OAuth tetap akan menolak code yang sudah expired/used
            }
        }

        if ($alreadyUsed) {
            return redirect()->route('login')->withErrors([
                'nis' => 'Kode otorisasi SSO sudah pernah digunakan atau kedaluwarsa. Silakan ulangi login melalui portal SiPintu.',
            ]);
        }

        // 4. Tukar authorization code ke SiPintu untuk mendapatkan access token
        $redirectUri = $this->siPintu->getRedirectUri();
        $tokenResult = $this->siPintu->exchangeAuthorizationCode($code, $redirectUri);

        if (!$tokenResult['success'] || empty($tokenResult['access_token'])) {
            return redirect()->route('login')->withErrors([
                'nis' => 'Gagal mendapatkan akses token dari SiPintu: ' . ($tokenResult['message'] ?? 'Respons tidak valid.'),
            ]);
        }

        $accessToken = $tokenResult['access_token'];

        // 5. Ambil data user dari endpoint /api/v1/user
        $userResult = $this->siPintu->getUserProfile($accessToken);

        // Jangan simpan access token di manapun setelah request ini selesai
        unset($accessToken);

        if (!$userResult['success'] || empty($userResult['data'])) {
            return redirect()->route('login')->withErrors([
                'nis' => 'Gagal mengambil data pengguna dari SiPintu: ' . ($userResult['message'] ?? 'Data tidak tersedia.'),
            ]);
        }

        $userData = $userResult['data'];

        // 6. Cocokkan data user SiPintu dengan database lokal (User model)
        $nis = $userData['nis']
            ?? $userData['nip']
            ?? $userData['nomor_induk']
            ?? $userData['nik']
            ?? $userData['nisn']
            ?? ($userData['user']['nis'] ?? $userData['user']['nip'] ?? null);

        $email = $userData['email']
            ?? ($userData['user']['email'] ?? null);

        $username = $userData['username']
            ?? ($userData['user']['username'] ?? null);

        $user = null;

        // 6.1 Cari berdasarkan NIS / NIP / nomor identitas
        if (!empty($nis)) {
            $user = User::where('nis', (string) $nis)->first();
        }

        // 6.2 Cari berdasarkan Email
        if (!$user && !empty($email)) {
            $user = User::where('email', (string) $email)->first();
        }

        // 6.3 Cari berdasarkan username / admin
        if (!$user && !empty($username)) {
            if (strtolower($username) === 'admin') {
                $user = User::where('nis', 'admin')->where('role', 'admin')->first();
            } else {
                $user = User::where('nis', (string) $username)->first();
            }
        }

        // 7. Jika user lokal TIDAK ditemukan, tolak login SSO
        if (!$user) {
            $identitas = $nis ?: ($email ?: ($username ?: 'Pengguna SiPintu'));
            Log::info("SSO SiPintu: Pengguna [{$identitas}] tidak terdaftar di database lokal GuruKuu.");
            return redirect()->route('login')->withErrors([
                'nis' => "Akun SiPintu Anda ({$identitas}) belum terdaftar pada aplikasi GuruKuu. Silakan hubungi Administrator sekolah.",
            ]);
        }

        // 8. Cek status aktif akun (apakah disuspensi / nonaktif)
        if (!$user->is_active) {
            // Jika suspensi berkala dan waktu suspensi telah berakhir, pulihkan
            if ($user->deactivation_type === 'berkala' && $user->deactivated_until && now()->gte($user->deactivated_until)) {
                $user->update([
                    'is_active' => true,
                    'deactivation_type' => null,
                    'deactivated_until' => null,
                    'deactivated_reason' => null,
                ]);
            } else {
                $reason = $user->deactivated_reason ? " Alasan: {$user->deactivated_reason}." : '';
                if ($user->deactivation_type === 'berkala' && $user->deactivated_until) {
                    $untilStr = $user->deactivated_until->translatedFormat('d F Y H:i');
                    return redirect()->route('login')->withErrors([
                        'nis' => "Akun Anda dinonaktifkan sementara hingga {$untilStr} oleh Administrator sekolah.{$reason}",
                    ]);
                } else {
                    return redirect()->route('login')->withErrors([
                        'nis' => "Akun Anda telah dinonaktifkan secara permanen oleh Administrator sekolah.{$reason}",
                    ]);
                }
            }
        }

        // 9. Login ke session lokal menggunakan Auth::login($user, true)
        Auth::login($user, true);

        // 10. Regenerasi session untuk mencegah session fixation & aman
        $request->session()->regenerate();

        Log::info("SSO SiPintu berhasil untuk user ID: {$user->id}, Role: {$user->role}, NIS/NIP: {$user->nis}");

        // 11. Redirect ke dashboard yang sesuai (mencegah redirect loop)
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')->with('success', 'Selamat datang, Administrator! (Masuk via SiPintu)');
        } elseif ($user->role === 'guru') {
            return redirect()->route('guru.dashboard')->with('success', 'Selamat datang, ' . $user->name . '! (Masuk via SiPintu)');
        }

        return redirect()->route('siswa.dashboard')->with('success', 'Selamat datang, ' . $user->name . '! (Masuk via SiPintu)');
    }
}
