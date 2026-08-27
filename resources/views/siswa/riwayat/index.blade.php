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

@if(session('success'))
    <div class="alert alert-success border-0 mb-4"><i class="bi bi-check-circle me-2"></i> {{ session('success') }}</div>
@endif

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

            <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted">Total Nilai</small>
                    <strong style="color: var(--secondary);">{{ number_format($item->total_nilai / 6, 1) }} / 5.0</strong>
                </div>
                <small class="text-muted d-block">Periode: {{ $item->periode->nama_periode }}</small>
            </div>

            @if($item->kritik || $item->saran)
                <div class="p-2 rounded small" style="background: var(--bg-light);">
                    @if($item->kritik)<div class="mb-1"><strong>Kritik:</strong> {{ Str::limit($item->kritik, 50) }}</div>@endif
                    @if($item->saran)<div><strong>Saran:</strong> {{ Str::limit($item->saran, 50) }}</div>@endif
                </div>
            @endif
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