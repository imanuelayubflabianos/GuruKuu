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
    @if(!$top2)
        {{-- HANYA 1 GURU TOP --}}
        @php $pct1 = round(($top1->rata_rata_nilai / 5) * 100); @endphp
        <div class="row justify-content-center mb-4 mb-md-5">
            <div class="col-12 col-sm-10 col-md-8 col-lg-5">
                <div class="gk-podium-card-revised is-first p-4 p-md-5 text-center d-flex flex-column justify-content-between shadow position-relative">
                    <div>
                        <div class="mb-3">
                            <span class="badge rounded-pill px-4 py-1.5 fw-bold" style="background: #fef08a; color: #854d0e; font-size: 0.9rem;">
                                #1st
                            </span>
                        </div>
                        <div class="gk-avatar-red-wrap mb-3" style="width: 114px; height: 114px; padding: 4px;">
                            <img src="{{ $top1->photo_url }}" width="114" height="114" alt="{{ $top1->nama }}">
                        </div>
                        <h4 class="fw-bold mb-1 text-dark" style="font-size: 1.25rem;" title="{{ $top1->nama }}">{{ $top1->nama }}</h4>
                        <div class="text-muted small mb-2">{{ strtoupper($top1->jurusan?->nama_jurusan ?? 'UMUM') }}</div>
                        <div class="text-warning mb-2" style="font-size: 1.1rem; letter-spacing: 2.5px;">
                            @php $stars1 = round($pct1 / 20); @endphp
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $i <= $stars1 ? 'bi-star-fill' : 'bi-star text-muted opacity-25' }}"></i>
                            @endfor
                        </div>
                        <div class="mb-2">
                            <div class="gk-progress-pill mx-auto" style="width: 155px; background: #334155;">
                                <div class="gk-progress-pill-fill" style="width: {{ $pct1 }}%;"></div>
                                <span class="position-relative" style="z-index: 2;">{{ $pct1 }}%</span>
                            </div>
                        </div>
                        <small class="text-muted font-mono d-block mb-3" style="font-size: 0.8rem;">{{ $top1->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa memilih' : 'ulasan' }}</small>
                    </div>

                    <a href="{{ route('guru.detail', $top1->id) }}" class="btn btn-primary-custom w-100 rounded-pill py-2.5 fw-semibold mb-3" style="background: #003366;">
                        <i class="bi bi-eye me-1"></i> Detail Guru
                    </a>

                    <div class="d-flex align-items-center justify-content-end gap-1.5 pt-3 border-top mt-auto" style="min-height: 42px;">
                        @forelse($top1->penghargaan as $penghargaan)
                            @if($penghargaan->badge)
                                <span class="gk-badge-mini-icon" style="background: {{ $penghargaan->badge->warna }}18; color: {{ $penghargaan->badge->warna }}; border-color: {{ $penghargaan->badge->warna }}33;" data-bs-toggle="tooltip" title="{{ $penghargaan->badge->nama_badge }}: {{ $penghargaan->badge->deskripsi }}">
                                    @if(str_starts_with($penghargaan->badge->icon, 'bi-'))<i class="bi {{ $penghargaan->badge->icon }}"></i>@else{{ $penghargaan->badge->icon }}@endif
                                </span>
                            @endif
                        @empty
                            <span class="gk-badge-mini-icon" style="background: #ec489918; color: #db2777; border-color: #ec489933;" title="Guru Terbaik">🏆</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @elseif(!$top3)
        {{-- HANYA 2 GURU TOP --}}
        @php
            $pct1 = round(($top1->rata_rata_nilai / 5) * 100);
            $pct2 = round(($top2->rata_rata_nilai / 5) * 100);
        @endphp
        <div class="row g-3 g-md-4 justify-content-center align-items-end mb-4 mb-md-5">
            {{-- #2 PERAK --}}
            <div class="col-6 col-md-5">
                <div class="gk-podium-card-revised p-3.5 p-md-4 text-center h-100 d-flex flex-column justify-content-between shadow-sm">
                    <div>
                        <div class="mb-3">
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #f1f5f9; color: #475569; font-size: 0.8rem;">
                                #2nd
                            </span>
                        </div>
                        <div class="gk-avatar-red-wrap mb-3">
                            <img src="{{ $top2->photo_url }}" width="96" height="96" alt="{{ $top2->nama }}">
                        </div>
                        <h5 class="fw-bold mb-1 text-dark" style="font-size: 1.05rem;" title="{{ $top2->nama }}">{{ $top2->nama }}</h5>
                        <div class="text-muted small mb-2">{{ strtoupper($top2->jurusan?->nama_jurusan ?? 'UMUM') }}</div>
                        <div class="text-warning mb-2" style="font-size: 0.95rem; letter-spacing: 2px;">
                            @php $stars2 = round($pct2 / 20); @endphp
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $i <= $stars2 ? 'bi-star-fill' : 'bi-star text-muted opacity-25' }}"></i>
                            @endfor
                        </div>
                        <div class="mb-2">
                            <div class="gk-progress-pill mx-auto" style="width: 140px; background: #64748b;">
                                <div class="gk-progress-pill-fill" style="width: {{ $pct2 }}%;"></div>
                                <span class="position-relative" style="z-index: 2;">{{ $pct2 }}%</span>
                            </div>
                        </div>
                        <small class="text-muted font-mono d-block mb-3" style="font-size: 0.78rem;">{{ $top2->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa memilih' : 'ulasan' }}</small>
                    </div>
                    <a href="{{ route('guru.detail', $top2->id) }}" class="btn btn-outline-custom btn-sm w-100 rounded-pill py-2 mb-3">
                        <i class="bi bi-eye me-1"></i> Detail
                    </a>
                    <div class="d-flex align-items-center justify-content-end gap-1.5 pt-3 border-top mt-auto" style="min-height: 42px;">
                        @forelse($top2->penghargaan as $penghargaan)
                            @if($penghargaan->badge)
                                <span class="gk-badge-mini-icon" style="background: {{ $penghargaan->badge->warna }}18; color: {{ $penghargaan->badge->warna }}; border-color: {{ $penghargaan->badge->warna }}33;" data-bs-toggle="tooltip" title="{{ $penghargaan->badge->nama_badge }}: {{ $penghargaan->badge->deskripsi }}">
                                    @if(str_starts_with($penghargaan->badge->icon, 'bi-'))<i class="bi {{ $penghargaan->badge->icon }}"></i>@else{{ $penghargaan->badge->icon }}@endif
                                </span>
                            @endif
                        @empty
                            <span class="gk-badge-mini-icon" style="background: #f59e0b18; color: #d97706; border-color: #f59e0b33;" title="Berprestasi"><i class="bi bi-arrow-up"></i></span>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- #1 EMAS --}}
            <div class="col-6 col-md-5">
                <div class="gk-podium-card-revised is-first p-3.5 p-md-4 text-center h-100 d-flex flex-column justify-content-between shadow position-relative">
                    <div>
                        <div class="mb-3">
                            <span class="badge rounded-pill px-4 py-1.5 fw-bold" style="background: #fef08a; color: #854d0e; font-size: 0.9rem;">
                                #1st
                            </span>
                        </div>
                        <div class="gk-avatar-red-wrap mb-3" style="width: 114px; height: 114px; padding: 4px;">
                            <img src="{{ $top1->photo_url }}" width="114" height="114" alt="{{ $top1->nama }}">
                        </div>
                        <h4 class="fw-bold mb-1 text-dark" style="font-size: 1.25rem;" title="{{ $top1->nama }}">{{ $top1->nama }}</h4>
                        <div class="text-muted small mb-2">{{ strtoupper($top1->jurusan?->nama_jurusan ?? 'UMUM') }}</div>
                        <div class="text-warning mb-2" style="font-size: 1.1rem; letter-spacing: 2.5px;">
                            @php $stars1 = round($pct1 / 20); @endphp
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $i <= $stars1 ? 'bi-star-fill' : 'bi-star text-muted opacity-25' }}"></i>
                            @endfor
                        </div>
                        <div class="mb-2">
                            <div class="gk-progress-pill mx-auto" style="width: 155px; background: #334155;">
                                <div class="gk-progress-pill-fill" style="width: {{ $pct1 }}%;"></div>
                                <span class="position-relative" style="z-index: 2;">{{ $pct1 }}%</span>
                            </div>
                        </div>
                        <small class="text-muted font-mono d-block mb-3" style="font-size: 0.8rem;">{{ $top1->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa memilih' : 'ulasan' }}</small>
                    </div>
                    <a href="{{ route('guru.detail', $top1->id) }}" class="btn btn-primary-custom w-100 rounded-pill py-2 fw-semibold mb-3" style="background: #003366;">
                        <i class="bi bi-eye me-1"></i> Detail
                    </a>
                    <div class="d-flex align-items-center justify-content-end gap-1.5 pt-3 border-top mt-auto" style="min-height: 42px;">
                        @forelse($top1->penghargaan as $penghargaan)
                            @if($penghargaan->badge)
                                <span class="gk-badge-mini-icon" style="background: {{ $penghargaan->badge->warna }}18; color: {{ $penghargaan->badge->warna }}; border-color: {{ $penghargaan->badge->warna }}33;" data-bs-toggle="tooltip" title="{{ $penghargaan->badge->nama_badge }}: {{ $penghargaan->badge->deskripsi }}">
                                    @if(str_starts_with($penghargaan->badge->icon, 'bi-'))<i class="bi {{ $penghargaan->badge->icon }}"></i>@else{{ $penghargaan->badge->icon }}@endif
                                </span>
                            @endif
                        @empty
                            <span class="gk-badge-mini-icon" style="background: #ec489918; color: #db2777; border-color: #ec489933;" title="Guru Terbaik">🏆</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- TAMPILAN 3 PODIUM PENUH (2-1-3) --}}
        @php
            $pct1 = round(($top1->rata_rata_nilai / 5) * 100);
            $pct2 = round(($top2->rata_rata_nilai / 5) * 100);
            $pct3 = round(($top3->rata_rata_nilai / 5) * 100);
        @endphp
        <div class="row g-2 g-md-4 mb-4 mb-md-5 align-items-end justify-content-center gk-podium-row">
            {{-- #2 PERAK --}}
            <div class="col-12 col-md-4 order-2 order-md-1 gk-podium-col gk-podium-2">
                <div class="gk-podium-card-revised p-3 p-md-4 text-center h-100 d-flex flex-column justify-content-between shadow-sm">
                    <div>
                        <div class="mb-3">
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #f1f5f9; color: #475569; font-size: 0.8rem;">
                                #2nd
                            </span>
                        </div>
                        <div class="gk-avatar-red-wrap mb-3">
                            <img src="{{ $top2->photo_url }}" width="96" height="96" alt="{{ $top2->nama }}">
                        </div>
                        <h5 class="fw-bold mb-1 text-dark" style="font-size: 1.05rem;" title="{{ $top2->nama }}">{{ $top2->nama }}</h5>
                        <div class="text-muted small mb-2">{{ strtoupper($top2->jurusan?->nama_jurusan ?? 'UMUM') }}</div>
                        <div class="text-warning mb-2" style="font-size: 0.95rem; letter-spacing: 2px;">
                            @php $stars2 = round($pct2 / 20); @endphp
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $i <= $stars2 ? 'bi-star-fill' : 'bi-star text-muted opacity-25' }}"></i>
                            @endfor
                        </div>
                        <div class="mb-2">
                            <div class="gk-progress-pill mx-auto" style="width: 140px; background: #64748b;">
                                <div class="gk-progress-pill-fill" style="width: {{ $pct2 }}%;"></div>
                                <span class="position-relative" style="z-index: 2;">{{ $pct2 }}%</span>
                            </div>
                        </div>
                        <small class="text-muted font-mono d-block mb-3" style="font-size: 0.78rem;">{{ $top2->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa memilih' : 'ulasan' }}</small>
                    </div>
                    <a href="{{ route('guru.detail', $top2->id) }}" class="btn btn-outline-custom btn-sm w-100 rounded-pill py-2 mb-3">
                        <i class="bi bi-eye me-1"></i> Detail
                    </a>
                    <div class="d-flex align-items-center justify-content-end gap-1.5 pt-3 border-top mt-auto" style="min-height: 42px;">
                        @forelse($top2->penghargaan as $penghargaan)
                            @if($penghargaan->badge)
                                <span class="gk-badge-mini-icon" style="background: {{ $penghargaan->badge->warna }}18; color: {{ $penghargaan->badge->warna }}; border-color: {{ $penghargaan->badge->warna }}33;" data-bs-toggle="tooltip" title="{{ $penghargaan->badge->nama_badge }}: {{ $penghargaan->badge->deskripsi }}">
                                    @if(str_starts_with($penghargaan->badge->icon, 'bi-'))<i class="bi {{ $penghargaan->badge->icon }}"></i>@else{{ $penghargaan->badge->icon }}@endif
                                </span>
                            @endif
                        @empty
                            <span class="gk-badge-mini-icon" style="background: #f59e0b18; color: #d97706; border-color: #f59e0b33;" title="Berprestasi"><i class="bi bi-arrow-up"></i></span>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- #1 EMAS (CENTER PODIUM) --}}
            <div class="col-12 col-md-4 order-1 order-md-2 mb-3 mb-md-0 gk-podium-col gk-podium-1">
                <div class="gk-podium-card-revised is-first p-3.5 p-md-4 text-center h-100 d-flex flex-column justify-content-between shadow position-relative" style="transform: translateY(-8px);">
                    <div>
                        <div class="mb-3">
                            <span class="badge rounded-pill px-4 py-1.5 fw-bold" style="background: #fef08a; color: #854d0e; font-size: 0.9rem;">
                                #1st
                            </span>
                        </div>
                        <div class="gk-avatar-red-wrap mb-3" style="width: 114px; height: 114px; padding: 4px;">
                            <img src="{{ $top1->photo_url }}" width="114" height="114" alt="{{ $top1->nama }}">
                        </div>
                        <h4 class="fw-bold mb-1 text-dark" style="font-size: 1.25rem;" title="{{ $top1->nama }}">{{ $top1->nama }}</h4>
                        <div class="text-muted small mb-2">{{ strtoupper($top1->jurusan?->nama_jurusan ?? 'UMUM') }}</div>
                        <div class="text-warning mb-2" style="font-size: 1.1rem; letter-spacing: 2.5px;">
                            @php $stars1 = round($pct1 / 20); @endphp
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $i <= $stars1 ? 'bi-star-fill' : 'bi-star text-muted opacity-25' }}"></i>
                            @endfor
                        </div>
                        <div class="mb-2">
                            <div class="gk-progress-pill mx-auto" style="width: 155px; background: #334155;">
                                <div class="gk-progress-pill-fill" style="width: {{ $pct1 }}%;"></div>
                                <span class="position-relative" style="z-index: 2;">{{ $pct1 }}%</span>
                            </div>
                        </div>
                        <small class="text-muted font-mono d-block mb-3" style="font-size: 0.8rem;">{{ $top1->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa memilih' : 'ulasan' }}</small>
                    </div>
                    <a href="{{ route('guru.detail', $top1->id) }}" class="btn btn-primary-custom w-100 rounded-pill py-2 fw-semibold mb-3" style="background: #003366;">
                        <i class="bi bi-eye me-1"></i> Detail
                    </a>
                    <div class="d-flex align-items-center justify-content-end gap-1.5 pt-3 border-top mt-auto" style="min-height: 42px;">
                        @forelse($top1->penghargaan as $penghargaan)
                            @if($penghargaan->badge)
                                <span class="gk-badge-mini-icon" style="background: {{ $penghargaan->badge->warna }}18; color: {{ $penghargaan->badge->warna }}; border-color: {{ $penghargaan->badge->warna }}33;" data-bs-toggle="tooltip" title="{{ $penghargaan->badge->nama_badge }}: {{ $penghargaan->badge->deskripsi }}">
                                    @if(str_starts_with($penghargaan->badge->icon, 'bi-'))<i class="bi {{ $penghargaan->badge->icon }}"></i>@else{{ $penghargaan->badge->icon }}@endif
                                </span>
                            @endif
                        @empty
                            <span class="gk-badge-mini-icon" style="background: #ec489918; color: #db2777; border-color: #ec489933;" title="Guru Terbaik">🏆</span>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- #3 PERUNGGU --}}
            <div class="col-12 col-md-4 order-3 order-md-3 gk-podium-col gk-podium-3">
                <div class="gk-podium-card-revised p-3 p-md-4 text-center h-100 d-flex flex-column justify-content-between shadow-sm">
                    <div>
                        <div class="mb-3">
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #fed7aa; color: #9a3412; font-size: 0.8rem;">
                                #3rd
                            </span>
                        </div>
                        <div class="gk-avatar-red-wrap mb-3">
                            <img src="{{ $top3->photo_url }}" width="96" height="96" alt="{{ $top3->nama }}">
                        </div>
                        <h5 class="fw-bold mb-1 text-dark" style="font-size: 1.05rem;" title="{{ $top3->nama }}">{{ $top3->nama }}</h5>
                        <div class="text-muted small mb-2">{{ strtoupper($top3->jurusan?->nama_jurusan ?? 'UMUM') }}</div>
                        <div class="text-warning mb-2" style="font-size: 0.95rem; letter-spacing: 2px;">
                            @php $stars3 = round($pct3 / 20); @endphp
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $i <= $stars3 ? 'bi-star-fill' : 'bi-star text-muted opacity-25' }}"></i>
                            @endfor
                        </div>
                        <div class="mb-2">
                            <div class="gk-progress-pill mx-auto" style="width: 140px; background: #94a3b8;">
                                <div class="gk-progress-pill-fill" style="width: {{ $pct3 }}%;"></div>
                                <span class="position-relative" style="z-index: 2;">{{ $pct3 }}%</span>
                            </div>
                        </div>
                        <small class="text-muted font-mono d-block mb-3" style="font-size: 0.78rem;">{{ $top3->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa memilih' : 'ulasan' }}</small>
                    </div>
                    <a href="{{ route('guru.detail', $top3->id) }}" class="btn btn-outline-custom btn-sm w-100 rounded-pill py-2 mb-3">
                        <i class="bi bi-eye me-1"></i> Detail
                    </a>
                    <div class="d-flex align-items-center justify-content-end gap-1.5 pt-3 border-top mt-auto" style="min-height: 42px;">
                        @forelse($top3->penghargaan as $penghargaan)
                            @if($penghargaan->badge)
                                <span class="gk-badge-mini-icon" style="background: {{ $penghargaan->badge->warna }}18; color: {{ $penghargaan->badge->warna }}; border-color: {{ $penghargaan->badge->warna }}33;" data-bs-toggle="tooltip" title="{{ $penghargaan->badge->nama_badge }}: {{ $penghargaan->badge->deskripsi }}">
                                    @if(str_starts_with($penghargaan->badge->icon, 'bi-'))<i class="bi {{ $penghargaan->badge->icon }}"></i>@else{{ $penghargaan->badge->icon }}@endif
                                </span>
                            @endif
                        @empty
                            <span class="gk-badge-mini-icon" style="background: #10b98118; color: #059669; border-color: #10b98133;" title="Inspiratif"><i class="bi bi-lightbulb"></i></span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif

{{-- Tabel Ranking Unified --}}
<div class="card-custom">
    <div class="card-header bg-white px-4 py-3.5 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 d-flex align-items-center gap-2"><i class="bi bi-trophy-fill text-warning fs-5"></i><span>Daftar Peringkat Guru</span></h6>
        <span class="badge bg-primary px-3 py-1.5 rounded-pill">{{ $list->count() }} Guru</span>
    </div>
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th style="width: 80px;" class="text-center">RANKING</th>
                    <th>NAMA GURU</th>
                    <th class="text-center">JURUSAN / KEAHLIAN</th>
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
                                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                    <strong>{{ $g->nama }}</strong>
                                    @if($isSelf)
                                        <span class="badge bg-success ms-1" style="font-size: 0.65rem;">Anda</span>
                                    @endif
                                    @foreach($g->penghargaan ?? [] as $p)
                                        @if($p->badge)
                                            <span class="badge rounded-pill border" style="background: {{ $p->badge->warna }}15; color: {{ $p->badge->warna }}; border-color: {{ $p->badge->warna }}40 !important; font-size: 0.68rem;" title="{{ $p->badge->deskripsi }}">
                                                @if(str_starts_with($p->badge->icon, 'bi-'))<i class="bi {{ $p->badge->icon }}"></i>@else{{ $p->badge->icon }}@endif {{ $p->badge->nama_badge }}
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="text-muted small">{{ $g->kategori_label }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="font-mono text-center" style="font-size: 0.75rem;">{{ strtoupper($g->jurusan?->nama_jurusan ?? 'Umum') }}</td>
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
