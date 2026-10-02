<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelanggaran;
use App\Models\Penilaian;
use App\Models\UlasanReport;
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
            'reports' => UlasanReport::count(),
            'reported_items' => UlasanReport::distinct('penilaian_id')->count('penilaian_id'),
        ];

        // Peringkat Siswa yang melanggar aturan (role siswa)
        $topSiswa = Pelanggaran::whereHas('user', fn($q) => $q->where('role', 'siswa'))
            ->selectRaw('user_id, count(*) as total_pelanggaran, max(created_at) as latest_violation')
            ->groupBy('user_id')
            ->orderByDesc('total_pelanggaran')
            ->with(['user.jurusan', 'user.kelas'])
            ->get()
            ->map(function ($item) {
                $item->pelanggaran_list = Pelanggaran::where('user_id', $item->user_id)
                    ->latest()
                    ->take(8)
                    ->get();
                return $item;
            });

        // Peringkat Guru yang melanggar aturan (role guru)
        $topGuru = Pelanggaran::whereHas('user', fn($q) => $q->where('role', 'guru'))
            ->selectRaw('user_id, count(*) as total_pelanggaran, max(created_at) as latest_violation')
            ->groupBy('user_id')
            ->orderByDesc('total_pelanggaran')
            ->with(['user.jurusan'])
            ->get()
            ->map(function ($item) {
                $item->pelanggaran_list = Pelanggaran::where('user_id', $item->user_id)
                    ->latest()
                    ->take(8)
                    ->get();
                return $item;
            });

        // Laporan Ulasan dari User
        $reportedReviews = UlasanReport::with(['penilaian.guru', 'penilaian.siswa', 'user'])
            ->selectRaw('penilaian_id, count(*) as total_reports, max(created_at) as latest_report_at')
            ->groupBy('penilaian_id')
            ->orderByDesc('total_reports')
            ->paginate(15, ['*'], 'page_reports')
            ->withQueryString();

        $reportedReviews->getCollection()->transform(function ($item) {
            $item->reports_detail = UlasanReport::with('user')
                ->where('penilaian_id', $item->penilaian_id)
                ->latest()
                ->get();
            return $item;
        });

        return view('admin.pelanggaran.index', compact('pelanggarans', 'stats', 'topSiswa', 'topGuru', 'reportedReviews'));
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

    public function markAsReadBySiswa(Pelanggaran $pelanggaran)
    {
        abort_unless($pelanggaran->user_id === auth()->id(), 403);

        $pelanggaran->update(['siswa_is_read' => true]);

        return back()->with('success', 'Notifikasi pelanggaran ditandai telah dibaca.');
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
            'siswa_is_read' => false,
            'notifikasi_siswa' => $pelanggaran->user_id
                ? 'Admin telah meninjau pelanggaran Anda. Anda mendapat peringatan dan akun tidak diblokir otomatis.'
                : $pelanggaran->notifikasi_siswa,
            'tindakan' => match ($actionType) {
                'nonaktif_permanen' => 'dinonaktifkan_permanen',
                'nonaktif_berkala' => 'dinonaktifkan_berkala',
                'aktifkan_kembali' => 'diaktifkan_kembali',
                default => 'diberi_peringatan',
            },
        ]);

        return back()->with('success', $successMessage);
    }

    public function resetAll()
    {
        Pelanggaran::query()->delete();

        // Pulihkan seluruh akun siswa dan guru yang terkena sanksi/peringatan uji coba
        User::whereIn('role', ['siswa', 'guru'])->update([
            'warning_count' => 0,
            'is_active' => true,
            'deactivation_type' => null,
            'deactivated_until' => null,
            'deactivated_reason' => null,
        ]);

        return back()->with('success', 'Seluruh log pelanggaran telah berhasil direset dan seluruh akun siswa & guru dipulihkan normal.');
    }

    public function resetUser(User $user)
    {
        Pelanggaran::where('user_id', $user->id)->delete();

        $user->update([
            'warning_count' => 0,
            'is_active' => true,
            'deactivation_type' => null,
            'deactivated_until' => null,
            'deactivated_reason' => null,
        ]);

        $roleLabel = $user->role === 'guru' ? 'Guru' : 'Siswa';
        return back()->with('success', "Seluruh log pelanggaran akun {$roleLabel} {$user->name} berhasil direset dan akunnya telah dipulihkan aktif.");
    }

    public function tindakUser(Request $request, User $user)
    {
        $actionType = $request->input('action_type', 'hanya_peringatan');
        $customReason = trim($request->input('deactivated_reason', ''));
        $targetRole = $user->role === 'guru' ? 'Guru' : 'Siswa';

        if ($request->boolean('increment_warning', true)) {
            $user->increment('warning_count');
        }

        if ($actionType === 'nonaktif_permanen') {
            $user->update([
                'is_active' => false,
                'deactivation_type' => 'permanen',
                'deactivated_until' => null,
                'deactivated_reason' => $customReason ?: "Dinonaktifkan secara permanen oleh Admin / Operator Sekolah karena pelanggaran tata tertib.",
            ]);
            $msg = "Akun {$targetRole} {$user->name} berhasil dinonaktifkan secara permanen.";
        } elseif ($actionType === 'nonaktif_berkala') {
            $days = (int) $request->input('duration_days', 3);
            $deactivatedUntil = $request->filled('custom_until')
                ? \Carbon\Carbon::parse($request->custom_until)->endOfDay()
                : now()->addDays($days);

            $user->update([
                'is_active' => false,
                'deactivation_type' => 'berkala',
                'deactivated_until' => $deactivatedUntil,
                'deactivated_reason' => $customReason ?: "Dinonaktifkan sementara hingga " . $deactivatedUntil->translatedFormat('d F Y') . " karena pelanggaran ulasan/komentar.",
            ]);
            $msg = "Akun {$targetRole} {$user->name} berhasil dinonaktifkan sementara hingga " . $deactivatedUntil->translatedFormat('d M Y H:i') . ".";
        } elseif ($actionType === 'aktifkan_kembali') {
            $user->update([
                'is_active' => true,
                'deactivation_type' => null,
                'deactivated_until' => null,
                'deactivated_reason' => null,
            ]);
            $msg = "Akun {$targetRole} {$user->name} berhasil diaktifkan kembali.";
        } else {
            $msg = "Peringatan berhasil dicatat untuk akun {$targetRole} {$user->name}.";
        }

        return back()->with('success', $msg);
    }

    public function resetGuru(\App\Models\Guru $guru)
    {
        Pelanggaran::where('guru_id', $guru->id)->delete();

        return back()->with('success', "Seluruh riwayat log insiden untuk {$guru->nama} berhasil dibersihkan.");
    }

    public function censorReportedReview($penilaian)
    {
        if (!$penilaian instanceof Penilaian) {
            $penilaian = Penilaian::findOrFail($penilaian);
        }
        $penilaian->update([
            'is_censored' => true,
            'alasan_sensor' => 'Disensor oleh Administrator setelah menerima laporan pengguna.'
        ]);

        return back()->with('success', 'Ulasan siswa berhasil disensor dan disembunyikan dari tampilan publik.');
    }

    public function dismissReportedReview($penilaian)
    {
        $penilaianId = $penilaian instanceof Penilaian ? $penilaian->id : $penilaian;
        UlasanReport::where('penilaian_id', $penilaianId)->delete();
        Pelanggaran::where('penilaian_id', $penilaianId)->where('tipe', 'ulasan_reported')->delete();

        return back()->with('success', 'Laporan ulasan berhasil ditolak dan dibersihkan dari daftar laporan.');
    }

    public function deleteReportedReview($penilaian)
    {
        if (!$penilaian instanceof Penilaian) {
            $penilaian = Penilaian::findOrFail($penilaian);
        }
        $guru = $penilaian->guru;
        $periodeId = $penilaian->periode_id;
        $penilaianId = $penilaian->id;

        UlasanReport::where('penilaian_id', $penilaianId)->delete();
        Pelanggaran::where('penilaian_id', $penilaianId)->delete();
        $penilaian->delete();

        if ($guru) {
            $guru->updateRataRata($periodeId);
        }

        return back()->with('success', 'Ulasan siswa dan seluruh data laporannya berhasil dihapus permanen.');
    }
}

