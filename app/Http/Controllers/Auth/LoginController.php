<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if (!empty($user->force_change_password)) {
                return redirect()->route('auth.ganti-password');
            }
            
            if ($user->role === 'guru') {
                return redirect()->route('guru.dashboard');
            }
            if ($user->role === 'admin' && !session('login_as_siswa')) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('siswa.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nis' => 'required|string',
            'login_role' => 'required|in:siswa,admin,guru',
            'tanggal_lahir' => 'nullable|date',
            'password' => 'nullable|string',
        ]);

        $nis = $request->nis;

        if ($request->login_role === 'guru') {
            return $this->handleGuruLogin($nis, $request);
        }

        if ($request->login_role === 'admin') {
            return $this->handleAdminLogin($nis, $request);
        }

        if ($request->login_role === 'siswa') {
            return $this->handleSiswaLogin($nis, $request);
        }

        return back()->withErrors(['nis' => 'Peran login tidak valid.'])->withInput();
    }

    /**
     * Handle login Guru - Auto-create user dari tabel guru jika belum ada
     */
    protected function handleGuruLogin($nis, Request $request)
    {
        // 1. Cek apakah user sudah ada di tabel users
        $user = User::where('nis', $nis)->where('role', 'guru')->first();

        // 2. Jika belum ada, ambil data dari tabel guru dan buat user otomatis
        if (!$user) {
            $guru = Guru::where('nip', $nis)->first();
            
            if (!$guru) {
                return back()->withErrors(['nis' => 'NIP / NIY tidak ditemukan di data Guru.'])->withInput();
            }

            // Buat akun user baru secara otomatis
            $user = User::create([
                'name' => $guru->nama,
                'nis' => $nis,
                'email' => $guru->email ?? ($nis . '@gurukuu.local'),
                'password' => Hash::make('password'), // Password default
                'role' => 'guru',
                'tanggal_lahir' => '1970-01-01',
                'force_change_password' => true,
                'is_active' => true,
            ]);
        }

        // 3. Validasi password
        if (!$request->password || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password Guru salah.'])->withInput();
        }

        // 4. Proses login
        Auth::login($user, $request->boolean('remember'));
        $request->session()->forget('login_as_siswa');
        $request->session()->regenerate();

        return $this->handlePostLoginRedirect($user);
    }

    protected function handleAdminLogin($nis, Request $request)
    {
        $user = User::where('nis', $nis)->where('role', 'admin')->first();

        if (!$user) {
            return back()->withErrors(['nis' => 'NIS/Akun ini bukan akun Admin.'])->withInput();
        }

        if (!$request->password || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password Admin salah.'])->withInput();
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->forget('login_as_siswa');
        $request->session()->regenerate();

        return $this->handlePostLoginRedirect($user);
    }

    protected function handleSiswaLogin($nis, Request $request)
    {
        $user = User::where('nis', $nis)->whereIn('role', ['siswa', 'admin'])->first();

        if (!$user) {
            return back()->withErrors(['nis' => 'NIS ini tidak terdaftar sebagai Siswa.'])->withInput();
        }

        if (!$request->tanggal_lahir || !$user->tanggal_lahir || 
            $user->tanggal_lahir->format('Y-m-d') !== $request->tanggal_lahir) {
            return back()->withErrors(['tanggal_lahir' => 'Tanggal lahir tidak cocok.'])->withInput();
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->put('login_as_siswa', true);
        $request->session()->regenerate();

        return $this->handlePostLoginRedirect($user);
    }

    protected function handlePostLoginRedirect($user)
    {
        if (!empty($user->force_change_password)) {
            return redirect()->route('auth.ganti-password');
        }

        if ($user->role === 'guru') {
            return redirect()->route('guru.dashboard');
        } elseif ($user->role === 'admin' && !session('login_as_siswa')) {
            return redirect()->route('admin.dashboard');
        }
        
        return redirect()->route('siswa.dashboard');
    }

    public function showGantiPassword()
    {
        $user = Auth::user();
        if (!$user || empty($user->force_change_password)) {
            return $this->handlePostLoginRedirect($user);
        }
        return view('auth.ganti-password');
    }

    public function gantiPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->force_change_password = false;
        $user->save();

        $dashboardRoute = $user->role === 'guru' ? 'guru.dashboard' : 
                         ($user->role === 'admin' && !session('login_as_siswa') ? 'admin.dashboard' : 'siswa.dashboard');

        return redirect()->route($dashboardRoute)->with('success', 'Password berhasil diubah. Selamat datang, ' . $user->name . '!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('landing.index');
    }
}