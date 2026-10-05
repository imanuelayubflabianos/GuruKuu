<!DOCTYPE html>
<html lang="id" data-theme="light" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - {{ $siteTitle ?? 'GuruKuu' }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ asset('favicon-48x48.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon-192x192.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --primary:#003366; --primary-light:#004080; --secondary:#ff6600; --accent:#f97316; --bg-light:#f8fafc; --text-dark:#0f172a; --text-muted:#64748b; --border:#e2e8f0; }
        body { background-color:var(--bg-light); color:var(--text-dark); font-family:'Inter',sans-serif; }
        .sidebar { width:260px; height:100vh; max-height:100vh; position:fixed; left:0; top:0; background:white; border-right:1px solid var(--border); z-index:1040; overflow-y:auto !important; overflow-x:hidden; display:flex; flex-direction:column; scrollbar-width:thin; padding-bottom:2.5rem; }
        .sidebar::-webkit-scrollbar { width:4px; }
        .sidebar::-webkit-scrollbar-thumb { background:rgba(0,0,0,0.12); border-radius:4px; }
        .sidebar-brand { padding:1.25rem 1.5rem; font-size:1.35rem; font-weight:800; color:var(--primary); text-decoration:none; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid var(--border); flex-shrink:0; }
        .sidebar-menu { padding:0.75rem 0; flex:1 0 auto; }
        .sidebar-link { display:flex; align-items:center; padding:0.75rem 1.5rem; color:var(--text-dark); text-decoration:none; transition:all 0.2s; cursor:pointer; }
        .sidebar-link:hover, .sidebar-link.active { background:var(--bg-light); color:var(--primary); border-right:3px solid var(--primary); }
        .sidebar-link i { margin-right:0.75rem; width:20px; text-align:center; }
        .sidebar-submenu { padding-left:3.25rem; font-size:0.9rem; }
        .sidebar-submenu .sidebar-link { padding:0.5rem 1.5rem; }
        .main-content { margin-left:260px; padding:2rem; min-height:100vh; }
        .page-header { margin-bottom:2rem; }
        .page-label { font-family:'Inter',sans-serif; font-size:0.75rem; font-weight:700; color:var(--primary); letter-spacing:1px; text-transform:uppercase; }
        .page-title { font-size:1.75rem; font-weight:800; margin-bottom:0.25rem; }
        .page-subtitle { color:var(--text-muted); font-size:0.95rem; }
        .card-custom { background:white; border-radius:12px; border:1px solid var(--border); box-shadow:0 2px 8px rgba(0,0,0,0.04); }
        .table-custom th { font-weight:600; font-size:0.85rem; text-transform:uppercase; color:var(--text-muted); border-bottom:2px solid var(--border); }
        .table-custom td { vertical-align:middle; font-size:0.9rem; }
        .btn-primary-custom { background:var(--primary); color:white; border:none; }
        .btn-primary-custom:hover { background:var(--primary-light); color:white; }
        .btn-outline-custom { background:transparent; color:var(--primary); border:1px solid var(--primary); }
        .btn-outline-custom:hover { background:var(--primary); color:white; }
        .stat-card { background:white; border-radius:12px; padding:1.5rem; border:1px solid var(--border); box-shadow:0 2px 8px rgba(0,0,0,0.04); transition:transform 0.2s; }
        .stat-card:hover { transform:translateY(-5px); }
        .stat-card-label { font-size:0.85rem; color:var(--text-muted); text-transform:uppercase; font-weight:600; }
        .stat-card-value { font-size:2rem; font-weight:800; color:var(--text-dark); }
        .table-responsive { min-height: 220px; }
        .table-custom .dropdown-menu { z-index: 1060; }

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
    @stack('styles')
    <script src="{{ asset('js/gurukuu-theme.js') }}"></script>
</head>
<body class="role-admin" data-admin-cache-user="{{ auth()->id() }}">
    @if(request()->routeIs('admin.dashboard'))
        @include('components.page-loader')
    @endif
    {{-- MOBILE TOPBAR HEADER (KHUSUS TAMPILAN HP - BERSIH TANPA BURGER MENU) --}}
    <header class="gk-mobile-header shadow-sm">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="text-decoration-none d-flex align-items-center gap-2" style="font-size: 1.1rem;">
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
            {{-- NOTIFIKASI GABUNGAN MOBILE (PELANGGARAN & CHAT) --}}
            @php $totalNotif = ($unreadPelanggaranCount ?? 0) + ($unreadChatCount ?? 0); @endphp
            <div class="dropdown">
                <button class="btn btn-light border position-relative rounded-circle shadow-sm gk-topbar-btn-sm" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" title="Pusat Notifikasi">
                    <i class="bi bi-bell-fill gk-bell-icon {{ $totalNotif > 0 ? 'has-unread' : 'no-unread' }} admin-bell-icon"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill gk-notif-badge admin-notif-badge" id="adminNotifBadgeMobile" style="font-size: 0.6rem; {{ $totalNotif > 0 ? '' : 'display: none !important;' }}">
                        <span class="admin-notif-count">{{ $totalNotif }}</span>
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0" style="width: 320px; max-width: 90vw;">
                    @include('components.admin-notif-dropdown', ['prefix' => 'mobile'])
                </ul>
            </div>

            {{-- MENU TITIK TIGA (BERANDA PUBLIK & SIPINTU) --}}
            @include('components.mobile-more-menu')
        </div>
    </header>

    {{-- MOBILE BOTTOM NAVIGATION BAR (KHUSUS ADMINISTRATOR) --}}
    <nav class="gk-bottom-nav d-lg-none" aria-label="Navigasi Bawah Admin">
        <a href="{{ route('admin.dashboard') }}" class="gk-bottom-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi {{ request()->routeIs('admin.dashboard') ? 'bi-grid-fill' : 'bi-grid' }}"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.guru.index') }}" class="gk-bottom-nav-item {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}">
            <i class="bi {{ request()->routeIs('admin.guru.*') ? 'bi-person-badge-fill' : 'bi-person-badge' }}"></i>
            <span>Guru</span>
        </a>
        <a href="{{ route('admin.siswa.index') }}" class="gk-bottom-nav-item {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}">
            <i class="bi {{ request()->routeIs('admin.siswa.*') ? 'bi-mortarboard-fill' : 'bi-mortarboard' }}"></i>
            <span>Siswa</span>
        </a>
        <a href="{{ route('admin.leaderboard.index') }}" class="gk-bottom-nav-item {{ request()->routeIs('admin.leaderboard.*') ? 'active' : '' }}">
            <i class="bi {{ request()->routeIs('admin.leaderboard.*') ? 'bi-trophy-fill' : 'bi-trophy' }}"></i>
            <span>Peringkat</span>
        </a>
        <button type="button" class="gk-bottom-nav-item border-0 bg-transparent p-0" onclick="GuruKuuTheme.toggleSidebar()" title="Buka Menu Lengkap">
            <i class="bi bi-three-dots"></i>
            <span>Lainnya</span>
        </button>
    </nav>

    <div class="sidebar">
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                @if(!empty($siteLogo))
                    <img src="{{ $siteLogo }}" alt="{{ $siteTitle ?? 'GuruKuu' }}" style="height: 32px; max-width: 45px; object-fit: contain;">
                @else
                    <i class="bi bi-mortarboard-fill fs-4" style="color: {{ $siteTitleColor1 ?? '#003366' }} !important;"></i>
                @endif
                <span class="fs-5 fw-bold brand-logo-text">
                    <span style="color: {{ $siteTitleColor1 ?? '#003366' }} !important;">{{ $siteTitlePart1 ?? 'Guru' }}</span><span style="color: {{ $siteTitleColor2 ?? '#FFC107' }} !important;">{{ $siteTitlePart2 ?? 'Kuu' }}</span>
                </span>
            </a>
            {{-- CLOSE BUTTON MOBILE --}}
            <button type="button" class="btn btn-sm btn-light border d-lg-none rounded-circle" onclick="GuruKuuTheme.closeSidebar()" aria-label="Tutup Menu">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="sidebar-menu">
            <div class="px-3 py-1 mb-1">
                <span class="badge bg-light text-muted border text-truncate w-100 text-start" style="font-size: 0.65rem;">
                    SMKN 1 BANGSRI • ADMIN
                </span>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            
            <div class="sidebar-link" data-bs-toggle="collapse" data-bs-target="#menuData">
                <i class="bi bi-database"></i> Data Lokal
                <i class="bi bi-chevron-down ms-auto" style="font-size:0.8rem;"></i>
            </div>
            <div class="collapse {{ request()->routeIs('admin.guru.*', 'admin.siswa.*') ? 'show' : '' }}" id="menuData">
                <div class="sidebar-submenu">
                    <a href="{{ route('admin.guru.index') }}" class="sidebar-link {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}" data-admin-page-link>Data Guru</a>
                    <a href="{{ route('admin.siswa.index') }}" class="sidebar-link {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}" data-admin-page-link>Data Siswa</a>
                </div>
            </div>

            <div class="sidebar-link" data-bs-toggle="collapse" data-bs-target="#menuSiPintu">
                <i class="bi bi-cloud-arrow-down text-primary"></i> Gateway SiPintu
                <span class="badge bg-primary ms-auto me-1" style="font-size: 0.65rem;">API</span>
                <i class="bi bi-chevron-down" style="font-size:0.8rem;"></i>
            </div>
            <div class="collapse {{ request()->routeIs('admin.sipintu.*') ? 'show' : '' }}" id="menuSiPintu">
                <div class="sidebar-submenu">
                    <a href="{{ route('admin.sipintu.index') }}" class="sidebar-link {{ request()->routeIs('admin.sipintu.index') ? 'active' : '' }}">Status Gateway</a>
                    <a href="{{ route('admin.sipintu.guru') }}" class="sidebar-link {{ request()->routeIs('admin.sipintu.guru*') ? 'active' : '' }}">Data Guru SiPintu</a>
                    <a href="{{ route('admin.sipintu.siswa') }}" class="sidebar-link {{ request()->routeIs('admin.sipintu.siswa*') ? 'active' : '' }}">Data Siswa SiPintu</a>
                </div>
            </div>

            @php $totalReportNotif = ($unreadPelanggaranCount ?? 0) + ($unreadChatCount ?? 0); @endphp
            <div class="sidebar-link d-flex align-items-center justify-content-between" data-bs-toggle="collapse" data-bs-target="#menuLaporan">
                <div><i class="bi bi-file-earmark-bar-graph"></i> Laporan & Feedback</div>
                <div class="d-flex align-items-center gap-1">
                    @if($totalReportNotif > 0)
                        <span class="badge bg-danger rounded-pill px-1.5 py-0.5" style="font-size: 0.65rem;">{{ $totalReportNotif }}</span>
                    @endif
                    <i class="bi bi-chevron-down" style="font-size:0.8rem;"></i>
                </div>
            </div>
            <div class="collapse {{ request()->routeIs('admin.leaderboard.*', 'admin.kritik-saran.*', 'admin.kontak.*', 'admin.pelanggaran.*') ? 'show' : '' }}" id="menuLaporan">
                <div class="sidebar-submenu">
                    <a href="{{ route('admin.leaderboard.index') }}" class="sidebar-link {{ request()->routeIs('admin.leaderboard.*') ? 'active' : '' }}">Leaderboard</a>
                    <a href="{{ route('admin.kritik-saran.index') }}" class="sidebar-link {{ request()->routeIs('admin.kritik-saran.*') ? 'active' : '' }}">Kritik & Saran</a>
                    <a href="{{ route('admin.kontak.index') }}" class="sidebar-link d-flex align-items-center justify-content-between {{ request()->routeIs('admin.kontak.*') ? 'active' : '' }}">
                        <span>Pesan Masuk</span>
                        @if(($unreadChatCount ?? 0) > 0)
                            <span class="badge bg-primary rounded-pill px-2" style="font-size: 0.65rem;">{{ $unreadChatCount }} baru</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.pelanggaran.index') }}" class="sidebar-link d-flex align-items-center justify-content-between {{ request()->routeIs('admin.pelanggaran.*') ? 'active' : '' }}">
                        <span>Log Pelanggaran</span>
                        @if(($unreadPelanggaranCount ?? 0) > 0)
                            <span class="badge bg-danger rounded-pill px-2" style="font-size: 0.65rem;">{{ $unreadPelanggaranCount }} baru</span>
                        @endif
                    </a>
                </div>
            </div>

            <a href="{{ route('admin.pengaturan.index') }}" class="sidebar-link {{ request()->routeIs('admin.pengaturan.*') ? 'active' : '' }}">
                <i class="bi bi-gear"></i> Pengaturan
            </a>
        </div>
    </div>

    <div class="main-content">
        {{-- TOPBAR HEADER --}}
        <div class="d-none d-lg-flex align-items-center justify-content-between bg-white px-4 py-2.5 rounded-3 border mb-4 shadow-sm" style="position: sticky; top: 0.75rem; z-index: 1020; backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.95) !important;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary-subtle text-primary fw-bold px-2.5 py-1">ADMINISTRATOR</span>
                <span class="text-muted small d-none d-md-inline">| Sistem Evaluasi & Akuntabilitas Pendidik</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                {{-- KEMBALI KE SIPINTU --}}
                <a href="{{ config('services.sipintu.base_url', 'https://sipintu.smkn1bangsri.sch.id') }}" target="_blank" class="btn btn-light border rounded-circle shadow-sm gk-topbar-btn" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Portal SiPintu">
                    <img src="{{ asset('images/sipintu-logo.png') }}" alt="SiPintu">
                </a>

                {{-- BERANDA PUBLIK ICON BUTTON (TOPBAR) --}}
                <a href="{{ url('/') }}" target="_blank" class="btn btn-light border rounded-circle shadow-sm gk-topbar-btn" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Kembali ke Beranda Publik" aria-label="Kembali ke Beranda Publik">
                    <i class="bi bi-globe2 text-primary"></i>
                </a>

                {{-- NOTIFIKASI GABUNGAN DESKTOP (PELANGGARAN & CHAT) --}}
                @php $totalNotif = ($unreadPelanggaranCount ?? 0) + ($unreadChatCount ?? 0); @endphp
                <div class="dropdown">
                    <button class="btn btn-light position-relative rounded-circle border shadow-sm gk-topbar-btn" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" title="Pusat Notifikasi">
                        <i class="bi bi-bell-fill gk-bell-icon {{ $totalNotif > 0 ? 'has-unread' : 'no-unread' }} admin-bell-icon"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill gk-notif-badge admin-notif-badge" id="adminNotifBadgeDesktop" style="font-size: 0.65rem; {{ $totalNotif > 0 ? '' : 'display: none !important;' }}">
                            <span class="admin-notif-count">{{ $totalNotif }}</span>
                            <span class="visually-hidden">notifikasi belum dibaca</span>
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0" style="width: 380px; max-width: 92vw;">
                        @include('components.admin-notif-dropdown', ['prefix' => 'desktop'])
                    </ul>
                </div>

                {{-- USER BADGE DROPDOWN --}}
                <div class="dropdown border-start ps-3 ms-2">
                    <button class="btn btn-light d-flex align-items-center gap-2 p-1.5 px-2.5 rounded-pill border shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        @php
                            $adminInitials = collect(explode(' ', auth()->user()->name ?? 'Admin'))
                                ->filter()
                                ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                                ->take(2)
                                ->implode('');
                        @endphp
                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 28px; height: 28px; background: #003366; font-size: 0.75rem; flex-shrink: 0;">
                            {{ $adminInitials ?: 'A' }}
                        </div>
                        <span class="d-none d-sm-inline small fw-bold text-dark">{{ auth()->user()->name ?? 'Admin' }}</span>
                        <i class="bi bi-chevron-down text-muted small"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 mt-2" style="border-radius: 12px; min-width: 190px;">
                        <li class="px-2 py-1 mb-1 border-bottom">
                            <small class="text-muted d-block" style="font-size: 0.7rem;">MASUK SEBAGAI</small>
                            <span class="fw-bold small text-dark">Administrator</span>
                        </li>
                        <li>
                            <a class="dropdown-item rounded py-1.5 small" href="{{ route('admin.pengaturan.index') }}">
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

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/gurukuu-modal.js') }}"></script>
    <script src="{{ asset('js/admin-page-cache.js') }}"></script>
    @include('components.welcome-landing-modal')
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
    @stack('scripts')
</body>
</html>
