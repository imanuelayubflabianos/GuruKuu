<!DOCTYPE html>
<html lang="id" data-theme="light" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ $siteTitle ?? 'GuruKuu' }} Siswa</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('favicon-48x48.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon-192x192.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #003366; --primary-light: #004080; --secondary: #ff6600;
            --accent: #f97316; --bg-light: #f8fafc; --text-dark: #0f172a; --text-muted: #64748b; --border: #e2e8f0;
        }
        * { font-family: 'Inter', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        body { background: var(--bg-light); color: var(--text-dark); overflow-x: hidden; }

        .sidebar { 
            width: 280px; 
            background: white; 
            border-right: 1px solid var(--border); 
            height: 100vh; 
            max-height: 100vh;
            position: fixed; 
            left: 0; 
            top: 0; 
            z-index: 1040; 
            padding: 1.25rem 1rem 1.5rem; 
            display: flex;
            flex-direction: column;
            overflow-y: auto !important;
            overflow-x: hidden;
            scrollbar-width: thin;
        }
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.12); border-radius: 4px; }
        .sidebar-brand { font-weight: 800; font-size: 1.3rem; margin-bottom: 0.25rem; text-decoration: none; display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
        .sidebar-subtitle { font-family: 'JetBrains Mono', monospace; font-size: 0.68rem; color: var(--text-muted); letter-spacing: 1.5px; margin-bottom: 1.25rem; flex-shrink: 0; }
        .sidebar-profile { display: flex; align-items: center; padding: 0.85rem; background: var(--bg-light); border-radius: 12px; margin-bottom: 1.25rem; cursor: pointer; transition: all 0.2s; text-decoration: none; color: inherit; flex-shrink: 0; }
        .sidebar-profile:hover { background: #e9ecef; color: inherit; }
        .sidebar-profile img { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; margin-right: 0.75rem; }
        .sidebar-profile-name { font-weight: 700; font-size: 0.9rem; }
        .sidebar-profile-role { font-family: 'JetBrains Mono', monospace; font-size: 0.65rem; color: var(--text-muted); letter-spacing: 1px; }
        .sidebar-menu { list-style: none; padding: 0; margin: 0; flex: 1 0 auto; }
        .sidebar-menu li { margin-bottom: 0.25rem; }
        .sidebar-menu a { display: flex; align-items: center; padding: 0.7rem 0.9rem; color: var(--text-dark); text-decoration: none; border-radius: 8px; font-weight: 500; font-size: 0.88rem; transition: all 0.2s; }
        .sidebar-menu a i { width: 22px; margin-right: 0.7rem; font-size: 1.05rem; color: var(--text-muted); }
        .sidebar-menu a:hover { background: var(--bg-light); color: var(--primary); }
        .sidebar-menu a.active { background: var(--primary); color: white; }
        .sidebar-menu a.active i { color: white; }
        .main-content { margin-left: 280px; padding: 2rem; min-height: 100vh; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; }
        .page-label { font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; font-weight: 600; color: var(--primary); letter-spacing: 2px; text-transform: uppercase; margin-bottom: 0.25rem; }
        .page-title { font-size: 2rem; font-weight: 800; color: var(--text-dark); margin: 0; }
        .page-subtitle { color: var(--text-muted); font-size: 0.95rem; margin: 0; }

        .card-custom { background: white; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .btn-primary-custom { background: var(--primary); color: white; border: none; padding: 0.6rem 1.25rem; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-block; }
        .btn-primary-custom:hover { background: var(--primary-light); color: white; }
        .btn-outline-custom { background: white; color: var(--primary); border: 1px solid var(--border); padding: 0.6rem 1.25rem; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-block; }
        .table-custom { margin-bottom: 0; }
        .table-custom thead th { font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; font-weight: 600; color: var(--text-muted); letter-spacing: 1px; text-transform: uppercase; border-bottom: 1px solid var(--border); padding: 1rem 1.5rem; }
        .table-custom tbody td { padding: 1rem 1.5rem; vertical-align: middle; border-bottom: 1px solid var(--border); }
        .badge-custom { font-family: 'JetBrains Mono', monospace; font-size: 0.65rem; font-weight: 600; letter-spacing: 1px; padding: 0.35rem 0.75rem; border-radius: 4px; }

        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 1.25rem 0.85rem !important; }
        }

        /* CIRCULAR TOPBAR ACTION BUTTONS */
        .gk-topbar-btn {
            width: 40px !important;
            height: 40px !important;
            min-width: 40px !important;
            min-height: 40px !important;
            max-width: 40px !important;
            max-height: 40px !important;
            aspect-ratio: 1 / 1 !important;
            border-radius: 50% !important;
            padding: 0 !important;
            margin: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
            line-height: 1 !important;
            box-sizing: border-box !important;
            text-decoration: none !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .gk-topbar-btn i { font-size: 1.15rem !important; line-height: 1 !important; display: inline-flex; align-items: center; justify-content: center; }
        .gk-topbar-btn img { width: 22px !important; height: 22px !important; object-fit: contain !important; display: block !important; }
        .gk-topbar-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important; }

        .gk-topbar-btn-sm {
            width: 34px !important;
            height: 34px !important;
            min-width: 34px !important;
            min-height: 34px !important;
            max-width: 34px !important;
            max-height: 34px !important;
            aspect-ratio: 1 / 1 !important;
            border-radius: 50% !important;
            padding: 0 !important;
            margin: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
            line-height: 1 !important;
            box-sizing: border-box !important;
            text-decoration: none !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .gk-topbar-btn-sm i { font-size: 0.95rem !important; line-height: 1 !important; display: inline-flex; align-items: center; justify-content: center; }
        .gk-topbar-btn-sm img { width: 18px !important; height: 18px !important; object-fit: contain !important; display: block !important; }
        .gk-topbar-btn-sm:hover { transform: translateY(-1px); box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08) !important; }
    </style>
    <link href="{{ asset('css/gurukuu-theme.css') }}?v={{ file_exists(public_path('css/gurukuu-theme.css')) ? filemtime(public_path('css/gurukuu-theme.css')) : time() }}" rel="stylesheet">
    <script src="{{ asset('js/gurukuu-theme.js') }}"></script>
</head>
<body>
    @if(request()->routeIs('siswa.dashboard'))
        @include('components.page-loader')
    @endif
    {{-- MOBILE HEADER BAR (KHUSUS HP - BERSIH TANPA TOMBOL BURGER MENU) --}}
    <header class="gk-mobile-header shadow-sm">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('siswa.dashboard') }}" class="text-decoration-none d-flex align-items-center gap-2" style="font-size: 1.1rem;">
                @if(!empty($siteLogo))
                    <img src="{{ $siteLogo }}" alt="{{ $siteTitle ?? 'GuruKuu' }}" style="height: 30px; max-width: 38px; object-fit: contain;">
                @else
                    <i class="bi bi-mortarboard-fill fs-4" style="color: {{ $siteTitleColor1 ?? '#003366' }};"></i>
                @endif
                <span class="fw-bold fs-5">
                    <span style="color: {{ $siteTitleColor1 ?? '#003366' }} !important;">{{ $siteTitlePart1 ?? 'Guru' }}</span><span style="color: {{ $siteTitleColor2 ?? '#FFC107' }} !important;">{{ $siteTitlePart2 ?? 'Kuu' }}</span>
                </span>
            </a>
        </div>
        <div class="d-flex align-items-center gap-2">
            {{-- NOTIFIKASI MOBILE (DISAMPING TITIK TIGA) --}}
            @include('components.user-notif-dropdown', ['prefix' => 'mobile', 'btnClass' => 'gk-topbar-btn-sm'])

            {{-- MENU TITIK TIGA (BERANDA PUBLIK & SIPINTU) --}}
            @include('components.mobile-more-menu')
        </div>
    </header>

    {{-- MOBILE BOTTOM NAVIGATION BAR (KHUSUS SISWA) --}}
    <nav class="gk-bottom-nav d-lg-none" aria-label="Navigasi Bawah Siswa">
        <a href="{{ route('siswa.dashboard') }}" class="gk-bottom-nav-item {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}">
            <i class="bi {{ request()->routeIs('siswa.dashboard') ? 'bi-grid-fill' : 'bi-grid' }}"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('siswa.guru.index') }}" class="gk-bottom-nav-item {{ request()->routeIs('siswa.guru.*') ? 'active' : '' }}">
            <i class="bi {{ request()->routeIs('siswa.guru.*') ? 'bi-person-badge-fill' : 'bi-person-badge' }}"></i>
            <span>Guru</span>
        </a>
        <a href="{{ route('siswa.riwayat') }}" class="gk-bottom-nav-item {{ request()->routeIs('siswa.riwayat') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i>
            <span>Riwayat</span>
        </a>
        <a href="{{ route('siswa.leaderboard.index') }}" class="gk-bottom-nav-item {{ request()->routeIs('siswa.leaderboard.*') ? 'active' : '' }}">
            <i class="bi {{ request()->routeIs('siswa.leaderboard.*') ? 'bi-trophy-fill' : 'bi-trophy' }}"></i>
            <span>Peringkat</span>
        </a>
        <a href="{{ route('siswa.pengaturan') }}" class="gk-bottom-nav-item {{ request()->routeIs('siswa.pengaturan*') ? 'active' : '' }}">
            <i class="bi {{ request()->routeIs('siswa.pengaturan*') ? 'bi-gear-fill' : 'bi-gear' }}"></i>
            <span>Akun</span>
        </a>
    </nav>

    <aside class="sidebar">
        <div class="sidebar-brand">
            <a href="{{ route('siswa.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                @if(!empty($siteLogo))
                    <img src="{{ $siteLogo }}" alt="{{ $siteTitle ?? 'GuruKuu' }}" style="height: 32px; max-width: 45px; object-fit: contain;">
                @else
                    <i class="bi bi-mortarboard-fill fs-4" style="color: {{ $siteTitleColor1 ?? '#003366' }} !important;"></i>
                @endif
                <span class="fs-5 fw-bold brand-logo-text">
                    <span style="color: {{ $siteTitleColor1 ?? '#003366' }} !important;">{{ $siteTitlePart1 ?? 'Guru' }}</span><span style="color: {{ $siteTitleColor2 ?? '#FFC107' }} !important;">{{ $siteTitlePart2 ?? 'Kuu' }}</span>
                </span>
            </a>
            <button type="button" class="btn btn-sm btn-light border d-lg-none rounded-circle" onclick="GuruKuuTheme.closeSidebar()" aria-label="Tutup">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="sidebar-subtitle">SMK NEGERI 1 BANGSRI • SISWA</div>
        
        <a href="{{ route('siswa.pengaturan') }}" class="sidebar-profile">
            @php
                $userInitials = collect(explode(' ', auth()->user()->name))
                    ->filter()
                    ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                    ->take(2)
                    ->implode('');
            @endphp
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3 fw-bold shadow-sm" style="width: 44px; height: 44px; font-size: 1rem; flex-shrink: 0; letter-spacing: 0.5px;">
                {{ $userInitials ?: 'S' }}
            </div>
            <div class="overflow-hidden">
                <div class="sidebar-profile-name text-truncate">{{ auth()->user()->name }}</div>
                <div class="sidebar-profile-role text-truncate">
                    @if(auth()->user()->role === 'admin' && session('login_as_siswa'))
                        ADMIN (Mode Siswa)
                    @else
                        NIS: {{ auth()->user()->nis }}
                    @endif
                </div>
            </div>
        </a>

        <ul class="sidebar-menu">
            <li><a href="{{ route('siswa.dashboard') }}" class="{{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}"><i class="bi bi-grid"></i> Dashboard</a></li>
            <li><a href="{{ route('siswa.guru.index') }}" class="{{ request()->routeIs('siswa.guru.*') ? 'active' : '' }}"><i class="bi bi-person-badge"></i> Daftar Guru</a></li>
            <li><a href="{{ route('siswa.riwayat') }}" class="{{ request()->routeIs('siswa.riwayat') ? 'active' : '' }}"><i class="bi bi-clock-history"></i> Riwayat Penilaian</a></li>
            <li><a href="{{ route('siswa.leaderboard.index') }}" class="{{ request()->routeIs('siswa.leaderboard.*') ? 'active' : '' }}"><i class="bi bi-trophy"></i> Leaderboard</a></li>
            <li><a href="{{ route('siswa.pengaturan') }}" class="{{ request()->routeIs('siswa.pengaturan*') ? 'active' : '' }}"><i class="bi bi-gear"></i> Pengaturan</a></li>
        </ul>
    </aside>

    <main class="main-content">
        {{-- DESKTOP TOPBAR HEADER (STICKY TOP) --}}
        <div class="d-flex align-items-center justify-content-between px-4 py-2.5 rounded-3 border mb-4 shadow-sm d-none d-lg-flex" style="position: sticky; top: 0.75rem; z-index: 1020; backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.95) !important;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary-subtle text-primary fw-bold px-2.5 py-1">PORTAL SISWA</span>
                <span class="text-muted small">| Evaluasi & Suara Siswa SMKN 1 Bangsri Berkarakter JUARA</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                {{-- PORTAL SIPINTU (KEMBALI KE SIPINTU) --}}
                <a href="{{ config('services.sipintu.base_url', 'https://sipintu.smkn1bangsri.sch.id') }}" target="_blank" class="btn btn-light border rounded-circle shadow-sm gk-topbar-btn" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Portal SiPintu">
                    <img src="{{ asset('images/sipintu-logo.png') }}" alt="SiPintu">
                </a>

                {{-- BERANDA PUBLIK ICON BUTTON (TOPBAR) --}}
                <a href="{{ url('/') }}" target="_blank" class="btn btn-light border rounded-circle shadow-sm gk-topbar-btn" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Kembali ke Beranda Publik" aria-label="Kembali ke Beranda Publik">
                    <i class="bi bi-globe2 text-primary"></i>
                </a>

                {{-- PUSAT NOTIFIKASI --}}
                @include('components.user-notif-dropdown', ['prefix' => 'desktop', 'btnClass' => 'gk-topbar-btn'])

                {{-- USER BADGE DROPDOWN (PERSIS SEPERTI ADMIN) --}}
                <div class="dropdown border-start ps-3 ms-2">
                    <button class="btn btn-light d-flex align-items-center gap-2 p-1.5 px-2.5 rounded-pill border shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.8rem;">
                            {{ strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) }}
                        </div>
                        <span class="d-none d-sm-inline small fw-bold text-dark">{{ auth()->user()->name ?? 'Siswa' }}</span>
                        <i class="bi bi-chevron-down text-muted small"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 mt-2" style="border-radius: 12px; min-width: 190px;">
                        <li class="px-2 py-1 mb-1 border-bottom">
                            <small class="text-muted d-block" style="font-size: 0.7rem;">MASUK SEBAGAI</small>
                            <span class="fw-bold small text-dark">Siswa ({{ auth()->user()->nis }})</span>
                        </li>
                        <li>
                            <a class="dropdown-item rounded py-1.5 small" href="{{ route('siswa.pengaturan') }}">
                                <i class="bi bi-gear me-2 text-primary"></i> Pengaturan
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item rounded py-1.5 small text-danger fw-semibold">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        @php
            $mInfo = \App\Services\MaintenanceService::getSiswaMaintenanceInfo();
        @endphp

        {{-- BANNER INFORMASI MODE PEMELIHARAAN --}}
        @if($mInfo['is_active'])
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center justify-content-between p-3 mb-4 rounded-3" style="background: #fff8e6; border-left: 5px solid #f59e0b !important;" role="alert">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center text-warning-emphasis flex-shrink-0" style="width: 42px; height: 42px;">
                        <i class="bi bi-tools fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Mode Pemeliharaan (Maintenance) Sedang Aktif</h6>
                        <p class="mb-0 small text-muted">
                            Siswa dapat melihat dashboard, leaderboard, dan data guru, namun aksi pemberian penilaian & pengiriman pesan dinonaktifkan sementara.
                            <span class="text-primary-emphasis fw-semibold">({{ $mInfo['schedule_text'] }})</span>
                        </p>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-warning text-dark fw-bold text-nowrap ms-2" onclick="showMaintenanceModal()">
                    <i class="bi bi-info-circle me-1"></i> Rincian
                </button>
            </div>
        @endif

        @if(session('success') && !request()->routeIs('siswa.guru.show'))
            <div class="alert alert-primary border-0 shadow-sm d-flex align-items-center gap-2 mb-4" style="background: rgba(37, 99, 235, 0.08); border-left: 4px solid var(--primary) !important; color: #1d4ed8; border-radius: 8px;">
                <i class="bi bi-info-circle-fill text-primary fs-5"></i>
                <span class="fw-semibold">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center gap-2 mb-4" style="border-left: 4px solid #dc2626 !important; border-radius: 8px;">
                <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
                <span class="fw-semibold">{{ session('error') }}</span>
            </div>
        @endif
        @yield('content')
    </main>

    @if(auth()->check() && !auth()->user()->is_active)
    <div style="position: fixed; inset: 0; z-index: 999999; background: rgba(15, 23, 42, 0.88); backdrop-filter: blur(6px); display: flex; align-items: center; justify-content: center; padding: 20px;">
        <div class="card shadow-lg border-0 text-center p-4 p-md-5" style="max-width: 520px; width: 100%; border-radius: 20px; background: white;">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 80px; height: 80px; background: #fee2e2; color: #dc2626; margin: 0 auto;">
                <i class="bi bi-shield-x-fill" style="font-size: 2.8rem;"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1">Akses Akun Dinonaktifkan</h4>
            <p class="text-muted small mb-4">
                @if(auth()->user()->deactivation_type === 'berkala' && auth()->user()->deactivated_until)
                    Akun siswa Anda dinonaktifkan sementara hingga <strong>{{ auth()->user()->deactivated_until->translatedFormat('d F Y H:i') }}</strong> ({{ auth()->user()->deactivated_until->diffForHumans() }}) oleh Admin Sekolah.
                @else
                    Akun siswa Anda telah dinonaktifkan secara permanen oleh Admin Sekolah.
                @endif
            </p>

            <div class="p-3 rounded mb-4 text-start border border-danger border-opacity-25" style="background: #fff5f5;">
                <strong class="text-danger small d-block mb-1">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>Alasan Penonaktifan:
                </strong>
                <p class="text-dark small mb-0 font-italic" style="line-height: 1.5;">
                    "{{ auth()->user()->deactivated_reason ?: 'Akun Anda dinonaktifkan oleh Admin. Silakan hubungi Admin untuk pengaktifan kembali.' }}"
                </p>
            </div>

            <div class="d-flex flex-column gap-2">
                <a href="{{ route('siswa.pengaturan') }}#tabChat" class="btn btn-primary-custom py-2 fw-semibold">
                    <i class="bi bi-headset me-1"></i> Hubungi Admin
                </a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger py-2 w-100 fw-semibold">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/gurukuu-modal.js') }}"></script>
    @if(session('violation_popup'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Anda Melakukan Pelanggaran',
                text: @json(session('violation_popup')),
                confirmButtonText: 'Saya Mengerti',
                confirmButtonColor: '#003366',
                allowOutsideClick: false
            });
        </script>
    @endif
    @include('components.welcome-landing-modal')
    @include('components.periode-notification-modal')

    {{-- MODAL POPUP MAINTENANCE SISWA --}}
    <div class="modal fade" id="modalMaintenanceSiswa" tabindex="-1" aria-labelledby="modalMaintenanceTitle" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-body text-center p-4 p-md-5">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 80px; height: 80px; background: #fff8e6; color: #d97706; margin: 0 auto;">
                        <i class="bi bi-tools" style="font-size: 2.5rem;"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-2" id="modalMaintenanceTitle">Server Sedang Dalam Pemeliharaan</h4>
                    <p class="text-muted small mb-4" id="modalMaintenanceMsg" style="line-height: 1.6;">
                        {{ $mInfo['message'] }}
                    </p>

                    <div class="p-3 rounded-3 text-start border mb-4" style="background: #f8fafc;">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-clock-history text-primary"></i>
                            <strong class="text-dark small">Jadwal / Durasi Pemeliharaan:</strong>
                        </div>
                        <p class="text-primary-emphasis small mb-0 fw-semibold ps-4" id="modalMaintenanceSchedule">
                            {{ $mInfo['schedule_text'] }}
                        </p>
                    </div>

                    <div class="p-2.5 rounded bg-light text-muted small text-start mb-4">
                        <i class="bi bi-info-circle text-info me-1"></i>
                        Anda tetap dapat melihat beranda, profil guru, riwayat ulasan, dan leaderboard seperti biasa. Fitur pemberian penilaian dan kirim pesan akan aktif kembali setelah pemeliharaan selesai.
                    </div>

                    <button type="button" class="btn btn-primary-custom w-100 py-2.5 rounded-3 fw-bold" data-bs-dismiss="modal">
                        Mengerti & Lanjutkan Melihat
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showMaintenanceModal(msg, schedule) {
            if (msg) {
                const msgEl = document.getElementById('modalMaintenanceMsg');
                if (msgEl) msgEl.textContent = msg;
            }
            if (schedule) {
                const schEl = document.getElementById('modalMaintenanceSchedule');
                if (schEl) schEl.textContent = schedule;
            }
            const modalEl = document.getElementById('modalMaintenanceSiswa');
            if (modalEl) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }
        }
        window.showMaintenanceModal = showMaintenanceModal;

        @if(session('maintenance_popup'))
            document.addEventListener('DOMContentLoaded', function() {
                @php $popInfo = session('maintenance_popup'); @endphp
                showMaintenanceModal(@json($popInfo['message'] ?? null), @json($popInfo['schedule_text'] ?? null));
            });
        @endif
    </script>
    @stack('scripts')
</body>
</html>