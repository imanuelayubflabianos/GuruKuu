@extends('layouts.siswa')
@section('title', 'Detail Guru')

@section('content')
<div class="page-header">
    <div>
        <a href="{{ route('siswa.guru.index') }}" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Guru
        </a>
        <h1 class="page-title mt-2">{{ $guru->nama }}</h1>
        <p class="page-subtitle">
            <span class="badge bg-{{ $guru->kategori == 'normada' ? 'primary' : 'success' }}">{{ ucfirst($guru->kategori) }}</span>
            @if($guru->jurusan)
                <span class="badge bg-warning text-dark">{{ $guru->jurusan->nama_jurusan }}</span>
            @endif
        </p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card-custom p-4 text-center">
            <img src="{{ $guru->photo_url }}" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid var(--primary);">
            <h4 class="fw-bold mb-2">{{ $guru->nama }}</h4>
            <p class="text-muted small mb-2">NIP: {{ $guru->nip }}</p>
            @if($guru->bio)
                <p class="small text-muted">{{ $guru->bio }}</p>
            @endif
        </div>
    </div>

    <div class="col-md-8">
        <div class="card-custom p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-graph-up me-2"></i>Statistik Evaluasi</h5>
            
            @if($stats['total_penilaian'] > 0)
            <div class="row g-3">
                @php
                    $aspects = [
                        'Kedisiplinan' => $stats['rata_kedisiplinan'],
                        'Cara Mengajar' => $stats['rata_cara_mengajar'],
                        'Komunikasi' => $stats['rata_komunikasi'],
                        'Tanggung Jawab' => $stats['rata_tanggung_jawab'],
                        'Kreativitas' => $stats['rata_kreativitas'],
                        'Keramahan' => $stats['rata_keramahan'],
                    ];
                @endphp
                @foreach($aspects as $label => $value)
                <div class="col-md-6">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="fw-bold">{{ $label }}</small>
                        <small class="text-primary fw-bold">{{ number_format($value, 1) }}/5</small>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar" style="width: {{ ($value/5)*100 }}%; background: var(--primary);"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-3 p-2 rounded text-center" style="background: var(--bg-light);">
                <small class="text-muted">Total {{ $stats['total_penilaian'] }} siswa telah memberikan penilaian</small>
            </div>
            @else
            <div class="text-center py-4 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                Belum ada penilaian untuk guru ini.
            </div>
            @endif

            @if($periodeAktif = \App\Models\Periode::where('status', 'aktif')->first())
                @if($sudahMenilai)
                    <div class="alert alert-success mt-3 mb-0">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <strong>Anda sudah menilai guru ini pada periode {{ $periodeAktif->nama_periode }}.</strong>
                    </div>
                @else
                    <a href="{{ route('siswa.penilaian.create', $guru) }}" class="btn btn-primary-custom w-100 mt-3">
                        <i class="bi bi-pencil-square me-1"></i> Beri Penilaian Sekarang
                    </a>
                @endif
            @endif
        </div>
    </div>
</div>

{{-- ULASAN SISWA LAIN --}}
<div class="card-custom p-4">
    <h5 class="fw-bold mb-3">
        <i class="bi bi-chat-left-quote me-2"></i>Ulasan dari Siswa Lain
        <span class="badge bg-primary ms-2">{{ $semuaFeedback->count() }}</span>
    </h5>

    @if($semuaFeedback->count() > 0)
        @foreach($semuaFeedback as $fb)
        <div class="border rounded p-3 mb-3" style="background: #f8f9fa;">
            <div class="d-flex justify-content-between mb-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: var(--primary); color: white; font-weight: bold;">
                        {{ strtoupper(substr($fb->siswa->name ?? 'A', 0, 1)) }}
                    </div>
                    <div>
                        <strong class="small">{{ $fb->siswa->name ?? 'Siswa Anonim' }}</strong>
                        <small class="text-muted d-block">{{ $fb->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
            @if($fb->kritik)
                <div class="p-2 rounded small mb-2" style="background: #fff3cd; border-left: 3px solid #ffc107;">
                    <strong>Kritik:</strong> {{ $fb->kritik }}
                </div>
            @endif
            @if($fb->saran)
                <div class="p-2 rounded small" style="background: #d1ecf1; border-left: 3px solid #17a2b8;">
                    <strong>Saran:</strong> {{ $fb->saran }}
                </div>
            @endif
        </div>
        @endforeach
    @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-chat-square-dots fs-1 d-block mb-3"></i>
            <p class="mb-0">Belum ada ulasan dari siswa lain untuk guru ini.</p>
            <small>Jadilah yang pertama memberikan kritik dan saran!</small>
        </div>
    @endif
</div>
@endsection