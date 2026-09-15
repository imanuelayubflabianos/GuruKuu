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
            @if($guru->jurusan)
                <span class="badge bg-primary text-white">{{ $guru->jurusan->nama_jurusan }}</span>
            @else
                <span class="badge bg-secondary text-white">Guru Pengajar</span>
            @endif
        </p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-5 col-lg-4">
        <div class="card-custom p-4 text-center">
            <div class="position-relative d-inline-block mb-3">
                <img src="{{ $guru->photo_url }}" class="rounded-circle shadow-sm" style="width: 140px; height: 140px; object-fit: cover; border: 4px solid var(--primary);">
            </div>
            <h4 class="fw-bold mb-1" style="color: var(--text-dark);">{{ $guru->nama }}</h4>
            <p class="text-muted small font-mono mb-3">NIP: {{ $guru->nip }}</p>

            {{-- DESKRIPSI & TENTANG GURU --}}
            <div class="text-start p-3 rounded mb-3" style="background: var(--bg-light); border: 1px solid var(--border);">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-person-lines-fill text-primary"></i>
                    <strong class="small text-dark">Tentang Guru & Profil Pendidik</strong>
                </div>
                <p class="small text-muted mb-0" style="line-height: 1.6;">
                    {{ $guru->bio ?: 'Guru pengajar yang berdedikasi membimbing dan mendidik siswa-siswi berakhlak mulia serta berprestasi unggul di lingkungan sekolah.' }}
                </p>
            </div>

            {{-- INFORMASI KONTAK & JURUSAN --}}
            <div class="text-start small text-muted">
                @if($guru->email)
                    <div class="mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-envelope text-primary"></i>
                        <span class="font-mono text-dark">{{ $guru->email }}</span>
                    </div>
                @endif
                @if($guru->phone)
                    <div class="mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-telephone text-primary"></i>
                        <span class="font-mono text-dark">{{ $guru->phone }}</span>
                    </div>
                @endif
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-mortarboard text-primary"></i>
                    <span class="text-dark">{{ $guru->jurusan ? $guru->jurusan->nama_jurusan : 'Guru Pengajar' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7 col-lg-8">
        <div class="card-custom p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-graph-up me-2 text-primary"></i>Statistik Evaluasi Periode Berjalan</h5>
            
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
                @php $valPct = round(($value / 5) * 100); @endphp
                <div class="col-md-6">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="fw-bold">{{ $label }}</small>
                        <small class="text-primary fw-bold font-mono">{{ $valPct }}%</small>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 10px;">
                        <div class="progress-bar rounded-pill" style="width: {{ $valPct }}%; background: var(--primary);"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-3 p-2 rounded text-center" style="background: var(--bg-light);">
                <small class="text-muted">Total <strong>{{ $stats['total_penilaian'] }} siswa</strong> telah memberikan penilaian pada periode aktif</small>
            </div>
            @else
            <div class="text-center py-4 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2 text-muted opacity-50"></i>
                Belum ada penilaian untuk guru ini pada periode aktif saat ini.
            </div>
            @endif

            @if($periodeAktif = \App\Models\Periode::where('status', 'aktif')->first())
                @if($sudahMenilai)
                    <div class="alert alert-success mt-3 mb-0 d-flex align-items-center">
                        <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                        <div>
                            <strong>Penilaian Anda Telah Tercatat!</strong>
                            <div class="small">Anda sudah memberikan evaluasi untuk guru ini pada {{ $periodeAktif->nama_periode }}. Setiap siswa dapat memberikan nilai 1 kali per periode aktif.</div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('siswa.penilaian.create', $guru) }}" class="btn btn-primary-custom w-100 mt-3 py-2">
                        <i class="bi bi-pencil-square me-1"></i> Beri Penilaian Sekarang
                    </a>
                @endif
            @else
                <div class="alert alert-warning mt-3 mb-0 small">
                    <i class="bi bi-exclamation-triangle me-1"></i> Belum ada periode semester aktif saat ini.
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ULASAN SISWA LAIN --}}
<div class="card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">
            <i class="bi bi-chat-left-quote me-2 text-primary"></i>Ulasan dari Siswa Lain
            <span class="badge bg-primary ms-2">{{ $semuaFeedback->count() }}</span>
        </h5>
        <small class="text-muted"><i class="bi bi-shield-check text-success me-1"></i>Anonimitas Siswa Terjamin</small>
    </div>

    @if($semuaFeedback->count() > 0)
        @foreach($semuaFeedback as $fb)
        @php
            $fbScore = round(($fb->total_nilai / 30) * 100);
            $fbColor = $fbScore >= 80 ? 'bg-success' : ($fbScore >= 60 ? 'bg-info' : ($fbScore >= 40 ? 'bg-warning' : 'bg-danger'));
        @endphp
        <div class="border rounded p-3 mb-3" style="background: #f8f9fa;">
            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-secondary bg-opacity-10 text-secondary" style="width: 38px; height: 38px;">
                        <i class="bi bi-incognito fs-5"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <strong class="small text-dark">Siswa (Anonim)</strong>
                            <span class="badge {{ $fbColor }} text-white font-mono" style="font-size: 0.72rem;">
                                Skor: {{ $fbScore }}%
                            </span>
                        </div>
                        <small class="text-muted d-block font-mono" style="font-size: 0.72rem;">{{ $fb->created_at->diffForHumans() }}</small>
                    </div>
                </div>
                <div style="min-width: 130px;">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted" style="font-size: 0.7rem;">Nilai Siswa:</small>
                        <small class="fw-bold font-mono text-dark" style="font-size: 0.7rem;">{{ $fbScore }}% ({{ $fb->total_nilai }}/30)</small>
                    </div>
                    <div class="progress" style="height: 6px; background-color: #dee2e6; border-radius: 3px;">
                        <div class="progress-bar {{ $fbColor }}" style="width: {{ $fbScore }}%;"></div>
                    </div>
                </div>
            </div>

            @if($fb->kritik)
                <div class="p-2 rounded small mb-2" style="background: #fff3cd; border-left: 3px solid #ffc107;">
                    <strong>Kritik:</strong> {{ $fb->kritik }}
                </div>
            @endif
            @if($fb->saran)
                <div class="p-2 rounded small mb-2" style="background: #d1ecf1; border-left: 3px solid #17a2b8;">
                    <strong>Saran:</strong> {{ $fb->saran }}
                </div>
            @endif
            @if($fb->balasan_guru)
                <div class="p-2 rounded small mt-2 bg-white border-start border-3 border-primary shadow-sm">
                    <strong class="text-primary d-block mb-1"><i class="bi bi-reply-fill me-1"></i>Balasan Guru ({{ $guru->nama }}):</strong>
                    <span class="text-dark">{{ $fb->balasan_guru }}</span>
                </div>
            @endif
        </div>
        @endforeach
    @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-chat-square-dots fs-1 d-block mb-3 opacity-50"></i>
            <p class="mb-0">Belum ada ulasan dari siswa lain untuk guru ini.</p>
            <small>Jadilah yang pertama memberikan kritik dan saran!</small>
        </div>
    @endif
</div>
@endsection