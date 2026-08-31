<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Penilaian;
use App\Models\Periode;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $periodeAktif = Periode::where('status', 'aktif')->first();
        $periodeId = $periodeAktif?->id;

        $filterKategori = $request->get('kategori', 'semua');
        $filterKelasId = $request->get('kelas_id', null);

        if (!$filterKelasId) {
            $filterKelasId = $user->kelas()
                ->wherePivot('tahun_ajaran', $periodeAktif?->tahun_ajaran)
                ->value('kelas_id');
        }

        $query = Guru::query();
        if ($filterKategori === 'normada') {
            $query->where('kategori', 'normada');
        } elseif ($filterKategori === 'produktif') {
            $query->where('kategori', 'produktif');
        }

        if ($filterKelasId) {
            $query->whereHas('kelas', function($q) use ($filterKelasId) {
                $q->where('kelas.id', $filterKelasId);
            });
        }

        $guruList = $query->get();

        $guruWithData = $guruList->map(function($guru) use ($filterKelasId, $periodeId) {
            $guru->persentase = $guru->getPersentasePartisipasiDiKelas($filterKelasId, $periodeId);
            $guru->jumlah_siswa = $guru->getJumlahSiswaMenilaiDiKelas($filterKelasId, $periodeId);
            $guru->total_siswa = Kelas::find($filterKelasId)?->jumlah_siswa ?? 0;
            $guru->rata_evaluasi = $guru->getRataRataEvaluasiDiKelas($filterKelasId, $periodeId);
            return $guru;
        });

        $rankedGuru = $guruWithData->sortByDesc('persentase')->values();
        $top3 = $rankedGuru->take(3);

        $semuaKelas = Kelas::with('jurusan')->orderBy('tingkat')->orderBy('nama_kelas')->get();

        return view('siswa.leaderboard.index', compact(
            'top3', 'rankedGuru', 'semuaKelas', 'filterKategori', 'filterKelasId'
        ));
    }
}