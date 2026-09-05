<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penilaian;
use Illuminate\Http\Request;

class KritikSaranController extends Controller
{
    public function index()
    {
        $feedbacks = Penilaian::with(['guru', 'siswa', 'kelas', 'periode'])
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('kritik')->whereRaw("TRIM(kritik) != ''");
                })->orWhere(function ($q) {
                    $q->whereNotNull('saran')->whereRaw("TRIM(saran) != ''");
                });
            })
            ->latest()
            ->get();

        return view('admin.kritik-saran.index', compact('feedbacks'));
    }

    public function destroy(Penilaian $kritikSaran)
    {
        $kritikSaran->update([
            'kritik' => null,
            'saran' => null,
        ]);

        return back()->with('success', 'Ulasan kritik & saran berhasil dihapus.');
    }

    public function warn(Penilaian $kritikSaran)
    {
        $kritikSaran->update([
            'kritik' => 'Pesan ini telah dihapus oleh Admin karena melanggar etika dan tata tertib.',
            'saran' => null,
        ]);

        if ($kritikSaran->siswa) {
            $kritikSaran->siswa->increment('warning_count');
        }

        return back()->with('success', 'Peringatan berhasil dikirim kepada siswa dan ulasan telah dimoderasi.');
    }
}