@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <div class="page-label">ADMIN DASHBOARD</div>
        <h1 class="page-title">Selamat Datang, {{ auth()->user()->name }}! 👋</h1>
        <p class="page-subtitle mb-0">Ringkasan sistem GuruKuu & integrasi data SiPintu Gateway secara real-time.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.pengaturan.index') }}" class="btn btn-primary-custom">
            <i class="bi bi-image me-1"></i> Ganti Thumbnail Hero
        </a>
        <a href="{{ route('landing.index') }}" target="_blank" class="btn btn-outline-custom">
            <i class="bi bi-box-arrow-up-right me-1"></i> Buka Landing Page
        </a>
    </div>
</div>

{{-- BANNER FITUR PENTING: GANTI THUMBNAIL LANDING PAGE & STATUS SIPINTU --}}
<div class="row g-4 mb-4">
    {{-- WIDGET 1: GANTI THUMBNAIL LANDING PAGE HERO --}}
    <div class="col-lg-7">
        <div class="card-custom p-4 h-100 position-relative overflow-hidden" 
             style="background: linear-gradient(135deg, rgba(0,51,102,0.92), rgba(0,51,102,0.8)), url('{{ $heroThumbnail }}') center/cover no-repeat; color: white;">
            <div class="position-relative" style="z-index: 2;">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="badge bg-warning text-dark font-mono px-2 py-1">
                        <i class="bi bi-image-fill me-1"></i> THUMBNAIL LANDING PAGE
                    </span>
                    <a href="{{ route('admin.pengaturan.index') }}" class="btn btn-sm btn-light fw-bold text-dark shadow-sm">
                        <i class="bi bi-pencil-square me-1"></i> Ganti Gambar / Teks
                    </a>
                </div>
                <h5 class="fw-bold mb-2" style="font-size: 1.3rem;">{{ $heroTitle }}</h5>
                <p class="small opacity-75 mb-3" style="max-width: 500px;">
                    Gambar latar gedung sekolah ini ditampilkan di halaman awal sebelum pengguna login. Anda dapat mengubah foto latar atau teksnya kapan saja.
                </p>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge bg-white bg-opacity-25 text-white border border-light">
                        <i class="bi bi-check-circle-fill text-success me-1"></i> Aktif di Beranda
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- WIDGET 2: STATUS KONEKSI SIPINTU & 1-CLICK SYNC --}}
    <div class="col-lg-5">
        <div class="card-custom p-4 h-100 d-flex flex-column justify-content-between" style="border-left: 4px solid {{ ($ping['success'] ?? false) ? 'var(--accent)' : '#dc3545' }};">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-hdd-network-fill text-primary me-2"></i>SiPintu Gateway
                    </h6>
                    <span class="badge {{ ($ping['success'] ?? false) ? 'bg-success' : 'bg-danger' }}">
                        <i class="bi {{ ($ping['success'] ?? false) ? 'bi-check-circle-fill' : 'bi-exclamation-octagon-fill' }} me-1"></i>
                        {{ ($ping['success'] ?? false) ? 'ONLINE' : 'OFFLINE' }}
                    </span>
                </div>
                <div class="text-muted small mb-3">
                    Latency: <strong>{{ $ping['latency_ms'] ?? 0 }} ms</strong> | Kredensial: <strong>Valid</strong>
                </div>
                <div class="p-2 rounded mb-3" style="background: var(--bg-light); font-size: 0.85rem;">
                    <div class="d-flex justify-content-between">
                        <span>Database Lokal:</span>
                        <strong>{{ $totalGuru }} Guru / {{ $totalSiswa }} Siswa Aktif</strong>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.sipintu.sync-all-full') }}" method="POST" onsubmit="return confirm('Mulai sinkronisasi pembaruan seluruh Guru & Siswa dari SiPintu?')">
                @csrf
                <button type="submit" class="btn btn-outline-success w-100 btn-sm py-2 fw-bold">
                    <i class="bi bi-cloud-arrow-down-fill me-1"></i> Sinkronkan Data SiPintu
                </button>
            </form>
        </div>
    </div>
</div>

{{-- RINGKASAN STATISTIK UTAMA --}}
<div class="row g-4 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-card" style="border-left: 4px solid var(--primary);">
            <div class="stat-card-label">TOTAL GURU</div>
            <div class="stat-card-value text-primary">{{ $totalGuru ?? 0 }}</div>
            <small class="text-muted"><a href="{{ route('admin.guru.index') }}" class="text-decoration-none">Kelola Guru &rarr;</a></small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="border-left: 4px solid var(--accent);">
            <div class="stat-card-label">TOTAL SISWA AKTIF</div>
            <div class="stat-card-value text-success">{{ $totalSiswa ?? 0 }}</div>
            <small class="text-muted"><a href="{{ route('admin.siswa.index') }}" class="text-decoration-none">Kelola Siswa &rarr;</a></small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="border-left: 4px solid var(--secondary);">
            <div class="stat-card-label">TOTAL PENILAIAN</div>
            <div class="stat-card-value" style="color: #d4a017;">{{ $totalPenilaian ?? 0 }}</div>
            <small class="text-muted"><a href="{{ route('admin.leaderboard.index') }}" class="text-decoration-none">Lihat Leaderboard &rarr;</a></small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-card" style="border-left: 4px solid #6366f1;">
            <div class="stat-card-label">KELAS & JURUSAN</div>
            <div class="stat-card-value" style="color: #6366f1; font-size: 1.5rem;">
                {{ $totalKelas ?? 0 }} <span class="fs-6 text-muted">Kelas / {{ $totalJurusan ?? 0 }} Jurusan</span>
            </div>
            <small class="text-muted"><a href="{{ route('admin.jurusan.index') }}" class="text-decoration-none">Kelola Jurusan &rarr;</a></small>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- TOP GURU TERBAIK --}}
    <div class="col-md-6">
        <div class="card-custom p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-trophy-fill me-2 text-warning"></i>Top Guru Terbaik</h5>
                <a href="{{ route('admin.leaderboard.index') }}" class="btn btn-sm btn-outline-custom">Lihat Semua</a>
            </div>

            @if(isset($topGuru) && count($topGuru) > 0)
                @foreach($topGuru as $index => $g)
                <div class="d-flex align-items-center mb-3 p-2 rounded" style="background:var(--bg-light);">
                    <div class="fs-4 me-3">{{ $index == 0 ? '🥇' : ($index == 1 ? '🥈' : '🥉') }}</div>
                    <img src="{{ $g->photo_url }}" class="rounded-circle me-3" style="width:40px; height:40px; object-fit:cover;"
                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($g->nama) }}&background=003366&color=fff'">
                    <div class="flex-grow-1">
                        <div class="fw-bold">{{ $g->nama }}</div>
                        <small class="text-muted">{{ $g->jurusan->nama_jurusan ?? ucfirst($g->kategori) }}</small>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-warning"><i class="bi bi-star-fill"></i> {{ number_format($g->rata_rata_nilai, 1) }}</div>
                        <small class="text-muted">{{ $g->total_penilaian }} penilaian</small>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                    Belum ada data ulasan penilaian dari siswa.
                </div>
            @endif
        </div>
    </div>

    {{-- KRITIK & SARAN TERBARU --}}
    <div class="col-md-6">
        <div class="card-custom p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0"><i class="bi bi-chat-dots-fill me-2 text-primary"></i>Kritik & Saran Terbaru</h5>
                <a href="{{ route('admin.kritik-saran.index') }}" class="btn btn-sm btn-outline-custom">Lihat Semua</a>
            </div>

            @if(isset($feedbacks) && count($feedbacks) > 0)
                @foreach($feedbacks as $f)
                <div class="mb-3 p-3 rounded" style="background:var(--bg-light);">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong class="small text-dark"><i class="bi bi-incognito me-1"></i>Siswa (Anonim)</strong>
                        <small class="text-muted">{{ $f->created_at->diffForHumans() }}</small>
                    </div>
                    <p class="mb-1 small text-secondary">
                        {{ Str::limit($f->kritik ?: $f->saran, 100) }}
                    </p>
                    <small class="text-muted font-mono" style="font-size: 0.75rem;">Untuk: <strong>{{ $f->guru->nama ?? '-' }}</strong></small>
                </div>
                @endforeach
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-chat-square-dots fs-2 d-block mb-2"></i>
                    Belum ada kritik & saran yang dikirim siswa.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection