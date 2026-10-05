@extends('layouts.siswa')
@section('title', 'Dashboard')

@section('content')
<style>
    /* ==============================================================
       TEMA GURUKUU: CLEAN PUBLIC DASHBOARD STYLE (MINIMAL & MODERN)
       ============================================================== */
    .gk-clean-card {
        background: var(--bg-card, #ffffff);
        border-radius: 20px;
        border: 1px solid rgba(15, 23, 42, 0.08) !important;
        box-shadow: 0 10px 25px -4px rgba(15, 23, 42, 0.06), 0 4px 10px -2px rgba(15, 23, 42, 0.03) !important;
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.25s ease;
    }
    .gk-clean-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 32px -6px rgba(15, 23, 42, 0.1), 0 6px 14px -3px rgba(15, 23, 42, 0.04) !important;
        border-color: rgba(15, 23, 42, 0.14) !important;
    }

    /* PANDUAN SLIDER / CAROUSEL MIRIP DASHBOARD PUBLIK */
    .gk-panduan-slider-wrapper {
        position: relative;
        max-width: 580px;
        width: 100%;
        margin: 0 auto;
    }
    .gk-panduan-viewport {
        overflow: hidden;
        position: relative;
        padding: 10px 4px;
        cursor: grab;
        touch-action: pan-y;
        user-select: none;
        -webkit-user-select: none;
    }
    .gk-panduan-viewport:active {
        cursor: grabbing;
    }
    .gk-panduan-track {
        display: flex;
        transition: transform 0.65s cubic-bezier(0.22, 1, 0.36, 1);
        will-change: transform;
    }
    .gk-panduan-slide {
        min-width: 100%;
        width: 100%;
        flex-shrink: 0;
        padding: 0 6px;
        box-sizing: border-box;
    }
    .gk-step-roman {
        width: 54px;
        height: 54px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #1e293b;
        font-weight: 800;
        font-size: 1.3rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
    }
    .gk-panduan-nav-btn {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #ffffff;
        border: 1.5px solid rgba(15, 23, 42, 0.1);
        color: #1e293b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        box-shadow: 0 4px 10px rgba(15, 23, 42, 0.06);
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        outline: none;
    }
    .gk-panduan-nav-btn:hover {
        background: #003366;
        border-color: #003366;
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 51, 102, 0.2);
    }
    .gk-panduan-dot {
        width: 8px;
        height: 8px;
        border-radius: 20px;
        background: #cbd5e1;
        border: none;
        padding: 0;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);
        outline: none;
    }
    .gk-panduan-dot:hover {
        background: #94a3b8;
    }
    .gk-panduan-dot.active {
        width: 26px;
        background: #003366;
    }

    /* AVATAR BERSIH MIRIP PUBLIK */
    .gk-avatar-clean-wrap {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 50% !important;
        background: transparent !important;
        padding: 0 !important;
        box-shadow: 0 8px 18px -3px rgba(15, 23, 42, 0.12) !important;
        border: none !important;
        margin: 0 auto;
    }
    .gk-avatar-clean-wrap img {
        border-radius: 50% !important;
        object-fit: cover !important;
        display: block !important;
        border: 3px solid #ffffff !important;
        box-shadow: 0 0 0 1px rgba(15, 23, 42, 0.08) !important;
    }

    /* 🏆 KARTU PODIUM DASHBOARD RESPONSIVE & ELEGAN */
    .gk-dash-podium-card {
        padding: 0.75rem 0.45rem !important;
        position: relative;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    @media (min-width: 576px) {
        .gk-dash-podium-card {
            padding: 1rem 0.75rem !important;
        }
    }
    @media (min-width: 992px) {
        .gk-dash-podium-card {
            padding: 1.35rem 0.95rem !important;
        }
    }
    .gk-dash-podium-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px -4px rgba(15, 23, 42, 0.08);
    }
    .gk-dash-podium-card.is-podium-1 {
        border: 1.5px solid rgba(234, 179, 8, 0.45) !important;
        box-shadow: 0 4px 18px -2px rgba(234, 179, 8, 0.15) !important;
    }
    .gk-dash-podium-card.is-podium-2 {
        border: 1.5px solid rgba(148, 163, 184, 0.4) !important;
    }
    .gk-dash-podium-card.is-podium-3 {
        border: 1.5px solid rgba(217, 119, 6, 0.35) !important;
    }

    .gk-dash-podium-avatar {
        width: 52px !important;
        height: 52px !important;
        margin: 0 auto 0.45rem !important;
    }
    .is-podium-1 .gk-dash-podium-avatar {
        width: 58px !important;
        height: 58px !important;
    }
    @media (min-width: 576px) {
        .gk-dash-podium-avatar {
            width: 62px !important;
            height: 62px !important;
            margin-bottom: 0.65rem !important;
        }
        .is-podium-1 .gk-dash-podium-avatar {
            width: 70px !important;
            height: 70px !important;
        }
    }
    @media (min-width: 992px) {
        .gk-dash-podium-avatar {
            width: 72px !important;
            height: 72px !important;
            margin-bottom: 0.75rem !important;
        }
        .is-podium-1 .gk-dash-podium-avatar {
            width: 80px !important;
            height: 80px !important;
        }
    }

    .gk-dash-podium-name {
        font-size: 0.78rem;
    }
    .gk-dash-podium-sub {
        font-size: 0.62rem;
    }
    .gk-dash-podium-stars {
        font-size: 0.70rem;
        letter-spacing: 0.5px;
    }
    .gk-dash-podium-ulasan {
        font-size: 0.66rem;
    }
    .gk-dash-podium-btn {
        font-size: 0.70rem !important;
    }
    @media (min-width: 576px) {
        .gk-dash-podium-name { font-size: 0.84rem; }
        .gk-dash-podium-sub { font-size: 0.66rem; }
        .gk-dash-podium-stars { font-size: 0.78rem; letter-spacing: 1px; }
        .gk-dash-podium-ulasan { font-size: 0.70rem; }
        .gk-dash-podium-btn { font-size: 0.74rem !important; }
    }
    @media (min-width: 992px) {
        .gk-dash-podium-name { font-size: 0.88rem; }
        .gk-dash-podium-sub { font-size: 0.68rem; }
        .gk-dash-podium-stars { font-size: 0.84rem; letter-spacing: 1.5px; }
        .gk-dash-podium-ulasan { font-size: 0.72rem; }
        .gk-dash-podium-btn { font-size: 0.76rem !important; }
    }

    /* PROGRESS PILL HITAM/NAVY TERUKUR & DIJAMIN TIDAK MELUBER */
    .gk-progress-pill {
        background: #0f172a !important;
        border-radius: 50px !important;
        color: #ffffff !important;
        font-weight: 800;
        font-size: 0.68rem;
        padding: 0 0.35rem;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: 100% !important;
        max-width: 76px !important;
        min-width: 0 !important;
        height: 21px !important;
        line-height: 21px !important;
        margin: 0 auto;
        text-align: center;
        position: relative;
        overflow: hidden !important;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.15);
        box-sizing: border-box !important;
    }
    .gk-dash-podium-card .gk-progress-pill {
        width: 100% !important;
        max-width: 74px !important;
        min-width: 0 !important;
        height: 20px !important;
        line-height: 20px !important;
        font-size: 0.66rem !important;
        padding: 0 0.2rem !important;
        margin: 0 auto !important;
    }
    @media (min-width: 576px) {
        .gk-dash-podium-card .gk-progress-pill {
            max-width: 90px !important;
            height: 22px !important;
            line-height: 22px !important;
            font-size: 0.72rem !important;
        }
    }
    @media (min-width: 992px) {
        .gk-progress-pill,
        .gk-dash-podium-card .gk-progress-pill {
            font-size: 0.80rem !important;
            padding: 0 0.6rem !important;
            max-width: 120px !important;
            height: 25px !important;
            line-height: 25px !important;
        }
    }
    .gk-progress-pill-fill {
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        height: 100%;
        border-radius: 50px;
        background: linear-gradient(90deg, #0284c7, #38bdf8) !important;
        transition: width 1s ease;
    }

    /* 📊 KARTU METRIK DASHBOARD LEGA & BERNAFAS (TIDAK KEDEMPETAN) */
    .gk-metric-clean-card {
        background: #ffffff;
        border-radius: 16px !important;
        border: 1px solid rgba(15, 23, 42, 0.08) !important;
        box-shadow: 0 4px 14px -3px rgba(15, 23, 42, 0.04) !important;
        padding: 0.85rem 0.65rem !important;
        min-height: 96px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .gk-metric-clean-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -4px rgba(15, 23, 42, 0.08) !important;
    }
    @media (min-width: 576px) {
        .gk-metric-clean-card {
            padding: 1rem 0.85rem !important;
            min-height: 104px;
        }
    }
    @media (min-width: 992px) {
        .gk-metric-clean-card {
            padding: 1.25rem 1.15rem !important;
            min-height: 115px;
            border-radius: 18px !important;
        }
    }
    .gk-metric-label {
        font-size: 0.58rem !important;
        letter-spacing: 0.3px;
        line-height: 1.2;
    }
    @media (min-width: 576px) {
        .gk-metric-label { font-size: 0.64rem !important; letter-spacing: 0.5px; }
    }
    @media (min-width: 992px) {
        .gk-metric-label { font-size: 0.72rem !important; letter-spacing: 0.6px; }
    }
    .gk-metric-icon {
        width: 24px !important;
        height: 24px !important;
        font-size: 0.76rem !important;
    }
    @media (min-width: 576px) {
        .gk-metric-icon { width: 28px !important; height: 28px !important; font-size: 0.85rem !important; }
    }
    @media (min-width: 992px) {
        .gk-metric-icon { width: 34px !important; height: 34px !important; font-size: 0.95rem !important; }
    }
    .gk-metric-value {
        font-size: 1.30rem !important;
        line-height: 1 !important;
    }
    @media (min-width: 576px) {
        .gk-metric-value { font-size: 1.45rem !important; }
    }
    @media (min-width: 992px) {
        .gk-metric-value { font-size: 1.65rem !important; }
    }
    .gk-metric-unit {
        font-size: 0.68rem !important;
    }
    @media (min-width: 992px) {
        .gk-metric-unit { font-size: 0.75rem !important; }
    }
    .gk-metric-pct {
        font-size: 0.62rem !important;
        padding: 0.15rem 0.4rem !important;
    }
    @media (min-width: 992px) {
        .gk-metric-pct { font-size: 0.72rem !important; padding: 0.2rem 0.55rem !important; }
    }

    /* TOMBOL PILL HITAM LIHAT SELENGKAPNYA */
    .gk-pill-btn-dark {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #000000;
        color: #ffffff !important;
        font-weight: 700;
        font-size: 0.82rem;
        padding: 0.5rem 1.8rem;
        border-radius: 50px;
        text-decoration: none;
        transition: all 0.25s ease;
        box-shadow: 0 4px 14px -3px rgba(0,0,0,0.3);
    }
    .gk-pill-btn-dark:hover {
        background: #1e293b;
        transform: translateY(-2px);
        box-shadow: 0 8px 18px -4px rgba(0,0,0,0.4);
    }

    /* SCROLL FEED RIWAYAT TERAKHIR */
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

    .gk-history-item {
        background: #f8fafc;
        border: 1px solid rgba(0, 51, 102, 0.08) !important;
        border-radius: 14px;
        padding: 0.85rem 1rem;
        margin-bottom: 0.75rem;
        transition: all 0.2s ease-in-out;
    }
    .gk-history-item:hover {
        background: #ffffff;
        transform: translateY(-1px);
        border-color: rgba(0, 51, 102, 0.2) !important;
        box-shadow: 0 4px 12px rgba(0, 51, 102, 0.05);
    }

    /* DARK MODE SUPPORT */
    [data-bs-theme="dark"] .gk-clean-card,
    [data-bs-theme="dark"] .gk-history-item {
        background: #1e293b !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    [data-bs-theme="dark"] .gk-step-roman {
        background: #334155;
        color: #f8fafc;
    }
    [data-bs-theme="dark"] .gk-panduan-nav-btn {
        background: #1e293b;
        color: #f8fafc;
        border-color: rgba(255, 255, 255, 0.12);
    }
    [data-bs-theme="dark"] .gk-avatar-clean-wrap img {
        border-color: #1e293b !important;
    }
    [data-bs-theme="dark"] .gk-feed-scroll::-webkit-scrollbar-track {
        background: #0f172a;
    }
    [data-bs-theme="dark"] .gk-feed-scroll::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.15);
    }
</style>

{{-- PAGE HEADER: PORTAL SISWA, GREETING & STATUS SEMESTER --}}
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <div class="page-label text-uppercase" style="color: #003366; font-weight: 700; letter-spacing: 1.2px; font-size: 0.75rem;">PORTAL SISWA</div>
        <h1 class="page-title mb-1" style="color: #003366; font-weight: 800; font-size: 1.75rem;">Halo, {{ auth()->user()->name }}! 👋</h1>
        @if(!empty($kelasAktif))
            <p class="text-muted mb-0 small">
                Kelas: <strong class="text-dark">{{ $kelasAktif->nama_kelas }} Kelas {{ $kelasAktif->tingkat }}</strong> ({{ $kelasAktif->jurusan->nama_jurusan }})
            </p>
        @else
            <p class="text-muted mb-0 small">Akses data evaluasi guru sekolah secara objektif dan transparan.</p>
        @endif
    </div>
    <div>
        <span class="badge rounded-pill px-3 py-2 shadow-sm" style="background: var(--bg-card); color: var(--text-dark); border: 1px solid var(--border); font-size: 0.85rem;">
            <i class="bi bi-calendar-check-fill text-primary me-1.5"></i> {{ $periodeAktif?->nama_periode ?? 'Semester Aktif' }}
        </span>
    </div>
</div>

@php
    $pctSelesai = $totalGuru > 0 ? round(($jumlahSudah / $totalGuru) * 100) : 0;
@endphp

{{-- ALERT NOTIFIKASI PELANGGARAN --}}
@if(($notifikasiPelanggaran ?? collect())->isNotEmpty())
    <div class="alert alert-warning border-warning shadow-sm mb-4">
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="bi bi-shield-exclamation fs-5"></i>
            <strong>Notifikasi Pelanggaran</strong>
        </div>
        @foreach($notifikasiPelanggaran as $notifikasi)
            <div class="d-flex align-items-start justify-content-between gap-3 border-top border-warning-subtle pt-2 mt-2">
                <span class="small">{{ $notifikasi->notifikasi_siswa ?: 'Anda menerima notifikasi pelanggaran dari sistem.' }}</span>
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    @if($notifikasi->guru_id)
                        <a href="{{ route('siswa.guru.show', $notifikasi->guru_id) }}" class="btn btn-sm btn-outline-primary">Lihat Guru</a>
                    @endif
                    <form action="{{ route('siswa.notifikasi.read', $notifikasi) }}" method="POST" class="m-0">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-outline-dark">Mengerti</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif

{{-- 1. PANDUAN PENGGUNAAN (CAROUSEL SLIDER MIRIP DASHBOARD PUBLIK) --}}
<div class="text-center mb-3">
    <h3 class="fw-bold mb-0 text-dark" style="font-size: clamp(1.3rem, 2.5vw, 1.75rem);">
        {{ \App\Models\Setting::get('panduan_title', 'Bagaimana Cara Memberi Penilaian?') }}
    </h3>
</div>

<div class="gk-panduan-slider-wrapper mb-4">
    <div class="gk-panduan-viewport" id="panduanViewport">
        <div class="gk-panduan-track" id="panduanTrack">
            {{-- LANGKAH 1 --}}
            <div class="gk-panduan-slide is-active" data-slide="0">
                <div class="gk-clean-card p-4 text-center h-100 d-flex flex-column justify-content-between" style="min-height: 220px;">
                    <div>
                        <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold text-uppercase" style="background: #f1f5f9; color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">
                                {{ \App\Models\Setting::get('panduan_step1_title', 'LOGIN NIS') }}
                            </span>
                            <span class="badge rounded-pill px-2.5 py-1 text-muted fw-semibold" style="background: #f8fafc; font-size: 0.7rem; font-family: monospace;">
                                01 / 03
                            </span>
                        </div>
                        <div class="gk-step-roman">I</div>
                        <p class="text-dark fw-semibold mb-0" style="font-size: 0.95rem; line-height: 1.5;">
                            {{ \App\Models\Setting::get('panduan_step1_desc', 'Masuk dengan akun NIS & tanggal lahir resmi terverifikasi.') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- LANGKAH 2 --}}
            <div class="gk-panduan-slide" data-slide="1">
                <div class="gk-clean-card p-4 text-center h-100 d-flex flex-column justify-content-between" style="min-height: 220px;">
                    <div>
                        <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold text-uppercase" style="background: #f1f5f9; color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">
                                {{ \App\Models\Setting::get('panduan_step2_title', 'BERI NILAI') }}
                            </span>
                            <span class="badge rounded-pill px-2.5 py-1 text-muted fw-semibold" style="background: #f8fafc; font-size: 0.7rem; font-family: monospace;">
                                02 / 03
                            </span>
                        </div>
                        <div class="gk-step-roman">II</div>
                        <p class="text-dark fw-semibold mb-0" style="font-size: 0.95rem; line-height: 1.5;">
                            {{ \App\Models\Setting::get('panduan_step2_desc', 'Pilih guru Normada/Produktif, beri nilai (1-5) pada 5 kriteria.') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- LANGKAH 3 --}}
            <div class="gk-panduan-slide" data-slide="2">
                <div class="gk-clean-card p-4 text-center h-100 d-flex flex-column justify-content-between" style="min-height: 220px;">
                    <div>
                        <div class="d-flex align-items-center justify-content-center gap-2 mb-3">
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold text-uppercase" style="background: #f1f5f9; color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">
                                {{ \App\Models\Setting::get('panduan_step3_title', 'KIRIM ANONIM') }}
                            </span>
                            <span class="badge rounded-pill px-2.5 py-1 text-muted fw-semibold" style="background: #f8fafc; font-size: 0.7rem; font-family: monospace;">
                                03 / 03
                            </span>
                        </div>
                        <div class="gk-step-roman">III</div>
                        <p class="text-dark fw-semibold mb-0" style="font-size: 0.95rem; line-height: 1.5;">
                            {{ \App\Models\Setting::get('panduan_step3_desc', 'Data tersimpan aman & anonim untuk perbaikan pengajaran.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- NAVIGASI SLIDER & INDIKATOR DOTS --}}
    <div class="d-flex align-items-center justify-content-center gap-3 mt-2.5">
        <button type="button" class="gk-panduan-nav-btn" id="panduanPrevBtn" aria-label="Langkah Sebelumnya" title="Sebelumnya">
            <i class="bi bi-chevron-left"></i>
        </button>

        <div class="d-flex align-items-center gap-1.5" id="panduanDots">
            <button type="button" class="gk-panduan-dot active" data-index="0" aria-label="Langkah 1"></button>
            <button type="button" class="gk-panduan-dot" data-index="1" aria-label="Langkah 2"></button>
            <button type="button" class="gk-panduan-dot" data-index="2" aria-label="Langkah 3"></button>
        </div>

        <button type="button" class="gk-panduan-nav-btn" id="panduanNextBtn" aria-label="Langkah Selanjutnya" title="Selanjutnya">
            <i class="bi bi-chevron-right"></i>
        </button>
    </div>
</div>

{{-- 2. GRID UTAMA 2 KOLOM: METRIK & TOP GURU (KIRI) vs RIWAYAT TERAKHIR TINGGI (KANAN) --}}
<div class="row g-4 align-items-stretch mb-5 pb-4">
    {{-- KOLOM KIRI (7 Kolom): METRIK KOMPAK + TOP GURU SEKOLAH SEJAJAR & RAPI --}}
    <div class="col-lg-7 d-flex flex-column justify-content-between">
        <div>
            {{-- KARTU METRIK KOMPAK (LEGA, BERNAFAS, TIDAK KEDEMPETAN) --}}
            <div class="row g-2 g-sm-3 mb-4">
                {{-- 1. Total Guru --}}
                <div class="col-4">
                    <div class="gk-metric-clean-card h-100">
                        <div class="d-flex align-items-center justify-content-between mb-1.5 gap-1">
                            <span class="text-muted fw-bold text-uppercase gk-metric-label">TOTAL GURU</span>
                            <div class="rounded-circle gk-metric-icon d-flex align-items-center justify-content-center flex-shrink-0" style="background: rgba(0, 51, 102, 0.08); color: #003366;">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline gap-1 mt-auto">
                            <h3 class="fw-bold text-dark mb-0 font-mono gk-metric-value">{{ $totalGuru }}</h3>
                            <span class="text-muted gk-metric-unit">Guru</span>
                        </div>
                    </div>
                </div>

                {{-- 2. Sudah Dinilai --}}
                <div class="col-4">
                    <div class="gk-metric-clean-card h-100">
                        <div class="d-flex align-items-center justify-content-between mb-1.5 gap-1">
                            <span class="text-muted fw-bold text-uppercase gk-metric-label">SUDAH DINILAI</span>
                            <div class="rounded-circle gk-metric-icon d-flex align-items-center justify-content-center flex-shrink-0" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                                <i class="bi bi-check2-circle"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline justify-content-between flex-wrap gap-1 mt-auto">
                            <div class="d-flex align-items-baseline gap-1">
                                <h3 class="fw-bold text-dark mb-0 font-mono gk-metric-value">{{ $jumlahSudah }}</h3>
                                <span class="text-muted gk-metric-unit">Guru</span>
                            </div>
                            <span class="badge rounded-pill gk-metric-pct font-mono fw-bold" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                                {{ $pctSelesai }}%
                            </span>
                        </div>
                    </div>
                </div>

                {{-- 3. Sisa Penilaian --}}
                <div class="col-4">
                    <div class="gk-metric-clean-card h-100">
                        <div class="d-flex align-items-center justify-content-between mb-1.5 gap-1">
                            <span class="text-muted fw-bold text-uppercase gk-metric-label">SISA PENILAIAN</span>
                            <div class="rounded-circle gk-metric-icon d-flex align-items-center justify-content-center flex-shrink-0" style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline gap-1 mt-auto">
                            <h3 class="fw-bold text-dark mb-0 font-mono gk-metric-value">{{ max(0, $totalGuru - $jumlahSudah) }}</h3>
                            <span class="text-muted gk-metric-unit">Guru</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- HEADER SECTION GURU DENGAN PARTISIPASI TERTINGGI --}}
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h4 class="fw-bold mb-0 text-dark" style="font-size: 1.25rem;">Guru dengan Partisipasi Tertinggi</h4>
                    <small class="text-muted" style="font-size: 0.78rem;">Data Guru dengan persentase nilai terbaik dari siswa</small>
                </div>
                <div>
                    <a href="{{ route('siswa.guru.index') }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-semibold shadow-xs" style="background: #003366; border-color: #003366; font-size: 0.78rem;">
                        <i class="bi bi-pencil-square me-1"></i> Mulai Nilai Guru
                    </a>
                </div>
            </div>

            {{-- 3 KARTU GURU BERSIH SEJAJAR (COL-4 MASING-MASING, IDENTIK DENGAN KOLOM METRIK DI ATASNYA) --}}
            @if($topGuru->isEmpty())
                <div class="gk-clean-card p-5 text-center text-muted mb-3">
                    <i class="bi bi-trophy fs-1 d-block mb-2 opacity-50" style="color: #cbd5e1;"></i>
                    <p class="mb-1 fw-semibold text-dark">Belum Ada Peringkat</p>
                    <p class="small text-muted mb-0">Belum ada evaluasi guru untuk periode ini.</p>
                </div>
            @else
                @php
                    $g1 = $topGuru->get(0);
                    $g2 = $topGuru->get(1);
                    $g3 = $topGuru->get(2);
                @endphp
                <div class="row g-2 g-md-3 align-items-stretch mb-3">
                    {{-- #2nd (KIRI - PERAK) --}}
                    @if($g2)
                    @php
                        $score2 = (float)($g2->rata_rata_nilai ?? 0);
                        $pct2 = round(($score2 / 5) * 100);
                        $stars2 = round($score2 * 2) / 2;
                    @endphp
                    <div class="col-4">
                        <div class="gk-clean-card gk-dash-podium-card is-podium-2 text-center h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="mb-1.5">
                                    <span class="badge rounded-pill px-2 py-0.5 px-md-3 py-md-1 fw-bold" style="background: #e0f2fe; color: #0369a1; font-size: 0.72rem;">
                                        #2nd
                                    </span>
                                </div>
                                <div class="gk-avatar-clean-wrap gk-dash-podium-avatar">
                                    <img src="{{ $g2->photo_url }}" alt="{{ $g2->nama }}" class="w-100 h-100">
                                </div>
                                <h6 class="fw-bold mb-0.5 text-dark text-truncate gk-dash-podium-name" title="{{ $g2->nama }}">{{ $g2->nama }}</h6>
                                <div class="text-muted small mb-1.5 text-truncate font-mono gk-dash-podium-sub">
                                    {{ strtoupper($g2->jurusan?->nama_jurusan ?? $g2->kategori_label ?? 'GURU NORMADA') }}
                                </div>

                                {{-- BINTANG EMAS --}}
                                <div class="text-warning mb-1.5 gk-dash-podium-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($stars2 >= $i)
                                            <i class="bi bi-star-fill"></i>
                                        @elseif($stars2 >= ($i - 0.5))
                                            <i class="bi bi-star-half"></i>
                                        @else
                                            <i class="bi bi-star text-muted opacity-25"></i>
                                        @endif
                                    @endfor
                                </div>

                                {{-- PROGRESS PILL PERSENTASE --}}
                                <div class="mb-1">
                                    <div class="gk-progress-pill mx-auto">
                                        <div class="gk-progress-pill-fill" style="width: {{ $pct2 }}%;"></div>
                                        <span class="position-relative text-white" style="z-index: 2; text-shadow: 0 1px 2px rgba(0,0,0,0.6);">{{ $pct2 }}%</span>
                                    </div>
                                </div>
                                <small class="text-muted font-mono d-block mb-2.5 gk-dash-podium-ulasan">{{ $g2->penilaian_count ?? $g2->total_penilaian ?? 0 }} ulasan</small>
                            </div>

                            <div>
                                <a href="{{ route('siswa.guru.show', $g2) }}" class="btn btn-sm btn-outline-primary w-100 rounded-pill py-1 py-md-1.5 fw-semibold gk-dash-podium-btn" style="border-color: rgba(0, 51, 102, 0.25); color: #003366;">
                                    <i class="bi bi-eye me-1"></i> Profil
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- #1st (TENGAH - EMAS UTAMA) --}}
                    @if($g1)
                    @php
                        $score1 = (float)($g1->rata_rata_nilai ?? 0);
                        $pct1 = round(($score1 / 5) * 100);
                        $stars1 = round($score1 * 2) / 2;
                    @endphp
                    <div class="col-4">
                        <div class="gk-clean-card gk-dash-podium-card is-podium-1 text-center h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="mb-1.5">
                                    <span class="badge rounded-pill px-2 py-0.5 px-md-3 py-md-1 fw-bold" style="background: #fef08a; color: #854d0e; font-size: 0.74rem;">
                                        #1st
                                    </span>
                                </div>
                                <div class="gk-avatar-clean-wrap gk-dash-podium-avatar">
                                    <img src="{{ $g1->photo_url }}" alt="{{ $g1->nama }}" class="w-100 h-100">
                                </div>
                                <h6 class="fw-bold mb-0.5 text-dark text-truncate gk-dash-podium-name" title="{{ $g1->nama }}">{{ $g1->nama }}</h6>
                                <div class="text-muted small mb-1.5 text-truncate font-mono gk-dash-podium-sub">
                                    {{ strtoupper($g1->jurusan?->nama_jurusan ?? $g1->kategori_label ?? 'GURU NORMADA') }}
                                </div>

                                {{-- BINTANG EMAS --}}
                                <div class="text-warning mb-1.5 gk-dash-podium-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($stars1 >= $i)
                                            <i class="bi bi-star-fill"></i>
                                        @elseif($stars1 >= ($i - 0.5))
                                            <i class="bi bi-star-half"></i>
                                        @else
                                            <i class="bi bi-star text-muted opacity-25"></i>
                                        @endif
                                    @endfor
                                </div>

                                {{-- PROGRESS PILL PERSENTASE --}}
                                <div class="mb-1">
                                    <div class="gk-progress-pill mx-auto">
                                        <div class="gk-progress-pill-fill" style="width: {{ $pct1 }}%;"></div>
                                        <span class="position-relative text-white" style="z-index: 2; text-shadow: 0 1px 2px rgba(0,0,0,0.6);">{{ $pct1 }}%</span>
                                    </div>
                                </div>
                                <small class="text-muted font-mono d-block mb-2.5 gk-dash-podium-ulasan">{{ $g1->penilaian_count ?? $g1->total_penilaian ?? 0 }} ulasan</small>
                            </div>

                            <div>
                                <a href="{{ route('siswa.guru.show', $g1) }}" class="btn btn-sm btn-outline-primary w-100 rounded-pill py-1 py-md-1.5 fw-semibold gk-dash-podium-btn" style="border-color: rgba(0, 51, 102, 0.25); color: #003366;">
                                    <i class="bi bi-eye me-1"></i> Profil
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- #3rd (KANAN - PERUNGGU) --}}
                    @if($g3)
                    @php
                        $score3 = (float)($g3->rata_rata_nilai ?? 0);
                        $pct3 = round(($score3 / 5) * 100);
                        $stars3 = round($score3 * 2) / 2;
                    @endphp
                    <div class="col-4">
                        <div class="gk-clean-card gk-dash-podium-card is-podium-3 text-center h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="mb-1.5">
                                    <span class="badge rounded-pill px-2 py-0.5 px-md-3 py-md-1 fw-bold" style="background: #fed7aa; color: #9a3412; font-size: 0.72rem;">
                                        #3rd
                                    </span>
                                </div>
                                <div class="gk-avatar-clean-wrap gk-dash-podium-avatar">
                                    <img src="{{ $g3->photo_url }}" alt="{{ $g3->nama }}" class="w-100 h-100">
                                </div>
                                <h6 class="fw-bold mb-0.5 text-dark text-truncate gk-dash-podium-name" title="{{ $g3->nama }}">{{ $g3->nama }}</h6>
                                <div class="text-muted small mb-1.5 text-truncate font-mono gk-dash-podium-sub">
                                    {{ strtoupper($g3->jurusan?->nama_jurusan ?? $g3->kategori_label ?? 'GURU NORMADA') }}
                                </div>

                                {{-- BINTANG EMAS --}}
                                <div class="text-warning mb-1.5 gk-dash-podium-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($stars3 >= $i)
                                            <i class="bi bi-star-fill"></i>
                                        @elseif($stars3 >= ($i - 0.5))
                                            <i class="bi bi-star-half"></i>
                                        @else
                                            <i class="bi bi-star text-muted opacity-25"></i>
                                        @endif
                                    @endfor
                                </div>

                                {{-- PROGRESS PILL PERSENTASE --}}
                                <div class="mb-1">
                                    <div class="gk-progress-pill mx-auto">
                                        <div class="gk-progress-pill-fill" style="width: {{ $pct3 }}%;"></div>
                                        <span class="position-relative text-white" style="z-index: 2; text-shadow: 0 1px 2px rgba(0,0,0,0.6);">{{ $pct3 }}%</span>
                                    </div>
                                </div>
                                <small class="text-muted font-mono d-block mb-2.5 gk-dash-podium-ulasan">{{ $g3->penilaian_count ?? $g3->total_penilaian ?? 0 }} ulasan</small>
                            </div>

                            <div>
                                <a href="{{ route('siswa.guru.show', $g3) }}" class="btn btn-sm btn-outline-primary w-100 rounded-pill py-1 py-md-1.5 fw-semibold gk-dash-podium-btn" style="border-color: rgba(0, 51, 102, 0.25); color: #003366;">
                                    <i class="bi bi-eye me-1"></i> Profil
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- TOMBOL PILL HITAM LIHAT SELENGKAPNYA MIRIP PUBLIK --}}
                <div class="text-center pt-2 mb-4">
                    <a href="{{ route('siswa.leaderboard.index') }}" class="gk-pill-btn-dark">
                        Lihat selengkapnya
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- KOLOM KANAN (5 Kolom): RIWAYAT PENILAIAN TERAKHIR (DENGAN JARAK RESPONSIF) --}}
    <div class="col-lg-5 d-flex flex-column mt-4 mt-lg-0">
        <div class="gk-clean-card p-3.5 p-md-4 h-100 d-flex flex-column justify-content-between mb-4 mb-lg-0" style="min-height: 520px;">
            <div>
                {{-- Header Riwayat --}}
                <div class="d-flex justify-content-between align-items-center mb-3.5">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: rgba(0, 51, 102, 0.08); color: #003366; font-size: 1rem;">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">Riwayat Terakhir</h5>
                            <small class="text-muted" style="font-size: 0.72rem;">Penilaian yang telah Anda kirimkan</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(0, 51, 102, 0.08); color: #003366; font-size: 0.72rem; font-weight: 700;">
                            {{ $jumlahSudah }} Selesai
                        </span>
                        <a href="{{ route('siswa.riwayat') }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1 fw-semibold" style="background: #003366; border-color: #003366; font-size: 0.75rem;">
                            Semua
                        </a>
                    </div>
                </div>

                {{-- Feed Scrollable Berdiri: Tinggi vertikal proporsional serupa feed ulasan Guru --}}
                <div class="gk-feed-scroll flex-grow-1 overflow-y-auto pe-1" style="max-height: 460px; min-height: 400px;">
                    @forelse($riwayatTerakhir as $riwayat)
                        @php
                            $rwScore = round(($riwayat->rata_rata_evaluasi / 5) * 100);
                            $badgeColor = $rwScore >= 75 ? 'rgba(16, 185, 129, 0.12)' : ($rwScore >= 50 ? 'rgba(0, 51, 102, 0.08)' : 'rgba(245, 158, 11, 0.12)');
                            $badgeTextColor = $rwScore >= 75 ? '#059669' : ($rwScore >= 50 ? '#003366' : '#b45309');
                        @endphp
                        <div class="gk-history-item mb-2.5">
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                <div class="d-flex align-items-center gap-2.5 overflow-hidden">
                                    <img src="{{ $riwayat->guru->photo_url }}" class="rounded-circle shadow-sm flex-shrink-0" style="width: 38px; height: 38px; min-width: 38px; min-height: 38px; aspect-ratio: 1 / 1; object-fit: cover; border: 2px solid rgba(0, 51, 102, 0.12);">
                                    <div class="overflow-hidden">
                                        <h6 class="fw-bold mb-0 text-truncate text-dark" title="{{ $riwayat->guru->nama }}" style="font-size: 0.88rem;">{{ $riwayat->guru->nama }}</h6>
                                        <span class="badge rounded-pill px-2 py-0.5 mt-0.5" style="background: rgba(0, 51, 102, 0.06); color: #003366; font-size: 0.66rem; font-weight: 600;">
                                            {{ strtoupper($riwayat->guru->jurusan?->nama_jurusan ?? $riwayat->guru->kategori ?? 'UMUM') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-end flex-shrink-0">
                                    <span class="badge rounded-pill px-2 py-0.5 font-mono fw-bold" style="background: {{ $badgeColor }}; color: {{ $badgeTextColor }}; font-size: 0.72rem;">
                                        {{ $rwScore }}%
                                    </span>
                                    <div class="text-muted font-mono d-block mt-0.5" style="font-size: 0.68rem;">
                                        {{ $riwayat->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>

                            {{-- Rating Bar --}}
                            @php
                                $dashStars = round(($riwayat->rata_rata_evaluasi ?? ($riwayat->total_nilai / 5)) * 2) / 2;
                            @endphp
                            <div class="mb-2">
                                <div class="d-flex justify-content-between align-items-center mb-1 font-mono" style="font-size: 0.7rem;">
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="text-warning d-inline-flex align-items-center" style="font-size: 0.72rem;">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($dashStars >= $i)
                                                    <i class="bi bi-star-fill"></i>
                                                @elseif($dashStars >= ($i - 0.5))
                                                    <i class="bi bi-star-half"></i>
                                                @else
                                                    <i class="bi bi-star text-muted opacity-25"></i>
                                                @endif
                                            @endfor
                                        </span>
                                        <span class="text-muted ms-0.5">({{ number_format($riwayat->total_nilai, 2) }}/25)</span>
                                    </div>
                                    <span class="fw-semibold text-dark">{{ $rwScore }}%</span>
                                </div>
                                <div class="progress" style="height: 5px; border-radius: 4px; background: #e2e8f0;">
                                    <div class="progress-bar rounded-pill" style="width: {{ $rwScore }}%; background: linear-gradient(90deg, #0284c7, #38bdf8);"></div>
                                </div>
                            </div>

                            {{-- Aksi Cepat / Status --}}
                            <div class="d-flex justify-content-between align-items-center pt-1.5 border-top" style="border-color: rgba(15, 23, 42, 0.06) !important;">
                                @if($riwayat->kritik || $riwayat->saran)
                                    <span class="badge rounded-pill px-2 py-0.5" style="background: rgba(2, 132, 199, 0.08); color: #0284c7; font-size: 0.68rem; font-weight: 600;">
                                        <i class="bi bi-chat-text me-0.5"></i> Ada Masukan
                                    </span>
                                @else
                                    <span class="badge rounded-pill px-2 py-0.5" style="background: rgba(16, 185, 129, 0.08); color: #059669; font-size: 0.68rem; font-weight: 600;">
                                        <i class="bi bi-check2 me-0.5"></i> Telah Dinilai
                                    </span>
                                @endif
                                <a href="{{ route('siswa.guru.show', $riwayat->guru_id) }}" class="btn btn-sm btn-link p-0 text-decoration-none fw-semibold" style="font-size: 0.74rem; color: #003366;">
                                    Lihat Profil <i class="bi bi-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x fs-1 d-block mb-2 opacity-40" style="color: #cbd5e1;"></i>
                            <p class="mb-1 fw-semibold text-dark" style="font-size: 0.9rem;">Belum Ada Penilaian</p>
                            <p class="text-muted small mb-3" style="font-size: 0.78rem;">Anda belum memberikan penilaian guru pada periode ini.</p>
                            <a href="{{ route('siswa.guru.index') }}" class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 fw-semibold" style="background: #003366; border-color: #003366;">
                                <i class="bi bi-pencil-square me-1"></i> Mulai Menilai
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Footer Riwayat --}}
            <div class="mt-3 pt-2.5 border-top d-flex justify-content-between align-items-center">
                <span class="text-muted small" style="font-size: 0.74rem;">
                    Ditampilkan {{ $riwayatTerakhir->count() }} penilaian terakhir.
                </span>
                <a href="{{ route('siswa.riwayat') }}" class="fw-bold small text-decoration-none" style="color: #003366; font-size: 0.78rem;">
                    Kelola Semua <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT SLIDER PANDUAN PENGGUNAAN (AUTOPLAY CONTINUOUS 1-2-3, PAUSE ON INTERACTION, TOUCH & DRAG) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const track = document.getElementById('panduanTrack');
    const viewport = document.getElementById('panduanViewport');
    const prevBtn = document.getElementById('panduanPrevBtn');
    const nextBtn = document.getElementById('panduanNextBtn');
    const dots = document.querySelectorAll('#panduanDots .gk-panduan-dot');
    const slides = document.querySelectorAll('.gk-panduan-slide');

    if (!track || !viewport || slides.length === 0) return;

    let currentIndex = 0;
    const totalSlides = slides.length;
    let autoplayTimer = null;
    let idleTimer = null;
    const AUTOPLAY_DELAY = 4000;      // 4 detik jeda geser otomatis berkala
    const RESUME_IDLE_DELAY = 4500;   // 4.5 detik diam setelah interaksi sebelum mulai geser lagi

    function updateSlide(animate = true) {
        if (!animate) {
            track.style.transition = 'none';
        } else {
            track.style.transition = 'transform 0.65s cubic-bezier(0.22, 1, 0.36, 1)';
        }

        track.style.transform = `translateX(-${currentIndex * 100}%)`;

        slides.forEach((slide, idx) => {
            slide.classList.toggle('is-active', idx === currentIndex);
        });

        dots.forEach((dot, idx) => {
            dot.classList.toggle('active', idx === currentIndex);
        });
    }

    function nextSlide() {
        currentIndex = (currentIndex + 1) % totalSlides;
        updateSlide(true);
    }

    function prevSlide() {
        currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
        updateSlide(true);
    }

    function goToSlide(index) {
        if (index < 0 || index >= totalSlides) return;
        currentIndex = index;
        updateSlide(true);
    }

    function startAutoplay() {
        stopAutoplay();
        autoplayTimer = setInterval(nextSlide, AUTOPLAY_DELAY);
    }

    function stopAutoplay() {
        if (autoplayTimer) {
            clearInterval(autoplayTimer);
            autoplayTimer = null;
        }
    }

    // Diam saat disentuh/diinteraksi, lalu jalan kembali setelah tidak ada interaksi selama beberapa waktu
    function pauseAndScheduleResume() {
        stopAutoplay();
        if (idleTimer) {
            clearTimeout(idleTimer);
            idleTimer = null;
        }
        idleTimer = setTimeout(function () {
            startAutoplay();
        }, RESUME_IDLE_DELAY);
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            nextSlide();
            pauseAndScheduleResume();
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            prevSlide();
            pauseAndScheduleResume();
        });
    }

    dots.forEach((dot) => {
        dot.addEventListener('click', function () {
            const idx = parseInt(this.getAttribute('data-index'), 10);
            goToSlide(idx);
            pauseAndScheduleResume();
        });
    });

    viewport.addEventListener('mouseenter', stopAutoplay);
    viewport.addEventListener('mouseleave', pauseAndScheduleResume);

    // ==========================================
    // GESER PAKAI TANGAN / SENTUH (TOUCH MOBILE)
    // ==========================================
    let touchStartX = 0;
    let touchStartY = 0;
    let touchCurrentX = 0;
    let isTouchSwiping = false;
    let hasDeterminedTouchDirection = false;
    let isTouchHorizontal = false;

    viewport.addEventListener('touchstart', function (e) {
        if (e.touches.length !== 1) return;
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
        touchCurrentX = touchStartX;
        isTouchSwiping = true;
        hasDeterminedTouchDirection = false;
        isTouchHorizontal = false;
        stopAutoplay();
    }, { passive: true });

    viewport.addEventListener('touchmove', function (e) {
        if (!isTouchSwiping || e.touches.length !== 1) return;
        touchCurrentX = e.touches[0].clientX;
        const currentY = e.touches[0].clientY;
        const diffX = touchCurrentX - touchStartX;
        const diffY = currentY - touchStartY;

        if (!hasDeterminedTouchDirection) {
            if (Math.abs(diffX) > 6 || Math.abs(diffY) > 6) {
                hasDeterminedTouchDirection = true;
                isTouchHorizontal = Math.abs(diffX) > Math.abs(diffY);
            }
        }

        if (isTouchHorizontal) {
            if (e.cancelable) e.preventDefault();
            const vpWidth = viewport.offsetWidth || 300;
            const dragPercent = (diffX / vpWidth) * 100;
            const currentTranslate = -currentIndex * 100;
            track.style.transition = 'none';
            track.style.transform = `translateX(${currentTranslate + dragPercent}%)`;
        }
    }, { passive: false });

    function handleTouchEnd() {
        if (!isTouchSwiping) return;
        isTouchSwiping = false;
        const diffX = touchCurrentX - touchStartX;
        track.style.transition = 'transform 0.65s cubic-bezier(0.22, 1, 0.36, 1)';

        if (isTouchHorizontal && Math.abs(diffX) > 35) {
            if (diffX < 0) {
                nextSlide();
            } else {
                prevSlide();
            }
        } else {
            updateSlide(true);
        }
        pauseAndScheduleResume();
    }

    viewport.addEventListener('touchend', handleTouchEnd);
    viewport.addEventListener('touchcancel', handleTouchEnd);

    // ==========================================
    // GESER DENGAN DRAG MOUSE (DESKTOP)
    // ==========================================
    let isMouseDown = false;
    let mouseStartX = 0;
    let mouseCurrentX = 0;

    viewport.addEventListener('mousedown', function (e) {
        if (e.button !== 0) return;
        isMouseDown = true;
        mouseStartX = e.clientX;
        mouseCurrentX = mouseStartX;
        viewport.style.cursor = 'grabbing';
        stopAutoplay();
        e.preventDefault();
    });

    window.addEventListener('mousemove', function (e) {
        if (!isMouseDown) return;
        mouseCurrentX = e.clientX;
        const diffX = mouseCurrentX - mouseStartX;
        const vpWidth = viewport.offsetWidth || 300;
        const dragPercent = (diffX / vpWidth) * 100;
        const currentTranslate = -currentIndex * 100;
        track.style.transition = 'none';
        track.style.transform = `translateX(${currentTranslate + dragPercent}%)`;
    });

    window.addEventListener('mouseup', function () {
        if (!isMouseDown) return;
        isMouseDown = false;
        viewport.style.cursor = 'grab';
        const diffX = mouseCurrentX - mouseStartX;
        track.style.transition = 'transform 0.65s cubic-bezier(0.22, 1, 0.36, 1)';

        if (Math.abs(diffX) > 40) {
            if (diffX < 0) {
                nextSlide();
            } else {
                prevSlide();
            }
        } else {
            updateSlide(true);
        }
        pauseAndScheduleResume();
    });

    // Mulai autoplay berkala
    startAutoplay();
});
</script>
@endsection