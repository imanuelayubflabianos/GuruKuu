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
        $request->validate([
            'nis' => 'required|string',
            'login_role' => 'required|in:siswa,guru',
            'password' => 'required|string',
        ]);

        $nis = $request->nis;
        $password = $request->password;
        $role = $request->login_role;

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
                return back()->withErrors(['nis' => 'NIS tidak ditemukan. Pastikan data sudah disinkronisasi oleh Admin melalui menu Gateway SiPintu.'])->withInput();
            }

            if (!Hash::check($password, $user->password)) {
                return back()->withErrors(['password' => 'Password salah.'])->withInput();
            }

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
                if ($password !== 'eskasaba') {
                    return back()->withErrors(['password' => 'Password admin salah.'])->withInput();
                }

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
                return back()->withErrors(['nis' => 'NIP tidak ditemukan. Pastikan data sudah disinkronisasi oleh Admin melalui menu Gateway SiPintu.'])->withInput();
            }

            $user = User::where('nis', $nis)->where('role', 'guru')->first();

            if (!$user) {
                $user = User::create([
                    'name' => $guru->nama,
                    'nis' => $nis,
                    'email' => $nis . '@gurukuu.local',
                    'password' => Hash::make('password'), // Default password
                    'role' => 'guru',
                    'is_active' => true,
                ]);
            }

            if (!Hash::check($password, $user->password)) {
                return back()->withErrors(['password' => 'Password Guru salah.'])->withInput();
            }

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            return redirect()->intended(route('guru.dashboard'))
                ->with('success', 'Selamat datang, ' . $user->name . '!');
        }

        return back()->withErrors(['nis' => 'Peran login tidak valid.'])->withInput();
    }

    public function showGantiPassword()
    {
        return view('auth.ganti-password');
    }

    public function gantiPassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required|string',
            'password' => 'required|min:8|confirmed',
        ], [
            'old_password.required' => 'Password lama wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'Password lama salah.'])->withInput();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $dashboard = $user->role === 'admin' ? 'admin.dashboard' : 'guru.dashboard';

        return redirect()->route($dashboard)
            ->with('success', 'Password berhasil diubah!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing.index');
    }
}