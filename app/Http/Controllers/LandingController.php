<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Penilaian;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandingController extends Controller
{
    /**
     * Halaman Utama (Landing Page)
     */
    public function index()
    {
        // ✅ REDIRECT OTOMATIS JIKA SUDAH LOGIN
        if (Auth::check()) {
            return redirect()->route(
                Auth::user()->role === 'admin' ? 'admin.dashboard' : 'siswa.dashboard'
            );
        }

        // ✅ Ambil periode yang sedang aktif
        $periodeAktif = Periode::where('status', 'aktif')->first();

        // ✅ Ambil Top 3 Guru untuk Mini Podium
        $guruTerbaik = Guru::with('penghargaan.badge', 'jurusan')
            ->terbaik(3)
            ->get();

        // ✅ STATISTIK REAL-TIME UNTUK DASHBOARD AWAL
        $totalGuru = Guru::count();
        $totalSiswa = User::where('role', 'siswa')->count();
        
        // Total penilaian HANYA untuk semester yang sedang aktif (agar reset per periode)
        $totalPenilaian = $periodeAktif ? Penilaian::where('periode_id', $periodeAktif->id)->count() : 0;

        return view('landing.index', compact(
            'guruTerbaik', 
            'periodeAktif', 
            'totalGuru', 
            'totalSiswa', 
            'totalPenilaian'
        ));
    }

    /**
     * Halaman Leaderboard Publik (Fallback jika ada yang akses langsung)
     */
    public function leaderboard()
    {
        if (Auth::check()) {
            return redirect()->route(
                Auth::user()->role === 'admin' ? 'admin.leaderboard.index' : 'siswa.leaderboard.index'
            );
        }

        $leaderboardNormada = Guru::with('jurusan', 'penghargaan.badge')
            ->normada()
            ->terbaik(20)
            ->get();

        $leaderboardProduktif = Guru::with('jurusan', 'penghargaan.badge')
            ->produktif()
            ->terbaik(20)
            ->get();

        return view('landing.leaderboard', compact('leaderboardNormada', 'leaderboardProduktif'));
    }

    /**
     * API Search Guru (Untuk Modal Pencarian di Navbar)
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        $guru = Guru::with('jurusan')
            ->where('nama', 'like', "%{$query}%")
            ->orWhereHas('jurusan', function($q) use ($query) {
                $q->where('nama_jurusan', 'like', "%{$query}%");
            })
            ->orderByDesc('rata_rata_nilai')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $guru->map(function($g) {
                return [
                    'id' => $g->id,
                    'nama' => $g->nama,
                    'kategori_label' => $g->kategori_label,
                    'rata_rata_nilai' => number_format($g->rata_rata_nilai, 2),
                    'photo_url' => $g->photo_url,
                    'jurusan' => [
                        'nama_jurusan' => $g->jurusan?->nama_jurusan,
                    ]
                ];
            })
        ]);
    }

    /**
     * Halaman Detail Guru Publik
     */
    public function guruDetail(Guru $guru)
    {
        if (Auth::check() && Auth::user()->role === 'siswa') {
            return redirect()->route('siswa.guru.show', $guru);
        }

        $guru->load(['jurusan', 'penghargaan.badge', 'penilaian.siswa']);
        
        return view('landing.guru-detail', compact('guru'));
    }
}