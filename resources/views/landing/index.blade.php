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

/* 🌟 HERO ENTRANCE TRANSITIONS */
@keyframes heroBadgeEntrance {
    0% { opacity: 0; transform: translateY(-30px) scale(0.9); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}
@keyframes heroTextSlideUp {
    0% { opacity: 0; transform: translateY(35px); }
    100% { opacity: 1; transform: translateY(0); }
}
.hero-anim-badge {
    animation: heroBadgeEntrance 0.85s cubic-bezier(0.16, 1, 0.3, 1) 0.15s both;
}
.hero-anim-title {
    animation: heroTextSlideUp 0.9s cubic-bezier(0.16, 1, 0.3, 1) 0.3s both;
}
.hero-anim-subtitle {
    animation: heroTextSlideUp 0.9s cubic-bezier(0.16, 1, 0.3, 1) 0.45s both;
}
.hero-anim-cta {
    animation: heroTextSlideUp 0.9s cubic-bezier(0.16, 1, 0.3, 1) 0.6s both;
}

/* 🌟 STAT CARDS SCROLL ENTRANCE */
.stat-card-modern {
    opacity: 0;
    transform: translateY(35px);
    transition: opacity 0.75s cubic-bezier(0.16, 1, 0.3, 1), transform 0.75s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.35s ease;
}
.stat-card-modern.is-visible {
    opacity: 1;
    transform: translateY(0);
}
.stat-card-modern:hover {
    transform: translateY(-6px);
}

/* 🌟 SEQUENTIAL LEADERBOARD PODIUM ENTRANCE (#1 DULU, LALU #2, LALU #3) */
.podium-anim-item {
    opacity: 0;
    transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.85s cubic-bezier(0.34, 1.3, 0.64, 1);
    will-change: transform, opacity;
}
.podium-anim-item.podium-rank-1 {
    transform: scale(0.82) translateY(55px);
}
.podium-anim-item.podium-rank-2 {
    transform: translateX(-55px) translateY(35px);
}
.podium-anim-item.podium-rank-3 {
    transform: translateX(55px) translateY(35px);
}
@media (min-width: 768px) {
    .podium-anim-item.podium-rank-1.is-revealed,
    .podium-anim-item.podium-rank-2.is-revealed,
    .podium-anim-item.podium-rank-3.is-revealed {
        opacity: 1 !important;
        transform: none !important;
    }
    .podium-anim-item.podium-rank-1 {
        z-index: 10;
    }
    .podium-anim-item.podium-rank-2 {
        z-index: 5;
    }
    .podium-anim-item.podium-rank-3 {
        z-index: 2;
    }

    .podium-rank-1 .gk-podium-card-revised {
        min-height: 480px !important;
        box-shadow: 0 20px 45px -8px rgba(15, 23, 42, 0.16), 0 8px 18px -4px rgba(15, 23, 42, 0.08) !important;
        border: none !important;
    }
    .podium-rank-2 .gk-podium-card-revised {
        min-height: 440px !important;
        box-shadow: 0 14px 32px -6px rgba(15, 23, 42, 0.11), 0 6px 14px -3px rgba(15, 23, 42, 0.05) !important;
        border: none !important;
    }
    .podium-rank-3 .gk-podium-card-revised {
        min-height: 410px !important;
        box-shadow: 0 10px 25px -4px rgba(15, 23, 42, 0.08), 0 4px 10px -2px rgba(15, 23, 42, 0.04) !important;
        border: none !important;
    }

    .podium-rank-1:hover .gk-podium-card-revised {
        transform: translateY(-6px);
        box-shadow: 0 25px 50px -8px rgba(15, 23, 42, 0.2) !important;
    }
    .podium-rank-2:hover .gk-podium-card-revised {
        transform: translateY(-5px);
        box-shadow: 0 18px 38px -6px rgba(15, 23, 42, 0.15) !important;
    }
    .podium-rank-3:hover .gk-podium-card-revised {
        transform: translateY(-5px);
        box-shadow: 0 16px 32px -6px rgba(15, 23, 42, 0.12) !important;
    }
}
@media (max-width: 767.98px) {
    .podium-anim-item.is-revealed {
        opacity: 1 !important;
        transform: none !important;
    }
}
.gk-progress-pill-fill {
    transition: width 1.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
}

/* 🌟 GENERAL CARDS SCROLL REVEAL */
.gk-scroll-reveal {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.75s cubic-bezier(0.16, 1, 0.3, 1), transform 0.75s cubic-bezier(0.16, 1, 0.3, 1);
}
.gk-scroll-reveal.is-revealed {
    opacity: 1;
    transform: translateY(0);
}

/* Smooth Public Dashboard Interactive Transitions & Refined Aesthetics */
.teacher-card, .tutorial-card, .gk-vm-card, .gk-feature-card, .card-custom, .gk-clean-card {
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.35s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.3s ease;
}

/* REFINED PODIUM CARDS & SHADOWS (BACK SHADOW JELAS & BORDER BERSIH TANPA GLOW) */
.gk-clean-card {
    background: #ffffff;
    border-radius: 22px;
    border: 1px solid rgba(15, 23, 42, 0.08) !important;
    box-shadow: 0 10px 25px -4px rgba(15, 23, 42, 0.08), 0 4px 10px -2px rgba(15, 23, 42, 0.04) !important;
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.35s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.3s ease;
}
.gk-clean-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 35px -6px rgba(15, 23, 42, 0.12), 0 8px 16px -3px rgba(15, 23, 42, 0.06) !important;
    border-color: rgba(15, 23, 42, 0.14) !important;
}
.gk-podium-card-revised {
    background: #ffffff;
    border-radius: 22px;
    border: none !important;
    box-shadow: 0 10px 25px -4px rgba(15, 23, 42, 0.08), 0 4px 10px -2px rgba(15, 23, 42, 0.04) !important;
    position: relative;
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
.gk-podium-card-revised:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 35px -6px rgba(15, 23, 42, 0.12), 0 8px 16px -3px rgba(15, 23, 42, 0.06) !important;
    border: none !important;
}
.gk-podium-card-revised.is-first {
    border-radius: 26px;
    border: none !important;
    box-shadow: 0 16px 36px -6px rgba(15, 23, 42, 0.11), 0 6px 14px -3px rgba(15, 23, 42, 0.05) !important;
    z-index: 2;
}
.gk-avatar-red-wrap,
.gk-avatar-clean-wrap {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    border-radius: 50% !important;
    background: transparent !important;
    padding: 0 !important;
    box-shadow: 0 8px 20px -3px rgba(15, 23, 42, 0.12) !important;
    border: none !important;
}
.gk-avatar-red-wrap img,
.gk-avatar-clean-wrap img {
    border-radius: 50% !important;
    object-fit: cover !important;
    display: block !important;
    border: 3px solid #ffffff !important;
    box-shadow: 0 0 0 1px rgba(15, 23, 42, 0.08) !important;
}
.gk-badge-mini-icon {
    width: 14px !important;
    height: 14px !important;
    min-width: 14px !important;
    border-radius: 3px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 0.55rem !important;
    line-height: 1 !important;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04) !important;
    border: 0.8px solid rgba(0,0,0,0.08) !important;
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease !important;
    cursor: default;
    padding: 0 !important;
}
.gk-badge-mini-icon:hover {
    transform: translateY(-1px) scale(1.4) !important;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15) !important;
    z-index: 5 !important;
}
.gk-step-roman {
    width: 58px;
    height: 58px;
    border-radius: 50%;
    background: #e2e8f0;
    color: #1e293b;
    font-weight: 800;
    font-size: 1.35rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.25rem;
}

/* PANDUAN SLIDER / CAROUSEL */
.gk-panduan-slider-wrapper {
    position: relative;
    max-width: 580px;
    width: 100%;
}
.gk-panduan-viewport {
    overflow: hidden;
    position: relative;
    padding: 14px 6px;
    margin: -14px -6px;
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
    padding: 0 8px;
    box-sizing: border-box;
}
.gk-panduan-slide .gk-clean-card {
    transition: transform 0.65s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.65s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.4s ease;
    transform: scale(0.94);
    opacity: 0.35;
    pointer-events: none;
}
.gk-panduan-slide.is-active .gk-clean-card {
    transform: scale(1);
    opacity: 1;
    pointer-events: auto;
    box-shadow: 0 16px 36px -8px rgba(15, 23, 42, 0.12), 0 6px 16px -4px rgba(15, 23, 42, 0.05) !important;
}
.gk-panduan-nav-btn {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #ffffff;
    border: 1.5px solid rgba(15, 23, 42, 0.1);
    color: #1e293b;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.07);
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    outline: none;
}
.gk-panduan-nav-btn:hover {
    background: #003366;
    border-color: #003366;
    color: #ffffff;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 51, 102, 0.25);
}
.gk-panduan-nav-btn:active {
    transform: translateY(0) scale(0.94);
}
.gk-panduan-dot {
    width: 10px;
    height: 10px;
    border-radius: 20px;
    background: #cbd5e1;
    border: none;
    padding: 0;
    cursor: pointer;
    transition: all 0.35s cubic-bezier(0.22, 1, 0.36, 1);
    outline: none;
}
.gk-panduan-dot:hover {
    background: #94a3b8;
}
.gk-panduan-dot.active {
    width: 28px;
    background: #003366;
    border-radius: 20px;
}
.gk-pill-btn-dark {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #000000;
    color: #ffffff !important;
    font-weight: 700;
    font-size: 0.95rem;
    padding: 0.75rem 2.5rem;
    border-radius: 50px;
    text-decoration: none;
    transition: all 0.25s ease;
    box-shadow: 0 8px 20px -4px rgba(0,0,0,0.35);
}
.gk-pill-btn-dark:hover {
    background: #1e293b;
    transform: translateY(-2px);
    box-shadow: 0 12px 25px -4px rgba(0,0,0,0.45);
}
.gk-progress-pill {
    background: #0f172a !important;
    border-radius: 50px;
    color: #ffffff !important;
    font-weight: 800;
    font-size: 0.85rem;
    padding: 0.35rem 1rem;
    display: inline-block;
    min-width: 140px;
    text-align: center;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.18);
}
.gk-progress-pill-fill {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    border-radius: 50px;
    transition: width 1.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.gk-pill-rank-1,
.gk-pill-rank-2,
.gk-pill-rank-3 {
    background: #0f172a !important;
    border: 1px solid rgba(59, 130, 246, 0.4) !important;
}
.gk-progress-pill-fill {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    border-radius: 50px;
    background: linear-gradient(90deg, #0284c7, #38bdf8) !important;
    transition: width 1.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.gk-bar-blue-high {
    background: linear-gradient(90deg, #003366, #2563eb) !important;
}
.gk-bar-blue-mid {
    background: linear-gradient(90deg, #0284c7, #38bdf8) !important;
}
.gk-bar-blue-low {
    background: linear-gradient(90deg, #38bdf8, #93c5fd) !important;
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
<section id="home" class="p-0 position-relative overflow-hidden" style="min-height: 600px; touch-action: pan-y;">
    {{-- Floating Ambient Glow Orbs --}}
    <div class="hero-ambient-orb" style="top: 15%; right: 12%; width: 280px; height: 280px; background: radial-gradient(circle, rgba(0, 168, 107, 0.45), transparent 70%);"></div>
    <div class="hero-ambient-orb" style="bottom: 12%; left: 8%; width: 340px; height: 340px; background: radial-gradient(circle, rgba(255, 193, 7, 0.38), transparent 70%); animation-delay: -4s;"></div>

    {{-- Background Carousel Slideshow --}}
    <div id="heroBgSlider" class="carousel slide carousel-fade position-absolute w-100 h-100" data-bs-ride="carousel" data-bs-interval="4500" style="top: 0; left: 0; z-index: 1; touch-action: pan-y;">
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
            <div class="col-lg-9 col-xl-8">
                <div class="hero-glass-badge hero-anim-badge">
                    <i class="bi bi-patch-check-fill text-warning"></i>
                    <span>{{ $heroBadge }}</span>
                </div>
                <h1 class="hero-title text-white hero-anim-title">{{ $heroTitle }}</h1>
                <p class="hero-subtitle text-white hero-anim-subtitle" style="color: rgba(255, 255, 255, 0.92) !important;">{{ $heroSubtitle }}</p>
                <div class="d-flex flex-wrap gap-3 align-items-center hero-anim-cta">
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

    {{-- Garis Indikator Carousel (Bisa Dipencet Pindah Foto) --}}
    <div class="carousel-indicators mb-4" style="z-index: 10; margin-bottom: 2rem;">
        @foreach($heroImages as $idx => $img)
            <button type="button" data-bs-target="#heroBgSlider" data-bs-slide-to="{{ $idx }}" class="{{ $idx === 0 ? 'active' : '' }}" aria-current="{{ $idx === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $idx + 1 }}" style="cursor: pointer;"></button>
        @endforeach
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

        // Click listener pada garis indikator
        document.querySelectorAll('[data-bs-target="#heroBgSlider"][data-bs-slide-to]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const slideIndex = parseInt(this.getAttribute('data-bs-slide-to'), 10);
                if (!isNaN(slideIndex)) {
                    carousel.to(slideIndex);
                }
            });
        });

        heroSlider.addEventListener('slid.bs.carousel', function(e) {
            updateHeroArrows(e.to);
            document.querySelectorAll('[data-bs-target="#heroBgSlider"][data-bs-slide-to]').forEach((btn, idx) => {
                if (idx === e.to) {
                    btn.classList.add('active');
                    btn.setAttribute('aria-current', 'true');
                } else {
                    btn.classList.remove('active');
                    btn.removeAttribute('aria-current');
                }
            });
        });

        updateHeroArrows(0);

        // 🌟 GESTURE GESER DENGAN JARI (TOUCH MOBILE) & MOUSE DRAG (DESKTOP)
        const heroSection = document.getElementById('home');
        if (heroSection) {
            let touchStartX = 0;
            let touchStartY = 0;
            let isMouseDown = false;
            let mouseStartX = 0;
            let mouseStartY = 0;
            const threshold = 40; // minimal geser 40px

            // Touch events untuk Mobile (swipe dengan jari)
            heroSection.addEventListener('touchstart', function(e) {
                if (e.touches && e.touches.length === 1) {
                    touchStartX = e.touches[0].clientX;
                    touchStartY = e.touches[0].clientY;
                }
            }, { passive: true });

            heroSection.addEventListener('touchend', function(e) {
                if (e.changedTouches && e.changedTouches.length === 1) {
                    const diffX = e.changedTouches[0].clientX - touchStartX;
                    const diffY = e.changedTouches[0].clientY - touchStartY;

                    // Deteksi geser horizontal dominan (bukan scroll vertikal)
                    if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > threshold) {
                        if (diffX < 0) {
                            carousel.next(); // Geser jari ke kiri -> Slide berikutnya
                        } else {
                            carousel.prev(); // Geser jari ke kanan -> Slide sebelumnya
                        }
                    }
                }
            }, { passive: true });

            // Mouse drag untuk Desktop
            heroSection.addEventListener('mousedown', function(e) {
                if (e.target.closest('a, button, input, textarea')) return;
                isMouseDown = true;
                mouseStartX = e.clientX;
                mouseStartY = e.clientY;
            });

            window.addEventListener('mouseup', function(e) {
                if (!isMouseDown) return;
                isMouseDown = false;
                const diffX = e.clientX - mouseStartX;
                const diffY = e.clientY - mouseStartY;

                if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > threshold) {
                    if (diffX < 0) {
                        carousel.next();
                    } else {
                        carousel.prev();
                    }
                }
            });
        }
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

    // 🌟 1. STATISTIK SCROLL ANIMATION & COUNT-UP COUNTER (0 KE DATA ASLI)
    const statSection = document.getElementById('statistik');
    const statCards = document.querySelectorAll('.stat-card-modern');
    const counters = document.querySelectorAll('.stat-counter');
    let statsAnimated = false;

    function runCounters() {
        if (statsAnimated) return;
        statsAnimated = true;

        statCards.forEach((card, idx) => {
            setTimeout(() => {
                card.classList.add('is-visible');
            }, idx * 120);
        });

        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'), 10) || 0;
            if (target === 0) {
                counter.textContent = '0';
                return;
            }
            const duration = 1600;
            const startTime = performance.now();

            function updateCount(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                // Smooth ease-out cubic curve
                const easeOut = 1 - Math.pow(1 - progress, 3);
                const current = Math.floor(easeOut * target);
                counter.textContent = current.toLocaleString('id-ID');

                if (progress < 1) {
                    requestAnimationFrame(updateCount);
                } else {
                    counter.textContent = target.toLocaleString('id-ID');
                }
            }
            requestAnimationFrame(updateCount);
        });
    }

    if (statSection && 'IntersectionObserver' in window) {
        const statObserver = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) {
                runCounters();
                statObserver.disconnect();
            }
        }, { threshold: 0.2 });
        statObserver.observe(statSection);
    } else {
        runCounters();
    }

    // 🌟 2. LEADERBOARD PODIUM BERURUTAN (#1 TOP 1 DULU, LALU #2, LALU #3)
    const podiumRow = document.getElementById('leaderboardPodium');
    let podiumAnimated = false;

    function runPodiumAnimation() {
        if (podiumAnimated || !podiumRow) return;
        podiumAnimated = true;

        const rank1 = podiumRow.querySelector('.podium-rank-1');
        const rank2 = podiumRow.querySelector('.podium-rank-2');
        const rank3 = podiumRow.querySelector('.podium-rank-3');

        // Urutan 1: Top 1 (Pemenang Pertama di tengah) muncul pertama
        setTimeout(() => {
            if (rank1) {
                rank1.classList.add('is-revealed');
                const fill1 = rank1.querySelector('.gk-progress-pill-fill');
                if (fill1) fill1.style.width = fill1.getAttribute('data-percentage') + '%';
            }
        }, 120);

        // Urutan 2: Top 2 (Perak di kiri) muncul berikutnya
        setTimeout(() => {
            if (rank2) {
                rank2.classList.add('is-revealed');
                const fill2 = rank2.querySelector('.gk-progress-pill-fill');
                if (fill2) fill2.style.width = fill2.getAttribute('data-percentage') + '%';
            }
        }, 600);

        // Urutan 3: Top 3 (Perunggu di kanan) muncul terakhir
        setTimeout(() => {
            if (rank3) {
                rank3.classList.add('is-revealed');
                const fill3 = rank3.querySelector('.gk-progress-pill-fill');
                if (fill3) fill3.style.width = fill3.getAttribute('data-percentage') + '%';
            }
        }, 1080);
    }

    if (podiumRow && 'IntersectionObserver' in window) {
        const podiumObserver = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) {
                runPodiumAnimation();
                podiumObserver.disconnect();
            }
        }, { threshold: 0.15 });
        podiumObserver.observe(podiumRow);
    } else if (podiumRow) {
        runPodiumAnimation();
    }

    // 🌟 3. SMOOTH SCROLL REVEAL PADA ELEMEN LAINNYA DI BERANDA
    const revealTargets = document.querySelectorAll('.tutorial-card, .gk-clean-card, .gk-feature-card, .teacher-card, .accordion-item');
    if ('IntersectionObserver' in window) {
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-revealed');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        revealTargets.forEach(el => {
            el.classList.add('gk-scroll-reveal');
            revealObserver.observe(el);
        });
    }
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
                    <div class="stat-number stat-counter" data-target="{{ $totalGuru ?? 0 }}">0</div>
                    <div class="stat-label">Total Guru</div>
                    <div class="mt-2">
                        <span class="badge rounded-pill" style="background: rgba(0,51,102,0.08); color: var(--primary); font-size: 0.68rem; font-weight: 600;">Data Pendidik</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card-modern">
                    <div class="stat-icon" style="background: rgba(2, 132, 199, 0.1); color: #0284c7;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stat-number stat-counter" data-target="{{ $totalSiswa ?? 0 }}">0</div>
                    <div class="stat-label">Siswa Terdaftar</div>
                    <div class="mt-2">
                        <span class="badge rounded-pill" style="background: rgba(2, 132, 199, 0.1); color: #0284c7; font-size: 0.68rem; font-weight: 600;">Siswa Aktif</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card-modern">
                    <div class="stat-icon" style="background: rgba(245, 158, 11, 0.12); color: #d97706;">
                        <i class="bi bi-clipboard-check-fill"></i>
                    </div>
                    <div class="stat-number stat-counter" data-target="{{ $totalPenilaian ?? 0 }}">0</div>
                    <div class="stat-label">Total Penilaian</div>
                    <div class="mt-2">
                        <span class="badge rounded-pill" style="background: rgba(245, 158, 11, 0.12); color: #d97706; font-size: 0.68rem; font-weight: 600;">Ulasan Masuk</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-card-modern">
                    <div class="stat-icon" style="background: rgba(29, 78, 216, 0.1); color: #1d4ed8;">
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
                        <span class="badge rounded-pill" style="background: rgba(29, 78, 216, 0.1); color: #1d4ed8; font-size: 0.68rem; font-weight: 600;">Semester Aktif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 3. GURU FAVORIT / LEADERBOARD (id="guru") --}}
<section id="guru" class="section-padding" style="background: var(--bg-light, #f8fafc); padding: 5rem 0;">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="small fw-bold text-uppercase text-muted mb-1" style="letter-spacing: 1.5px; font-size: 0.78rem;">{{ \App\Models\Setting::get('leaderboard_label', 'PENCAPAIAN TERBAIK') }}</div>
            <h2 class="fw-bold mb-2 text-dark" style="font-size: clamp(1.6rem, 3.5vw, 2.2rem);">{{ \App\Models\Setting::get('leaderboard_title', 'Guru dengan Partisipasi Tertinggi') }}</h2>
            <p class="text-muted mb-0" style="font-size: 0.95rem;">{{ \App\Models\Setting::get('leaderboard_subtitle', 'Data Guru dengan persentase nilai terbaik dari siswa') }}</p>
        </div>

        @php
            $list = $leaderboard ?? \App\Models\Guru::leaderboardFor('rating');
            $eligibleList = $list->filter(fn($g) => !empty($g->leaderboard_rank))->values();
            $top1 = $eligibleList->get(0);
            $top2 = $eligibleList->get(1);
            $top3 = $eligibleList->get(2);
        @endphp

        @if(!$top1)
            <div class="row justify-content-center mb-5" data-aos="fade-up">
                <div class="col-12 col-md-10 col-lg-8">
                    <div class="card-custom p-4 text-center border shadow-xs" style="background: rgba(0, 51, 102, 0.03); border-color: rgba(0, 51, 102, 0.1) !important; border-radius: 14px;">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 42px; height: 42px; background: rgba(0, 51, 102, 0.08); color: #003366;">
                            <i class="bi bi-info-circle fs-5"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Belum Ada Guru di Ranking Podium</h6>
                        <p class="text-muted small mb-0" style="max-width: 580px; margin: 0 auto; line-height: 1.5;">
                            Sesuai ketentuan, guru harus memiliki <strong>minimal 5 penilaian</strong> dari siswa untuk dapat masuk dalam peringkat leaderboard. Seluruh nilai guru tetap tercatat dan dapat dilihat pada leaderboard lengkap.
                        </p>
                    </div>
                </div>
            </div>
        @else
            @if(!$top2)
                {{-- HANYA 1 GURU TOP --}}
                @php $pct1 = round(($top1->rata_rata_nilai / 5) * 100); @endphp
                <div class="row justify-content-center mb-5" data-aos="zoom-in">
                    <div class="col-12 col-sm-10 col-md-8 col-lg-5">
                        <div class="gk-podium-card-revised is-first p-4 p-md-5 text-center d-flex flex-column justify-content-between shadow position-relative">
                            <div>
                                <div class="mb-1">
                                    <span class="badge rounded-pill px-4 py-1.5 fw-bold" style="background: #fef08a; color: #854d0e; font-size: 0.9rem;">
                                        #1st
                                    </span>
                                </div>
                                <div class="gk-podium-badges-row">
                                    @foreach(($top1->penghargaan ?? collect()) as $penghargaan)
                                        @if($penghargaan->badge)
                                            <span class="gk-badge-mini-icon" style="background: {{ $penghargaan->badge->warna }}18; color: {{ $penghargaan->badge->warna }}; border-color: {{ $penghargaan->badge->warna }}33;" data-bs-toggle="tooltip" title="{{ $penghargaan->badge->nama_badge }}: {{ $penghargaan->badge->deskripsi }}">
                                                @if(str_starts_with($penghargaan->badge->icon, 'bi-'))<i class="bi {{ $penghargaan->badge->icon }}"></i>@else{{ $penghargaan->badge->icon }}@endif
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="gk-avatar-clean-wrap mb-3" style="width: 114px; height: 114px;">
                                    <img src="{{ $top1->photo_url }}" width="114" height="114" alt="{{ $top1->nama }}">
                                </div>
                                <h4 class="fw-bold mb-1 text-dark" style="font-size: 1.25rem;" title="{{ $top1->nama }}">{{ $top1->nama }}</h4>
                                <div class="text-muted small mb-2">{{ strtoupper($top1->jurusan?->nama_jurusan ?? $top1->kategori_label ?? 'UMUM') }}</div>
                                
                                <div class="text-warning mb-2" style="font-size: 1.1rem; letter-spacing: 2.5px;">
                                    @php
                                        $val1 = $pct1 / 20;
                                        $stars1 = round($val1 * 2) / 2;
                                    @endphp
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

                                <div class="mb-2">
                                    <div class="gk-progress-pill gk-pill-rank-1 mx-auto" style="width: 145px; height: 24px; font-size: 0.8rem;">
                                        <div class="gk-progress-pill-fill {{ $pct1 >= 75 ? 'gk-bar-blue-high' : ($pct1 >= 50 ? 'gk-bar-blue-mid' : 'gk-bar-blue-low') }}" style="width: {{ $pct1 }}%;"></div>
                                        <span class="position-relative text-white" style="z-index: 2; text-shadow: 0 1px 2px rgba(0,0,0,0.6);">{{ $pct1 }}%</span>
                                    </div>
                                </div>
                                <small class="text-muted font-mono d-block mb-3" style="font-size: 0.8rem;">{{ $top1->total_penilaian }} ulasan</small>
                            </div>

                            <a href="{{ route('landing.guru.detail', $top1->id) }}" class="btn btn-sm btn-outline-primary gk-btn-podium-profile mb-2">
                                <i class="bi bi-eye me-1"></i> Profil
                            </a>
                        </div>
                    </div>
                </div>
            @elseif(!$top3)
                {{-- HANYA 2 GURU TOP --}}
                @php
                    $pct1 = round(($top1->rata_rata_nilai / 5) * 100);
                    $pct2 = round(($top2->rata_rata_nilai / 5) * 100);
                @endphp
                <div class="row g-3 g-md-4 justify-content-center align-items-end mb-5">
                    {{-- #2 PERAK --}}
                    <div class="col-6 col-md-5" data-aos="fade-right">
                        <div class="gk-podium-card-revised p-3.5 p-md-4 text-center h-100 d-flex flex-column justify-content-between shadow-sm">
                            <div>
                                <div class="mb-1">
                                    <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #f1f5f9; color: #475569; font-size: 0.8rem;">
                                        #2nd
                                    </span>
                                </div>
                                <div class="gk-podium-badges-row">
                                    @foreach(($top2->penghargaan ?? collect()) as $penghargaan)
                                        @if($penghargaan->badge)
                                            <span class="gk-badge-mini-icon" style="background: {{ $penghargaan->badge->warna }}18; color: {{ $penghargaan->badge->warna }}; border-color: {{ $penghargaan->badge->warna }}33;" data-bs-toggle="tooltip" title="{{ $penghargaan->badge->nama_badge }}: {{ $penghargaan->badge->deskripsi }}">
                                                @if(str_starts_with($penghargaan->badge->icon, 'bi-'))<i class="bi {{ $penghargaan->badge->icon }}"></i>@else{{ $penghargaan->badge->icon }}@endif
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="gk-avatar-clean-wrap mb-3" style="width: 96px; height: 96px;">
                                    <img src="{{ $top2->photo_url }}" width="96" height="96" alt="{{ $top2->nama }}">
                                </div>
                                <h5 class="fw-bold mb-1 text-dark" style="font-size: 1.05rem;" title="{{ $top2->nama }}">{{ $top2->nama }}</h5>
                                <div class="text-muted small mb-2">{{ strtoupper($top2->jurusan?->nama_jurusan ?? $top2->kategori_label ?? 'UMUM') }}</div>
                                <div class="text-warning mb-2" style="font-size: 0.95rem; letter-spacing: 2px;">
                                    @php
                                        $val2 = $pct2 / 20;
                                        $stars2 = round($val2 * 2) / 2;
                                    @endphp
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
                                <div class="mb-2">
                                    <div class="gk-progress-pill gk-pill-rank-2 mx-auto" style="width: 135px; height: 22px; font-size: 0.74rem;">
                                        <div class="gk-progress-pill-fill {{ $pct2 >= 75 ? 'gk-bar-blue-high' : ($pct2 >= 50 ? 'gk-bar-blue-mid' : 'gk-bar-blue-low') }}" style="width: {{ $pct2 }}%;"></div>
                                        <span class="position-relative text-white" style="z-index: 2; text-shadow: 0 1px 2px rgba(0,0,0,0.6);">{{ $pct2 }}%</span>
                                    </div>
                                </div>
                                <small class="text-muted font-mono d-block mb-3" style="font-size: 0.78rem;">{{ $top2->total_penilaian }} ulasan</small>
                            </div>
                            <a href="{{ route('landing.guru.detail', $top2->id) }}" class="btn btn-sm btn-outline-primary gk-btn-podium-profile mb-2">
                                <i class="bi bi-eye me-1"></i> Profil
                            </a>
                        </div>
                    </div>

                    {{-- #1 EMAS --}}
                    <div class="col-6 col-md-5" data-aos="fade-left">
                        <div class="gk-podium-card-revised is-first p-3.5 p-md-4 text-center h-100 d-flex flex-column justify-content-between shadow position-relative">
                            <div>
                                <div class="mb-1">
                                    <span class="badge rounded-pill px-4 py-1.5 fw-bold" style="background: #fef08a; color: #854d0e; font-size: 0.85rem;">
                                        #1st
                                    </span>
                                </div>
                                <div class="gk-podium-badges-row">
                                    @foreach(($top1->penghargaan ?? collect()) as $penghargaan)
                                        @if($penghargaan->badge)
                                            <span class="gk-badge-mini-icon" style="background: {{ $penghargaan->badge->warna }}18; color: {{ $penghargaan->badge->warna }}; border-color: {{ $penghargaan->badge->warna }}33;" data-bs-toggle="tooltip" title="{{ $penghargaan->badge->nama_badge }}: {{ $penghargaan->badge->deskripsi }}">
                                                @if(str_starts_with($penghargaan->badge->icon, 'bi-'))<i class="bi {{ $penghargaan->badge->icon }}"></i>@else{{ $penghargaan->badge->icon }}@endif
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="gk-avatar-clean-wrap mb-3" style="width: 104px; height: 104px;">
                                    <img src="{{ $top1->photo_url }}" width="104" height="104" alt="{{ $top1->nama }}">
                                </div>
                                <h4 class="fw-bold mb-1 text-dark" style="font-size: 1.15rem;" title="{{ $top1->nama }}">{{ $top1->nama }}</h4>
                                <div class="text-muted small mb-2">{{ strtoupper($top1->jurusan?->nama_jurusan ?? $top1->kategori_label ?? 'UMUM') }}</div>
                                <div class="text-warning mb-2" style="font-size: 1rem; letter-spacing: 2px;">
                                    @php
                                        $val1 = $pct1 / 20;
                                        $stars1 = round($val1 * 2) / 2;
                                    @endphp
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
                                <div class="mb-2">
                                    <div class="gk-progress-pill gk-pill-rank-1 mx-auto" style="width: 140px; height: 22px; font-size: 0.74rem;">
                                        <div class="gk-progress-pill-fill {{ $pct1 >= 75 ? 'gk-bar-blue-high' : ($pct1 >= 50 ? 'gk-bar-blue-mid' : 'gk-bar-blue-low') }}" style="width: {{ $pct1 }}%;"></div>
                                        <span class="position-relative text-white" style="z-index: 2; text-shadow: 0 1px 2px rgba(0,0,0,0.6);">{{ $pct1 }}%</span>
                                    </div>
                                </div>
                                <small class="text-muted font-mono d-block mb-3" style="font-size: 0.78rem;">{{ $top1->total_penilaian }} ulasan</small>
                            </div>
                            <a href="{{ route('landing.guru.detail', $top1->id) }}" class="btn btn-sm btn-outline-primary gk-btn-podium-profile mb-2">
                                <i class="bi bi-eye me-1"></i> Profil
                            </a>
                        </div>
                    </div>
                </div>
            @else
                {{-- TOP 3 LENGKAP --}}
                @php
                    $pct1 = round(($top1->rata_rata_nilai / 5) * 100);
                    $pct2 = round(($top2->rata_rata_nilai / 5) * 100);
                    $pct3 = round(($top3->rata_rata_nilai / 5) * 100);
                @endphp
                <div class="row g-3 g-lg-4 justify-content-center align-items-end mb-5 gk-podium-row" id="leaderboardPodium">
                    {{-- #2 PERAK (KIRI - NORMAL) --}}
                    <div class="col-11 col-sm-8 col-md-4 order-2 order-md-1 podium-anim-item podium-rank-2 gk-podium-2">
                        <div class="gk-podium-card-revised p-3.5 p-md-4 text-center h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="mb-1">
                                    <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #f1f5f9; color: #475569; font-size: 0.8rem;">
                                        #2nd
                                    </span>
                                </div>
                                
                                <div class="gk-podium-badges-row">
                                    @foreach(($top2->penghargaan ?? collect()) as $penghargaan)
                                        @if($penghargaan->badge)
                                            <span class="gk-badge-mini-icon" style="background: {{ $penghargaan->badge->warna }}18; color: {{ $penghargaan->badge->warna }}; border-color: {{ $penghargaan->badge->warna }}33;" data-bs-toggle="tooltip" title="{{ $penghargaan->badge->nama_badge }}: {{ $penghargaan->badge->deskripsi }}">
                                                @if(str_starts_with($penghargaan->badge->icon, 'bi-'))
                                                     <i class="bi {{ $penghargaan->badge->icon }}"></i>
                                                @else
                                                    {{ $penghargaan->badge->icon }}
                                                @endif
                                            </span>
                                        @endif
                                    @endforeach
                                </div>

                                <div class="gk-avatar-clean-wrap mb-3" style="width: 96px; height: 96px;">
                                    <img src="{{ $top2->photo_url }}" width="96" height="96" alt="{{ $top2->nama }}">
                                </div>
                                <h5 class="fw-bold mb-1 text-dark" style="font-size: 1.05rem;" title="{{ $top2->nama }}">{{ $top2->nama }}</h5>
                                <div class="text-muted small mb-2">{{ strtoupper($top2->jurusan?->nama_jurusan ?? $top2->kategori_label ?? 'UMUM') }}</div>
                                
                                <div class="text-warning mb-2" style="font-size: 0.95rem; letter-spacing: 2px;">
                                    @php
                                        $val2 = $pct2 / 20;
                                        $stars2 = round($val2 * 2) / 2;
                                    @endphp
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

                                <div class="mb-2">
                                    <div class="gk-progress-pill gk-pill-rank-2 mx-auto" style="width: 140px; height: 32px; font-size: 0.84rem;">
                                        <div class="gk-progress-pill-fill {{ $pct2 >= 75 ? 'gk-bar-blue-high' : ($pct2 >= 50 ? 'gk-bar-blue-mid' : 'gk-bar-blue-low') }}" style="width: 0%;" data-percentage="{{ $pct2 }}"></div>
                                        <span class="position-relative text-white" style="z-index: 2; text-shadow: 0 1px 2px rgba(0,0,0,0.6);">{{ $pct2 }}%</span>
                                    </div>
                                </div>
                                <small class="text-muted font-mono d-block" style="font-size: 0.78rem;">{{ $top2->total_penilaian }} ulasan</small>
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('landing.guru.detail', $top2->id) }}" class="btn btn-sm btn-outline-primary gk-btn-podium-profile mb-2">
                                    <i class="bi bi-eye me-1"></i> Profil
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- #1 EMAS (TENGAH - BESAR) --}}
                    <div class="col-11 col-sm-8 col-md-4 order-1 order-md-2 podium-anim-item podium-rank-1 gk-podium-1">
                        <div class="gk-podium-card-revised is-first p-4 p-md-5 text-center h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="mb-1">
                                    <span class="badge rounded-pill px-4 py-1.5 fw-bold" style="background: #fef08a; color: #854d0e; font-size: 0.9rem;">
                                        #1st
                                    </span>
                                </div>

                                <div class="gk-podium-badges-row">
                                    @foreach(($top1->penghargaan ?? collect()) as $penghargaan)
                                        @if($penghargaan->badge)
                                            <span class="gk-badge-mini-icon" style="background: {{ $penghargaan->badge->warna }}18; color: {{ $penghargaan->badge->warna }}; border-color: {{ $penghargaan->badge->warna }}33;" data-bs-toggle="tooltip" title="{{ $penghargaan->badge->nama_badge }}: {{ $penghargaan->badge->deskripsi }}">
                                                @if(str_starts_with($penghargaan->badge->icon, 'bi-'))
                                                     <i class="bi {{ $penghargaan->badge->icon }}"></i>
                                                @else
                                                    {{ $penghargaan->badge->icon }}
                                                @endif
                                            </span>
                                        @endif
                                    @endforeach
                                </div>

                                <div class="gk-avatar-clean-wrap mb-3" style="width: 114px; height: 114px;">
                                    <img src="{{ $top1->photo_url }}" width="114" height="114" alt="{{ $top1->nama }}">
                                </div>
                                <h4 class="fw-bold mb-1 text-dark" style="font-size: 1.25rem;" title="{{ $top1->nama }}">{{ $top1->nama }}</h4>
                                <div class="text-muted small mb-2">{{ strtoupper($top1->jurusan?->nama_jurusan ?? $top1->kategori_label ?? 'UMUM') }}</div>
                                
                                <div class="text-warning mb-2" style="font-size: 1.1rem; letter-spacing: 2.5px;">
                                    @php
                                        $val1 = $pct1 / 20;
                                        $stars1 = round($val1 * 2) / 2;
                                    @endphp
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

                                <div class="mb-2">
                                    <div class="gk-progress-pill gk-pill-rank-1 mx-auto" style="width: 156px; height: 35px; font-size: 0.92rem;">
                                        <div class="gk-progress-pill-fill {{ $pct1 >= 75 ? 'gk-bar-blue-high' : ($pct1 >= 50 ? 'gk-bar-blue-mid' : 'gk-bar-blue-low') }}" style="width: 0%;" data-percentage="{{ $pct1 }}"></div>
                                        <span class="position-relative text-white" style="z-index: 2; text-shadow: 0 1px 2px rgba(0,0,0,0.6);">{{ $pct1 }}%</span>
                                    </div>
                                </div>
                                <small class="text-muted font-mono d-block" style="font-size: 0.8rem;">{{ $top1->total_penilaian }} ulasan</small>
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('landing.guru.detail', $top1->id) }}" class="btn btn-sm btn-outline-primary gk-btn-podium-profile mb-2">
                                    <i class="bi bi-eye me-1"></i> Profil
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- #3 PERUNGGU (KANAN - KECIL) --}}
                    <div class="col-11 col-sm-8 col-md-4 order-3 order-md-3 podium-anim-item podium-rank-3 gk-podium-3">
                        <div class="gk-podium-card-revised p-3 p-md-3.5 text-center h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="mb-1">
                                    <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: #fed7aa; color: #9a3412; font-size: 0.75rem;">
                                        #3rd
                                    </span>
                                </div>

                                <div class="gk-podium-badges-row">
                                    @foreach(($top3->penghargaan ?? collect()) as $penghargaan)
                                        @if($penghargaan->badge)
                                            <span class="gk-badge-mini-icon" style="background: {{ $penghargaan->badge->warna }}18; color: {{ $penghargaan->badge->warna }}; border-color: {{ $penghargaan->badge->warna }}33;" data-bs-toggle="tooltip" title="{{ $penghargaan->badge->nama_badge }}: {{ $penghargaan->badge->deskripsi }}">
                                                @if(str_starts_with($penghargaan->badge->icon, 'bi-'))
                                                     <i class="bi {{ $penghargaan->badge->icon }}"></i>
                                                @else
                                                    {{ $penghargaan->badge->icon }}
                                                @endif
                                            </span>
                                        @endif
                                    @endforeach
                                </div>

                                <div class="gk-avatar-clean-wrap mb-3" style="width: 82px; height: 82px;">
                                    <img src="{{ $top3->photo_url }}" width="82" height="82" alt="{{ $top3->nama }}">
                                </div>
                                <h5 class="fw-bold mb-1 text-dark" style="font-size: 0.95rem;" title="{{ $top3->nama }}">{{ $top3->nama }}</h5>
                                <div class="text-muted small mb-2">{{ strtoupper($top3->jurusan?->nama_jurusan ?? $top3->kategori_label ?? 'UMUM') }}</div>
                                
                                <div class="text-warning mb-2" style="font-size: 0.85rem; letter-spacing: 1.5px;">
                                    @php
                                        $val3 = $pct3 / 20;
                                        $stars3 = round($val3 * 2) / 2;
                                    @endphp
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

                                <div class="mb-2">
                                    <div class="gk-progress-pill gk-pill-rank-3 mx-auto" style="width: 124px; height: 28px; font-size: 0.78rem;">
                                        <div class="gk-progress-pill-fill {{ $pct3 >= 75 ? 'gk-bar-blue-high' : ($pct3 >= 50 ? 'gk-bar-blue-mid' : 'gk-bar-blue-low') }}" style="width: 0%;" data-percentage="{{ $pct3 }}"></div>
                                        <span class="position-relative text-white" style="z-index: 2; text-shadow: 0 1px 2px rgba(0,0,0,0.6);">{{ $pct3 }}%</span>
                                    </div>
                                </div>
                                <small class="text-muted font-mono d-block" style="font-size: 0.75rem;">{{ $top3->total_penilaian }} ulasan</small>
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('landing.guru.detail', $top3->id) }}" class="btn btn-sm btn-outline-primary gk-btn-podium-profile mb-2">
                                    <i class="bi bi-eye me-1"></i> Profil
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        {{-- Button Pill Hitam: Lihat Selengkapnya --}}
        <div class="text-center" data-aos="zoom-in">
            <a href="{{ route('landing.leaderboard') }}" class="gk-pill-btn-dark">
                Lihat selengkapnya
            </a>
        </div>
    </div>
</section>

{{-- 4. PANDUAN / CARA PENILAIAN (id="panduan") --}}
<section id="panduan" class="section-padding" style="background: #ffffff; padding: 5rem 0;">
    <div class="container">
        <div class="text-center mb-4 mb-md-5" data-aos="fade-up">
            <div class="small fw-semibold text-muted mb-1" style="font-size: 0.85rem; letter-spacing: 0.8px; text-transform: uppercase;">
                {{ \App\Models\Setting::get('panduan_label', 'PANDUAN PENGGUNAAN') }}
            </div>
            <h2 class="fw-bold mb-2 text-dark" style="font-size: clamp(1.6rem, 3.5vw, 2.2rem);">
                {{ \App\Models\Setting::get('panduan_title', 'Bagaimana Cara Memberi Penilaian?') }}
            </h2>
            <p class="text-muted mb-0" style="font-size: 0.95rem;">
                {{ \App\Models\Setting::get('panduan_subtitle', 'Hanya butuh 3 langkah mudah untuk berkontribusi bagi sekolahmu') }}
            </p>
        </div>

        {{-- SLIDER PANDUAN LANGKAH 1, 2, 3 (CAROUSEL WITH AUTOPLAY, TOUCH SWIPE & CENTER CONTROLS) --}}
        <div class="gk-panduan-slider-wrapper mx-auto" data-aos="fade-up" data-aos-delay="100">
            <div class="gk-panduan-viewport" id="panduanViewport">
                <div class="gk-panduan-track" id="panduanTrack">
                    {{-- LANGKAH 1 --}}
                    <div class="gk-panduan-slide is-active" data-slide="0">
                        <div class="gk-clean-card p-4 p-md-5 text-center h-100 d-flex flex-column justify-content-between" style="min-height: 280px;">
                            <div>
                                <div class="d-flex align-items-center justify-content-center gap-2 mb-4">
                                    <span class="badge rounded-pill px-3 py-1.5 fw-bold text-uppercase" style="background: #f1f5f9; color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">
                                        {{ \App\Models\Setting::get('panduan_step1_title', 'LOGIN NIS') }}
                                    </span>
                                    <span class="badge rounded-pill px-2.5 py-1 text-muted fw-semibold" style="background: #f8fafc; font-size: 0.7rem; font-family: monospace;">
                                        01 / 03
                                    </span>
                                </div>
                                <div class="gk-step-roman">I</div>
                                <p class="text-dark fw-semibold mb-0" style="font-size: 1rem; line-height: 1.6;">
                                    {{ \App\Models\Setting::get('panduan_step1_desc', 'Masuk dengan akun NIS & tanggal lahir resmi terverifikasi.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- LANGKAH 2 --}}
                    <div class="gk-panduan-slide" data-slide="1">
                        <div class="gk-clean-card p-4 p-md-5 text-center h-100 d-flex flex-column justify-content-between" style="min-height: 280px;">
                            <div>
                                <div class="d-flex align-items-center justify-content-center gap-2 mb-4">
                                    <span class="badge rounded-pill px-3 py-1.5 fw-bold text-uppercase" style="background: #f1f5f9; color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">
                                        {{ \App\Models\Setting::get('panduan_step2_title', 'BERI NILAI') }}
                                    </span>
                                    <span class="badge rounded-pill px-2.5 py-1 text-muted fw-semibold" style="background: #f8fafc; font-size: 0.7rem; font-family: monospace;">
                                        02 / 03
                                    </span>
                                </div>
                                <div class="gk-step-roman">II</div>
                                <p class="text-dark fw-semibold mb-0" style="font-size: 1rem; line-height: 1.6;">
                                    {{ \App\Models\Setting::get('panduan_step2_desc', 'Pilih guru Normada/Produktif, beri nilai (1-5) pada 5 kriteria.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- LANGKAH 3 --}}
                    <div class="gk-panduan-slide" data-slide="2">
                        <div class="gk-clean-card p-4 p-md-5 text-center h-100 d-flex flex-column justify-content-between" style="min-height: 280px;">
                            <div>
                                <div class="d-flex align-items-center justify-content-center gap-2 mb-4">
                                    <span class="badge rounded-pill px-3 py-1.5 fw-bold text-uppercase" style="background: #f1f5f9; color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">
                                        {{ \App\Models\Setting::get('panduan_step3_title', 'KIRIM ANONIM') }}
                                    </span>
                                    <span class="badge rounded-pill px-2.5 py-1 text-muted fw-semibold" style="background: #f8fafc; font-size: 0.7rem; font-family: monospace;">
                                        03 / 03
                                    </span>
                                </div>
                                <div class="gk-step-roman">III</div>
                                <p class="text-dark fw-semibold mb-0" style="font-size: 1rem; line-height: 1.6;">
                                    {{ \App\Models\Setting::get('panduan_step3_desc', 'Data tersimpan aman & anonim untuk perbaikan pengajaran.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TOMBOL PANAH & INDIKATOR DOTS CENTER DI BAWAH PANDUAN --}}
            <div class="d-flex align-items-center justify-content-center gap-3 mt-4 pt-1">
                <button type="button" class="gk-panduan-nav-btn" id="panduanPrevBtn" aria-label="Langkah Sebelumnya" title="Sebelumnya">
                    <i class="bi bi-chevron-left"></i>
                </button>

                <div class="d-flex align-items-center gap-2" id="panduanDots">
                    <button type="button" class="gk-panduan-dot active" data-index="0" aria-label="Langkah 1"></button>
                    <button type="button" class="gk-panduan-dot" data-index="1" aria-label="Langkah 2"></button>
                    <button type="button" class="gk-panduan-dot" data-index="2" aria-label="Langkah 3"></button>
                </div>

                <button type="button" class="gk-panduan-nav-btn" id="panduanNextBtn" aria-label="Langkah Selanjutnya" title="Selanjutnya">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</section>

{{-- 5. TENTANG KAMI - VISI MISI (id="tentang") --}}
<section id="tentang" class="section-padding" style="background: var(--bg-light, #f8fafc); padding: 5rem 0;">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="small fw-semibold text-muted mb-1" style="font-size: 0.85rem; letter-spacing: 0.8px; text-transform: uppercase;">{{ \App\Models\Setting::get('about_label', 'Tentang kami') }}</div>
            <h2 class="fw-bold mb-2 text-dark" style="font-size: clamp(1.6rem, 3.5vw, 2.2rem);">{{ \App\Models\Setting::get('about_title', 'Mengapa GuruKuu – SMK Negeri 1 Bangsri Ada?') }}</h2>
            <p class="text-muted" style="max-width: 680px; margin: 0 auto; font-size: 0.95rem; text-wrap: balance;">
                {{ \App\Models\Setting::get('about_subtitle', 'Platform evaluasi terintegrasi untuk SMK yang membangun jembatan komunikasi positif antara siswa, guru, dan management sekolah.') }}
            </p>
        </div>

        {{-- 2-COLUMN LAYOUT: VISI & MISI DI KIRI POL, FITUR DI SAMPING KANANNYA --}}
        <div class="row g-4 align-items-stretch">
            {{-- SISI KIRI POL: VISI & MISI KAMI --}}
            <div class="col-lg-6 d-flex flex-column gap-4" data-aos="fade-right">
                {{-- VISI KAMI --}}
                <div class="gk-clean-card p-4 p-md-5 flex-grow-1">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <i class="bi bi-bullseye fs-3 text-dark"></i>
                        <h5 class="fw-bold mb-0 text-dark" style="letter-spacing: 0.5px; font-size: 1.15rem;">VISI KAMI</h5>
                    </div>
                    <p class="text-secondary mb-0" style="line-height: 1.7; font-size: 0.94rem;">
                        {{ \App\Models\Setting::get('visi_text', 'Menjadi standar nasional dalam evaluasi pengajaran berbasis data untuk menciptakan ekosistem pendidikan yang responsif, transparan, dan berkelanjutan di seluruh SMK Indonesia.') }}
                    </p>
                </div>

                {{-- MISI KAMI --}}
                <div class="gk-clean-card p-4 p-md-5 flex-grow-1">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <i class="bi bi-file-earmark-check fs-3 text-dark"></i>
                        <h5 class="fw-bold mb-0 text-dark" style="letter-spacing: 0.5px; font-size: 1.15rem;">MISI KAMI</h5>
                    </div>
                    @php
                        $misiRaw = \App\Models\Setting::get('misi_text', "Memberikan saluran aspirasi yang aman dan anonim bagi siswa.\nMenyediakan data analitik yang dapat ditindaklanjuti oleh manajemen sekolah.\nMendorong pengembangan profesional guru secara berkelanjutan.");
                        $misiList = array_filter(array_map('trim', explode("\n", $misiRaw)));
                    @endphp
                    <ul class="text-secondary mb-0 ps-3" style="line-height: 1.7; font-size: 0.94rem;">
                        @foreach($misiList as $misiItem)
                            <li>{{ ltrim($misiItem, '-*• ') }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- SISI KANAN: FITUR UTAMA (ANONIMITAS, BERBASIS DATA, KOLABORATIF) --}}
            <div class="col-lg-6 d-flex flex-column gap-3" data-aos="fade-left">
                <div class="gk-clean-card p-4 flex-grow-1 d-flex flex-column justify-content-center">
                    <div class="d-flex align-items-center gap-2.5 mb-2">
                        <i class="bi bi-shield-check fs-3 text-dark"></i>
                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">{{ \App\Models\Setting::get('feature1_title', 'Anonimitas') }}</h6>
                    </div>
                    <p class="text-secondary mb-0 small" style="line-height: 1.55; font-size: 0.88rem;">
                        {{ \App\Models\Setting::get('feature1_desc', 'Identitas siswa aman dengan enkripsi tanpa tekanan.') }}
                    </p>
                </div>

                <div class="gk-clean-card p-4 flex-grow-1 d-flex flex-column justify-content-center">
                    <div class="d-flex align-items-center gap-2.5 mb-2">
                        <i class="bi bi-journal-text fs-3 text-dark"></i>
                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">{{ \App\Models\Setting::get('feature2_title', 'Berbasis Data') }}</h6>
                    </div>
                    <p class="text-secondary mb-0 small" style="line-height: 1.55; font-size: 0.88rem;">
                        {{ \App\Models\Setting::get('feature2_desc', 'Data statistik valid & terukur untuk setiap apresiasi.') }}
                    </p>
                </div>

                <div class="gk-clean-card p-4 flex-grow-1 d-flex flex-column justify-content-center">
                    <div class="d-flex align-items-center gap-2.5 mb-2">
                        <i class="bi bi-people fs-3 text-dark"></i>
                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">{{ \App\Models\Setting::get('feature3_title', 'Kolaboratif') }}</h6>
                    </div>
                    <p class="text-secondary mb-0 small" style="line-height: 1.55; font-size: 0.88rem;">
                        {{ \App\Models\Setting::get('feature3_desc', 'Membangun komunikasi positif siswa, guru, & sekolah.') }}
                    </p>
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

@push('scripts')
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
    const AUTOPLAY_DELAY = 3600; // 3.6 detik jeda waktu

    function updateSlide(animate = true) {
        if (!animate) {
            track.style.transition = 'none';
        } else {
            track.style.transition = 'transform 0.65s cubic-bezier(0.22, 1, 0.36, 1)';
        }

        track.style.transform = `translateX(-${currentIndex * 100}%)`;

        slides.forEach((slide, idx) => {
            if (idx === currentIndex) {
                slide.classList.add('is-active');
            } else {
                slide.classList.remove('is-active');
            }
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

    function resetAutoplay() {
        stopAutoplay();
        startAutoplay();
    }

    // Button controls
    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            nextSlide();
            resetAutoplay();
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            prevSlide();
            resetAutoplay();
        });
    }

    // Dots indicator clicks
    dots.forEach((dot) => {
        dot.addEventListener('click', function () {
            const idx = parseInt(this.getAttribute('data-index'), 10);
            goToSlide(idx);
            resetAutoplay();
        });
    });

    // Pause autoplay on mouse hover (desktop)
    viewport.addEventListener('mouseenter', stopAutoplay);
    viewport.addEventListener('mouseleave', startAutoplay);

    // Touch / Swipe Support for Mobile (geser dengan jari)
    let startX = 0;
    let startY = 0;
    let currentX = 0;
    let isSwiping = false;
    let hasDeterminedDirection = false;
    let isHorizontal = false;

    viewport.addEventListener('touchstart', function (e) {
        if (e.touches.length !== 1) return;
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
        currentX = startX;
        isSwiping = true;
        hasDeterminedDirection = false;
        isHorizontal = false;
        stopAutoplay();
    }, { passive: true });

    viewport.addEventListener('touchmove', function (e) {
        if (!isSwiping || e.touches.length !== 1) return;
        currentX = e.touches[0].clientX;
        const currentY = e.touches[0].clientY;
        const diffX = currentX - startX;
        const diffY = currentY - startY;

        if (!hasDeterminedDirection) {
            if (Math.abs(diffX) > 7 || Math.abs(diffY) > 7) {
                hasDeterminedDirection = true;
                isHorizontal = Math.abs(diffX) > Math.abs(diffY);
            }
        }

        if (isHorizontal) {
            const vpWidth = viewport.offsetWidth || 300;
            const dragPercent = (diffX / vpWidth) * 100;
            const currentTranslate = -currentIndex * 100;
            track.style.transition = 'none';
            track.style.transform = `translateX(${currentTranslate + dragPercent}%)`;
        }
    }, { passive: true });

    viewport.addEventListener('touchend', function () {
        if (!isSwiping) return;
        isSwiping = false;
        const diffX = currentX - startX;

        track.style.transition = 'transform 0.65s cubic-bezier(0.22, 1, 0.36, 1)';

        if (isHorizontal && Math.abs(diffX) > 40) {
            if (diffX < 0) {
                nextSlide();
            } else {
                prevSlide();
            }
        } else {
            updateSlide(true);
        }
        startAutoplay();
    });

    // Mouse drag support for desktop
    let isMouseDown = false;
    let mouseStartX = 0;
    let mouseCurrentX = 0;

    viewport.addEventListener('mousedown', function (e) {
        if (e.button !== 0) return;
        isMouseDown = true;
        mouseStartX = e.clientX;
        mouseCurrentX = mouseStartX;
        stopAutoplay();
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
        const diffX = mouseCurrentX - mouseStartX;
        track.style.transition = 'transform 0.65s cubic-bezier(0.22, 1, 0.36, 1)';
        if (Math.abs(diffX) > 45) {
            if (diffX < 0) {
                nextSlide();
            } else {
                prevSlide();
            }
        } else {
            updateSlide(true);
        }
        startAutoplay();
    });

    // Pause when tab is hidden
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            stopAutoplay();
        } else {
            startAutoplay();
        }
    });

    // Initialize
    updateSlide(false);
    startAutoplay();
});
</script>
@endpush