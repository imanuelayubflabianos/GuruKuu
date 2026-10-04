<?php

namespace App\Http\Controllers;

use App\Models\Pelanggaran;
use App\Models\Penilaian;
use App\Models\UlasanReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class UlasanReportController extends Controller
{
    public function store(Request $request, Penilaian $penilaian)
    {
        if (!Schema::hasTable('ulasan_reports')) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fitur laporan sedang dalam pemeliharaan (tabel belum dimigrasi).',
                ], 503);
            }
            return back()->with('error', 'Fitur laporan sedang dalam pemeliharaan.');
        }

        $validated = $request->validate([
            'alasan' => 'required|string|in:kata_kasar,ujaran_kebencian,fitnah,spam,lainnya',
            'catatan' => 'nullable|string|max:255',
        ]);

        if (!empty($validated['catatan'])) {
            if (\App\Services\ProfanityFilterService::containsLink($validated['catatan'])) {
                $err = 'Catatan laporan tidak boleh mengandung tautan / link URL luar.';
                return $request->wantsJson() ? response()->json(['success' => false, 'message' => $err], 422) : back()->withInput()->with('error', $err);
            }
            if (!\App\Services\ProfanityFilterService::isClean($validated['catatan'])) {
                $err = 'Catatan laporan mengandung kata yang melanggar etika moderasi bahasa.';
                return $request->wantsJson() ? response()->json(['success' => false, 'message' => $err], 422) : back()->withInput()->with('error', $err);
            }
        }

        $userId = auth()->id();
        $ip = $request->ip();

        // 🛡️ ANTI-SPAM: Cukup 1 kali report per user / IP untuk ulasan yang sama
        $hasReported = false;
        if ($userId) {
            $hasReported = UlasanReport::where('penilaian_id', $penilaian->id)
                ->where('user_id', $userId)
                ->exists();
        } else {
            $hasReported = UlasanReport::where('penilaian_id', $penilaian->id)
                ->where('ip_address', $ip)
                ->exists();
        }

        if ($hasReported) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah pernah melaporkan ulasan ini sebelumnya. Laporan Anda sudah kami catat untuk diverifikasi oleh Administrator.',
                ], 422);
            }
            return back()->with('error', 'Anda sudah pernah melaporkan ulasan ini sebelumnya.');
        }

        // Catat laporan
        $report = UlasanReport::create([
            'penilaian_id' => $penilaian->id,
            'user_id' => $userId,
            'ip_address' => $ip,
            'alasan' => $validated['alasan'],
            'catatan' => $validated['catatan'] ?? null,
        ]);

        // Catat notifikasi ke tabel Pelanggaran sistem
        $alasanLabel = match ($validated['alasan']) {
            'kata_kasar' => 'Kata Kasar / Tidak Pantas',
            'ujaran_kebencian' => 'Ujaran Kebencian / Menghina',
            'fitnah' => 'Fitnah / Pencemaran Nama Baik Guru',
            'spam' => 'Spam / Tidak Relevan',
            default => 'Pelanggaran Kebijakan',
        };

        Pelanggaran::create([
            'user_id' => $penilaian->siswa_id,
            'guru_id' => $penilaian->guru_id,
            'penilaian_id' => $penilaian->id,
            'tipe' => 'laporan_pengguna',
            'isi_teks' => "Kritik: " . ($penilaian->kritik ?: '-') . " | Saran: " . ($penilaian->saran ?: '-'),
            'kata_terdeteksi' => ['Report: ' . $alasanLabel],
            'ip_address' => $ip,
            'user_agent' => $request->userAgent(),
            'tindakan' => 'menunggu_verifikasi_admin',
            'is_read' => false,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Terima kasih atas laporan Anda. Tim Administrator akan segera meninjau ulasan ini demi kenyamanan bersama.',
            ]);
        }

        return back()->with('success', 'Laporan ulasan berhasil dikirimkan dan masuk ke antrean audit Administrator.');
    }
}
