<?php
// app/Http/Controllers/Admin/BadgeController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\Guru;
use App\Models\Periode;
use App\Models\Penghargaan;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    public function index()
    {
        $badge = Badge::withCount('penghargaan')->latest()->get();
        return view('admin.badge.index', compact('badge'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_badge' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'warna' => 'nullable|string|max:20',
        ]);

        Badge::create($validated);

        return redirect()->route('admin.badge.index')
            ->with('success', 'Badge berhasil ditambahkan!');
    }

    public function update(Request $request, Badge $badge)
    {
        $validated = $request->validate([
            'nama_badge' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'warna' => 'nullable|string|max:20',
        ]);

        $badge->update($validated);

        return redirect()->route('admin.badge.index')
            ->with('success', 'Badge berhasil diperbarui!');
    }

    public function destroy(Badge $badge)
    {
        $badge->delete();
        return redirect()->route('admin.badge.index')
            ->with('success', 'Badge berhasil dihapus!');
    }

    // Berikan badge ke guru
    public function award(Request $request)
    {
        $validated = $request->validate([
            'guru_id' => 'required|exists:guru,id',
            'badge_id' => 'required|exists:badge,id',
            'periode_id' => 'required|exists:periode,id',
        ]);

        Penghargaan::updateOrCreate(
            [
                'guru_id' => $validated['guru_id'],
                'badge_id' => $validated['badge_id'],
                'periode_id' => $validated['periode_id'],
            ]
        );

        return redirect()->route('admin.badge.index')
            ->with('success', 'Badge berhasil diberikan ke guru!');
    }
}