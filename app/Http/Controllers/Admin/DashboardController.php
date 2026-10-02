<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Penilaian;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

class DashboardController extends Controller
{
    public function index()
    {
        $totalGuru = Guru::count();
        $totalSiswa = User::where('role', 'siswa')->count();
        $totalPenilaian = Penilaian::count();
        $totalJurusan = Jurusan::count();
        $totalKelas = Kelas::count();
        
        $periodeAktif = Periode::where('status', 'aktif')->first();
        $topGuru = Guru::with('jurusan')
            ->withRatings()
            ->orderBy('rata_rata_nilai', 'desc')
            ->limit(3)
            ->get();
            
        // Ambil ulasan terbaru dari tabel penilaian (yang memiliki teks kritik atau saran dan tidak disensor)
        $feedbacks = Penilaian::with(['guru', 'siswa', 'kelas'])
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

    public function clearCache()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            \Illuminate\Support\Facades\Artisan::call('route:clear');
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            try {
                \Illuminate\Support\Facades\Cache::flush();
            } catch (\Throwable $e) {}

            return back()->with('success', 'Cache tampilan (view), rute, konfigurasi, dan sistem aplikasi berhasil dibersihkan dengan aman.');
        } catch (\Throwable $e) {
            return back()->with('warning', 'Sebagian cache dibersihkan, status: ' . $e->getMessage());
        }
    }
}
