<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - {{ $siteTitle ?? 'GuruKuu' }}</title>
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
    <style>
        :root { --primary:#003366; --primary-light:#004080; --secondary:#FFC107; --accent:#00A86B; --bg-light:#f5f7fa; --text-dark:#1a1a2e; --text-muted:#64748b; --border:#e2e8f0; }
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
        .page-label { font-family:'JetBrains Mono',monospace; font-size:0.75rem; font-weight:600; color:var(--primary); letter-spacing:1px; text-transform:uppercase; }
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
    </style>
    <link href="{{ asset('css/gurukuu-theme.css') }}" rel="stylesheet">
    @stack('styles')
    <script src="{{ asset('js/gurukuu-theme.js') }}"></script>
</head>
<body data-admin-cache-user="{{ auth()->id() }}">
    {{-- MOBILE TOPBAR HEADER (KHUSUS TAMPILAN HP) --}}
    <header class="gk-mobile-header shadow-sm">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-light border p-1 px-2.5 rounded-3" type="button" onclick="GuruKuuTheme.toggleSidebar()" aria-label="Menu Utama">
                <i class="bi bi-list fs-4"></i>
            </button>
            <a href="{{ route('admin.dashboard') }}" class="text-decoration-none fw-bold text-dark d-flex align-items-center gap-1.5" style="font-size: 1.1rem;">
                <span class="badge bg-primary text-white py-1 px-1.5 rounded">SMK</span>
                <span>{{ $siteTitlePart2 ?? 'Bangsri' }}</span>
            </a>
        </div>
        <div class="d-flex align-items-center gap-1.5">
            {{-- BERANDA PUBLIK MOBILE --}}
            <a href="{{ url('/') }}" target="_blank" class="btn btn-light border p-1.5 rounded-circle shadow-sm" title="Buka Beranda Publik">
                <i class="bi bi-globe2 text-primary" style="font-size: 1rem;"></i>
            </a>
            {{-- THEME TOGGLE MOBILE --}}
            <button class="btn btn-light border p-1.5 rounded-circle shadow-sm" type="button" onclick="GuruKuuTheme.toggleTheme()" title="Ganti Mode Gelap / Terang">
                <i class="bi bi-moon-stars-fill gk-theme-icon" style="font-size: 1rem;"></i>
            </button>
            {{-- DISPLAY PERF SETTINGS MOBILE --}}
            <button class="btn btn-light border p-1.5 rounded-circle shadow-sm" type="button" onclick="GuruKuuTheme.openDisplayModal()" title="Pengaturan Grafis & Performa">
                <i class="bi bi-sliders2-vertical text-primary" style="font-size: 1rem;"></i>
            </button>
            {{-- NOTIF DROPDOWN MOBILE --}}
            <a href="{{ route('admin.pelanggaran.index') }}" class="btn btn-light border position-relative p-1.5 rounded-circle shadow-sm" title="Notifikasi Pelanggaran">
                <i class="bi bi-bell-fill {{ ($unreadPelanggaranCount ?? 0) > 0 ? 'text-danger' : 'text-secondary' }}" style="font-size: 1rem;"></i>
                @if(($unreadPelanggaranCount ?? 0) > 0)
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                @endif
            </a>
        </div>
    </header>

    <div class="sidebar">
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none text-dark">
                @if(!empty($siteLogo))
                    <img src="{{ $siteLogo }}" alt="{{ $siteTitle ?? 'GuruKuu' }}" style="height: 32px; max-width: 45px; object-fit: contain;">
                @else
                    <i class="bi bi-mortarboard-fill fs-4 text-primary"></i>
                @endif
                <span class="fs-5 fw-bold">
                    <span>{{ $siteTitlePart1 ?? 'Guru' }}</span><span class="text-warning">{{ $siteTitlePart2 ?? 'Kuu' }}</span>
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
                    🏫 SMKN 1 BANGSRI • ADMIN
                </span>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            
            <div class="sidebar-link" data-bs-toggle="collapse" data-bs-target="#menuData">
                <i class="bi bi-database"></i> Data Lokal
                <i class="bi bi-chevron-down ms-auto" style="font-size:0.8rem;"></i>
            </div>
            <div class="collapse {{ request()->routeIs('admin.guru.*', 'admin.siswa.*', 'admin.jurusan.*') ? 'show' : '' }}" id="menuData">
                <div class="sidebar-submenu">
                    <a href="{{ route('admin.guru.index') }}" class="sidebar-link {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}" data-admin-page-link>Data Guru</a>
                    <a href="{{ route('admin.siswa.index') }}" class="sidebar-link {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}" data-admin-page-link>Data Siswa</a>
                    <a href="{{ route('admin.jurusan.index') }}" class="sidebar-link {{ request()->routeIs('admin.jurusan.*') ? 'active' : '' }}" data-admin-page-link>Data Jurusan</a>
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

            <div class="sidebar-link d-flex align-items-center justify-content-between" data-bs-toggle="collapse" data-bs-target="#menuLaporan">
                <div><i class="bi bi-file-earmark-bar-graph"></i> Laporan & Feedback</div>
                <div class="d-flex align-items-center gap-1">
                    @if(($unreadPelanggaranCount ?? 0) > 0)
                        <span class="badge bg-danger rounded-pill px-1.5 py-0.5" style="font-size: 0.65rem;">{{ $unreadPelanggaranCount }}</span>
                    @endif
                    <i class="bi bi-chevron-down" style="font-size:0.8rem;"></i>
                </div>
            </div>
            <div class="collapse {{ request()->routeIs('admin.leaderboard.*', 'admin.kritik-saran.*', 'admin.kontak.*', 'admin.pelanggaran.*') ? 'show' : '' }}" id="menuLaporan">
                <div class="sidebar-submenu">
                    <a href="{{ route('admin.leaderboard.index') }}" class="sidebar-link {{ request()->routeIs('admin.leaderboard.*') ? 'active' : '' }}">Leaderboard</a>
                    <a href="{{ route('admin.kritik-saran.index') }}" class="sidebar-link {{ request()->routeIs('admin.kritik-saran.*') ? 'active' : '' }}">Kritik & Saran</a>
                    <a href="{{ route('admin.kontak.index') }}" class="sidebar-link {{ request()->routeIs('admin.kontak.*') ? 'active' : '' }}">Pesan Masuk</a>
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
        <div class="d-none d-lg-flex align-items-center justify-content-between bg-white px-4 py-2.5 rounded-3 border mb-4 shadow-sm">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary-subtle text-primary fw-bold px-2.5 py-1">ADMINISTRATOR</span>
                <span class="text-muted small d-none d-md-inline">| Sistem Evaluasi & Akuntabilitas Pendidik</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                {{-- BERANDA PUBLIK LINK (TOPBAR) --}}
                <a href="{{ url('/') }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1.5 text-decoration-none shadow-sm" title="Buka Beranda Publik">
                    <i class="bi bi-globe2 text-primary"></i>
                    <span class="d-none d-md-inline small fw-semibold">Beranda Publik</span>
                </a>

                {{-- THEME TOGGLE DESKTOP --}}
                <button class="btn btn-light border p-2 rounded-circle shadow-sm" type="button" onclick="GuruKuuTheme.toggleTheme()" title="Ganti Mode Gelap / Terang">
                    <i class="bi bi-moon-stars-fill gk-theme-icon fs-5"></i>
                </button>

                {{-- DISPLAY SETTINGS DESKTOP --}}
                <button class="btn btn-light border p-2 rounded-circle shadow-sm" type="button" onclick="GuruKuuTheme.openDisplayModal()" title="Pengaturan Grafis & Performa Layar">
                    <i class="bi bi-sliders2-vertical text-primary fs-5"></i>
                </button>

                {{-- NOTIFIKASI BELL DROPDOWN --}}
                <div class="dropdown">
                    <button class="btn btn-light position-relative p-2 rounded-circle border shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifikasi Pelanggaran">
                        <i class="bi bi-bell-fill {{ ($unreadPelanggaranCount ?? 0) > 0 ? 'text-danger' : 'text-secondary' }} fs-5"></i>
                        @if(($unreadPelanggaranCount ?? 0) > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.65rem;">
                                {{ $unreadPelanggaranCount }}
                                <span class="visually-hidden">notifikasi belum dibaca</span>
                            </span>
                        @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0" style="width: 360px; max-width: 92vw;">
                        <li class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between">
                            <span class="fw-bold text-dark small"><i class="bi bi-shield-exclamation text-danger me-1"></i> Notifikasi Pelanggaran</span>
                            @if(($unreadPelanggaranCount ?? 0) > 0)
                                <span class="badge bg-danger rounded-pill">{{ $unreadPelanggaranCount }} Perlu Tindakan</span>
                            @endif
                        </li>
                        <div style="max-height: 320px; overflow-y: auto;">
                            @forelse(($recentPelanggarans ?? collect()) as $notif)
                                <li>
                                    <a class="dropdown-item p-3 border-bottom text-wrap" href="{{ route('admin.pelanggaran.index') }}">
                                        <div class="d-flex align-items-start gap-2.5">
                                            <span class="badge bg-danger text-white rounded-circle p-1.5 mt-0.5 flex-shrink-0">
                                                <i class="bi bi-exclamation-octagon-fill"></i>
                                            </span>
                                            <div class="flex-grow-1">
                                                @if($notif->user)
                                                    <div class="d-flex justify-content-between align-items-center mb-0.5">
                                                        <strong class="text-danger small" style="font-size: 0.85rem;">{{ $notif->user->name }}</strong>
                                                        @if($notif->user->warning_count > 0)
                                                            <span class="badge bg-warning text-dark font-mono" style="font-size: 0.65rem;">⚠️ {{ $notif->user->warning_count }}x Sanksi</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-dark small font-mono" style="font-size: 0.73rem;">
                                                        NIS: <strong>{{ $notif->user->nis ?? '-' }}</strong> &bull; 
                                                        {{ $notif->user->nama_kelas }}
                                                    </div>

                                                @else
                                                    <div class="fw-bold text-dark small">Tamu Publik (Anonim)</div>
                                                    <div class="text-muted small font-mono" style="font-size: 0.7rem;">IP: {{ $notif->ip_address ?? '-' }}</div>
                                                @endif

                                                <div class="text-muted my-1" style="font-size: 0.72rem;">
                                                    <span class="badge bg-light text-secondary border me-1">{{ $notif->tipe_label }}</span>
                                                    @if($notif->guru)
                                                        ke Guru: <strong class="text-dark">{{ $notif->guru->nama }}</strong>
                                                    @endif
                                                </div>

                                                @if(is_array($notif->kata_terdeteksi) && count($notif->kata_terdeteksi) > 0)
                                                    <div class="mb-1 d-flex flex-wrap gap-1">
                                                        @foreach($notif->kata_terdeteksi as $kw)
                                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-1.5 py-0.5" style="font-size: 0.68rem;">
                                                                "{{ $kw }}"
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                <div class="d-flex justify-content-between align-items-center mt-1">
                                                    <span class="text-secondary font-mono" style="font-size: 0.68rem;">
                                                        <i class="bi bi-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}
                                                    </span>
                                                    <span class="text-primary small fw-semibold" style="font-size: 0.72rem;">
                                                        Lihat Detail &rarr;
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li class="p-4 text-center text-muted small">
                                    <i class="bi bi-check-circle text-success fs-4 d-block mb-1"></i>
                                    Tidak ada pelanggaran baru
                                </li>
                            @endforelse
                        </div>
                        <li class="p-2 text-center bg-light border-top">
                            <a href="{{ route('admin.pelanggaran.index') }}" class="btn btn-primary-custom btn-sm w-100 py-1.5 rounded-pill fw-semibold" style="font-size: 0.8rem;">
                                Kelola & Tinjau Semua Pelanggaran
                            </a>
                        </li>
                    </ul>

                </div>

                {{-- USER BADGE DROPDOWN --}}
                <div class="dropdown border-start ps-3">
                    <button class="btn btn-light d-flex align-items-center gap-2 p-1.5 px-2.5 rounded-pill border shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.8rem;">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
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
                                    <i class="bi bi-box-arrow-right me-2"></i> Keluar
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
    @stack('scripts')
</body>
</html>
