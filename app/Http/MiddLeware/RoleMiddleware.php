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
        
        // Jika belum login, lempar ke halaman login
        if (!$user) {
            return redirect()->route('login');
        }

        // Cek apakah role user sesuai dengan yang diminta
        if ($user->role !== $role) {
            abort(403, 'Akses ditolak. Anda bukan ' . ucfirst($role) . '.');
        }

        return $next($request);
    }
}