<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - GuruKuu</title>
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
        body { background: var(--bg-light); }
        .sidebar { width: 280px; background: white; border-right: 1px solid var(--border); min-height: 100vh; position: fixed; left: 0; top: 0; z-index: 100; padding: 1.5rem; overflow-y: auto; }
        .sidebar-brand { font-weight: 800; font-size: 1.5rem; color: var(--primary); margin-bottom: 0.25rem; }
        .sidebar-subtitle { font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; color: var(--text-muted); letter-spacing: 2px; margin-bottom: 2rem; }
        .sidebar-profile { display: flex; align-items: center; padding: 1rem; background: var(--bg-light); border-radius: 12px; margin-bottom: 2rem; cursor: pointer; transition: all 0.2s; text-decoration: none; color: inherit; }
        .sidebar-profile:hover { background: #e9ecef; color: inherit; }
        .sidebar-profile img { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; margin-right: 0.75rem; }
        .sidebar-profile-name { font-weight: 700; font-size: 0.95rem; }
        .sidebar-profile-role { font-family: 'JetBrains Mono', monospace; font-size: 0.65rem; color: var(--text-muted); letter-spacing: 1px; }
        .sidebar-menu { list-style: none; padding: 0; margin: 0; }
        .sidebar-menu li { margin-bottom: 0.25rem; }
        .sidebar-menu a { display: flex; align-items: center; padding: 0.75rem 1rem; color: var(--text-dark); text-decoration: none; border-radius: 8px; font-weight: 500; font-size: 0.9rem; transition: all 0.2s; }
        .sidebar-menu a i { width: 24px; margin-right: 0.75rem; font-size: 1.1rem; color: var(--text-muted); }
        .sidebar-menu a:hover { background: var(--bg-light); color: var(--primary); }
        .sidebar-menu a.active { background: var(--primary); color: white; }
        .sidebar-menu a.active i { color: white; }
        .sidebar-logout { margin-top: 2rem; padding-top: 1rem; border-top: 1px solid var(--border); }
        .sidebar-logout a { color: #dc3545 !important; }
        .sidebar-logout a i { color: #dc3545 !important; }
        .main-content { margin-left: 280px; padding: 2rem; min-height: 100vh; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; }
        .page-label { font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; font-weight: 600; color: var(--primary); letter-spacing: 2px; text-transform: uppercase; margin-bottom: 0.25rem; }
        .page-title { font-size: 2rem; font-weight: 800; color: var(--text-dark); margin: 0; }
        .page-subtitle { color: var(--text-muted); font-size: 0.95rem; margin: 0; }
        .card-custom { background: white; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        .stat-card { background: white; border-radius: 12px; border: 1px solid var(--border); padding: 1.5rem; height: 100%; }
        .stat-card-label { font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; font-weight: 600; color: var(--text-muted); letter-spacing: 1.5px; text-transform: uppercase; }
        .stat-card-value { font-size: 2.5rem; font-weight: 900; color: var(--primary); line-height: 1.1; margin: 0.5rem 0; }
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
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-brand">GuruKuu</div>
        <div class="sidebar-subtitle">Control Center</div>
        
        {{-- ✅ KLIK PROFIL LANGSUNG KE HALAMAN PROFIL ADMIN --}}
        <a href="{{ route('admin.profil.index') }}" class="sidebar-profile {{ request()->routeIs('admin.profil.*') ? 'active' : '' }}">
            <img src="{{ auth()->user()->photo_url }}" alt="avatar">
            <div>
                <div class="sidebar-profile-name">{{ auth()->user()->name }}</div>
                <div class="sidebar-profile-role">ADMINISTRATOR</div>
            </div>
        </a>

        <ul class="sidebar-menu">
            <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-grid"></i> Dashboard</a></li>
            <li><a href="{{ route('admin.guru.index') }}" class="{{ request()->routeIs('admin.guru.*') ? 'active' : '' }}"><i class="bi bi-person-badge"></i> Data Guru</a></li>
            <li><a href="{{ route('admin.siswa.index') }}" class="{{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}"><i class="bi bi-people"></i> Data Siswa</a></li>
            <li><a href="{{ route('admin.jurusan.index') }}" class="{{ request()->routeIs('admin.jurusan.*') ? 'active' : '' }}"><i class="bi bi-book"></i> Kategori Jurusan</a></li>
            <li><a href="{{ route('admin.kritik-saran.index') }}" class="{{ request()->routeIs('admin.kritik-saran.*') ? 'active' : '' }}"><i class="bi bi-chat-dots"></i> Feedback</a></li>
            <li><a href="{{ route('admin.kontak.index') }}" class="{{ request()->routeIs('admin.kontak.*') ? 'active' : '' }}"><i class="bi bi-envelope"></i> Pesan Masuk</a></li>
            <li><a href="{{ route('admin.leaderboard.index') }}" class="{{ request()->routeIs('admin.leaderboard.*') ? 'active' : '' }}"><i class="bi bi-trophy"></i> Leaderboard</a></li>
            <li><a href="{{ route('admin.statistik.index') }}" class="{{ request()->routeIs('admin.statistik.*') ? 'active' : '' }}"><i class="bi bi-bar-chart"></i> Laporan</a></li>
            <li><a href="{{ route('admin.pengaturan.index') }}" class="{{ request()->routeIs('admin.pengaturan.*') ? 'active' : '' }}"><i class="bi bi-gear"></i> Pengaturan</a></li>
        </ul>

        <div class="sidebar-logout">
            <ul class="sidebar-menu">
                <li>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </a>
                </li>
            </ul>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </div>
    </aside>

    <main class="main-content">
        @if(session('success')) <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div> @endif
        @if(session('error')) <div class="alert alert-danger border-0 shadow-sm">{{ session('error') }}</div> @endif
        @yield('content')
    </main>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
</body>
</html>