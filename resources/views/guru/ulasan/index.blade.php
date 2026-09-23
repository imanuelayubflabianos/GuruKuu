@extends('layouts.guru')
@section('title', 'Ulasan Siswa')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">EVALUASI & ASPIRASI SISWA</div>
        <h1 class="page-title">Riwayat Ulasan Siswa</h1>
        <p class="page-subtitle">Daftar lengkap kritik, saran, dan evaluasi pengajaran dari siswa. Anda dapat menanggapi ulasan secara profesional.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('guru.dashboard') }}" class="btn btn-outline-custom">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>
</div>

{{-- STATS SUMMARY WIDGET --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card-custom p-3 d-flex align-items-center gap-3">
            <div class="rounded-3 p-3 text-primary" style="background: rgba(0, 51, 102, 0.08); font-size: 1.75rem;">
                <i class="bi bi-chat-quote-fill"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold text-uppercase font-mono">Total Ulasan Masuk</div>
                <h3 class="fw-bold mb-0 text-dark">{{ $totalUlasan }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom p-3 d-flex align-items-center gap-3">
            <div class="rounded-3 p-3 text-success" style="background: rgba(16, 185, 129, 0.1); font-size: 1.75rem;">
                <i class="bi bi-check2-circle"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold text-uppercase font-mono">Sudah Dibalas</div>
                <h3 class="fw-bold mb-0 text-success">{{ $totalDibalas }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom p-3 d-flex align-items-center gap-3">
            <div class="rounded-3 p-3 text-warning" style="background: rgba(245, 158, 11, 0.1); font-size: 1.75rem;">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div>
                <div class="text-muted small fw-semibold text-uppercase font-mono">Belum Dibalas</div>
                <h3 class="fw-bold mb-0 text-warning">{{ $totalBelumDibalas }}</h3>
            </div>
        </div>
    </div>
</div>

{{-- FILTER BAR --}}
<div class="card-custom p-3 mb-4">
    <form method="GET" action="{{ route('guru.ulasan') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-bold text-muted">Filter Status Balasan</label>
            <select name="status" class="form-select form-select-sm" style="border-radius: 8px;" onchange="this.form.submit()">
                <option value="">Semua Status ({{ $totalUlasan }})</option>
                <option value="belum" {{ request('status') === 'belum' ? 'selected' : '' }}>Belum Dibalas ({{ $totalBelumDibalas }})</option>
                <option value="dibalas" {{ request('status') === 'dibalas' ? 'selected' : '' }}>Sudah Dibalas ({{ $totalDibalas }})</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold text-muted">Filter Periode Semester</label>
            <select name="periode_id" class="form-select form-select-sm" style="border-radius: 8px;" onchange="this.form.submit()">
                <option value="">Semua Periode</option>
                @foreach($semuaPeriode as $p)
                    <option value="{{ $p->id }}" {{ request('periode_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->nama_periode }} {{ $p->status === 'aktif' ? '(Aktif)' : '' }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <a href="{{ route('guru.ulasan') }}" class="btn btn-sm btn-outline-custom w-100">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
            </a>
        </div>
    </form>
</div>

{{-- DAFTAR ULASAN --}}
<div class="row g-3">
    @forelse($semuaUlasan as $review)
    <div class="col-12">
        <div class="card-custom p-4 position-relative">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3 pb-2 border-bottom">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-secondary-subtle text-secondary border px-2 py-1" style="font-size: 0.75rem;">
                        <i class="bi bi-shield-lock-fill me-1"></i>Siswa (Anonim)
                    </span>
                    @if($review->kelas)
                        <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.75rem;">
                            {{ $review->kelas->nama_kelas }} Kelas {{ $review->kelas->tingkat }}
                        </span>
                    @endif
                    @if($review->periode)
                        <span class="badge bg-primary-subtle text-primary border px-2 py-1" style="font-size: 0.75rem;">
                            {{ $review->periode->nama_periode }}
                        </span>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-2">
                    <small class="text-muted font-mono" style="font-size: 0.75rem;">
                        <i class="bi bi-clock me-1"></i>{{ $review->created_at->format('d M Y, H:i') }} WIB
                    </small>
                </div>
            </div>

            {{-- DETAIL SKOR 6 ASPEK --}}
            <div class="mb-3 p-3 rounded bg-light border">
                <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                    @php $revPct = round(($review->rata_rata_evaluasi / 5) * 100); @endphp
                    <div class="d-flex align-items-center gap-3">
                        <span class="fw-bold text-primary font-mono" style="font-size: 1.2rem;">
                            {{ $revPct }}%
                        </span>
                        <div class="progress" style="height: 7px; width: 140px; border-radius: 10px;">
                            <div class="progress-bar bg-primary rounded-pill" style="width: {{ $revPct }}%;"></div>
                        </div>
                    </div>
                    <span class="badge bg-white text-dark border font-mono">
                        Skor Total: {{ $review->total_nilai }} / 25 ({{ $revPct }}%)
                    </span>
                </div>
                <div class="row g-2 text-center" style="font-size: 0.78rem;">
                    <div class="col-4 col-md">
                        <div class="p-1 bg-white rounded border">
                            <span class="text-muted d-block">Disiplin</span>
                            <strong class="text-primary">{{ $review->kedisiplinan }}/5</strong>
                        </div>
                    </div>
                    <div class="col-4 col-md">
                        <div class="p-1 bg-white rounded border">
                            <span class="text-muted d-block">Komunikasi</span>
                            <strong class="text-primary">{{ $review->komunikasi }}/5</strong>
                        </div>
                    </div>
                    <div class="col-4 col-md">
                        <div class="p-1 bg-white rounded border">
                            <span class="text-muted d-block">Tanggung Jwb</span>
                            <strong class="text-primary">{{ $review->tanggung_jawab }}/5</strong>
                        </div>
                    </div>
                    <div class="col-4 col-md">
                        <div class="p-1 bg-white rounded border">
                            <span class="text-muted d-block">Kreativitas</span>
                            <strong class="text-primary">{{ $review->kreativitas }}/5</strong>
                        </div>
                    </div>
                    <div class="col-4 col-md">
                        <div class="p-1 bg-white rounded border">
                            <span class="text-muted d-block">Keramahan</span>
                            <strong class="text-primary">{{ $review->keramahan }}/5</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ISI KRITIK & SARAN --}}
            @if($review->is_censored)
                <div class="p-3 rounded mb-3 bg-light border text-muted fst-italic">
                    <i class="bi bi-shield-exclamation text-warning me-1"></i> Ulasan ini disembunyikan oleh Administrator karena tidak memenuhi kriteria etika dan kebijakan komunitas.
                </div>
            @else
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded h-100" style="background: #fff5f5; border-left: 4px solid #ef4444;">
                            <strong class="text-danger small d-flex align-items-center gap-1 mb-1">
                                <i class="bi bi-chat-left-dots-fill"></i> Kritik / Catatan Siswa:
                            </strong>
                            <p class="mb-0 text-dark small" style="line-height: 1.6;">
                                {{ $review->kritik ?: 'Tidak ada kritik tertulis.' }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded h-100" style="background: #f0fdf4; border-left: 4px solid #22c55e;">
                            <strong class="text-success small d-flex align-items-center gap-1 mb-1">
                                <i class="bi bi-lightbulb-fill"></i> Saran & Harapan Siswa:
                            </strong>
                            <p class="mb-0 text-dark small" style="line-height: 1.6;">
                                {{ $review->saran ?: 'Tidak ada saran tertulis.' }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- THREAD DISKUSI / BALASAN ULASAN BERTINGKAT --}}
            <x-penilaian-thread :penilaian="$review" />
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card-custom p-5 text-center text-muted">
            <i class="bi bi-chat-square-quote text-muted opacity-50" style="font-size: 3rem;"></i>
            <h5 class="fw-bold mt-3 mb-1">Belum Ada Ulasan</h5>
            <p class="small text-muted mb-0">Tidak ada ulasan siswa yang cocok dengan kriteria filter saat ini.</p>
        </div>
    </div>
    @endforelse
</div>

{{-- PAGINATION --}}
@if($semuaUlasan->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $semuaUlasan->links() }}
    </div>
@endif
@endsection
