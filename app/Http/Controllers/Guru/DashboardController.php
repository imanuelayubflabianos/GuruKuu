<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Penilaian;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Ambil data guru yang login berdasarkan NIP / NIS di tabel users atau email
        $guru = Guru::where('nip', $user->nis)
            ->orWhere('email', $user->email)
            ->first();
        
        if (!$guru) {
            return redirect()->route('login')->with('error', 'Data profil Guru tidak ditemukan dalam sistem.');
        }

        $periodeAktif = Periode::where('status', 'aktif')->first();

        // Ambil ulasan & kritik saran dari siswa untuk guru ini (100% Anonim)
        $ulasanTerbaru = Penilaian::where('guru_id', $guru->id)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('kritik')->whereRaw("TRIM(kritik) != ''");
                })->orWhere(function ($q) {
                    $q->whereNotNull('saran')->whereRaw("TRIM(saran) != ''");
                });
            })
            ->with(['periode'])
            ->latest()
            ->get();

        return view('guru.dashboard', compact(
            'guru',
            'ulasanTerbaru',
            'periodeAktif'
        ));
    }

    public function ulasan(Request $request)
    {
        $user = Auth::user();
        $guru = Guru::where('nip', $user->nis)
            ->orWhere('email', $user->email)
            ->first();

        if (!$guru) {
            return redirect()->route('login')->with('error', 'Data profil Guru tidak ditemukan dalam sistem.');
        }

        $periodeAktif = Periode::where('status', 'aktif')->first();
        $semuaPeriode = Periode::orderBy('tahun_ajaran', 'desc')->orderBy('semester', 'desc')->get();

        $query = Penilaian::where('guru_id', $guru->id)->with(['periode', 'kelas']);

        // Filter status balasan
        if ($request->filled('status')) {
            if ($request->status === 'dibalas') {
                $query->whereNotNull('balasan_guru')->whereRaw("TRIM(balasan_guru) != ''");
            } elseif ($request->status === 'belum') {
                $query->where(function ($q) {
                    $q->whereNull('balasan_guru')->orWhereRaw("TRIM(balasan_guru) = ''");
                });
            }
        }

        // Filter periode
        if ($request->filled('periode_id')) {
            $query->where('periode_id', $request->periode_id);
        }

        // Filter kritik & saran teks
        if ($request->filled('tipe')) {
            if ($request->tipe === 'ada_teks') {
                $query->where(function ($q) {
                    $q->where(function ($sub) {
                        $sub->whereNotNull('kritik')->whereRaw("TRIM(kritik) != ''");
                    })->orWhere(function ($sub) {
                        $sub->whereNotNull('saran')->whereRaw("TRIM(saran) != ''");
                    });
                });
            }
        }

        $semuaUlasan = (clone $query)->latest()->paginate(10)->withQueryString();

        $totalUlasan = Penilaian::where('guru_id', $guru->id)->count();
        $totalDibalas = Penilaian::where('guru_id', $guru->id)->whereNotNull('balasan_guru')->whereRaw("TRIM(balasan_guru) != ''")->count();
        $totalBelumDibalas = $totalUlasan - $totalDibalas;

        return view('guru.ulasan.index', compact(
            'guru',
            'semuaUlasan',
            'totalUlasan',
            'totalDibalas',
            'totalBelumDibalas',
            'periodeAktif',
            'semuaPeriode'
        ));
    }

    public function replyPenilaian(Request $request, Penilaian $penilaian)
    {
        $request->validate([
            'balasan_guru' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $guru = Guru::where('nip', $user->nis)
            ->orWhere('email', $user->email)
            ->first();

        if (!$guru || $penilaian->guru_id !== $guru->id) {
            return back()->with('error', 'Anda hanya dapat membalas ulasan yang ditujukan untuk profil Anda.');
        }

        // 🛡️ Filter kata tidak pantas pada balasan guru (Guru tidak kebal aturan)
        $profanityResult = \App\Services\ProfanityFilterService::check($request->balasan_guru);
        if (!$profanityResult['clean']) {
            $detected = $profanityResult['detected'] ?? [];

            // Otomatis catat pelanggaran Guru ke log admin
            \App\Models\Pelanggaran::create([
                'user_id' => $user->id,
                'guru_id' => $guru->id,
                'penilaian_id' => $penilaian->id,
                'tipe' => 'balasan_guru',
                'isi_teks' => $request->balasan_guru,
                'kata_terdeteksi' => $detected,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'tindakan' => 'diblokir_otomatis',
                'is_read' => false,
            ]);

            $user->increment('warning_count');

            $words = implode(', ', $detected);
            return back()->withInput()->with('error', "Balasan Anda diblokir karena mengandung kata tidak pantas: \"{$words}\". Pelanggaran ini telah dicatat dalam log sistem dan dilaporkan ke Admin.");
        }

        $penilaian->update([
            'balasan_guru' => trim($request->balasan_guru),
            'balasan_guru_at' => now(),
        ]);

        // Simpan ke thread diskusi penilaian_balasans
        \App\Models\PenilaianBalasan::create([
            'penilaian_id' => $penilaian->id,
            'user_id' => $user->id,
            'pesan' => trim($request->balasan_guru),
            'role' => 'guru',
            'is_anonim' => false,
        ]);

        return back()->with('success', 'Balasan ulasan berhasil disimpan dan ditampilkan!');
    }

    public function deleteReplyPenilaian(Penilaian $penilaian)
    {
        $user = Auth::user();
        $guru = Guru::where('nip', $user->nis)
            ->orWhere('email', $user->email)
            ->first();

        if (!$guru || $penilaian->guru_id !== $guru->id) {
            return back()->with('error', 'Anda hanya dapat menghapus balasan ulasan yang ditujukan untuk profil Anda.');
        }

        $penilaian->update([
            'balasan_guru' => null,
            'balasan_guru_at' => null,
        ]);

        return back()->with('success', 'Balasan ulasan berhasil dihapus.');
    }

    public function leaderboard()
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        
        $leaderboard = Guru::with('jurusan')
            ->where('total_penilaian', '>', 0)
            ->orderBy('rata_rata_nilai', 'desc')
            ->orderBy('total_penilaian', 'desc')
            ->get();

        // Fallback jika belum ada penilaian
        if ($leaderboard->isEmpty()) {
            $leaderboard = Guru::with('jurusan')
                ->orderBy('nama', 'asc')
                ->get();
        }

        return view('guru.leaderboard', compact(
            'periodeAktif',
            'leaderboard'
        ));
    }

    public function detailGuru(Guru $guru)
    {
        $periodeAktif = Periode::where('status', 'aktif')->first();
        $periodeId = $periodeAktif?->id;

        $allPenilaian = Penilaian::where('guru_id', $guru->id)
            ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
            ->get();

        $stats = [
            'total_penilaian' => $allPenilaian->count(),
            'rata_kedisiplinan' => $allPenilaian->avg('kedisiplinan') ?? 0,
            'rata_cara_mengajar' => $allPenilaian->avg('cara_mengajar') ?? 0,
            'rata_komunikasi' => $allPenilaian->avg('komunikasi') ?? 0,
            'rata_tanggung_jawab' => $allPenilaian->avg('tanggung_jawab') ?? 0,
            'rata_kreativitas' => $allPenilaian->avg('kreativitas') ?? 0,
            'rata_keramahan' => $allPenilaian->avg('keramahan') ?? 0,
        ];

        $semuaFeedback = Penilaian::with('siswa')
            ->where('guru_id', $guru->id)
            ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
            ->latest()
            ->get();

        $arsipPeriode = Periode::where('id', '!=', $periodeId)
            ->whereHas('penilaian', function($q) use ($guru) {
                $q->where('guru_id', $guru->id);
            })
            ->with(['penilaian' => function($q) use ($guru) {
                $q->where('guru_id', $guru->id)->latest();
            }])
            ->latest('tanggal_mulai')
            ->get();

        return view('guru.show', compact('guru', 'periodeAktif', 'stats', 'semuaFeedback', 'arsipPeriode'));
    }
}