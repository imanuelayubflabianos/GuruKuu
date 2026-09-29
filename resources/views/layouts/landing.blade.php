<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $siteTitle ?? 'GuruKuu') - {{ $siteTitle ?? 'GuruKuu' }}</title>
    @if(!empty($siteLogo))
        <link rel="icon" href="{{ $siteLogo }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <style>
        html { scroll-behavior: smooth; }
        :root {
            --primary: #003366; --primary-light: #004080; --secondary: #FFC107;
            --accent: #00A86B; --bg-light: #f5f7fa; --text-dark: #1a1a2e; --text-muted: #64748b; --border: #e2e8f0;
        }
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        /* Safety fallback: ensure elements with data-aos are never permanently hidden if AOS CDN or script delays */
        html:not(.aos-init) [data-aos] {
            opacity: 1 !important;
            transform: none !important;
            visibility: visible !important;
        }
        
        .navbar-custom {
            background: rgba(246, 248, 251, 0.98) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 2px 14px rgba(15, 23, 42, 0.05);
            padding: 0.85rem 0;
            transition: all 0.3s;
            z-index: 1030;
        }
        .navbar-brand-custom { font-weight: 800; font-size: 1.5rem; color: var(--primary); margin-right: 2rem; text-decoration: none; }
        .nav-menu-center { flex: 1; display: flex; justify-content: center; gap: 2rem; }
        .nav-link-custom {
            font-weight: 600; color: var(--text-dark); text-decoration: none;
            padding: 0.5rem 0; position: relative; transition: color 0.2s ease; font-size: 0.95rem; cursor: pointer;
        }
        .nav-link-custom:hover { color: var(--primary); }
        .nav-link-custom.active { color: var(--primary) !important; font-weight: 700; }
        .nav-link-custom::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 0;
            height: 3px;
            background: var(--primary);
            border-radius: 3px;
            transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .nav-link-custom:hover::after {
            width: 50%;
            background: rgba(0, 51, 102, 0.4);
        }
        .nav-link-custom.active::after {
            width: 100% !important;
            background: var(--primary) !important;
        }
        .nav-actions { display: flex; align-items: center; margin-left: auto; gap: 0.75rem; }
        .btn-masuk {
            background: var(--primary); color: white; padding: 0.55rem 1.35rem;
            border-radius: 8px; font-weight: 600; font-size: 0.88rem; border: none; text-decoration: none; transition: all 0.25s ease;
            display: inline-flex; align-items: center; gap: 0.45rem;
            box-shadow: 0 2px 8px rgba(0, 51, 102, 0.2);
        }
        .btn-masuk:hover { background: var(--primary-light); color: white; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(0, 51, 102, 0.3); }
        .btn-dashboard {
            background: var(--primary); color: white; padding: 0.55rem 1.25rem;
            border-radius: 8px; font-weight: 600; font-size: 0.88rem; border: none; text-decoration: none; transition: all 0.25s ease;
            display: inline-flex; align-items: center; gap: 0.45rem;
            box-shadow: 0 2px 8px rgba(0, 51, 102, 0.2);
        }
        .btn-dashboard:hover { background: var(--primary-light); color: white; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(0, 51, 102, 0.3); }
        .btn-logout {
            background: rgba(225, 29, 72, 0.08); color: #e11d48; padding: 0.55rem 1.15rem;
            border-radius: 8px; font-weight: 600; font-size: 0.88rem; border: 1px solid rgba(225, 29, 72, 0.25); cursor: pointer; transition: all 0.25s ease;
            display: inline-flex; align-items: center; gap: 0.45rem;
        }
        .btn-logout:hover { background: #e11d48; color: white; border-color: #e11d48; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(225, 29, 72, 0.25); }

        .hero-section {
            background: linear-gradient(rgba(10, 25, 47, 0.8), rgba(0, 51, 102, 0.75)), url('https://images.unsplash.com/photo-1562774053-701939374585?w=1920') center/cover no-repeat;
            color: white; padding: 170px 0 130px; min-height: 620px; display: flex; align-items: center;
            position: relative;
        }
        .hero-glass-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.45rem 1.15rem;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #f8fafc;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 1px;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }
        .hero-title { font-size: 3.2rem; font-weight: 900; line-height: 1.15; margin-bottom: 1.25rem; letter-spacing: -0.5px; color: #ffffff !important; }
        .hero-subtitle { font-size: 1.15rem; opacity: 0.95; margin-bottom: 2.25rem; max-width: 640px; line-height: 1.7; color: rgba(255, 255, 255, 0.92) !important; }
        .btn-cta {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #0f172a !important;
            padding: 0.9rem 2.25rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.35);
            box-shadow: 0 8px 24px -4px rgba(245, 158, 11, 0.5);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-cta:hover {
            background: linear-gradient(135deg, #fbbf24 0%, #b45309 100%);
            transform: translateY(-3px);
            box-shadow: 0 14px 30px -4px rgba(245, 158, 11, 0.65);
            color: #000000 !important;
        }

        .stats-section { padding: 5.5rem 0; background: var(--bg-light); }
        .stat-card-modern {
            background: var(--bg-card);
            border-radius: 18px;
            padding: 2rem 1.5rem;
            text-align: center;
            border: 1px solid var(--border);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            position: relative;
        }
        .stat-card-modern:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 32px rgba(0, 51, 102, 0.09);
            border-color: rgba(0, 51, 102, 0.25);
        }
        .stat-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            font-size: 1.75rem;
            transition: transform 0.3s ease;
        }
        .stat-card-modern:hover .stat-icon {
            transform: scale(1.08);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--text-dark);
            line-height: 1.1;
            margin-bottom: 0.5rem;
            font-feature-settings: "tnum";
            font-variant-numeric: tabular-nums;
        }
        .stat-label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--text-muted);
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .section-padding { padding: 4.5rem 0; }
        .section-label { font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; font-weight: 600; color: var(--primary); letter-spacing: 2px; text-transform: uppercase; margin-bottom: 0.5rem; }
        .section-title { font-size: clamp(1.5rem, 3.5vw, 2rem); font-weight: 800; color: var(--text-dark); margin-bottom: 0.75rem; }
        .card-custom { background: white; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 2px 8px rgba(0,0,0,0.04); }

        .tutorial-card {
            background: white; border-radius: 12px; border: 1px solid var(--border);
            box-shadow: 0 4px 20px rgba(0,0,0,0.06); transition: all 0.3s;
        }
        .tutorial-card:hover { transform: translateY(-5px); box-shadow: 0 8px 30px rgba(0,0,0,0.12); }
        .tutorial-number {
            width: 52px; height: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            margin: 0 auto; font-size: 1.4rem; font-weight: 900; box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }

        .footer-custom { 
            background: linear-gradient(180deg, #1c2438 0%, #151d30 100%) !important; 
            color: #e2e8f0 !important;
            padding: 4.5rem 0 2.2rem; 
            border-top: 1px solid rgba(255, 255, 255, 0.08); 
            position: relative;
        }
        [data-theme="dark"] .footer-custom,
        html.dark-theme .footer-custom,
        body.dark-theme .footer-custom {
            background: linear-gradient(180deg, #131929 0%, #0d1220 100%) !important;
            border-top-color: rgba(255, 255, 255, 0.06);
        }
        .footer-brand-title {
            font-weight: 800;
            color: #ffffff;
            font-size: 1.5rem;
            letter-spacing: -0.5px;
            margin-bottom: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .footer-col-title {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: #ffffff;
            margin-bottom: 1.25rem;
            text-transform: uppercase;
        }
        .footer-links-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .footer-links-list li {
            margin-bottom: 0.75rem;
        }
        .footer-nav-link {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .footer-nav-link i.arrow-icon {
            font-size: 0.75rem;
            color: #64748b;
            transition: transform 0.25s ease, color 0.25s ease;
        }
        .footer-nav-link:hover {
            color: #38bdf8;
            transform: translateX(4px);
        }
        .footer-nav-link:hover i.arrow-icon {
            color: #38bdf8;
            transform: translateX(2px);
        }
        .footer-contact-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            color: #94a3b8;
            font-size: 0.88rem;
            margin-bottom: 0.85rem;
            line-height: 1.5;
        }
        .footer-contact-item i {
            color: #f59e0b;
            font-size: 1.1rem;
            flex-shrink: 0;
            margin-top: 0.15rem;
        }
        .footer-bottom-bar {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 1.8rem;
            margin-top: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .btn-floating-top {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: rgba(0, 78, 167, 1);
            color: #ffffffff;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            z-index: 1045;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), background 0.2s ease;
        }
        .btn-floating-top.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        .btn-floating-top:hover {
            background: #b91c1c;
            color: #ffffff;
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(220, 38, 38, 0.55);
        }

        .btn-primary-custom { background: var(--primary); color: white; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; border: none; text-decoration: none; display: inline-block; }
        .btn-primary-custom:hover { background: var(--primary-light); color: white; }

        @media (max-width: 768px) {
            .hero-title { font-size: 1.75rem; }
            .nav-menu-center { flex-direction: column; gap: 0.75rem; align-items: flex-start; padding-left: 0.75rem; }
            .nav-actions { margin-top: 0.75rem; width: 100%; justify-content: center; }
            .stat-number { font-size: 1.4rem; }
            .text-lg-end { text-align: left !important; }
            .section-padding { padding: 2.25rem 0; }
        }
    </style>
    <link href="{{ asset('css/gurukuu-theme.css') }}" rel="stylesheet">
    <script src="{{ asset('js/gurukuu-theme.js') }}"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <div class="d-flex align-items-center gap-2">
                <a class="navbar-brand navbar-brand-custom d-flex align-items-center gap-2 m-0" href="{{ route('landing.index') }}">
                    @if(!empty($siteLogo))
                        <img src="{{ $siteLogo }}" alt="{{ $siteTitle ?? 'GuruKuu' }}" style="height: 36px; max-width: 45px; object-fit: contain;">
                    @else
                        <i class="bi bi-mortarboard-fill fs-3" style="color: {{ $siteTitleColor1 ?? '#003366' }};"></i>
                    @endif
                    <span class="fs-4 fw-bold">
                        <span style="color: {{ $siteTitleColor1 ?? '#003366' }} !important;">{{ $siteTitlePart1 ?? 'Guru' }}</span><span style="color: {{ $siteTitleColor2 ?? '#FFC107' }} !important;">{{ $siteTitlePart2 ?? 'Kuu' }}</span>
                    </span>
                </a>
                <a href="{{ config('services.sipintu.base_url', 'https://sipintu.smkn1bangsri.sch.id') }}" 
                   target="_blank" rel="noopener noreferrer"
                   class="btn btn-light border p-1 rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                   style="width: 36px; height: 36px;"
                   title="Portal SiPintu">
                    <img src="{{ asset('images/sipintu-logo.png') }}" alt="SiPintu" style="height: 22px; width: 22px; object-fit: contain;">
                </a>
            </div>
            <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navMenu"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse" id="navMenu">
                <div class="nav-menu-center">
                    <a class="nav-link nav-link-custom {{ request()->routeIs('landing.index') ? 'active' : '' }}" data-nav-target="home" href="{{ route('landing.index') }}#home">Beranda</a>
                    <a class="nav-link nav-link-custom" data-nav-target="guru" href="{{ route('landing.index') }}#guru">Guru</a>
                    <a class="nav-link nav-link-custom" data-nav-target="panduan" href="{{ route('landing.index') }}#panduan">Panduan</a>
                    <a class="nav-link nav-link-custom" data-nav-target="tentang" href="{{ route('landing.index') }}#tentang">Tentang</a>
                </div>
                <div class="nav-actions">
                    @auth
                        @php
                            $userRole = Auth::user()->role;
                            $dashboardUrl = match($userRole) {
                                'admin' => route('admin.dashboard'),
                                'guru' => route('guru.dashboard'),
                                'siswa' => route('siswa.dashboard'),
                                default => route('landing.index')
                            };
                            $dashboardLabel = match($userRole) {
                                'admin' => 'Dashboard Admin',
                                'guru' => 'Dashboard Guru',
                                'siswa' => 'Dashboard Siswa',
                                default => 'Dashboard'
                            };
                        @endphp
                        
                        <a href="{{ $dashboardUrl }}" class="btn btn-dashboard">
                            <i class="bi bi-speedometer2 me-1"></i> {{ $dashboardLabel }}
                        </a>
                        
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                        <form action="{{ route('logout') }}" method="POST" class="d-inline" data-confirm="Apakah Anda yakin ingin keluar dari sistem?" data-confirm-title="Konfirmasi Logout" data-confirm-btn="Ya, Logout" data-confirm-type="danger">
                            @csrf
                            <button type="submit" class="btn-logout">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn-masuk">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span>Masuk</span>
                        </a>
                    @endauth

                </div>
            </div>
        </div>
    </nav>

    @yield('content')

    <footer class="footer-custom">
        <div class="container">
            <div class="row g-4 align-items-start">
                {{-- KOLOM 1: BRANDING & DESKRIPSI --}}
                <div class="col-lg-5 col-md-6 mb-4 mb-lg-0">
                    <div class="footer-brand-title d-flex align-items-center gap-2">
                        @if(!empty($siteLogo))
                            <img src="{{ $siteLogo }}" alt="{{ $siteTitle ?? 'GuruKuu' }}" style="height: 38px; max-width: 45px; object-fit: contain;">
                        @else
                            <i class="bi bi-mortarboard-fill fs-3" style="color: {{ $siteTitleColor1 ?? '#003366' }};"></i>
                        @endif
                        <span class="fs-4 fw-bold">
                            <span style="color: {{ $siteTitleColor1 ?? '#003366' }} !important;">{{ $siteTitlePart1 ?? 'Guru' }}</span><span style="color: {{ $siteTitleColor2 ?? '#FFC107' }} !important;">{{ $siteTitlePart2 ?? 'Kuu' }}</span>
                        </span>
                    </div>
                    <p class="text-white-50 mb-0" style="font-size: 0.9rem; line-height: 1.65; max-width: 420px;">
                        {{ \App\Models\Setting::get('footer_about', 'Sistem Informasi Evaluasi dan Apresiasi Kinerja Pendidik berbasis siswa yang transparan, objektif, dan terpercaya bagi kemajuan pendidikan SMK Negeri 1 Bangsri.') }}
                    </p>
                </div>

                {{-- KOLOM 2: LAYANAN & LEGAL --}}
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="footer-col-title">LAYANAN</div>
                    <ul class="footer-links-list">
                        <li>
                            <a href="{{ route('legal.privacy') }}" class="footer-nav-link">
                                <i class="bi bi-chevron-right arrow-icon"></i>
                                <span>Kebijakan Privasi</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('legal.terms') }}" class="footer-nav-link">
                                <i class="bi bi-chevron-right arrow-icon"></i>
                                <span>Syarat & Ketentuan</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('kontak.guest.page') }}" class="footer-nav-link">
                                <i class="bi bi-chevron-right arrow-icon"></i>
                                <span>Hubungi Admin</span>
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- KOLOM 3: KONTAK --}}
                <div class="col-lg-4 col-md-12">
                    <div class="footer-col-title">KONTAK</div>
                    <div class="footer-contact-item">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>Jl. KH. Achmad Fauzan No.17, Krasak, Bangsri, Kab. Jepara, Jawa Tengah 59453</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="bi bi-instagram"></i>
                        <a href="https://instagram.com/smkn1bangsri" target="_blank" class="footer-nav-link text-white-50">
                            <span>@smkn1bangsri</span>
                        </a>
                    </div>
                    <div class="footer-contact-item">
                        <i class="bi bi-envelope-fill"></i>
                        <a href="mailto:smkn1bangsri@yahoo.co.id" class="footer-nav-link text-white-50">
                            <span>smkn1bangsri@yahoo.co.id</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- BOTTOM BAR (CENTERED COPYRIGHT) --}}
            <div class="footer-bottom-bar text-center justify-content-center">
                <div class="text-white-50 small mb-0 w-100 text-center">
                    @php
                        $rawCopyright = \App\Models\Setting::get('footer_copyright', 'Hak Cipta Dilindungi.');
                        if (str_contains($rawCopyright, '©') || str_contains($rawCopyright, '&copy;')) {
                            $finalCopyright = $rawCopyright;
                        } else {
                            $finalCopyright = '&copy; ' . date('Y') . ' ' . \App\Models\Setting::get('site_title', 'GuruKuu') . ' - SMK Negeri 1 Bangsri. ' . $rawCopyright;
                        }
                    @endphp
                    {!! $finalCopyright !!}
                </div>
            </div>
        </div>
    </footer>

    {{-- FLOATING BACK TO TOP BUTTON --}}
    <button type="button" id="btnBackToTop" class="btn-floating-top" title="Kembali ke atas" aria-label="Kembali ke atas">
        <i class="bi bi-arrow-up"></i>
    </button>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof AOS !== 'undefined') {
                AOS.init({ duration: 800, once: true });
            }
        });
    </script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.nav-link-custom');
            const navbar = document.querySelector('.navbar-custom');
            const navbarHeight = navbar ? navbar.offsetHeight : 80;
            
            const guruSection = document.getElementById('guru');
            const panduanSection = document.getElementById('panduan');
            const tentangSection = document.getElementById('tentang');
            const homeSection = document.getElementById('home');
            
            const isLandingPage = (guruSection !== null && homeSection !== null);

            function getTarget(link) {
                if (link.dataset.navTarget) return link.dataset.navTarget;
                const href = link.getAttribute('href') || '';
                const hashIndex = href.indexOf('#');
                return hashIndex !== -1 ? href.substring(hashIndex + 1) : '';
            }

            function setActiveLink(targetId) {
                navLinks.forEach(link => {
                    if (getTarget(link) === targetId) {
                        link.classList.add('active');
                    } else {
                        link.classList.remove('active');
                    }
                });
            }

            function updateActiveNav() {
                if (!isLandingPage) {
                    const path = window.location.pathname;
                    if (path.includes('/guru') || path.includes('/leaderboard')) {
                        setActiveLink('guru');
                    } else if (path.includes('/kontak') || path.includes('/panduan')) {
                        setActiveLink('panduan');
                    } else {
                        setActiveLink('home');
                    }
                    return;
                }

                // Check bottom of page
                if ((window.innerHeight + window.scrollY) >= (document.documentElement.scrollHeight - 80)) {
                    setActiveLink('tentang');
                    return;
                }

                const scrollPos = window.scrollY + navbarHeight + 80;
                let activeId = 'home';

                if (tentangSection && scrollPos >= tentangSection.offsetTop) {
                    activeId = 'tentang';
                } else if (panduanSection && scrollPos >= panduanSection.offsetTop) {
                    activeId = 'panduan';
                } else if (guruSection && scrollPos >= guruSection.offsetTop) {
                    activeId = 'guru';
                } else {
                    activeId = 'home';
                }

                setActiveLink(activeId);
            }

            let ticking = false;
            window.addEventListener('scroll', function() {
                if (!ticking) {
                    window.requestAnimationFrame(function() { 
                        updateActiveNav(); 
                        ticking = false; 
                    });
                    ticking = true;
                }
            });

            // Initial call
            updateActiveNav();

            // Smooth scroll click handler
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const targetId = getTarget(this);
                    if (targetId && isLandingPage) {
                        const targetElem = document.getElementById(targetId);
                        if (targetElem) {
                            e.preventDefault();
                            const topPos = targetElem.offsetTop - navbarHeight + 2;
                            window.scrollTo({ top: Math.max(0, topPos), behavior: 'smooth' });
                            setActiveLink(targetId);
                            const navCollapse = document.getElementById('navMenu');
                            if (navCollapse && navCollapse.classList.contains('show')) {
                                new bootstrap.Collapse(navCollapse).hide();
                            }
                        }
                    }
                });
            });

            // Floating Back-to-Top button listener
            const btnTop = document.getElementById('btnBackToTop');
            if (btnTop) {
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 300) {
                        btnTop.classList.add('show');
                    } else {
                        btnTop.classList.remove('show');
                    }
                });
                btnTop.addEventListener('click', function() {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/gurukuu-modal.js') }}"></script>
    @stack('scripts')
</body>
</html>