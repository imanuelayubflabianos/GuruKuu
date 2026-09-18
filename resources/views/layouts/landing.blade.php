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
        
        .navbar-custom {
            background: rgba(255,255,255,0.98); backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border); padding: 0.75rem 0;
            transition: all 0.3s; z-index: 1030;
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
            background: var(--primary); color: white; padding: 0.5rem 1.25rem;
            border-radius: 8px; font-weight: 600; font-size: 0.9rem; border: none; text-decoration: none; transition: all 0.3s;
        }
        .btn-masuk:hover { background: var(--primary-light); color: white; transform: translateY(-1px); }
        .btn-dashboard {
            background: var(--accent); color: white; padding: 0.5rem 1.25rem;
            border-radius: 8px; font-weight: 600; font-size: 0.9rem; border: none; text-decoration: none; transition: all 0.3s;
        }
        .btn-dashboard:hover { background: #008f5a; color: white; transform: translateY(-1px); }
        .btn-logout {
            background: #dc3545; color: white; padding: 0.5rem 1rem;
            border-radius: 8px; font-weight: 600; font-size: 0.85rem; border: none; cursor: pointer; transition: all 0.3s;
        }
        .btn-logout:hover { background: #c82333; color: white; }

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
        .hero-title { font-size: 3.2rem; font-weight: 900; line-height: 1.15; margin-bottom: 1.25rem; letter-spacing: -0.5px; }
        .hero-subtitle { font-size: 1.15rem; opacity: 0.92; margin-bottom: 2.25rem; max-width: 640px; line-height: 1.7; }
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

        .footer-custom { background: var(--bg-light); padding: 3.5rem 0 2rem; border-top: 1px solid var(--border); }
        .footer-title { font-weight: 800; color: var(--primary); font-size: 1.35rem; margin-bottom: 0.75rem; }
        .footer-link { color: var(--text-muted); text-decoration: none; display: inline-block; margin-bottom: 0.4rem; font-size: 0.88rem; transition: color 0.2s; }
        .footer-link:hover { color: var(--primary); }
        .footer-label { font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; font-weight: 600; letter-spacing: 2px; color: var(--text-dark); margin-bottom: 0.75rem; text-transform: uppercase; }

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
            <a class="navbar-brand navbar-brand-custom d-flex align-items-center gap-2" href="{{ route('landing.index') }}">
                @if(!empty($siteLogo))
                    <img src="{{ $siteLogo }}" alt="Logo" style="height: 36px; max-width: 45px; object-fit: contain;">
                @else
                    <i class="bi bi-mortarboard-fill fs-3" style="color: {{ $siteTitleColor1 }};"></i>
                @endif
                <span class="fs-4 fw-bold">
                    <span style="color: {{ $siteTitleColor1 }};">{{ $siteTitlePart1 }}</span><span style="color: {{ $siteTitleColor2 }};">{{ $siteTitlePart2 }}</span>
                </span>
            </a>
            <div class="d-flex align-items-center gap-1.5 d-lg-none ms-auto me-2">
                <button class="btn btn-light border p-1.5 rounded-circle shadow-sm" type="button" onclick="GuruKuuTheme.toggleTheme()" title="Mode Gelap / Terang">
                    <i class="bi bi-moon-stars-fill gk-theme-icon" style="font-size: 0.95rem;"></i>
                </button>
                <button class="btn btn-light border p-1.5 rounded-circle shadow-sm" type="button" onclick="GuruKuuTheme.openDisplayModal()" title="Pengaturan Grafis & Performa">
                    <i class="bi bi-sliders2-vertical text-primary" style="font-size: 0.95rem;"></i>
                </button>
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
        <button type="submit" class="btn btn-danger btn-sm">
            <i class="bi bi-box-arrow-right me-1"></i> Keluar
        </button>
    </form>
@else
    <a href="{{ route('login') }}" class="btn btn-primary">
        <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
    </a>
@endauth

    {{-- THEME & DISPLAY CONTROLS --}}
    <div class="d-flex align-items-center gap-1.5 ms-2 ps-2 border-start">
        <button class="btn btn-light border p-1.5 rounded-circle shadow-sm" type="button" onclick="GuruKuuTheme.toggleTheme()" title="Mode Gelap / Terang">
            <i class="bi bi-moon-stars-fill gk-theme-icon" style="font-size: 0.95rem;"></i>
        </button>
        <button class="btn btn-light border p-1.5 rounded-circle shadow-sm" type="button" onclick="GuruKuuTheme.openDisplayModal()" title="Pengaturan Grafis & Performa">
            <i class="bi bi-sliders2-vertical text-primary" style="font-size: 0.95rem;"></i>
        </button>
    </div>
                </div>
            </div>
        </div>
    </nav>

    @yield('content')

    <footer class="footer-custom">
        <div class="container">
            <div class="row align-items-start">
                <div class="col-lg-7 mb-4">
                    <div class="footer-title d-flex align-items-center gap-2">
                        @if($logo = \App\Models\Setting::get('site_logo'))
                            <img src="{{ $logo }}" alt="Logo" style="height: 32px; object-fit: contain;">
                        @endif
                        <span>{{ \App\Models\Setting::get('site_title', 'GuruKuu') }}</span>
                    </div>
                    <p class="text-muted mb-0" style="font-size: 0.9rem; max-width: 500px;">
                        {{ \App\Models\Setting::get('footer_about', 'Sistem Manajemen Penilaian Guru Berbasis Siswa untuk SMK N 1 Bangsri.') }}
                    </p>
                </div>
                <div class="col-lg-5 mb-4 text-lg-end">
                    <div class="footer-label">LEGAL & BANTUAN</div>
                    <a href="{{ route('legal.privacy') }}" class="footer-link me-3">Kebijakan Privasi</a>
                    <a href="{{ route('legal.terms') }}" class="footer-link me-3">Syarat & Ketentuan</a>
                    <a href="{{ route('kontak.guest.page') }}" class="footer-link">Hubungi Admin Operator Sekolah</a>

                </div>
            </div>
            <hr class="border-secondary my-4">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="text-muted mb-0" style="font-size: 0.85rem;">
                        &copy; {{ date('Y') }} {{ \App\Models\Setting::get('site_title', 'GuruKuu') }}. {{ \App\Models\Setting::get('footer_copyright', 'All rights reserved.') }}
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    
    <script>
        AOS.init({ duration: 800, once: true });

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
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/gurukuu-modal.js') }}"></script>
</body>
</html>