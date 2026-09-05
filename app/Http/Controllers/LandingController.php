<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Penilaian;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        
        $totalGuru = Guru::count();
        $totalSiswa = User::where('role', 'siswa')->count();
        $totalPenilaian = Penilaian::count();

        // Top 3 guru terbaik secara keseluruhan
        $topGuru = Guru::with('jurusan')
            ->where('total_penilaian', '>', 0)
            ->orderBy('rata_rata_nilai', 'desc')
            ->orderBy('total_penilaian', 'desc')
            ->limit(3)
            ->get();

        // Top 3 guru normada
        $topNormada = Guru::with('jurusan')
            ->where('kategori', 'normada')
            ->where('total_penilaian', '>', 0)
            ->orderBy('rata_rata_nilai', 'desc')
            ->orderBy('total_penilaian', 'desc')
            ->limit(3)
            ->get();

        // Top 3 guru produktif
        $topProduktif = Guru::with('jurusan')
            ->where('kategori', 'produktif')
            ->where('total_penilaian', '>', 0)
            ->orderBy('rata_rata_nilai', 'desc')
            ->orderBy('total_penilaian', 'desc')
            ->limit(3)
            ->get();

        return view('landing.index', compact(
            'periodeAktif',
            'totalGuru',
            'totalSiswa',
            'totalPenilaian',
            'topGuru',
            'topNormada',
            'topProduktif'
        ));
    }

    public function leaderboard()
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        
        $leaderboardNormada = Guru::with('jurusan')
            ->where('kategori', 'normada')
            ->where('total_penilaian', '>', 0)
            ->orderBy('rata_rata_nilai', 'desc')
            ->orderBy('total_penilaian', 'desc')
            ->get();

        $leaderboardProduktif = Guru::with('jurusan')
            ->where('kategori', 'produktif')
            ->where('total_penilaian', '>', 0)
            ->orderBy('rata_rata_nilai', 'desc')
            ->orderBy('total_penilaian', 'desc')
            ->get();

        // Fallback jika belum ada penilaian
        if ($leaderboardNormada->isEmpty()) {
            $leaderboardNormada = Guru::with('jurusan')
                ->where('kategori', 'normada')
                ->orderBy('nama', 'asc')
                ->get();
        }

        if ($leaderboardProduktif->isEmpty()) {
            $leaderboardProduktif = Guru::with('jurusan')
                ->where('kategori', 'produktif')
                ->orderBy('nama', 'asc')
                ->get();
        }

        return view('landing.leaderboard', compact(
            'periodeAktif',
            'leaderboardNormada',
            'leaderboardProduktif'
        ));
    }

    public function search(Request $request)
    {
        $search = $request->get('q');
        
        $guru = Guru::where('nama', 'like', "%{$search}%")
            ->orWhere('nip', 'like', "%{$search}%")
            ->limit(20)
            ->get();

        return view('landing.search', compact('guru', 'search'));
    }

    public function guruDetail(Guru $guru)
    {
        return view('landing.guru-detail', compact('guru'));
    }
}