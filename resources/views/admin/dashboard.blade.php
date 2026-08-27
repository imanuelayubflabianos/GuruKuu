@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">RINGKASAN SISTEM</div>
        <h1 class="page-title">Dashboard Administrator</h1>
        <p class="page-subtitle">Selamat datang kembali, {{ auth()->user()->name }}!</p>
    </div>
    <div>
        <span class="badge bg-primary px-3 py-2" style="font-size: 0.9rem;">
            <i class="bi bi-calendar-event me-1"></i> {{ $periodeAktif?->nama_periode ?? 'Periode Tidak Aktif' }}
        </span>
    </div>
</div>

{{-- STATISTIK CEPAT --}}
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(0,51,102,0.1); color: var(--primary); flex-shrink: 0;">
                <i class="bi bi-people-fill fs-4"></i>
            </div>
            <div>
                <div class="stat-card-label">Total Guru</div>
                <div class="stat-card-value" style="font-size: 1.8rem;">{{ $totalGuru }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(0,168,107,0.1); color: var(--accent); flex-shrink: 0;">
                <i class="bi bi-person-badge-fill fs-4"></i>
            </div>
            <div>
                <div class="stat-card-label">Total Siswa</div>
                <div class="stat-card-value" style="font-size: 1.8rem;">{{ $totalSiswa }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(255,193,7,0.15); color: #d4a017; flex-shrink: 0;">
                <i class="bi bi-clipboard-data-fill fs-4"></i>
            </div>
            <div>
                <div class="stat-card-label">Total Penilaian</div>
                <div class="stat-card-value" style="font-size: 1.8rem;">{{ $totalPenilaian }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: rgba(99,102,241,0.1); color: #6366f1; flex-shrink: 0;">
                <i class="bi bi-star-fill fs-4"></i>
            </div>
            <div>
                <div class="stat-card-label">Rata-rata Umum</div>
                <div class="stat-card-value" style="font-size: 1.8rem; color: var(--secondary);">{{ $rataRataUmum }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- TOP GURU TERBAIK --}}
    <div class="col-lg-6">
        <div class="card-custom p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-trophy-fill text-warning me-2"></i>Top 5 Guru Terbaik</h5>
                <a href="{{ route('admin.leaderboard.index') }}" class="btn btn-sm btn-outline-custom">Lihat Semua</a>
            </div>

            @if($topGuru->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    <p class="mb-0">Belum ada data penilaian.</p>
                </div>
            @else
                <div class="d-flex flex-column gap-3">
                    @foreach($topGuru as $index => $guru)
                        @php
                            $colors = ['#FFD700', '#C0C0C0', '#CD7F32', '#e2e8f0', '#e2e8f0'];
                            $bgs = ['rgba(255,215,0,0.1)', 'rgba(192,192,192,0.1)', 'rgba(205,127,50,0.1)', 'var(--bg-light)', 'var(--bg-light)'];
                        @endphp
                        <div class="d-flex align-items-center gap-3 p-3 rounded" style="background: {{ $bgs[$index] }}; border-left: 4px solid {{ $colors[$index] }};">
                            <span class="fw-bold fs-5" style="width: 30px; color: {{ $index < 3 ? 'var(--primary)' : 'var(--text-muted)' }};">
                                {{ $index + 1 }}.
                            </span>
                            <img src="{{ $guru->photo_url }}" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-bold">{{ $guru->nama }}</h6>
                                <small class="text-muted">{{ $guru->jurusan?->nama_jurusan ?? 'Umum' }} • {{ $guru->total_penilaian }} vote</small>
                            </div>
                            <div class="fw-bold" style="color: var(--secondary);">
                                <i class="bi bi-star-fill"></i> {{ number_format($guru->rata_rata_nilai, 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- KRITIK & SARAN TERBARU --}}
    <div class="col-lg-6">
        <div class="card-custom p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-chat-dots-fill text-primary me-2"></i>Kritik & Saran Terbaru</h5>
                <a href="{{ route('admin.kritik-saran.index') }}" class="btn btn-sm btn-outline-custom">Lihat Semua</a>
            </div>

            @if($kritikTerbaru->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-journal-x fs-1 d-block mb-2"></i>
                    <p class="mb-0">Belum ada kritik atau saran masuk.</p>
                </div>
            @else
                <div class="d-flex flex-column gap-3">
                    @foreach($kritikTerbaru as $kritik)
                        <div class="p-3 rounded" style="background: var(--bg-light); border-left: 3px solid var(--primary);">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="fw-bold mb-1" style="font-size: 0.9rem;">{{ $kritik->guru->nama }}</h6>
                                    <small class="text-muted">Oleh: {{ $kritik->siswa?->name ?? 'Anonim' }} • {{ $kritik->created_at->diffForHumans() }}</small>
                                </div>
                                @if($kritik->isToxic())
                                    <span class="badge-custom" style="background: #fee2e2; color: #991b1b; font-size: 0.65rem;">
                                        <i class="bi bi-exclamation-triangle-fill"></i> TOXIC
                                    </span>
                                @endif
                            </div>
                            @if($kritik->kritik)
                                <p class="mb-1 small"><strong class="text-muted">Kritik:</strong> {{ Str::limit($kritik->kritik, 80) }}</p>
                            @endif
                            @if($kritik->saran)
                                <p class="mb-0 small"><strong class="text-muted">Saran:</strong> {{ Str::limit($kritik->saran, 80) }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection