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
        $query = Penilaian::with(['guru.jurusan', 'siswa.kelas', 'kelas', 'periode', 'balasans.user']);

        // Filter Kelas
        if ($request->filled('kelas_id')) {
            $kelasId = (int) $request->kelas_id;
            $classMatches = (clone $query)->where(function ($q) use ($kelasId) {
                $q->where('class_id', $kelasId)
                  ->orWhereHas('siswa.kelas', function ($sk) use ($kelasId) {
                      $sk->where('kelas.id', $kelasId);
                  });
            });
            if (!$request->filled('search') || $classMatches->exists()) {
                $query->where(function ($q) use ($kelasId) {
                    $q->where('class_id', $kelasId)
                      ->orWhereHas('siswa.kelas', function ($sk) use ($kelasId) {
                          $sk->where('kelas.id', $kelasId);
                      });
                });
            }
        }

        // Filter cakupan data ulasan / vote:
        // Default: semua evaluasi/vote masuk (agar rating bintang tanpa kritik/saran tetap tampil & tidak hilang).
        // Pilihan:
        // - 'ada_teks': hanya penilaian yang memiliki kritik atau saran tertulis
        // - 'hanya_bintang': hanya penilaian rating bintang murni tanpa teks
        if ($request->get('cakupan') === 'ada_teks') {
            $query->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNotNull('kritik')->whereRaw("TRIM(kritik) != ''");
                })->orWhere(function ($sub) {
                    $sub->whereNotNull('saran')->whereRaw("TRIM(saran) != ''");
                });
            });
        } elseif ($request->get('cakupan') === 'hanya_bintang') {
            $query->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNull('kritik')->orWhereRaw("TRIM(kritik) = ''");
                })->where(function ($sub) {
                    $sub->whereNull('saran')->orWhereRaw("TRIM(saran) = ''");
                });
            });
        }

        // Pencarian (Siswa, Guru, NIS, NIP, isi kritik atau saran - multi kata)
        if ($request->filled('search')) {
            $terms = preg_split('/\s+/', trim($request->search), -1, PREG_SPLIT_NO_EMPTY);
            foreach ($terms as $term) {
                $query->where(function ($q) use ($term) {
                    $q->where('kritik', 'like', "%{$term}%")
                      ->orWhere('saran', 'like', "%{$term}%")
                      ->orWhereHas('guru', function ($g) use ($term) {
                          $g->where('nama', 'like', "%{$term}%")
                            ->orWhere('nip', 'like', "%{$term}%");
                      })
                      ->orWhereHas('siswa', function ($s) use ($term) {
                          $s->where('name', 'like', "%{$term}%")
                            ->orWhere('nis', 'like', "%{$term}%");
                      });
                });
            }
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

        // Filter Periode
        if ($request->filled('periode_id')) {
            $query->where('periode_id', $request->periode_id);
        }

        $feedbacks = $query->latest()
            ->paginate(15)
            ->withQueryString();

        $semuaPeriode = \App\Models\Periode::orderBy('tahun_ajaran', 'desc')->orderBy('semester', 'desc')->get();

        // Data Kelas untuk dropdown filter
        $kelasList = \App\Models\Kelas::with('jurusan')
            ->orderBy('tingkat', 'asc')
            ->orderBy('nama_kelas', 'asc')
            ->get();

        // Statistik ringkas untuk cards (sinkron dinamis mengikuti filter periode, kelas, dan pencarian)
        $statsBase = Penilaian::query();

        if ($request->filled('periode_id')) {
            $statsBase->where('periode_id', $request->periode_id);
        }

        if ($request->filled('kelas_id')) {
            $kelasId = (int) $request->kelas_id;
            $statsBase->where(function ($q) use ($kelasId) {
                $q->where('class_id', $kelasId)
                  ->orWhereHas('siswa.kelas', function ($sk) use ($kelasId) {
                      $sk->where('kelas.id', $kelasId);
                  });
            });
        }

        if ($request->filled('search')) {
            $terms = preg_split('/\s+/', trim($request->search), -1, PREG_SPLIT_NO_EMPTY);
            foreach ($terms as $term) {
                $statsBase->where(function ($q) use ($term) {
                    $q->where('kritik', 'like', "%{$term}%")
                      ->orWhere('saran', 'like', "%{$term}%")
                      ->orWhereHas('guru', function ($g) use ($term) {
                          $g->where('nama', 'like', "%{$term}%")
                            ->orWhere('nip', 'like', "%{$term}%");
                      })
                      ->orWhereHas('siswa', function ($s) use ($term) {
                          $s->where('name', 'like', "%{$term}%")
                            ->orWhere('nis', 'like', "%{$term}%");
                      });
                });
            }
        }

        $avgTotal = (clone $statsBase)->avg('total_nilai');
        $stats = [
            'total_ulasan' => (clone $statsBase)->where(function ($q) {
                $q->whereNotNull('kritik')->whereRaw("TRIM(kritik) != ''")
                  ->orWhereNotNull('saran')->whereRaw("TRIM(saran) != ''");
            })->count(),
            'total_penilaian' => (clone $statsBase)->count(),
            'rata_rata_bintang' => $avgTotal ? round($avgTotal / 5, 2) : 0,
            'bintang_5' => (clone $statsBase)->whereRaw('ROUND(total_nilai / 5) = 5')->count(),
            'total_disensor' => (clone $statsBase)->where('is_censored', true)->count(),
        ];

        return view('admin.kritik-saran.index', compact('feedbacks', 'stats', 'kelasList', 'semuaPeriode'));
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

    public function destroyBatch(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return back()->with('error', 'Pilih minimal satu ulasan untuk dihapus.');
        }

        $penilaians = Penilaian::with('guru')->whereIn('id', $ids)->get();
        if ($penilaians->isEmpty()) {
            return back()->with('error', 'Tidak ada data ulasan yang ditemukan.');
        }

        $guruIds = [];
        DB::transaction(function () use ($penilaians, $ids, &$guruIds) {
            \App\Models\PenilaianBalasan::whereIn('penilaian_id', $ids)->delete();
            \App\Models\PenilaianHelpful::whereIn('penilaian_id', $ids)->delete();
            \App\Models\Pelanggaran::whereIn('penilaian_id', $ids)->delete();

            foreach ($penilaians as $p) {
                if ($p->guru_id && !in_array($p->guru_id, $guruIds)) {
                    $guruIds[] = $p->guru_id;
                }
                $p->delete();
            }
        });

        foreach ($guruIds as $gid) {
            \App\Models\Guru::find($gid)?->updateRataRata();
        }

        return back()->with('success', count($penilaians) . ' ulasan berhasil dihapus permanen.');
    }
}
