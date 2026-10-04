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

        if (!\App\Services\ProfanityFilterService::isClean($validated['nama_periode'])) {
            return back()->withInput()->with('error', 'Nama periode mengandung kata yang tidak pantas atau dilarang oleh filter moderasi.');
        }

        Periode::create($validated);

        return back()->with('success', 'Periode berhasil ditambahkan!');
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

        if (!\App\Services\ProfanityFilterService::isClean($validated['nama_periode'])) {
            return back()->withInput()->with('error', 'Nama periode mengandung kata yang tidak pantas atau dilarang oleh filter moderasi.');
        }

        $periode->update($validated);

        if ($periode->status === 'aktif') {
            \App\Models\Setting::set('active_periode_name', $periode->nama_periode);
            \App\Models\Setting::set('active_periode_changed_at', (string)now()->timestamp);
        }

        return back()->with('success', 'Periode berhasil diperbarui!');
    }

    public function toggleStatus(Periode $periode)
    {
        // Nonaktifkan semua periode lain jika periode ini diaktifkan
        if ($periode->status === 'nonaktif') {
            Periode::where('id', '!=', $periode->id)->update(['status' => 'nonaktif']);
            $periode->update(['status' => 'aktif']);
            \App\Models\Guru::recalculateAll($periode->id);
            \App\Models\Setting::set('active_periode_changed_at', (string)now()->timestamp);
            \App\Models\Setting::set('active_periode_id', (string)$periode->id);
            \App\Models\Setting::set('active_periode_name', $periode->nama_periode);
        } else {
            $periode->update(['status' => 'nonaktif']);
            \App\Models\Guru::recalculateAll();
            \App\Models\Setting::set('active_periode_changed_at', (string)now()->timestamp);
            \App\Models\Setting::set('active_periode_id', '0');
            \App\Models\Setting::set('active_periode_name', 'Tidak Ada Periode Aktif');
        }

        return back()->with('success', 'Status periode ' . $periode->nama_periode . ' berhasil diubah menjadi ' . strtoupper($periode->status) . '!');
    }

    public function destroy(Periode $periode)
    {
        if ($periode->status === 'aktif') {
            return back()->with('error', 'Periode yang sedang aktif tidak dapat dihapus. Nonaktifkan atau aktifkan periode lain terlebih dahulu.');
        }

        $nama = $periode->nama_periode;
        \App\Models\Penilaian::where('periode_id', $periode->id)->delete();
        $periode->delete();

        $activePeriode = Periode::where('status', 'aktif')->first();
        \App\Models\Guru::recalculateAll($activePeriode?->id);

        return back()->with('success', "Periode '{$nama}' dan seluruh data ulasan di dalamnya berhasil dihapus!");
    }
}