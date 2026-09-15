@extends('layouts.siswa')
@section('title', 'Leaderboard Guru')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <div class="page-label">PENCAPAIAN TERTINGGI</div>
        <h1 class="page-title">Leaderboard Guru</h1>
        <p class="page-subtitle">Peringkat guru terbaik berdasarkan evaluasi dan ulasan objektif siswa.</p>
    </div>
    <div>
        <a href="{{ url('/') }}" class="btn btn-outline-custom">
            <i class="bi bi-house-door me-1"></i> Ke Beranda Publik
        </a>
    </div>
</div>

{{-- Podium Top 3 --}}
@php
    $list = $leaderboard ?? collect();
    $top1 = $list->get(0);
    $top2 = $list->get(1);
    $top3 = $list->get(2);
@endphp

@if($top1)
<div class="row g-4 mb-5 align-items-end">
    {{-- #2 --}}
    <div class="col-md-4">
        @if($top2)
        @php $pct2 = round(($top2->rata_rata_nilai / 5) * 100); @endphp
        <div class="card-custom p-4 text-center">
            <div class="position-relative d-inline-block mb-3">
                <img src="{{ $top2->photo_url }}" class="rounded-circle" width="100" height="100" style="object-fit: cover; border: 4px solid var(--border);">
                <span class="position-absolute bottom-0 end-0 bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: 700;">2</span>
            </div>
            <h5 class="fw-bold mb-1">{{ $top2->nama }}</h5>
            <div class="font-mono" style="font-size: 0.7rem; color: var(--text-muted); letter-spacing: 1px;">{{ strtoupper($top2->jurusan?->nama_jurusan ?? 'UMUM') }}</div>
            <div class="mt-3 px-3">
                <div class="d-flex justify-content-between font-mono mb-1" style="font-size: 0.75rem;">
                    <span class="text-muted">Rating:</span>
                    <span class="fw-bold text-primary">{{ $pct2 }}%</span>
                </div>
                <div class="progress" style="height: 6px; border-radius: 10px;">
                    <div class="progress-bar bg-primary rounded-pill" style="width: {{ $pct2 }}%;"></div>
                </div>
                <small class="text-muted mt-2 d-block">{{ $top2->total_penilaian }} Ulasan</small>
            </div>
        </div>
        @endif
    </div>

    {{-- #1 --}}
    <div class="col-md-4">
        @php $pct1 = round(($top1->rata_rata_nilai / 5) * 100); @endphp
        <div class="card-custom p-5 text-center" style="background: var(--primary); color: white; border: none;">
            <div class="position-relative d-inline-block mb-3">
                <img src="{{ $top1->photo_url }}" class="rounded-circle" width="120" height="120" style="object-fit: cover; border: 4px solid var(--secondary);">
                <span class="position-absolute bottom-0 end-0 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: 700; background: var(--secondary); color: var(--primary);">1</span>
            </div>
            <h4 class="fw-bold mb-1 text-white">{{ $top1->nama }}</h4>
            <div class="font-mono" style="font-size: 0.75rem; letter-spacing: 2px; color: var(--secondary);">{{ strtoupper($top1->jurusan?->nama_jurusan ?? 'UMUM') }}</div>
            <div class="row g-3 mt-3">
                <div class="col-6">
                    <div class="font-mono" style="font-size: 0.65rem; letter-spacing: 1px; opacity: 0.8;">KEPUASAN</div>
                    <div class="fw-bold fs-3 text-warning">{{ $pct1 }}%</div>
                </div>
                <div class="col-6">
                    <div class="font-mono" style="font-size: 0.65rem; letter-spacing: 1px; opacity: 0.8;">TOTAL ULASAN</div>
                    <div class="fw-bold fs-3 text-white">{{ $top1->total_penilaian }}</div>
                </div>
            </div>
            <div class="progress mt-3" style="height: 7px; background-color: rgba(255,255,255,0.2); border-radius: 10px;">
                <div class="progress-bar bg-warning rounded-pill" style="width: {{ $pct1 }}%;"></div>
            </div>
        </div>
    </div>

    {{-- #3 --}}
    <div class="col-md-4">
        @if($top3)
        @php $pct3 = round(($top3->rata_rata_nilai / 5) * 100); @endphp
        <div class="card-custom p-4 text-center">
            <div class="position-relative d-inline-block mb-3">
                <img src="{{ $top3->photo_url }}" class="rounded-circle" width="100" height="100" style="object-fit: cover; border: 4px solid var(--border);">
                <span class="position-absolute bottom-0 end-0 bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: 700;">3</span>
            </div>
            <h5 class="fw-bold mb-1">{{ $top3->nama }}</h5>
            <div class="font-mono" style="font-size: 0.7rem; color: var(--text-muted); letter-spacing: 1px;">{{ strtoupper($top3->jurusan?->nama_jurusan ?? 'UMUM') }}</div>
            <div class="mt-3 px-3">
                <div class="d-flex justify-content-between font-mono mb-1" style="font-size: 0.75rem;">
                    <span class="text-muted">Rating:</span>
                    <span class="fw-bold text-primary">{{ $pct3 }}%</span>
                </div>
                <div class="progress" style="height: 6px; border-radius: 10px;">
                    <div class="progress-bar bg-primary rounded-pill" style="width: {{ $pct3 }}%;"></div>
                </div>
                <small class="text-muted mt-2 d-block">{{ $top3->total_penilaian }} Ulasan</small>
            </div>
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
                    <th style="width: 200px;">RATING KEPUASAN</th>
                    <th class="text-center">TOTAL ULASAN</th>
                    <th class="text-center" style="width: 140px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($list as $index => $g)
                @php $pct = round(($g->rata_rata_nilai / 5) * 100); @endphp
                <tr>
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
                                <div class="text-muted small font-mono">{{ $g->nip }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="font-mono" style="font-size: 0.75rem;">{{ strtoupper($g->jurusan?->nama_jurusan ?? 'Umum') }}</td>
                    <td>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold font-mono text-primary" style="font-size: 0.85rem;">{{ $pct }}%</span>
                        </div>
                        <div class="progress" style="height: 6px; border-radius: 10px;">
                            <div class="progress-bar bg-primary rounded-pill" style="width: {{ $pct }}%;"></div>
                        </div>
                    </td>
                    <td class="text-center font-mono">{{ $g->total_penilaian }}</td>
                    <td class="text-center">
                        <a href="{{ route('siswa.guru.show', $g->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-info-circle me-1"></i> Detail Guru
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