<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Penilaian;
use App\Models\Periode;

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

        return view('siswa.guru.show', compact('guru', 'kelasAktif', 'periodeId', 'sudahMenilai'));
    }
}