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
/* Smooth Public Dashboard Interactive Transitions & Refined Aesthetics */
.stat-card-modern, .teacher-card, .tutorial-card, .gk-vm-card, .gk-feature-card, .card-custom, .gk-clean-card {
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.35s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.3s ease;
}
.stat-card-modern:hover {
    transform: translateY(-6px);
}

/* REFINED PODIUM CARDS & SHADOWS */
.gk-clean-card {
    background: #ffffff;
    border-radius: 24px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 10px 30px -8px rgba(0, 0, 0, 0.05), 0 4px 10px -2px rgba(0, 0, 0, 0.02);
}
.gk-clean-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 38px -10px rgba(0, 0, 0, 0.08), 0 8px 16px -4px rgba(0, 0, 0, 0.03);
}
.gk-podium-card-revised {
    background: #ffffff;
    border-radius: 24px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 10px 28px -6px rgba(0, 0, 0, 0.06), 0 4px 10px -2px rgba(0, 0, 0, 0.02);
    position: relative;
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
.gk-podium-card-revised:hover {
    transform: translateY(-6px);
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.09), 0 8px 16px -4px rgba(0, 0, 0, 0.03);
}
.gk-podium-card-revised.is-first {
    border-radius: 28px;
    box-shadow: 0 18px 38px -8px rgba(0, 0, 0, 0.08), 0 6px 14px -3px rgba(0, 0, 0, 0.03);
    z-index: 2;
}
.gk-avatar-red-wrap {
    display: inline-block;
    border-radius: 50%;
    background: #dc2626;
    padding: 3px;
    box-shadow: 0 6px 16px -3px rgba(220, 38, 38, 0.35);
}
.gk-avatar-red-wrap img {
    border-radius: 50%;
    object-fit: cover;
    display: block;
}
.gk-badge-mini-icon {
    width: 26px;
    height: 26px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    border: 1px solid rgba(0,0,0,0.08);
    transition: transform 0.2s;
    cursor: default;
}
.gk-badge-mini-icon:hover {
    transform: translateY(-2px) scale(1.12);
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
    background: #475569;
    border-radius: 50px;
    color: #ffffff;
    font-weight: 700;
    font-size: 0.85rem;
    padding: 0.35rem 1rem;
    display: inline-block;
    min-width: 140px;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.gk-progress-pill-fill {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    background: rgba(255, 255, 255, 0.22);
    border-radius: 50px;
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
<section id="guru" class="section-padding" style="background: var(--bg-light, #f8fafc); padding: 5rem 0;">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="small fw-bold text-uppercase text-muted mb-1" style="letter-spacing: 1.5px; font-size: 0.78rem;">{{ \App\Models\Setting::get('leaderboard_label', 'PENCAPAIAN TERBAIK') }}</div>
            <h2 class="fw-bold mb-2 text-dark" style="font-size: clamp(1.6rem, 3.5vw, 2.2rem);">{{ \App\Models\Setting::get('leaderboard_title', 'Guru dengan Partisipasi Tertinggi') }}</h2>
            <p class="text-muted mb-0" style="font-size: 0.95rem;">{{ \App\Models\Setting::get('leaderboard_subtitle', 'Data Guru dengan persentase nilai terbaik dari siswa') }}</p>
        </div>

        @php
            $allTeachers = \App\Models\Guru::with(['jurusan', 'penghargaan.badge'])->withRatings()
                ->orderByDesc('rata_rata_nilai')->orderByDesc('total_penilaian')->limit(3)->get();
            
            $topList = $allTeachers->map(function($guru) {
                $guru->persentase = round(($guru->rata_rata_nilai / 5) * 100);
                return $guru;
            })->sortByDesc('rata_rata_nilai')->take(3)->values();
        @endphp

        <div class="row g-3 g-lg-4 justify-content-center align-items-end mb-5 gk-podium-row">
            @if($topList->count() > 0)
                {{-- #2 PERAK (KIRI) --}}
                @if($topList->count() > 1)
                <div class="col-11 col-sm-8 col-md-4 order-2 order-md-1" data-aos="fade-right">
                    <div class="gk-podium-card-revised p-4 text-center h-100 d-flex flex-column justify-content-between" style="min-height: 410px;">
                        <div>
                            <div class="mb-3">
                                <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #f1f5f9; color: #475569; font-size: 0.8rem;">
                                    #2nd
                                </span>
                            </div>
                            <div class="gk-avatar-red-wrap mb-3">
                                <img src="{{ $topList[1]->photo_url }}" width="96" height="96" alt="{{ $topList[1]->nama }}">
                            </div>
                            <h5 class="fw-bold mb-1 text-dark" style="font-size: 1.05rem;" title="{{ $topList[1]->nama }}">{{ $topList[1]->nama }}</h5>
                            
                            {{-- Bintang --}}
                            <div class="text-warning mb-2" style="font-size: 0.95rem; letter-spacing: 2px;">
                                @php $stars2 = round($topList[1]->persentase / 20); @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $i <= $stars2 ? 'bi-star-fill' : 'bi-star text-muted opacity-25' }}"></i>
                                @endfor
                            </div>

                            {{-- Progress Pill --}}
                            <div class="mb-2">
                                <div class="gk-progress-pill mx-auto" style="width: 140px; background: #64748b;">
                                    <div class="gk-progress-pill-fill" style="width: {{ $topList[1]->persentase }}%;"></div>
                                    <span class="position-relative" style="z-index: 2;">{{ $topList[1]->persentase }}%</span>
                                </div>
                            </div>
                            <small class="text-muted font-mono d-block" style="font-size: 0.78rem;">{{ $topList[1]->total_penilaian }} ulasan</small>
                        </div>

                        {{-- Row Badge Icons --}}
                        <div class="d-flex align-items-center justify-content-end gap-1.5 pt-3 border-top mt-3" style="min-height: 42px;">
                            @forelse($topList[1]->penghargaan as $penghargaan)
                                @if($penghargaan->badge)
                                    <span class="gk-badge-mini-icon" style="background: {{ $penghargaan->badge->warna }}18; color: {{ $penghargaan->badge->warna }}; border-color: {{ $penghargaan->badge->warna }}33;" data-bs-toggle="tooltip" title="{{ $penghargaan->badge->nama_badge }}: {{ $penghargaan->badge->deskripsi }}">
                                        @if(str_starts_with($penghargaan->badge->icon, 'bi-'))
                                            <i class="bi {{ $penghargaan->badge->icon }}"></i>
                                        @else
                                            {{ $penghargaan->badge->icon }}
                                        @endif
                                    </span>
                                @endif
                            @empty
                                <span class="gk-badge-mini-icon" style="background: #f59e0b18; color: #d97706; border-color: #f59e0b33;" title="Guru Berprestasi">
                                    <i class="bi bi-arrow-up"></i>
                                </span>
                                <span class="gk-badge-mini-icon" style="background: #10b98118; color: #059669; border-color: #10b98133;" title="Terverifikasi">
                                    <i class="bi bi-check-lg"></i>
                                </span>
                            @endforelse
                        </div>
                    </div>
                </div>
                @endif

                {{-- #1 EMAS (TENGAH - LEBIH TINGGI) --}}
                @if($topList->count() > 0)
                <div class="col-11 col-sm-8 col-md-4 order-1 order-md-2" data-aos="zoom-in">
                    <div class="gk-podium-card-revised is-first p-4 p-md-5 text-center h-100 d-flex flex-column justify-content-between" style="min-height: 460px;">
                        <div>
                            <div class="mb-3">
                                <span class="badge rounded-pill px-4 py-1.5 fw-bold" style="background: #fef08a; color: #854d0e; font-size: 0.9rem;">
                                    #1st
                                </span>
                            </div>
                            <div class="gk-avatar-red-wrap mb-3" style="padding: 4px;">
                                <img src="{{ $topList[0]->photo_url }}" width="124" height="124" alt="{{ $topList[0]->nama }}">
                            </div>
                            <h4 class="fw-bold mb-1 text-dark" style="font-size: 1.25rem;" title="{{ $topList[0]->nama }}">{{ $topList[0]->nama }}</h4>
                            
                            {{-- Bintang --}}
                            <div class="text-warning mb-2" style="font-size: 1.1rem; letter-spacing: 2.5px;">
                                @php $stars1 = round($topList[0]->persentase / 20); @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $i <= $stars1 ? 'bi-star-fill' : 'bi-star text-muted opacity-25' }}"></i>
                                @endfor
                            </div>

                            {{-- Progress Pill --}}
                            <div class="mb-2">
                                <div class="gk-progress-pill mx-auto" style="width: 155px; background: #334155;">
                                    <div class="gk-progress-pill-fill" style="width: {{ $topList[0]->persentase }}%;"></div>
                                    <span class="position-relative" style="z-index: 2;">{{ $topList[0]->persentase }}%</span>
                                </div>
                            </div>
                            <small class="text-muted font-mono d-block" style="font-size: 0.8rem;">{{ $topList[0]->total_penilaian }} ulasan</small>
                        </div>

                        {{-- Row Badge Icons (Lengkap) --}}
                        <div class="d-flex align-items-center justify-content-center justify-content-md-end gap-1.5 pt-3 border-top mt-3" style="min-height: 42px;">
                            @forelse($topList[0]->penghargaan as $penghargaan)
                                @if($penghargaan->badge)
                                    <span class="gk-badge-mini-icon" style="background: {{ $penghargaan->badge->warna }}18; color: {{ $penghargaan->badge->warna }}; border-color: {{ $penghargaan->badge->warna }}33;" data-bs-toggle="tooltip" title="{{ $penghargaan->badge->nama_badge }}: {{ $penghargaan->badge->deskripsi }}">
                                        @if(str_starts_with($penghargaan->badge->icon, 'bi-'))
                                            <i class="bi {{ $penghargaan->badge->icon }}"></i>
                                        @else
                                            {{ $penghargaan->badge->icon }}
                                        @endif
                                    </span>
                                @endif
                            @empty
                                <span class="gk-badge-mini-icon" style="background: #ec489918; color: #db2777; border-color: #ec489933;" title="Guru Terbaik">🏆</span>
                                <span class="gk-badge-mini-icon" style="background: #06b6d418; color: #0891b2; border-color: #06b6d433;" title="Disiplin">📅</span>
                                <span class="gk-badge-mini-icon" style="background: #10b98118; color: #059669; border-color: #10b98133;" title="Inspiratif">💡</span>
                                <span class="gk-badge-mini-icon" style="background: #6366f118; color: #4f46e5; border-color: #6366f133;" title="Favorit">⭐</span>
                                <span class="gk-badge-mini-icon" style="background: #f59e0b18; color: #d97706; border-color: #f59e0b33;" title="Komunikator">🤝</span>
                            @endforelse
                        </div>
                    </div>
                </div>
                @endif

                {{-- #3 PERUNGGU (KANAN) --}}
                @if($topList->count() > 2)
                <div class="col-11 col-sm-8 col-md-4 order-3 order-md-3" data-aos="fade-left">
                    <div class="gk-podium-card-revised p-4 text-center h-100 d-flex flex-column justify-content-between" style="min-height: 410px;">
                        <div>
                            <div class="mb-3">
                                <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #fed7aa; color: #9a3412; font-size: 0.8rem;">
                                    #3rd
                                </span>
                            </div>
                            <div class="gk-avatar-red-wrap mb-3">
                                <img src="{{ $topList[2]->photo_url }}" width="96" height="96" alt="{{ $topList[2]->nama }}">
                            </div>
                            <h5 class="fw-bold mb-1 text-dark" style="font-size: 1.05rem;" title="{{ $topList[2]->nama }}">{{ $topList[2]->nama }}</h5>
                            
                            {{-- Bintang --}}
                            <div class="text-warning mb-2" style="font-size: 0.95rem; letter-spacing: 2px;">
                                @php $stars3 = round($topList[2]->persentase / 20); @endphp
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $i <= $stars3 ? 'bi-star-fill' : 'bi-star text-muted opacity-25' }}"></i>
                                @endfor
                            </div>

                            {{-- Progress Pill --}}
                            <div class="mb-2">
                                <div class="gk-progress-pill mx-auto" style="width: 140px; background: #94a3b8;">
                                    <div class="gk-progress-pill-fill" style="width: {{ $topList[2]->persentase }}%;"></div>
                                    <span class="position-relative" style="z-index: 2;">{{ $topList[2]->persentase }}%</span>
                                </div>
                            </div>
                            <small class="text-muted font-mono d-block" style="font-size: 0.78rem;">{{ $topList[2]->total_penilaian }} ulasan</small>
                        </div>

                        {{-- Row Badge Icons --}}
                        <div class="d-flex align-items-center justify-content-end gap-1.5 pt-3 border-top mt-3" style="min-height: 42px;">
                            @forelse($topList[2]->penghargaan as $penghargaan)
                                @if($penghargaan->badge)
                                    <span class="gk-badge-mini-icon" style="background: {{ $penghargaan->badge->warna }}18; color: {{ $penghargaan->badge->warna }}; border-color: {{ $penghargaan->badge->warna }}33;" data-bs-toggle="tooltip" title="{{ $penghargaan->badge->nama_badge }}: {{ $penghargaan->badge->deskripsi }}">
                                        @if(str_starts_with($penghargaan->badge->icon, 'bi-'))
                                            <i class="bi {{ $penghargaan->badge->icon }}"></i>
                                        @else
                                            {{ $penghargaan->badge->icon }}
                                        @endif
                                    </span>
                                @endif
                            @empty
                                <span class="gk-badge-mini-icon" style="background: #f59e0b18; color: #d97706; border-color: #f59e0b33;" title="Guru Terfavorit">
                                    <i class="bi bi-arrow-up"></i>
                                </span>
                            @endforelse
                        </div>
                    </div>
                </div>
                @endif
            @else
                <div class="col-12 text-center text-muted py-4">Belum ada data evaluasi guru.</div>
            @endif
        </div>

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
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="small fw-semibold text-muted mb-1" style="font-size: 0.85rem;">{{ \App\Models\Setting::get('panduan_label', 'Panduan Penggunaan') }}</div>
            <h2 class="fw-bold mb-2 text-dark" style="font-size: clamp(1.6rem, 3.5vw, 2.2rem);">{{ \App\Models\Setting::get('panduan_title', 'Bagaimana Cara Memberi Penilaian?') }}</h2>
            <p class="text-muted mb-0" style="font-size: 0.95rem;">{{ \App\Models\Setting::get('panduan_subtitle', '3 Langkah Mudah untuk Memberi Ulasan') }}</p>
        </div>
        <div class="row g-3 g-md-4 justify-content-center">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="gk-clean-card p-4 p-md-5 text-center h-100 d-flex flex-column justify-content-between" style="min-height: 270px;">
                    <div>
                        <div class="mb-4">
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold text-uppercase" style="background: #f1f5f9; color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">
                                {{ \App\Models\Setting::get('panduan_step1_title', 'LOGIN') }}
                            </span>
                        </div>
                        <div class="gk-step-roman">I</div>
                        <p class="text-dark fw-semibold mb-0" style="font-size: 0.96rem; line-height: 1.55;">
                            {{ \App\Models\Setting::get('panduan_step1_desc', 'Masuk dengan akun NIS & tanggal lahir resmi terverifikasi.') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="gk-clean-card p-4 p-md-5 text-center h-100 d-flex flex-column justify-content-between" style="min-height: 270px;">
                    <div>
                        <div class="mb-4">
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold text-uppercase" style="background: #f1f5f9; color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">
                                {{ \App\Models\Setting::get('panduan_step2_title', 'BERI NILAI') }}
                            </span>
                        </div>
                        <div class="gk-step-roman">II</div>
                        <p class="text-dark fw-semibold mb-0" style="font-size: 0.96rem; line-height: 1.55;">
                            {{ \App\Models\Setting::get('panduan_step2_desc', 'Pilih guru Normada/Produktif, beri nilai (1-5) pada 5 kriteria.') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="gk-clean-card p-4 p-md-5 text-center h-100 d-flex flex-column justify-content-between" style="min-height: 270px;">
                    <div>
                        <div class="mb-4">
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold text-uppercase" style="background: #f1f5f9; color: #475569; font-size: 0.72rem; letter-spacing: 0.5px;">
                                {{ \App\Models\Setting::get('panduan_step3_title', 'KIRIM ULASAN') }}
                            </span>
                        </div>
                        <div class="gk-step-roman">III</div>
                        <p class="text-dark fw-semibold mb-0" style="font-size: 0.96rem; line-height: 1.55;">
                            {{ \App\Models\Setting::get('panduan_step3_desc', 'Data tersimpan aman & anonim untuk perbaikan pengajaran.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 5. TENTANG KAMI - VISI MISI (id="tentang") --}}
<section id="tentang" class="section-padding" style="background: var(--bg-light, #f8fafc); padding: 5rem 0;">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="small fw-bold text-primary mb-1" style="font-size: 0.85rem;">{{ \App\Models\Setting::get('about_label', 'Tentang kami') }}</div>
            <h2 class="fw-bold mb-2 text-dark" style="font-size: clamp(1.6rem, 3.5vw, 2.2rem);">{{ \App\Models\Setting::get('about_title', 'Mengapa GuruKuu – SMK Negeri 1 Bangsri Ada?') }}</h2>
            <p class="text-muted" style="max-width: 680px; margin: 0 auto; font-size: 0.95rem; text-wrap: balance;">
                {{ \App\Models\Setting::get('about_subtitle', 'Platform evaluasi terintegrasi untuk SMK yang membangun jembatan komunikasi positif antara siswa, guru, dan management sekolah.') }}
            </p>
        </div>

        {{-- 2 KARTU UTAMA: VISI & MISI (MONOKROM ELEGAN, RAPI TANPA KEBANYAKAN WARNA) --}}
        <div class="row g-3 g-md-4 mb-4">
            <div class="col-md-6" data-aos="fade-right">
                <div class="gk-clean-card p-4 p-md-5 h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <i class="bi bi-bullseye fs-3 text-dark"></i>
                        <h5 class="fw-bold mb-0 text-dark" style="letter-spacing: 0.5px; font-size: 1.15rem;">VISI KAMI</h5>
                    </div>
                    <p class="text-secondary mb-0" style="line-height: 1.65; font-size: 0.92rem;">
                        {{ \App\Models\Setting::get('visi_text', 'Menjadi standar nasional dalam evaluasi pengajaran berbasis data untuk menciptakan ekosistem pendidikan yang responsif, transparan, dan berkelanjutan di seluruh SMK Indonesia.') }}
                    </p>
                </div>
            </div>
            <div class="col-md-6" data-aos="fade-left">
                <div class="gk-clean-card p-4 p-md-5 h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <i class="bi bi-file-earmark-check fs-3 text-dark"></i>
                        <h5 class="fw-bold mb-0 text-dark" style="letter-spacing: 0.5px; font-size: 1.15rem;">MISI KAMI</h5>
                    </div>
                    @php
                        $misiRaw = \App\Models\Setting::get('misi_text', "Memberikan saluran aspirasi yang aman dan anonim bagi siswa.\nMenyediakan data analitik yang dapat ditindaklanjuti oleh manajemen sekolah.\nMendorong pengembangan profesional guru secara berkelanjutan.");
                        $misiList = array_filter(array_map('trim', explode("\n", $misiRaw)));
                    @endphp
                    <ul class="text-secondary mb-0 ps-3" style="line-height: 1.7; font-size: 0.92rem;">
                        @foreach($misiList as $misiItem)
                            <li>{{ ltrim($misiItem, '-*• ') }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        {{-- 3 KARTU FITUR BAWAH: ANONIMITAS, BERBASIS DATA, KOLABORATIF --}}
        <div class="row g-3 g-md-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="gk-clean-card p-4 p-md-4 h-100">
                    <div class="d-flex align-items-center gap-2.5 mb-2">
                        <i class="bi bi-shield-check fs-3 text-dark"></i>
                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">{{ \App\Models\Setting::get('feature1_title', 'Anonimitas') }}</h6>
                    </div>
                    <p class="text-secondary mb-0 small" style="line-height: 1.5; font-size: 0.85rem;">
                        {{ \App\Models\Setting::get('feature1_desc', 'Identitas siswa aman dengan enkripsi tanpa tekanan.') }}
                    </p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="gk-clean-card p-4 p-md-4 h-100">
                    <div class="d-flex align-items-center gap-2.5 mb-2">
                        <i class="bi bi-journal-text fs-3 text-dark"></i>
                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">{{ \App\Models\Setting::get('feature2_title', 'Berbasis Data') }}</h6>
                    </div>
                    <p class="text-secondary mb-0 small" style="line-height: 1.5; font-size: 0.85rem;">
                        {{ \App\Models\Setting::get('feature2_desc', 'Data statistik valid & terukur untuk setiap apresiasi.') }}
                    </p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="gk-clean-card p-4 p-md-4 h-100">
                    <div class="d-flex align-items-center gap-2.5 mb-2">
                        <i class="bi bi-people fs-3 text-dark"></i>
                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">{{ \App\Models\Setting::get('feature3_title', 'Kolaboratif') }}</h6>
                    </div>
                    <p class="text-secondary mb-0 small" style="line-height: 1.5; font-size: 0.85rem;">
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