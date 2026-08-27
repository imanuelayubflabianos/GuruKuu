<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login') - GuruKuu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #003366;
            --primary-light: #004080;
            --bg-light: #f5f7fa;
            --text-dark: #1a1a2e;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }
        * { font-family: 'Inter', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        body { background-color: var(--bg-light); }
        
        .card-custom {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--border);
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }
        .btn-masuk {
            background: var(--primary);
            color: white;
            padding: 0.6rem 1.25rem;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            text-decoration: none;
            transition: all 0.3s;
        }
        .btn-masuk:hover { background: var(--primary-light); color: white; }
    </style>
</head>
<body>
    {{-- ✅ BAGIAN ATAS SUDAH DIHAPUS --}}

    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>