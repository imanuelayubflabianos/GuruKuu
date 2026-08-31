<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();
        $loginAsSiswa = session('login_as_siswa', false);

        // Jika route membutuhkan role 'admin'
        if ($role === 'admin') {
            // Jika user adalah admin DAN TIDAK dalam mode siswa, izinkan
            if ($user && $user->role === 'admin' && !$loginAsSiswa) {
                return $next($request);
            }
            // Jika user adalah admin TAPI dalam mode siswa, alihkan ke dashboard siswa
            if ($user && $user->role === 'admin' && $loginAsSiswa) {
                return redirect()->route('siswa.dashboard')->with('error', 'Anda sedang login dalam mode Siswa. Silakan Logout terlebih dahulu untuk mengakses halaman Admin.');
            }
            abort(403, 'Akses ditolak.');
        }

        // Jika route membutuhkan role 'siswa'
        if ($role === 'siswa') {
            // Izinkan jika user adalah siswa, ATAU admin yang sedang dalam mode siswa
            if ($user && ($user->role === 'siswa' || ($user->role === 'admin' && $loginAsSiswa))) {
                return $next($request);
            }
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}