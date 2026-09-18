<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terlalu Banyak Permintaan - {{ $siteTitle ?? 'GuruKuu' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .error-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 2.5rem;
            max-width: 520px;
            text-align: center;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        }
        .icon-box {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #fef3c7;
            color: #d97706;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.25rem;
            margin: 0 auto 1.5rem;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="icon-box">
            <i class="bi bi-shield-exclamation"></i>
        </div>
        <span class="badge bg-warning bg-opacity-25 text-dark font-mono px-3 py-1 mb-2" style="font-size: 0.8rem;">429 TOO MANY REQUESTS</span>
        <h4 class="fw-bold text-dark mt-2 mb-2">Aktivitas Terlalu Cepat</h4>
        <p class="text-muted small mb-4">
            Sistem mendeteksi terlalu banyak permintaan dalam waktu sangat singkat dari perangkat Anda demi keamanan dan stabilitas server. Mohon tunggu sekitar 30–60 detik sebelum memuat ulang halaman.
        </p>
        <div class="d-flex gap-2 justify-content-center">
            <a href="javascript:location.reload()" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold" style="background: #003366; border: none;">
                <i class="bi bi-arrow-clockwise me-1"></i> Coba Lagi
            </a>
            <a href="/" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>
