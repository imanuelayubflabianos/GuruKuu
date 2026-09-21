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
        $feedbacks = Penilaian::with(['guru', 'siswa', 'kelas', 'periode'])
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('kritik')->whereRaw("TRIM(kritik) != ''");
                })->orWhere(function ($q) {
                    $q->whereNotNull('saran')->whereRaw("TRIM(saran) != ''");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.kritik-saran.index', compact('feedbacks'));
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
