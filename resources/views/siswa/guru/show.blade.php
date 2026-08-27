@extends('layouts.siswa')
@section('title', 'Detail Guru')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">PROFIL GURU</div>
        <h1 class="page-title">{{ $guru->nama }}</h1>
        <p class="page-subtitle">Lihat profil, pencapaian, dan ulasan dari guru ini.</p>
    </div>
    <a href="{{ route('siswa.guru.index') }}" class="btn btn-outline-custom">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Guru
    </a>
</div>

<div class="row g-4">
    {{-- Kolom Kiri: Profil & Statistik --}}
    <div class="col-lg-4">
        <div class="card-custom p-4 text-center mb-4">
            <img src="{{ $guru->photo_url }}" alt="{{ $guru->nama }}" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover; border: 4px solid var(--primary);">
            <h4 class="fw-bold mb-1">{{ $guru->nama }}</h4>
            <p class="text-muted small mb-3">NIP: {{ $guru->nip }}</p>
            
            <div class="d-flex justify-content-center gap-2 mb-3 flex-wrap">
                <span class="badge-custom" style="background: {{ $guru->kategori === 'normada' ? 'rgba(0,51,102,0.1)' : 'rgba(0,168,107,0.1)' }}; color: {{ $guru->kategori === 'normada' ? 'var(--primary)' : 'var(--accent)' }};">
                    {{ strtoupper($guru->kategori) }}
                </span>
                @if($guru->jurusan)
                    <span class="badge-custom" style="background: rgba(255,193,7,0.15); color: #d4a017;">{{ $guru->jurusan->nama_jurusan }}</span>
                @endif
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <div class="p-3 rounded" style="background: var(--bg-light);">
                        <div class="fw-bold" style="color: var(--primary); font-size: 1.5rem;">{{ number_format($guru->rata_rata_nilai, 1) }}</div>
                        <div class="text-muted small font-mono">RATA-RATA</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-3 rounded" style="background: var(--bg-light);">
                        <div class="fw-bold" style="color: var(--accent); font-size: 1.5rem;">{{ $guru->total_penilaian }}</div>
                        <div class="text-muted small font-mono">ULASAN</div>
                    </div>
                </div>
            </div>

            {{-- Tombol Beri Penilaian --}}
            @if(!$sudahMenilai)
                <a href="{{ route('siswa.penilaian.create', $guru) }}" class="btn btn-primary-custom w-100">
                    <i class="bi bi-star-fill me-1"></i> Beri Penilaian
                </a>
            @else
                <button class="btn btn-outline-secondary w-100" disabled>
                    <i class="bi bi-check-circle-fill me-1"></i> Sudah Dinilai Periode Ini
                </button>
            @endif
        </div>

        {{-- Badge / Penghargaan --}}
        @if(isset($guru->penghargaan) && $guru->penghargaan->isNotEmpty())
        <div class="card-custom p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-trophy-fill text-warning me-2"></i>Penghargaan</h6>
            <div class="d-flex flex-column gap-2">
                @foreach($guru->penghargaan as $award)
                    <div class="d-flex align-items-center gap-2 p-2 rounded" style="background: var(--bg-light);">
                        <span style="font-size: 1.5rem;">{{ $award->badge->icon }}</span>
                        <div class="text-start">
                            <div class="fw-bold small">{{ $award->badge->nama_badge }}</div>
                            <div class="text-muted" style="font-size: 0.7rem;">{{ $award->periode->nama_periode }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Kolom Kanan: Bio & Ulasan Terbaru --}}
    <div class="col-lg-8">
        {{-- Bio --}}
        <div class="card-custom p-4 mb-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Tentang Guru</h5>
            <p class="text-muted mb-0" style="line-height: 1.8;">
                {{ $guru->bio ?: 'Belum ada deskripsi yang ditambahkan oleh guru ini.' }}
            </p>
        </div>

        {{-- Ulasan Terbaru --}}
        <div class="card-custom p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-chat-quote-fill me-2 text-primary"></i>Ulasan Terbaru</h5>
            
            @if(!isset($ulasanTerbaru) || $ulasanTerbaru->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-chat-square-text fs-1 mb-2 d-block opacity-50"></i>
                    <p class="mb-0">Belum ada ulasan untuk guru ini.</p>
                </div>
            @else
                <div class="d-flex flex-column gap-3">
                    @foreach($ulasanTerbaru as $ulasan)
                        <div class="p-3 rounded" style="background: var(--bg-light); border-left: 3px solid var(--primary);">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="d-flex text-warning">
                                        @php $avgRating = round($ulasan->total_nilai / 6); @endphp
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $avgRating)
                                                <i class="bi bi-star-fill"></i>
                                            @else
                                                <i class="bi bi-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="fw-bold small">{{ number_format($ulasan->total_nilai / 6, 1) }}/5.0</span>
                                </div>
                                <small class="text-muted">{{ $ulasan->created_at->diffForHumans() }}</small>
                            </div>
                            
                            @if($ulasan->kritik || $ulasan->saran)
                                <div class="mb-2">
                                    @if($ulasan->kritik)
                                        <p class="mb-1 small"><strong class="text-muted">Kritik:</strong> {{ $ulasan->kritik }}</p>
                                    @endif
                                    @if($ulasan->saran)
                                        <p class="mb-0 small"><strong class="text-muted">Saran:</strong> {{ $ulasan->saran }}</p>
                                    @endif
                                </div>
                            @else
                                <p class="mb-0 small text-muted fst-italic">"Guru yang sangat baik dan mengajar dengan penuh dedikasi."</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection