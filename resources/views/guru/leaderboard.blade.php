@extends('layouts.guru')
@section('title', 'Leaderboard Guru')

@section('content')
<div class="text-center mb-4">
    <h1 class="page-title fw-bold text-dark mb-1" style="font-size: 1.85rem;">Leaderboard Guru</h1>
    <p class="text-muted mb-2" style="font-size: 0.95rem;">Peringkat guru terbaik berdasarkan penilaian dan ulasan objektif siswa.</p>
    @php
        $lbCatatan = \App\Models\Setting::get('leaderboard_catatan', 'Leaderboard ini hanyalah fitur apresiasi tambahan. Fokus utama evaluasi adalah refleksi pembelajaran, dedikasi, serta kedisiplinan guru dalam mengajar.');
    @endphp
    @if(!empty(trim($lbCatatan)))
    <div class="d-inline-flex align-items-center justify-content-center gap-2 px-3 py-1.5 rounded-pill border" style="background: rgba(0, 51, 102, 0.04); border-color: rgba(0, 51, 102, 0.12) !important; max-width: 680px;">
        <i class="bi bi-info-circle text-primary flex-shrink-0" style="font-size: 0.85rem;"></i>
        <span class="text-muted small" style="font-size: 0.8rem; line-height: 1.4;">
            <strong>Catatan:</strong> {{ $lbCatatan }}
        </span>
    </div>
    @endif
</div>

@include('components.leaderboard-filter')

{{-- BANNER POSISI PERINGKAT PRIBADI GURU (BERSIFAT PRIVAT & HANYA DILIHAT OLEH GURU INI) --}}
@if(isset($myRankInfo))
<div class="card-custom p-3 p-md-3.5 mb-4 border shadow-sm" style="background: linear-gradient(135deg, rgba(0, 51, 102, 0.04) 0%, rgba(2, 132, 199, 0.08) 100%); border-left: 5px solid #003366 !important; border-radius: 14px;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background: #003366; color: #ffffff; font-size: 1.3rem;">
                <i class="bi bi-person-badge-fill"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge rounded-pill bg-primary px-2.5 py-1 font-mono" style="font-size: 0.72rem;">STATUS PRIBADI ANDA</span>
                    <small class="text-muted" style="font-size: 0.75rem;">Hanya dapat dilihat oleh Anda</small>
                </div>
                <h5 class="fw-bold mb-0 text-dark mt-1" style="font-size: 1.05rem;">
                    @if($myRankInfo['rank'])
                        Posisi Anda: <span class="text-primary font-mono fw-extrabold">Peringkat #{{ $myRankInfo['rank'] }}</span>
                        <span class="text-muted fw-normal" style="font-size: 0.85rem;">dari {{ $myRankInfo['total_gurus'] }} Guru</span>
                        @if($myRankInfo['is_top_limit'])
                            <span class="badge bg-success-subtle text-success border border-success-subtle ms-1" style="font-size: 0.7rem;">Top {{ $myRankInfo['limit'] }}</span>
                        @endif
                    @else
                        Posisi Anda: <span class="text-muted fw-semibold">Belum Masuk Peringkat</span>
                    @endif
                </h5>
                <p class="text-muted small mb-0 mt-0.5" style="font-size: 0.78rem;">
                    @if($myRankInfo['is_top_limit'])
                        Profil Anda berhasil masuk ke daftar Top {{ $myRankInfo['limit'] }} leaderboard. Terus pertahankan apresiasi belajar ini!
                    @elseif($myRankInfo['rank'])
                        Peringkat Anda tersimpan aman secara privat untuk bahan evaluasi diri dan <strong>tidak dipublikasikan</strong> ke leaderboard umum (aman dari ledekan).
                    @else
                        Tingkatkan evaluasi dan pembelajaran bersama siswa untuk memperoleh peringkat.
                    @endif
                </p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="text-end d-none d-sm-block">
                <span class="text-muted small d-block" style="font-size: 0.7rem;">Rata-rata Nilai Anda</span>
                <span class="fw-bold font-mono text-dark" style="font-size: 1.1rem;">{{ number_format($myRankInfo['rata_rata'], 2) }} / 5.0</span>
            </div>
        </div>
    </div>
</div>
@endif

<div class="mb-3">
    <div class="page-label">PENCAPAIAN TERTINGGI</div>
</div>

{{-- Podium Top 3 --}}
@php
    $list = $leaderboard ?? collect();
    if (!($list instanceof \Illuminate\Support\Collection)) {
        $list = collect($list ?? []);
    }
    $eligibleList = $list->filter(fn($g) => is_object($g) && !($g instanceof \__PHP_Incomplete_Class) && !empty($g->leaderboard_rank))->values();
    $top1 = $eligibleList->get(0);
    $top2 = $eligibleList->get(1);
    $top3 = $eligibleList->get(2);
@endphp

@if(!$top1)
    <div class="card-custom p-4 mb-4 text-center border shadow-xs" style="background: rgba(0, 51, 102, 0.03); border-color: rgba(0, 51, 102, 0.1) !important; border-radius: 14px;">
        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 42px; height: 42px; background: rgba(0, 51, 102, 0.08); color: #003366;">
            <i class="bi bi-info-circle fs-5"></i>
        </div>
        <h6 class="fw-bold text-dark mb-1">Belum Ada Guru di Ranking Podium</h6>
        <p class="text-muted small mb-0" style="max-width: 580px; margin: 0 auto; line-height: 1.5;">
            Belum ada guru yang memenuhi peringkat leaderboard pada periode ini.
        </p>
    </div>
@endif

@if($top1)
    @if(!$top2)
        {{-- HANYA 1 GURU TOP --}}
        @php $pct1 = round(($top1->rata_rata_nilai / 5) * 100); @endphp
        <div class="row justify-content-center mb-4 mb-md-5">
            <div class="col-12 col-sm-10 col-md-8 col-lg-5">
                <div class="gk-podium-card-revised is-first p-4 p-md-5 text-center d-flex flex-column justify-content-between shadow position-relative">
                    @include('components.podium-laurel-badge', ['rank' => 1])
                    <div>
                        <div class="gk-podium-badges-row mt-2">
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
                        <small class="text-muted font-mono d-block mb-3" style="font-size: 0.8rem;">{{ $top1->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa yang menilai' : 'ulasan' }}</small>
                    </div>
                    <a href="{{ route('guru.detail', $top1->id) }}" class="btn btn-sm btn-outline-primary gk-btn-podium-profile mb-2">
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
            <div class="col-6 col-md-5 gk-podium-col gk-podium-2">
                <div class="gk-podium-card-revised p-3.5 p-md-4 text-center h-100 d-flex flex-column justify-content-between shadow-sm position-relative">
                    @include('components.podium-laurel-badge', ['rank' => 2])
                    <div>
                        <div class="gk-podium-badges-row mt-2">
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
                        <small class="text-muted font-mono d-block mb-3" style="font-size: 0.78rem;">{{ $top2->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa yang menilai' : 'ulasan' }}</small>
                    </div>
                    <a href="{{ route('guru.detail', $top2->id) }}" class="btn btn-sm btn-outline-primary gk-btn-podium-profile mb-2">
                        <i class="bi bi-eye me-1"></i> Profil
                    </a>
                </div>
            </div>

            {{-- #1 EMAS --}}
            <div class="col-6 col-md-5 gk-podium-col gk-podium-1">
                <div class="gk-podium-card-revised is-first p-3.5 p-md-4 text-center h-100 d-flex flex-column justify-content-between shadow position-relative">
                    @include('components.podium-laurel-badge', ['rank' => 1])
                    <div>
                        <div class="gk-podium-badges-row mt-2">
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
                        <small class="text-muted font-mono d-block mb-3" style="font-size: 0.8rem;">{{ $top1->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa yang menilai' : 'ulasan' }}</small>
                    </div>
                    <a href="{{ route('guru.detail', $top1->id) }}" class="btn btn-sm btn-outline-primary gk-btn-podium-profile mb-2">
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
        <div class="row g-1 g-md-4 mb-4 mb-md-5 align-items-end justify-content-center gk-podium-row">
            {{-- #2 PERAK (NORMAL) --}}
            <div class="col-4 col-md-4 order-1 podium-anim-item podium-rank-2 gk-podium-col gk-podium-2 px-1">
                <div class="gk-podium-card-revised p-3.5 p-md-4 text-center h-100 d-flex flex-column justify-content-between shadow-sm position-relative">
                    @include('components.podium-laurel-badge', ['rank' => 2])
                    <div>
                        <div class="gk-podium-badges-row mt-2">
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
                        <small class="text-muted font-mono d-block mb-3" style="font-size: 0.78rem;">{{ $top2->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa yang menilai' : 'ulasan' }}</small>
                    </div>
                    <a href="{{ route('guru.detail', $top2->id) }}" class="btn btn-sm btn-outline-primary gk-btn-podium-profile mb-2">
                        <i class="bi bi-eye me-1"></i> Profil
                    </a>
                </div>
            </div>

            {{-- #1 EMAS (CENTER PODIUM - BESAR) --}}
            <div class="col-4 col-md-4 order-2 mb-0 podium-anim-item podium-rank-1 gk-podium-col gk-podium-1 px-1">
                <div class="gk-podium-card-revised is-first p-4 p-md-5 text-center h-100 d-flex flex-column justify-content-between shadow position-relative">
                    @include('components.podium-laurel-badge', ['rank' => 1])
                    <div>
                        <div class="gk-podium-badges-row mt-2">
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
                        <small class="text-muted font-mono d-block mb-3" style="font-size: 0.8rem;">{{ $top1->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa yang menilai' : 'ulasan' }}</small>
                    </div>
                    <a href="{{ route('guru.detail', $top1->id) }}" class="btn btn-sm btn-outline-primary gk-btn-podium-profile mb-2">
                        <i class="bi bi-eye me-1"></i> Profil
                    </a>
                </div>
            </div>

            {{-- #3 PERUNGGU (KECIL) --}}
            <div class="col-4 col-md-4 order-3 podium-anim-item podium-rank-3 gk-podium-col gk-podium-3 px-1">
                <div class="gk-podium-card-revised p-3 p-md-3.5 text-center h-100 d-flex flex-column justify-content-between shadow-sm position-relative">
                    @include('components.podium-laurel-badge', ['rank' => 3])
                    <div>
                        <div class="gk-podium-badges-row mt-2">
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
                            <div class="gk-progress-pill gk-pill-rank-3 mx-auto" style="width: 125px; height: 20px; font-size: 0.72rem;">
                                <div class="gk-progress-pill-fill {{ $pct3 >= 75 ? 'gk-bar-blue-high' : ($pct3 >= 50 ? 'gk-bar-blue-mid' : 'gk-bar-blue-low') }}" style="width: {{ $pct3 }}%;"></div>
                                <span class="position-relative text-white" style="z-index: 2; text-shadow: 0 1px 2px rgba(0,0,0,0.6);">{{ $pct3 }}%</span>
                            </div>
                        </div>
                        <small class="text-muted font-mono d-block mb-3" style="font-size: 0.75rem;">{{ $top3->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa yang menilai' : 'ulasan' }}</small>
                    </div>
                    <a href="{{ route('guru.detail', $top3->id) }}" class="btn btn-sm btn-outline-primary gk-btn-podium-profile mb-2">
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
        <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-trophy-fill text-warning fs-5"></i>
            <span>Daftar Peringkat {{ $mode === 'partisipasi' ? 'Top 5 Guru' : 'Top 30 Guru' }}</span>
        </h6>
        <span class="badge bg-primary px-3 py-1.5 rounded-pill">{{ $list->count() }} Guru (Top {{ $mode === 'partisipasi' ? '5' : '30' }})</span>
    </div>
    <div class="table-responsive">
        <table class="table table-custom mb-0 align-middle">
            <thead class="align-middle">
                <tr>
                    <th style="width: 80px;" class="text-center align-middle">RANKING</th>
                    <th class="align-middle">NAMA GURU</th>
                    <th class="text-center align-middle">JURUSAN / KEAHLIAN</th>
                    <th style="width: 200px;" class="align-middle">RATING KEPUASAN</th>
                    <th class="text-center align-middle">{{ $mode === 'partisipasi' ? 'SISWA YANG MENILAI' : 'TOTAL ULASAN' }}</th>
                    <th class="text-center align-middle" style="width: 140px;">AKSI</th>
                </tr>
            </thead>
            <tbody class="align-middle">
                @forelse($list as $index => $g)
                @php 
                    $pct = round(($g->rata_rata_nilai / 5) * 100); 
                    $isSelf = (auth()->check() && (auth()->user()->nis === $g->nip || auth()->user()->email === $g->email));
                @endphp
                <tr class="align-middle {{ $isSelf ? 'table-primary' : '' }}">
                    <td class="text-center align-middle">
                        @if($g->leaderboard_rank === 1) <span class="gk-rank-medal">🥇</span>
                        @elseif($g->leaderboard_rank === 2) <span class="gk-rank-medal">🥈</span>
                        @elseif($g->leaderboard_rank === 3) <span class="gk-rank-medal">🥉</span>
                        @elseif($g->leaderboard_rank) <span class="gk-rank-badge font-mono">#{{ $g->leaderboard_rank }}</span>
                        @endif
                    </td>
                    <td class="align-middle">
                        <div class="d-flex align-items-center">
                            <img src="{{ $g->photo_url }}" class="rounded-circle me-3 flex-shrink-0" width="44" height="44" style="width: 44px; height: 44px; min-width: 44px; min-height: 44px; aspect-ratio: 1 / 1; object-fit: cover; flex-shrink: 0;">
                            <div>
                                <div class="fw-bold text-dark lh-sm">
                                    {{ $g->nama }}
                                    @if($isSelf)
                                        <span class="badge bg-success ms-1" style="font-size: 0.65rem;">Anda</span>
                                    @endif
                                </div>
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
                        @php
                            $valTable = $pct / 20;
                            $starsTable = round($valTable * 2) / 2;
                        @endphp
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold font-mono text-dark" style="font-size: 0.85rem;">
                                {{ $pct }}% <span class="text-muted fw-normal" style="font-size: 0.78rem;">· {{ number_format($g->rata_rata_nilai, 1) }}</span>
                            </span>
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
                                    <a class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2 text-primary" href="{{ route('guru.detail', $g->id) }}">
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
@endsection
