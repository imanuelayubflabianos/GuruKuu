<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Penilaian;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        return view('admin.pengaturan.index');
    }

    public function resetPenilaian()
    {
        // ✅ Reset nilai angka menjadi 0, TAPI biarkan kolom 'kritik' dan 'saran' tetap ada
        Penilaian::query()->update([
            'kedisiplinan' => 0,
            'cara_mengajar' => 0,
            'komunikasi' => 0,
            'tanggung_jawab' => 0,
            'kreativitas' => 0,
            'keramahan' => 0,
            'total_nilai' => 0,
        ]);

        // Reset rata-rata dan total penilaian di tabel guru
        Guru::query()->update([
            'rata_rata_nilai' => 0,
            'total_penilaian' => 0,
        ]);

        return back()->with('success', 'Semua nilai penilaian berhasil direset. Riwayat komentar/kritik siswa tetap tersimpan!');
    }
}