<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Penilaian;
use App\Models\Periode;
use App\Models\User;

class StatistikController extends Controller
{
    public function index()
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        $totalGuru = Guru::count();
        $totalSiswa = User::where('role', 'siswa')->count();
        $totalPenilaian = $periodeAktif ? Penilaian::where('periode_id', $periodeAktif->id)->count() : 0;
        $rataRataUmum = Penilaian::avg('total_nilai') ? round(Penilaian::avg('total_nilai') / 6, 2) : 0;

        // Distribusi Bintang 1-5
        $distribusiBintang = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        foreach (Penilaian::all() as $p) {
            $bintang = max(1, min(5, (int) round($p->total_nilai / 6)));
            $distribusiBintang[$bintang]++;
        }
        $totalUntukPersen = max(1, Penilaian::count());

        // ✅ Feedback / Kritik Saran (Connect ke database)
        $feedbacks = Penilaian::where(function($q) {
            $q->whereNotNull('kritik')->orWhereNotNull('saran');
        })->with(['guru', 'siswa'])->latest()->get();

        return view('admin.statistik.index', compact(
            'periodeAktif', 'totalGuru', 'totalSiswa', 'totalPenilaian',
            'rataRataUmum', 'distribusiBintang', 'totalUntukPersen', 'feedbacks'
        ));
    }
}