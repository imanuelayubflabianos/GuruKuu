<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            // Jika sudah login, redirect ke dashboard yang sesuai
            if (session('login_as_siswa')) {
                return redirect()->route('siswa.dashboard');
            }
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nis' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'login_role' => 'required|in:siswa,admin',
            'password' => 'nullable|string',
        ]);

        $user = User::where('nis', $request->nis)->first();

        if (!$user) {
            return back()->withErrors(['nis' => 'NIS tidak ditemukan.'])->withInput();
        }

        if (!$user->tanggal_lahir || $user->tanggal_lahir->format('Y-m-d') !== $request->tanggal_lahir) {
            return back()->withErrors(['tanggal_lahir' => 'Tanggal lahir tidak cocok.'])->withInput();
        }

        // Login sebagai admin
        if ($request->login_role === 'admin') {
            if ($user->role !== 'admin') {
                return back()->withErrors(['nis' => 'NIS ini bukan admin. Gunakan tab Siswa.'])->withInput();
            }
            if (!$request->password || !Hash::check($request->password, $user->password)) {
                return back()->withErrors(['password' => 'Password salah.'])->withInput();
            }
            
            Auth::login($user, $request->boolean('remember'));
            $request->session()->forget('login_as_siswa');
            $request->session()->regenerate();
            
            // Redirect langsung ke admin dashboard
            return redirect()->intended(route('admin.dashboard'));
        }

        // Login sebagai siswa
        if ($user->role !== 'siswa' && $user->role !== 'admin') {
            return back()->withErrors(['nis' => 'NIS ini tidak valid.'])->withInput();
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->put('login_as_siswa', true);
        $request->session()->regenerate();
        
        // Redirect langsung ke siswa dashboard
        return redirect()->intended(route('siswa.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Redirect ke landing page
        return redirect()->route('landing.index');
    }
}