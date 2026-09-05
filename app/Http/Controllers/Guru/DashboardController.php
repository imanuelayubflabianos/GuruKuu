<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Penilaian;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Ambil data guru yang login berdasarkan NIP / NIS di tabel users atau email
        $guru = Guru::where('nip', $user->nis)
            ->orWhere('email', $user->email)
            ->first();
        
        if (!$guru) {
            return redirect()->route('login')->with('error', 'Data profil Guru tidak ditemukan dalam sistem.');
        }

        $periodeAktif = Periode::where('status', 'aktif')->first();
        $periodeId = $periodeAktif?->id;

        // Ambil kelas yang diampu beserta perhitungan partisipasi
        $kelasList = $guru->kelas()->withPivot('mata_pelajaran')->get()->map(function($kelas) use ($guru, $periodeId) {
            $kelas->partisipasi = $guru->getPersentasePartisipasiDiKelas($kelas->id, $periodeId);
            $kelas->sudah_menilai = $guru->getJumlahSiswaMenilaiDiKelas($kelas->id, $periodeId);
            return $kelas;
        });

        // Ambil ulasan & kritik saran dari siswa untuk guru ini
        $ulasanTerbaru = Penilaian::where('guru_id', $guru->id)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('kritik')->whereRaw("TRIM(kritik) != ''");
                })->orWhere(function ($q) {
                    $q->whereNotNull('saran')->whereRaw("TRIM(saran) != ''");
                });
            })
            ->with(['siswa', 'kelas', 'periode'])
            ->latest()
            ->get();

        return view('guru.dashboard', compact(
            'guru',
            'kelasList',
            'ulasanTerbaru',
            'periodeAktif'
        ));
    }
}