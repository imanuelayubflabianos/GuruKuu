@extends('layouts.guru')
@section('title', 'Ulasan Siswa')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title mb-1">Riwayat Ulasan Siswa</h1>
        <p class="page-subtitle mb-0">Daftar lengkap kritik, saran, dan evaluasi pengajaran dari siswa. Anda dapat menanggapi ulasan secara profesional.</p>
    </div>
    <div class="d-none d-md-flex gap-2">
        <a href="{{ route('guru.dashboard') }}" class="gk-btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>
</div>

{{-- STATS SUMMARY WIDGET (3 KOLOM SEJAJAR TIDAK BUANG RUANG & RAPI PROPORSI JARAKNYA) --}}
<div class="row g-2 g-md-3 mb-4">
    <div class="col-4">
        <div class="card-custom p-2.5 p-sm-3 d-flex flex-column flex-md-row align-items-center gap-1.5 gap-md-3 text-center text-md-start h-100 shadow-sm">
            <div class="rounded-3 text-primary flex-shrink-0 d-inline-flex align-items-center justify-content-center mb-1.5 mb-md-0" style="background: rgba(0, 51, 102, 0.08); font-size: clamp(1rem, 2vw, 1.4rem); width: clamp(34px, 8vw, 44px); height: clamp(34px, 8vw, 44px);">
                <i class="bi bi-chat-quote-fill"></i>
            </div>
            <div class="min-w-0 w-100">
                <div class="text-muted fw-semibold text-uppercase font-mono mb-1" style="font-size: clamp(0.55rem, 1.6vw, 0.72rem); line-height: 1.25;">Total Ulasan</div>
                <h3 class="fw-bold mb-0 text-dark" style="font-size: clamp(1.1rem, 2.8vw, 1.6rem); line-height: 1;">{{ $totalUlasan }}</h3>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card-custom p-2.5 p-sm-3 d-flex flex-column flex-md-row align-items-center gap-1.5 gap-md-3 text-center text-md-start h-100 shadow-sm">
            <div class="rounded-3 text-success flex-shrink-0 d-inline-flex align-items-center justify-content-center mb-1.5 mb-md-0" style="background: rgba(16, 185, 129, 0.1); font-size: clamp(1rem, 2vw, 1.4rem); width: clamp(34px, 8vw, 44px); height: clamp(34px, 8vw, 44px);">
                <i class="bi bi-check2-circle"></i>
            </div>
            <div class="min-w-0 w-100">
                <div class="text-muted fw-semibold text-uppercase font-mono mb-1" style="font-size: clamp(0.55rem, 1.6vw, 0.72rem); line-height: 1.25;">Sudah Dibalas</div>
                <h3 class="fw-bold mb-0 text-success" style="font-size: clamp(1.1rem, 2.8vw, 1.6rem); line-height: 1;">{{ $totalDibalas }}</h3>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card-custom p-2.5 p-sm-3 d-flex flex-column flex-md-row align-items-center gap-1.5 gap-md-3 text-center text-md-start h-100 shadow-sm">
            <div class="rounded-3 text-warning flex-shrink-0 d-inline-flex align-items-center justify-content-center mb-1.5 mb-md-0" style="background: rgba(245, 158, 11, 0.1); font-size: clamp(1rem, 2vw, 1.4rem); width: clamp(34px, 8vw, 44px); height: clamp(34px, 8vw, 44px);">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="min-w-0 w-100">
                <div class="text-muted fw-semibold text-uppercase font-mono mb-1" style="font-size: clamp(0.55rem, 1.6vw, 0.72rem); line-height: 1.25;">Belum Dibalas</div>
                <h3 class="fw-bold mb-0 text-warning" style="font-size: clamp(1.1rem, 2.8vw, 1.6rem); line-height: 1;">{{ $totalBelumDibalas }}</h3>
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
        <div id="ulasan-{{ $review->id }}" class="card-custom p-4 position-relative" style="transition: all 0.3s ease;">
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

            {{-- DETAIL SKOR 5 ASPEK --}}
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
                        <div class="p-1.5 bg-white rounded border">
                            <span class="text-muted d-block text-truncate" title="Ketepatan Waktu">Waktu</span>
                            <div class="text-warning mt-0.5 d-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-star-fill" style="font-size: 0.7rem;"></i>
                                <span class="fw-bold font-mono text-dark" style="font-size: 0.76rem;">{{ $review->kedisiplinan }}<span class="text-muted fw-normal" style="font-size: 0.65rem;">/5</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 col-md">
                        <div class="p-1.5 bg-white rounded border">
                            <span class="text-muted d-block text-truncate" title="Kehadiran di Kelas">Kehadiran</span>
                            <div class="text-warning mt-0.5 d-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-star-fill" style="font-size: 0.7rem;"></i>
                                <span class="fw-bold font-mono text-dark" style="font-size: 0.76rem;">{{ $review->tanggung_jawab }}<span class="text-muted fw-normal" style="font-size: 0.65rem;">/5</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 col-md">
                        <div class="p-1.5 bg-white rounded border">
                            <span class="text-muted d-block text-truncate" title="Penyampaian Materi">Materi</span>
                            <div class="text-warning mt-0.5 d-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-star-fill" style="font-size: 0.7rem;"></i>
                                <span class="fw-bold font-mono text-dark" style="font-size: 0.76rem;">{{ $review->komunikasi }}<span class="text-muted fw-normal" style="font-size: 0.65rem;">/5</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 col-md">
                        <div class="p-1.5 bg-white rounded border">
                            <span class="text-muted d-block text-truncate" title="Interaksi dengan Siswa">Interaksi</span>
                            <div class="text-warning mt-0.5 d-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-star-fill" style="font-size: 0.7rem;"></i>
                                <span class="fw-bold font-mono text-dark" style="font-size: 0.76rem;">{{ $review->keramahan }}<span class="text-muted fw-normal" style="font-size: 0.65rem;">/5</span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 col-md">
                        <div class="p-1.5 bg-white rounded border">
                            <span class="text-muted d-block text-truncate" title="Keterlibatan & Suasana Belajar">Suasana</span>
                            <div class="text-warning mt-0.5 d-flex align-items-center justify-content-center gap-1">
                                <i class="bi bi-star-fill" style="font-size: 0.7rem;"></i>
                                <span class="fw-bold font-mono text-dark" style="font-size: 0.76rem;">{{ $review->kreativitas }}<span class="text-muted fw-normal" style="font-size: 0.65rem;">/5</span></span>
                            </div>
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
                        <div class="p-3 rounded h-100" style="background: #fff8e1; border-left: 4px solid #ffc107;">
                            <strong class="text-warning-emphasis small mb-1 d-block">
                                Kritik Membangun:
                            </strong>
                            <p class="mb-0 text-dark small" style="line-height: 1.6;">
                                {{ $review->kritik ?: 'Tidak ada kritik tertulis.' }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded h-100" style="background: #e1f5fe; border-left: 4px solid #0288d1;">
                            <strong class="text-primary small mb-1 d-block">
                                Saran Perbaikan:
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const hash = window.location.hash;
    if (hash && hash.startsWith('#ulasan-')) {
        const target = document.querySelector(hash);
        if (target) {
            setTimeout(() => {
                target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                target.style.transition = 'box-shadow 0.4s ease, border-color 0.4s ease';
                target.style.borderColor = '#003366';
                target.style.boxShadow = '0 0 0 3px rgba(0, 51, 102, 0.25)';

                const idNum = hash.replace('#ulasan-', '');
                const discCollapse = document.getElementById('discussionDetail-' + idNum);
                if (discCollapse && !discCollapse.classList.contains('show')) {
                    const bsDisc = new bootstrap.Collapse(discCollapse, { toggle: true });
                }
            }, 350);
        }
    }
});
</script>
@endpush
@endsection
