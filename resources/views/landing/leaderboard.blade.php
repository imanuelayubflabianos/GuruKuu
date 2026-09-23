{{-- resources/views/landing/leaderboard.blade.php --}}
@extends('layouts.landing')
@section('title', 'Leaderboard')

@section('content')
<section style="background: var(--bg-light); padding: 140px 0 80px; min-height: 100vh;">
    <div class="container">
        {{-- Navigation Bar / Back button --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2" data-aos="fade-down">
            <a href="{{ route('landing.index') }}" class="btn btn-outline-custom btn-sm rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center gap-2 fw-semibold">
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

        {{-- Podium Top 3 --}}
        @php
            $list = $leaderboard ?? collect();
            $top1 = $list->get(0);
            $top2 = $list->get(1);
            $top3 = $list->get(2);
        @endphp

        @if($top1)
            @if(!$top2)
                {{-- HANYA 1 GURU TOP (TIDAK DEMPET, PROPORSI LEBAR & BERSIH) --}}
                @php $pct1 = round(($top1->rata_rata_nilai / 5) * 100); @endphp
                <div class="row justify-content-center mb-4 mb-md-5" data-aos="zoom-in">
                    <div class="col-12 col-sm-10 col-md-8 col-lg-6">
                        <div class="card-custom gk-podium-gold p-4 p-md-5 text-center position-relative shadow" style="border-radius: 20px;">
                            <div class="mb-3">
                                <span class="badge rounded-pill px-3.5 py-1.5 fw-bold shadow-sm" style="background: linear-gradient(135deg, #d97706, #b45309); color: #ffffff; font-size: 0.85rem; letter-spacing: 0.5px;">
                                    <i class="bi bi-trophy-fill me-1"></i> #1 EMAS - PERINGKAT TERTINGGI
                                </span>
                            </div>
                            <div class="position-relative d-inline-block mb-3">
                                <img src="{{ $top1->photo_url }}" class="rounded-circle shadow" width="115" height="115" style="object-fit: cover;">
                            </div>
                            <h3 class="fw-bold mb-1 gk-podium-nama" title="{{ $top1->nama }}">{{ $top1->nama }}</h3>
                            <div class="font-mono mb-3 gk-podium-jurusan" style="font-size: 0.85rem; letter-spacing: 1px;" title="{{ strtoupper($top1->jurusan?->nama_jurusan ?? 'UMUM') }}">{{ strtoupper($top1->jurusan?->nama_jurusan ?? 'UMUM') }}</div>
                            <div class="p-3 rounded-3 mb-3.5 gk-podium-statbox">
                                <div class="fw-bold gk-podium-score" style="font-size: 2.4rem; line-height: 1;">{{ $pct1 }}% <span class="text-warning fs-5">@for($star = 1; $star <= 5; $star++){{ $star <= round($pct1 / 20) ? '★' : '☆' }}@endfor</span></div>
                                <div class="progress mt-2 mb-1.5" style="height: 6px; background-color: rgba(217, 119, 6, 0.2); border-radius: 10px;">
                                    <div class="progress-bar rounded-pill" style="width: {{ $pct1 }}%; background: #d97706;"></div>
                                </div>
                                <small class="text-muted font-mono d-block mt-1 gk-podium-reviews" style="font-size: 0.78rem;">{{ $top1->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa memilih' : 'ulasan' }}</small>
                            </div>
                            <a href="{{ route('landing.guru.detail', $top1->id) }}" class="btn btn-primary-custom w-100 rounded-pill py-2.5 fw-semibold" style="font-size: 0.95rem; background: #003366;">
                                <i class="bi bi-eye me-1"></i> Lihat Profil Lengkap
                            </a>
                        </div>
                    </div>
                </div>
            @elseif(!$top3)
                {{-- HANYA 2 GURU TOP (SEIMBANG 2 KOLOM) --}}
                @php
                    $pct1 = round(($top1->rata_rata_nilai / 5) * 100);
                    $pct2 = round(($top2->rata_rata_nilai / 5) * 100);
                @endphp
                <div class="row g-3 g-md-4 justify-content-center align-items-end mb-4 mb-md-5">
                    {{-- #2 PERAK --}}
                    <div class="col-6 col-md-5" data-aos="fade-right">
                        <div class="card-custom gk-podium-silver p-3 p-md-4 text-center h-100 shadow-sm" style="border-radius: 16px;">
                            <div class="mb-2 mb-md-3">
                                <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: #64748b; color: #fff; font-size: 0.75rem;">
                                    <i class="bi bi-award-fill me-1"></i> #2 PERAK
                                </span>
                            </div>
                            <div class="position-relative d-inline-block mb-2 mb-md-3">
                                <img src="{{ $top2->photo_url }}" class="rounded-circle shadow-sm" width="90" height="90" style="object-fit: cover;">
                            </div>
                            <h5 class="fw-bold mb-1 gk-podium-nama" title="{{ $top2->nama }}">{{ $top2->nama }}</h5>
                            <div class="font-mono mb-2 mb-md-3 gk-podium-jurusan" style="font-size: 0.72rem;" title="{{ strtoupper($top2->jurusan?->nama_jurusan ?? 'UMUM') }}">{{ strtoupper($top2->jurusan?->nama_jurusan ?? 'UMUM') }}</div>
                            <div class="p-2 p-md-3 rounded-3 mb-2 mb-md-3 gk-podium-statbox">
                                <div class="fw-bold gk-podium-score" style="font-size: 1.8rem; line-height: 1;">{{ $pct2 }}% <span class="text-warning fs-6">@for($star = 1; $star <= 5; $star++){{ $star <= round($pct2 / 20) ? '★' : '☆' }}@endfor</span></div>
                                <div class="progress mt-1.5 mb-1" style="height: 5px; border-radius: 10px; background: rgba(100, 116, 139, 0.2);">
                                    <div class="progress-bar rounded-pill" style="width: {{ $pct2 }}%; background: #64748b;"></div>
                                </div>
                                <small class="text-muted font-mono gk-podium-reviews" style="font-size: 0.72rem;">{{ $top2->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa memilih' : 'ulasan' }}</small>
                            </div>
                            <a href="{{ route('landing.guru.detail', $top2->id) }}" class="btn btn-outline-custom btn-sm w-100 rounded-pill py-2">
                                <i class="bi bi-eye me-1"></i> Profil
                            </a>
                        </div>
                    </div>

                    {{-- #1 EMAS --}}
                    <div class="col-6 col-md-5" data-aos="fade-left">
                        <div class="card-custom gk-podium-gold p-3 p-md-4 text-center position-relative shadow" style="border-radius: 18px;">
                            <div class="mb-2 mb-md-3">
                                <span class="badge rounded-pill px-3 py-1 fw-bold shadow-sm" style="background: linear-gradient(135deg, #d97706, #b45309); color: #ffffff; font-size: 0.8rem;">
                                    <i class="bi bi-trophy-fill me-1"></i> #1 EMAS
                                </span>
                            </div>
                            <div class="position-relative d-inline-block mb-2 mb-md-3">
                                <img src="{{ $top1->photo_url }}" class="rounded-circle shadow" width="100" height="100" style="object-fit: cover;">
                            </div>
                            <h5 class="fw-bold mb-1 gk-podium-nama" title="{{ $top1->nama }}">{{ $top1->nama }}</h5>
                            <div class="font-mono mb-2 mb-md-3 gk-podium-jurusan" style="font-size: 0.75rem; letter-spacing: 1px;" title="{{ strtoupper($top1->jurusan?->nama_jurusan ?? 'UMUM') }}">{{ strtoupper($top1->jurusan?->nama_jurusan ?? 'UMUM') }}</div>
                            <div class="p-2 p-md-3 rounded-3 mb-2 mb-md-3 gk-podium-statbox">
                                <div class="fw-bold gk-podium-score" style="font-size: 2rem; line-height: 1;">{{ $pct1 }}% <span class="text-warning fs-6">@for($star = 1; $star <= 5; $star++){{ $star <= round($pct1 / 20) ? '★' : '☆' }}@endfor</span></div>
                                <div class="progress mt-1.5 mb-1" style="height: 6px; background-color: rgba(217, 119, 6, 0.2); border-radius: 10px;">
                                    <div class="progress-bar rounded-pill" style="width: {{ $pct1 }}%; background: #d97706;"></div>
                                </div>
                                <small class="text-muted font-mono d-block mt-1 gk-podium-reviews" style="font-size: 0.72rem;">{{ $top1->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa memilih' : 'ulasan' }}</small>
                            </div>
                            <a href="{{ route('landing.guru.detail', $top1->id) }}" class="btn btn-primary-custom w-100 rounded-pill py-2 fw-semibold" style="background: #003366;">
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
                    {{-- #2 PERAK --}}
                    <div class="col-4 col-md-4 order-1 order-md-1 gk-podium-col gk-podium-2" data-aos="fade-right">
                        <div class="card-custom gk-podium-card gk-podium-silver p-2 p-md-4 text-center h-100 shadow-sm" style="border-radius: 16px;">
                            <div class="mb-2 mb-md-3">
                                <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: #64748b; color: #fff; font-size: 0.75rem;">
                                    <i class="bi bi-award-fill me-1"></i> #2 PERAK
                                </span>
                            </div>
                            <div class="position-relative d-inline-block mb-2 mb-md-3">
                                <img src="{{ $top2->photo_url }}" class="rounded-circle shadow-sm gk-podium-avatar-2" width="90" height="90" style="object-fit: cover;">
                            </div>
                            <h5 class="fw-bold mb-1 gk-podium-nama" title="{{ $top2->nama }}">{{ $top2->nama }}</h5>
                            <div class="font-mono mb-2 mb-md-3 gk-podium-jurusan" style="font-size: 0.72rem;" title="{{ strtoupper($top2->jurusan?->nama_jurusan ?? 'UMUM') }}">{{ strtoupper($top2->jurusan?->nama_jurusan ?? 'UMUM') }}</div>
                            <div class="p-1.5 p-md-3 rounded-3 mb-2 mb-md-3 gk-podium-statbox">
                                <div class="fw-bold gk-podium-score" style="font-size: 1.8rem; line-height: 1;">{{ $pct2 }}% <span class="text-warning fs-6">@for($star = 1; $star <= 5; $star++){{ $star <= round($pct2 / 20) ? '★' : '☆' }}@endfor</span></div>
                                <div class="progress mt-1 mt-md-2 mb-1" style="height: 5px; border-radius: 10px; background: rgba(100, 116, 139, 0.2);">
                                    <div class="progress-bar rounded-pill" style="width: {{ $pct2 }}%; background: #64748b;"></div>
                                </div>
                                <small class="text-muted font-mono gk-podium-reviews" style="font-size: 0.72rem;">{{ $top2->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa memilih' : 'ulasan' }}</small>
                            </div>
                            <a href="{{ route('landing.guru.detail', $top2->id) }}" class="btn btn-outline-custom btn-sm btn-podium w-100 rounded-pill">
                                <i class="bi bi-eye me-1"></i> Profil
                            </a>
                        </div>
                    </div>

                    {{-- #1 EMAS (CENTER PODIUM) --}}
                    <div class="col-4 col-md-4 order-2 order-md-2 mb-0 gk-podium-col gk-podium-1" data-aos="zoom-in">
                        <div class="card-custom gk-podium-card gk-podium-gold p-2.5 p-md-5 text-center position-relative shadow" style="border-radius: 18px; transform: translateY(-8px);">
                            <div class="mb-2 mb-md-3">
                                <span class="badge rounded-pill px-2.5 px-md-3.5 py-1 py-md-1.5 fw-bold shadow-sm" style="background: linear-gradient(135deg, #d97706, #b45309); color: #ffffff; font-size: 0.8rem; letter-spacing: 0.5px;">
                                    <i class="bi bi-trophy-fill me-1"></i> #1 EMAS
                                </span>
                            </div>
                            <div class="position-relative d-inline-block mb-2 mb-md-3">
                                <img src="{{ $top1->photo_url }}" class="rounded-circle shadow gk-podium-avatar-1" width="115" height="115" style="object-fit: cover;">
                            </div>
                            <h4 class="fw-bold mb-1 gk-podium-nama" title="{{ $top1->nama }}">{{ $top1->nama }}</h4>
                            <div class="font-mono mb-2 mb-md-3 gk-podium-jurusan" style="font-size: 0.75rem; letter-spacing: 1px;" title="{{ strtoupper($top1->jurusan?->nama_jurusan ?? 'UMUM') }}">{{ strtoupper($top1->jurusan?->nama_jurusan ?? 'UMUM') }}</div>
                            <div class="p-2 p-md-3 rounded-3 mb-2 mb-md-3 gk-podium-statbox">
                                <div class="fw-bold gk-podium-score" style="font-size: 2.3rem; line-height: 1;">{{ $pct1 }}% <span class="text-warning fs-6">@for($star = 1; $star <= 5; $star++){{ $star <= round($pct1 / 20) ? '★' : '☆' }}@endfor</span></div>
                                <div class="progress mt-1 mt-md-2 mb-1" style="height: 6px; background-color: rgba(217, 119, 6, 0.2); border-radius: 10px;">
                                    <div class="progress-bar rounded-pill" style="width: {{ $pct1 }}%; background: #d97706;"></div>
                                </div>
                                <small class="text-muted font-mono d-block mt-0.5 mt-md-1 gk-podium-reviews" style="font-size: 0.75rem;">{{ $top1->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa memilih' : 'ulasan' }}</small>
                            </div>
                            <a href="{{ route('landing.guru.detail', $top1->id) }}" class="btn btn-primary-custom btn-podium w-100 rounded-pill py-1.5 py-md-2 fw-semibold" style="background: #003366;">
                                <i class="bi bi-eye me-1"></i> Profil
                            </a>
                        </div>
                    </div>

                    {{-- #3 PERUNGGU --}}
                    <div class="col-4 col-md-4 order-3 order-md-3 gk-podium-col gk-podium-3" data-aos="fade-left">
                        <div class="card-custom gk-podium-card gk-podium-bronze p-2 p-md-4 text-center h-100 shadow-sm" style="border-radius: 16px;">
                            <div class="mb-2 mb-md-3">
                                <span class="badge rounded-pill px-2.5 py-1 fw-bold" style="background: #b45309; color: #fff; font-size: 0.75rem;">
                                    <i class="bi bi-award-fill me-1"></i> #3 PERUNGGU
                                </span>
                            </div>
                            <div class="position-relative d-inline-block mb-2 mb-md-3">
                                <img src="{{ $top3->photo_url }}" class="rounded-circle shadow-sm gk-podium-avatar-3" width="90" height="90" style="object-fit: cover;">
                            </div>
                            <h5 class="fw-bold mb-1 gk-podium-nama" title="{{ $top3->nama }}">{{ $top3->nama }}</h5>
                            <div class="font-mono mb-2 mb-md-3 gk-podium-jurusan" style="font-size: 0.72rem;" title="{{ strtoupper($top3->jurusan?->nama_jurusan ?? 'UMUM') }}">{{ strtoupper($top3->jurusan?->nama_jurusan ?? 'UMUM') }}</div>
                            <div class="p-1.5 p-md-3 rounded-3 mb-2 mb-md-3 gk-podium-statbox">
                                <div class="fw-bold gk-podium-score" style="font-size: 1.8rem; line-height: 1;">{{ $pct3 }}% <span class="text-warning fs-6">@for($star = 1; $star <= 5; $star++){{ $star <= round($pct3 / 20) ? '★' : '☆' }}@endfor</span></div>
                                <div class="progress mt-1 mt-md-2 mb-1" style="height: 5px; border-radius: 10px; background: rgba(180, 83, 9, 0.2);">
                                    <div class="progress-bar rounded-pill" style="width: {{ $pct3 }}%; background: #b45309;"></div>
                                </div>
                                <small class="text-muted font-mono gk-podium-reviews" style="font-size: 0.72rem;">{{ $top3->total_penilaian }} {{ $mode === 'partisipasi' ? 'siswa memilih' : 'ulasan' }}</small>
                            </div>
                            <a href="{{ route('landing.guru.detail', $top3->id) }}" class="btn btn-outline-custom btn-sm btn-podium w-100 rounded-pill">
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
                        @php $pct = round(($g->rata_rata_nilai / 5) * 100); @endphp
                        <tr>
                            <td class="text-center">
                                @if($index === 0) <span class="fs-4">🥇</span>
                                @elseif($index === 1) <span class="fs-4">🥈</span>
                                @elseif($index === 2) <span class="fs-4">🥉</span>
                                @else <span class="badge font-mono" style="background: var(--bg-light); color: var(--text-dark); border: 1px solid var(--border);">#{{ $index + 1 }}</span>
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
                                <a href="{{ route('landing.guru.detail', $g->id) }}" class="btn btn-sm btn-outline-primary">
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

        {{-- Bottom Kembali ke Beranda --}}
        <div class="text-center mt-5" data-aos="fade-up">
            <a href="{{ route('landing.index') }}" class="btn btn-outline-custom rounded-pill px-4 py-2.5 shadow-sm d-inline-flex align-items-center gap-2 fw-semibold">
                <i class="bi bi-arrow-left"></i> Kembali ke Beranda Utama
            </a>
        </div>
    </div>
</section>
@endsection