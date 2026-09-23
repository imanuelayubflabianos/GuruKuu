@extends('layouts.guru')
@section('title', 'Dashboard Guru')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <div class="page-label">PORTAL GURU</div>
        <h1 class="page-title">Selamat Datang, {{ $guru->nama }}! 👋</h1>
        <p class="page-subtitle">Berikut ringkasan evaluasi kinerja dan aspirasi siswa pada periode {{ $periodeAktif->nama_periode ?? 'Aktif' }}.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('guru.leaderboard') }}" class="btn btn-outline-custom rounded-pill px-3 py-2 shadow-sm">
            <i class="bi bi-trophy me-1 text-warning"></i> Lihat Leaderboard
        </a>
        <a href="{{ route('guru.ulasan') }}" class="btn btn-primary-custom rounded-pill px-3 py-2 shadow-sm">
            <i class="bi bi-chat-square-quote me-1"></i> Kelola Ulasan Siswa
        </a>
    </div>
</div>

{{-- STATISTIK UTAMA (FROSTED GLASS CARDS) --}}
<div class="row g-2 g-md-4 mb-3 mb-md-4">
    {{-- Rata-rata Nilai --}}
    <div class="col-4 col-md-4">
        <div class="card-glass p-2.5 p-md-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(245, 158, 11, 0.12); color: #d97706;">
                        <i class="bi bi-award-fill fs-5"></i>
                    </div>
                    @php $pctKepuasan = round((($guru->rata_rata_nilai ?? 0) / 5) * 100); @endphp
                    <span class="badge rounded-pill d-none d-sm-inline" style="background: rgba(245, 158, 11, 0.12); color: #d97706; font-size: 0.72rem; font-weight: 600;">
                        {{ $pctKepuasan }}%
                    </span>
                </div>
                <div class="text-muted small font-mono" style="font-size: 0.62rem; letter-spacing: 0.5px;">RATA-RATA</div>
                <div class="d-flex align-items-baseline gap-1 mt-0.5">
                    <h3 class="fw-bold mb-0 text-dark" style="font-feature-settings: 'tnum'; font-size: 1.5rem;">{{ number_format($guru->rata_rata_nilai ?? 0, 2) }}</h3>
                    <span class="text-muted small d-none d-sm-inline">/5</span>
                </div>
            </div>
            <div class="progress mt-2" style="height: 5px; border-radius: 10px;">
                <div class="progress-bar bg-warning rounded-pill" style="width: {{ $pctKepuasan }}%;"></div>
            </div>
        </div>
    </div>

    {{-- Total Penilaian Siswa --}}
    <div class="col-4 col-md-4">
        <div class="card-glass p-2.5 p-md-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(0, 168, 107, 0.1); color: var(--accent);">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>
                    <span class="badge rounded-pill d-none d-sm-inline" style="background: rgba(0, 168, 107, 0.1); color: var(--accent); font-size: 0.72rem; font-weight: 600;">
                        Siswa
                    </span>
                </div>
                <div class="text-muted small font-mono" style="font-size: 0.62rem; letter-spacing: 0.5px;">EVALUASI</div>
                <div class="fw-bold text-dark mt-0.5" style="font-size: 1.5rem; font-feature-settings: 'tnum'; line-height: 1.1;">{{ $guru->total_penilaian ?? 0 }}</div>
            </div>
            <small class="text-muted font-mono d-none d-md-block mt-1" style="font-size: 0.72rem;">Siswa yang telah menilai</small>
        </div>
    </div>

    {{-- Total Feedback Masuk --}}
    <div class="col-4 col-md-4">
        <div class="card-glass p-2.5 p-md-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(236, 72, 153, 0.1); color: #ec4899;">
                        <i class="bi bi-chat-quote-fill fs-5"></i>
                    </div>
                    <span class="badge rounded-pill d-none d-sm-inline" style="background: rgba(236, 72, 153, 0.1); color: #ec4899; font-size: 0.72rem; font-weight: 600;">
                        Saran
                    </span>
                </div>
                <div class="text-muted small font-mono" style="font-size: 0.62rem; letter-spacing: 0.5px;">FEEDBACK</div>
                <div class="fw-bold text-dark mt-0.5" style="font-size: 1.5rem; font-feature-settings: 'tnum'; line-height: 1.1;">{{ ($ulasanTerbaru ?? collect())->count() }}</div>
            </div>
            <small class="text-muted font-mono d-none d-md-block mt-1" style="font-size: 0.72rem;">Aspirasi & masukan</small>
        </div>
    </div>
</div>

{{-- DETAIL PROFIL GURU & INFORMASI AKUN (FROSTED GLASS PANEL) --}}
<div class="card-glass p-4 mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <img src="{{ $guru->photo_url }}" alt="{{ $guru->nama }}" class="rounded-circle me-3 shadow-sm" style="width: 64px; height: 64px; object-fit: cover; border: 3px solid var(--border);" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($guru->nama) }}&background=003366&color=fff'">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">{{ $guru->nama }}</h4>
                    <div class="text-muted small font-mono d-flex align-items-center gap-2 flex-wrap">
                        <span>NIP: <strong>{{ $guru->nip }}</strong></span>
                        <span class="badge rounded-pill px-2.5 py-1" style="background: var(--bg-light); color: var(--primary); border: 1px solid var(--border);">
                            {{ strtoupper($guru->jurusan?->nama_jurusan ?? 'UMUM') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ route('guru.pengaturan') }}" class="btn btn-outline-custom rounded-pill px-3 py-1.5 shadow-sm">
                <i class="bi bi-gear me-1"></i> Pengaturan Akun
            </a>
        </div>
    </div>
</div>

{{-- SECTION FEEDBACK / ULASAN SISWA --}}
<div class="card-glass p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-chat-heart-fill me-2 text-danger"></i>Ulasan & Aspirasi Siswa Terbaru
            </h5>
            <small class="text-muted">Identitas siswa 100% rahasia & anonim demi objektivitas evaluasi.</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge rounded-pill px-3 py-1.5" style="background: rgba(0, 51, 102, 0.08); color: var(--primary); border: 1px solid var(--border);">
                {{ ($ulasanTerbaru ?? collect())->count() }} Ulasan Masuk
            </span>
            <a href="{{ route('guru.ulasan') }}" class="btn btn-sm btn-primary-custom rounded-pill px-3 py-1.5">
                <i class="bi bi-chat-square-quote me-1"></i> Kelola di Menu Ulasan
            </a>
        </div>
    </div>

    @if(!empty($ulasanTerbaru) && $ulasanTerbaru->isNotEmpty())
        <div class="row g-3">
            @foreach($ulasanTerbaru->take(4) as $review)
            <div class="col-md-6">
                <div class="p-3.5 rounded-3 border h-100 d-flex flex-column justify-content-between" style="background: var(--bg-card); border-color: var(--border) !important;">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(0, 51, 102, 0.06); color: var(--primary); font-size: 0.72rem; border: 1px solid var(--border);">
                                <i class="bi bi-shield-lock-fill me-1 text-success"></i> Siswa (Anonim)
                            </span>
                            <small class="text-muted font-mono" style="font-size: 0.72rem;">
                                {{ $review->created_at->format('d M Y, H:i') }}
                            </small>
                        </div>

                        {{-- Rating Persen & Bar --}}
                        @php $revPct = round(($review->rata_rata_evaluasi / 5) * 100); @endphp
                        <div class="mb-2" style="max-width: 220px;">
                            <div class="d-flex justify-content-between align-items-center mb-1 font-mono" style="font-size: 0.75rem;">
                                <span class="fw-bold text-primary">{{ $revPct }}%</span>
                                <span class="text-muted">({{ $review->total_nilai }}/25)</span>
                            </div>
                            <div class="progress" style="height: 5px; border-radius: 10px;">
                                <div class="progress-bar bg-primary rounded-pill" style="width: {{ $revPct }}%;"></div>
                            </div>
                        </div>

                        @if($review->is_censored)
                            <div class="mb-2 p-2.5 rounded-3 border text-muted fst-italic small" style="background: var(--bg-light); border-color: var(--border) !important;">
                                <i class="bi bi-shield-exclamation text-warning me-1"></i> Ulasan ini disembunyikan oleh Administrator sekolah.
                            </div>
                        @else
                            @if($review->kritik)
                                <div class="mb-2">
                                    <small class="text-danger fw-bold d-block mb-1 font-mono" style="font-size: 0.7rem;"><i class="bi bi-chat-left-dots me-1"></i>KRITIK / CATATAN:</small>
                                    <p class="mb-0 small text-dark p-2 rounded-3 border" style="background: rgba(220, 53, 69, 0.04); border-color: rgba(220, 53, 69, 0.2) !important;">{{ Str::limit($review->kritik, 120) }}</p>
                                </div>
                            @endif

                            @if($review->saran)
                                <div class="mb-2">
                                    <small class="text-success fw-bold d-block mb-1 font-mono" style="font-size: 0.7rem;"><i class="bi bi-lightbulb me-1"></i>SARAN & HARAPAN:</small>
                                    <p class="mb-0 small text-dark p-2 rounded-3 border" style="background: rgba(25, 135, 84, 0.04); border-color: rgba(25, 135, 84, 0.2) !important;">{{ Str::limit($review->saran, 120) }}</p>
                                </div>
                            @endif
                        @endif

                        @if($review->balasans && $review->balasans->count() > 0)
                            <div class="mt-2 p-2.5 rounded-3 border-start border-3 border-primary shadow-xs" style="background: var(--bg-light);">
                                <small class="text-primary fw-bold d-block mb-1" style="font-size: 0.72rem;"><i class="bi bi-chat-left-dots-fill me-1"></i>Diskusi Terkini ({{ $review->balasans->count() }} balasan):</small>
                                <p class="mb-0 small text-muted font-italic">{{ Str::limit($review->balasans->last()->pesan, 80) }}</p>
                            </div>
                        @elseif($review->balasan_guru)
                            <div class="mt-2 p-2.5 rounded-3 border-start border-3 border-success shadow-sm" style="background: var(--bg-light);">
                                <small class="text-success fw-bold d-block mb-1" style="font-size: 0.72rem;"><i class="bi bi-reply-fill me-1"></i>Telah Anda Balas:</small>
                                <p class="mb-0 small text-muted font-italic">{{ Str::limit($review->balasan_guru, 80) }}</p>
                            </div>
                        @endif
                    </div>

                    <div class="mt-3 pt-2 border-top d-flex justify-content-between align-items-center" style="border-color: var(--border) !important;">
                        @if(($review->balasans && $review->balasans->count() > 0) || $review->balasan_guru)
                            <span class="badge rounded-pill px-2 py-1" style="background: rgba(25, 135, 84, 0.1); color: #198754; font-size: 0.72rem;"><i class="bi bi-check-lg me-1"></i>Ada Diskusi</span>
                        @else
                            <span class="badge rounded-pill px-2 py-1" style="background: rgba(245, 158, 11, 0.12); color: #d97706; font-size: 0.72rem;"><i class="bi bi-hourglass-split me-1"></i>Belum Dibalas</span>
                        @endif
                        <a href="{{ route('guru.ulasan') }}" class="btn btn-sm btn-outline-primary rounded-pill py-0.5 px-2.5" style="font-size: 0.78rem;">
                            Buka Diskusi <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($ulasanTerbaru->count() > 4)
            <div class="text-center mt-3 pt-2">
                <a href="{{ route('guru.ulasan') }}" class="btn btn-outline-custom rounded-pill btn-sm px-4">
                    Lihat Seluruh {{ $ulasanTerbaru->count() }} Ulasan Siswa <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        @endif
    @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-chat-square-text fs-1 d-block mb-2 text-secondary opacity-50"></i>
            Belum ada ulasan atau kritik dan saran yang masuk dari siswa untuk periode ini.
        </div>
    @endif
</div>
@endsection