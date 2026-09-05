<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Penilaian;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalGuru = Guru::count();
        $totalSiswa = User::where('role', 'siswa')->count();
        $totalPenilaian = Penilaian::count();
        
        $topGuru = Guru::with('jurusan')
            ->where('total_penilaian', '>', 0)
            ->orderBy('rata_rata_nilai', 'desc')
            ->limit(3)
            ->get();
            
        // Ambil ulasan terbaru dari tabel penilaian (yang memiliki teks kritik atau saran)
        $feedbacks = Penilaian::with(['guru', 'siswa', 'kelas'])
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('kritik')->whereRaw("TRIM(kritik) != ''");
                })->orWhere(function ($q) {
                    $q->whereNotNull('saran')->whereRaw("TRIM(saran) != ''");
                });
            })
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalGuru', 
            'totalSiswa', 
            'totalPenilaian', 
            'topGuru', 
            'feedbacks'
        ));
    }
}