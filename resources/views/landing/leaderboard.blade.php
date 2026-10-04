{{-- resources/views/landing/leaderboard.blade.php --}}
@extends('layouts.landing')
@section('title', 'Leaderboard')

@section('content')
<section style="background: var(--bg-light); padding: 140px 0 80px; min-height: 100vh;">
    <div class="container">
        {{-- Navigation Bar / Back button --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2" data-aos="fade-down">
            <a href="{{ route('landing.index') }}" class="gk-btn-back">
                <i class="bi bi-arrow-left"></i> Kembali ke Beranda
            </a>
            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-pill px-3 py-2" style="background: rgba(26, 60, 52, 0.08); color: var(--primary); font-size: 0.75rem; border: 1px solid var(--border);">
                    <i class="bi bi-patch-check-fill text-success me-1"></i> SMK Negeri 1 Bangsri
                </span>
            </div>
        </div>

        <div class="text-center mb-5" data-aos="fade-up">
            <div class="section-label">PENCAPAIAN TERTINGGI</div>
            <h1 class="section-title">Leaderboard Guru</h1>
            <p class="text-muted">Peringkat guru terbaik berdasarkan evaluasi dan ulasan siswa</p>
        </div>

        @include('components.leaderboard-filter')

        {{-- Podium Top 3 (Hanya Guru yang Memenuhi Syarat Minimal 5 Penilaian) --}}
        @php
            $list = $leaderboard ?? collect();
            $eligibleList = $list->filter(fn($g) => !empty($g->leaderboard_rank))->values();
            $top1 = $eligibleList->get(0);
            $top2 = $eligibleList->get(1);
            $top3 = $eligibleList->get(2);
        @endphp

        @if(!$top1)
            <div class="card-custom p-4 mb-4 text-center border shadow-xs" data-aos="fade-up" style="background: rgba(0, 51, 102, 0.03); border-color: rgba(0, 51, 102, 0.1) !important; border-radius: 14px;">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 42px; height: 42px; background: rgba(0, 51, 102, 0.08); color: #003366;">
                    <i class="bi bi-info-circle fs-5"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Belum Ada Guru di Ranking Podium</h6>
                <p class="text-muted small mb-0" style="max-width: 580px; margin: 0 auto; line-height: 1.5;">
                    Sesuai ketentuan, guru harus memiliki <strong>minimal 5 penilaian</strong> dari siswa untuk dapat masuk dalam peringkat leaderboard. Seluruh nilai guru tetap tercatat dan dapat dilihat pada tabel di bawah.
                </p>
            </div>
        @endif

        @if($top1)
            @if(!$top2)
                {{-- HANYA 1 GURU TOP --}}
                @php $pct1 = round(($top1->rata_rata_nilai / 5) * 100); @endphp
                <div class="row justify-content-center mb-4 mb-md-5" data-aos="zoom-in">
                    <div class="col-12 col-sm-10 col-md-8 col-lg-5">
                        <div class="gk-podium-card-revised is-first p-4 p-md-5 text-center d-flex flex-column justify-content-between shadow position-relative">
                            @include('components.podium-laurel-badge', ['rank' => 1])
                            <div>
                                <div class="gk-podium-badges-row mt-2">
                                    @foreach(($top1->penghargaan ?? collect()) as $penghargaan)
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

                                {{-- Progress Pill --}}
                                <div class="mb-2">
                                    <div class="gk-progress-pill gk-pill-rank-1 mx-auto" style="width: 145px; height: 24px; font-size: 0.8rem;">
                                        <div class="gk-progress-pill-fill {{ $pct1 >= 75 ? 'gk-bar-blue-high' : ($pct1 >= 50 ? 'gk-bar-blue-mid' : 'gk-bar-blue-low') }}" style="width: {{ $pct1 }}%;"></div>
                                        <span class="position-relative text-white" style="z-index: 2; text-shadow: 0 1px 2px rgba(0,0,0,0.6);">{{ $pct1 }}%</span>
                                    </div>
                                </div>
                                <small class="text-muted font-mono d-block mb-3" style="font-size: 0.8rem;">{{ $top1->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa memilih' : 'ulasan' }}</small>
                            </div>

                            <a href="{{ route('landing.guru.detail', $top1->id) }}" class="btn btn-sm btn-outline-primary gk-btn-podium-profile mb-2">
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
                <div class="row g-3 g-md-4 justify-content-center align-items-end mb-4 mb-md-5 gk-podium-row">
                    {{-- #2 PERAK --}}
                    <div class="col-6 col-md-5 gk-podium-col gk-podium-2" data-aos="fade-right">
                        <div class="gk-podium-card-revised p-3.5 p-md-4 text-center h-100 d-flex flex-column justify-content-between shadow-sm position-relative">
                            @include('components.podium-laurel-badge', ['rank' => 2])
                            <div>
                                <div class="gk-podium-badges-row mt-2">
                                    @foreach(($top2->penghargaan ?? collect()) as $penghargaan)
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
                            <a href="{{ route('landing.guru.detail', $top2->id) }}" class="btn btn-sm btn-outline-primary gk-btn-podium-profile mb-2">
                                <i class="bi bi-eye me-1"></i> Profil
                            </a>
                        </div>
                    </div>

                    {{-- #1 EMAS --}}
                    <div class="col-6 col-md-5 gk-podium-col gk-podium-1" data-aos="fade-left">
                        <div class="gk-podium-card-revised is-first p-3.5 p-md-4 text-center h-100 d-flex flex-column justify-content-between shadow position-relative">
                            @include('components.podium-laurel-badge', ['rank' => 1])
                            <div>
                                <div class="gk-podium-badges-row mt-2">
                                    @foreach(($top1->penghargaan ?? collect()) as $penghargaan)
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
                            <a href="{{ route('landing.guru.detail', $top1->id) }}" class="btn btn-sm btn-outline-primary gk-btn-podium-profile mb-2">
                                <i class="bi bi-eye me-1"></i> Profil
                            </a>
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
                    {{-- #2 PERAK (NORMAL) --}}
                    <div class="col-12 col-md-4 order-2 order-md-1 gk-podium-col gk-podium-2" data-aos="fade-right">
                        <div class="gk-podium-card-revised p-3.5 p-md-4 text-center h-100 d-flex flex-column justify-content-between shadow-sm position-relative">
                            @include('components.podium-laurel-badge', ['rank' => 2])
                            <div>
                                <div class="gk-podium-badges-row mt-2">
                                    @foreach(($top2->penghargaan ?? collect()) as $penghargaan)
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
                            <a href="{{ route('landing.guru.detail', $top2->id) }}" class="btn btn-sm btn-outline-primary gk-btn-podium-profile mb-2">
                                <i class="bi bi-eye me-1"></i> Profil
                            </a>
                        </div>
                    </div>

                    {{-- #1 EMAS (CENTER PODIUM - BESAR) --}}
                    <div class="col-12 col-md-4 order-1 order-md-2 mb-3 mb-md-0 gk-podium-col gk-podium-1" data-aos="zoom-in">
                        <div class="gk-podium-card-revised is-first p-4 p-md-5 text-center h-100 d-flex flex-column justify-content-between shadow position-relative">
                            @include('components.podium-laurel-badge', ['rank' => 1])
                            <div>
                                <div class="gk-podium-badges-row mt-2">
                                    @foreach(($top1->penghargaan ?? collect()) as $penghargaan)
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
                            <a href="{{ route('landing.guru.detail', $top1->id) }}" class="btn btn-sm btn-outline-primary gk-btn-podium-profile mb-2">
                                <i class="bi bi-eye me-1"></i> Profil
                            </a>
                        </div>
                    </div>

                    {{-- #3 PERUNGGU (KECIL) --}}
                    <div class="col-12 col-md-4 order-3 order-md-3 gk-podium-col gk-podium-3" data-aos="fade-left">
                        <div class="gk-podium-card-revised p-3 p-md-3.5 text-center h-100 d-flex flex-column justify-content-between shadow-sm position-relative">
                            @include('components.podium-laurel-badge', ['rank' => 3])
                            <div>
                                <div class="gk-podium-badges-row mt-2">
                                    @foreach(($top3->penghargaan ?? collect()) as $penghargaan)
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
                                    <div class="gk-progress-pill gk-pill-rank-3 mx-auto" style="width: 125px; height: 20px; font-size: 0.72rem;">
                                        <div class="gk-progress-pill-fill {{ $pct3 >= 75 ? 'gk-bar-blue-high' : ($pct3 >= 50 ? 'gk-bar-blue-mid' : 'gk-bar-blue-low') }}" style="width: {{ $pct3 }}%;"></div>
                                        <span class="position-relative text-white" style="z-index: 2; text-shadow: 0 1px 2px rgba(0,0,0,0.6);">{{ $pct3 }}%</span>
                                    </div>
                                </div>
                                <small class="text-muted font-mono d-block mb-3" style="font-size: 0.75rem;">{{ $top3->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa memilih' : 'ulasan' }}</small>
                            </div>
                            <a href="{{ route('landing.guru.detail', $top3->id) }}" class="btn btn-sm btn-outline-primary gk-btn-podium-profile mb-2">
                                <i class="bi bi-eye me-1"></i> Profil
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        {{-- Tabel Ranking Unified --}}
        <div class="card-custom" data-aos="fade-up">
            <div class="card-header px-4 py-3.5 border-bottom d-flex justify-content-between align-items-center" style="background: var(--bg-card); color: var(--text-dark); border-color: var(--border) !important;">
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
                                @if($g->leaderboard_rank === 1) <span class="gk-rank-medal">🥇</span>
                                @elseif($g->leaderboard_rank === 2) <span class="gk-rank-medal">🥈</span>
                                @elseif($g->leaderboard_rank === 3) <span class="gk-rank-medal">🥉</span>
                                @elseif($g->leaderboard_rank) <span class="gk-rank-badge font-mono">#{{ $g->leaderboard_rank }}</span>
                                @else
                                    <span class="badge rounded-pill px-2.5 py-1 text-muted" style="background: rgba(0, 51, 102, 0.05); border: 1px solid rgba(0, 51, 102, 0.12); font-size: 0.7rem; font-weight: 600;" data-bs-toggle="tooltip" title="Belum cukup data untuk masuk peringkat leaderboard (minimal 5 penilaian)">
                                        Belum cukup data ({{ $g->total_penilaian }}/5)
                                    </span>
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
                            <td class="font-mono text-center align-middle" style="font-size: 0.75rem;">{{ strtoupper($g->jurusan?->nama_jurusan ?? 'Umum') }}</td>
                            <td class="align-middle">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    @php
                                        $valTable = $pct / 20;
                                        $starsTable = round($valTable * 2) / 2;
                                    @endphp
                                    <span class="fw-bold font-mono text-dark" style="font-size: 0.85rem;">
                                        {{ $pct }}% <span class="text-muted fw-normal" style="font-size: 0.78rem;">· {{ number_format($g->rata_rata_nilai, 1) }}</span>
                                        <span class="text-warning ms-1" style="font-size: 0.85rem;" title="{{ number_format($starsTable, 1) }} dari 5 bintang">
                                            @for($s = 1; $s <= 5; $s++)
                                                @if($starsTable >= $s)
                                                    <i class="bi bi-star-fill"></i>
                                                @elseif($starsTable >= ($s - 0.5))
                                                    <i class="bi bi-star-half"></i>
                                                @else
                                                    <i class="bi bi-star text-muted opacity-25"></i>
                                                @endif
                                            @endfor
                                        </span>
                                    </span>
                                </div>
                                <div class="gk-table-progress-wrap">
                                    <div class="gk-table-progress-fill {{ $pct >= 75 ? 'gk-bar-blue-high' : ($pct >= 50 ? 'gk-bar-blue-mid' : 'gk-bar-blue-low') }}" style="width: {{ $pct }}%;"></div>
                                </div>
                            </td>
                            <td class="text-center font-mono align-middle" style="font-size: 0.82rem;">
                                <strong>{{ $g->total_penilaian }}</strong> <span class="text-muted small">{{ $mode === 'partisipasi' ? 'siswa' : 'penilaian' }}</span>
                            </td>
                            <td class="text-center align-middle">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border shadow-xs rounded-circle d-inline-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 32px; height: 32px;" title="Pilihan Aksi">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-1" style="border-radius: 12px; font-size: 0.85rem; min-width: 170px; box-shadow: 0 10px 25px rgba(0,0,0,0.12) !important;">
                                        <li>
                                            <a class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2 text-primary" href="{{ route('landing.guru.detail', $g->id) }}">
                                                <i class="bi bi-person-badge text-primary"></i>
                                                <span>Lihat Profil Guru</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
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


    </div>
</section>
@endsection