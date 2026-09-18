@extends('layouts.siswa')
@section('title', 'Riwayat Penilaian')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">RIWAYAT</div>
        <h1 class="page-title">Riwayat Penilaian Anda</h1>
        <p class="page-subtitle">Daftar guru yang telah Anda nilai. Anda dapat menghapusnya jika diperlukan.</p>
    </div>
</div>

<div class="row g-4">
    @forelse($riwayat as $item)
    <div class="col-md-6 col-lg-4">
        <div class="card-custom p-4 h-100 position-relative">
            {{-- Tombol Hapus di Pojok Kanan Atas --}}
            <form action="{{ route('siswa.riwayat.destroy', $item->id) }}" method="POST" class="position-absolute top-0 end-0 p-3" onsubmit="return confirm('Yakin ingin menghapus riwayat penilaian ini? Tindakan ini tidak dapat dibatalkan.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Hapus Riwayat">
                    <i class="bi bi-trash-fill"></i>
                </button>
            </form>

            <div class="d-flex align-items-center gap-3 mb-3">
                <img src="{{ $item->guru->photo_url }}" class="rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">
                <div>
                    <h6 class="fw-bold mb-1">{{ $item->guru->nama }}</h6>
                    <span class="badge bg-secondary small">{{ $item->guru->kategori }}</span>
                </div>
            </div>

            @php
                $pct = round(($item->total_nilai / 30) * 100);
                $pctBadge = $pct >= 80 ? 'bg-success' : ($pct >= 60 ? 'bg-info' : ($pct >= 40 ? 'bg-warning' : 'bg-danger'));
            @endphp
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small class="text-muted fw-semibold">Tingkat Kepuasan</small>
                    <span class="badge {{ $pctBadge }} font-mono">{{ $pct }}% ({{ $item->total_nilai }}/30)</span>
                </div>
                <div class="progress mb-2" style="height: 6px; border-radius: 4px; background-color: #e9ecef;">
                    <div class="progress-bar {{ $pctBadge }} rounded-pill" style="width: {{ $pct }}%;"></div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted font-mono" style="font-size: 0.72rem;">Periode: {{ $item->periode->nama_periode }}</small>
                    <small class="text-muted font-mono" style="font-size: 0.72rem;">{{ $item->created_at->diffForHumans() }}</small>
                </div>
            </div>

            @if($item->kritik || $item->saran)
                <div class="p-2 rounded small mb-3" style="background: var(--bg-light);">
                    @if($item->kritik)<div class="mb-1"><strong>Kritik:</strong> {{ Str::limit($item->kritik, 60) }}</div>@endif
                    @if($item->saran)<div><strong>Saran:</strong> {{ Str::limit($item->saran, 60) }}</div>@endif
                </div>
            @endif

            <a href="{{ route('siswa.guru.show', $item->guru->id) }}" class="btn btn-sm btn-outline-custom w-100 rounded-pill">
                <i class="bi bi-eye me-1"></i> Lihat Detail Guru
            </a>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info text-center">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            Anda belum memberikan penilaian apapun.
        </div>
    </div>
    @endforelse
</div>
@endsection