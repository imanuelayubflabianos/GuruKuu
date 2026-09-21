<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Penilaian;
use App\Models\Periode;
use App\Models\Pelanggaran;

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

        // Semua Guru di sekolah
        $semuaGuru = \App\Models\Guru::with('jurusan')->withCount('penilaian')->get();
        $guruDiKelas = $semuaGuru; // Kompatibel dengan view
        $guruNormada = $semuaGuru->where('kategori', 'normada');
        $guruProduktif = $semuaGuru->where('kategori', 'produktif');

        // ✅ TOP GURU SEKOLAH
        $topGuruRated = $semuaGuru->filter(fn ($guru) => $guru->penilaian_count > 0)
            ->sortByDesc(fn($g) => ($g->rata_rata_nilai * 0.7) + ($g->rasio_penilaian * 0.3))
            ->values()->take(3);
        
        $topGuru = $topGuruRated;

        // Status penilaian siswa
        $sudahDinilai = $periodeAktif
            ? Penilaian::where('siswa_id', $user->id)->where('periode_id', $periodeAktif->id)->pluck('guru_id')->toArray()
            : [];
        $totalGuru = $semuaGuru->count();
        $jumlahSudah = count($sudahDinilai);

        // Riwayat terakhir
        $riwayatTerakhir = Penilaian::where('siswa_id', $user->id)
            ->with('guru')->latest()->take(3)->get();

        $notifikasiPelanggaran = Pelanggaran::where('user_id', $user->id)
            ->where('siswa_is_read', false)
            ->latest()
            ->take(5)
            ->get();

        return view('siswa.dashboard', compact(
            'periodeAktif', 'kelasAktif', 'guruDiKelas', 'guruNormada', 'guruProduktif',
            'topGuru', 'sudahDinilai', 'totalGuru', 'jumlahSudah', 'riwayatTerakhir',
            'notifikasiPelanggaran'
        ));
    }
}