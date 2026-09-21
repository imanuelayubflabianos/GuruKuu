<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Penilaian;
use App\Models\Periode;
use Illuminate\Http\Request;

class PenilaianController extends Controller
{
    public function create(Guru $guru)
    {
        $user = auth()->user();
        $periodeAktif = Periode::where('status', 'aktif')->first();

        if (!$periodeAktif) {
            return redirect()->route('siswa.guru.index')->with('error', 'Tidak ada periode aktif saat ini.');
        }

        $kelasAktif = $user->kelas()
                          ->wherePivot('tahun_ajaran', $periodeAktif->tahun_ajaran)
                          ->first();

        // Cek apakah sudah menilai (unique constraint)
        $sudahMenilai = Penilaian::where('siswa_id', $user->id)
                                 ->where('guru_id', $guru->id)
                                 ->where('periode_id', $periodeAktif->id)
                                 ->exists();

        if ($sudahMenilai) {
            return redirect()->route('siswa.guru.show', $guru)->with('error', 'Anda sudah menilai guru ini pada periode ini.');
        }

        return view('siswa.penilaian.create', compact('guru', 'kelasAktif'));
    }

    public function store(Request $request, Guru $guru)
    {
        $request->validate([
            'kedisiplinan' => 'required|integer|min:1|max:5',
            'cara_mengajar' => 'required|integer|min:1|max:5',
            'komunikasi' => 'required|integer|min:1|max:5',
            'tanggung_jawab' => 'required|integer|min:1|max:5',
            'kreativitas' => 'required|integer|min:1|max:5',
            'keramahan' => 'required|integer|min:1|max:5',
            'kritik' => 'nullable|string|max:500',
            'saran' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();

        // 🛡️ FILTER OTOMATIS KATA KASAR, EJEKAN, & TOXIC (Bilingual: ID & EN)
        $combinedText = trim(($request->kritik ?? '') . ' ' . ($request->saran ?? ''));
        $profanityResult = \App\Services\ProfanityFilterService::check($combinedText);
        if (!$profanityResult['clean']) {
            // Catat log pelanggaran etika untuk notifikasi Admin
            try {
                \App\Models\Pelanggaran::create([
                    'user_id' => $user->id,
                    'tipe' => 'penilaian_toxic',
                    'guru_id' => $guru->id,
                    'kata_terdeteksi' => $profanityResult['detected'],
                    'isi_teks' => $combinedText,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'is_read' => false,
                    'siswa_is_read' => false,
                    'notifikasi_siswa' => 'Anda terkena pelanggaran karena mengirim ulasan yang mengandung kata tidak pantas. Ulasan belum dikirim dan Anda dapat memperbaikinya lalu menilai kembali.',
                    'tindakan' => 'diblokir_otomatis',
                ]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Gagal mencatat log pelanggaran: ' . $e->getMessage());
            }

            return redirect()
                ->route('siswa.penilaian.create', $guru)
                ->with('violation_popup', 'Anda melakukan pelanggaran etika. Penilaian dibatalkan dan belum disimpan. Silakan isi ulang rating serta tulis kritik dan saran dengan bahasa yang sopan.');
        }

        $periodeAktif = Periode::where('status', 'aktif')->first();

        if (!$periodeAktif) {
            return back()->with('error', 'Tidak ada periode aktif saat ini.');
        }

        $kelasAktif = $user->kelas()
                          ->wherePivot('tahun_ajaran', $periodeAktif->tahun_ajaran)
                          ->first();

        // Cek unique constraint
        $sudahMenilai = Penilaian::where('siswa_id', $user->id)
                                 ->where('guru_id', $guru->id)
                                 ->where('periode_id', $periodeAktif->id)
                                 ->exists();

        if ($sudahMenilai) {
            return back()->with('error', 'Anda sudah menilai guru ini pada periode ini.');
        }

        $totalNilai = Penilaian::hitungTotal($request->all());

        Penilaian::create([
            'siswa_id' => $user->id,
            'guru_id' => $guru->id,
            'periode_id' => $periodeAktif->id,
            'class_id' => $kelasAktif?->id, // ✅ Simpan class_id jika ada, null jika belum dipetakan
            'kedisiplinan' => $request->kedisiplinan,
            'cara_mengajar' => $request->cara_mengajar,
            'komunikasi' => $request->komunikasi,
            'tanggung_jawab' => $request->tanggung_jawab,
            'kreativitas' => $request->kreativitas,
            'keramahan' => $request->keramahan,
            'total_nilai' => $totalNilai,
            'kritik' => $request->filled('kritik') ? strip_tags(trim($request->kritik)) : null,
            'saran' => $request->filled('saran') ? strip_tags(trim($request->saran)) : null,
        ]);

        $guru->updateRataRata();

        return redirect()->route('siswa.guru.show', $guru)->with('success', 'Penilaian berhasil dikirim! Terima kasih atas kontribusi Anda.');
    }

    public function riwayat()
    {
        $riwayat = Penilaian::where('siswa_id', auth()->id())
                           ->with(['guru', 'periode', 'kelas'])
                           ->latest()
                           ->get();

        return view('siswa.riwayat.index', compact('riwayat'));
    }

    public function destroy(Penilaian $penilaian)
    {
        if ($penilaian->siswa_id !== auth()->id()) {
            return back()->with('error', 'Anda tidak memiliki izin untuk menghapus penilaian ini.');
        }

        $guruId = $penilaian->guru_id;
        $penilaian->delete();
        
        $guru = Guru::find($guruId);
        if ($guru) {
            $guru->updateRataRata();
        }

        return back()->with('success', 'Riwayat penilaian berhasil dihapus.');
    }
}