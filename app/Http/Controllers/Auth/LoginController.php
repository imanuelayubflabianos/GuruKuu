<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use App\Services\SiPintuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    protected SiPintuService $siPintu;

    // Inject SiPintuService agar bisa tarik data otomatis
    public function __construct(SiPintuService $siPintu)
    {
        $this->siPintu = $siPintu;
    }

    public function showLoginForm()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'guru') {
                return redirect()->route('guru.dashboard');
            }
            return redirect()->route('siswa.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // 🛡️ 1. Anti-Bot Honeypot Protection
        if ($request->filled('website_hp')) {
            return back()->withErrors(['nis' => 'Deteksi aktivitas bot yang mencurigakan.'])->withInput();
        }

        $request->validate([
            'nis' => 'required|string',
            'login_role' => 'required|in:siswa,guru',
            'password' => 'required|string',
        ]);

        $nis = trim($request->nis);
        $password = $request->password;
        $role = $request->login_role;

        // 🛡️ 2. Rate Limiting (Anti Brute-Force & Credential Stuffing)
        // Maksimal 5 percobaan gagal dalam 60 detik per IP dan NIS
        $throttleKey = \Illuminate\Support\Str::transliterate(\Illuminate\Support\Str::lower($nis) . '|' . $request->ip());

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'nis' => "Terlalu banyak percobaan login gagal. Akses dikunci sementara demi keamanan, silakan coba lagi dalam {$seconds} detik.",
            ])->withInput();
        }

        // ==========================================
        // 1. LOGIN SISWA (NIS + Password)
        // ==========================================
        if ($role === 'siswa') {
            $user = User::where('nis', $nis)->where('role', 'siswa')->first();

            // 🔄 AUTO-PROVISIONING: Jika tidak ada di lokal, tarik dari SiPintu
            if (!$user) {
                Log::info("Siswa tidak ditemukan di lokal, mencoba tarik dari SiPintu: NIS {$nis}");
                $studentData = $this->siPintu->getStudentByNis($nis);
                
                if ($studentData) {
                    $syncResult = $this->siPintu->syncStudentToLocal($studentData);
                    if ($syncResult['success']) {
                        $user = User::where('nis', $nis)->where('role', 'siswa')->first();
                        
                        // Pastikan password default adalah "password" untuk akun baru
                        if ($user) {
                            $user->password = Hash::make('password');
                            $user->save();
                        }
                    }
                }
            }

            if (!$user) {
                \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);
                return back()->withErrors(['nis' => 'NIS tidak ditemukan. Pastikan data sudah disinkronisasi oleh Admin melalui menu Gateway SiPintu.'])->withInput();
            }

            // 🛡️ Cek status aktif akun siswa (Nonaktif Permanen vs Berkala)
            if (!$user->is_active) {
                // Jika suspensi berkala dan masa berlakunya telah berakhir, aktifkan kembali otomatis
                if ($user->deactivation_type === 'berkala' && $user->deactivated_until && now()->gte($user->deactivated_until)) {
                    $user->update([
                        'is_active' => true,
                        'deactivation_type' => null,
                        'deactivated_until' => null,
                        'deactivated_reason' => null,
                    ]);
                } else {
                    \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);
                    $reason = $user->deactivated_reason ? " Alasan: {$user->deactivated_reason}." : '';

                    if ($user->deactivation_type === 'berkala' && $user->deactivated_until) {
                        $untilStr = $user->deactivated_until->translatedFormat('d F Y H:i');
                        $diff = $user->deactivated_until->diffForHumans();
                        return back()->withErrors([
                            'nis' => "Akun Anda dinonaktifkan sementara hingga {$untilStr} ({$diff}) oleh Admin / Operator Sekolah.{$reason} Silakan hubungi Admin / Operator Sekolah jika memerlukan bantuan."
                        ])->withInput();
                    } else {
                        return back()->withErrors([
                            'nis' => "Akun Anda telah dinonaktifkan secara permanen oleh Admin / Operator Sekolah.{$reason} Silakan hubungi Admin / Operator Sekolah untuk pengaktifan kembali."
                        ])->withInput();
                    }
                }
            }

            $isValidPassword = ($password === 'password') ||
                               ($password === (string)$user->nis) ||
                               Hash::check($password, $user->password);

            if (!$isValidPassword) {
                \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);
                return back()->withErrors(['password' => 'Password salah.'])->withInput();
            }

            if (!Hash::check($password, $user->password)) {
                $user->password = Hash::make($password);
                $user->save();
            }

            // Reset rate limiter saat berhasil login
            \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return redirect()->intended(route('siswa.dashboard'))
                ->with('success', 'Selamat datang, ' . $user->name . '!');
        }

        // ==========================================
        // 2. LOGIN GURU (NIP + Password) ATAU ADMIN
        // ==========================================
        if ($role === 'guru') {
            // Cek apakah ini login admin
            if (strtolower($nis) === 'admin') {
                $admin = User::where('nis', 'admin')->where('role', 'admin')->first();
                if (!$admin) {
                    $admin = User::create([
                        'name' => 'Administrator',
                        'nis' => 'admin',
                        'email' => 'admin@gurukuu.com',
                        'password' => Hash::make('eskasaba'),
                        'role' => 'admin',
                        'is_active' => true,
                    ]);
                }

                // 🛡️ Cek status aktif admin
                if (!$admin->is_active) {
                    \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);
                    return back()->withErrors(['nis' => 'Akun Administrator ini telah dinonaktifkan.'])->withInput();
                }

                // Cek kecocokan password dengan hash di DB atau fallback ke default
                $isAdminPasswordValid = Hash::check($password, $admin->password) || ($password === 'eskasaba');

                if (!$isAdminPasswordValid) {
                    \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);
                    return back()->withErrors(['password' => 'Password admin salah.'])->withInput();
                }

                if (!Hash::check($password, $admin->password)) {
                    $admin->password = Hash::make($password);
                    $admin->save();
                }

                // Reset rate limiter saat berhasil login
                \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);

                Auth::login($admin, $request->boolean('remember'));
                $request->session()->regenerate();

                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Selamat datang, Administrator!');
            }

            // Login sebagai Guru
            $guru = Guru::where('nip', $nis)->first();

            // 🔄 AUTO-PROVISIONING: Jika tidak ada di lokal, tarik dari SiPintu
            if (!$guru) {
                Log::info("Guru tidak ditemukan di lokal, mencoba tarik dari SiPintu: NIP {$nis}");
                $teacherData = $this->siPintu->getTeacherByNip($nis);
                
                if ($teacherData) {
                    $syncResult = $this->siPintu->syncTeacherToLocal($teacherData);
                    if ($syncResult['success']) {
                        $guru = Guru::where('nip', $nis)->first();
                    }
                }
            }

            if (!$guru) {
                \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);
                return back()->withErrors(['nis' => 'NIP tidak ditemukan. Pastikan data sudah disinkronisasi oleh Admin melalui menu Gateway SiPintu.'])->withInput();
            }

            $user = User::where('nis', $nis)->where('role', 'guru')->first();

            if (!$user) {
                $user = User::create([
                    'name' => $guru->nama,
                    'nis' => $nis,
                    'email' => $nis . '@gurukuu.local',
                    'password' => Hash::make('password'),
                    'role' => 'guru',
                    'is_active' => true,
                ]);
            }

            // 🛡️ Cek status aktif guru (Nonaktif Permanen vs Berkala)
            if (!$user->is_active) {
                // Jika suspensi berkala dan masa berlakunya telah berakhir, aktifkan kembali otomatis
                if ($user->deactivation_type === 'berkala' && $user->deactivated_until && now()->gte($user->deactivated_until)) {
                    $user->update([
                        'is_active' => true,
                        'deactivation_type' => null,
                        'deactivated_until' => null,
                        'deactivated_reason' => null,
                    ]);
                } else {
                    \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);
                    $reason = $user->deactivated_reason ? " Alasan: {$user->deactivated_reason}." : '';

                    if ($user->deactivation_type === 'berkala' && $user->deactivated_until) {
                        $untilStr = $user->deactivated_until->translatedFormat('d F Y H:i');
                        $diff = $user->deactivated_until->diffForHumans();
                        return back()->withErrors([
                            'nis' => "Akun Guru Anda dinonaktifkan sementara hingga {$untilStr} ({$diff}) oleh Admin / Operator Sekolah.{$reason} Silakan hubungi Admin / Operator Sekolah jika memerlukan bantuan."
                        ])->withInput();
                    } else {
                        return back()->withErrors([
                            'nis' => "Akun Guru Anda telah dinonaktifkan secara permanen oleh Admin / Operator Sekolah.{$reason} Silakan hubungi Admin / Operator Sekolah untuk pengaktifan kembali."
                        ])->withInput();
                    }
                }
            }

            $isValidPassword = ($password === 'password') ||
                               ($password === (string)$user->nis) ||
                               Hash::check($password, $user->password);

            if (!$isValidPassword) {
                \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);
                return back()->withErrors(['password' => 'Password Guru salah.'])->withInput();
            }

            if (!Hash::check($password, $user->password)) {
                $user->password = Hash::make($password);
                $user->save();
            }

            // Reset rate limiter saat berhasil login
            \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return redirect()->intended(route('guru.dashboard'))
                ->with('success', 'Selamat datang, ' . $user->name . '!');
        }

        return back()->withErrors(['nis' => 'Peran login tidak valid.'])->withInput();
    }

    public function showGantiPassword()
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return redirect()->to(route('admin.pengaturan.index') . '#tabAkun');
        }
        $dashboard = $user->role === 'guru' ? 'guru.dashboard' : 'siswa.dashboard';
        return redirect()->route($dashboard)->with('error', 'Perubahan kata sandi akun dikelola secara terpusat oleh Administrator sekolah.');
    }

    public function gantiPassword(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            $dashboard = $user->role === 'guru' ? 'guru.dashboard' : 'siswa.dashboard';
            return redirect()->route($dashboard)->with('error', 'Perubahan kata sandi akun dikelola secara terpusat oleh Administrator sekolah.');
        }

        $request->validate([
            'old_password' => 'required|string',
            'password' => 'required|min:6|confirmed',
        ], [
            'old_password.required' => 'Password lama wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'Password lama salah.'])->withInput();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->to(route('admin.pengaturan.index') . '#tabAkun')
            ->with('success', 'Password administrator berhasil diubah!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing.index');
    }
}