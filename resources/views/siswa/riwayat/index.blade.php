@extends('layouts.siswa')
@section('title', 'Riwayat Penilaian')

@section('content')
<style>
.card-custom {
    background: #ffffff;
    border-radius: 16px !important;
    border: 1px solid rgba(0, 51, 102, 0.09) !important;
    box-shadow: 0 10px 28px -4px rgba(0, 51, 102, 0.10), 0 3px 8px -1px rgba(0, 0, 0, 0.04) !important;
    transition: transform 0.22s ease, box-shadow 0.22s ease;
}
.card-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 36px -4px rgba(0, 51, 102, 0.14), 0 6px 12px rgba(0, 0, 0, 0.05) !important;
}
</style>

<div class="page-header">
    <div>
        <h1 class="page-title mb-1">Riwayat Penilaian Anda</h1>
        <p class="page-subtitle mb-0">Daftar guru yang telah Anda nilai. Anda dapat menghapusnya jika diperlukan.</p>
    </div>
</div>

<div class="row g-4">
    @forelse($riwayat as $item)
    <div class="col-md-6 col-lg-4">
        <div class="card-custom p-4 h-100 position-relative">
            {{-- Tombol Menu Titik Tiga --}}
            <div class="dropdown position-absolute top-0 end-0 p-3">
                <button class="btn btn-sm btn-light border shadow-xs rounded-circle d-inline-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 32px; height: 32px;" title="Pilihan Aksi">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-1" style="border-radius: 12px; font-size: 0.85rem; min-width: 170px; box-shadow: 0 10px 25px rgba(0,0,0,0.12) !important;">
                    <li>
                        <form action="{{ route('siswa.riwayat.destroy', $item->id) }}" method="POST" data-confirm="Yakin ingin menghapus riwayat penilaian ini? Tindakan ini tidak dapat dibatalkan." data-confirm-title="Hapus Riwayat Penilaian?" data-confirm-btn="Ya, Hapus" data-confirm-type="danger">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2 text-danger">
                                <i class="bi bi-trash text-danger"></i>
                                <span>Hapus Riwayat</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </div>

            <div class="d-flex align-items-center gap-3 mb-3">
                <img src="{{ $item->guru->photo_url }}" class="rounded-circle flex-shrink-0" style="width: 60px; height: 60px; min-width: 60px; min-height: 60px; aspect-ratio: 1 / 1; object-fit: cover; flex-shrink: 0;">
                <div>
                    <h6 class="fw-bold mb-1">{{ $item->guru->nama }}</h6>
                    <span class="badge bg-secondary small">{{ $item->guru->kategori }}</span>
                </div>
            </div>

            @php
                $score = $item->rata_rata_evaluasi ?? ($item->total_nilai / 5);
                $pct = round(($score / 5) * 100);
                $stars = round($score * 2) / 2;
                $pctBadge = $pct >= 80 ? 'bg-success' : ($pct >= 60 ? 'bg-info' : ($pct >= 40 ? 'bg-warning' : 'bg-danger'));
            @endphp
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1.5 flex-wrap gap-1">
                    <div class="d-flex align-items-center gap-1.5">
                        <small class="text-muted fw-semibold me-1">Tingkat Kepuasan</small>
                        <span class="text-warning d-inline-flex align-items-center" style="font-size: 0.85rem; letter-spacing: 0.5px;" title="{{ number_format($score, 2) }} / 5.0">
                            @for($i = 1; $i <= 5; $i++)
                                @if($stars >= $i)
                                    <i class="bi bi-star-fill"></i>
                                @elseif($stars >= ($i - 0.5))
                                    <i class="bi bi-star-half"></i>
                                @else
                                    <i class="bi bi-star text-muted opacity-25"></i>
                                @endif
                            @endfor
                        </span>
                        <span class="fw-bold font-mono text-dark ms-1" style="font-size: 0.82rem;">{{ number_format($score, 1) }}</span>
                    </div>
                    <span class="badge {{ $pctBadge }} font-mono">{{ $pct }}% ({{ $item->total_nilai }}/25)</span>
                </div>
                <div class="progress mb-2" style="height: 6px; border-radius: 4px; background-color: #e9ecef;">
                    <div class="progress-bar {{ $pctBadge }} rounded-pill" style="width: {{ $pct }}%;"></div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted font-mono" style="font-size: 0.72rem;">Periode: {{ $item->periode->nama_periode }}</small>
                    <small class="text-muted font-mono" style="font-size: 0.72rem;">{{ $item->created_at->diffForHumans() }}</small>
                </div>
            </div>

            {{-- Rincian 5 Aspek Bintang (Collapsible) --}}
            <div class="mb-3">
                <button class="btn btn-sm btn-light border w-100 py-1 px-2.5 d-flex justify-content-between align-items-center text-muted" type="button" data-bs-toggle="collapse" data-bs-target="#rincian-{{ $item->id }}" aria-expanded="false" style="font-size: 0.74rem; border-radius: 8px; background-color: #f8fafc;">
                    <span class="d-inline-flex align-items-center gap-1">
                        <i class="bi bi-star-fill text-warning"></i>
                        <span class="fw-semibold text-secondary">Rincian Bintang Per Aspek</span>
                    </span>
                    <i class="bi bi-chevron-down small"></i>
                </button>
                <div class="collapse mt-2" id="rincian-{{ $item->id }}">
                    <div class="p-2 rounded border bg-light" style="font-size: 0.7rem; border-color: #e2e8f0 !important;">
                        <div class="row g-1 text-center">
                            @php
                                $aspects = [
                                    'Ketepatan' => $item->kedisiplinan,
                                    'Kehadiran' => $item->komunikasi,
                                    'Materi' => $item->tanggung_jawab,
                                    'Interaksi' => $item->kreativitas,
                                    'Suasana' => $item->keramahan,
                                ];
                            @endphp
                            @foreach($aspects as $lbl => $val)
                            <div class="col">
                                <div class="bg-white p-1 rounded border">
                                    <div class="text-muted text-truncate fw-medium" style="font-size: 0.65rem;" title="{{ $lbl }}">{{ $lbl }}</div>
                                    <div class="text-warning d-flex align-items-center justify-content-center gap-0.5 mt-0.5">
                                        <i class="bi bi-star-fill" style="font-size: 0.62rem;"></i>
                                        <span class="fw-bold font-mono text-dark" style="font-size: 0.72rem;">{{ $val ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
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