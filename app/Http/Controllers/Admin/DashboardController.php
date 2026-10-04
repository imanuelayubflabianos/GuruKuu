<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Penilaian;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DashboardController extends Controller
{
    public function index()
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        $totalGuru = Guru::count();
        $totalSiswa = User::where('role', 'siswa')->count();
        $totalPenilaian = $periodeAktif ? Penilaian::where('periode_id', $periodeAktif->id)->count() : Penilaian::count();
        $totalJurusan = Jurusan::count();
        $totalKelas = Kelas::count();
        
        $topGuru = Guru::leaderboardFor('rating', null, $periodeAktif?->id)->take(3);
            
        // Ambil ulasan terbaru dari tabel penilaian (yang memiliki teks kritik atau saran dan tidak disensor)
        $feedbacks = Penilaian::with(['guru', 'siswa', 'kelas'])
            ->when($periodeAktif, fn($q) => $q->where('periode_id', $periodeAktif->id))
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('kritik')->whereRaw("TRIM(kritik) != ''");
                })->orWhere(function ($q) {
                    $q->whereNotNull('saran')->whereRaw("TRIM(saran) != ''");
                });
            })
            ->where(function ($query) {
                $query->where('is_censored', false)->orWhereNull('is_censored');
            })
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalGuru', 
            'totalSiswa', 
            'totalPenilaian', 
            'totalJurusan',
            'totalKelas',
            'periodeAktif',
            'topGuru', 
            'feedbacks'
        ));
    }

    public function clearCache(Request $request)
    {
        $scope = $request->input('scope', 'all');
        $details = [];

        try {
            // 1. Bersihkan Cache Laravel jika scope all atau cache
            if (in_array($scope, ['all', 'cache'])) {
                Artisan::call('view:clear');
                Artisan::call('route:clear');
                Artisan::call('config:clear');
                Artisan::call('cache:clear');
                try {
                    \Illuminate\Support\Facades\Cache::flush();
                } catch (\Throwable $e) {}
                $details[] = 'Cache tampilan, rute, konfigurasi & data aplikasi';
            }

            // 2. Bersihkan File Log Server (storage/logs/*.log) jika scope all atau logs
            if (in_array($scope, ['all', 'logs'])) {
                $freedBytes = 0;
                $logFiles = glob(storage_path('logs/*.log'));
                if (!empty($logFiles)) {
                    foreach ($logFiles as $file) {
                        if (is_file($file)) {
                            $freedBytes += filesize($file);
                            @file_put_contents($file, '');
                        }
                    }
                }
                $sizeKb = round($freedBytes / 1024, 1);
                $details[] = "File log server dikosongkan ({$sizeKb} KB dilepas)";
            }

            // 3. Bersihkan Sesi Kedaluwarsa & Riwayat Usang jika scope all atau sessions
            if (in_array($scope, ['all', 'sessions'])) {
                if (\Illuminate\Support\Facades\Schema::hasTable('sessions')) {
                    $lifetime = config('session.lifetime', 120) * 60;
                    $deletedSessions = \Illuminate\Support\Facades\DB::table('sessions')
                        ->where('last_activity', '<', time() - $lifetime)
                        ->delete();
                    if ($deletedSessions > 0) {
                        $details[] = "{$deletedSessions} sesi kedaluwarsa dibersihkan";
                    }
                }

                if (\Illuminate\Support\Facades\Schema::hasTable('login_histories')) {
                    $deletedHistories = \App\Models\LoginHistory::where('is_active', false)
                        ->where('last_activity', '<', now()->subDays(7))
                        ->delete();
                    if ($deletedHistories > 0) {
                        $details[] = "{$deletedHistories} riwayat login lama (> 7 hari) dibersihkan";
                    }
                }
            }

            $message = 'Pembersihan berhasil: ' . implode(', ', $details) . '.';
            return back()->with('success', $message);
        } catch (\Throwable $e) {
            return back()->with('warning', 'Pembersihan selesai dengan catatan: ' . $e->getMessage());
        }
    }
}
