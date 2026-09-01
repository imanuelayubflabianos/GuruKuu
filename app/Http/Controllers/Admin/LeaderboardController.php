<?php

namespace App\Http\Controllers\Admin;

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
        $filterKategori = $request->get('kategori', 'semua');
        $filterKelasId = $request->get('kelas_id', null);
        $periodeId = Periode::where('status', 'aktif')->value('id');

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
            if ($filterKelasId) {
                $kelas = Kelas::find($filterKelasId);
                $totalSiswa = $kelas ? $kelas->jumlah_siswa : 0;
                
                $jumlahMenilai = Penilaian::where('guru_id', $guru->id)
                    ->where('class_id', $filterKelasId)
                    ->where('periode_id', $periodeId)
                    ->distinct('siswa_id')
                    ->count('siswa_id');
            } else {
                $totalSiswa = $guru->kelas->sum('jumlah_siswa');
                
                $jumlahMenilai = Penilaian::where('guru_id', $guru->id)
                    ->where('periode_id', $periodeId)
                    ->distinct('siswa_id')
                    ->count('siswa_id');
            }

            $persentase = $totalSiswa > 0 ? round(($jumlahMenilai / $totalSiswa) * 100, 1) : 0;

            $guru->persentase = $persentase;
            $guru->jumlah_siswa = $jumlahMenilai;
            $guru->total_siswa = $totalSiswa;

            return $guru;
        });

        $rankedGuru = $guruWithData->sortByDesc('persentase')->values();
        $top3 = $rankedGuru->take(3);

        $semuaKelas = Kelas::with('jurusan')->orderBy('tingkat')->orderBy('nama_kelas')->get();

        return view('admin.leaderboard.index', compact(
            'top3',
            'rankedGuru',
            'semuaKelas',
            'filterKategori',
            'filterKelasId'
        ));
    }
}