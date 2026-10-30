<?php

namespace App\Http\Controllers\Admin;

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
        $periodeAktif = Periode::getActivePeriode();
        $semuaPeriode = Periode::orderBy('tahun_ajaran', 'desc')->orderBy('semester', 'desc')->get();

        // Admin dapat memilih melihat periode aktif maupun periode lampau (histori evaluasi)
        $selectedPeriodeId = $request->input('periode_id');
        $selectedPeriode = $selectedPeriodeId ? Periode::find($selectedPeriodeId) : null;
        if (!$selectedPeriode) {
            $selectedPeriode = $periodeAktif ?: $semuaPeriode->first();
        }
        $periodeId = $selectedPeriode?->id;

        $kelasList = Kelas::with('jurusan')->orderBy('tingkat')->orderBy('nama_kelas')->get();
        $mode = $request->input('mode', 'rating');
        $kelasId = $request->integer('kelas_id') ?: null;
        if ($mode === 'partisipasi' && !$kelasId && $kelasList->isNotEmpty()) {
            $kelasId = $kelasList->first()->id;
        }
        if ($mode === 'rating') {
            $kelasId = null;
        }
        $leaderboard = Guru::leaderboardFor($mode, $kelasId, $periodeId);

        return view('admin.leaderboard.index', compact(
            'periodeAktif', 'semuaPeriode', 'selectedPeriode', 'leaderboard', 'kelasList', 'mode', 'kelasId'
        ));
    }
}