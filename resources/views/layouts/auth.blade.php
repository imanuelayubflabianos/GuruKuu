<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') - {{ $siteTitle ?? 'GuruKuu' }}</title>
    @if(!empty($siteLogo))
        <link rel="icon" href="{{ $siteLogo }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}">
    @endif
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root { 
            --primary: #003366; 
            --primary-hover: #002244;
            --bg-light: #f8fafc; 
        }
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
        html {
            height: 100%;
        }
        body.auth-page { 
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            background-color: var(--bg-light, #f8fafc);
            color: var(--text-dark, #0f172a);
            position: relative;
            padding: 0;
            margin: 0;
            overflow-y: auto !important;
            overflow-x: hidden;
        }
        [data-theme="dark"] body.auth-page {
            background-color: #0b1329;
            color: #f1f5f9;
        }
        .auth-container-wrapper {
            position: relative;
            z-index: 2;
            width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        .auth-controls-floating {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1050;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .auth-controls-floating .btn-theme-nav {
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid var(--border, #e2e8f0);
            color: var(--text-dark, #0f172a);
            transition: all 0.25s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }
        .auth-controls-floating .btn-theme-nav:hover {
            transform: translateY(-2px);
            background: #ffffff;
        }
        [data-theme="dark"] .auth-controls-floating .btn-theme-nav {
            background: rgba(15, 23, 42, 0.85);
            border-color: rgba(255, 255, 255, 0.15);
            color: #f8fafc;
        }
    </style>
    <link href="{{ asset('css/gurukuu-theme.css') }}" rel="stylesheet">
    <script src="{{ asset('js/gurukuu-theme.js') }}"></script>
</head>
<body class="auth-page">
    {{-- FLOATING THEME CONTROL --}}
    <div class="auth-controls-floating">
        <button class="btn btn-theme-nav p-2 rounded-circle shadow-sm" type="button" onclick="GuruKuuTheme.toggleTheme()" title="Mode Gelap / Terang">
            <i class="bi bi-moon-stars-fill gk-theme-icon fs-5"></i>
        </button>
    </div>

    <div class="auth-container-wrapper">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>