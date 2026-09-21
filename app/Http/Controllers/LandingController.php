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
            ->withRatings()
            ->orderBy('rata_rata_nilai', 'desc')
            ->orderBy('total_penilaian', 'desc')
            ->limit(3)
            ->get();


        return view('landing.index', compact(
            'periodeAktif',
            'totalGuru',
            'totalSiswa',
            'totalPenilaian',
            'topGuru'
        ));
    }

    public function leaderboard(Request $request)
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        $kelasList = \App\Models\Kelas::with('jurusan')->orderBy('tingkat')->orderBy('nama_kelas')->get();
        $mode = $request->input('mode', 'rating');
        $kelasId = $request->integer('kelas_id') ?: null;
        $leaderboard = Guru::leaderboardFor($mode, $kelasId, $periodeAktif?->id);

        return view('landing.leaderboard', compact(
            'periodeAktif', 'leaderboard', 'kelasList', 'mode', 'kelasId'
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
        $periodeAktif = Periode::where('status', 'aktif')->first();
        $periodeId = $periodeAktif?->id;

        $sudahMenilai = false;
        if (auth()->check() && auth()->user()->role === 'siswa' && $periodeId) {
            $sudahMenilai = Penilaian::where('siswa_id', auth()->id())
                ->where('guru_id', $guru->id)
                ->where('periode_id', $periodeId)
                ->exists();
        }

        // Statistik evaluasi periode berjalan
        $allPenilaian = Penilaian::where('guru_id', $guru->id)
            ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
            ->get();

        $stats = [
            'total_penilaian' => $allPenilaian->count(),
            'rata_kedisiplinan' => $allPenilaian->avg('kedisiplinan') ?? 0,
            'rata_cara_mengajar' => $allPenilaian->avg('cara_mengajar') ?? 0,
            'rata_komunikasi' => $allPenilaian->avg('komunikasi') ?? 0,
            'rata_tanggung_jawab' => $allPenilaian->avg('tanggung_jawab') ?? 0,
            'rata_kreativitas' => $allPenilaian->avg('kreativitas') ?? 0,
            'rata_keramahan' => $allPenilaian->avg('keramahan') ?? 0,
        ];

        // Semua ulasan & masukan siswa periode berjalan
        $semuaFeedback = Penilaian::with(['siswa', 'balasans.user'])
            ->where('guru_id', $guru->id)
            ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
            ->where(function($q) {
                $q->whereNotNull('kritik')->where('kritik', '!=', '')
                  ->orWhereNotNull('saran')->where('saran', '!=', '');
            })
            ->latest()
            ->get();

        // Arsip penilaian dari periode-periode lampau
        $arsipPeriode = Periode::where('id', '!=', $periodeId)
            ->whereHas('penilaian', function($q) use ($guru) {
                $q->where('guru_id', $guru->id);
            })
            ->with(['penilaian' => function($q) use ($guru) {
                $q->where('guru_id', $guru->id)->latest();
            }])
            ->latest('tanggal_mulai')
            ->get();

        return view('landing.guru-detail', compact(
            'guru', 'periodeAktif', 'stats', 'semuaFeedback', 'arsipPeriode', 'sudahMenilai'
        ));
    }
}