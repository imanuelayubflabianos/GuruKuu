<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Penilaian;
use App\Models\Periode;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $periodeAktif = Periode::where('status', 'aktif')->first();
        $periodeId = $periodeAktif?->id;

        $kelasAktif = $user->kelas()
            ->wherePivot('tahun_ajaran', $periodeAktif?->tahun_ajaran)
            ->first();

        $guruKelasSayaIds = $kelasAktif
            ? $kelasAktif->guru()->pluck('guru.id')->toArray()
            : [];

        $mode = $request->input('mode', 'kelas');
        $query = Guru::with('jurusan')->orderBy('nama');

        $isSearchActive = $request->filled('search');
        if ($isSearchActive) {
            $terms = preg_split('/\s+/', trim($request->search), -1, PREG_SPLIT_NO_EMPTY);
            foreach ($terms as $term) {
                $query->where(function($q) use ($term) {
                    $q->where('nama', 'LIKE', "%{$term}%")
                      ->orWhere('nip', 'LIKE', "%{$term}%")
                      ->orWhere('email', 'LIKE', "%{$term}%")
                      ->orWhereHas('jurusan', fn($j) => $j->where('nama_jurusan', 'LIKE', "%{$term}%"));
                });
            }
        }

        if ($mode === 'kelas' && $kelasAktif) {
            $classMatches = (clone $query)->whereHas('kelas', fn ($kelas) => $kelas->whereKey($kelasAktif->id));
            if (!$isSearchActive || $classMatches->exists()) {
                $query->whereHas('kelas', fn ($kelas) => $kelas->whereKey($kelasAktif->id));
            } else {
                $mode = 'semua';
            }
        }

        if ($request->filled('kategori') && in_array($request->kategori, ['normada', 'produktif'])) {
            $query->where('kategori', $request->kategori);
        }

        $guru = $query->get();

        $penilaianSayaMap = $periodeId
            ? Penilaian::where('siswa_id', $user->id)
                ->where('periode_id', $periodeId)
                ->get()
                ->keyBy('guru_id')
            : collect();

        $sudahMenilaiIds = $penilaianSayaMap->keys()->toArray();

        return view('siswa.guru.index', compact('guru', 'kelasAktif', 'periodeId', 'sudahMenilaiIds', 'penilaianSayaMap', 'mode', 'guruKelasSayaIds'));
    }

    public function show(Guru $guru)
    {
        $guru->load(['jurusan', 'penghargaan.badge']);
        $user = auth()->user();
        $periodeAktif = Periode::getActivePeriode();
        $periodeId = $periodeAktif?->id;

        $kelasAktif = $user->kelas()
            ->wherePivot('tahun_ajaran', $periodeAktif?->tahun_ajaran)
            ->first();

        $isMengajarKelas = $kelasAktif
            ? $guru->kelas()->whereKey($kelasAktif->id)->exists()
            : false;

        $penilaianSaya = null;
        if ($periodeId) {
            $penilaianSaya = Penilaian::where('siswa_id', $user->id)
                ->where('guru_id', $guru->id)
                ->where('periode_id', $periodeId)
                ->first();
        }
        $sudahMenilai = (bool) $penilaianSaya;

        // Ambil semua penilaian/feedback siswa untuk guru ini (Ulasan siswa yang login diposisikan paling atas khusus untuk siswa tsb)
        $userId = $user->id;
        $semuaFeedback = $periodeId
            ? Penilaian::with(['siswa', 'balasans.user'])
                ->where('guru_id', $guru->id)
                ->where('periode_id', $periodeId)
                ->where(function($q) use ($userId) {
                    $q->where(function($sub) {
                        $sub->where('is_censored', false)->orWhereNull('is_censored');
                    })->orWhere('siswa_id', $userId);
                })
                ->orderByRaw("CASE WHEN siswa_id = ? THEN 0 ELSE 1 END", [$userId])
                ->latest()
                ->get()
            : collect();

        // Statistik evaluasi
        $allPenilaian = $periodeId
            ? Penilaian::where('guru_id', $guru->id)
                ->where('periode_id', $periodeId)
                ->get()
            : collect();

        $stats = [
            'total_penilaian' => $allPenilaian->count(),
            'rata_kedisiplinan' => $allPenilaian->avg('kedisiplinan') ?? 0,
            'rata_komunikasi' => $allPenilaian->avg('komunikasi') ?? 0,
            'rata_tanggung_jawab' => $allPenilaian->avg('tanggung_jawab') ?? 0,
            'rata_kreativitas' => $allPenilaian->avg('kreativitas') ?? 0,
            'rata_keramahan' => $allPenilaian->avg('keramahan') ?? 0,
        ];

        // Arsip penilaian dari periode-periode nonaktif (lampau)
        $arsipPeriode = Periode::where('status', 'nonaktif')
            ->whereHas('penilaian', function($q) use ($guru) {
                $q->where('guru_id', $guru->id);
            })
            ->with(['penilaian' => function($q) use ($guru, $userId) {
                $q->where('guru_id', $guru->id)
                  ->where(function($sub) {
                      $sub->where('is_censored', false)->orWhereNull('is_censored');
                  })
                  ->orderByRaw("CASE WHEN siswa_id = ? THEN 0 ELSE 1 END", [$userId])
                  ->latest();
            }])
            ->latest('tanggal_mulai')
            ->get();

        return view('siswa.guru.show', compact(
            'guru', 'kelasAktif', 'periodeId', 'sudahMenilai', 'penilaianSaya',
            'semuaFeedback', 'stats', 'arsipPeriode', 'isMengajarKelas'
        ));
    }
}