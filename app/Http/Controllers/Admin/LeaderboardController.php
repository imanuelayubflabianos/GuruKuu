<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Periode;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        $filterKelasId = $request->get('kelas_id', null);

        // TOP 3 GLOBAL
        $top3Global = Guru::with('jurusan')
            ->where('total_penilaian', '>', 0)
            ->get()
            ->sortByDesc(fn($g) => ($g->rata_rata_nilai * 0.7) + ($g->rasio_penilaian * 0.3))
            ->values()->take(3);

        // TOP 10 GLOBAL
        $top10Global = Guru::with('jurusan')
            ->where('total_penilaian', '>', 0)
            ->get()
            ->sortByDesc(fn($g) => ($g->rata_rata_nilai * 0.7) + ($g->rasio_penilaian * 0.3))
            ->values()->take(10);

        // STATISTIK PER KELAS (TERPISAH NORMADA & PRODUKTIF)
        $kelasList = Kelas::with('jurusan')->orderBy('tingkat')->orderBy('nama_kelas')->get();
        $statistikPerKelas = [];
        foreach ($kelasList as $kelas) {
            $statistikPerKelas[$kelas->id] = [
                'kelas' => $kelas,
                'normada' => $kelas->guru()->where('kategori', 'normada')
                    ->withCount(['penilaian' => fn($q) => $periodeAktif ? $q->where('periode_id', $periodeAktif->id) : $q])
                    ->get()->sortByDesc('rata_rata_nilai')->values(),
                'produktif' => $kelas->guru()->where('kategori', 'produktif')
                    ->withCount(['penilaian' => fn($q) => $periodeAktif ? $q->where('periode_id', $periodeAktif->id) : $q])
                    ->get()->sortByDesc('rata_rata_nilai')->values(),
            ];
        }

        $kelasAktifStats = $filterKelasId && isset($statistikPerKelas[$filterKelasId])
            ? $statistikPerKelas[$filterKelasId]
            : (count($statistikPerKelas) > 0 ? reset($statistikPerKelas) : null);

        return view('admin.leaderboard.index', compact(
            'top3Global', 'top10Global', 'kelasList', 'kelasAktifStats', 'filterKelasId'
        ));
    }
}