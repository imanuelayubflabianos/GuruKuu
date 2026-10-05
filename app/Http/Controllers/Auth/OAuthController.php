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
            ?? $userData['external_id']
            ?? $userData['nik']
            ?? $userData['nisn']
            ?? ($userData['user']['external_id'] ?? $userData['user']['nis'] ?? $userData['user']['nip'] ?? null);

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

        // 7. Jika user lokal TIDAK ditemukan, lakukan auto-provisioning dari SiPintu
        if (!$user) {
            $identitas = $nis ?: ($email ?: ($username ?: 'Pengguna SiPintu'));
            Log::info("SSO SiPintu: Pengguna [{$identitas}] belum ada di lokal, mencoba sinkronisasi otomatis...");

            $isStudent = !empty($userData['classroom']) || !empty($userData['classroom_id']) || !empty($userData['nis']) || !empty($userData['nisn']) || (!empty($userData['role']) && strtolower($userData['role']) === 'siswa');
            $isTeacher = !empty($userData['nip']) || (!empty($userData['role']) && in_array(strtolower($userData['role']), ['guru', 'teacher']));

            if ($isStudent) {
                $syncRes = $this->siPintu->syncStudentToLocal($userData);
                if ($syncRes['success'] ?? false) {
                    $user = User::where('nis', (string) $nis)->orWhere('email', (string) $email)->first();
                }
            } elseif ($isTeacher) {
                $syncRes = $this->siPintu->syncTeacherToLocal($userData);
                if ($syncRes['success'] ?? false) {
                    $user = User::where('nis', (string) $nis)->orWhere('email', (string) $email)->first();
                }
            }

            // Fallback: coba cari di gateway dengan NIS / NIP
            if (!$user && !empty($nis)) {
                $studentData = $this->siPintu->getStudentByNis((string) $nis);
                if ($studentData) {
                    $syncRes = $this->siPintu->syncStudentToLocal($studentData);
                    if ($syncRes['success'] ?? false) {
                        $user = User::where('nis', (string) $nis)->first();
                    }
                }
                if (!$user) {
                    $teacherData = $this->siPintu->getTeacherByNip((string) $nis);
                    if ($teacherData) {
                        $syncRes = $this->siPintu->syncTeacherToLocal($teacherData);
                        if ($syncRes['success'] ?? false) {
                            $user = User::where('nis', (string) $nis)->first();
                        }
                    }
                }
            }

            if (!$user) {
                Log::info("SSO SiPintu: Pengguna [{$identitas}] tidak terdaftar dan gagal di-provisioning.");
                return redirect()->route('login')->withErrors([
                    'nis' => "Akun SiPintu Anda ({$identitas}) belum terdaftar pada aplikasi GuruKuu. Silakan hubungi Administrator sekolah.",
                ]);
            }
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

        // Tandai agar muncul pop-up konfirmasi beranda/dashboard KHUSUS masuk lewat SSO gateway SiPintu
        $ssoToken = uniqid('sipintu_', true);
        $request->session()->flash('show_welcome_landing_popup', true);
        $request->session()->flash('sso_entry_token', $ssoToken);

        // 11. Redirect ke dashboard yang sesuai (mencegah redirect loop)
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')->with('success', 'Selamat datang, Administrator! (Masuk via SiPintu)');
        } elseif ($user->role === 'guru') {
            return redirect()->route('guru.dashboard')->with('success', 'Selamat datang, ' . $user->name . '! (Masuk via SiPintu)');
        }

        return redirect()->route('siswa.dashboard')->with('success', 'Selamat datang, ' . $user->name . '! (Masuk via SiPintu)');
    }

    /**
     * Redirect pengguna ke SiPintu OAuth Authorization Server
     * GET /login/sipintu
     */
    public function redirectToSiPintu()
    {
        $baseUrl = rtrim(config('services.sipintu.base_url', 'https://sipintu.smkn1bangsri.sch.id'), '/');
        $clientId = config('services.sipintu.client_id');
        $redirectUri = urlencode($this->siPintu->getRedirectUri());

        $authUrl = "{$baseUrl}/oauth/authorize?client_id={$clientId}&redirect_uri={$redirectUri}&response_type=code&scope=";

        return redirect()->away($authUrl);
    }

    /**
     * Webhook Sinkronisasi Real-Time & Smart Conflict Resolution dari SiPintu Gateway
     * POST /api/sipintu/sync-user atau /sipintu/sync-user
     */
    public function syncUser(Request $request)
    {
        // 1. Verifikasi Signature HMAC SHA-256
        $signature = $request->header('X-SiPintu-Signature');
        $secret = config('services.sipintu.client_secret') ?: env('SIPINTU_CLIENT_SECRET');
        if ($secret && (! $signature || ! hash_equals(hash_hmac('sha256', $request->getContent(), $secret), $signature))) {
            return response()->json(['status' => 'error', 'message' => 'Invalid signature.'], 401);
        }

        $userData = $request->input('user') ?? $request->all();
        $previous = $request->input('previous', []);

        // 2. Cari User secara fleksibel (External ID, NIS, NIP, Username, atau Email)
        $user = null;
        if (! empty($userData['external_id'])) {
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'external_id')) {
                $user = User::where('external_id', $userData['external_id'])->first();
            }
            if (! $user) {
                $user = User::where('nis', (string) $userData['external_id'])->first();
            }
        }
        if (! $user && ! empty($userData['nis'])) {
            $user = User::where('nis', (string) $userData['nis'])->first();
        }
        if (! $user && ! empty($userData['nip'])) {
            $user = User::where('nis', (string) $userData['nip'])->first();
        }
        if (! $user && ! empty($userData['username'])) {
            $user = User::where('nis', (string) $userData['username'])->first();
        }
        if (! $user && ! empty($userData['email'])) {
            $user = User::where('email', $userData['email'])
                ->when(! empty($previous['email']), fn ($q) => $q->orWhere('email', $previous['email']))
                ->first();
        }

        $syncTime = now();

        // 2b. Handle event penonaktifan / penghapusan akun dari SiPintu
        if ($request->header('X-SiPintu-Event') === 'user.deleted' || $request->input('event') === 'user.deleted') {
            if ($user) {
                $user->update([
                    'is_active' => false,
                    'deactivated_reason' => 'Dinonaktifkan via sinkronisasi SiPintu Gateway',
                    'sipintu_last_synced_at' => $syncTime,
                ]);
                return response()->json(['status' => 'success', 'action' => 'deactivated', 'user_id' => $user->id]);
            }
            return response()->json(['status' => 'skipped', 'message' => 'User not found']);
        }

        // 3. Jika belum ada: Auto-provision akun baru
        if (! $user) {
            $role = 'siswa';
            if (! empty($userData['role'])) {
                $r = strtolower($userData['role']);
                if (in_array($r, ['guru', 'teacher'])) $role = 'guru';
                elseif ($r === 'admin') $role = 'admin';
            }

            $user = User::create([
                'name' => $userData['name'] ?? 'User',
                'email' => $userData['email'] ?? null,
                'role' => $role,
                'nis' => (string) ($userData['nis'] ?? $userData['nip'] ?? $userData['external_id'] ?? $userData['username'] ?? null),
                'is_active' => ($userData['status'] ?? 'active') === 'active',
                'password' => $userData['password'] ?? \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(32)),
                'sipintu_last_synced_at' => $syncTime,
            ]);

            // Pastikan password hash tersimpan murni (mencegah double-hashing oleh Laravel casts)
            if (! empty($userData['password'])) {
                \Illuminate\Support\Facades\DB::table('users')->where('id', $user->id)->update(['password' => $userData['password']]);
            }

            return response()->json(['status' => 'success', 'action' => 'created', 'user_id' => $user->id]);
        }

        // 4. Deteksi Perubahan Lokal Pengguna
        $hasLocalEdits = $user->sipintu_last_synced_at !== null && $user->updated_at->gt($user->sipintu_last_synced_at);
        $changedFields = (array) $request->input('changed_fields', []);

        // Field Selalu Mengikuti SiPintu (Source of Truth)
        $updateFields = [];
        if (! empty($userData['email'])) {
            $updateFields['email'] = $userData['email'];
        }
        if (! empty($userData['role'])) {
            $r = strtolower($userData['role']);
            if (in_array($r, ['siswa', 'student'])) $updateFields['role'] = 'siswa';
            elseif (in_array($r, ['guru', 'teacher'])) $updateFields['role'] = 'guru';
            elseif ($r === 'admin') $updateFields['role'] = 'admin';
        }
        if (isset($userData['status'])) {
            $updateFields['is_active'] = ($userData['status'] === 'active');
        }

        // Field Lokal: Ditimpa jika TIDAK ADA perubahan lokal, ATAU jika field baru saja diubah di SiPintu
        if (! $hasLocalEdits || in_array('name', $changedFields, true)) {
            if (isset($userData['name'])) $updateFields['name'] = $userData['name'];
        }
        if (! $hasLocalEdits || in_array('phone', $changedFields, true)) {
            if (isset($userData['phone']) && \Illuminate\Support\Facades\Schema::hasColumn('users', 'phone')) {
                $updateFields['phone'] = $userData['phone'];
            }
        }
        if (! $hasLocalEdits || in_array('classroom', $changedFields, true)) {
            if (isset($userData['classroom']) && \Illuminate\Support\Facades\Schema::hasColumn('users', 'kelas')) {
                $updateFields['kelas'] = $userData['classroom'];
            }
        }

        // 5. Update & Selaraskan Timestamp
        $updateFields['sipintu_last_synced_at'] = $syncTime;
        $user->fill($updateFields);
        $user->sipintu_last_synced_at = $syncTime;
        $user->updated_at = $syncTime;
        $user->save();

        // 6. SINKRONISASI PASSWORD: Gunakan DB::table() langsung agar TIDAK terkena cast 'hashed' (Mencegah Double-Hashing)
        if (! empty($userData['password'])) {
            \Illuminate\Support\Facades\DB::table('users')->where('id', $user->id)->update([
                'password' => $userData['password'],
            ]);
        }

        return response()->json(['status' => 'success', 'action' => 'updated', 'user_id' => $user->id]);
    }
}
