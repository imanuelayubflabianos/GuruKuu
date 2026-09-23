<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
    
    @php
        $heroImage = \App\Models\Setting::get('hero_image', 'https://images.unsplash.com/photo-1562774053-701939374585?w=1920');
    @endphp
    <style>
        :root { 
            --primary: #003366; 
            --primary-hover: #002244;
            --bg-light: #f8fafc; 
        }
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
        body.auth-page { 
            min-height: 100dvh;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.72), rgba(0, 51, 102, 0.82)), url('{{ $heroImage }}') center/cover no-repeat fixed;
            position: relative;
            padding: 1rem;
            margin: 0;
            overflow-x: hidden;
        }
        body.auth-page::before {
            content: '';
            position: fixed;
            inset: 0;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            background: rgba(0, 0, 0, 0.15);
            pointer-events: none;
            z-index: 0;
        }
        .auth-container-wrapper {
            position: relative;
            z-index: 2;
            width: 100%;
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
        .auth-controls-floating .btn-glass-nav {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: #ffffff;
            transition: all 0.25s ease;
        }
        .auth-controls-floating .btn-glass-nav:hover {
            background: rgba(255, 255, 255, 0.4);
            transform: translateY(-2px);
            color: #ffffff;
        }
        [data-theme="dark"] .auth-controls-floating .btn-glass-nav {
            background: rgba(15, 23, 42, 0.6);
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
        <button class="btn btn-glass-nav p-2 rounded-circle shadow-sm" type="button" onclick="GuruKuuTheme.toggleTheme()" title="Mode Gelap / Terang">
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