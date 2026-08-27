<?php
// app/Http/Middleware/CheckPeriodeActive.php

namespace App\Http\Middleware;

use App\Models\Periode;
use Closure;
use Illuminate\Http\Request;

class CheckPeriodeActive
{
    public function handle(Request $request, Closure $next)
    {
        if (! Periode::getActivePeriode()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada periode penilaian yang aktif saat ini.',
                ], 403);
            }

            return redirect()->route('siswa.dashboard')
                ->with('error', 'Tidak ada periode penilaian yang aktif saat ini.');
        }

        return $next($request);
    }
}