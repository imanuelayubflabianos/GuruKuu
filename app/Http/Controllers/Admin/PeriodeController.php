<?php
// app/Http/Controllers/Admin/PeriodeController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Penilaian;
use App\Models\Periode;
use App\Models\Setting;
use App\Services\ProfanityFilterService;
use Illuminate\Http\Request;

class PeriodeController extends Controller
{
    public function index()
    {
        Periode::syncAllStatuses();
        $periode = Periode::latest('tanggal_mulai')->get();
        return view('admin.periode.index', compact('periode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_periode' => ['required', 'string', 'max:255', 'unique:periode,nama_periode'],
            'tahun_ajaran' => 'required|string|max:20',
            'semester' => 'required|in:ganjil,genap',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status' => 'nullable|in:otomatis,aktif,terjadwal,selesai,nonaktif',
        ]);

        if (!ProfanityFilterService::isClean($validated['nama_periode'])) {
            return back()->withInput()->with('error', 'Nama periode mengandung kata yang tidak pantas atau dilarang oleh filter moderasi.');
        }

        $inputStatus = $validated['status'] ?? 'otomatis';
        if ($inputStatus === 'otomatis') {
            $status = Periode::calculateStatusFromDates($validated['tanggal_mulai'], $validated['tanggal_selesai']);
        } else {
            $status = $inputStatus;
        }
        $validated['status'] = $status;

        $periode = Periode::create($validated);
        Periode::syncAllStatuses();

        return back()->with('success', 'Periode berhasil ditambahkan!');
    }

    public function update(Request $request, Periode $periode)
    {
        $validated = $request->validate([
            'nama_periode' => ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('periode', 'nama_periode')->ignore($periode->id)],
            'tahun_ajaran' => 'required|string|max:20',
            'semester' => 'required|in:ganjil,genap',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status' => 'nullable|in:otomatis,aktif,terjadwal,selesai,nonaktif',
        ]);

        if (!ProfanityFilterService::isClean($validated['nama_periode'])) {
            return back()->withInput()->with('error', 'Nama periode mengandung kata yang tidak pantas atau dilarang oleh filter moderasi.');
        }

        $inputStatus = $validated['status'] ?? ($periode->status ?: 'otomatis');
        if ($inputStatus === 'otomatis') {
            $status = Periode::calculateStatusFromDates($validated['tanggal_mulai'], $validated['tanggal_selesai']);
        } else {
            $status = $inputStatus;
        }
        $validated['status'] = $status;

        $periode->update($validated);
        Periode::syncAllStatuses();

        if ($periode->status === 'aktif') {
            Setting::set('active_periode_name', $periode->nama_periode);
            Setting::set('active_periode_changed_at', (string)now()->timestamp);
        }

        return back()->with('success', 'Periode berhasil diperbarui!');
    }

    public function toggleStatus(Periode $periode)
    {
        if ($periode->status !== 'aktif') {
            $now = now();
            $updates = ['status' => 'aktif'];
            if ($periode->tanggal_mulai && $now->lt($periode->tanggal_mulai)) {
                $updates['tanggal_mulai'] = $now;
            }
            if ($periode->tanggal_selesai && $now->gt($periode->tanggal_selesai)) {
                $updates['tanggal_selesai'] = $now->copy()->addMonths(6);
            }

            Periode::where('id', '!=', $periode->id)->update(['status' => 'selesai']);
            $periode->update($updates);
            Guru::recalculateAll($periode->id);
            Guru::clearLeaderboardCache();
            Setting::set('active_periode_changed_at', (string)now()->timestamp);
            Setting::set('active_periode_id', (string)$periode->id);
            Setting::set('active_periode_name', $periode->nama_periode);
        } else {
            $periode->update(['status' => 'selesai']);
            Periode::syncAllStatuses();
            $periodeLain = Periode::where('status', 'aktif')->first();
            if ($periodeLain) {
                Guru::recalculateAll($periodeLain->id);
                Guru::clearLeaderboardCache();
                Setting::set('active_periode_changed_at', (string)now()->timestamp);
                Setting::set('active_periode_id', (string)$periodeLain->id);
                Setting::set('active_periode_name', $periodeLain->nama_periode);
            } else {
                Guru::recalculateAll();
                Guru::clearLeaderboardCache();
                Setting::set('active_periode_changed_at', (string)now()->timestamp);
                Setting::set('active_periode_id', '0');
                Setting::set('active_periode_name', 'Tidak Ada Periode Aktif');
            }
        }

        return back()->with('success', 'Status periode ' . $periode->nama_periode . ' berhasil diubah menjadi ' . strtoupper($periode->status) . '!');
    }

    public function destroy(Periode $periode)
    {
        if ($periode->status === 'aktif') {
            return back()->with('error', 'Periode yang sedang aktif tidak dapat dihapus. Nonaktifkan atau aktifkan periode lain terlebih dahulu.');
        }

        $nama = $periode->nama_periode;
        Penilaian::where('periode_id', $periode->id)->delete();
        $periode->delete();

        $activePeriode = Periode::where('status', 'aktif')->first();
        Guru::recalculateAll($activePeriode?->id);
        Guru::clearLeaderboardCache();

        return back()->with('success', "Periode '{$nama}' dan seluruh data ulasan di dalamnya berhasil dihapus!");
    }
}
