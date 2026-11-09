<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
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

        $allowedRoles = [];
        foreach ($roles as $roleParam) {
            foreach (explode(',', $roleParam) as $r) {
                $allowedRoles[] = trim($r);
            }
        }

        if (!in_array($user->role, $allowedRoles)) {
            $targetDashboard = match ($user->role) {
                'admin', 'wakil' => route('admin.dashboard'),
                'guru' => route('guru.dashboard'),
                'siswa' => route('siswa.dashboard'),
                default => route('landing.index'),
            };

            return redirect($targetDashboard)->with('error', 'Akses dialihkan. Anda saat ini aktif sebagai ' . ucfirst($user->role) . '. Anda tidak memiliki izin untuk mengakses halaman tersebut.');
        }

        return $next($request);
    }
}
