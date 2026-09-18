<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Penilaian;
use App\Models\Periode;
use App\Models\Setting;
use App\Models\User;
use App\Services\SiPintuService;

class DashboardController extends Controller
{
    public function index(SiPintuService $siPintu)
    {
        $totalGuru = Guru::count();
        $totalSiswa = User::where('role', 'siswa')->count();
        $totalPenilaian = Penilaian::count();
        $totalJurusan = Jurusan::count();
        $totalKelas = Kelas::count();
        
        $periodeAktif = Periode::where('status', 'aktif')->first();
        $heroThumbnail = Setting::get('hero_image', 'https://images.unsplash.com/photo-1562774053-701939374585?w=1920');
        $heroTitle = Setting::get('hero_title', 'Bangun Sekolah yang Lebih Baik Melalui Penilaian Guru yang Objektif');

        // Cache status ping selama 60 detik agar tidak membebani loading admin dashboard (0ms latency)
        $ping = \Illuminate\Support\Facades\Cache::remember('sipintu_gateway_ping', 60, function () use ($siPintu) {
            return $siPintu->ping();
        });

        $topGuru = Guru::with('jurusan')
            ->where('total_penilaian', '>', 0)
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
            'heroThumbnail',
            'heroTitle',
            'ping',
            'topGuru', 
            'feedbacks'
        ));
    }
}