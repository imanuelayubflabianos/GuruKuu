<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('landing.index');
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

        // ✅ TAB ADMIN: Harus punya role admin di database
        if ($request->login_role === 'admin') {
            if ($user->role !== 'admin') {
                return back()->withErrors(['nis' => 'NIS ini bukan admin. Gunakan tab Siswa.'])->withInput();
            }
            if (!$request->password || !Hash::check($request->password, $user->password)) {
                return back()->withErrors(['password' => 'Password salah.'])->withInput();
            }
            
            Auth::login($user, $request->boolean('remember'));
            $request->session()->forget('login_as_siswa'); // Hapus flag siswa
            $request->session()->regenerate();
            
            return redirect()->route('landing.index')->with('success', 'Login sebagai Admin berhasil!');
        }

        // ✅ TAB SISWA: Boleh siapa saja (termasuk admin yang ingin login sebagai siswa)
        // Tidak ada validasi role di sini
        Auth::login($user, $request->boolean('remember'));
        
        // ✅ Set flag "login sebagai siswa" agar middleware mengizinkan admin masuk ke route siswa
        $request->session()->put('login_as_siswa', true);
        $request->session()->regenerate();
        
        return redirect()->route('landing.index')->with('success', 'Login sebagai Siswa berhasil!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Cookie::queue(Cookie::forget('laravel_session'));
        Cookie::queue(Cookie::forget('XSRF-TOKEN'));
        
        return redirect()->route('landing.index')->with('success', 'Anda telah keluar.');
    }
}