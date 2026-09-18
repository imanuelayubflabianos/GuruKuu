<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ $siteTitle ?? 'GuruKuu' }} Siswa</title>
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
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #003366; --primary-light: #004080; --secondary: #FFC107;
            --accent: #00A86B; --bg-light: #f5f7fa; --text-dark: #1a1a2e; --text-muted: #64748b; --border: #e2e8f0;
        }
        * { font-family: 'Inter', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        body { background: var(--bg-light); color: var(--text-dark); }

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
            .main-content { margin-left: 0; }
        }
    </style>
    <link href="{{ asset('css/gurukuu-theme.css') }}" rel="stylesheet">
    <script src="{{ asset('js/gurukuu-theme.js') }}"></script>
</head>
<body>
    {{-- MOBILE HEADER BAR (KHUSUS HP) --}}
    <header class="gk-mobile-header shadow-sm">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-light border p-1 px-2.5 rounded-3" type="button" onclick="GuruKuuTheme.toggleSidebar()" aria-label="Buka Menu">
                <i class="bi bi-list fs-4"></i>
            </button>
            <a href="{{ route('siswa.dashboard') }}" class="text-decoration-none fw-bold text-dark d-flex align-items-center gap-1.5" style="font-size: 1.1rem;">
                <span class="badge bg-primary text-white py-1 px-1.5 rounded">SISWA</span>
                <span>SMKN 1 Bangsri</span>
            </a>
        </div>
        <div class="d-flex align-items-center gap-1.5">
            {{-- BERANDA PUBLIK MOBILE --}}
            <a href="{{ url('/') }}" target="_blank" class="btn btn-light border p-1.5 rounded-circle shadow-sm" title="Buka Beranda Publik">
                <i class="bi bi-globe2 text-primary" style="font-size: 1rem;"></i>
            </a>
            <button class="btn btn-light border p-1.5 rounded-circle shadow-sm" type="button" onclick="GuruKuuTheme.toggleTheme()" title="Mode Gelap/Terang">
                <i class="bi bi-moon-stars-fill gk-theme-icon" style="font-size: 1rem;"></i>
            </button>
            <button class="btn btn-light border p-1.5 rounded-circle shadow-sm" type="button" onclick="GuruKuuTheme.openDisplayModal()" title="Pengaturan Grafis & Performa">
                <i class="bi bi-sliders2-vertical text-primary" style="font-size: 1rem;"></i>
            </button>
        </div>
    </header>

    <aside class="sidebar">
        <div class="sidebar-brand">
            <a href="{{ route('siswa.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none text-dark">
                @if(!empty($siteLogo))
                    <img src="{{ $siteLogo }}" alt="{{ $siteTitle ?? 'GuruKuu' }}" style="height: 32px; max-width: 45px; object-fit: contain;">
                @else
                    <i class="bi bi-mortarboard-fill fs-4 text-primary"></i>
                @endif
                <span class="fs-5 fw-bold">
                    <span>{{ $siteTitlePart1 ?? 'Guru' }}</span><span class="text-warning">{{ $siteTitlePart2 ?? 'Kuu' }}</span>
                </span>
            </a>
            <button type="button" class="btn btn-sm btn-light border d-lg-none rounded-circle" onclick="GuruKuuTheme.closeSidebar()" aria-label="Tutup">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="sidebar-subtitle">SMK NEGERI 1 BANGSRI • SISWA</div>
        
        <a href="{{ route('siswa.pengaturan') }}" class="sidebar-profile">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; font-size: 1.5rem; flex-shrink: 0;">
                <i class="bi bi-person-fill"></i>
            </div>
            <div class="overflow-hidden">
                <div class="sidebar-profile-name text-truncate" title="{{ auth()->user()->name }}">{{ auth()->user()->name }}</div>
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
        {{-- DESKTOP TOPBAR HEADER --}}
        <div class="d-flex align-items-center justify-content-between bg-white px-4 py-2.5 rounded-3 border mb-4 shadow-sm d-none d-lg-flex">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary-subtle text-primary fw-bold px-2.5 py-1">PORTAL SISWA</span>
                <span class="text-muted small">| Evaluasi & Suara Siswa SMKN 1 Bangsri Berkarakter JUARA</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                {{-- BERANDA PUBLIK LINK (TOPBAR) --}}
                <a href="{{ url('/') }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 text-decoration-none shadow-sm" title="Buka Beranda Publik">
                    <i class="bi bi-globe2 text-primary"></i>
                    <span class="d-none d-md-inline small fw-semibold">Beranda Publik</span>
                </a>
                <button class="btn btn-light border p-2 rounded-circle shadow-sm" type="button" onclick="GuruKuuTheme.toggleTheme()" title="Ganti Mode Gelap / Terang">
                    <i class="bi bi-moon-stars-fill gk-theme-icon fs-5"></i>
                </button>
                <button class="btn btn-light border p-2 rounded-circle shadow-sm" type="button" onclick="GuruKuuTheme.openDisplayModal()" title="Pengaturan Grafis & Performa">
                    <i class="bi bi-sliders2-vertical text-primary fs-5"></i>
                </button>

                {{-- USER BADGE DROPDOWN (PERSIS SEPERTI ADMIN) --}}
                <div class="dropdown border-start ps-3 ms-1">
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
                                    <i class="bi bi-box-arrow-right me-2"></i> Keluar
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        @if(session('success')) <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div> @endif
        @if(session('error')) <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div> @endif
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
                    Akun siswa Anda dinonaktifkan sementara hingga <strong>{{ auth()->user()->deactivated_until->translatedFormat('d F Y H:i') }}</strong> ({{ auth()->user()->deactivated_until->diffForHumans() }}) oleh Admin Operator Sekolah.
                @else
                    Akun siswa Anda telah dinonaktifkan secara permanen oleh Admin Operator Sekolah.
                @endif
            </p>

            <div class="p-3 rounded mb-4 text-start border border-danger border-opacity-25" style="background: #fff5f5;">
                <strong class="text-danger small d-block mb-1">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>Alasan Penonaktifan:
                </strong>
                <p class="text-dark small mb-0 font-italic" style="line-height: 1.5;">
                    "{{ auth()->user()->deactivated_reason ?: 'Akun Anda dinonaktifkan oleh Admin Operator Sekolah. Silakan hubungi Admin Operator Sekolah untuk pengaktifan kembali.' }}"
                </p>
            </div>

            <div class="d-flex flex-column gap-2">
                <a href="{{ route('siswa.pengaturan') }}#tabChat" class="btn btn-primary-custom py-2 fw-semibold">
                    <i class="bi bi-chat-dots-fill me-1"></i> Hubungi Admin Operator Sekolah
                </a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger py-2 w-100 fw-semibold">
                        <i class="bi bi-box-arrow-right me-1"></i> Keluar (Logout)
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
    @stack('scripts')
</body>
</html>