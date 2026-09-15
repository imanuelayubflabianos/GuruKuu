<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $periodeAktif = Periode::where('status', 'aktif')->first();
        $kelasAktif = $periodeAktif ? $user->kelas()->wherePivot('tahun_ajaran', $periodeAktif->tahun_ajaran)->first() : null;
        return view('siswa.profil.index', compact('kelasAktif'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        $user->update(['name' => $request->name]);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}