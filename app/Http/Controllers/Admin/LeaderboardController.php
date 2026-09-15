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
        $periodeAktif = Periode::where('status', 'aktif')->first();
        
        $leaderboard = Guru::with('jurusan')
            ->where('total_penilaian', '>', 0)
            ->orderBy('rata_rata_nilai', 'desc')
            ->orderBy('total_penilaian', 'desc')
            ->get();

        // Fallback jika belum ada penilaian
        if ($leaderboard->isEmpty()) {
            $leaderboard = Guru::with('jurusan')
                ->orderBy('nama', 'asc')
                ->get();
        }

        return view('admin.leaderboard.index', compact(
            'periodeAktif',
            'leaderboard'
        ));
    }
}