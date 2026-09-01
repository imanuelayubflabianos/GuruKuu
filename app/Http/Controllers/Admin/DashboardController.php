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
            
        // Ambil feedback dari tabel penilaian (yang punya kritik atau saran)
        $feedbacks = Penilaian::with(['guru', 'siswa'])
            ->where(function($q) {
                $q->whereNotNull('kritik')
                  ->where('kritik', '!=', '')
                  ->orWhere(function($q2) {
                      $q2->whereNotNull('saran')->where('saran', '!=', '');
                  });
            })
            ->latest()
            ->limit(3)
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