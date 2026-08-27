{{-- resources/views/landing/guru-detail.blade.php --}}
@extends('layouts.landing')
@section('title', $guru->nama)

@section('content')
<section style="background: var(--bg-light); padding: 140px 0 80px; min-height: 100vh;">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb" style="font-size: 0.85rem;">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none" style="color: var(--text-muted);">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('landing.leaderboard') }}" class="text-decoration-none" style="color: var(--text-muted);">Leaderboard</a></li>
                <li class="breadcrumb-item active">{{ $guru->nama }}</li>
            </ol>
        </nav>

        <div class="row g-4">
            <div class="col-lg-4">
                <img src="{{ $guru->photo_url }}" class="rounded w-100 mb-3" style="height: 400px; object-fit: cover;">
                <div class="card-custom p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="font-mono" style="font-size: 0.7rem; font-weight: 600; color: var(--text-muted); letter-spacing: 2px;">RATING KESELURUHAN</div>
                        <div style="color: var(--secondary); font-weight: 700;">
                            <i class="bi bi-star-fill"></i> {{ number_format($guru->rata_rata_nilai, 1) }}
                        </div>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 4px;">
                        <div class="progress-bar" style="width: {{ ($guru->rata_rata_nilai / 5) * 100 }}%; background: var(--secondary);"></div>
                    </div>
                    <small class="text-muted mt-2 d-block">Berdasarkan {{ $guru->total_penilaian }} penilaian siswa</small>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="mb-3">
                    <span class="badge-custom" style="background: var(--primary); color: white; padding: 0.5rem 1rem; border-radius: 6px; font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; letter-spacing: 1px;">
                        {{ strtoupper($guru->kategori_label) }}
                    </span>
                    <span class="badge-custom" style="background: var(--bg-light); color: var(--text-dark); border: 1px solid var(--border); padding: 0.5rem 1rem; border-radius: 6px; font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; letter-spacing: 1px;">
                        {{ strtoupper($guru->jurusan?->kode_jurusan ?? 'UMUM') }}
                    </span>
                </div>

                <h1 class="fw-bold mb-2" style="font-size: 2.5rem;">{{ $guru->nama }}</h1>
                <h5 class="mb-4" style="color: var(--primary); font-weight: 600;">{{ $guru->jurusan?->nama_jurusan }}</h5>

                <p class="text-muted mb-4" style="line-height: 1.7; font-size: 1.05rem;">
                    {{ $guru->bio ?? 'Berdedikasi dalam mendidik siswa SMK untuk menjadi tenaga profesional di bidangnya.' }}
                </p>

                <div class="row g-3 mb-4">
                    <div class="col-md-3 col-6">
                        <div class="card-custom p-3 text-center">
                            <div class="font-mono" style="font-size: 0.65rem; color: var(--text-muted); letter-spacing: 1px;">RATA-RATA</div>
                            <div class="fw-bold fs-5" style="color: var(--secondary);">{{ number_format($guru->rata_rata_nilai, 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card-custom p-3 text-center">
                            <div class="font-mono" style="font-size: 0.65rem; color: var(--text-muted); letter-spacing: 1px;">TOTAL ULASAN</div>
                            <div class="fw-bold fs-5">{{ $guru->total_penilaian }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card-custom p-3 text-center">
                            <div class="font-mono" style="font-size: 0.65rem; color: var(--text-muted); letter-spacing: 1px;">KATEGORI</div>
                            <div class="fw-bold fs-6">{{ $guru->kategori === 'normada' ? 'Normada' : 'Produktif' }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card-custom p-3 text-center">
                            <div class="font-mono" style="font-size: 0.65rem; color: var(--text-muted); letter-spacing: 1px;">BADGE</div>
                            <div class="fw-bold fs-5">{{ $guru->penghargaan->count() }}</div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('login') }}" class="btn btn-primary-custom" style="padding: 0.75rem 2rem;">
                    <i class="bi bi-pencil-square"></i> Login untuk Memberi Penilaian
                </a>

                {{-- Penilaian Terbaru --}}
                @if($guru->penilaian->isNotEmpty())
                <h4 class="fw-bold mt-5 mb-3">Penilaian Terbaru</h4>
                @foreach($guru->penilaian->take(5) as $p)
                <div class="card-custom p-4 mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                <i class="bi bi-person"></i>
                            </div>
                            <div>
                                <div class="fw-bold">{{ $p->siswa->name ?? 'Siswa Anonim' }}</div>
                                <div class="font-mono" style="font-size: 0.7rem; color: var(--text-muted); letter-spacing: 1px;">
                                    {{ $p->created_at->format('d M Y') }}
                                </div>
                            </div>
                        </div>
                        <div style="color: var(--secondary);">
                            @for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i <= $p->total_nilai ? '-fill' : '' }}"></i>@endfor
                        </div>
                    </div>
                    @if($p->kritik)
                        <p class="mb-2" style="font-size: 0.95rem;">"{{ $p->kritik }}"</p>
                    @endif
                    @if($p->saran)
                        <p class="mb-0 text-muted" style="font-size: 0.9rem;"><strong>Saran:</strong> {{ $p->saran }}</p>
                    @endif
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</section>
@endsection