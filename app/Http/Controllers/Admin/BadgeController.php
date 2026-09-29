<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama_badge' => 'required|string|max:100',
            'deskripsi'  => 'nullable|string|max:500',
            'icon'       => 'required|string|max:50',
            'warna'      => 'nullable|string|max:20',
        ]);

        Badge::create([
            'nama_badge' => $request->nama_badge,
            'deskripsi'  => $request->deskripsi,
            'icon'       => $request->icon ?: '🏆',
            'warna'      => $request->warna ?: '#d97706',
        ]);

        return redirect()->back()->with('success', 'Badge baru berhasil ditambahkan!');
    }

    public function update(Request $request, Badge $badge)
    {
        $request->validate([
            'nama_badge' => 'required|string|max:100',
            'deskripsi'  => 'nullable|string|max:500',
            'icon'       => 'required|string|max:50',
            'warna'      => 'nullable|string|max:20',
        ]);

        $badge->update([
            'nama_badge' => $request->nama_badge,
            'deskripsi'  => $request->deskripsi,
            'icon'       => $request->icon ?: '🏆',
            'warna'      => $request->warna ?: '#d97706',
        ]);

        return redirect()->back()->with('success', 'Badge berhasil diperbarui!');
    }

    public function destroy(Badge $badge)
    {
        $badge->delete();
        return redirect()->back()->with('success', 'Badge berhasil dihapus!');
    }
}