<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KritikSaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Penilaian::with(['guru.jurusan', 'siswa', 'kelas', 'periode']);

        // Filter cakupan data (default: hanya yang memiliki kritik atau saran tertulis)
        if ($request->get('cakupan') !== 'semua') {
            $query->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNotNull('kritik')->whereRaw("TRIM(kritik) != ''");
                })->orWhere(function ($sub) {
                    $sub->whereNotNull('saran')->whereRaw("TRIM(saran) != ''");
                });
            });
        }

        // Pencarian (Siswa, Guru, NIS, NIP, isi kritik atau saran)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('kritik', 'like', "%{$search}%")
                  ->orWhere('saran', 'like', "%{$search}%")
                  ->orWhereHas('guru', function ($g) use ($search) {
                      $g->where('nama', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%");
                  })
                  ->orWhereHas('siswa', function ($s) use ($search) {
                      $s->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%");
                  });
            });
        }

        // Filter Rating Bintang (1 - 5) berdasarkan pembulatan nilai rata-rata (total_nilai / 5)
        if ($request->filled('rating')) {
            $rating = (int) $request->rating;
            if ($rating >= 1 && $rating <= 5) {
                $query->whereRaw('ROUND(total_nilai / 5) = ?', [$rating]);
            }
        }

        // Filter Status Sensor
        if ($request->filled('status')) {
            if ($request->status === 'censored') {
                $query->where('is_censored', true);
            } elseif ($request->status === 'clean') {
                $query->where('is_censored', false);
            }
        }

        $feedbacks = $query->latest()
            ->paginate(15)
            ->withQueryString();

        // Statistik ringkas untuk cards
        $avgTotal = Penilaian::avg('total_nilai');
        $stats = [
            'total_ulasan' => Penilaian::where(function ($q) {
                $q->whereNotNull('kritik')->whereRaw("TRIM(kritik) != ''")
                  ->orWhereNotNull('saran')->whereRaw("TRIM(saran) != ''");
            })->count(),
            'total_penilaian' => Penilaian::count(),
            'rata_rata_bintang' => $avgTotal ? round($avgTotal / 5, 2) : 0,
            'bintang_5' => Penilaian::whereRaw('ROUND(total_nilai / 5) = 5')->count(),
            'total_disensor' => Penilaian::where('is_censored', true)->count(),
        ];

        return view('admin.kritik-saran.index', compact('feedbacks', 'stats'));
    }

    public function destroy(Penilaian $kritikSaran)
    {
        $guru = $kritikSaran->guru;
        $siswaId = $kritikSaran->siswa_id;
        $guruName = $guru?->nama ?? 'guru';

        DB::transaction(function () use ($kritikSaran, $guru) {
            $kritikSaran->delete();
            $guru?->updateRataRata();
        });

        if ($siswaId) {
            \App\Models\Pelanggaran::create([
                'user_id' => $siswaId,
                'guru_id' => $guru?->id,
                'tipe' => 'ulasan_dihapus',
                'isi_teks' => 'Penilaian dan ulasan untuk ' . $guruName . ' dihapus oleh Admin.',
                'kata_terdeteksi' => ['moderasi_admin'],
                'is_read' => true,
                'siswa_is_read' => false,
                'tindakan' => 'penilaian_dihapus',
                'notifikasi_siswa' => 'Penilaian dan ulasan Anda untuk ' . $guruName . ' dihapus oleh Admin. Anda dapat menilai guru tersebut kembali.',
            ]);
        }

        return back()->with('success', 'Penilaian dan ulasan berhasil dihapus permanen. Rating guru telah dihitung ulang.');
    }

    public function warn(Penilaian $kritikSaran)
    {
        $kritikAsli = $kritikSaran->kritik;
        $saranAsli = $kritikSaran->saran;
        $kritikTersamar = \App\Services\ProfanityFilterService::mask($kritikAsli);
        $saranTersamar = \App\Services\ProfanityFilterService::mask($saranAsli);

        $kritikSaran->update([
            'kritik' => $kritikTersamar,
            'saran' => $saranTersamar,
            'is_censored' => true,
            'censored_reason' => 'Ulasan ini tidak memenuhi kriteria kebijakan komunitas.',
        ]);

        if ($kritikSaran->siswa) $kritikSaran->siswa->increment('warning_count');

        // Catat ke log pelanggaran
        try {
            \App\Models\Pelanggaran::create([
                'user_id' => $kritikSaran->siswa_id,
                'tipe' => 'komentar_disensor',
                'guru_id' => $kritikSaran->guru_id,
                'penilaian_id' => $kritikSaran->id,
                'kata_terdeteksi' => ['disensor_admin'],
                'isi_teks' => "Kritik: " . ($kritikAsli ?? '-') . " | Saran: " . ($saranAsli ?? '-'),
                'is_read' => false,
                'siswa_is_read' => false,
                'tindakan' => 'diberi_peringatan',
                'notifikasi_siswa' => 'Ulasan Anda disembunyikan oleh Admin karena tidak memenuhi etika penulisan. Penilaian Anda tetap tersimpan dan akun Anda tidak diblokir.',
            ]);
        } catch (\Throwable $e) {}

        return back()->with('success', 'Peringatan dicatat dan ulasan disembunyikan. Penilaian tetap tersimpan.');
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
