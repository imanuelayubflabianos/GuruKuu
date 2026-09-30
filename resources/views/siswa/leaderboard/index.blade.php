@extends('layouts.siswa')
@section('title', 'Leaderboard Guru')

@section('content')
<div class="text-center mb-4">
    <h1 class="page-title fw-bold text-dark mb-1" style="font-size: 1.85rem;">Leaderboard Guru</h1>
    <p class="text-muted mb-0" style="font-size: 0.95rem;">Peringkat guru terbaik berdasarkan penilaian dan ulasan objektif siswa.</p>
</div>

@include('components.leaderboard-filter')

<div class="mb-3">
    <div class="page-label">PENCAPAIAN TERTINGGI</div>
</div>

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
                        <div class="mb-1">
                            <span class="badge rounded-pill px-4 py-1.5 fw-bold" style="background: #fef08a; color: #854d0e; font-size: 0.9rem;">
                                #1st
                            </span>
                        </div>
                        <div class="gk-podium-badges-row">
                            @foreach(($top1->penghargaan ?? []) as $penghargaan)
                                @if($penghargaan->badge)
                                    <span class="gk-badge-mini-icon" style="background: {{ $penghargaan->badge->warna }}18; color: {{ $penghargaan->badge->warna }}; border-color: {{ $penghargaan->badge->warna }}33;" data-bs-toggle="tooltip" title="{{ $penghargaan->badge->nama_badge }}: {{ $penghargaan->badge->deskripsi }}">
                                        @if(str_starts_with($penghargaan->badge->icon, 'bi-'))<i class="bi {{ $penghargaan->badge->icon }}"></i>@else{{ $penghargaan->badge->icon }}@endif
                                    </span>
                                @endif
                            @endforeach
                        </div>
                        <div class="gk-avatar-clean-wrap mb-3" style="width: 114px; height: 114px;">
                            <img src="{{ $top1->photo_url }}" width="114" height="114" alt="{{ $top1->nama }}">
                        </div>
                        <h4 class="fw-bold mb-1 text-dark" style="font-size: 1.25rem;" title="{{ $top1->nama }}">{{ $top1->nama }}</h4>
                        <div class="text-muted small mb-2">{{ strtoupper($top1->jurusan?->nama_jurusan ?? $top1->kategori_label ?? 'UMUM') }}</div>
                        {{-- Bintang (Support 0.5 Setengah Bintang) --}}
                        <div class="text-warning mb-2" style="font-size: 1.1rem; letter-spacing: 2.5px;">
                            @php
                                $val1 = $pct1 / 20;
                                $stars1 = round($val1 * 2) / 2;
                            @endphp
                            @for($i = 1; $i <= 5; $i++)
                                @if($stars1 >= $i)
                                    <i class="bi bi-star-fill"></i>
                                @elseif($stars1 >= ($i - 0.5))
                                    <i class="bi bi-star-half"></i>
                                @else
                                    <i class="bi bi-star text-muted opacity-25"></i>
                                @endif
                            @endfor
                        </div>
                        <div class="mb-2">
                            <div class="gk-progress-pill gk-pill-rank-1 mx-auto" style="width: 145px; height: 24px; font-size: 0.8rem;">
                                <div class="gk-progress-pill-fill {{ $pct1 >= 75 ? 'gk-bar-blue-high' : ($pct1 >= 50 ? 'gk-bar-blue-mid' : 'gk-bar-blue-low') }}" style="width: {{ $pct1 }}%;"></div>
                                <span class="position-relative text-white" style="z-index: 2; text-shadow: 0 1px 2px rgba(0,0,0,0.6);">{{ $pct1 }}%</span>
                            </div>
                        </div>
                        <small class="text-muted font-mono d-block mb-3" style="font-size: 0.8rem;">{{ $top1->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa memilih' : 'ulasan' }}</small>
                    </div>

                    <a href="{{ route('siswa.guru.show', $top1->id) }}" class="btn btn-sm btn-outline-primary gk-btn-podium-profile mb-2">
                        <i class="bi bi-eye me-1"></i> Profil
                    </a>
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
                        <div class="mb-1">
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #f1f5f9; color: #475569; font-size: 0.8rem;">
                                #2nd
                            </span>
                        </div>
                        <div class="gk-podium-badges-row">
                            @foreach(($top2->penghargaan ?? []) as $penghargaan)
                                @if($penghargaan->badge)
                                    <span class="gk-badge-mini-icon" style="background: {{ $penghargaan->badge->warna }}18; color: {{ $penghargaan->badge->warna }}; border-color: {{ $penghargaan->badge->warna }}33;" data-bs-toggle="tooltip" title="{{ $penghargaan->badge->nama_badge }}: {{ $penghargaan->badge->deskripsi }}">
                                        @if(str_starts_with($penghargaan->badge->icon, 'bi-'))<i class="bi {{ $penghargaan->badge->icon }}"></i>@else{{ $penghargaan->badge->icon }}@endif
                                    </span>
                                @endif
                            @endforeach
                        </div>
                        <div class="gk-avatar-clean-wrap mb-3" style="width: 96px; height: 96px;">
                            <img src="{{ $top2->photo_url }}" width="96" height="96" alt="{{ $top2->nama }}">
                        </div>
                        <h5 class="fw-bold mb-1 text-dark" style="font-size: 1.05rem;" title="{{ $top2->nama }}">{{ $top2->nama }}</h5>
                        <div class="text-muted small mb-2">{{ strtoupper($top2->jurusan?->nama_jurusan ?? $top2->kategori_label ?? 'UMUM') }}</div>
                        <div class="text-warning mb-2" style="font-size: 0.95rem; letter-spacing: 2px;">
                            @php
                                $val2 = $pct2 / 20;
                                $stars2 = round($val2 * 2) / 2;
                            @endphp
                            @for($i = 1; $i <= 5; $i++)
                                @if($stars2 >= $i)
                                    <i class="bi bi-star-fill"></i>
                                @elseif($stars2 >= ($i - 0.5))
                                    <i class="bi bi-star-half"></i>
                                @else
                                    <i class="bi bi-star text-muted opacity-25"></i>
                                @endif
                            @endfor
                        </div>
                        <div class="mb-2">
                            <div class="gk-progress-pill gk-pill-rank-2 mx-auto" style="width: 135px; height: 22px; font-size: 0.74rem;">
                                <div class="gk-progress-pill-fill {{ $pct2 >= 75 ? 'gk-bar-blue-high' : ($pct2 >= 50 ? 'gk-bar-blue-mid' : 'gk-bar-blue-low') }}" style="width: {{ $pct2 }}%;"></div>
                                <span class="position-relative text-white" style="z-index: 2; text-shadow: 0 1px 2px rgba(0,0,0,0.6);">{{ $pct2 }}%</span>
                            </div>
                        </div>
                        <small class="text-muted font-mono d-block mb-3" style="font-size: 0.78rem;">{{ $top2->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa memilih' : 'ulasan' }}</small>
                    </div>
                    <a href="{{ route('siswa.guru.show', $top2->id) }}" class="btn btn-sm btn-outline-primary gk-btn-podium-profile mb-2">
                        <i class="bi bi-eye me-1"></i> Profil
                    </a>
                </div>
            </div>

            {{-- #1 EMAS --}}
            <div class="col-6 col-md-5">
                <div class="gk-podium-card-revised is-first p-3.5 p-md-4 text-center h-100 d-flex flex-column justify-content-between shadow position-relative">
                    <div>
                        <div class="mb-1">
                            <span class="badge rounded-pill px-4 py-1.5 fw-bold" style="background: #fef08a; color: #854d0e; font-size: 0.9rem;">
                                #1st
                            </span>
                        </div>
                        <div class="gk-podium-badges-row">
                            @foreach(($top1->penghargaan ?? []) as $penghargaan)
                                @if($penghargaan->badge)
                                    <span class="gk-badge-mini-icon" style="background: {{ $penghargaan->badge->warna }}18; color: {{ $penghargaan->badge->warna }}; border-color: {{ $penghargaan->badge->warna }}33;" data-bs-toggle="tooltip" title="{{ $penghargaan->badge->nama_badge }}: {{ $penghargaan->badge->deskripsi }}">
                                        @if(str_starts_with($penghargaan->badge->icon, 'bi-'))<i class="bi {{ $penghargaan->badge->icon }}"></i>@else{{ $penghargaan->badge->icon }}@endif
                                    </span>
                                @endif
                            @endforeach
                        </div>
                        <div class="gk-avatar-clean-wrap mb-3" style="width: 114px; height: 114px;">
                            <img src="{{ $top1->photo_url }}" width="114" height="114" alt="{{ $top1->nama }}">
                        </div>
                        <h4 class="fw-bold mb-1 text-dark" style="font-size: 1.25rem;" title="{{ $top1->nama }}">{{ $top1->nama }}</h4>
                        <div class="text-muted small mb-2">{{ strtoupper($top1->jurusan?->nama_jurusan ?? $top1->kategori_label ?? 'UMUM') }}</div>
                        <div class="text-warning mb-2" style="font-size: 1.1rem; letter-spacing: 2.5px;">
                            @php
                                $val1 = $pct1 / 20;
                                $stars1 = round($val1 * 2) / 2;
                            @endphp
                            @for($i = 1; $i <= 5; $i++)
                                @if($stars1 >= $i)
                                    <i class="bi bi-star-fill"></i>
                                @elseif($stars1 >= ($i - 0.5))
                                    <i class="bi bi-star-half"></i>
                                @else
                                    <i class="bi bi-star text-muted opacity-25"></i>
                                @endif
                            @endfor
                        </div>
                        <div class="mb-2">
                            <div class="gk-progress-pill gk-pill-rank-1 mx-auto" style="width: 145px; height: 24px; font-size: 0.8rem;">
                                <div class="gk-progress-pill-fill {{ $pct1 >= 75 ? 'gk-bar-blue-high' : ($pct1 >= 50 ? 'gk-bar-blue-mid' : 'gk-bar-blue-low') }}" style="width: {{ $pct1 }}%;"></div>
                                <span class="position-relative text-white" style="z-index: 2; text-shadow: 0 1px 2px rgba(0,0,0,0.6);">{{ $pct1 }}%</span>
                            </div>
                        </div>
                        <small class="text-muted font-mono d-block mb-3" style="font-size: 0.8rem;">{{ $top1->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa memilih' : 'ulasan' }}</small>
                    </div>
                    <a href="{{ route('siswa.guru.show', $top1->id) }}" class="btn btn-sm btn-outline-primary gk-btn-podium-profile mb-2">
                        <i class="bi bi-eye me-1"></i> Profil
                    </a>
                </div>
            </div>
        </div>
    @else
        {{-- TAMPILAN 3 PODIUM PENUH (2-1-3 FULL CONTAINER) --}}
        @php
            $pct1 = round(($top1->rata_rata_nilai / 5) * 100);
            $pct2 = round(($top2->rata_rata_nilai / 5) * 100);
            $pct3 = round(($top3->rata_rata_nilai / 5) * 100);
        @endphp
        <div class="row g-2 g-md-4 mb-4 mb-md-5 align-items-end justify-content-center gk-podium-row">
            {{-- #2 PERAK (NORMAL) --}}
            <div class="col-12 col-md-4 order-2 order-md-1 gk-podium-col gk-podium-2">
                <div class="gk-podium-card-revised p-3.5 p-md-4 text-center h-100 d-flex flex-column justify-content-between shadow-sm">
                    <div>
                        <div class="mb-1">
                            <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: #f1f5f9; color: #475569; font-size: 0.8rem;">
                                #2nd
                            </span>
                        </div>
                        <div class="gk-podium-badges-row">
                            @foreach(($top2->penghargaan ?? []) as $penghargaan)
                                @if($penghargaan->badge)
                                    <span class="gk-badge-mini-icon" style="background: {{ $penghargaan->badge->warna }}18; color: {{ $penghargaan->badge->warna }}; border-color: {{ $penghargaan->badge->warna }}33;" data-bs-toggle="tooltip" title="{{ $penghargaan->badge->nama_badge }}: {{ $penghargaan->badge->deskripsi }}">
                                        @if(str_starts_with($penghargaan->badge->icon, 'bi-'))<i class="bi {{ $penghargaan->badge->icon }}"></i>@else{{ $penghargaan->badge->icon }}@endif
                                    </span>
                                @endif
                            @endforeach
                        </div>
                        <div class="gk-avatar-clean-wrap mb-3" style="width: 96px; height: 96px;">
                            <img src="{{ $top2->photo_url }}" width="96" height="96" alt="{{ $top2->nama }}">
                        </div>
                        <h5 class="fw-bold mb-1 text-dark" style="font-size: 1.05rem;" title="{{ $top2->nama }}">{{ $top2->nama }}</h5>
                        <div class="text-muted small mb-2">{{ strtoupper($top2->jurusan?->nama_jurusan ?? $top2->kategori_label ?? 'UMUM') }}</div>
                        <div class="text-warning mb-2" style="font-size: 0.95rem; letter-spacing: 2px;">
                            @php
                                $val2 = $pct2 / 20;
                                $stars2 = round($val2 * 2) / 2;
                            @endphp
                            @for($i = 1; $i <= 5; $i++)
                                @if($stars2 >= $i)
                                    <i class="bi bi-star-fill"></i>
                                @elseif($stars2 >= ($i - 0.5))
                                    <i class="bi bi-star-half"></i>
                                @else
                                    <i class="bi bi-star text-muted opacity-25"></i>
                                @endif
                            @endfor
                        </div>
                        <div class="mb-2">
                            <div class="gk-progress-pill gk-pill-rank-2 mx-auto" style="width: 135px; height: 22px; font-size: 0.74rem;">
                                <div class="gk-progress-pill-fill {{ $pct2 >= 75 ? 'gk-bar-blue-high' : ($pct2 >= 50 ? 'gk-bar-blue-mid' : 'gk-bar-blue-low') }}" style="width: {{ $pct2 }}%;"></div>
                                <span class="position-relative text-white" style="z-index: 2; text-shadow: 0 1px 2px rgba(0,0,0,0.6);">{{ $pct2 }}%</span>
                            </div>
                        </div>
                        <small class="text-muted font-mono d-block mb-3" style="font-size: 0.78rem;">{{ $top2->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa memilih' : 'ulasan' }}</small>
                    </div>
                    <a href="{{ route('siswa.guru.show', $top2->id) }}" class="btn btn-sm btn-outline-primary gk-btn-podium-profile mb-2">
                        <i class="bi bi-eye me-1"></i> Profil
                    </a>
                </div>
            </div>

            {{-- #1 EMAS (CENTER PODIUM - BESAR) --}}
            <div class="col-12 col-md-4 order-1 order-md-2 mb-3 mb-md-0 gk-podium-col gk-podium-1">
                <div class="gk-podium-card-revised is-first p-4 p-md-5 text-center h-100 d-flex flex-column justify-content-between shadow position-relative">
                    <div>
                        <div class="mb-1">
                            <span class="badge rounded-pill px-4 py-1.5 fw-bold" style="background: #fef08a; color: #854d0e; font-size: 0.9rem;">
                                #1st
                            </span>
                        </div>
                        <div class="gk-podium-badges-row">
                            @foreach(($top1->penghargaan ?? []) as $penghargaan)
                                @if($penghargaan->badge)
                                    <span class="gk-badge-mini-icon" style="background: {{ $penghargaan->badge->warna }}18; color: {{ $penghargaan->badge->warna }}; border-color: {{ $penghargaan->badge->warna }}33;" data-bs-toggle="tooltip" title="{{ $penghargaan->badge->nama_badge }}: {{ $penghargaan->badge->deskripsi }}">
                                        @if(str_starts_with($penghargaan->badge->icon, 'bi-'))<i class="bi {{ $penghargaan->badge->icon }}"></i>@else{{ $penghargaan->badge->icon }}@endif
                                    </span>
                                @endif
                            @endforeach
                        </div>
                        <div class="gk-avatar-clean-wrap mb-3" style="width: 114px; height: 114px;">
                            <img src="{{ $top1->photo_url }}" width="114" height="114" alt="{{ $top1->nama }}">
                        </div>
                        <h4 class="fw-bold mb-1 text-dark" style="font-size: 1.25rem;" title="{{ $top1->nama }}">{{ $top1->nama }}</h4>
                        <div class="text-muted small mb-2">{{ strtoupper($top1->jurusan?->nama_jurusan ?? $top1->kategori_label ?? 'UMUM') }}</div>
                        <div class="text-warning mb-2" style="font-size: 1.1rem; letter-spacing: 2.5px;">
                            @php
                                $val1 = $pct1 / 20;
                                $stars1 = round($val1 * 2) / 2;
                            @endphp
                            @for($i = 1; $i <= 5; $i++)
                                @if($stars1 >= $i)
                                    <i class="bi bi-star-fill"></i>
                                @elseif($stars1 >= ($i - 0.5))
                                    <i class="bi bi-star-half"></i>
                                @else
                                    <i class="bi bi-star text-muted opacity-25"></i>
                                @endif
                            @endfor
                        </div>
                        <div class="mb-2">
                            <div class="gk-progress-pill gk-pill-rank-1 mx-auto" style="width: 145px; height: 24px; font-size: 0.8rem;">
                                <div class="gk-progress-pill-fill {{ $pct1 >= 75 ? 'gk-bar-blue-high' : ($pct1 >= 50 ? 'gk-bar-blue-mid' : 'gk-bar-blue-low') }}" style="width: {{ $pct1 }}%;"></div>
                                <span class="position-relative text-white" style="z-index: 2; text-shadow: 0 1px 2px rgba(0,0,0,0.6);">{{ $pct1 }}%</span>
                            </div>
                        </div>
                        <small class="text-muted font-mono d-block mb-3" style="font-size: 0.8rem;">{{ $top1->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa memilih' : 'ulasan' }}</small>
                    </div>
                    <a href="{{ route('siswa.guru.show', $top1->id) }}" class="btn btn-sm btn-outline-primary gk-btn-podium-profile mb-2">
                        <i class="bi bi-eye me-1"></i> Profil
                    </a>
                </div>
            </div>

            {{-- #3 PERUNGGU (KECIL) --}}
            <div class="col-12 col-md-4 order-3 order-md-3 gk-podium-col gk-podium-3">
                <div class="gk-podium-card-revised p-3 p-md-3.5 text-center h-100 d-flex flex-column justify-content-between shadow-sm">
                    <div>
                        <div class="mb-1">
                            <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: #fed7aa; color: #9a3412; font-size: 0.75rem;">
                                #3rd
                            </span>
                        </div>
                        <div class="gk-podium-badges-row">
                            @foreach(($top3->penghargaan ?? []) as $penghargaan)
                                @if($penghargaan->badge)
                                    <span class="gk-badge-mini-icon" style="background: {{ $penghargaan->badge->warna }}18; color: {{ $penghargaan->badge->warna }}; border-color: {{ $penghargaan->badge->warna }}33;" data-bs-toggle="tooltip" title="{{ $penghargaan->badge->nama_badge }}: {{ $penghargaan->badge->deskripsi }}">
                                        @if(str_starts_with($penghargaan->badge->icon, 'bi-'))<i class="bi {{ $penghargaan->badge->icon }}"></i>@else{{ $penghargaan->badge->icon }}@endif
                                    </span>
                                @endif
                            @endforeach
                        </div>
                        <div class="gk-avatar-clean-wrap mb-3" style="width: 82px; height: 82px;">
                            <img src="{{ $top3->photo_url }}" width="82" height="82" alt="{{ $top3->nama }}">
                        </div>
                        <h5 class="fw-bold mb-1 text-dark" style="font-size: 0.95rem;" title="{{ $top3->nama }}">{{ $top3->nama }}</h5>
                        <div class="text-muted small mb-2">{{ strtoupper($top3->jurusan?->nama_jurusan ?? $top3->kategori_label ?? 'UMUM') }}</div>
                        <div class="text-warning mb-2" style="font-size: 0.85rem; letter-spacing: 1.5px;">
                            @php
                                $val3 = $pct3 / 20;
                                $stars3 = round($val3 * 2) / 2;
                            @endphp
                            @for($i = 1; $i <= 5; $i++)
                                @if($stars3 >= $i)
                                    <i class="bi bi-star-fill"></i>
                                @elseif($stars3 >= ($i - 0.5))
                                    <i class="bi bi-star-half"></i>
                                @else
                                    <i class="bi bi-star text-muted opacity-25"></i>
                                @endif
                            @endfor
                        </div>
                        <div class="mb-2">
                            <div class="gk-progress-pill gk-pill-rank-3 mx-auto" style="width: 135px; height: 22px; font-size: 0.74rem;">
                                <div class="gk-progress-pill-fill {{ $pct3 >= 75 ? 'gk-bar-blue-high' : ($pct3 >= 50 ? 'gk-bar-blue-mid' : 'gk-bar-blue-low') }}" style="width: {{ $pct3 }}%;"></div>
                                <span class="position-relative text-white" style="z-index: 2; text-shadow: 0 1px 2px rgba(0,0,0,0.6);">{{ $pct3 }}%</span>
                            </div>
                        </div>
                        <small class="text-muted font-mono d-block mb-3" style="font-size: 0.75rem;">{{ $top3->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa memilih' : 'ulasan' }}</small>
                    </div>
                    <a href="{{ route('siswa.guru.show', $top3->id) }}" class="btn btn-sm btn-outline-primary gk-btn-podium-profile mb-2">
                        <i class="bi bi-eye me-1"></i> Profil
                    </a>
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
        <table class="table table-custom mb-0 align-middle">
            <thead class="align-middle">
                <tr>
                    <th style="width: 80px;" class="text-center align-middle">RANKING</th>
                    <th class="align-middle">NAMA GURU</th>
                    <th class="text-center align-middle">JURUSAN / KEAHLIAN</th>
                    <th style="width: 200px;" class="align-middle">{{ $mode === 'partisipasi' ? 'PARTISIPASI KELAS' : 'RATING KEPUASAN' }}</th>
                    <th class="text-center align-middle">{{ $mode === 'partisipasi' ? 'SISWA MEMILIH' : 'TOTAL ULASAN' }}</th>
                    <th class="text-center align-middle" style="width: 140px;">AKSI</th>
                </tr>
            </thead>
            <tbody class="align-middle">
                @forelse($list as $index => $g)
                @php $pct = round(($g->rata_rata_nilai / 5) * 100); @endphp
                <tr class="align-middle">
                    <td class="text-center align-middle">
                        @if($index === 0) <span class="gk-rank-medal">🥇</span>
                        @elseif($index === 1) <span class="gk-rank-medal">🥈</span>
                        @elseif($index === 2) <span class="gk-rank-medal">🥉</span>
                        @else <span class="gk-rank-badge font-mono">#{{ $index + 1 }}</span>
                        @endif
                    </td>
                    <td class="align-middle">
                        <div class="d-flex align-items-center">
                            <img src="{{ $g->photo_url }}" class="rounded-circle me-3 flex-shrink-0" width="44" height="44" style="width: 44px; height: 44px; min-width: 44px; min-height: 44px; aspect-ratio: 1 / 1; object-fit: cover; flex-shrink: 0;">
                            <div>
                                <div class="fw-bold text-dark lh-sm">{{ $g->nama }}</div>
                                @if(($g->penghargaan ?? collect())->whereNotNull('badge')->isNotEmpty())
                                    <div class="d-flex align-items-center gap-1 flex-wrap my-1">
                                        @foreach($g->penghargaan as $p)
                                            @if($p->badge)
                                                <span class="gk-badge-mini-icon" style="background: {{ $p->badge->warna }}18; color: {{ $p->badge->warna }}; border-color: {{ $p->badge->warna }}33;" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $p->badge->nama_badge }}: {{ $p->badge->deskripsi }}">
                                                    @if(str_starts_with($p->badge->icon, 'bi-'))<i class="bi {{ $p->badge->icon }}"></i>@else{{ $p->badge->icon }}@endif
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                                <div class="text-muted small" style="font-size: 0.76rem;">{{ $g->kategori_label }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center font-mono align-middle" style="font-size: 0.75rem;">{{ strtoupper($g->jurusan?->nama_jurusan ?? 'Umum') }}</td>
                    <td class="align-middle">
                        @php
                            $valTable = $pct / 20;
                            $starsTable = round($valTable * 2) / 2;
                        @endphp
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold font-mono text-dark" style="font-size: 0.85rem;">{{ $pct }}%</span>
                            <span class="text-warning small" style="font-size: 0.78rem;">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($starsTable >= $i)
                                        <i class="bi bi-star-fill"></i>
                                    @elseif($starsTable >= ($i - 0.5))
                                        <i class="bi bi-star-half"></i>
                                    @else
                                        <i class="bi bi-star text-muted opacity-25"></i>
                                    @endif
                                @endfor
                            </span>
                        </div>
                        <div class="gk-table-progress-wrap">
                            <div class="gk-table-progress-fill {{ $pct >= 75 ? 'gk-bar-blue-high' : ($pct >= 50 ? 'gk-bar-blue-mid' : 'gk-bar-blue-low') }}" style="width: {{ $pct }}%;"></div>
                        </div>
                    </td>
                    <td class="text-center font-mono align-middle">{{ $g->total_penilaian }}</td>
                    <td class="text-center align-middle">
                        <a href="{{ route('siswa.guru.show', $g->id) }}" class="btn btn-sm btn-outline-primary" style="border-radius: 6px; font-size: 0.82rem; font-weight: 600; padding: 0.35rem 0.8rem;">
                            <i class="bi bi-eye me-1"></i> Profil
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