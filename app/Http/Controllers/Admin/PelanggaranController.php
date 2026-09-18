<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggaran;
use App\Models\User;
use Illuminate\Http\Request;

class PelanggaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Pelanggaran::with(['user.jurusan', 'user.kelas', 'guru', 'penilaian']);


        // Filter status baca
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->where('is_read', false);
            } elseif ($request->status === 'read') {
                $query->where('is_read', true);
            }
        }

        // Filter tipe
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        // Pencarian teks / NIS / Nama Siswa
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('isi_teks', 'like', "%{$search}%")
                  ->orWhere('kata_terdeteksi', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                  })
                  ->orWhereHas('guru', function ($g) use ($search) {
                      $g->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $pelanggarans = $query->latest()->paginate(15)->withQueryString();

        // Statistik ringkas
        $stats = [
            'total' => Pelanggaran::count(),
            'unread' => Pelanggaran::where('is_read', false)->count(),
            'penilaian' => Pelanggaran::where('tipe', 'penilaian_toxic')->count(),
            'kontak' => Pelanggaran::where('tipe', 'kontak_toxic')->count(),
        ];

        return view('admin.pelanggaran.index', compact('pelanggarans', 'stats'));
    }

    public function markAsRead(Pelanggaran $pelanggaran)
    {
        $pelanggaran->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return back()->with('success', 'Notifikasi pelanggaran ditandai telah dibaca.');
    }

    public function markAllAsRead()
    {
        Pelanggaran::where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return back()->with('success', 'Semua notifikasi pelanggaran telah ditandai dibaca.');
    }

    public function destroy(Pelanggaran $pelanggaran)
    {
        $pelanggaran->delete();

        return back()->with('success', 'Log pelanggaran berhasil dihapus.');
    }

    public function warnSiswa(Request $request, Pelanggaran $pelanggaran)
    {
        $actionType = $request->input('action_type', 'hanya_peringatan');
        $customReason = trim($request->input('deactivated_reason', ''));
        $successMessage = 'Tindakan peringatan kepada siswa berhasil dicatat.';

        if ($pelanggaran->user_id) {
            $siswa = User::find($pelanggaran->user_id);
            if ($siswa) {
                // Tambahkan hitungan peringatan jika diminta
                if ($request->boolean('increment_warning', true)) {
                    $siswa->increment('warning_count');
                }

                if ($actionType === 'nonaktif_permanen') {
                    $siswa->update([
                        'is_active' => false,
                        'deactivation_type' => 'permanen',
                        'deactivated_until' => null,
                        'deactivated_reason' => $customReason ?: 'Dinonaktifkan secara permanen oleh Admin / Operator Sekolah karena pelanggaran etika dan tata tertib.',
                    ]);
                    $successMessage = 'Akun ' . $siswa->name . ' berhasil dinonaktifkan secara permanen. Siswa harus menghubungi Admin / Operator Sekolah untuk pengaktifan kembali.';
                } elseif ($actionType === 'nonaktif_berkala') {
                    $days = (int) $request->input('duration_days', 3);
                    if ($request->filled('custom_until')) {
                        $deactivatedUntil = \Carbon\Carbon::parse($request->custom_until)->endOfDay();
                    } else {
                        $deactivatedUntil = now()->addDays($days);
                    }

                    $siswa->update([
                        'is_active' => false,
                        'deactivation_type' => 'berkala',
                        'deactivated_until' => $deactivatedUntil,
                        'deactivated_reason' => $customReason ?: 'Dinonaktifkan sementara oleh Admin / Operator Sekolah hingga ' . $deactivatedUntil->translatedFormat('d F Y') . ' karena pelanggaran ulasan.',
                    ]);
                    $successMessage = 'Akun ' . $siswa->name . ' dinonaktifkan berkala hingga ' . $deactivatedUntil->translatedFormat('d M Y H:i') . '.';
                } elseif ($actionType === 'aktifkan_kembali') {
                    $siswa->update([
                        'is_active' => true,
                        'deactivation_type' => null,
                        'deactivated_until' => null,
                        'deactivated_reason' => null,
                    ]);
                    $successMessage = 'Akun ' . $siswa->name . ' telah berhasil diaktifkan kembali.';
                }
            }
        }

        $pelanggaran->update([
            'is_read' => true,
            'read_at' => now(),
            'tindakan' => match ($actionType) {
                'nonaktif_permanen' => 'dinonaktifkan_permanen',
                'nonaktif_berkala' => 'dinonaktifkan_berkala',
                'aktifkan_kembali' => 'diaktifkan_kembali',
                default => 'diberi_peringatan',
            },
        ]);

        return back()->with('success', $successMessage);
    }
}

