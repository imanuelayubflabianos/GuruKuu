{{-- resources/views/siswa/penilaian/riwayat.blade.php --}}
@extends('layouts.siswa')
@section('title', 'Riwayat Penilaian')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">ARSIP PENILAIAN</div>
        <h1 class="page-title">Riwayat Penilaian Saya</h1>
        <p class="page-subtitle">Daftar seluruh penilaian yang telah Anda berikan kepada guru.</p>
    </div>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom mb-0" id="riwayatTable">
            <thead>
                <tr>
                    <th>GURU</th>
                    <th>KATEGORI</th>
                    <th>PERIODE</th>
                    <th class="text-center">TOTAL NILAI</th>
                    <th class="text-center">TANGGAL</th>
                    <th class="text-center">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat as $r)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="{{ $r->guru->photo_url }}" class="rounded-circle me-3 flex-shrink-0" width="48" height="48" style="width: 48px; height: 48px; min-width: 48px; min-height: 48px; aspect-ratio: 1 / 1; object-fit: cover; flex-shrink: 0;">
                            <div>
                                <strong>{{ $r->guru->nama }}</strong>
                                <div class="font-mono" style="font-size: 0.7rem; color: var(--text-muted); letter-spacing: 1px;">
                                    {{ strtoupper($r->guru->jurusan?->nama_jurusan ?? 'UMUM') }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge-custom" style="background: {{ $r->guru->kategori === 'normada' ? 'rgba(0,51,102,0.1)' : 'rgba(0,168,107,0.1)' }}; color: {{ $r->guru->kategori === 'normada' ? 'var(--primary)' : 'var(--accent)' }};">
                            {{ strtoupper($r->guru->kategori_label) }}
                        </span>
                    </td>
                    <td>
                        <div class="fw-bold">{{ $r->periode->nama_periode }}</div>
                        <small class="text-muted font-mono" style="font-size: 0.7rem;">
                            {{ $r->periode->semester === 'ganjil' ? '🍂 Ganjil' : '🌸 Genap' }} {{ $r->periode->tahun_ajaran }}
                        </small>
                    </td>
                    <td class="text-center">
                        @php 
                            $pct = round(($r->rata_rata_evaluasi / 5) * 100); 
                            $rStars = round(($r->rata_rata_evaluasi ?? ($r->total_nilai / 5)) * 2) / 2;
                        @endphp
                        <div class="text-warning mb-1" style="font-size: 0.76rem; letter-spacing: 0.5px;">
                            @for($i = 1; $i <= 5; $i++)
                                @if($rStars >= $i)
                                    <i class="bi bi-star-fill"></i>
                                @elseif($rStars >= ($i - 0.5))
                                    <i class="bi bi-star-half"></i>
                                @else
                                    <i class="bi bi-star text-muted opacity-25"></i>
                                @endif
                            @endfor
                        </div>
                        <div class="fw-bold font-mono text-primary mb-1">{{ $pct }}%</div>
                        <div class="progress mx-auto" style="height: 6px; width: 80px; border-radius: 10px;">
                            <div class="progress-bar bg-primary rounded-pill" style="width: {{ $pct }}%;"></div>
                        </div>
                        <small class="text-muted font-mono" style="font-size: 0.7rem;">({{ $r->total_nilai }}/25)</small>
                    </td>
                    <td class="text-center">
                        <div class="font-mono" style="font-size: 0.8rem;">
                            {{ $r->created_at->format('d M Y') }}
                        </div>
                        <small class="text-muted">
                            {{ $r->created_at->diffForHumans() }}
                        </small>
                    </td>
                    <td class="text-center">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border shadow-xs rounded-circle d-inline-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 32px; height: 32px;" title="Pilihan Aksi">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-1" style="border-radius: 12px; font-size: 0.85rem; min-width: 170px; box-shadow: 0 10px 25px rgba(0,0,0,0.12) !important;">
                                <li>
                                    <form action="{{ route('siswa.riwayat.destroy', $r) }}" method="POST" data-confirm="Hapus riwayat penilaian ini? Rating guru akan dihitung ulang dan Anda dapat menilai kembali." data-confirm-title="Hapus Riwayat Penilaian?" data-confirm-btn="Ya, Hapus" data-confirm-type="danger">
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
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                        <p class="text-muted mb-0">Anda belum memberikan penilaian apapun.</p>
                        <a href="{{ route('siswa.guru.index') }}" class="btn btn-primary-custom mt-3">
                            <i class="bi bi-pencil"></i> Mulai Beri Penilaian
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#riwayatTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
        order: [[4, 'desc']], // Urutkan berdasarkan tanggal terbaru
        pageLength: 10,
        responsive: true
    });
});
</script>
@endpush