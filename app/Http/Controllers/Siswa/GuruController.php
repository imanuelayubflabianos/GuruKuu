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
        
        // ✅ Get kelas aktif siswa
        $kelasAktif = $user->kelas()
                          ->wherePivot('tahun_ajaran', $periodeAktif->tahun_ajaran)
                          ->first();

        if (!$kelasAktif) {
            return view('siswa.guru.index', ['guru' => collect(), 'kelasAktif' => null]);
        }

        // ✅ Hanya tampilkan guru yang mengajar di kelas siswa
        $guru = $kelasAktif->guru()->with('jurusan')->get();

        return view('siswa.guru.index', compact('guru', 'kelasAktif'));
    }

    public function show(Guru $guru)
    {
        $user = auth()->user();
        $periodeAktif = Periode::where('status', 'aktif')->first();
        
        // ✅ Validasi: Guru harus mengajar di kelas siswa
        $kelasAktif = $user->kelas()
                          ->wherePivot('tahun_ajaran', $periodeAktif->tahun_ajaran)
                          ->first();

        if (!$kelasAktif || !$kelasAktif->guru->contains($guru->id)) {
            abort(403, 'Guru ini tidak mengajar di kelas Anda.');
        }

        $guru->load(['jurusan', 'penghargaan.badge', 'penghargaan.periode']);
        
        $sudahMenilai = Penilaian::where('siswa_id', $user->id)
                                 ->where('guru_id', $guru->id)
                                 ->where('periode_id', $periodeAktif->id)
                                 ->exists();

        $ulasanTerbaru = Penilaian::where('guru_id', $guru->id)
                                  ->where(function($query) {
                                      $query->whereNotNull('kritik')->orWhereNotNull('saran');
                                  })
                                  ->latest()
                                  ->take(5)
                                  ->get();

        return view('siswa.guru.show', compact('guru', 'sudahMenilai', 'ulasanTerbaru'));
    }
}