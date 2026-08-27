@extends('layouts.siswa')
@section('title', 'Leaderboard & Statistik')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">PERINGKAT & STATISTIK</div>
        <h1 class="page-title">Leaderboard Guru</h1>
        <p class="page-subtitle">Peringkat guru terbaik berdasarkan penilaian siswa</p>
    </div>
    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalTop10">
        <i class="bi bi-list-ul me-1"></i> Lihat Top 10 Semua Kelas
    </button>
</div>

{{-- 1. TOP 3 GLOBAL (Layout: #2 Kiri, #1 Tengah Besar, #3 Kanan) --}}
@if($top3Global->count() >= 3)
@php
    $top1 = $top3Global[0];
    $top2 = $top3Global[1];
    $top3 = $top3Global[2];
@endphp
<div class="row g-4 mb-5 align-items-end justify-content-center">
    {{-- TOP 2 (Kiri) --}}
    <div class="col-md-3 order-md-1">
        <a href="{{ route('siswa.guru.show', $top2) }}" class="text-decoration-none" style="display: block;">
            <div class="card-custom p-4 text-center h-100" style="border: 1px solid var(--border); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                <div class="position-relative d-inline-block mb-3">
                    <img src="{{ $top2->photo_url }}" class="rounded-circle" style="width: 90px; height: 90px; object-fit: cover; border: 4px solid #C0C0C0;">
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background: #C0C0C0; color: #000; font-size: 0.9rem; padding: 0.5rem 0.75rem;">#2</span>
                </div>
                <h6 class="fw-bold mb-1" style="color: var(--text-dark);">{{ $top2->nama }}</h6>
                <p class="text-muted small mb-2">{{ $top2->jurusan?->nama_jurusan ?? 'Umum' }}</p>
                <div style="color: var(--secondary); font-weight: 700; font-size: 1.1rem;">
                    <i class="bi bi-star-fill"></i> {{ number_format($top2->rata_rata_nilai, 2) }}
                </div>
                <small class="text-muted d-block mt-1">{{ $top2->total_penilaian }} vote</small>
            </div>
        </a>
    </div>

    {{-- TOP 1 (Tengah, Besar) --}}
    <div class="col-md-4 order-md-2 mt-md-4">
        <a href="{{ route('siswa.guru.show', $top1) }}" class="text-decoration-none" style="display: block;">
            <div class="card-custom p-5 text-center h-100" style="background: var(--primary); border: none; color: white; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                <div class="position-relative d-inline-block mb-3">
                    <img src="{{ $top1->photo_url }}" class="rounded-circle" style="width: 130px; height: 130px; object-fit: cover; border: 5px solid var(--secondary);">
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background: #FFD700; color: #000; font-size: 1.1rem; padding: 0.6rem 0.9rem;">🏆 #1</span>
                </div>
                <h4 class="fw-bold mb-1">{{ $top1->nama }}</h4>
                <p class="mb-2" style="opacity: 0.8; font-size: 0.9rem;">{{ $top1->jurusan?->nama_jurusan ?? 'Umum' }}</p>
                <div style="color: var(--secondary); font-weight: 700; font-size: 1.75rem;">
                    <i class="bi bi-star-fill"></i> {{ number_format($top1->rata_rata_nilai, 2) }}
                </div>
                <small style="opacity: 0.8;" class="d-block mt-1">{{ $top1->total_penilaian }} vote</small>
            </div>
        </a>
    </div>

    {{-- TOP 3 (Kanan) --}}
    <div class="col-md-3 order-md-3">
        <a href="{{ route('siswa.guru.show', $top3) }}" class="text-decoration-none" style="display: block;">
            <div class="card-custom p-4 text-center h-100" style="border: 1px solid var(--border); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                <div class="position-relative d-inline-block mb-3">
                    <img src="{{ $top3->photo_url }}" class="rounded-circle" style="width: 90px; height: 90px; object-fit: cover; border: 4px solid #CD7F32;">
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background: #CD7F32; color: #fff; font-size: 0.9rem; padding: 0.5rem 0.75rem;">#3</span>
                </div>
                <h6 class="fw-bold mb-1" style="color: var(--text-dark);">{{ $top3->nama }}</h6>
                <p class="text-muted small mb-2">{{ $top3->jurusan?->nama_jurusan ?? 'Umum' }}</p>
                <div style="color: var(--secondary); font-weight: 700; font-size: 1.1rem;">
                    <i class="bi bi-star-fill"></i> {{ number_format($top3->rata_rata_nilai, 2) }}
                </div>
                <small class="text-muted d-block mt-1">{{ $top3->total_penilaian }} vote</small>
            </div>
        </a>
    </div>
</div>

<p class="text-center text-muted small mb-5">
    <i class="bi bi-info-circle me-1"></i> Klik kartu di atas untuk melihat detail & memberi penilaian
</p>
@endif

<hr class="my-5">

{{-- 2. STATISTIK VOTING PER KELAS (TERPISAH NORMADA & PRODUKTIF) --}}
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card-custom p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-building me-2 text-primary"></i>Pilih Kelas</h5>
            <p class="text-muted small mb-3">Pilih kelas untuk melihat hasil polling vote guru di kelas tersebut.</p>
            
            <form method="GET" action="{{ route('siswa.leaderboard.index') }}">
                <select name="kelas_id" class="form-select mb-3" onchange="this.form.submit()" style="border-radius: 8px; border: 2px solid var(--border);">
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ $filterKelasId == $kelas->id ? 'selected' : '' }}>
                            Tingkat {{ $kelas->tingkat }} - {{ $kelas->nama_kelas }} ({{ $kelas->jurusan->nama_jurusan }})
                        </option>
                    @endforeach
                </select>
            </form>

            @if($kelasAktifStats)
                <div class="mt-4 p-3 rounded" style="background: var(--bg-light);">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small text-muted">Total Guru</span>
                        <strong>{{ $kelasAktifStats['normada']->count() + $kelasAktifStats['produktif']->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small text-muted">Guru Normada</span>
                        <strong style="color: var(--primary);">{{ $kelasAktifStats['normada']->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="small text-muted">Guru Produktif</span>
                        <strong style="color: var(--accent);">{{ $kelasAktifStats['produktif']->count() }}</strong>
                    </div>
                    <hr class="my-2">
                    <div class="d-flex justify-content-between">
                        <span class="small text-muted">Total Siswa</span>
                        <strong>{{ $kelasAktifStats['kelas']->jumlah_siswa }}</strong>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="col-lg-8">
        @if($kelasAktifStats)
            <div class="card-custom p-4">
                <h5 class="fw-bold mb-1">
                    <i class="bi bi-bar-chart-fill me-2 text-primary"></i>
                    Hasil Polling: Tingkat {{ $kelasAktifStats['kelas']->tingkat }} - {{ $kelasAktifStats['kelas']->nama_kelas }}
                </h5>
                <p class="text-muted small mb-4">{{ $kelasAktifStats['kelas']->jurusan->nama_jurusan }}</p>

                {{-- Tab Normada vs Produktif --}}
                <ul class="nav nav-pills mb-4" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active d-flex align-items-center gap-2" data-bs-toggle="pill" data-bs-target="#tabNormada" type="button">
                            <i class="bi bi-book-fill"></i> Guru Normada
                            <span class="badge bg-light text-dark">{{ $kelasAktifStats['normada']->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link d-flex align-items-center gap-2" data-bs-toggle="pill" data-bs-target="#tabProduktif" type="button">
                            <i class="bi bi-briefcase-fill"></i> Guru Produktif
                            <span class="badge bg-light text-dark">{{ $kelasAktifStats['produktif']->count() }}</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    {{-- TAB NORMADA --}}
                    <div class="tab-pane fade show active" id="tabNormada">
                        @if($kelasAktifStats['normada']->isEmpty())
                            <div class="alert alert-info">Belum ada data penilaian untuk guru normada di kelas ini.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-custom mb-0">
                                    <thead>
                                        <tr>
                                            <th>Peringkat</th>
                                            <th>Nama Guru</th>
                                            <th>Mata Pelajaran</th>
                                            <th class="text-center">Jumlah Vote</th>
                                            <th class="text-center">Rata-rata</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($kelasAktifStats['normada'] as $index => $guru)
                                        <tr>
                                            <td>
                                                @if($index === 0) <span class="badge" style="background: #FFD700; color: #000;">🥇 #1</span>
                                                @elseif($index === 1) <span class="badge" style="background: #C0C0C0; color: #000;">🥈 #2</span>
                                                @elseif($index === 2) <span class="badge" style="background: #CD7F32; color: #fff;">🥉 #3</span>
                                                @else <span class="text-muted fw-bold">#{{ $index + 1 }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <img src="{{ $guru->photo_url }}" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                                    <span class="fw-bold">{{ $guru->nama }}</span>
                                                </div>
                                            </td>
                                            <td><small class="text-muted">{{ $guru->pivot->mata_pelajaran ?? '-' }}</small></td>
                                            <td class="text-center fw-bold">{{ $guru->penilaian_count }}</td>
                                            <td class="text-center">
                                                <span class="fw-bold" style="color: var(--secondary);">
                                                    <i class="bi bi-star-fill"></i> {{ number_format($guru->rata_rata_nilai, 2) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    {{-- TAB PRODUKTIF --}}
                    <div class="tab-pane fade" id="tabProduktif">
                        @if($kelasAktifStats['produktif']->isEmpty())
                            <div class="alert alert-info">Belum ada data penilaian untuk guru produktif di kelas ini.</div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-custom mb-0">
                                    <thead>
                                        <tr>
                                            <th>Peringkat</th>
                                            <th>Nama Guru</th>
                                            <th>Mata Pelajaran</th>
                                            <th class="text-center">Jumlah Vote</th>
                                            <th class="text-center">Rata-rata</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($kelasAktifStats['produktif'] as $index => $guru)
                                        <tr>
                                            <td>
                                                @if($index === 0) <span class="badge" style="background: #FFD700; color: #000;">🥇 #1</span>
                                                @elseif($index === 1) <span class="badge" style="background: #C0C0C0; color: #000;">🥈 #2</span>
                                                @elseif($index === 2) <span class="badge" style="background: #CD7F32; color: #fff;">🥉 #3</span>
                                                @else <span class="text-muted fw-bold">#{{ $index + 1 }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <img src="{{ $guru->photo_url }}" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                                    <span class="fw-bold">{{ $guru->nama }}</span>
                                                </div>
                                            </td>
                                            <td><small class="text-muted">{{ $guru->pivot->mata_pelajaran ?? '-' }}</small></td>
                                            <td class="text-center fw-bold">{{ $guru->penilaian_count }}</td>
                                            <td class="text-center">
                                                <span class="fw-bold" style="color: var(--secondary);">
                                                    <i class="bi bi-star-fill"></i> {{ number_format($guru->rata_rata_nilai, 2) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-warning">Pilih kelas dari menu di sebelah kiri untuk melihat statistiknya.</div>
        @endif
    </div>
</div>

{{-- MODAL TOP 10 SEMUA KELAS --}}
<div class="modal fade" id="modalTop10" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-trophy-fill text-warning me-2"></i>Top 10 Guru Terbaik (Semua Kelas)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if($top10Global->isEmpty())
                    <p class="text-center text-muted">Belum ada data penilaian.</p>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($top10Global as $index => $guru)
                        <a href="{{ route('siswa.guru.show', $guru) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
                            <div class="d-flex align-items-center gap-3">
                                <span class="fw-bold fs-5" style="width: 30px; color: {{ $index < 3 ? 'var(--primary)' : 'var(--text-muted)' }};">
                                    {{ $index + 1 }}.
                                </span>
                                <img src="{{ $guru->photo_url }}" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $guru->nama }}</h6>
                                    <small class="text-muted">{{ $guru->jurusan?->nama_jurusan ?? 'Umum' }}</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold" style="color: var(--secondary);">
                                    <i class="bi bi-star-fill"></i> {{ number_format($guru->rata_rata_nilai, 2) }}
                                </div>
                                <small class="text-muted">{{ $guru->total_penilaian }} vote</small>
                            </div>
                        </a>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection