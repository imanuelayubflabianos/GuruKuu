{{-- resources/views/landing/leaderboard.blade.php --}}
@extends('layouts.landing')
@section('title', 'Leaderboard')

@section('content')
<section style="background: var(--bg-light); padding: 140px 0 80px; min-height: 100vh;">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="section-label">PENCAPAIAN TERTINGGI</div>
            <h1 class="section-title">Leaderboard Guru</h1>
            <p class="text-muted">Peringkat guru terbaik berdasarkan penilaian siswa</p>
        </div>

        {{-- Podium Top 3 --}}
        @php
            $all = $leaderboardNormada->merge($leaderboardProduktif)
                ->sortByDesc('rata_rata_nilai')
                ->take(3)
                ->values();
            $top1 = $all[0] ?? null;
            $top2 = $all[1] ?? null;
            $top3 = $all[2] ?? null;
        @endphp

        <div class="row g-4 mb-5 align-items-end">
            {{-- #2 --}}
            <div class="col-md-4" data-aos="fade-right">
                @if($top2)
                <div class="card-custom p-4 text-center">
                    <div class="position-relative d-inline-block mb-3">
                        <img src="{{ $top2->photo_url }}" class="rounded-circle" width="100" height="100" style="object-fit: cover; border: 4px solid var(--border);">
                        <span class="position-absolute bottom-0 end-0 bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: 700;">2</span>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $top2->nama }}</h5>
                    <div class="font-mono" style="font-size: 0.7rem; color: var(--text-muted); letter-spacing: 1px;">{{ strtoupper($top2->jurusan?->nama_jurusan ?? '-') }}</div>
                    <div class="mt-2" style="color: var(--secondary);">
                        <i class="bi bi-star-fill"></i> {{ number_format($top2->rata_rata_nilai, 1) }}
                    </div>
                </div>
                @endif
            </div>

            {{-- #1 --}}
            <div class="col-md-4" data-aos="zoom-in">
                @if($top1)
                <div class="card-custom p-5 text-center" style="background: var(--primary); color: white; border: none;">
                    <div class="position-relative d-inline-block mb-3">
                        <img src="{{ $top1->photo_url }}" class="rounded-circle" width="120" height="120" style="object-fit: cover; border: 4px solid var(--secondary);">
                        <span class="position-absolute bottom-0 end-0 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: 700; background: var(--secondary); color: var(--primary);">1</span>
                    </div>
                    <h4 class="fw-bold mb-1">{{ $top1->nama }}</h4>
                    <div class="font-mono" style="font-size: 0.75rem; letter-spacing: 2px; color: var(--secondary);">{{ strtoupper($top1->jurusan?->nama_jurusan ?? '-') }}</div>
                    <div class="row g-3 mt-3">
                        <div class="col-6">
                            <div class="font-mono" style="font-size: 0.65rem; letter-spacing: 1px; opacity: 0.8;">RATING</div>
                            <div class="fw-bold fs-4">{{ number_format($top1->rata_rata_nilai, 1) }}</div>
                        </div>
                        <div class="col-6">
                            <div class="font-mono" style="font-size: 0.65rem; letter-spacing: 1px; opacity: 0.8;">ULASAN</div>
                            <div class="fw-bold fs-4">{{ $top1->total_penilaian }}</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- #3 --}}
            <div class="col-md-4" data-aos="fade-left">
                @if($top3)
                <div class="card-custom p-4 text-center">
                    <div class="position-relative d-inline-block mb-3">
                        <img src="{{ $top3->photo_url }}" class="rounded-circle" width="100" height="100" style="object-fit: cover; border: 4px solid var(--border);">
                        <span class="position-absolute bottom-0 end-0 bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: 700;">3</span>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $top3->nama }}</h5>
                    <div class="font-mono" style="font-size: 0.7rem; color: var(--text-muted); letter-spacing: 1px;">{{ strtoupper($top3->jurusan?->nama_jurusan ?? '-') }}</div>
                    <div class="mt-2" style="color: var(--secondary);">
                        <i class="bi bi-star-fill"></i> {{ number_format($top3->rata_rata_nilai, 1) }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Tabs Kategori --}}
        <ul class="nav nav-pills justify-content-center mb-4" data-aos="fade-up">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#normada" style="border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 600;">
                    Guru Normada
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#produktif" style="border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 600;">
                    Guru Produktif
                </button>
            </li>
        </ul>

        {{-- Tabel Ranking --}}
        <div class="tab-content" data-aos="fade-up">
            @foreach(['normada' => $leaderboardNormada, 'produktif' => $leaderboardProduktif] as $key => $list)
            <div class="tab-pane fade {{ $key === 'normada' ? 'show active' : '' }}" id="{{ $key }}">
                <div class="card-custom">
                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">RANKING</th>
                                    <th>NAMA GURU</th>
                                    <th>DEPARTEMEN</th>
                                    <th class="text-center">RATING</th>
                                    <th class="text-center">ULASAN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($list as $index => $g)
                                <tr>
                                    <td>
                                        @if($index === 0) <span class="fs-4">🥇</span>
                                        @elseif($index === 1) <span class="fs-4">🥈</span>
                                        @elseif($index === 2) <span class="fs-4">🥉</span>
                                        @else <span class="fw-bold" style="font-size: 1.1rem;">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $g->photo_url }}" class="rounded-circle me-3" width="48" height="48" style="object-fit: cover;">
                                            <strong>{{ $g->nama }}</strong>
                                        </div>
                                    </td>
                                    <td class="font-mono" style="font-size: 0.75rem;">{{ strtoupper($g->jurusan?->nama_jurusan ?? '-') }}</td>
                                    <td class="text-center">
                                        <strong style="color: var(--secondary);">
                                            <i class="bi bi-star-fill"></i> {{ number_format($g->rata_rata_nilai, 1) }}
                                        </strong>
                                    </td>
                                    <td class="text-center">{{ $g->total_penilaian }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection