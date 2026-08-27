<?php
// app/Http/Controllers/Admin/PeriodeController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Periode;
use Illuminate\Http\Request;

class PeriodeController extends Controller
{
    public function index()
    {
        $periode = Periode::latest()->get();
        return view('admin.periode.index', compact('periode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_periode' => 'required|string|max:255',
            'tahun_ajaran' => 'required|string|max:20',
            'semester' => 'required|in:ganjil,genap',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        ]);

        Periode::create($validated);

        return redirect()->route('admin.periode.index')
            ->with('success', 'Periode berhasil ditambahkan!');
    }

    public function update(Request $request, Periode $periode)
    {
        $validated = $request->validate([
            'nama_periode' => 'required|string|max:255',
            'tahun_ajaran' => 'required|string|max:20',
            'semester' => 'required|in:ganjil,genap',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
        ]);

        $periode->update($validated);

        return redirect()->route('admin.periode.index')
            ->with('success', 'Periode berhasil diperbarui!');
    }

    public function toggleStatus(Periode $periode)
    {
        // Nonaktifkan semua periode lain dulu
        if ($periode->status === 'nonaktif') {
            Periode::where('id', '!=', $periode->id)->update(['status' => 'nonaktif']);
            $periode->update(['status' => 'aktif']);
        } else {
            $periode->update(['status' => 'nonaktif']);
        }

        return redirect()->route('admin.periode.index')
            ->with('success', 'Status periode berhasil diubah!');
    }

    public function destroy(Periode $periode)
    {
        $periode->delete();
        return redirect()->route('admin.periode.index')
            ->with('success', 'Periode berhasil dihapus!');
    }
}