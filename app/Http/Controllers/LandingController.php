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
        $totalPenilaian = $periodeAktif 
            ? Penilaian::where('periode_id', $periodeAktif->id)->count() 
            : Penilaian::count();

        // Leaderboard resmi menggunakan aturan standar sistem (Top 10 Publik)
        $leaderboard = Guru::leaderboardFor('rating', null, $periodeAktif?->id)
            ->filter(fn($g) => !empty($g->leaderboard_rank))
            ->take(10);

        return view('landing.index', compact(
            'periodeAktif',
            'totalGuru',
            'totalSiswa',
            'totalPenilaian',
            'leaderboard'
        ));
    }

    public function leaderboard(Request $request)
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        $kelasList = \App\Models\Kelas::with('jurusan')->orderBy('tingkat')->orderBy('nama_kelas')->get();
        $mode = $request->input('mode', 'rating');
        $kelasId = $request->integer('kelas_id') ?: null;
        if ($mode === 'partisipasi' && !$kelasId && $kelasList->isNotEmpty()) {
            $kelasId = $kelasList->first()->id;
        }
        if ($mode === 'rating') {
            $kelasId = null;
        }

        // Tampilan publik dibatasi: Top 10 (Semua Guru) & Top 5 (Berdasarkan Kelas)
        $limit = ($mode === 'partisipasi') ? 5 : 10;
        $leaderboard = Guru::leaderboardFor($mode, $kelasId, $periodeAktif?->id)
            ->filter(fn($g) => !empty($g->leaderboard_rank))
            ->take($limit);

        return view('landing.leaderboard', compact(
            'periodeAktif', 'leaderboard', 'kelasList', 'mode', 'kelasId'
        ));
    }

    public function search(Request $request)
    {
        $search = trim((string) $request->get('q', $request->get('search', '')));
        $query = Guru::with('jurusan');

        if (!empty($search)) {
            $terms = preg_split('/\s+/', $search, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($terms as $term) {
                $query->where(function ($q) use ($term) {
                    $q->where('nama', 'like', "%{$term}%")
                      ->orWhere('nip', 'like', "%{$term}%")
                      ->orWhere('email', 'like', "%{$term}%")
                      ->orWhereHas('jurusan', fn($j) => $j->where('nama_jurusan', 'like', "%{$term}%"));
                });
            }
        }

        $guru = $query->limit(20)->get();

        return view('landing.search', compact('guru', 'search'));
    }

    public function guruDetail(Guru $guru)
    {
        $guru->load(['jurusan', 'penghargaan.badge']);
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
            'rata_komunikasi' => $allPenilaian->avg('komunikasi') ?? 0,
            'rata_tanggung_jawab' => $allPenilaian->avg('tanggung_jawab') ?? 0,
            'rata_kreativitas' => $allPenilaian->avg('kreativitas') ?? 0,
            'rata_keramahan' => $allPenilaian->avg('keramahan') ?? 0,
        ];

        // Semua ulasan & masukan siswa periode berjalan
        $semuaFeedback = Penilaian::with(['siswa', 'balasans.user'])
            ->where('guru_id', $guru->id)
            ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
            ->where(function ($query) {
                $query->where('is_censored', false)->orWhereNull('is_censored');
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