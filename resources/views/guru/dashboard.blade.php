@extends('layouts.guru')
@section('title', 'Dashboard Guru')

@section('content')
<style>
    /* Styling Terpadu Tema GuruKuu (Biru Tua #003366 & Orange/Amber #f59e0b) */
    .gk-dash-btn-primary {
        background: #003366;
        color: #ffffff !important;
        border: 1px solid #003366;
        padding: 0.45rem 1.1rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.82rem;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        text-decoration: none;
        box-shadow: 0 2px 5px rgba(0, 51, 102, 0.15);
        transition: all 0.2s ease-in-out;
    }
    .gk-dash-btn-primary:hover {
        background: #002244;
        border-color: #002244;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 51, 102, 0.25);
    }

    .gk-dash-btn-outline {
        background: #ffffff;
        color: #003366 !important;
        border: 1px solid rgba(0, 51, 102, 0.2);
        padding: 0.45rem 1.1rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.82rem;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        text-decoration: none;
        transition: all 0.2s ease-in-out;
    }
    .gk-dash-btn-outline:hover {
        background: rgba(0, 51, 102, 0.05);
        border-color: #003366;
        color: #003366 !important;
        transform: translateY(-1px);
    }

    .gk-metric-card {
        background: #ffffff;
        border: 1px solid rgba(0, 51, 102, 0.1);
        border-radius: 14px;
        padding: 1.25rem 1.35rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .gk-metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 51, 102, 0.06);
    }

    /* Notifikasi / Badge Ulasan Masuk */
    .gk-notif-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: #f0f7ff;
        border: 1px solid rgba(0, 51, 102, 0.15);
        padding: 0.25rem 0.65rem;
        border-radius: 50rem;
        color: #003366;
        font-weight: 600;
        font-size: 0.75rem;
    }
    .gk-notif-counter {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #003366;
        color: #ffffff;
        font-size: 0.7rem;
        font-weight: 700;
        min-width: 18px;
        height: 18px;
        border-radius: 50%;
        padding: 0 4px;
    }

    /* Kotak Kritik & Saran Sesuai Standar Tema (Kuning & Biru) */
    .gk-box-kritik {
        background: #fffbeb;
        border-left: 3.5px solid #f59e0b;
        border-radius: 8px;
        padding: 0.65rem 0.85rem;
        line-height: 1.5;
    }
    .gk-box-saran {
        background: #f0f9ff;
        border-left: 3.5px solid #0284c7;
        border-radius: 8px;
        padding: 0.65rem 0.85rem;
        line-height: 1.5;
    }

    /* Scrollbar Halus untuk Feed Ulasan */
    .gk-feed-scroll::-webkit-scrollbar {
        width: 5px;
    }
    .gk-feed-scroll::-webkit-scrollbar-track {
        background: #f8fafc;
        border-radius: 10px;
    }
    .gk-feed-scroll::-webkit-scrollbar-thumb {
        background: rgba(0, 51, 102, 0.15);
        border-radius: 10px;
    }
    .gk-feed-scroll::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 51, 102, 0.3);
    }

    /* Paginasi Minimalis Tema GuruKuu */
    .gk-dash-pagination nav {
        display: flex;
        justify-content: center;
        width: 100%;
    }
    .gk-dash-pagination .pagination {
        margin-bottom: 0;
        gap: 3px;
    }
    .gk-dash-pagination .page-link {
        padding: 0.2rem 0.55rem;
        font-size: 0.72rem;
        border-radius: 6px !important;
        color: #003366;
        border: 1px solid rgba(0, 51, 102, 0.15);
        background: #ffffff;
    }
    .gk-dash-pagination .page-item.active .page-link {
        background: #003366;
        border-color: #003366;
        color: #ffffff !important;
        font-weight: 700;
    }
    .gk-dash-pagination .page-item.disabled .page-link {
        opacity: 0.5;
        background: #f8fafc;
    }
</style>

{{-- PAGE HEADER DENGAN SELAMAT DATANG, NAMA GURU & EMOJI TANGAN 👋 --}}
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <div class="page-label text-uppercase" style="color: #003366; font-weight: 700; letter-spacing: 1.2px; font-size: 0.75rem;">PORTAL GURU</div>
        <h1 class="page-title mb-1" style="color: #003366; font-weight: 800; font-size: 1.75rem;">Selamat Datang, {{ $guru->nama }}! 👋</h1>
        <p class="text-muted mb-0 small">Berikut ringkasan evaluasi kinerja dan aspirasi siswa pada periode aktif: <strong class="text-dark">{{ $periodeAktif->nama_periode ?? 'Aktif' }}</strong>.</p>
    </div>
    <div>
        <a href="{{ route('guru.leaderboard') }}" class="gk-dash-btn-outline">
            Lihat Leaderboard
        </a>
    </div>
</div>

{{-- LAYOUT 2 KOLOM PROPORSIONAL, SEIMBANG TINGGINYA & RAPI --}}
<div class="row g-4 align-items-start">
    {{-- KOLOM KIRI (7 Kolom): PERIODE AKTIF & STATISTIK EVALUASI --}}
    <div class="col-lg-7">
        <div class="d-flex flex-column gap-3">
            {{-- STATUS PERIODE PEMBELAJARAN (POSISI DI ATAS STATISTIK EVALUASI) --}}
            <div class="gk-metric-card" style="padding: 1rem 1.25rem;">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(0, 51, 102, 0.08); color: #003366; font-size: 1rem; flex-shrink: 0;">
                            <i class="bi bi-calendar2-check"></i>
                        </div>
                        <div>
                            <div class="text-muted text-uppercase fw-semibold" style="font-size: 0.68rem; letter-spacing: 0.8px;">STATUS PERIODE PEMBELAJARAN</div>
                            <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $periodeAktif->nama_periode ?? 'Periode Aktif' }}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-pill px-3 py-1.5" style="background: rgba(0, 51, 102, 0.08); color: #003366; font-size: 0.75rem; font-weight: 700; border: 1px solid rgba(0, 51, 102, 0.12);">
                            {{ strtoupper($guru->jurusan?->nama_jurusan ?? 'UMUM') }}
                        </span>
                        <span class="badge rounded-pill px-3 py-1.5" style="background: rgba(245, 158, 11, 0.12); color: #b45309; font-size: 0.75rem; font-weight: 700; border: 1px solid rgba(245, 158, 11, 0.2);">
                            {{ $guru->kategori_label }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- GRID KARTU METRIK: RATA-RATA & TOTAL EVALUASI --}}
            <div class="row g-3">
                {{-- Kartu 1: Rata-Rata Nilai --}}
                <div class="col-12 col-sm-6">
                    <div class="gk-metric-card h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.8px; color: #b45309;">RATA-RATA EVALUASI</span>
                                @php $pctKepuasan = round((($guru->rata_rata_nilai ?? 0) / 5) * 100); @endphp
                                <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(245, 158, 11, 0.12); color: #b45309; font-weight: 700; font-size: 0.75rem; border: 1px solid rgba(245, 158, 11, 0.2);">
                                    {{ $pctKepuasan }}% Kepuasan
                                </span>
                            </div>
                            <div class="d-flex align-items-baseline gap-1 mt-2">
                                <h2 class="fw-bold mb-0 text-dark" style="font-feature-settings: 'tnum'; font-size: 2.1rem; line-height: 1;">{{ number_format($guru->rata_rata_nilai ?? 0, 2) }}</h2>
                                <span class="text-muted fs-6">/ 5.0</span>
                            </div>
                        </div>
                        <div class="mt-3 pt-1">
                            <div class="progress" style="height: 6px; border-radius: 10px; background: #e2e8f0;">
                                <div class="progress-bar rounded-pill" style="width: {{ $pctKepuasan }}%; background: linear-gradient(90deg, #f59e0b, #d97706);"></div>
                            </div>
                            <div class="text-muted small mt-1.5" style="font-size: 0.75rem;">Skor kumulatif penilaian siswa</div>
                        </div>
                    </div>
                </div>

                {{-- Kartu 2: Total Penilaian Siswa --}}
                <div class="col-12 col-sm-6">
                    <div class="gk-metric-card h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.8px; color: #003366;">TOTAL EVALUASI</span>
                                <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(0, 51, 102, 0.08); color: #003366; font-weight: 700; font-size: 0.75rem; border: 1px solid rgba(0, 51, 102, 0.12);">
                                    Responden
                                </span>
                            </div>
                            <div class="d-flex align-items-baseline gap-1 mt-2">
                                <h2 class="fw-bold mb-0 text-dark" style="font-feature-settings: 'tnum'; font-size: 2.1rem; line-height: 1;">{{ $guru->total_penilaian ?? 0 }}</h2>
                                <span class="text-muted fs-6">Siswa</span>
                            </div>
                        </div>
                        <div class="mt-3 pt-1">
                            <div class="text-muted small" style="font-size: 0.78rem; line-height: 1.4;">
                                Siswa yang telah selesai mengisi evaluasi pengajaran pada periode ini.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kartu 3: Total Aspirasi & Masukan Tertulis --}}
            <div class="gk-metric-card">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                    <div>
                        <span class="fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.8px; color: #0284c7;">ASPIRASI & MASUKAN</span>
                        <div class="d-flex align-items-baseline gap-2 mt-1">
                            <h3 class="fw-bold mb-0 text-dark" style="font-size: 1.85rem; line-height: 1;">{{ $totalUlasanCount ?? ($ulasanTerbaru->total() ?? 0) }}</h3>
                            <span class="text-muted small">Ulasan Masuk</span>
                        </div>
                    </div>
                    <span class="badge rounded-pill px-3 py-1.5" style="background: rgba(2, 132, 199, 0.1); color: #0284c7; font-weight: 700; font-size: 0.75rem; border: 1px solid rgba(2, 132, 199, 0.2);">
                        Kritik & Saran Tertulis
                    </span>
                </div>
                <p class="text-muted small mb-0 mt-2" style="font-size: 0.8rem; line-height: 1.5;">
                    Ulasan siswa disampaikan secara 100% anonim dan objektif demi menjaga kenyamanan proses refleksi serta peningkatan kualitas pengajaran Anda.
                </p>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN (5 Kolom): FEED ULASAN SISWA (RINGKAS, TINGGI SEIMBANG DENGAN KIRI, DAPAT DI-SCROLL & DIPAGINASI) --}}
    <div class="col-lg-5">
        <div class="gk-metric-card d-flex flex-column" style="padding: 1.25rem;">
            {{-- Header Feed Ulasan --}}
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2.5 border-bottom flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold mb-0 text-dark" style="color: #003366 !important; font-size: 1rem;">
                        Ulasan Siswa
                    </h6>
                    <small class="text-muted" style="font-size: 0.72rem;">Feed aspirasi tegak & anonim</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="gk-notif-pill">
                        <span class="gk-notif-counter">{{ $totalUlasanCount ?? ($ulasanTerbaru->total() ?? 0) }}</span>
                        <span>Masuk</span>
                    </div>
                    <a href="{{ route('guru.ulasan') }}" class="gk-dash-btn-primary">
                        Kelola
                    </a>
                </div>
            </div>

            {{-- Feed List Scrollable Berdiri: Tinggi disesuaikan (380px) agar seimbang dengan kolom kiri --}}
            <div class="gk-feed-scroll flex-grow-1 overflow-y-auto pe-1" style="max-height: 380px;">
                @forelse($ulasanTerbaru as $review)
                <div class="p-3 mb-3 rounded-3 border bg-white" style="border-color: rgba(0, 51, 102, 0.1) !important; box-shadow: 0 1px 4px rgba(0, 51, 102, 0.04);">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(0, 51, 102, 0.06); color: #003366; font-size: 0.72rem; border: 1px solid rgba(0, 51, 102, 0.12); font-weight: 600;">
                            Siswa (Anonim)
                        </span>
                        <small class="text-muted font-mono" style="font-size: 0.7rem;">
                            {{ $review->created_at->format('d M Y, H:i') }}
                        </small>
                    </div>

                    {{-- Rating Persen & Bar dengan Gradient Sesuai Standar Leaderboard --}}
                    @php $revPct = round(($review->rata_rata_evaluasi / 5) * 100); @endphp
                    <div class="mb-2.5">
                        <div class="d-flex justify-content-between align-items-center mb-1 font-mono" style="font-size: 0.74rem;">
                            <span class="fw-bold" style="color: #003366;">{{ $revPct }}%</span>
                            <span class="text-muted">({{ number_format($review->total_nilai, 2) }}/25)</span>
                        </div>
                        <div class="progress" style="height: 6px; border-radius: 10px; background: #e2e8f0;">
                            <div class="progress-bar rounded-pill {{ $revPct >= 75 ? 'gk-bar-blue-high' : ($revPct >= 50 ? 'gk-bar-blue-mid' : 'gk-bar-blue-low') }}" style="width: {{ $revPct }}%;"></div>
                        </div>
                    </div>

                    @if($review->is_censored)
                        <div class="mb-2 p-2.5 rounded-3 border text-muted fst-italic small" style="background: #f8fafc; font-size: 0.78rem;">
                            Ulasan ini disembunyikan oleh Administrator sekolah.
                        </div>
                    @else
                        {{-- KRITIK: Latar Kuning Lembut, Border Kiri Kuning/Amber --}}
                        @if($review->kritik)
                            <div class="gk-box-kritik mb-2" style="font-size: 0.8rem;">
                                <div style="color: #b45309; font-weight: 700; font-size: 0.72rem; letter-spacing: 0.5px; margin-bottom: 2px;">Kritik Membangun:</div>
                                <span class="text-dark">{{ $review->kritik }}</span>
                            </div>
                        @endif

                        {{-- SARAN: Latar Biru Lembut, Border Kiri Biru --}}
                        @if($review->saran)
                            <div class="gk-box-saran mb-2" style="font-size: 0.8rem;">
                                <div style="color: #0284c7; font-weight: 700; font-size: 0.72rem; letter-spacing: 0.5px; margin-bottom: 2px;">Saran Perbaikan:</div>
                                <span class="text-dark">{{ $review->saran }}</span>
                            </div>
                        @endif
                    @endif

                    @if($review->balasans && $review->balasans->count() > 0)
                        <div class="mt-2.5 p-2 rounded-2 border-start border-3" style="background: #f8fafc; border-color: #003366 !important; font-size: 0.76rem;">
                            <div class="fw-bold mb-0.5" style="color: #003366 !important; font-size: 0.72rem;">Diskusi Terkini ({{ $review->balasans->count() }} balasan):</div>
                            <p class="mb-0 text-muted fst-italic">{{ Str::limit($review->balasans->last()->pesan, 75) }}</p>
                        </div>
                    @elseif($review->balasan_guru)
                        <div class="mt-2.5 p-2 rounded-2 border-start border-3" style="background: #f8fafc; border-color: #f59e0b !important; font-size: 0.76rem;">
                            <div class="fw-bold mb-0.5" style="color: #b45309 !important; font-size: 0.72rem;">Telah Anda Balas:</div>
                            <p class="mb-0 text-muted fst-italic">{{ Str::limit($review->balasan_guru, 75) }}</p>
                        </div>
                    @endif

                    <div class="mt-3 pt-2 border-top d-flex justify-content-between align-items-center">
                        @if(($review->balasans && $review->balasans->count() > 0) || $review->balasan_guru)
                            <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(0, 51, 102, 0.08); color: #003366; font-size: 0.72rem; font-weight: 600;">Ada Diskusi</span>
                        @else
                            <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(245, 158, 11, 0.12); color: #b45309; font-size: 0.72rem; font-weight: 600;">Belum Dibalas</span>
                        @endif
                        <a href="{{ route('guru.ulasan') }}" class="btn btn-sm btn-outline-secondary rounded-pill py-0.5 px-3" style="font-size: 0.75rem; border-color: rgba(0, 51, 102, 0.25); color: #003366; font-weight: 600;">
                            Buka Diskusi
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <p class="small mb-0">Belum ada ulasan kritik dan saran masuk untuk periode ini.</p>
                </div>
                @endforelse
            </div>

            {{-- PAGINASI FEED ULASAN (MENCEGAH LOAD SEMUA DATA & MENJAGA PERFORMA SERVER) --}}
            @if(method_exists($ulasanTerbaru, 'hasPages') && $ulasanTerbaru->hasPages())
                <div class="pt-2 mt-2 border-top gk-dash-pagination">
                    {{ $ulasanTerbaru->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection