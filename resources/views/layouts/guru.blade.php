<!DOCTYPE html>
<html lang="id" data-theme="light" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ $siteTitle ?? 'GuruKuu' }} Guru</title>
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
        .sidebar-profile { display: flex; align-items: center; padding: 0.85rem; background: var(--bg-light); border-radius: 12px; margin-bottom: 1.25rem; flex-shrink: 0; cursor: pointer; transition: all 0.2s ease-in-out; text-decoration: none !important; color: inherit !important; border: 1px solid transparent; }
        .sidebar-profile:hover { background: #e9ecef; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0, 51, 102, 0.08); border-color: rgba(0, 51, 102, 0.12); color: #003366 !important; }
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

        .card-custom { background: white; border-radius: 16px; border: 1px solid rgba(0, 51, 102, 0.09); box-shadow: 0 10px 28px -4px rgba(0, 51, 102, 0.10), 0 3px 8px -1px rgba(0, 0, 0, 0.04); }
        .btn-primary-custom { background: var(--primary); color: white; border: none; padding: 0.6rem 1.25rem; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-block; }
        .btn-primary-custom:hover { background: var(--primary-light); color: white; }
        .btn-outline-custom { background: white; color: var(--primary); border: 1px solid var(--border); padding: 0.6rem 1.25rem; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-block; }
        .table-custom thead th { font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; font-weight: 600; color: var(--text-muted); letter-spacing: 1px; text-transform: uppercase; border-bottom: 1px solid var(--border); padding: 1rem 1.25rem; }
        .table-custom tbody td { padding: 1rem 1.25rem; vertical-align: middle; border-bottom: 1px solid var(--border); }

        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
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
    @if(request()->routeIs('guru.dashboard'))
        @include('components.page-loader')
    @endif
    {{-- MOBILE HEADER BAR (KHUSUS HP - BERSIH TANPA BURGER MENU) --}}
    <header class="gk-mobile-header shadow-sm">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('guru.dashboard') }}" class="text-decoration-none d-flex align-items-center gap-2" style="font-size: 1.1rem;">
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

    {{-- MOBILE BOTTOM NAVIGATION BAR (KHUSUS GURU) --}}
    <nav class="gk-bottom-nav d-lg-none" aria-label="Navigasi Bawah Guru">
        <a href="{{ route('guru.dashboard') }}" class="gk-bottom-nav-item {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
            <i class="bi {{ request()->routeIs('guru.dashboard') ? 'bi-grid-fill' : 'bi-grid' }}"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('guru.ulasan') }}" class="gk-bottom-nav-item {{ request()->routeIs('guru.ulasan*') ? 'active' : '' }}">
            <i class="bi {{ request()->routeIs('guru.ulasan*') ? 'bi-chat-square-quote-fill' : 'bi-chat-square-quote' }}"></i>
            <span>Ulasan</span>
        </a>
        <a href="{{ route('guru.leaderboard') }}" class="gk-bottom-nav-item {{ request()->routeIs('guru.leaderboard*') ? 'active' : '' }}">
            <i class="bi {{ request()->routeIs('guru.leaderboard*') ? 'bi-trophy-fill' : 'bi-trophy' }}"></i>
            <span>Peringkat</span>
        </a>
        <a href="{{ route('guru.pengaturan') }}" class="gk-bottom-nav-item {{ request()->routeIs('guru.pengaturan*') ? 'active' : '' }}">
            <i class="bi {{ request()->routeIs('guru.pengaturan*') ? 'bi-gear-fill' : 'bi-gear' }}"></i>
            <span>Pengaturan</span>
        </a>
    </nav>

    <aside class="sidebar">
        <div class="sidebar-brand">
            <a href="{{ route('guru.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none">
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
        <div class="sidebar-subtitle">SMK NEGERI 1 BANGSRI • GURU</div>
        
        @php
            $guruInitials = collect(explode(' ', auth()->user()->name))
                ->filter()
                ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                ->take(2)
                ->implode('');
        @endphp
        <a href="{{ route('guru.pengaturan') }}" class="sidebar-profile">
            @if(auth()->user()->photo)
                <img src="{{ auth()->user()->photo_url }}" alt="{{ auth()->user()->name }}" class="rounded-circle me-3 shadow-sm" style="width: 44px; height: 44px; object-fit: cover;">
            @else
                <div class="rounded-circle text-white d-flex align-items-center justify-content-center me-3 fw-bold shadow-sm" style="width: 44px; height: 44px; min-width: 44px; min-height: 44px; background: #003366; font-size: 1rem; flex-shrink: 0; letter-spacing: 0.5px;">
                    {{ $guruInitials ?: 'G' }}
                </div>
            @endif
            <div class="overflow-hidden">
                <div class="sidebar-profile-name text-truncate">{{ auth()->user()->name }}</div>
                <div class="sidebar-profile-role text-truncate">NIP: {{ auth()->user()->nis ?? '-' }}</div>
            </div>
        </a>

        <ul class="sidebar-menu">
            <li><a href="{{ route('guru.dashboard') }}" class="{{ request()->routeIs('guru.dashboard') ? 'active' : '' }}"><i class="bi bi-grid"></i> Dashboard</a></li>
            <li><a href="{{ route('guru.ulasan') }}" class="{{ request()->routeIs('guru.ulasan*') ? 'active' : '' }}"><i class="bi bi-chat-square-quote"></i> Ulasan Siswa</a></li>
            <li><a href="{{ route('guru.leaderboard') }}" class="{{ request()->routeIs('guru.leaderboard*') ? 'active' : '' }}"><i class="bi bi-trophy"></i> Leaderboard</a></li>
            <li><a href="{{ route('guru.pengaturan') }}" class="{{ request()->routeIs('guru.pengaturan*') ? 'active' : '' }}"><i class="bi bi-gear"></i> Pengaturan</a></li>
        </ul>
    </aside>

    <main class="main-content">
        {{-- DESKTOP TOPBAR HEADER (STICKY TOP) --}}
        <div class="d-flex align-items-center justify-content-between px-4 py-2.5 rounded-3 border mb-4 shadow-sm d-none d-lg-flex" style="position: sticky; top: 0.75rem; z-index: 1020; backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.95) !important;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary-subtle text-primary fw-bold px-2.5 py-1">PORTAL GURU</span>
                <span class="text-muted small">| Evaluasi & Refleksi Pembelajaran Siswa SMKN 1 Bangsri</span>
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
                    @php
                        $guruName = auth()->user()->name ?? 'Guru';
                        $nameParts = array_values(array_filter(explode(' ', trim($guruName))));
                        if (count($nameParts) >= 2) {
                            $guruInitials = strtoupper(mb_substr($nameParts[0], 0, 1) . mb_substr(end($nameParts), 0, 1));
                        } elseif (count($nameParts) === 1 && mb_strlen($nameParts[0]) > 0) {
                            $guruInitials = strtoupper(mb_substr($nameParts[0], 0, 1));
                        } else {
                            $guruInitials = 'G';
                        }
                    @endphp
                    <button class="btn btn-light d-flex align-items-center gap-1.5 p-1 pe-2 rounded-pill border shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="height: 36px; background: #f8fafc; border-color: #e2e8f0 !important; cursor: pointer;" title="{{ $guruName }}">
                        <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold shadow-sm flex-shrink-0" style="width: 28px; height: 28px; background: linear-gradient(135deg, #0d6efd, #003366); font-size: 0.75rem; letter-spacing: 0.5px;">
                            {{ $guruInitials }}
                        </div>
                        <i class="bi bi-chevron-down text-secondary" style="font-size: 0.72rem; margin-left: 2px;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 mt-2" style="border-radius: 12px; min-width: 190px;">
                        <li class="px-2 py-1 mb-1 border-bottom">
                            <div class="fw-bold small text-dark text-truncate">{{ auth()->user()->name ?? 'Guru' }}</div>
                            <small class="text-muted d-block" style="font-size: 0.7rem;">Guru ({{ auth()->user()->nis ?? auth()->user()->nip ?? '-' }})</small>
                        </li>
                        <li>
                            <a class="dropdown-item rounded py-1.5 small" href="{{ route('guru.pengaturan') }}">
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
            $mInfoGuru = \App\Services\MaintenanceService::getSiswaMaintenanceInfo();
        @endphp

        {{-- BANNER INFORMASI MODE PEMELIHARAAN --}}
        @if($mInfoGuru['is_active'])
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center justify-content-between p-3 mb-4 rounded-3" style="background: #fff8e6; border-left: 5px solid #f59e0b !important;" role="alert">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center text-warning-emphasis flex-shrink-0" style="width: 42px; height: 42px;">
                        <i class="bi bi-tools fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Mode Pemeliharaan (Maintenance) Sedang Aktif</h6>
                        <p class="mb-0 small text-muted">
                            Sistem sedang dalam masa pemeliharaan berkala untuk peningkatan performa.
                            <span class="text-primary-emphasis fw-semibold">({{ $mInfoGuru['schedule_text'] }})</span>
                        </p>
                    </div>
                </div>
            </div>
        @endif

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
                    Akun guru Anda dinonaktifkan sementara hingga <strong>{{ auth()->user()->deactivated_until->translatedFormat('d F Y H:i') }}</strong> ({{ auth()->user()->deactivated_until->diffForHumans() }}) oleh Admin Sekolah.
                @else
                    Akun guru Anda telah dinonaktifkan secara permanen oleh Admin Sekolah.
                @endif
            </p>

            <div class="p-3 rounded mb-4 text-start border border-danger border-opacity-25" style="background: #fff5f5;">
                <strong class="text-danger small d-block mb-1">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>Alasan Penonaktifan:
                </strong>
                <p class="text-dark small mb-0 font-italic" style="line-height: 1.5;">
                    "{{ auth()->user()->deactivated_reason ?: 'Akun Anda dinonaktifkan oleh Admin. Silakan hubungi Operator / Admin Sekolah untuk pengaktifan kembali.' }}"
                </p>
            </div>

            <div class="d-flex flex-column gap-2">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger w-100 py-2">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout dari Akun
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
    @include('components.welcome-landing-modal')
    @include('components.periode-notification-modal')
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
