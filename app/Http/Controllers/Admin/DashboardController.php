<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Penilaian;
use App\Models\Periode;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        $totalGuru = Guru::count();
        $totalSiswa = User::where('role', 'siswa')->count();
        $totalPenilaian = $periodeAktif ? Penilaian::where('periode_id', $periodeAktif->id)->count() : 0;
        $rataRataUmum = Penilaian::avg('total_nilai') ? round(Penilaian::avg('total_nilai') / 6, 2) : 0;

        // ✅ TOP GURU GLOBAL (RUMUS SAMA DENGAN LEADERBOARD = SINKRON)
        $topGuru = Guru::with('jurusan')
            ->where('total_penilaian', '>', 0)
            ->get()
            ->sortByDesc(fn($g) => ($g->rata_rata_nilai * 0.7) + ($g->rasio_penilaian * 0.3))
            ->values()->take(5);

        // Kritik terbaru
        $kritikTerbaru = Penilaian::whereNotNull('kritik')
            ->with(['guru', 'siswa'])->latest()->take(3)->get();

        // Penilaian terbaru
        $penilaianTerbaru = Penilaian::with(['guru', 'siswa'])->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'periodeAktif', 'totalGuru', 'totalSiswa', 'totalPenilaian',
            'rataRataUmum', 'topGuru', 'kritikTerbaru', 'penilaianTerbaru'
        ));
    }
}