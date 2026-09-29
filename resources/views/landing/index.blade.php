@extends('layouts.landing')
@section('title', 'Beranda')

@section('content')
@php
    $heroBadge = \App\Models\Setting::get('hero_badge') ?: 'SMK NEGERI 1 BANGSRI • JUARA';
    $heroTitle = \App\Models\Setting::get('hero_title') ?: 'Bangun Sekolah yang Lebih Baik Melalui Penilaian Guru yang Objektif';
    $heroSubtitle = \App\Models\Setting::get('hero_subtitle') ?: 'Suarakan aspirasimu secara aman untuk meningkatkan kualitas pengajaran dan menciptakan lingkungan belajar yang inspiratif.';
    $heroCtaText = \App\Models\Setting::get('hero_cta_text') ?: 'Siap Memulai?';
    $heroCtaUrl = \App\Models\Setting::get('hero_cta_url') ?: route('login');

    $heroImg1 = \App\Models\Setting::get('hero_image', '/uploads/hero/hero_KRCqd4TJtmyGUMoMVp58Vr6Z.png');
    $heroImg2 = \App\Models\Setting::get('hero_image_2', '');
    $heroImg3 = \App\Models\Setting::get('hero_image_3', '');

    $heroImages = array_values(array_filter([$heroImg1, $heroImg2, $heroImg3]));
    if (count($heroImages) < 2) {
        $heroImages = [
            $heroImg1 ?: 'https://images.unsplash.com/photo-1562774053-701939374585?w=1920',
            'https://images.unsplash.com/photo-1580582932707-520aed937b7b?w=1920',
            'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=1920'
        ];
    }
@endphp

<style>
.hero-ambient-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(70px);
    opacity: 0.35;
    pointer-events: none;
    z-index: 1;
    animation: floatAmbient 9s ease-in-out infinite alternate;
}
@keyframes floatAmbient {
    0% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(30px, -20px) scale(1.12); }
    100% { transform: translate(-20px, 25px) scale(0.92); }
}
.hero-slide-bg {
    transition: transform 6s cubic-bezier(0.25, 1, 0.5, 1);
    transform: scale(1);
    min-height: 640px;
}
.carousel-item.active .hero-slide-bg {
    transform: scale(1.06);
}
.hero-glass-badge {
    animation: pulseBadge 3.5s ease-in-out infinite;
}
@keyframes pulseBadge {
    0%, 100% { transform: translateY(0); box-shadow: 0 4px 15px rgba(255, 193, 7, 0.25); }
    50% { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255, 193, 7, 0.45); }
}
.hero-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.65rem;
    padding: 0.85rem 2.2rem;
    min-height: 52px;
    border-radius: 50px;
    font-size: 0.95rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
}
.hero-btn-primary {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: #0f172a !important;
    border: 1px solid rgba(255, 255, 255, 0.35);
    box-shadow: 0 8px 24px -4px rgba(245, 158, 11, 0.5);
}
.hero-btn-primary:hover {
    background: linear-gradient(135deg, #fbbf24 0%, #b45309 100%);
    color: #000000 !important;
    transform: translateY(-3px);
    box-shadow: 0 14px 30px -4px rgba(245, 158, 11, 0.65);
}
.hero-btn-secondary {
    background: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    color: #ffffff !important;
    border: 1.5px solid rgba(255, 255, 255, 0.38);
    box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.25);
}
.hero-btn-secondary:hover {
    background: rgba(255, 255, 255, 0.25);
    border-color: #ffffff;
    color: #ffffff !important;
    transform: translateY(-3px);
    box-shadow: 0 14px 30px -4px rgba(0, 0, 0, 0.35);
}
/* Smooth Public Dashboard Interactive Transitions */
.stat-card-modern, .teacher-card, .tutorial-card, .gk-vm-card, .gk-feature-card, .card-custom {
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.35s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.3s ease;
}
.stat-card-modern:hover, .tutorial-card:hover, .gk-feature-card:hover {
    transform: translateY(-6px);
}
.gk-vm-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(0, 51, 102, 0.08);
}
.accordion-button {
    transition: background-color 0.25s ease, color 0.25s ease, box-shadow 0.25s ease;
}
.accordion-button:not(.collapsed) {
    background-color: var(--primary-subtle, rgba(0, 51, 102, 0.08)) !important;
    color: var(--primary, #003366) !important;
}
</style>

{{-- 1. HERO SECTION (id="home") - SLIDER DENGAN EFEK ANIMASI RINGAN DAN DINAMIS --}}
<section id="home" class="p-0 position-relative overflow-hidden" style="min-height: 600px;">
    {{-- Floating Ambient Glow Orbs --}}
    <div class="hero-ambient-orb" style="top: 15%; right: 12%; width: 280px; height: 280px; background: radial-gradient(circle, rgba(0, 168, 107, 0.45), transparent 70%);"></div>
    <div class="hero-ambient-orb" style="bottom: 12%; left: 8%; width: 340px; height: 340px; background: radial-gradient(circle, rgba(255, 193, 7, 0.38), transparent 70%); animation-delay: -4s;"></div>

    {{-- Background Carousel Slideshow --}}
    <div id="heroBgSlider" class="carousel slide carousel-fade position-absolute w-100 h-100" data-bs-ride="carousel" data-bs-interval="4500" style="top: 0; left: 0; z-index: 1;">
        <div class="carousel-indicators mb-4" style="z-index: 3;">
            @foreach($heroImages as $idx => $img)
                <button type="button" data-bs-target="#heroBgSlider" data-bs-slide-to="{{ $idx }}" class="{{ $idx === 0 ? 'active' : '' }}" aria-current="{{ $idx === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $idx + 1 }}"></button>
            @endforeach
        </div>
        <div class="carousel-inner w-100 h-100">
            @foreach($heroImages as $idx => $img)
                <div class="carousel-item {{ $idx === 0 ? 'active' : '' }} w-100 h-100">
                    <div class="hero-slide-bg w-100 h-100" style="background: linear-gradient(135deg, rgba(10, 25, 47, 0.88), rgba(0, 51, 102, 0.78)), url('{{ $img }}') center/cover no-repeat; min-height: 640px;"></div>
                </div>
            @endforeach
        </div>

        {{-- Next / Prev Arrows: Slide 1 only Next; Slide 2 Prev & Next; Slide 3 only Prev --}}
        <button class="carousel-control-prev" id="heroPrevBtn" type="button" data-bs-target="#heroBgSlider" data-bs-slide="prev" style="z-index: 4; width: 5%; display: none;">
            <span class="carousel-control-prev-icon rounded-circle p-2.5" style="background-color: rgba(0,0,0,0.35); backdrop-filter: blur(4px);" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" id="heroNextBtn" type="button" data-bs-target="#heroBgSlider" data-bs-slide="next" style="z-index: 4; width: 5%;">
            <span class="carousel-control-next-icon rounded-circle p-2.5" style="background-color: rgba(0,0,0,0.35); backdrop-filter: blur(4px);" aria-hidden="true"></span>
        </button>
    </div>

        {{-- Content Overlay --}}
    <div class="container position-relative" style="z-index: 2; padding: 160px 0 110px;">
        <div class="row">
            <div class="col-lg-9 col-xl-8" data-aos="fade-right">
                <div class="hero-glass-badge">
                    <i class="bi bi-patch-check-fill text-warning"></i>
                    <span>{{ $heroBadge }}</span>
                </div>
                <h1 class="hero-title text-white">{{ $heroTitle }}</h1>
                <p class="hero-subtitle text-white" style="color: rgba(255, 255, 255, 0.92) !important;">{{ $heroSubtitle }}</p>
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <a href="{{ $heroCtaUrl }}" class="hero-btn hero-btn-primary">
                        <span>{{ $heroCtaText }}</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    <a href="#statistik" class="hero-btn hero-btn-secondary">
                        <i class="bi bi-bar-chart-line"></i>
                        <span>Lihat Statistik</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const heroSlider = document.getElementById('heroBgSlider');
    const prevBtn = document.getElementById('heroPrevBtn');
    const nextBtn = document.getElementById('heroNextBtn');
    const totalSlides = {{ count($heroImages) }};

    function updateHeroArrows(index) {
        if (!prevBtn || !nextBtn) return;
        if (index <= 0) {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'flex';
        } else if (index >= totalSlides - 1) {
            prevBtn.style.display = 'flex';
            nextBtn.style.display = 'none';
        } else {
            prevBtn.style.display = 'flex';
            nextBtn.style.display = 'flex';
        }
    }

    if (heroSlider && typeof bootstrap !== 'undefined') {
        const carousel = new bootstrap.Carousel(heroSlider, {
            interval: 5000,
            ride: 'carousel',
            wrap: false
        });

        heroSlider.addEventListener('slid.bs.carousel', function(e) {
            updateHeroArrows(e.to);
        });

        updateHeroArrows(0);
    }

    // FAQ Accordion Toggle (Buka & Tutup)
    document.querySelectorAll('#landingFaqAccordion .accordion-button').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const targetSelector = this.getAttribute('data-bs-target');
            const targetEl = targetSelector ? document.querySelector(targetSelector) : null;
            if (!targetEl || typeof bootstrap === 'undefined') return;

            const bsCollapse = bootstrap.Collapse.getOrCreateInstance(targetEl, { toggle: false });
            const isShown = targetEl.classList.contains('show');

            if (isShown) {
                bsCollapse.hide();
                this.classList.add('collapsed');
                this.setAttribute('aria-expanded', 'false');
            } else {
                bsCollapse.show();
                this.classList.remove('collapsed');
                this.setAttribute('aria-expanded', 'true');
            }
        });
    });
});
</script>
@endpush

{{-- 2. STATISTIK REAL-TIME (id="statistik") --}}
<section id="statistik" class="stats-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="section-label">{{ \App\Models\Setting::get('stats_label', 'DATA SEKOLAH') }}</div>
            <h2 class="section-title">{{ \App\Models\Setting::get('stats_title', 'Sekolah Kami dalam Angka') }}</h2>
            <p class="text-muted" style="max-width: 600px; margin: 0 auto;">{{ \App\Models\Setting::get('stats_subtitle', 'Statistik real-time dari sistem penilaian kinerja GuruKuu') }}</p>
        </div>
        <div class="row g-4">
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-card-modern">
                    <div class="stat-icon" style="background: rgba(0,51,102,0.08); color: var(--primary);">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                    <div class="stat-number">{{ $totalGuru ?? 0 }}</div>
                    <div class="stat-label">Total Guru</div>
                    <div class="mt-2">
                        <span class="badge rounded-pill" style="background: rgba(0,51,102,0.08); color: var(--primary); font-size: 0.68rem; font-weight: 600;">Data Pendidik</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-card-modern">
                    <div class="stat-icon" style="background: rgba(0,168,107,0.08); color: var(--accent);">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stat-number">{{ $totalSiswa ?? 0 }}</div>
                    <div class="stat-label">Siswa Terdaftar</div>
                    <div class="mt-2">
                        <span class="badge rounded-pill" style="background: rgba(0,168,107,0.1); color: var(--accent); font-size: 0.68rem; font-weight: 600;">Siswa Aktif</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-card-modern">
                    <div class="stat-icon" style="background: rgba(245,158,11,0.12); color: #d97706;">
                        <i class="bi bi-clipboard-check-fill"></i>
                    </div>
                    <div class="stat-number">{{ $totalPenilaian ?? 0 }}</div>
                    <div class="stat-label">Total Penilaian</div>
                    <div class="mt-2">
                        <span class="badge rounded-pill" style="background: rgba(245,158,11,0.12); color: #d97706; font-size: 0.68rem; font-weight: 600;">Ulasan Masuk</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-card-modern">
                    <div class="stat-icon" style="background: rgba(99,102,241,0.1); color: #6366f1;">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                    @if(isset($periodeAktif) && $periodeAktif)
                        <div class="stat-number" style="font-size: 1.5rem; line-height: 1.3;">
                            {{ $periodeAktif->semester === 'ganjil' ? 'Ganjil' : 'Genap' }}
                            <div style="font-size: 1rem; font-weight: 600; margin-top: 0.25rem;">{{ $periodeAktif->tahun_ajaran }}</div>
                        </div>
                    @else
                        <div class="stat-number" style="font-size: 1.5rem;">Belum Aktif</div>
                    @endif
                    <div class="stat-label">Periode Saat Ini</div>
                    <div class="mt-2">
                        <span class="badge rounded-pill" style="background: rgba(99,102,241,0.1); color: #6366f1; font-size: 0.68rem; font-weight: 600;">Semester Aktif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 3. GURU FAVORIT / LEADERBOARD (id="guru") --}}
<section id="guru" class="section-padding" style="background: var(--bg-light);">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="section-label">{{ \App\Models\Setting::get('leaderboard_label', 'PENCAPAIAN TERBAIK') }}</div>
            <h2 class="section-title">{{ \App\Models\Setting::get('leaderboard_title', 'Guru dengan Partisipasi Tertinggi') }}</h2>
            <p class="text-muted">{{ \App\Models\Setting::get('leaderboard_subtitle', 'Guru dengan persentase kepuasan dan partisipasi penilaian tertinggi dari siswa') }}</p>
        </div>

        @php
            $allTeachers = \App\Models\Guru::with('jurusan')->withRatings()
                ->orderByDesc('rata_rata_nilai')->orderByDesc('total_penilaian')->limit(3)->get();
            
            $topList = $allTeachers->map(function($guru) {
                $guru->persentase = round(($guru->rata_rata_nilai / 5) * 100);
                return $guru;
            })->sortByDesc('rata_rata_nilai')->take(3)->values();
        @endphp

        <div class="row g-2 g-md-4 justify-content-center align-items-end mb-4 mb-md-5 gk-podium-row">
            @if($topList->count() > 0)
                {{-- #2 PERAK --}}
                @if($topList->count() > 1)
                <div class="col-4 col-lg-3 order-1 order-md-1 gk-podium-col gk-podium-2" data-aos="fade-right">
                    <div class="card-custom gk-podium-card gk-podium-silver gk-public-podium p-2.5 p-md-4 text-center h-100 shadow-sm" style="border-radius: 16px;">
                        <div class="mb-2 mb-md-3">
                            <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: #64748b; color: #fff; font-size: 0.75rem;">
                                <i class="bi bi-award-fill me-1"></i> #2 PERAK
                            </span>
                        </div>
                        <div class="position-relative d-inline-block mb-2 mb-md-3">
                            <img src="{{ $topList[1]->photo_url }}" class="rounded-circle shadow-sm gk-podium-avatar-2" width="85" height="85" style="object-fit: cover;">
                        </div>
                        <h6 class="fw-bold mb-1 gk-podium-nama" title="{{ $topList[1]->nama }}">{{ $topList[1]->nama }}</h6>
                        <div class="font-mono mb-2 mb-md-3 gk-podium-jurusan" style="font-size: 0.72rem;" title="{{ strtoupper($topList[1]->jurusan?->nama_jurusan ?? 'UMUM') }}">{{ strtoupper($topList[1]->jurusan?->nama_jurusan ?? 'UMUM') }}</div>
                        <div class="p-1.5 p-md-2 rounded-3 mb-2 mb-md-3 gk-podium-statbox">
                            <div class="fw-bold gk-podium-score" style="font-size: 1.5rem; line-height: 1;">
                                {{ $topList[1]->persentase }}%
                                <span class="text-warning fs-6">★</span>
                            </div>
                            <small class="text-muted font-mono gk-podium-reviews" style="font-size: 0.7rem;">{{ $topList[1]->total_penilaian }} ulasan</small>
                        </div>
                        <a href="{{ route('landing.guru.detail', $topList[1]->id) }}" class="btn btn-outline-custom btn-sm btn-podium w-100 rounded-pill">
                            Lihat Profil
                        </a>
                    </div>
                </div>
                @endif

                {{-- #1 EMAS (CENTER PODIUM) DENGAN EFEK ANIMASI RINGAN --}}
                @if($topList->count() > 0)
                <div class="col-4 col-lg-4 order-2 order-md-2 mb-0 gk-podium-col gk-podium-1" data-aos="zoom-in">
                    <div class="card-custom gk-podium-card gk-podium-gold gk-public-podium-gold p-2.5 p-md-5 text-center position-relative shadow" style="border-radius: 18px;">
                        <div class="mb-2 mb-md-3">
                            <span class="badge rounded-pill px-2.5 px-md-3.5 py-1 py-md-1.5 fw-bold shadow-sm" style="background: linear-gradient(135deg, #d97706, #b45309); color: #ffffff; font-size: 0.8rem; letter-spacing: 0.5px;">
                                <i class="bi bi-trophy-fill me-1"></i> #1 EMAS
                            </span>
                        </div>
                        <div class="position-relative d-inline-block mb-2 mb-md-3">
                            <img src="{{ $topList[0]->photo_url }}" class="rounded-circle shadow gk-podium-avatar-1" width="110" height="110" style="object-fit: cover;">
                        </div>
                        <h5 class="fw-bold mb-1 gk-podium-nama" title="{{ $topList[0]->nama }}">{{ $topList[0]->nama }}</h5>
                        <div class="font-mono mb-2 mb-md-3 gk-podium-jurusan" style="font-size: 0.75rem; letter-spacing: 1px;" title="{{ strtoupper($topList[0]->jurusan?->nama_jurusan ?? 'UMUM') }}">{{ strtoupper($topList[0]->jurusan?->nama_jurusan ?? 'UMUM') }}</div>
                        <div class="p-2 p-md-3 rounded-3 mb-2 mb-md-3 gk-podium-statbox">
                            <div class="fw-bold gk-podium-score" style="font-size: 2.2rem; line-height: 1;">
                                {{ $topList[0]->persentase }}%
                                <span class="text-warning fs-6">★ ★ ★ ★ ★</span>
                            </div>
                            <div class="progress mt-1 mt-md-2 mb-1" style="height: 6px; background-color: rgba(217, 119, 6, 0.2); border-radius: 10px;">
                                <div class="progress-bar rounded-pill" style="width: {{ $topList[0]->persentase }}%; background: #d97706;"></div>
                            </div>
                            <small class="text-muted font-mono d-block mt-0.5 mt-md-1 gk-podium-reviews" style="font-size: 0.75rem;">{{ $topList[0]->total_penilaian }} ulasan</small>
                        </div>
                        <a href="{{ route('landing.guru.detail', $topList[0]->id) }}" class="btn btn-primary-custom btn-podium w-100 rounded-pill py-1.5 py-md-2 fw-semibold" style="background: #003366;">
                            Lihat Profil
                        </a>
                    </div>
                </div>
                @endif

                {{-- #3 PERUNGGU --}}
                @if($topList->count() > 2)
                <div class="col-4 col-lg-3 order-3 order-md-3 gk-podium-col gk-podium-3" data-aos="fade-left">
                    <div class="card-custom gk-podium-card gk-podium-bronze gk-public-podium p-2.5 p-md-4 text-center h-100 shadow-sm" style="border-radius: 16px;">
                        <div class="mb-2 mb-md-3">
                            <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: #b45309; color: #fff; font-size: 0.75rem;">
                                <i class="bi bi-award-fill me-1"></i> #3 PERUNGGU
                            </span>
                        </div>
                        <div class="position-relative d-inline-block mb-2 mb-md-3">
                            <img src="{{ $topList[2]->photo_url }}" class="rounded-circle shadow-sm gk-podium-avatar-3" width="85" height="85" style="object-fit: cover;">
                        </div>
                        <h6 class="fw-bold mb-1 gk-podium-nama" title="{{ $topList[2]->nama }}">{{ $topList[2]->nama }}</h6>
                        <div class="font-mono mb-2 mb-md-3 gk-podium-jurusan" style="font-size: 0.72rem;" title="{{ strtoupper($topList[2]->jurusan?->nama_jurusan ?? 'UMUM') }}">{{ strtoupper($topList[2]->jurusan?->nama_jurusan ?? 'UMUM') }}</div>
                        <div class="p-1.5 p-md-2 rounded-3 mb-2 mb-md-3 gk-podium-statbox">
                            <div class="fw-bold gk-podium-score" style="font-size: 1.5rem; line-height: 1;">
                                {{ $topList[2]->persentase }}%
                                <span class="text-warning fs-6">★</span>
                            </div>
                            <small class="text-muted font-mono gk-podium-reviews" style="font-size: 0.7rem;">{{ $topList[2]->total_penilaian }} ulasan</small>
                        </div>
                        <a href="{{ route('landing.guru.detail', $topList[2]->id) }}" class="btn btn-outline-custom btn-sm btn-podium w-100 rounded-pill">
                            Lihat Profil
                        </a>
                    </div>
                </div>
                @endif
            @else
                <div class="col-12 text-center text-muted py-4">Belum ada data evaluasi guru.</div>
            @endif
        </div>

        <div class="text-center" data-aos="zoom-in">
            <a href="{{ route('landing.leaderboard') }}" class="btn btn-cta">
                <i class="bi bi-trophy-fill me-2"></i> Lihat Leaderboard Selengkapnya
            </a>
        </div>
    </div>
</section>

{{-- 4. PANDUAN / CARA PENILAIAN (id="panduan") --}}
<section id="panduan" class="section-padding" style="background: var(--bg-card);">
    <div class="container">
        <div class="text-center mb-4 mb-md-5" data-aos="fade-up">
            <div class="section-label">{{ \App\Models\Setting::get('panduan_label', 'PANDUAN PENGGUNAAN') }}</div>
            <h2 class="section-title">{{ \App\Models\Setting::get('panduan_title', 'Bagaimana Cara Memberi Penilaian?') }}</h2>
            <p class="text-muted mb-0 small">{{ \App\Models\Setting::get('panduan_subtitle', 'Hanya butuh 3 langkah mudah untuk berkontribusi bagi sekolahmu') }}</p>
        </div>
        <div class="row g-2 g-md-4">
            <div class="col-4 col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="tutorial-card p-2 p-sm-3 p-md-4 text-center h-100">
                    <div class="tutorial-number" style="background: var(--primary); color: white; width: clamp(34px, 8vw, 52px); height: clamp(34px, 8vw, 52px); font-size: clamp(0.85rem, 2.2vw, 1.4rem);">1</div>
                    <h5 class="fw-bold mb-1 mb-md-2 mt-2 mt-md-3" style="font-size: clamp(0.78rem, 2.2vw, 1.15rem);">{{ \App\Models\Setting::get('panduan_step1_title', 'Login NIS') }}</h5>
                    <p class="text-muted mb-0 small" style="font-size: clamp(0.65rem, 1.8vw, 0.85rem); line-height: 1.35;">{{ \App\Models\Setting::get('panduan_step1_desc', 'Masuk dengan akun NIS & tanggal lahir resmi terverifikasi.') }}</p>
                </div>
            </div>
            <div class="col-4 col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="tutorial-card p-2 p-sm-3 p-md-4 text-center h-100">
                    <div class="tutorial-number" style="background: var(--accent); color: white; width: clamp(34px, 8vw, 52px); height: clamp(34px, 8vw, 52px); font-size: clamp(0.85rem, 2.2vw, 1.4rem);">2</div>
                    <h5 class="fw-bold mb-1 mb-md-2 mt-2 mt-md-3" style="font-size: clamp(0.78rem, 2.2vw, 1.15rem);">{{ \App\Models\Setting::get('panduan_step2_title', 'Beri Nilai') }}</h5>
                    <p class="text-muted mb-0 small" style="font-size: clamp(0.65rem, 1.8vw, 0.85rem); line-height: 1.35;">{{ \App\Models\Setting::get('panduan_step2_desc', 'Pilih guru Normada/Produktif, beri nilai (1-5) pada 5 kriteria.') }}</p>
                </div>
            </div>
            <div class="col-4 col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="tutorial-card p-2 p-sm-3 p-md-4 text-center h-100">
                    <div class="tutorial-number" style="background: var(--secondary); color: var(--text-dark); width: clamp(34px, 8vw, 52px); height: clamp(34px, 8vw, 52px); font-size: clamp(0.85rem, 2.2vw, 1.4rem);">3</div>
                    <h5 class="fw-bold mb-1 mb-md-2 mt-2 mt-md-3" style="font-size: clamp(0.78rem, 2.2vw, 1.15rem);">{{ \App\Models\Setting::get('panduan_step3_title', 'Kirim Anonim') }}</h5>
                    <p class="text-muted mb-0 small" style="font-size: clamp(0.65rem, 1.8vw, 0.85rem); line-height: 1.35;">{{ \App\Models\Setting::get('panduan_step3_desc', 'Data tersimpan aman & anonim untuk perbaikan pengajaran.') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 5. TENTANG KAMI - VISI MISI (id="tentang") --}}
<section id="tentang" class="section-padding" style="background: var(--bg-light);">
    <div class="container">
        <div class="text-center mb-4 mb-md-5" data-aos="fade-up">
            <div class="section-label">{{ \App\Models\Setting::get('about_label', 'TENTANG KAMI') }}</div>
            <h2 class="section-title">{{ \App\Models\Setting::get('about_title', 'Mengapa GuruKuu Ada?') }}</h2>
            <p class="text-muted" style="max-width: 650px; margin: 0 auto; font-size: 0.92rem; text-wrap: balance;">
                {{ \App\Models\Setting::get('about_subtitle', 'Platform evaluasi terintegrasi untuk SMK yang membangun jembatan komunikasi positif antara siswa, guru, dan manajemen sekolah.') }}
            </p>
        </div>

        <div class="row g-2 g-md-4 mb-3 mb-md-4">
            <div class="col-6 col-md-6" data-aos="fade-right">
                <div class="card-custom gk-vm-card p-2.5 p-sm-3 p-md-4 h-100" style="border: 1px solid var(--border);">
                    <div class="d-flex align-items-center mb-2 mb-md-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-2 me-md-3 gk-vm-icon" style="width: clamp(30px, 7vw, 40px); height: clamp(30px, 7vw, 40px); background: var(--primary); color: white; flex-shrink: 0; font-size: clamp(0.85rem, 2vw, 1.1rem);">
                            <i class="bi bi-bullseye"></i>
                        </div>
                        <h5 class="fw-bold mb-0" style="font-size: clamp(0.85rem, 2.4vw, 1.25rem);">Visi Kami</h5>
                    </div>
                    <p class="text-muted mb-0" style="line-height: 1.5; font-size: clamp(0.72rem, 1.9vw, 0.88rem);">
                        {{ \App\Models\Setting::get('visi_text', 'Menjadi standar nasional dalam evaluasi pengajaran berbasis data untuk menciptakan ekosistem pendidikan yang responsif, transparan, dan berkelanjutan di seluruh SMK Indonesia.') }}
                    </p>
                </div>
            </div>
            <div class="col-6 col-md-6" data-aos="fade-left">
                <div class="card-custom gk-vm-card p-2.5 p-sm-3 p-md-4 h-100" style="border: 1px solid var(--border);">
                    <div class="d-flex align-items-center mb-2 mb-md-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-2 me-md-3 gk-vm-icon" style="width: clamp(30px, 7vw, 40px); height: clamp(30px, 7vw, 40px); background: var(--accent); color: white; flex-shrink: 0; font-size: clamp(0.85rem, 2vw, 1.1rem);">
                            <i class="bi bi-rocket-takeoff"></i>
                        </div>
                        <h5 class="fw-bold mb-0" style="font-size: clamp(0.85rem, 2.4vw, 1.25rem);">Misi Kami</h5>
                    </div>
                    @php
                        $misiRaw = \App\Models\Setting::get('misi_text', "Memberikan saluran aspirasi yang aman dan anonim bagi siswa.\nMenyediakan data analitik yang dapat ditindaklanjuti oleh manajemen sekolah.\nMendorong pengembangan profesional guru secara berkelanjutan.");
                        $misiList = array_filter(array_map('trim', explode("\n", $misiRaw)));
                    @endphp
                    <ul class="text-muted mb-0 ps-2.5 ps-md-3" style="line-height: 1.5; font-size: clamp(0.72rem, 1.9vw, 0.88rem);">
                        @foreach($misiList as $misiItem)
                            <li>{{ ltrim($misiItem, '-*• ') }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="row g-2 g-md-4">
            <div class="col-4 col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card-custom gk-feature-card p-2 p-sm-3 p-md-4 text-center h-100">
                    <i class="bi bi-shield-check mb-1 mb-md-2" style="color: var(--primary); font-size: clamp(1.3rem, 3.5vw, 2rem);"></i>
                    <h5 class="fw-bold mb-1" style="font-size: clamp(0.75rem, 2.2vw, 1.1rem);">{{ \App\Models\Setting::get('feature1_title', 'Anonimitas') }}</h5>
                    <p class="text-muted mb-0 small" style="font-size: clamp(0.65rem, 1.8vw, 0.85rem); line-height: 1.35;">{{ \App\Models\Setting::get('feature1_desc', 'Identitas siswa aman dengan enkripsi tanpa tekanan.') }}</p>
                </div>
            </div>
            <div class="col-4 col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card-custom gk-feature-card p-2 p-sm-3 p-md-4 text-center h-100">
                    <i class="bi bi-graph-up-arrow mb-1 mb-md-2" style="color: var(--accent); font-size: clamp(1.3rem, 3.5vw, 2rem);"></i>
                    <h5 class="fw-bold mb-1" style="font-size: clamp(0.75rem, 2.2vw, 1.1rem);">{{ \App\Models\Setting::get('feature2_title', 'Berbasis Data') }}</h5>
                    <p class="text-muted mb-0 small" style="font-size: clamp(0.65rem, 1.8vw, 0.85rem); line-height: 1.35;">{{ \App\Models\Setting::get('feature2_desc', 'Data statistik valid & terukur untuk setiap apresiasi.') }}</p>
                </div>
            </div>
            <div class="col-4 col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card-custom gk-feature-card p-2 p-sm-3 p-md-4 text-center h-100">
                    <i class="bi bi-people-fill mb-1 mb-md-2" style="color: var(--secondary); font-size: clamp(1.3rem, 3.5vw, 2rem);"></i>
                    <h5 class="fw-bold mb-1" style="font-size: clamp(0.75rem, 2.2vw, 1.1rem);">{{ \App\Models\Setting::get('feature3_title', 'Kolaboratif') }}</h5>
                    <p class="text-muted mb-0 small" style="font-size: clamp(0.65rem, 1.8vw, 0.85rem); line-height: 1.35;">{{ \App\Models\Setting::get('feature3_desc', 'Membangun komunikasi positif siswa, guru, & sekolah.') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 6. PERTANYAAN UMUM / FAQ (id="faq") --}}
@php
    $publicFaqs = \App\Services\FaqService::getForRole('publik');
@endphp
@if(count($publicFaqs) > 0)
<section id="faq" class="section-padding" style="background: var(--bg-card); border-top: 1px solid var(--border);">
    <div class="container">
        <div class="text-center mb-4 mb-md-5" data-aos="fade-up">
            <div class="section-label">FAQ & BANTUAN</div>
            <h2 class="section-title">Pertanyaan yang Sering Diajukan</h2>
            <p class="text-muted mb-0 small" style="max-width: 600px; margin: 0 auto;">Jawaban ringkas seputar platform evaluasi {{ $siteTitle ?? 'GuruKuu' }} bagi publik, siswa, dan guru.</p>
        </div>
        <div class="row justify-content-center" data-aos="fade-up" data-aos-delay="100">
            <div class="col-lg-9 col-xl-8">
                <div class="accordion accordion-flush" id="landingFaqAccordion">
                    @foreach($publicFaqs as $index => $faq)
                        <div class="accordion-item mb-2.5 border rounded-3 overflow-hidden shadow-none" style="border-color: var(--border) !important;">
                            <h2 class="accordion-header" id="headingLandingFaq{{ $index }}">
                                <button class="accordion-button collapsed fw-bold text-dark py-3 px-3.5 bg-light-subtle" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLandingFaq{{ $index }}" aria-expanded="false" aria-controls="collapseLandingFaq{{ $index }}" style="font-size: 0.95rem;">
                                    <i class="bi {{ $faq['icon'] ?? 'bi-question-circle' }} text-primary me-2.5 fs-5"></i>
                                    {{ $faq['q'] }}
                                </button>
                            </h2>
                            <div id="collapseLandingFaq{{ $index }}" class="accordion-collapse collapse" aria-labelledby="headingLandingFaq{{ $index }}">
                                <div class="accordion-body text-secondary lh-base p-3.5 bg-white border-top small">
                                    {{ $faq['a'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif
@endsection