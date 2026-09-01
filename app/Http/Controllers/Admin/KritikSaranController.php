<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penilaian;
use Illuminate\Http\Request;

class KritikSaranController extends Controller
{
    public function index()
    {
        $feedbacks = Penilaian::with(['guru', 'siswa'])
            ->where(function($q) {
                $q->whereNotNull('kritik')->orWhereNotNull('saran');
            })
            ->latest()
            ->get();

        return view('admin.kritik-saran.index', compact('feedbacks'));
    }

    // Satu tombol untuk hapus dan beri peringatan
    public function warnAndDelete(Penilaian $kritikSaran)
    {
        $kritikSaran->update([
            'kritik' => 'Pesan anda dihapus karena melanggar aturan.',
            'saran' => null,
            'is_replied' => true,
            'is_read' => true
        ]);

        return back()->with('success', 'Pesan telah dihapus dan peringatan otomatis telah diberikan.');
    }
}