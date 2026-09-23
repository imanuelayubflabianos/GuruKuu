@extends('layouts.guru')
@section('title', 'Leaderboard Guru')

@section('content')
<div class="page-header">
    <div class="page-label">PENCAPAIAN TERTINGGI</div>
    <h1 class="page-title">Leaderboard Guru</h1>
    <p class="page-subtitle">Peringkat guru terbaik berdasarkan penilaian dan ulasan objektif siswa.</p>
</div>

@include('components.leaderboard-filter')

{{-- Podium Top 3 --}}
@php
    $list = $leaderboard ?? collect();
    $top1 = $list->get(0);
    $top2 = $list->get(1);
    $top3 = $list->get(2);
@endphp

@if($top1)
<div class="row g-2 g-md-4 mb-4 mb-md-5 align-items-end justify-content-center gk-podium-row">
    {{-- #2 PERAK --}}
    <div class="col-4 col-md-4 order-1 order-md-1 gk-podium-col gk-podium-2">
        @if($top2)
        @php $pct2 = round(($top2->rata_rata_nilai / 5) * 100); @endphp
        <div class="card-custom gk-podium-card p-2 p-md-4 text-center h-100" style="border: 1px solid rgba(148, 163, 184, 0.4); box-shadow: 0 8px 24px rgba(0,0,0,0.04);">
            <div class="mb-2 mb-md-3">
                <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: #94a3b8; color: #fff; font-size: 0.75rem;">
                    <i class="bi bi-award-fill me-1"></i> #2 PERAK
                </span>
            </div>
            <div class="position-relative d-inline-block mb-2 mb-md-3">
                <img src="{{ $top2->photo_url }}" class="rounded-circle shadow-sm gk-podium-avatar-2" width="90" height="90" style="object-fit: cover; border: 3px solid #94a3b8;">
            </div>
            <h5 class="fw-bold mb-1 gk-podium-nama" title="{{ $top2->nama }}">{{ $top2->nama }}</h5>
            <div class="font-mono mb-2 mb-md-3 gk-podium-jurusan" style="font-size: 0.72rem; color: var(--text-muted);" title="{{ strtoupper($top2->jurusan?->nama_jurusan ?? 'UMUM') }}">{{ strtoupper($top2->jurusan?->nama_jurusan ?? 'UMUM') }}</div>
            <div class="p-1.5 p-md-3 rounded-3 mb-2 mb-md-3 gk-podium-statbox" style="background: var(--bg-light);">
                <div class="fw-bold text-primary gk-podium-score" style="font-size: 1.8rem; line-height: 1;">{{ $pct2 }}% <span class="text-warning fs-6">@for($star = 1; $star <= 5; $star++){{ $star <= round($pct2 / 20) ? '★' : '☆' }}@endfor</span></div>
                <div class="progress mt-1 mt-md-2 mb-1" style="height: 5px; border-radius: 10px;">
                    <div class="progress-bar bg-primary rounded-pill" style="width: {{ $pct2 }}%;"></div>
                </div>
                <small class="text-muted font-mono gk-podium-reviews" style="font-size: 0.72rem;">{{ $top2->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa memilih' : 'ulasan' }}</small>
            </div>
            <a href="{{ route('guru.detail', $top2->id) }}" class="btn btn-outline-custom btn-sm btn-podium w-100 rounded-pill">
                <i class="bi bi-eye me-1"></i> Detail
            </a>
        </div>
        @endif
    </div>

    {{-- #1 EMAS (CENTER PODIUM) --}}
    <div class="col-4 col-md-4 order-2 order-md-2 mb-0 gk-podium-col gk-podium-1">
        @php $pct1 = round(($top1->rata_rata_nilai / 5) * 100); @endphp
        <div class="card-custom gk-podium-card p-2.5 p-md-5 text-center position-relative" style="border: 2px solid #f59e0b; box-shadow: 0 16px 36px rgba(245, 158, 11, 0.16); background: var(--bg-card); transform: translateY(-8px);">
            <div class="mb-2 mb-md-3">
                <span class="badge rounded-pill px-2.5 px-md-3.5 py-1 py-md-1.5 fw-bold shadow-sm" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: #ffffff; font-size: 0.8rem; letter-spacing: 0.5px;">
                    <i class="bi bi-trophy-fill me-1"></i> #1 EMAS
                </span>
            </div>
            <div class="position-relative d-inline-block mb-2 mb-md-3">
                <img src="{{ $top1->photo_url }}" class="rounded-circle shadow gk-podium-avatar-1" width="115" height="115" style="object-fit: cover; border: 4px solid #f59e0b;">
            </div>
            <h4 class="fw-bold mb-1 gk-podium-nama" title="{{ $top1->nama }}">{{ $top1->nama }}</h4>
            <div class="font-mono mb-2 mb-md-3 gk-podium-jurusan" style="font-size: 0.75rem; letter-spacing: 1px; color: var(--secondary);" title="{{ strtoupper($top1->jurusan?->nama_jurusan ?? 'UMUM') }}">{{ strtoupper($top1->jurusan?->nama_jurusan ?? 'UMUM') }}</div>
            <div class="p-2 p-md-3 rounded-3 mb-2 mb-md-3 gk-podium-statbox" style="background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.2);">
                <div class="fw-bold text-warning gk-podium-score" style="font-size: 2.3rem; line-height: 1;">{{ $pct1 }}% <span class="text-warning fs-6">@for($star = 1; $star <= 5; $star++){{ $star <= round($pct1 / 20) ? '★' : '☆' }}@endfor</span></div>
                <div class="progress mt-1 mt-md-2 mb-1" style="height: 6px; background-color: rgba(245, 158, 11, 0.2); border-radius: 10px;">
                    <div class="progress-bar bg-warning rounded-pill" style="width: {{ $pct1 }}%;"></div>
                </div>
                <small class="text-muted font-mono d-block mt-0.5 mt-md-1 gk-podium-reviews" style="font-size: 0.75rem;">{{ $top1->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa memilih' : 'ulasan' }}</small>
            </div>
            <a href="{{ route('guru.detail', $top1->id) }}" class="btn btn-primary-custom btn-podium w-100 rounded-pill py-1.5 py-md-2 fw-semibold">
                <i class="bi bi-eye me-1"></i> Detail
            </a>
        </div>
    </div>

    {{-- #3 PERUNGGU --}}
    <div class="col-4 col-md-4 order-3 order-md-3 gk-podium-col gk-podium-3">
        @if($top3)
        @php $pct3 = round(($top3->rata_rata_nilai / 5) * 100); @endphp
        <div class="card-custom gk-podium-card p-2 p-md-4 text-center h-100" style="border: 1px solid rgba(217, 119, 6, 0.3); box-shadow: 0 8px 24px rgba(0,0,0,0.04);">
            <div class="mb-2 mb-md-3">
                <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: #d97706; color: #fff; font-size: 0.75rem;">
                    <i class="bi bi-award-fill me-1"></i> #3 PERUNGGU
                </span>
            </div>
            <div class="position-relative d-inline-block mb-2 mb-md-3">
                <img src="{{ $top3->photo_url }}" class="rounded-circle shadow-sm gk-podium-avatar-3" width="90" height="90" style="object-fit: cover; border: 3px solid #d97706;">
            </div>
            <h5 class="fw-bold mb-1 gk-podium-nama" title="{{ $top3->nama }}">{{ $top3->nama }}</h5>
            <div class="font-mono mb-2 mb-md-3 gk-podium-jurusan" style="font-size: 0.72rem; color: var(--text-muted);" title="{{ strtoupper($top3->jurusan?->nama_jurusan ?? 'UMUM') }}">{{ strtoupper($top3->jurusan?->nama_jurusan ?? 'UMUM') }}</div>
            <div class="p-1.5 p-md-3 rounded-3 mb-2 mb-md-3 gk-podium-statbox" style="background: var(--bg-light);">
                <div class="fw-bold text-primary gk-podium-score" style="font-size: 1.8rem; line-height: 1;">{{ $pct3 }}% <span class="text-warning fs-6">@for($star = 1; $star <= 5; $star++){{ $star <= round($pct3 / 20) ? '★' : '☆' }}@endfor</span></div>
                <div class="progress mt-1 mt-md-2 mb-1" style="height: 5px; border-radius: 10px;">
                    <div class="progress-bar bg-primary rounded-pill" style="width: {{ $pct3 }}%;"></div>
                </div>
                <small class="text-muted font-mono gk-podium-reviews" style="font-size: 0.72rem;">{{ $top3->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa memilih' : 'ulasan' }}</small>
            </div>
            <a href="{{ route('guru.detail', $top3->id) }}" class="btn btn-outline-custom btn-sm btn-podium w-100 rounded-pill">
                <i class="bi bi-eye me-1"></i> Detail
            </a>
        </div>
        @endif
    </div>
</div>
@endif

{{-- Tabel Ranking Unified --}}
<div class="card-custom">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0"><i class="bi bi-trophy-fill text-warning me-2"></i>Daftar Peringkat Guru</h6>
        <span class="badge bg-primary">{{ $list->count() }} Guru</span>
    </div>
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th style="width: 80px;" class="text-center">RANKING</th>
                    <th>NAMA GURU</th>
                    <th>JURUSAN / KEAHLIAN</th>
                    <th style="width: 200px;">{{ $mode === 'partisipasi' ? 'PARTISIPASI KELAS' : 'RATING KEPUASAN' }}</th>
                    <th class="text-center">{{ $mode === 'partisipasi' ? 'SISWA MEMILIH' : 'TOTAL ULASAN' }}</th>
                    <th class="text-center" style="width: 140px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($list as $index => $g)
                @php 
                    $pct = round(($g->rata_rata_nilai / 5) * 100); 
                    $isSelf = (auth()->check() && (auth()->user()->nis === $g->nip || auth()->user()->email === $g->email));
                @endphp
                <tr class="{{ $isSelf ? 'table-primary' : '' }}">
                    <td class="text-center">
                        @if($index === 0) <span class="fs-4">🥇</span>
                        @elseif($index === 1) <span class="fs-4">🥈</span>
                        @elseif($index === 2) <span class="fs-4">🥉</span>
                        @else <span class="badge bg-light text-dark border font-mono">#{{ $index + 1 }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="{{ $g->photo_url }}" class="rounded-circle me-3" width="44" height="44" style="object-fit: cover;">
                            <div>
                                <strong>{{ $g->nama }}</strong>
                                @if($isSelf)
                                    <span class="badge bg-success ms-1" style="font-size: 0.65rem;">Anda</span>
                                @endif
                                <div class="text-muted small font-mono">{{ $g->nip }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="font-mono" style="font-size: 0.75rem;">{{ strtoupper($g->jurusan?->nama_jurusan ?? 'Umum') }}</td>
                    <td>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold font-mono text-primary" style="font-size: 0.85rem;">{{ $pct }}% <span class="text-warning" aria-label="{{ round($pct / 20) }} dari 5 bintang">@for($star = 1; $star <= 5; $star++){{ $star <= round($pct / 20) ? '★' : '☆' }}@endfor</span></span>
                        </div>
                        <div class="progress" style="height: 6px; border-radius: 10px;">
                            <div class="progress-bar bg-primary rounded-pill" style="width: {{ $pct }}%;"></div>
                        </div>
                    </td>
                    <td class="text-center font-mono">{{ $g->total_penilaian }}</td>
                    <td class="text-center">
                        <a href="{{ route('guru.detail', $g->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye me-1"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        Belum ada data penilaian guru.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
