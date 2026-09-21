@extends('layouts.landing')
@section('title', 'Beranda')

@section('content')
@php
    $heroImage = \App\Models\Setting::get('hero_image', 'https://images.unsplash.com/photo-1562774053-701939374585?w=1920');
    $heroTitle = \App\Models\Setting::get('hero_title', 'Bangun Sekolah yang Lebih Baik Melalui Penilaian Guru yang Objektif');
    $heroSubtitle = \App\Models\Setting::get('hero_subtitle', 'Suarakan aspirasimu secara aman untuk meningkatkan kualitas pengajaran dan menciptakan lingkungan belajar yang inspiratif.');
    $heroCtaText = \App\Models\Setting::get('hero_cta_text', 'Siap Memulai?');
    $heroCtaUrl = \App\Models\Setting::get('hero_cta_url', route('login'));
@endphp

{{-- 1. HERO SECTION (id="home") --}}
<section id="home" class="hero-section" style="background: linear-gradient(135deg, rgba(10, 25, 47, 0.85), rgba(0, 51, 102, 0.78)), url('{{ $heroImage }}') center/cover no-repeat;">
    <div class="container position-relative" style="z-index: 2;">
        <div class="row">
            <div class="col-lg-9 col-xl-8" data-aos="fade-right">
                <div class="hero-glass-badge">
                    <i class="bi bi-patch-check-fill text-warning"></i>
                    <span>SMK NEGERI 1 BANGSRI • JUARA</span>
                </div>
                <h1 class="hero-title">{{ $heroTitle }}</h1>
                <p class="hero-subtitle">{{ $heroSubtitle }}</p>
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <a href="{{ $heroCtaUrl }}" class="btn btn-cta">
                        <span>{{ $heroCtaText }}</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                    <a href="#statistik" class="btn btn-outline-light rounded-pill px-4 py-2.5 fw-semibold d-inline-flex align-items-center gap-2" style="border: 1px solid rgba(255,255,255,0.3); backdrop-filter: blur(8px);">
                        <i class="bi bi-bar-chart-line"></i> Lihat Statistik
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 2. STATISTIK REAL-TIME (id="statistik") --}}
<section id="statistik" class="stats-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="section-label">DATA SEKOLAH</div>
            <h2 class="section-title">Sekolah Kami dalam Angka</h2>
            <p class="text-muted" style="max-width: 600px; margin: 0 auto;">Statistik real-time dari sistem penilaian kinerja GuruKuu</p>
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
            <div class="section-label">PENCAPAIAN TERBAIK</div>
            <h2 class="section-title">Guru dengan Partisipasi Tertinggi</h2>
            <p class="text-muted">Guru dengan persentase kepuasan dan partisipasi penilaian tertinggi dari siswa</p>
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
                    <div class="card-custom gk-podium-card p-2 p-md-4 text-center h-100" style="border: 1px solid rgba(148, 163, 184, 0.4); box-shadow: 0 8px 24px rgba(0,0,0,0.04);">
                        <div class="mb-2 mb-md-3">
                            <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: #94a3b8; color: #fff; font-size: 0.75rem;">
                                <i class="bi bi-award-fill me-1"></i> #2 PERAK
                            </span>
                        </div>
                        <div class="position-relative d-inline-block mb-2 mb-md-3">
                            <img src="{{ $topList[1]->photo_url }}" class="rounded-circle shadow-sm gk-podium-avatar-2" width="85" height="85" style="object-fit: cover; border: 3px solid #94a3b8;">
                        </div>
                        <h6 class="fw-bold mb-1 gk-podium-nama" title="{{ $topList[1]->nama }}">{{ $topList[1]->nama }}</h6>
                        <div class="font-mono mb-2 mb-md-3 gk-podium-jurusan" style="font-size: 0.72rem; color: var(--text-muted);" title="{{ strtoupper($topList[1]->jurusan?->nama_jurusan ?? 'UMUM') }}">{{ strtoupper($topList[1]->jurusan?->nama_jurusan ?? 'UMUM') }}</div>
                        <div class="p-1.5 p-md-2 rounded-3 mb-2 mb-md-3 gk-podium-statbox" style="background: var(--bg-light);">
                            <div class="fw-bold text-primary gk-podium-score" style="font-size: 1.5rem;">{{ $topList[1]->persentase }}%</div>
                            <small class="text-muted font-mono gk-podium-reviews" style="font-size: 0.7rem;">{{ $topList[1]->total_penilaian }} ulasan</small>
                        </div>
                        <a href="{{ route('landing.guru.detail', $topList[1]->id) }}" class="btn btn-outline-custom btn-sm btn-podium w-100 rounded-pill">
                            Lihat Profil
                        </a>
                    </div>
                </div>
                @endif

                {{-- #1 EMAS (CENTER PODIUM) --}}
                @if($topList->count() > 0)
                <div class="col-4 col-lg-4 order-2 order-md-2 mb-0 gk-podium-col gk-podium-1" data-aos="zoom-in">
                    <div class="card-custom gk-podium-card p-2.5 p-md-5 text-center position-relative" style="border: 2px solid #f59e0b; box-shadow: 0 16px 36px rgba(245, 158, 11, 0.16); background: var(--bg-card);">
                        <div class="mb-2 mb-md-3">
                            <span class="badge rounded-pill px-2.5 px-md-3.5 py-1 py-md-1.5 fw-bold shadow-sm" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: #ffffff; font-size: 0.8rem; letter-spacing: 0.5px;">
                                <i class="bi bi-trophy-fill me-1"></i> #1 EMAS
                            </span>
                        </div>
                        <div class="position-relative d-inline-block mb-2 mb-md-3">
                            <img src="{{ $topList[0]->photo_url }}" class="rounded-circle shadow gk-podium-avatar-1" width="110" height="110" style="object-fit: cover; border: 4px solid #f59e0b;">
                        </div>
                        <h5 class="fw-bold mb-1 gk-podium-nama" title="{{ $topList[0]->nama }}">{{ $topList[0]->nama }}</h5>
                        <div class="font-mono mb-2 mb-md-3 gk-podium-jurusan" style="font-size: 0.75rem; letter-spacing: 1px; color: var(--secondary);" title="{{ strtoupper($topList[0]->jurusan?->nama_jurusan ?? 'UMUM') }}">{{ strtoupper($topList[0]->jurusan?->nama_jurusan ?? 'UMUM') }}</div>
                        <div class="p-2 p-md-3 rounded-3 mb-2 mb-md-3 gk-podium-statbox" style="background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.2);">
                            <div class="fw-bold text-warning gk-podium-score" style="font-size: 2.2rem; line-height: 1;">{{ $topList[0]->persentase }}%</div>
                            <div class="progress mt-1 mt-md-2 mb-1" style="height: 6px; background-color: rgba(245, 158, 11, 0.2); border-radius: 10px;">
                                <div class="progress-bar bg-warning rounded-pill" style="width: {{ $topList[0]->persentase }}%;"></div>
                            </div>
                            <small class="text-muted font-mono d-block mt-0.5 mt-md-1 gk-podium-reviews" style="font-size: 0.75rem;">{{ $topList[0]->total_penilaian }} ulasan</small>
                        </div>
                        <a href="{{ route('landing.guru.detail', $topList[0]->id) }}" class="btn btn-primary-custom btn-podium w-100 rounded-pill py-1.5 py-md-2 fw-semibold">
                            Lihat Profil
                        </a>
                    </div>
                </div>
                @endif

                {{-- #3 PERUNGGU --}}
                @if($topList->count() > 2)
                <div class="col-4 col-lg-3 order-3 order-md-3 gk-podium-col gk-podium-3" data-aos="fade-left">
                    <div class="card-custom gk-podium-card p-2 p-md-4 text-center h-100" style="border: 1px solid rgba(217, 119, 6, 0.3); box-shadow: 0 8px 24px rgba(0,0,0,0.04);">
                        <div class="mb-2 mb-md-3">
                            <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: #d97706; color: #fff; font-size: 0.75rem;">
                                <i class="bi bi-award-fill me-1"></i> #3 PERUNGGU
                            </span>
                        </div>
                        <div class="position-relative d-inline-block mb-2 mb-md-3">
                            <img src="{{ $topList[2]->photo_url }}" class="rounded-circle shadow-sm gk-podium-avatar-3" width="85" height="85" style="object-fit: cover; border: 3px solid #d97706;">
                        </div>
                        <h6 class="fw-bold mb-1 gk-podium-nama" title="{{ $topList[2]->nama }}">{{ $topList[2]->nama }}</h6>
                        <div class="font-mono mb-2 mb-md-3 gk-podium-jurusan" style="font-size: 0.72rem; color: var(--text-muted);" title="{{ strtoupper($topList[2]->jurusan?->nama_jurusan ?? 'UMUM') }}">{{ strtoupper($topList[2]->jurusan?->nama_jurusan ?? 'UMUM') }}</div>
                        <div class="p-1.5 p-md-2 rounded-3 mb-2 mb-md-3 gk-podium-statbox" style="background: var(--bg-light);">
                            <div class="fw-bold text-primary gk-podium-score" style="font-size: 1.5rem;">{{ $topList[2]->persentase }}%</div>
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
            <div class="section-label">PANDUAN PENGGUNAAN</div>
            <h2 class="section-title">Bagaimana Cara Memberi Penilaian?</h2>
            <p class="text-muted mb-0 small">Hanya butuh 3 langkah mudah untuk berkontribusi bagi sekolahmu</p>
        </div>
        <div class="row g-2 g-md-4">
            <div class="col-4 col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="tutorial-card p-2 p-md-4 text-center h-100">
                    <div class="tutorial-number" style="background: var(--primary); color: white;">1</div>
                    <h5 class="fw-bold mb-1 mb-md-2 mt-2 mt-md-3">Login NIS</h5>
                    <p class="text-muted mb-0 small">Masuk dengan akun NIS & tanggal lahir resmi terverifikasi.</p>
                </div>
            </div>
            <div class="col-4 col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="tutorial-card p-2 p-md-4 text-center h-100">
                    <div class="tutorial-number" style="background: var(--accent); color: white;">2</div>
                    <h5 class="fw-bold mb-1 mb-md-2 mt-2 mt-md-3">Beri Nilai</h5>
                    <p class="text-muted mb-0 small">Pilih guru Normada/Produktif, beri nilai (1-5) pada 6 kriteria.</p>
                </div>
            </div>
            <div class="col-4 col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="tutorial-card p-2 p-md-4 text-center h-100">
                    <div class="tutorial-number" style="background: var(--secondary); color: var(--text-dark);">3</div>
                    <h5 class="fw-bold mb-1 mb-md-2 mt-2 mt-md-3">Kirim Anonim</h5>
                    <p class="text-muted mb-0 small">Data tersimpan aman & anonim untuk perbaikan pengajaran.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 5. TENTANG KAMI - VISI MISI (id="tentang") --}}
<section id="tentang" class="section-padding" style="background: var(--bg-light);">
    <div class="container">
        <div class="text-center mb-4 mb-md-5" data-aos="fade-up">
            <div class="section-label">TENTANG KAMI</div>
            <h2 class="section-title">Mengapa {{ \App\Models\Setting::get('site_title', 'GuruKuu') }} Ada?</h2>
            <p class="text-muted" style="max-width: 700px; margin: 0 auto; font-size: 0.9rem;">
                Platform evaluasi terintegrasi untuk SMK yang membangun jembatan komunikasi positif antara siswa, guru, dan manajemen sekolah.
            </p>
        </div>

        <div class="row g-2 g-md-4 mb-3 mb-md-4">
            <div class="col-12 col-md-6" data-aos="fade-right">
                <div class="card-custom gk-vm-card p-3 p-md-4 h-100" style="border-top: 4px solid var(--primary);">
                    <div class="d-flex align-items-center mb-2 mb-md-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-2.5 me-md-3 gk-vm-icon" style="width: 40px; height: 40px; background: var(--primary); color: white; flex-shrink: 0;">
                            <i class="bi bi-bullseye fs-5"></i>
                        </div>
                        <h5 class="fw-bold mb-0">Visi Kami</h5>
                    </div>
                    <p class="text-muted mb-0" style="line-height: 1.6; font-size: 0.88rem;">
                        {{ \App\Models\Setting::get('visi_text', 'Menjadi standar nasional dalam evaluasi pengajaran berbasis data untuk menciptakan ekosistem pendidikan yang responsif, transparan, dan berkelanjutan di seluruh SMK Indonesia.') }}
                    </p>
                </div>
            </div>
            <div class="col-12 col-md-6" data-aos="fade-left">
                <div class="card-custom gk-vm-card p-3 p-md-4 h-100" style="border-top: 4px solid var(--accent);">
                    <div class="d-flex align-items-center mb-2 mb-md-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-2.5 me-md-3 gk-vm-icon" style="width: 40px; height: 40px; background: var(--accent); color: white; flex-shrink: 0;">
                            <i class="bi bi-rocket-takeoff fs-5"></i>
                        </div>
                        <h5 class="fw-bold mb-0">Misi Kami</h5>
                    </div>
                    @php
                        $misiRaw = \App\Models\Setting::get('misi_text', "Memberikan saluran aspirasi yang aman dan anonim bagi siswa.\nMenyediakan data analitik yang dapat ditindaklanjuti oleh manajemen sekolah.\nMendorong pengembangan profesional guru secara berkelanjutan.");
                        $misiList = array_filter(array_map('trim', explode("\n", $misiRaw)));
                    @endphp
                    <ul class="text-muted mb-0 ps-3" style="line-height: 1.7; font-size: 0.88rem;">
                        @foreach($misiList as $misiItem)
                            <li>{{ ltrim($misiItem, '-*• ') }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="row g-2 g-md-4">
            <div class="col-4 col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card-custom gk-feature-card p-2.5 p-md-4 text-center h-100">
                    <i class="bi bi-shield-check fs-2 mb-2" style="color: var(--primary);"></i>
                    <h5 class="fw-bold mb-1">Anonimitas</h5>
                    <p class="text-muted mb-0 small">Identitas siswa aman dengan enkripsi tanpa tekanan.</p>
                </div>
            </div>
            <div class="col-4 col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card-custom gk-feature-card p-2.5 p-md-4 text-center h-100">
                    <i class="bi bi-graph-up-arrow fs-2 mb-2" style="color: var(--accent);"></i>
                    <h5 class="fw-bold mb-1">Berbasis Data</h5>
                    <p class="text-muted mb-0 small">Data statistik valid & terukur untuk setiap apresiasi.</p>
                </div>
            </div>
            <div class="col-4 col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card-custom gk-feature-card p-2.5 p-md-4 text-center h-100">
                    <i class="bi bi-people-fill fs-2 mb-2" style="color: var(--secondary);"></i>
                    <h5 class="fw-bold mb-1">Kolaboratif</h5>
                    <p class="text-muted mb-0 small">Membangun komunikasi positif siswa, guru, & sekolah.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection