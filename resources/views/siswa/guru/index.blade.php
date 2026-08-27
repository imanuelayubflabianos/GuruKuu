@extends('layouts.siswa')
@section('title', 'Daftar Guru')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">PENILAIAN GURU</div>
        <h1 class="page-title">Daftar Guru</h1>
        @if($kelasAktif)
            <p class="page-subtitle">
                <span class="badge bg-primary me-2">{{ $kelasAktif->nama_kelas }} - Tingkat {{ $kelasAktif->tingkat }}</span>
                Beri penilaian untuk guru yang mengajar di kelas Anda
            </p>
        @else
            <p class="page-subtitle text-danger">Anda belum terdaftar di kelas manapun.</p>
        @endif
    </div>
</div>

@if(!$kelasAktif)
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-2"></i>
        Anda belum terdaftar di kelas manapun. Silakan hubungi admin.
    </div>
@else
    @php
        // Pisahkan koleksi guru berdasarkan kategori
        $guruNormada = $guru->where('kategori', 'normada');
        $guruProduktif = $guru->where('kategori', 'produktif');
    @endphp

    {{-- TAB NAVIGASI --}}
    <ul class="nav nav-pills mb-4" id="guruTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active d-flex align-items-center gap-2" id="normada-tab" data-bs-toggle="pill" data-bs-target="#normada" type="button" role="tab" aria-selected="true">
                <i class="bi bi-book-fill"></i> Guru Normada 
                <span class="badge bg-light text-dark ms-1">{{ $guruNormada->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link d-flex align-items-center gap-2" id="produktif-tab" data-bs-toggle="pill" data-bs-target="#produktif" type="button" role="tab" aria-selected="false">
                <i class="bi bi-briefcase-fill"></i> Guru Produktif 
                <span class="badge bg-light text-dark ms-1">{{ $guruProduktif->count() }}</span>
            </button>
        </li>
    </ul>

    {{-- KONTEN TAB --}}
    <div class="tab-content" id="guruTabContent">
        
        {{-- TAB 1: GURU NORMADA --}}
        <div class="tab-pane fade show active" id="normada" role="tabpanel" aria-labelledby="normada-tab">
            @if($guruNormada->isEmpty())
                <div class="alert alert-info d-flex align-items-center">
                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                    <div>Tidak ada guru normada yang mengajar di kelas Anda saat ini.</div>
                </div>
            @else
                <div class="row g-4">
                    @foreach($guruNormada as $g)
                    <div class="col-md-6 col-lg-4">
                        <div class="card-custom p-4 h-100" style="transition: all 0.3s; border-top: 4px solid var(--primary);" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="text-center mb-3">
                                <img src="{{ $g->photo_url }}" alt="{{ $g->nama }}" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover; border: 4px solid var(--primary);">
                            </div>
                            <h5 class="fw-bold text-center mb-2">{{ $g->nama }}</h5>
                            <div class="text-center mb-3">
                                <span class="badge-custom" style="background: rgba(0,51,102,0.1); color: var(--primary);">NORMADA</span>
                                @if($g->jurusan)
                                    <span class="badge-custom" style="background: rgba(255,193,7,0.15); color: #d4a017;">{{ $g->jurusan->nama_jurusan }}</span>
                                @endif
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3 px-2">
                                <div>
                                    <div class="text-muted small">Rating</div>
                                    <div class="fw-bold" style="color: var(--secondary); font-size: 1.1rem;">
                                        <i class="bi bi-star-fill"></i> {{ number_format($g->rata_rata_nilai, 1) }}
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="text-muted small">Total Penilaian</div>
                                    <div class="fw-bold">{{ $g->total_penilaian }} <small class="text-muted">siswa</small></div>
                                </div>
                            </div>
                            <a href="{{ route('siswa.guru.show', $g) }}" class="btn btn-primary-custom w-100">
                                <i class="bi bi-eye me-1"></i> Lihat Detail & Nilai
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- TAB 2: GURU PRODUKTIF --}}
        <div class="tab-pane fade" id="produktif" role="tabpanel" aria-labelledby="produktif-tab">
            @if($guruProduktif->isEmpty())
                <div class="alert alert-info d-flex align-items-center">
                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                    <div>Tidak ada guru produktif yang mengajar di kelas Anda saat ini.</div>
                </div>
            @else
                <div class="row g-4">
                    @foreach($guruProduktif as $g)
                    <div class="col-md-6 col-lg-4">
                        <div class="card-custom p-4 h-100" style="transition: all 0.3s; border-top: 4px solid var(--accent);" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="text-center mb-3">
                                <img src="{{ $g->photo_url }}" alt="{{ $g->nama }}" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover; border: 4px solid var(--accent);">
                            </div>
                            <h5 class="fw-bold text-center mb-2">{{ $g->nama }}</h5>
                            <div class="text-center mb-3">
                                <span class="badge-custom" style="background: rgba(0,168,107,0.1); color: var(--accent);">PRODUKTIF</span>
                                @if($g->jurusan)
                                    <span class="badge-custom" style="background: rgba(255,193,7,0.15); color: #d4a017;">{{ $g->jurusan->nama_jurusan }}</span>
                                @endif
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3 px-2">
                                <div>
                                    <div class="text-muted small">Rating</div>
                                    <div class="fw-bold" style="color: var(--secondary); font-size: 1.1rem;">
                                        <i class="bi bi-star-fill"></i> {{ number_format($g->rata_rata_nilai, 1) }}
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="text-muted small">Total Penilaian</div>
                                    <div class="fw-bold">{{ $g->total_penilaian }} <small class="text-muted">siswa</small></div>
                                </div>
                            </div>
                            <a href="{{ route('siswa.guru.show', $g) }}" class="btn btn-primary-custom w-100">
                                <i class="bi bi-eye me-1"></i> Lihat Detail & Nilai
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
@endif
@endsection