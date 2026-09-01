<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Penilaian;
use App\Models\Periode;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $periodeAktif = Periode::where('status', 'aktif')->first();
        $periodeId = $periodeAktif?->id;

        $kelasAktif = $user->kelas()
            ->wherePivot('tahun_ajaran', $periodeAktif?->tahun_ajaran)
            ->first();

        $guru = collect();
        if ($kelasAktif) {
            $guru = $kelasAktif->guru()->with('jurusan')->get();
        }

        return view('siswa.guru.index', compact('guru', 'kelasAktif', 'periodeId'));
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
        if ($kelasAktif && $periodeId) {
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

        return view('siswa.guru.show', compact(
            'guru', 'kelasAktif', 'periodeId', 'sudahMenilai', 
            'semuaFeedback', 'stats'
        ));
    }
}