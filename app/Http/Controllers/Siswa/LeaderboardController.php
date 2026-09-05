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

        if (!$filterKelasId && $user) {
            $filterKelasId = $user->kelas()
                ->wherePivot('tahun_ajaran', $periodeAktif?->tahun_ajaran)
                ->value('kelas_id');
        }

        $query = Guru::query()->with(['jurusan', 'kelas']);
        
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
            $guru->total_siswa = $filterKelasId 
                ? (Kelas::find($filterKelasId)?->jumlah_siswa ?? 0) 
                : $guru->kelas->sum('jumlah_siswa');
            $guru->rata_evaluasi = $filterKelasId 
                ? $guru->getRataRataEvaluasiDiKelas($filterKelasId, $periodeId) 
                : ($guru->rata_rata_nilai ?? 0);
            return $guru;
        });

        // Urutkan peringkat stabil: Persentase DESC, Rata Evaluasi DESC, Jumlah Siswa DESC, Nama ASC
        $rankedGuru = $guruWithData->sort(function($a, $b) {
            if ($b->persentase != $a->persentase) {
                return $b->persentase <=> $a->persentase;
            }
            if ($b->rata_evaluasi != $a->rata_evaluasi) {
                return $b->rata_evaluasi <=> $a->rata_evaluasi;
            }
            if ($b->jumlah_siswa != $a->jumlah_siswa) {
                return $b->jumlah_siswa <=> $a->jumlah_siswa;
            }
            return strcmp($a->nama, $b->nama);
        })->values();

        $semuaKelas = Kelas::with('jurusan')->orderBy('tingkat')->orderBy('nama_kelas')->get();

        return view('siswa.leaderboard.index', compact(
            'rankedGuru', 'semuaKelas', 'filterKategori', 'filterKelasId'
        ));
    }
}