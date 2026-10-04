<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use App\Models\PenilaianBalasan;
use App\Models\Guru;
use App\Models\Pelanggaran;
use App\Services\ProfanityFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PenilaianBalasanController extends Controller
{
    /**
     * Kirim balasan ulasan baru (Threaded Discussion)
     */
    public function store(Request $request, Penilaian $penilaian)
    {
        $user = Auth::user();
        if (!$user) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Silakan login terlebih dahulu untuk membalas ulasan.'], 401);
            }
            return back()->with('error', 'Silakan login terlebih dahulu untuk membalas ulasan.');
        }

        // Cek apakah akun pengirim sedang dinonaktifkan (berlaku untuk Siswa maupun Guru)
        if ($user->isDeactivated()) {
            $msg = 'Akun Anda sedang dinonaktifkan oleh Admin/Operator Sekolah. ' . ($user->deactivated_reason ?: 'Hubungi Admin / Operator Sekolah.');
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return back()->with('error', $msg);
        }

        // Cek Hak Akses Berdasarkan Role (Privasi: Hanya Siswa pembuat ulasan dan Guru yang dinilai; Admin hanya memantau)
        if ($user->role === 'admin') {
            $msg = 'Administrator hanya berwenang memantau diskusi dan tidak dapat mengirim balasan.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return back()->with('error', $msg);
        } elseif ($user->role === 'guru') {
            $guru = Guru::where('nip', $user->nis)
                ->orWhere('email', $user->email)
                ->first();

            if (!$guru || $penilaian->guru_id != $guru->id) {
                $msg = 'Anda hanya dapat membalas ulasan yang ditujukan untuk diri Anda.';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 403);
                }
                return back()->with('error', $msg);
            }
        } elseif ($user->role === 'siswa') {
            if ($penilaian->siswa_id != $user->id) {
                $msg = 'Diskusi ini bersifat pribadi antara guru dan siswa yang menilai.';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 403);
                }
                return back()->with('error', $msg);
            }
        } else {
            $msg = 'Anda tidak memiliki hak akses untuk membalas ulasan ini.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return back()->with('error', $msg);
        }

        // ⏱️ SLOWMODE ANTI-SPAM (Mirip Discord Cooldown)
        $cooldownSeconds = 25;
        $slowmodeKey = "slowmode_reply_{$user->id}";
        if ($user->role !== 'admin' && Cache::has($slowmodeKey)) {
            $expiresAt = Cache::get($slowmodeKey);
            $remaining = max(1, $expiresAt - now()->timestamp);
            $msg = "Mode lambat (Slowmode) aktif seperti Discord! Harap tunggu {$remaining} detik sebelum dapat mengirim pesan lagi.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg, 'remaining' => $remaining], 429);
            }
            return back()->withInput()->with('error', $msg);
        }

        $validated = $request->validate([
            'pesan' => 'required|string|min:2|max:255',
            'parent_id' => 'nullable|exists:penilaian_balasans,id',
        ], [
            'pesan.required' => 'Isi balasan tidak boleh kosong.',
            'pesan.max' => 'Panjang balasan melebihi batas maksimal 255 karakter.',
        ]);

        // 🛡️ KEAMANAN: Cegah link/URL sembarangan
        if (\App\Services\ProfanityFilterService::containsLink($validated['pesan'])) {
            return back()->withInput()->with('error', 'Balasan ulasan tidak boleh mengandung tautan / link URL luar demi keamanan sistem.');
        }

        // PENEGAKAN FILTER KATA KASAR (SISWA DAN GURU TIDAK KEBAL)
        $profanityFilter = new ProfanityFilterService();
        $filterResult = $profanityFilter->check($validated['pesan']);

        if (!$filterResult['clean']) {
            $detectedWords = $filterResult['detected'] ?? [];

            // Otomatis catat ke tabel Pelanggaran sistem
            Pelanggaran::create([
                'user_id' => $user->id,
                'guru_id' => $penilaian->guru_id,
                'penilaian_id' => $penilaian->id,
                'tipe' => 'balasan_ulasan',
                'isi_teks' => $validated['pesan'],
                'kata_terdeteksi' => $detectedWords,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'tindakan' => 'diblokir_otomatis',
                'is_read' => false,
            ]);

            $user->increment('warning_count');

            $wordList = implode(', ', $detectedWords);
            return back()->with('error', "Balasan Anda diblokir karena mengandung kata yang melanggar etika dan tata tertib: \"{$wordList}\". Pelanggaran ini telah dicatat dalam sistem dan dilaporkan ke Admin.");
        }

        // Simpan balasan
        $isAnonim = false;
        if ($user->role === 'siswa') {
            // Siswa tetap tampil anonim agar aman dari penilaian subjektif
            $isAnonim = true;
        }

        $balasan = PenilaianBalasan::create([
            'penilaian_id' => $penilaian->id,
            'user_id' => $user->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'pesan' => trim($validated['pesan']),
            'role' => $user->role,
            'is_anonim' => $isAnonim,
        ]);

        // Sinkronisasi data lama penilaian jika pengirim adalah guru
        if ($user->role === 'guru') {
            $penilaian->update([
                'balasan_guru' => trim($validated['pesan']),
                'balasan_guru_at' => now(),
            ]);
        }

        // Aktifkan cooldown Slowmode (25 detik) untuk non-admin
        if ($user->role !== 'admin') {
            Cache::put($slowmodeKey, now()->addSeconds($cooldownSeconds)->timestamp, $cooldownSeconds);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Balasan ulasan berhasil dikirimkan.',
                'cooldown' => $user->role !== 'admin' ? $cooldownSeconds : 0,
                'balasan' => [
                    'id' => $balasan->id,
                    'pesan' => $balasan->pesan,
                    'role' => $balasan->role,
                    'is_anonim' => $balasan->is_anonim,
                    'created_at_human' => $balasan->created_at->diffForHumans(),
                ]
            ]);
        }

        return back()->with('success', 'Balasan ulasan berhasil dikirimkan.');
    }

    /**
     * Hapus balasan
     */
    public function destroy(PenilaianBalasan $balasan)
    {
        $user = Auth::user();
        if (!$user) {
            return back()->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($user->role !== 'admin' && $balasan->user_id !== $user->id) {
            return back()->with('error', 'Anda tidak memiliki wewenang untuk menghapus balasan ini.');
        }

        $penilaian = $balasan->penilaian;
        $balasan->delete();

        // Update ulasan jika balasan guru terakhir dihapus
        if ($balasan->role === 'guru') {
            $lastGuruReply = PenilaianBalasan::where('penilaian_id', $penilaian->id)
                ->where('role', 'guru')
                ->latest()
                ->first();

            $penilaian->update([
                'balasan_guru' => $lastGuruReply ? $lastGuruReply->pesan : null,
                'balasan_guru_at' => $lastGuruReply ? $lastGuruReply->created_at : null,
            ]);
        }

        return back()->with('success', 'Balasan ulasan berhasil dihapus.');
    }
}
