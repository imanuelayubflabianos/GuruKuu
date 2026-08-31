<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Periode;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        
        // Top 3 guru terbaik (gunakan query langsung, bukan scope)
        $topGuru = Guru::where('total_penilaian', '>', 0)
            ->orderBy('rata_rata_nilai', 'desc')
            ->orderBy('total_penilaian', 'desc')
            ->limit(3)
            ->get();

        // Top 3 guru normada
        $topNormada = Guru::where('kategori', 'normada')
            ->where('total_penilaian', '>', 0)
            ->orderBy('rata_rata_nilai', 'desc')
            ->orderBy('total_penilaian', 'desc')
            ->limit(3)
            ->get();

        // Top 3 guru produktif
        $topProduktif = Guru::where('kategori', 'produktif')
            ->where('total_penilaian', '>', 0)
            ->orderBy('rata_rata_nilai', 'desc')
            ->orderBy('total_penilaian', 'desc')
            ->limit(3)
            ->get();

        return view('landing.index', compact(
            'periodeAktif',
            'topGuru',
            'topNormada',
            'topProduktif'
        ));
    }

    public function leaderboard()
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        
        $topGuru = Guru::where('total_penilaian', '>', 0)
            ->orderBy('rata_rata_nilai', 'desc')
            ->orderBy('total_penilaian', 'desc')
            ->limit(10)
            ->get();

        return view('landing.leaderboard', compact('periodeAktif', 'topGuru'));
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