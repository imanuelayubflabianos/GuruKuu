<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login') - GuruKuu</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root { 
            --primary: #003366; 
            --bg-light: #f5f7fa; 
        }
        body { 
            background-color: var(--bg-light); 
            font-family: 'Inter', sans-serif; 
        }
        .card-custom { 
            background: white; 
            border-radius: 12px; 
            border: 1px solid #e2e8f0; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.05); 
        }
        .btn-masuk {
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-masuk:hover {
            background-color: #002244;
            color: white;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
    </style>
</head>
<body>

    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>