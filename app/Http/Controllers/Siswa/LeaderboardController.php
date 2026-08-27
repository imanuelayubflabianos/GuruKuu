<?php

namespace App\Http\Controllers\Siswa;

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

        // 1. TOP 3 GLOBAL (Semua Kelas)
        $top3Global = Guru::with('jurusan')
            ->where('total_penilaian', '>', 0)
            ->get()
            ->sortByDesc(function($guru) {
                return ($guru->rata_rata_nilai * 0.7) + ($guru->rasio_penilaian * 0.3);
            })
            ->values()
            ->take(3);

        // 2. TOP 10 GLOBAL
        $top10Global = Guru::with('jurusan')
            ->where('total_penilaian', '>', 0)
            ->get()
            ->sortByDesc(function($guru) {
                return ($guru->rata_rata_nilai * 0.7) + ($guru->rasio_penilaian * 0.3);
            })
            ->values()
            ->take(10);

        // 3. STATISTIK PER KELAS (TERPISAH NORMADA & PRODUKTIF)
        $kelasList = Kelas::with('jurusan')->orderBy('tingkat')->orderBy('nama_kelas')->get();
        
        $statistikPerKelas = [];
        foreach ($kelasList as $kelas) {
            $guruNormada = $kelas->guru()
                ->where('kategori', 'normada')
                ->withCount(['penilaian' => function($q) use ($periodeAktif) {
                    if ($periodeAktif) $q->where('periode_id', $periodeAktif->id);
                }])
                ->get()
                ->sortByDesc('rata_rata_nilai')
                ->values();

            $guruProduktif = $kelas->guru()
                ->where('kategori', 'produktif')
                ->withCount(['penilaian' => function($q) use ($periodeAktif) {
                    if ($periodeAktif) $q->where('periode_id', $periodeAktif->id);
                }])
                ->get()
                ->sortByDesc('rata_rata_nilai')
                ->values();
            
            $statistikPerKelas[$kelas->id] = [
                'kelas' => $kelas,
                'normada' => $guruNormada,
                'produktif' => $guruProduktif,
            ];
        }

        $kelasAktifStats = $filterKelasId && isset($statistikPerKelas[$filterKelasId]) 
            ? $statistikPerKelas[$filterKelasId] 
            : (count($statistikPerKelas) > 0 ? reset($statistikPerKelas) : null);

        return view('siswa.leaderboard.index', compact(
            'top3Global',
            'top10Global',
            'kelasList',
            'statistikPerKelas',
            'kelasAktifStats',
            'filterKelasId'
        ));
    }
}