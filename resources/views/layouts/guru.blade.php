<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - GuruKuu Guru</title>
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

        .sidebar { width: 280px; background: white; border-right: 1px solid var(--border); min-height: 100vh; position: fixed; left: 0; top: 0; z-index: 100; padding: 1.5rem; }
        .sidebar-brand { font-weight: 800; font-size: 1.5rem; color: var(--primary); margin-bottom: 0.25rem; text-decoration: none; display: block; }
        .sidebar-subtitle { font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; color: var(--text-muted); letter-spacing: 2px; margin-bottom: 2rem; }
        .sidebar-profile { display: flex; align-items: center; padding: 1rem; background: var(--bg-light); border-radius: 12px; margin-bottom: 2rem; }
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
    </style>
</head>
<body>
    <aside class="sidebar">
        <a href="{{ route('guru.dashboard') }}" class="sidebar-brand">GuruKuu</a>
        <div class="sidebar-subtitle">Teacher Portal</div>
        
        <div class="sidebar-profile">
            <img src="{{ auth()->user()->photo_url }}" alt="avatar">
            <div>
                <div class="sidebar-profile-name">{{ auth()->user()->name }}</div>
                <div class="sidebar-profile-role">NIP: {{ auth()->user()->nis }}</div>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li><a href="{{ route('guru.dashboard') }}" class="{{ request()->routeIs('guru.dashboard') ? 'active' : '' }}"><i class="bi bi-grid"></i> Dashboard</a></li>
            <li><a href="{{ route('landing.leaderboard') }}" target="_blank"><i class="bi bi-trophy"></i> Leaderboard Publik</a></li>
            <li><a href="{{ route('auth.ganti-password') }}"><i class="bi bi-key"></i> Ganti Password</a></li>
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
    @stack('scripts')
</body>
</html>
