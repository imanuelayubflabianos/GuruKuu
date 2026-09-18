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
            'is_censored' => false,
            'censored_reason' => null,
        ]);

        return back()->with('success', 'Ulasan kritik & saran berhasil dihapus permanen.');
    }

    public function warn(Penilaian $kritikSaran)
    {
        $kritikSaran->update([
            'is_censored' => true,
            'censored_reason' => 'Ulasan ini tidak memenuhi kriteria kebijakan komunitas.',
        ]);

        if ($kritikSaran->siswa) {
            $kritikSaran->siswa->increment('warning_count');
        }

        // Catat ke log pelanggaran
        try {
            \App\Models\Pelanggaran::create([
                'user_id' => $kritikSaran->siswa_id,
                'tipe' => 'komentar_disensor',
                'guru_id' => $kritikSaran->guru_id,
                'penilaian_id' => $kritikSaran->id,
                'kata_terdeteksi' => ['disensor_admin'],
                'isi_teks' => "Kritik: " . ($kritikSaran->kritik ?? '-') . " | Saran: " . ($kritikSaran->saran ?? '-'),
                'is_read' => true,
                'read_at' => now(),
                'tindakan' => 'diberi_peringatan',
            ]);
        } catch (\Throwable $e) {}

        return back()->with('success', 'Peringatan berhasil dikirim kepada siswa dan ulasan telah disensor/disembunyikan.');
    }

    public function unwarn(Penilaian $kritikSaran)
    {
        $kritikSaran->update([
            'is_censored' => false,
            'censored_reason' => null,
        ]);

        return back()->with('success', 'Status sensor ulasan berhasil dibatalkan.');
    }
}