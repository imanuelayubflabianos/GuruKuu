@extends('layouts.admin')
@section('title', 'Laporan & Feedback')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">ANALISIS DATA</div>
        <h1 class="page-title">Laporan & Feedback Siswa</h1>
        <p class="page-subtitle">Ringkasan data penilaian & umpan balik {{ $periodeAktif?->nama_periode ?? '-' }}</p>
    </div>
</div>

{{-- STATISTIK CEPAT --}}
<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="stat-card"><div class="stat-card-label">Total Guru</div><div class="stat-card-value">{{ $totalGuru }}</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-card-label">Total Siswa</div><div class="stat-card-value">{{ $totalSiswa }}</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-card-label">Total Penilaian</div><div class="stat-card-value">{{ $totalPenilaian }}</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-card-label">Rata-rata Umum</div><div class="stat-card-value" style="color: var(--secondary);"><i class="bi bi-star-fill"></i> {{ $rataRataUmum }}</div></div></div>
</div>

<div class="row g-4">
    {{-- DISTRIBUTI BINTANG --}}
    <div class="col-lg-5">
        <div class="card-custom p-4 h-100">
            <h5 class="fw-bold mb-4"><i class="bi bi-star-fill me-2 text-warning"></i>Distribusi Rating</h5>
            @foreach([5,4,3,2,1] as $bintang)
                @php
                    $jumlah = $distribusiBintang[$bintang];
                    $persen = round(($jumlah / $totalUntukPersen) * 100, 1);
                    $warna = $bintang >= 4 ? '#22c55e' : ($bintang == 3 ? '#f59e0b' : '#ef4444');
                @endphp
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-bold">
                            @for($i = 1; $i <= 5; $i++) <i class="bi bi-star-fill" style="color: {{ $i <= $bintang ? '#FFC107' : '#e2e8f0' }}; font-size: 0.9rem;"></i> @endfor
                            <span class="ms-2 text-muted small">({{ $bintang }})</span>
                        </span>
                        <span class="fw-bold">{{ $jumlah }} <span class="text-muted small">({{ $persen }}%)</span></span>
                    </div>
                    <div class="progress" style="height: 10px; border-radius: 5px;">
                        <div class="progress-bar" style="width: {{ $persen }}%; background: {{ $warna }}; border-radius: 5px;"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- FEEDBACK / KRITIK SARAN --}}
    <div class="col-lg-7">
        <div class="card-custom p-4 h-100">
            <h5 class="fw-bold mb-4"><i class="bi bi-chat-dots-fill me-2 text-primary"></i>Kritik & Saran Siswa</h5>
            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                <table class="table table-custom mb-0" id="feedbackTable">
                    <thead>
                        <tr>
                            <th>GURU</th>
                            <th>FEEDBACK</th>
                            <th>STATUS</th>
                            <th>TANGGAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($feedbacks as $f)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $f->guru->photo_url }}" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                    <strong class="small">{{ $f->guru->nama }}</strong>
                                </div>
                            </td>
                            <td style="max-width: 300px;">
                                @if($f->kritik)<div class="mb-1 small p-1 rounded" style="background:#fef3c7;"><strong>K:</strong> {{ Str::limit($f->kritik, 60) }}</div>@endif
                                @if($f->saran)<div class="small p-1 rounded" style="background:#dbeafe;"><strong>S:</strong> {{ Str::limit($f->saran, 60) }}</div>@endif
                            </td>
                            <td>
                                @if($f->isToxic()) <span class="badge bg-danger">TOXIC</span>
                                @else <span class="badge bg-success">AMAN</span> @endif
                            </td>
                            <td class="small text-muted">{{ $f->created_at->format('d M Y') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada feedback.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#feedbackTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
        searching: true, // ✅ KOLOM SEARCH AKTIF
        order: [[3, 'desc']],
        pageLength: 10
    });
});
</script>
@endpush