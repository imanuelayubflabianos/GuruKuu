<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\Penilaian;
use App\Models\Periode;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        $kelasList = Kelas::with('jurusan')->orderBy('tingkat')->orderBy('nama_kelas')->get();
        $mode = $request->input('mode', 'rating');
        $kelasId = $request->integer('kelas_id') ?: null;
        $leaderboard = Guru::leaderboardFor($mode, $kelasId, $periodeAktif?->id);

        return view('siswa.leaderboard.index', compact(
            'periodeAktif', 'leaderboard', 'kelasList', 'mode', 'kelasId'
        ));
    }
}