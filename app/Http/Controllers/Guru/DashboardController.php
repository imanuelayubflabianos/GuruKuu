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

        // Ambil ulasan & kritik saran dari siswa untuk guru ini (100% Anonim, tidak disensor) dengan paginasi efisien
        $ulasanQuery = Penilaian::where('guru_id', $guru->id)
            ->when($periodeAktif, fn($q) => $q->where('periode_id', $periodeAktif->id))
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereNotNull('kritik')->whereRaw("TRIM(kritik) != ''");
                })->orWhere(function ($q) {
                    $q->whereNotNull('saran')->whereRaw("TRIM(saran) != ''");
                });
            })
            ->where(function ($query) {
                $query->where('is_censored', false)->orWhereNull('is_censored');
            })
            ->with(['periode', 'balasans', 'kelas.jurusan', 'siswa.jurusan'])
            ->latest();

        $totalUlasanCount = (clone $ulasanQuery)->count();
        $ulasanTerbaru = $ulasanQuery->paginate(3)->withQueryString();

        // Hitung peringkat pribadi guru di sekolah (mode Semua Guru / rating) secara privat
        $semuaGuruRanked = Guru::leaderboardFor('rating', null, $periodeAktif?->id);
        $myRankEntry = $semuaGuruRanked->first(fn($g) => $g->id === $guru->id);
        $mySchoolRank = $myRankEntry?->leaderboard_rank;
        $totalEvaluatedGurus = $semuaGuruRanked->filter(fn($g) => $g->total_penilaian > 0)->count();

        // Statistik Detail 5 Kriteria Penilaian Siswa untuk Dashboard Guru
        $activePenilaians = Penilaian::where('guru_id', $guru->id)
            ->when($periodeAktif, fn($q) => $q->where('periode_id', $periodeAktif->id))
            ->get();

        $totalResponden = $activePenilaians->count();
        $avgWaktu = $totalResponden > 0 ? ($activePenilaians->avg('kedisiplinan') ?? 0) : 0;
        $avgKehadiran = $totalResponden > 0 ? ($activePenilaians->avg('tanggung_jawab') ?? 0) : 0;
        $avgMateri = $totalResponden > 0 ? ($activePenilaians->avg('komunikasi') ?? 0) : 0;
        $avgInteraksi = $totalResponden > 0 ? ($activePenilaians->avg('keramahan') ?? 0) : 0;
        $avgSuasana = $totalResponden > 0 ? ($activePenilaians->avg('kreativitas') ?? 0) : 0;

        $overallTotalAvg = $totalResponden > 0 ? (($avgWaktu + $avgKehadiran + $avgMateri + $avgInteraksi + $avgSuasana) / 5) : 0;
        $overallPct = round(($overallTotalAvg / 5) * 100);

        $kriteriaDashboard = [
            'total_responden' => $totalResponden,
            'overall_avg' => round($overallTotalAvg, 2),
            'overall_pct' => $overallPct,
            'aspects' => [
                'Ketepatan Waktu' => [
                    'avg' => round($avgWaktu, 1),
                    'pct' => round(($avgWaktu / 5) * 100),
                    'icon' => 'bi-clock-history',
                ],
                'Kehadiran di Kelas' => [
                    'avg' => round($avgKehadiran, 1),
                    'pct' => round(($avgKehadiran / 5) * 100),
                    'icon' => 'bi-calendar-check',
                ],
                'Penyampaian Materi' => [
                    'avg' => round($avgMateri, 1),
                    'pct' => round(($avgMateri / 5) * 100),
                    'icon' => 'bi-journal-bookmark',
                ],
                'Interaksi dengan Siswa' => [
                    'avg' => round($avgInteraksi, 1),
                    'pct' => round(($avgInteraksi / 5) * 100),
                    'icon' => 'bi-chat-dots',
                ],
                'Keterlibatan & Suasana' => [
                    'avg' => round($avgSuasana, 1),
                    'pct' => round(($avgSuasana / 5) * 100),
                    'icon' => 'bi-emoji-smile',
                ],
            ]
        ];

        return view('guru.dashboard', compact(
            'guru',
            'ulasanTerbaru',
            'totalUlasanCount',
            'periodeAktif',
            'mySchoolRank',
            'totalEvaluatedGurus',
            'kriteriaDashboard'
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

        $query = Penilaian::where('guru_id', $guru->id)->with(['periode', 'kelas.jurusan', 'siswa.jurusan']);

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

        $statsBase = Penilaian::where('guru_id', $guru->id);
        if ($request->filled('periode_id')) {
            $statsBase->where('periode_id', $request->periode_id);
        }
        if ($request->filled('tipe')) {
            if ($request->tipe === 'ada_teks') {
                $statsBase->where(function ($q) {
                    $q->where(function ($sub) {
                        $sub->whereNotNull('kritik')->whereRaw("TRIM(kritik) != ''");
                    })->orWhere(function ($sub) {
                        $sub->whereNotNull('saran')->whereRaw("TRIM(saran) != ''");
                    });
                });
            }
        }

        $totalUlasan = (clone $statsBase)->count();
        $totalDibalas = (clone $statsBase)->whereNotNull('balasan_guru')->whereRaw("TRIM(balasan_guru) != ''")->count();
        $totalBelumDibalas = $totalUlasan - $totalDibalas;

        // Rata-rata 5 Kriteria Penilaian Siswa (sinkron dinamis dengan filter periode semester & teks)
        $avgWaktu = (clone $statsBase)->avg('kedisiplinan') ?? 0;
        $avgKehadiran = (clone $statsBase)->avg('tanggung_jawab') ?? 0;
        $avgMateri = (clone $statsBase)->avg('komunikasi') ?? 0;
        $avgInteraksi = (clone $statsBase)->avg('keramahan') ?? 0;
        $avgSuasana = (clone $statsBase)->avg('kreativitas') ?? 0;

        $overallTotalAvg = $totalUlasan > 0 ? (($avgWaktu + $avgKehadiran + $avgMateri + $avgInteraksi + $avgSuasana) / 5) : 0;
        $overallPct = round(($overallTotalAvg / 5) * 100);

        $kriteriaStats = [
            'total_evaluasi' => $totalUlasan,
            'overall_avg' => round($overallTotalAvg, 2),
            'overall_pct' => $overallPct,
            'aspects' => [
                'Ketepatan Waktu' => [
                    'avg' => round($avgWaktu, 1),
                    'pct' => round(($avgWaktu / 5) * 100),
                    'icon' => 'bi-clock-history',
                ],
                'Kehadiran di Kelas' => [
                    'avg' => round($avgKehadiran, 1),
                    'pct' => round(($avgKehadiran / 5) * 100),
                    'icon' => 'bi-calendar-check',
                ],
                'Penyampaian Materi' => [
                    'avg' => round($avgMateri, 1),
                    'pct' => round(($avgMateri / 5) * 100),
                    'icon' => 'bi-journal-bookmark',
                ],
                'Interaksi dengan Siswa' => [
                    'avg' => round($avgInteraksi, 1),
                    'pct' => round(($avgInteraksi / 5) * 100),
                    'icon' => 'bi-chat-dots',
                ],
                'Keterlibatan & Suasana' => [
                    'avg' => round($avgSuasana, 1),
                    'pct' => round(($avgSuasana / 5) * 100),
                    'icon' => 'bi-emoji-smile',
                ],
            ]
        ];

        return view('guru.ulasan.index', compact(
            'guru',
            'semuaUlasan',
            'totalUlasan',
            'totalDibalas',
            'totalBelumDibalas',
            'periodeAktif',
            'semuaPeriode',
            'kriteriaStats'
        ));
    }

    public function replyPenilaian(Request $request, Penilaian $penilaian)
    {
        $request->validate([
            'balasan_guru' => 'required|string|max:255',
        ], [
            'balasan_guru.max' => 'Balasan Anda melebihi batas maksimal 255 karakter.',
        ]);

        $user = Auth::user();
        $guru = Guru::where('nip', $user->nis)
            ->orWhere('email', $user->email)
            ->first();

        if (!$guru || $penilaian->guru_id !== $guru->id) {
            return back()->with('error', 'Anda hanya dapat membalas ulasan yang ditujukan untuk profil Anda.');
        }

        // 🛡️ KEAMANAN: Cegah link/URL sembarangan
        if (\App\Services\ProfanityFilterService::containsLink($request->balasan_guru)) {
            return back()->withInput()->with('error', 'Balasan ulasan tidak boleh mengandung tautan / link URL luar demi keamanan sistem.');
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

    public function leaderboard(Request $request)
    {
        $user = Auth::user();
        $myGuru = Guru::where('nip', $user->nis)
            ->orWhere('email', $user->email)
            ->first();

        $periodeAktif = Periode::where('status', 'aktif')->first();
        $kelasList = \App\Models\Kelas::with('jurusan')->orderBy('tingkat')->orderBy('nama_kelas')->get();
        $mode = $request->input('mode', 'rating');
        $kelasId = $request->integer('kelas_id') ?: null;
        if ($mode === 'partisipasi' && !$kelasId && $kelasList->isNotEmpty()) {
            $kelasId = $kelasList->first()->id;
        }
        if ($mode === 'rating') {
            $kelasId = null;
        }

        // Hitung leaderboard lengkap seluruh guru
        $fullLeaderboard = Guru::leaderboardFor($mode, $kelasId, $periodeAktif?->id);

        // Ambil info peringkat pribadi guru yang sedang login (bersifat privat)
        $myRankInfo = null;
        if ($myGuru) {
            $myEntry = $fullLeaderboard->first(fn($g) => $g->id === $myGuru->id);
            $limit = ($mode === 'partisipasi') ? 5 : 10;
            $totalEvaluated = $fullLeaderboard->filter(fn($g) => $g->total_penilaian > 0)->count();

            $myRankInfo = [
                'guru' => $myGuru,
                'rank' => $myEntry?->leaderboard_rank,
                'total_penilaian' => $myEntry?->total_penilaian ?? 0,
                'rata_rata' => $myEntry?->rata_rata_nilai ?? 0.0,
                'total_gurus' => $totalEvaluated,
                'limit' => $limit,
                'is_top_limit' => $myEntry && !empty($myEntry->leaderboard_rank) && $myEntry->leaderboard_rank <= $limit,
                'mode' => $mode,
            ];
        }

        // Tampilan umum user dibatasi: Top 10 (Semua Guru) & Top 5 (Berdasarkan Kelas)
        $limit = ($mode === 'partisipasi') ? 5 : 10;
        $leaderboard = $fullLeaderboard
            ->filter(fn($g) => !empty($g->leaderboard_rank))
            ->take($limit);

        return view('guru.leaderboard', compact(
            'periodeAktif', 'leaderboard', 'kelasList', 'mode', 'kelasId', 'myRankInfo'
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
            'rata_komunikasi' => $allPenilaian->avg('komunikasi') ?? 0,
            'rata_tanggung_jawab' => $allPenilaian->avg('tanggung_jawab') ?? 0,
            'rata_kreativitas' => $allPenilaian->avg('kreativitas') ?? 0,
            'rata_keramahan' => $allPenilaian->avg('keramahan') ?? 0,
        ];

        $semuaFeedback = Penilaian::with(['siswa.jurusan', 'kelas.jurusan'])
            ->where('guru_id', $guru->id)
            ->where(function($q) {
                $q->where('is_censored', false)->orWhereNull('is_censored');
            })
            ->when($periodeId, fn($q) => $q->where('periode_id', $periodeId))
            ->latest()
            ->get();

        $arsipPeriode = Periode::where('id', '!=', $periodeId)
            ->whereHas('penilaian', function($q) use ($guru) {
                $q->where('guru_id', $guru->id);
            })
            ->with(['penilaian' => function($q) use ($guru) {
                $q->where('guru_id', $guru->id)->with(['siswa.jurusan', 'kelas.jurusan'])->latest();
            }])
            ->latest('tanggal_mulai')
            ->get();

        return view('guru.show', compact('guru', 'periodeAktif', 'stats', 'semuaFeedback', 'arsipPeriode'));
    }
}