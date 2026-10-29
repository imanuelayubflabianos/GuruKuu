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
        if ($mode === 'partisipasi' && !$kelasId && $kelasList->isNotEmpty()) {
            $kelasId = $kelasList->first()->id;
        }
        if ($mode === 'rating') {
            $kelasId = null;
        }
        // Tampilan siswa dibatasi: Top 10 (Semua Guru) & Top 5 (Berdasarkan Kelas)
        $limit = ($mode === 'partisipasi') ? 5 : 10;
        $leaderboard = Guru::leaderboardFor($mode, $kelasId, $periodeAktif?->id)
            ->filter(fn($g) => !empty($g->leaderboard_rank))
            ->take($limit);

        return view('siswa.leaderboard.index', compact(
            'periodeAktif', 'leaderboard', 'kelasList', 'mode', 'kelasId'
        ));
    }
}