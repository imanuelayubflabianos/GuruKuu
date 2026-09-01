<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Periode;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        return view('admin.pengaturan.index', compact('periodeAktif'));
    }

    public function reset(Request $request)
    {
        // Logika reset penilaian di sini
        return back()->with('success', 'Data berhasil direset.');
    }
}