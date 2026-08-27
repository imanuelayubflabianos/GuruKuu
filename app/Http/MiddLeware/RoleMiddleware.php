<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        $user = $request->user();

        // ✅ IZINKAN ADMIN MASUK KE ROUTE SISWA jika punya flag "login_as_siswa"
        if ($role === 'siswa' && $user && $user->role === 'admin' && session('login_as_siswa')) {
            return $next($request);
        }

        // Cek standar: jika user tidak login atau role-nya tidak cocok
        if (!$user || $user->role !== $role) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin.');
        }

        return $next($request);
    }
}