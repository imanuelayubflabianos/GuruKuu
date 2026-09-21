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
            
        // Ambil ulasan terbaru dari tabel penilaian (yang memiliki teks kritik atau saran)
        $feedbacks = Penilaian::with(['guru', 'siswa', 'kelas'])
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('kritik')->whereRaw("TRIM(kritik) != ''");
                })->orWhere(function ($q) {
                    $q->whereNotNull('saran')->whereRaw("TRIM(saran) != ''");
                });
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
        Artisan::call('optimize:clear');

        return back()->with('success', 'Cache aplikasi, konfigurasi, route, dan view berhasil dibersihkan.');
    }
}
