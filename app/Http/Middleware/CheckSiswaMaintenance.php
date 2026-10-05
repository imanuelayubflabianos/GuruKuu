<?php

namespace App\Http\Middleware;

use App\Services\MaintenanceService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSiswaMaintenance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role === 'siswa') {
            if (MaintenanceService::isSiswaMaintenanceActive()) {
                $isInputAttempt = false;

                // Cek rute khusus pembuatan penilaian
                if ($request->routeIs('siswa.penilaian.*') || $request->routeIs('siswa.riwayat.destroy')) {
                    $isInputAttempt = true;
                }

                // Cek rute chat dan kontak
                if ($request->routeIs('siswa.pengaturan.chat') || 
                    ($request->routeIs('siswa.kontak.*') && !in_array($request->route()->getName(), ['siswa.kontak.index', 'siswa.kontak.stream']))) {
                    $isInputAttempt = true;
                }

                // Cek metode penulisan data (POST, PUT, PATCH, DELETE) kecuali baca notifikasi
                if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']) && 
                    !$request->routeIs('siswa.notifikasi.read')) {
                    $isInputAttempt = true;
                }

                if ($isInputAttempt) {
                    $info = MaintenanceService::getSiswaMaintenanceInfo();

                    if ($request->expectsJson() || $request->ajax()) {
                        return response()->json([
                            'success'       => false,
                            'maintenance'   => true,
                            'message'       => $info['message'],
                            'schedule_text' => $info['schedule_text'],
                            'info'          => $info,
                        ], 423);
                    }

                    return redirect()->route('siswa.dashboard')->with('maintenance_popup', $info);
                }
            }
        }

        return $next($request);
    }
}
