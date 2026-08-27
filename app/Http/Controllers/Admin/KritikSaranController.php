<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penilaian;
use Illuminate\Http\Request;

class KritikSaranController extends Controller
{
    public function index()
    {
        $kritik = Penilaian::where(function($q) {
                $q->whereNotNull('kritik')->orWhereNotNull('saran');
            })
            ->with(['guru', 'siswa', 'periode'])
            ->latest()
            ->get();
        return view('admin.kritik-saran.index', compact('kritik'));
    }

    // ✅ HAPUS FEEDBACK SAJA (kritik+saran), nilai tetap ada
    public function destroyFeedback(Penilaian $kritikSaran)
    {
        $kritikSaran->update(['kritik' => null, 'saran' => null]);
        return back()->with('success', 'Feedback berhasil dihapus. Nilai penilaian tetap tersimpan.');
    }

    // ✅ HAPUS SELURUH PENILAIAN
    public function destroy(Penilaian $kritikSaran)
    {
        $guruId = $kritikSaran->guru_id;
        $kritikSaran->delete();
        $guru = \App\Models\Guru::find($guruId);
        if ($guru) $guru->updateRataRata();
        return back()->with('success', 'Seluruh penilaian berhasil dihapus.');
    }
}