@extends('layouts.siswa')
@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">SELAMAT DATANG</div>
        <h1 class="page-title">Halo, {{ auth()->user()->name }}! 👋</h1>
        @if($kelasAktif)
            <p class="page-subtitle">Kelas: <strong>{{ $kelasAktif->nama_kelas }} - Tingkat {{ $kelasAktif->tingkat }}</strong> ({{ $kelasAktif->jurusan->nama_jurusan }})</p>
        @else
            <p class="page-subtitle text-warning">Anda belum terdaftar di kelas manapun untuk periode ini.</p>
        @endif
    </div>
    <div>
        <span class="badge bg-primary px-3 py-2" style="font-size: 0.9rem;">
            <i class="bi bi-calendar-event me-1"></i> {{ $periodeAktif?->nama_periode ?? 'Periode Tidak Aktif' }}
        </span>
    </div>
</div>

{{-- STATISTIK CEPAT --}}
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card-custom p-4 d-flex align-items-center gap-3" style="border-left: 4px solid var(--primary);">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(0,51,102,0.1); color: var(--primary);">
                <i class="bi bi-people-fill fs-4"></i>
            </div>
            <div>
                <div class="text-muted small font-mono">TOTAL GURU DI KELAS</div>
                <div class="fw-bold fs-4">{{ $totalGuru }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom p-4 d-flex align-items-center gap-3" style="border-left: 4px solid var(--accent);">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(0,168,107,0.1); color: var(--accent);">
                <i class="bi bi-check-circle-fill fs-4"></i>
            </div>
            <div>
                <div class="text-muted small font-mono">SUDAH DINILAI</div>
                <div class="fw-bold fs-4">{{ $jumlahSudah }} <span class="text-muted fs-6">/ {{ $totalGuru }}</span></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom p-4 d-flex align-items-center gap-3" style="border-left: 4px solid var(--secondary);">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(255,193,7,0.15); color: #d4a017;">
                <i class="bi bi-star-fill fs-4"></i>
            </div>
            <div>
                <div class="text-muted small font-mono">SISA PENILAIAN</div>
                <div class="fw-bold fs-4">{{ $totalGuru - $jumlahSudah }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- TOP GURU DI KELAS ANDA --}}
    <div class="col-lg-8">
        <div class="card-custom p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-trophy-fill text-warning me-2"></i>Top Guru di Kelas Anda</h5>
                <a href="{{ route('siswa.leaderboard.index') }}" class="btn btn-sm btn-outline-custom">Lihat Semua</a>
            </div>

            @if($topGuru->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <p class="mb-0">Belum ada data penilaian di kelas Anda.</p>
                </div>
            @else
                <div class="row g-3">
                    @foreach($topGuru as $index => $guru)
                        @php
                            $colors = ['#FFD700', '#C0C0C0', '#CD7F32'];
                            $bgs = ['rgba(255,215,0,0.1)', 'rgba(192,192,192,0.1)', 'rgba(205,127,50,0.1)'];
                        @endphp
                        <div class="col-md-4">
                            <div class="card-custom p-3 text-center h-100" style="border-top: 3px solid {{ $colors[$index] }}; background: {{ $bgs[$index] }};">
                                <div class="position-relative d-inline-block mb-2">
                                    <img src="{{ $guru->photo_url }}" class="rounded-circle" style="width: 60px; height: 60px; object-fit: cover; border: 3px solid {{ $colors[$index] }};">
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background: {{ $colors[$index] }}; color: #000; font-size: 0.75rem; padding: 0.3rem 0.5rem;">
                                        #{{ $index + 1 }}
                                    </span>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem;">{{ $guru->nama }}</h6>
                                <small class="text-muted d-block mb-2" style="font-size: 0.75rem;">{{ $guru->jurusan?->nama_jurusan ?? 'Umum' }}</small>
                                <div class="fw-bold" style="color: var(--secondary); font-size: 1.1rem;">
                                    <i class="bi bi-star-fill"></i> {{ number_format($guru->rata_rata_nilai, 2) }}
                                </div>
                                <a href="{{ route('siswa.guru.show', $guru) }}" class="btn btn-sm btn-primary-custom mt-2 w-100" style="font-size: 0.8rem;">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- RIWAYAT PENILAIAN TERAKHIR --}}
    <div class="col-lg-4">
        <div class="card-custom p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Riwayat Terakhir</h5>
                <a href="{{ route('siswa.riwayat') }}" class="btn btn-sm btn-outline-custom">Semua</a>
            </div>

            @if($riwayatTerakhir->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-journal-x fs-1 d-block mb-2"></i>
                    <p class="mb-0 small">Anda belum memberikan penilaian.</p>
                    <a href="{{ route('siswa.guru.index') }}" class="btn btn-sm btn-primary-custom mt-2">Mulai Menilai</a>
                </div>
            @else
                <div class="d-flex flex-column gap-3">
                    @foreach($riwayatTerakhir as $riwayat)
                        <div class="d-flex align-items-start gap-3 p-2 rounded" style="background: var(--bg-light);">
                            <img src="{{ $riwayat->guru->photo_url }}" class="rounded-circle flex-shrink-0" style="width: 40px; height: 40px; object-fit: cover;">
                            <div class="flex-grow-1">
                                <h6 class="fw-bold mb-1" style="font-size: 0.9rem;">{{ $riwayat->guru->nama }}</h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-success bg-opacity-10 text-success" style="font-size: 0.75rem;">
                                        <i class="bi bi-star-fill"></i> {{ number_format($riwayat->total_nilai / 6, 1) }}
                                    </span>
                                    <small class="text-muted" style="font-size: 0.7rem;">{{ $riwayat->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

{{-- TOMBOL AKSI CEPAT --}}
@if($totalGuru > $jumlahSudah)
<div class="mt-4 text-center">
    <a href="{{ route('siswa.guru.index') }}" class="btn btn-primary-custom px-5 py-3" style="font-size: 1.1rem;">
        <i class="bi bi-pencil-square me-2"></i> Lanjutkan Penilaian Guru
    </a>
</div>
@endif
@endsection