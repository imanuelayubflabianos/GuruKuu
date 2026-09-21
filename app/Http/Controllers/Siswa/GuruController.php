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

        $mode = $request->input('mode', 'kelas');
        $query = Guru::with('jurusan')->orderBy('nama');

        if ($mode === 'kelas' && $kelasAktif) {
            $query->whereHas('kelas', fn ($kelas) => $kelas->whereKey($kelasAktif->id));
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function($q) use ($search) {
                                $q->where('nama', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('kategori') && in_array($request->kategori, ['normada', 'produktif'])) {
            $query->where('kategori', $request->kategori);
        }

        $guru = $query->get();

        $sudahMenilaiIds = $periodeId
            ? Penilaian::where('siswa_id', $user->id)->where('periode_id', $periodeId)->pluck('guru_id')->toArray()
            : [];

        return view('siswa.guru.index', compact('guru', 'kelasAktif', 'periodeId', 'sudahMenilaiIds', 'mode'));
    }

    public function show(Guru $guru)
    {
        $user = auth()->user();
        $periodeAktif = Periode::where('status', 'aktif')->first();
        $periodeId = $periodeAktif?->id;

        $kelasAktif = $user->kelas()
            ->wherePivot('tahun_ajaran', $periodeAktif?->tahun_ajaran)
            ->first();

        $sudahMenilai = false;
        if ($periodeId) {
            $sudahMenilai = Penilaian::where('siswa_id', $user->id)
                ->where('guru_id', $guru->id)
                ->where('periode_id', $periodeId)
                ->exists();
        }

        // ✅ FIX: Ambil semua feedback siswa lain untuk guru ini
        $semuaFeedback = Penilaian::with('siswa')
            ->where('guru_id', $guru->id)
            ->where('periode_id', $periodeId)
            ->where(function($q) {
                $q->whereNotNull('kritik')->where('kritik', '!=', '')
                  ->orWhereNotNull('saran')->where('saran', '!=', '');
            })
            ->latest()
            ->get();

        // Statistik evaluasi
        $allPenilaian = Penilaian::where('guru_id', $guru->id)
            ->where('periode_id', $periodeId)
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

        return view('siswa.guru.show', compact(
            'guru', 'kelasAktif', 'periodeId', 'sudahMenilai', 
            'semuaFeedback', 'stats', 'arsipPeriode'
        ));
    }
}