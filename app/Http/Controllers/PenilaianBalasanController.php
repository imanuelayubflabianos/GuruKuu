<?php

namespace App\Http\Controllers;

use App\Models\Penilaian;
use App\Models\PenilaianBalasan;
use App\Models\Guru;
use App\Models\Pelanggaran;
use App\Services\ProfanityFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenilaianBalasanController extends Controller
{
    /**
     * Kirim balasan ulasan baru (Threaded Discussion)
     */
    public function store(Request $request, Penilaian $penilaian)
    {
        $user = Auth::user();
        if (!$user) {
            return back()->with('error', 'Silakan login terlebih dahulu untuk membalas ulasan.');
        }

        // Cek apakah akun pengirim sedang dinonaktifkan (berlaku untuk Siswa maupun Guru)
        if ($user->isDeactivated()) {
            return back()->with('error', 'Akun Anda sedang dinonaktifkan oleh Admin/Operator Sekolah. ' . ($user->deactivated_reason ?: 'Hubungi Admin / Operator Sekolah.'));
        }

        // Cek Hak Akses Berdasarkan Role
        if ($user->role === 'guru') {
            $guru = Guru::where('nip', $user->nis)
                ->orWhere('email', $user->email)
                ->first();

            if (!$guru || $penilaian->guru_id != $guru->id) {
                return back()->with('error', 'Anda hanya dapat membalas ulasan yang ditujukan untuk diri Anda.');
            }
        } elseif ($user->role === 'siswa') {
            if ($penilaian->siswa_id != $user->id) {
                return back()->with('error', 'Anda hanya berhak membalas pada ulasan yang Anda buat.');
            }
        } elseif ($user->role !== 'admin') {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk membalas ulasan ini.');
        }

        $validated = $request->validate([
            'pesan' => 'required|string|min:2|max:1000',
            'parent_id' => 'nullable|exists:penilaian_balasans,id',
        ], [
            'pesan.required' => 'Isi balasan tidak boleh kosong.',
            'pesan.max' => 'Panjang balasan maksimal 1000 karakter.',
        ]);

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

        PenilaianBalasan::create([
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
