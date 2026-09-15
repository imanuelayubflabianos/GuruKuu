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
<section id="home" class="hero-section" style="background: linear-gradient(rgba(0,51,102,0.85), rgba(0,51,102,0.7)), url('{{ $heroImage }}') center/cover no-repeat;">
    <div class="container">
        <div class="row">
            <div class="col-lg-8" data-aos="fade-right">
                <h1 class="hero-title">{{ $heroTitle }}</h1>
                <p class="hero-subtitle">{{ $heroSubtitle }}</p>
                <a href="{{ $heroCtaUrl }}" class="btn btn-cta">{{ $heroCtaText }}</a>
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
            <p class="text-muted" style="max-width: 600px; margin: 0 auto;">Statistik real-time dari sistem penilaian GuruKuu</p>
        </div>
        <div class="row g-4">
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-card-modern" style="border-left: 4px solid var(--primary);">
                    <div class="stat-icon" style="background: rgba(0,51,102,0.1); color: var(--primary);"><i class="bi bi-person-badge-fill"></i></div>
                    <div class="stat-number">{{ $totalGuru ?? 0 }}</div>
                    <div class="stat-label">Total Guru</div>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-card-modern" style="border-left: 4px solid var(--accent);">
                    <div class="stat-icon" style="background: rgba(0,168,107,0.1); color: var(--accent);"><i class="bi bi-people-fill"></i></div>
                    <div class="stat-number">{{ $totalSiswa ?? 0 }}</div>
                    <div class="stat-label">Siswa Terdaftar</div>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-card-modern" style="border-left: 4px solid var(--secondary);">
                    <div class="stat-icon" style="background: rgba(255,193,7,0.15); color: #d4a017;"><i class="bi bi-star-fill"></i></div>
                    <div class="stat-number">{{ $totalPenilaian ?? 0 }}</div>
                    <div class="stat-label">Total Penilaian</div>
                </div>
            </div>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-card-modern" style="border-left: 4px solid #6366f1;">
                    <div class="stat-icon" style="background: rgba(99,102,241,0.1); color: #6366f1;"><i class="bi bi-calendar-check-fill"></i></div>
                    @if(isset($periodeAktif) && $periodeAktif)
                        <div class="stat-number" style="font-size: 1.5rem; line-height: 1.3;">
                            {{ $periodeAktif->semester === 'ganjil' ? 'Ganjil' : 'Genap' }}
                            <div style="font-size: 1rem; font-weight: 600; margin-top: 0.25rem;">{{ $periodeAktif->tahun_ajaran }}</div>
                        </div>
                    @else
                        <div class="stat-number" style="font-size: 1.5rem;">Belum Aktif</div>
                    @endif
                    <div class="stat-label">Periode Saat Ini</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 3. GURU FAVORIT / LEADERBOARD (id="guru") - MENGGUNAKAN PERSENTASE PARTISIPASI --}}
<section id="guru" class="section-padding" style="background: var(--bg-light);">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="section-label">PENCAPAIAN TERBAIK</div>
            <h2 class="section-title">Guru dengan Partisipasi Tertinggi</h2>
            <p class="text-muted">Guru dengan persentase partisipasi penilaian tertinggi dari siswa</p>
        </div>

        @php
            // Ambil guru dengan persentase kepuasan / partisipasi tertinggi secara keseluruhan
            $allTeachers = \App\Models\Guru::with('jurusan')->where('total_penilaian', '>', 0)->get();
            if ($allTeachers->isEmpty()) {
                $allTeachers = \App\Models\Guru::with('jurusan')->get();
            }
            
            $topList = $allTeachers->map(function($guru) {
                $guru->persentase = round(($guru->rata_rata_nilai / 5) * 100);
                return $guru;
            })->sortByDesc('rata_rata_nilai')->take(3)->values();
        @endphp

        <div class="row g-4 justify-content-center align-items-end mb-5">
            @if($topList->count() > 0)
                @if($topList->count() > 1)
                <div class="col-md-3 order-md-1" data-aos="fade-right">
                    <div class="card-custom p-4 text-center" style="border: 1px solid var(--border);">
                        <div class="position-relative d-inline-block mb-3">
                            <img src="{{ $topList[1]->photo_url }}" class="rounded-circle" width="85" height="85" style="object-fit: cover; border: 3px solid #C0C0C0;">
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background: #C0C0C0; color: #fff; font-size: 0.9rem; padding: 0.5rem 0.75rem;">#2</span>
                        </div>
                        <h6 class="fw-bold mb-1">{{ $topList[1]->nama }}</h6>
                        <div class="font-mono mb-2" style="font-size: 0.7rem; color: var(--text-muted);">{{ strtoupper($topList[1]->jurusan?->nama_jurusan ?? 'UMUM') }}</div>
                        <div class="mb-2">
                            <div class="fw-bold text-primary" style="font-size: 1.4rem;">{{ $topList[1]->persentase }}%</div>
                            <small class="text-muted">{{ $topList[1]->total_penilaian }} ulasan</small>
                        </div>
                    </div>
                </div>
                @endif

                @if($topList->count() > 0)
                <div class="col-md-4 order-md-2 mt-md-4" data-aos="zoom-in">
                    <div class="card-custom p-5 text-center" style="background: var(--primary); border: none; color: white;">
                        <div class="position-relative d-inline-block mb-3">
                            <img src="{{ $topList[0]->photo_url }}" class="rounded-circle" width="115" height="115" style="object-fit: cover; border: 4px solid var(--secondary);">
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background: #FFD700; color: #000; font-size: 1rem; padding: 0.6rem 0.85rem;">#1</span>
                        </div>
                        <h5 class="fw-bold mb-1 text-white">{{ $topList[0]->nama }}</h5>
                        <div class="font-mono mb-2" style="font-size: 0.75rem; letter-spacing: 1px; color: var(--secondary);">{{ strtoupper($topList[0]->jurusan?->nama_jurusan ?? 'UMUM') }}</div>
                        <div class="mb-2">
                            <div class="fw-bold text-warning" style="font-size: 2.2rem;">{{ $topList[0]->persentase }}%</div>
                            <small style="opacity: 0.85;">{{ $topList[0]->total_penilaian }} ulasan</small>
                        </div>
                    </div>
                </div>
                @endif

                @if($topList->count() > 2)
                <div class="col-md-3 order-md-3" data-aos="fade-left">
                    <div class="card-custom p-4 text-center" style="border: 1px solid var(--border);">
                        <div class="position-relative d-inline-block mb-3">
                            <img src="{{ $topList[2]->photo_url }}" class="rounded-circle" width="85" height="85" style="object-fit: cover; border: 3px solid #CD7F32;">
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background: #CD7F32; color: #fff; font-size: 0.9rem; padding: 0.5rem 0.75rem;">#3</span>
                        </div>
                        <h6 class="fw-bold mb-1">{{ $topList[2]->nama }}</h6>
                        <div class="font-mono mb-2" style="font-size: 0.7rem; color: var(--text-muted);">{{ strtoupper($topList[2]->jurusan?->nama_jurusan ?? 'UMUM') }}</div>
                        <div class="mb-2">
                            <div class="fw-bold text-primary" style="font-size: 1.4rem;">{{ $topList[2]->persentase }}%</div>
                            <small class="text-muted">{{ $topList[2]->total_penilaian }} ulasan</small>
                        </div>
                    </div>
                </div>
                @endif
            @else
                <div class="col-12 text-center text-muted py-5">Belum ada data evaluasi guru.</div>
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
<section id="panduan" class="section-padding" style="background: white;">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="section-label">PANDUAN PENGGUNAAN</div>
            <h2 class="section-title">Bagaimana Cara Memberi Penilaian?</h2>
            <p class="text-muted">Hanya butuh 3 langkah mudah untuk berkontribusi bagi sekolahmu</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="tutorial-card p-4 p-md-5 text-center h-100">
                    <div class="tutorial-number" style="background: var(--primary); color: white;">1</div>
                    <h5 class="fw-bold mb-3 mt-4">Login dengan NIS</h5>
                    <p class="text-muted mb-0 small">Masuk menggunakan akun NIS dan tanggal lahir yang telah terverifikasi oleh sistem sekolah.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="tutorial-card p-4 p-md-5 text-center h-100">
                    <div class="tutorial-number" style="background: var(--accent); color: white;">2</div>
                    <h5 class="fw-bold mb-3 mt-4">Pilih Guru & Beri Nilai</h5>
                    <p class="text-muted mb-0 small">Pilih guru Normada atau Produktif, lalu beri nilai (1-5) pada 6 kriteria pengajaran.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="tutorial-card p-4 p-md-5 text-center h-100">
                    <div class="tutorial-number" style="background: var(--secondary); color: var(--text-dark);">3</div>
                    <h5 class="fw-bold mb-3 mt-4">Kirim Secara Anonim</h5>
                    <p class="text-muted mb-0 small">Data akan tersimpan aman dan anonim. Kritik & saran Anda membantu perbaikan kualitas mengajar.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 5. TENTANG KAMI - VISI MISI (id="tentang") --}}
<section id="tentang" class="section-padding" style="background: var(--bg-light);">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="section-label">TENTANG KAMI</div>
            <h2 class="section-title">Mengapa {{ \App\Models\Setting::get('site_title', 'GuruKuu') }} Ada?</h2>
            <p class="text-muted" style="max-width: 700px; margin: 0 auto;">
                Platform evaluasi terintegrasi untuk SMK yang membangun jembatan komunikasi positif antara siswa, guru, dan manajemen sekolah.
            </p>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-6" data-aos="fade-right">
                <div class="card-custom p-4 p-md-5 h-100" style="border-top: 4px solid var(--primary);">
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px; background: var(--primary); color: white; flex-shrink: 0;">
                            <i class="bi bi-bullseye fs-4"></i>
                        </div>
                        <h4 class="fw-bold mb-0">Visi Kami</h4>
                    </div>
                    <p class="text-muted mb-0" style="line-height: 1.8; font-size: 0.95rem;">
                        {{ \App\Models\Setting::get('visi_text', 'Menjadi standar nasional dalam evaluasi pengajaran berbasis data untuk menciptakan ekosistem pendidikan yang responsif, transparan, dan berkelanjutan di seluruh SMK Indonesia.') }}
                    </p>
                </div>
            </div>
            <div class="col-md-6" data-aos="fade-left">
                <div class="card-custom p-4 p-md-5 h-100" style="border-top: 4px solid var(--accent);">
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 56px; height: 56px; background: var(--accent); color: white; flex-shrink: 0;">
                            <i class="bi bi-rocket-takeoff fs-4"></i>
                        </div>
                        <h4 class="fw-bold mb-0">Misi Kami</h4>
                    </div>
                    @php
                        $misiRaw = \App\Models\Setting::get('misi_text', "Memberikan saluran aspirasi yang aman dan anonim bagi siswa.\nMenyediakan data analitik yang dapat ditindaklanjuti oleh manajemen sekolah.\nMendorong pengembangan profesional guru secara berkelanjutan.");
                        $misiList = array_filter(array_map('trim', explode("\n", $misiRaw)));
                    @endphp
                    <ul class="text-muted mb-0 ps-3" style="line-height: 2; font-size: 0.95rem;">
                        @foreach($misiList as $misiItem)
                            <li>{{ ltrim($misiItem, '-*• ') }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card-custom p-4 p-md-5 text-center h-100">
                    <i class="bi bi-shield-check display-5 mb-3" style="color: var(--primary);"></i>
                    <h5 class="fw-bold mb-3">Anonimitas Terjamin</h5>
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">Identitas siswa dilindungi enkripsi agar penilaian tetap jujur dan tanpa tekanan.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card-custom p-4 p-md-5 text-center h-100">
                    <i class="bi bi-graph-up-arrow display-5 mb-3" style="color: var(--accent);"></i>
                    <h5 class="fw-bold mb-3">Berbasis Data</h5>
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">Setiap keputusan dan apresiasi didasarkan pada data statistik yang valid dan terukur.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card-custom p-4 p-md-5 text-center h-100">
                    <i class="bi bi-people-fill display-5 mb-3" style="color: var(--secondary);"></i>
                    <h5 class="fw-bold mb-3">Kolaboratif</h5>
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">Membangun jembatan komunikasi positif antara siswa, guru, dan manajemen sekolah.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection