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

        if (!$user) {
            return redirect()->route('login');
        }

        // 🛡️ Pencegahan Akses Akun yang Dinonaktifkan (Session Invalidation)
        if (!$user->is_active) {
            \Illuminate\Support\Facades\Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'nis' => 'Sesi berakhir. Akun Anda telah dinonaktifkan oleh Administrator sekolah.'
            ]);
        }

        if ($user->role !== $role) {
            $targetDashboard = match ($user->role) {
                'admin' => route('admin.dashboard'),
                'guru' => route('guru.dashboard'),
                'siswa' => route('siswa.dashboard'),
                default => route('landing.index'),
            };

            return redirect($targetDashboard)->with('error', 'Akses dialihkan. Anda saat ini aktif sebagai ' . ucfirst($user->role) . ', bukan ' . ucfirst($role) . '. Silakan logout terlebih dahulu jika ingin mengganti akun.');
        }

        return $next($request);
    }
}