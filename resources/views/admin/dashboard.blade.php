@extends('layouts.admin')
@section('title', 'Dashboard Administrator')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <div class="page-label d-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded-pill mb-2" style="background: rgba(0, 51, 102, 0.08); color: var(--primary); font-size: 0.75rem; font-weight: 700; letter-spacing: 0.05em;">
            <i class="bi bi-shield-check"></i> ADMIN DASHBOARD
        </div>
        <h1 class="page-title fw-bold mb-1" style="font-size: 1.75rem; letter-spacing: -0.02em;">Selamat Datang, {{ auth()->user()->name }}! 👋</h1>
        <p class="page-subtitle text-muted mb-0" style="font-size: 0.92rem;">Ringkasan sistem GuruKuu & integrasi data SiPintu Gateway secara real-time.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap align-items-center">
        <div class="dropdown">
            <button class="btn btn-outline-custom dropdown-toggle rounded-pill px-3 py-2 shadow-xs fw-semibold" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.88rem;">
                <i class="bi bi-download me-1.5 text-primary"></i> Export Laporan
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 p-2" style="min-width: 220px;">
                <li>
                    <a class="dropdown-item rounded-2 py-2 small fw-medium" href="{{ route('admin.export.leaderboard.excel') }}">
                        <i class="bi bi-file-earmark-excel text-success me-2 fs-6"></i> Export Leaderboard (.xlsx)
                    </a>
                </li>
                <li>
                    <a class="dropdown-item rounded-2 py-2 small fw-medium" href="{{ route('admin.export.leaderboard.pdf') }}">
                        <i class="bi bi-file-earmark-pdf text-danger me-2 fs-6"></i> Export Leaderboard (.pdf)
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <a class="dropdown-item rounded-2 py-2 small fw-medium" href="{{ route('admin.export.guru.excel') }}">
                        <i class="bi bi-person-lines-fill text-primary me-2 fs-6"></i> Export Data Guru (.xlsx)
                    </a>
                </li>
                <li>
                    <a class="dropdown-item rounded-2 py-2 small fw-medium" href="{{ route('admin.export.siswa.excel') }}">
                        <i class="bi bi-people-fill text-info me-2 fs-6"></i> Export Data Siswa (.xlsx)
                    </a>
                </li>
            </ul>
        </div>
        <a href="{{ route('admin.pengaturan.index') }}" class="btn btn-primary-custom rounded-pill px-3 py-2 shadow-xs fw-semibold" style="font-size: 0.88rem;">
            <i class="bi bi-image me-1.5"></i> Ganti Thumbnail Hero
        </a>
    </div>
</div>

@if(($unreadPelanggaranCount ?? 0) > 0)
    <div class="card-glass border-0 p-3.5 mb-4 shadow-sm position-relative overflow-hidden" style="border-left: 4px solid #ef4444 !important; background: rgba(239, 68, 68, 0.06);">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-2.5">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle text-white d-flex align-items-center justify-content-center flex-shrink-0 shadow-xs" style="width: 44px; height: 44px; background: linear-gradient(135deg, #ef4444, #b91c1c);">
                    <i class="bi bi-shield-exclamation fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1 text-danger d-flex align-items-center gap-2">
                        <span>Peringatan Keamanan Komunitas</span>
                        <span class="badge bg-danger rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">{{ $unreadPelanggaranCount }} Perlu Tindakan</span>
                    </h6>
                    <p class="mb-0 small text-muted">Sistem mendeteksi aktivitas ulasan yang melanggar tata tertib etika sekolah.</p>
                </div>
            </div>
            <a href="{{ route('admin.pelanggaran.index') }}" class="btn btn-danger btn-sm rounded-pill px-3.5 py-2 shadow-xs fw-bold d-inline-flex align-items-center gap-1.5">
                <i class="bi bi-journal-text"></i> Tinjau Log Pelanggaran
            </a>
        </div>

        @if(isset($recentPelanggarans) && $recentPelanggarans->isNotEmpty())
            <div class="p-2.5 rounded-3 bg-white bg-opacity-75 border border-danger border-opacity-20 mt-2">
                <div class="small fw-bold text-dark mb-2 d-flex align-items-center gap-1.5">
                    <i class="bi bi-person-exclamation text-danger"></i> Siswa Terdeteksi Melakukan Pelanggaran Terkini:
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($recentPelanggarans->take(3) as $rp)
                        <div class="p-2 rounded-2 border bg-white small d-flex align-items-center gap-2 shadow-xs">
                            <div class="rounded-circle bg-danger-subtle text-danger p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="bi bi-person-fill fs-6"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-1.5">
                                    <strong class="text-danger" style="font-size: 0.84rem;">{{ $rp->user->name ?? 'Tamu Publik' }}</strong>
                                    @if($rp->user && $rp->user->warning_count > 0)
                                        <span class="badge bg-warning text-dark font-mono" style="font-size: 0.65rem;">⚠️ {{ $rp->user->warning_count }}x Sanksi</span>
                                    @endif
                                </div>
                                <div class="text-muted font-mono" style="font-size: 0.7rem;">
                                    NIS: <strong>{{ $rp->user->nis ?? '-' }}</strong> &bull; 
                                    {{ $rp->user->nama_kelas }}
                                    @if($rp->guru) &bull; Guru: <strong class="text-dark">{{ $rp->guru->nama }}</strong> @endif
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endif


{{-- BANNER FITUR PENTING: GANTI THUMBNAIL LANDING PAGE & STATUS SIPINTU --}}
<div class="row g-4 mb-4">
    {{-- WIDGET 1: GANTI THUMBNAIL LANDING PAGE HERO --}}
    <div class="col-lg-7">
        <div class="card-glass h-100 p-4 position-relative overflow-hidden d-flex flex-column justify-content-between text-white shadow-sm" 
             style="background: linear-gradient(135deg, rgba(0,35,71,0.92), rgba(0,51,102,0.85)), url('{{ $heroThumbnail }}') center/cover no-repeat; min-height: 200px;">
            <div class="position-relative" style="z-index: 2;">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="badge rounded-pill px-3 py-1.5 fw-bold text-dark d-inline-flex align-items-center gap-1.5 shadow-xs" style="background: #fbbf24; font-size: 0.75rem;">
                        <i class="bi bi-image-fill"></i> THUMBNAIL LANDING PAGE
                    </span>
                    <a href="{{ route('admin.pengaturan.index') }}" class="btn btn-sm btn-light rounded-pill px-3 py-1.5 fw-bold text-dark shadow-sm d-inline-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                        <i class="bi bi-pencil-square text-primary"></i> Ganti Gambar / Teks
                    </a>
                </div>
                <h4 class="fw-bold mb-2 text-white" style="letter-spacing: -0.01em;">{{ $heroTitle ?? \App\Models\Setting::get('hero_title', 'SMK Negeri 1 Bangsri') }}</h4>
                <p class="small text-white-50 mb-3" style="max-width: 520px; line-height: 1.5;">
                    Gambar latar gedung sekolah ini ditampilkan di halaman awal sebelum login. Foto latar dan judul dapat disesuaikan sewaktu-waktu.
                </p>
            </div>
            <div class="position-relative d-flex align-items-center gap-2" style="z-index: 2;">
                <span class="badge rounded-pill px-2.5 py-1 text-white border border-white border-opacity-25" style="background: rgba(255,255,255,0.15); font-size: 0.75rem;">
                    <i class="bi bi-check-circle-fill text-success me-1"></i> Aktif di Beranda Publik
                </span>
            </div>
        </div>
    </div>

    {{-- WIDGET 2: STATUS KONEKSI SIPINTU & 1-CLICK SYNC --}}
    <div class="col-lg-5">
        <div class="card-glass h-100 p-4 d-flex flex-column justify-content-between shadow-sm position-relative overflow-hidden">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 36px; height: 36px; background: rgba(0, 51, 102, 0.1); color: var(--primary);">
                            <i class="bi bi-hdd-network-fill"></i>
                        </div>
                        <h6 class="fw-bold mb-0">SiPintu Gateway</h6>
                    </div>
                    @php $isOnline = ($ping['success'] ?? false); @endphp
                    <span class="badge rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 {{ $isOnline ? 'bg-success text-white' : 'bg-danger text-white' }}" style="font-size: 0.75rem;">
                        <span class="rounded-circle bg-white" style="width: 6px; height: 6px; display: inline-block;"></span>
                        {{ $isOnline ? 'ONLINE' : 'OFFLINE' }}
                    </span>
                </div>
                <div class="text-muted small mb-3 d-flex align-items-center justify-content-between">
                    <span>Latency: <strong class="text-dark">{{ $ping['latency_ms'] ?? 0 }} ms</strong></span>
                    <span>Kredensial: <strong class="text-success">Valid</strong></span>
                </div>
                <div class="p-2.5 rounded-3 mb-3" style="background: rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.04); font-size: 0.85rem;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Database Lokal:</span>
                        <span class="fw-bold text-dark">{{ $totalGuru }} Guru &bull; {{ $totalSiswa }} Siswa</span>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.sipintu.sync-all-full') }}" method="POST" onsubmit="return confirm('Mulai sinkronisasi pembaruan seluruh Guru & Siswa dari SiPintu?')">
                @csrf
                <button type="submit" class="btn btn-outline-success w-100 rounded-pill py-2 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-xs" style="font-size: 0.88rem;">
                    <i class="bi bi-cloud-arrow-down-fill fs-6"></i> Sinkronkan Data SiPintu
                </button>
            </form>
        </div>
    </div>
</div>

{{-- RINGKASAN STATISTIK UTAMA (FROSTED GLASS) --}}
<div class="row g-3 g-md-4 mb-4">
    {{-- TOTAL GURU --}}
    <div class="col-6 col-lg-3">
        <div class="card-glass p-3.5 p-md-4 h-100 d-flex flex-column justify-content-between position-relative overflow-hidden shadow-sm">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-uppercase fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.06em;">TOTAL GURU</span>
                <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 38px; height: 38px; background: rgba(0, 51, 102, 0.1); color: var(--primary);">
                    <i class="bi bi-person-badge-fill fs-6"></i>
                </div>
            </div>
            <div class="my-1">
                <div class="fw-bold" style="font-size: 2rem; color: var(--primary); line-height: 1.1;">{{ $totalGuru ?? 0 }}</div>
            </div>
            <div class="pt-2 border-top border-light border-opacity-50">
                <a href="{{ route('admin.guru.index') }}" class="text-decoration-none small fw-semibold text-primary d-inline-flex align-items-center gap-1">
                    Kelola Guru <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- TOTAL SISWA AKTIF --}}
    <div class="col-6 col-lg-3">
        <div class="card-glass p-3.5 p-md-4 h-100 d-flex flex-column justify-content-between position-relative overflow-hidden shadow-sm">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-uppercase fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.06em;">SISWA AKTIF</span>
                <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 38px; height: 38px; background: rgba(16, 185, 129, 0.12); color: #10b981;">
                    <i class="bi bi-people-fill fs-6"></i>
                </div>
            </div>
            <div class="my-1">
                <div class="fw-bold text-success" style="font-size: 2rem; line-height: 1.1;">{{ $totalSiswa ?? 0 }}</div>
            </div>
            <div class="pt-2 border-top border-light border-opacity-50">
                <a href="{{ route('admin.siswa.index') }}" class="text-decoration-none small fw-semibold text-success d-inline-flex align-items-center gap-1">
                    Kelola Siswa <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- TOTAL PENILAIAN --}}
    <div class="col-6 col-lg-3">
        <div class="card-glass p-3.5 p-md-4 h-100 d-flex flex-column justify-content-between position-relative overflow-hidden shadow-sm">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-uppercase fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.06em;">TOTAL PENILAIAN</span>
                <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 38px; height: 38px; background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                    <i class="bi bi-clipboard-check-fill fs-6"></i>
                </div>
            </div>
            <div class="my-1">
                <div class="fw-bold" style="font-size: 2rem; color: #d97706; line-height: 1.1;">{{ $totalPenilaian ?? 0 }}</div>
            </div>
            <div class="pt-2 border-top border-light border-opacity-50">
                <a href="{{ route('admin.leaderboard.index') }}" class="text-decoration-none small fw-semibold text-warning d-inline-flex align-items-center gap-1">
                    Leaderboard <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- KELAS & JURUSAN --}}
    <div class="col-6 col-lg-3">
        <div class="card-glass p-3.5 p-md-4 h-100 d-flex flex-column justify-content-between position-relative overflow-hidden shadow-sm">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="text-uppercase fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.06em;">KELAS & JURUSAN</span>
                <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 38px; height: 38px; background: rgba(99, 102, 241, 0.12); color: #6366f1;">
                    <i class="bi bi-mortarboard-fill fs-6"></i>
                </div>
            </div>
            <div class="my-1">
                <div class="fw-bold" style="font-size: 1.65rem; color: #6366f1; line-height: 1.2;">
                    {{ $totalKelas ?? 0 }} <span class="fs-6 text-muted fw-normal">Kelas</span>
                </div>
                <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $totalJurusan ?? 0 }} Jurusan Terdaftar</small>
            </div>
            <div class="pt-2 border-top border-light border-opacity-50">
                <a href="{{ route('admin.jurusan.index') }}" class="text-decoration-none small fw-semibold text-indigo d-inline-flex align-items-center gap-1" style="color: #6366f1;">
                    Kelola Jurusan <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- TOP GURU TERBAIK (PODIUM GLASS 2-1-3) --}}
    <div class="col-lg-7">
        <div class="card-glass p-4 h-100 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-light border-opacity-50">
                <div>
                    <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-trophy-fill text-warning"></i> Top Guru Sekolah
                    </h5>
                    <small class="text-muted">Peringkat 1 (Tengah), 2 (Kiri), dan 3 (Kanan) berdasarkan ulasan</small>
                </div>
                <a href="{{ route('admin.leaderboard.index') }}" class="btn btn-sm btn-outline-custom rounded-pill px-3 py-1 font-mono fw-semibold" style="font-size: 0.8rem;">
                    Lihat Semua
                </a>
            </div>

            @if(isset($topGuru) && count($topGuru) > 0)
                @php
                    $g1 = $topGuru->get(0);
                    $g2 = $topGuru->get(1);
                    $g3 = $topGuru->get(2);
                @endphp
                <div class="row g-2 g-md-3 align-items-end pt-2 pb-2">
                    {{-- #2 PERAK (KIRI) --}}
                    <div class="col-4">
                        @if($g2)
                        <div class="card-glass p-3 text-center h-100 border-0 shadow-xs" style="background: rgba(148, 163, 184, 0.08); border-top: 3px solid #94a3b8 !important;">
                            <div class="position-relative d-inline-block mb-2">
                                <img src="{{ $g2->photo_url }}" class="rounded-circle shadow-xs" style="width: 54px; height: 54px; object-fit: cover; border: 2.5px solid #94a3b8;"
                                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($g2->nama) }}&background=94a3b8&color=fff'">
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-secondary text-white fw-bold shadow-xs" style="font-size: 0.7rem;">#2</span>
                            </div>
                            <h6 class="fw-bold mb-1 text-truncate" title="{{ $g2->nama }}" style="font-size: 0.82rem;">{{ $g2->nama }}</h6>
                            <small class="text-muted d-block mb-2 text-truncate font-mono" style="font-size: 0.7rem;">{{ $g2->jurusan->nama_jurusan ?? 'Umum' }}</small>
                            <div class="fw-bold text-primary font-mono small mb-1">{{ round(($g2->rata_rata_nilai / 5) * 100) }}%</div>
                            <small class="text-muted font-mono" style="font-size: 0.68rem;">{{ $g2->total_penilaian }} ulasan</small>
                        </div>
                        @endif
                    </div>

                    {{-- #1 EMAS (TENGAH - ELEVATED) --}}
                    <div class="col-4">
                        @if($g1)
                        <div class="card-glass p-3 text-center h-100 position-relative shadow" 
                             style="background: linear-gradient(180deg, rgba(234, 179, 8, 0.14) 0%, rgba(255, 255, 255, 0.85) 100%); border: 1.5px solid rgba(234, 179, 8, 0.5) !important; transform: translateY(-8px); box-shadow: 0 12px 25px -8px rgba(234, 179, 8, 0.35) !important;">
                            <div class="position-relative d-inline-block mb-2">
                                <img src="{{ $g1->photo_url }}" class="rounded-circle shadow-sm" style="width: 66px; height: 66px; object-fit: cover; border: 3px solid #eab308;"
                                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($g1->nama) }}&background=eab308&color=000'">
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark fw-bold shadow-xs" style="font-size: 0.75rem;">
                                    <i class="bi bi-trophy-fill"></i> #1
                                </span>
                            </div>
                            <h6 class="fw-bold mb-1 text-truncate text-dark" title="{{ $g1->nama }}" style="font-size: 0.88rem;">{{ $g1->nama }}</h6>
                            <div class="badge bg-warning bg-opacity-25 text-dark font-mono mb-2 text-truncate px-2 py-0.5 rounded-pill" style="font-size: 0.68rem;">
                                {{ $g1->jurusan->nama_jurusan ?? 'Umum' }}
                            </div>
                            <div class="fw-bold font-mono fs-5 text-warning mb-1">{{ round(($g1->rata_rata_nilai / 5) * 100) }}%</div>
                            <small class="text-muted font-mono" style="font-size: 0.7rem;">{{ $g1->total_penilaian }} ulasan</small>
                        </div>
                        @endif
                    </div>

                    {{-- #3 PERUNGGU (KANAN) --}}
                    <div class="col-4">
                        @if($g3)
                        <div class="card-glass p-3 text-center h-100 border-0 shadow-xs" style="background: rgba(217, 119, 6, 0.08); border-top: 3px solid #d97706 !important;">
                            <div class="position-relative d-inline-block mb-2">
                                <img src="{{ $g3->photo_url }}" class="rounded-circle shadow-xs" style="width: 54px; height: 54px; object-fit: cover; border: 2.5px solid #d97706;"
                                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($g3->nama) }}&background=d97706&color=fff'">
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-white fw-bold shadow-xs" style="background: #d97706; font-size: 0.7rem;">#3</span>
                            </div>
                            <h6 class="fw-bold mb-1 text-truncate" title="{{ $g3->nama }}" style="font-size: 0.82rem;">{{ $g3->nama }}</h6>
                            <small class="text-muted d-block mb-2 text-truncate font-mono" style="font-size: 0.7rem;">{{ $g3->jurusan->nama_jurusan ?? 'Umum' }}</small>
                            <div class="fw-bold text-primary font-mono small mb-1">{{ round(($g3->rata_rata_nilai / 5) * 100) }}%</div>
                            <small class="text-muted font-mono" style="font-size: 0.68rem;">{{ $g3->total_penilaian }} ulasan</small>
                        </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                    Belum ada data ulasan penilaian dari siswa.
                </div>
            @endif
        </div>
    </div>

    {{-- KRITIK & SARAN TERBARU (FROSTED GLASS) --}}
    <div class="col-lg-5">
        <div class="card-glass p-4 h-100 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-light border-opacity-50">
                <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-chat-dots-fill text-primary"></i> Kritik & Saran Terbaru
                </h5>
                <a href="{{ route('admin.kritik-saran.index') }}" class="btn btn-sm btn-outline-custom rounded-pill px-3 py-1 font-mono fw-semibold" style="font-size: 0.8rem;">
                    Lihat Semua
                </a>
            </div>

            @if(isset($feedbacks) && count($feedbacks) > 0)
                <div class="d-flex flex-column gap-3">
                    @foreach($feedbacks as $f)
                    <div class="p-3 rounded-3" style="background: rgba(0, 51, 102, 0.03); border: 1px solid rgba(0, 51, 102, 0.06);">
                        <div class="d-flex justify-content-between align-items-center mb-1.5">
                            @if($f->siswa)
                                <strong class="small text-dark d-flex align-items-center gap-1.5">
                                    <i class="bi bi-person-circle text-primary"></i>{{ $f->siswa->name }}
                                    <span class="badge bg-light text-muted font-mono fw-normal" style="font-size: 0.7rem;">{{ $f->siswa->nis }}</span>
                                </strong>
                            @else
                                <strong class="small text-muted d-flex align-items-center gap-1.5">
                                    <i class="bi bi-incognito text-secondary"></i> Siswa (Anonim)
                                </strong>
                            @endif
                            <small class="text-muted font-mono" style="font-size: 0.72rem;">{{ $f->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-2 small text-secondary" style="line-height: 1.45;">
                            &ldquo;{{ Str::limit($f->kritik ?: $f->saran, 95) }}&rdquo;
                        </p>
                        <div class="d-flex align-items-center gap-1.5">
                            <span class="badge bg-white text-dark border font-mono fw-semibold" style="font-size: 0.72rem;">
                                Guru: {{ $f->guru->nama ?? '-' }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-chat-square-dots fs-2 d-block mb-2 opacity-50"></i>
                    Belum ada kritik & saran yang dikirim siswa.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection