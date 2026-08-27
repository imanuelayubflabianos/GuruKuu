<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Penilaian;
use App\Models\Periode;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $periodeAktif = Periode::where('status', 'aktif')->first();

        // Kelas aktif siswa
        $kelasAktif = $periodeAktif
            ? $user->kelas()->wherePivot('tahun_ajaran', $periodeAktif->tahun_ajaran)->first()
            : null;

        // Guru di kelas siswa
        $guruDiKelas = $kelasAktif ? $kelasAktif->guru()->with('jurusan')->get() : collect();
        $guruNormada = $guruDiKelas->where('kategori', 'normada');
        $guruProduktif = $guruDiKelas->where('kategori', 'produktif');

        // ✅ TOP GURU DI KELAS (RUMUS SAMA DENGAN LEADERBOARD = SINKRON)
        $topGuru = $guruDiKelas->where('total_penilaian', '>', 0)
            ->sortByDesc(fn($g) => ($g->rata_rata_nilai * 0.7) + ($g->rasio_penilaian * 0.3))
            ->values()->take(3);

        // Status penilaian siswa
        $sudahDinilai = $periodeAktif
            ? Penilaian::where('siswa_id', $user->id)->where('periode_id', $periodeAktif->id)->pluck('guru_id')->toArray()
            : [];
        $totalGuru = $guruDiKelas->count();
        $jumlahSudah = count($sudahDinilai);

        // Riwayat terakhir
        $riwayatTerakhir = Penilaian::where('siswa_id', $user->id)
            ->with('guru')->latest()->take(3)->get();

        return view('siswa.dashboard', compact(
            'periodeAktif', 'kelasAktif', 'guruDiKelas', 'guruNormada', 'guruProduktif',
            'topGuru', 'sudahDinilai', 'totalGuru', 'jumlahSudah', 'riwayatTerakhir'
        ));
    }
}