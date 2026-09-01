@extends('layouts.siswa')
@section('title', 'Leaderboard')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">PERINGKAT GURU</div>
        <h1 class="page-title">Leaderboard</h1>
    </div>
</div>

<div class="card-custom p-3 mb-4">
    <form method="GET" action="{{ route('siswa.leaderboard.index') }}" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label small fw-bold text-muted">Kategori</label>
            <select name="kategori" class="form-select form-select-sm">
                <option value="semua" {{ $filterKategori == 'semua' ? 'selected' : '' }}>Semua</option>
                <option value="normada" {{ $filterKategori == 'normada' ? 'selected' : '' }}>Normada</option>
                <option value="produktif" {{ $filterKategori == 'produktif' ? 'selected' : '' }}>Produktif</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold text-muted">Kelas</label>
            <select name="kelas_id" class="form-select form-select-sm">
                <option value="">Semua Kelas</option>
                @foreach($semuaKelas as $kelas)
                    <option value="{{ $kelas->id }}" {{ $filterKelasId == $kelas->id ? 'selected' : '' }}>
                        {{ $kelas->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary-custom btn-sm flex-grow-1"><i class="bi bi-funnel"></i> Filter</button>
            <a href="{{ route('siswa.leaderboard.index') }}" class="btn btn-outline-custom btn-sm"><i class="bi bi-arrow-counterclockwise"></i></a>
        </div>
    </form>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom mb-0" id="leaderboardTable">
            <thead>
                <tr>
                    <th style="width: 60px;">#</th>
                    <th>GURU</th>
                    <th>KATEGORI</th>
                    <th>PARTISIPASI</th>
                    <th>DETAIL</th>
                    <th class="text-center" style="width: 100px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rankedGuru as $index => $guru)
                <tr>
                    <td class="text-center fw-bold fs-5">
                        @if($index == 0) 🥇
                        @elseif($index == 1) 🥈
                        @elseif($index == 2) 🥉
                        @else {{ $index + 1 }} @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $guru->photo_url }}" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                            <strong>{{ $guru->nama }}</strong>
                        </div>
                    </td>
                    <td><span class="badge bg-light text-dark border">{{ ucfirst($guru->kategori) }}</span></td>
                    <td>
                        @php
                            $p = $guru->persentase ?? 0;
                            $warna = $p >= 70 ? '#22c55e' : ($p >= 50 ? '#f59e0b' : '#ef4444');
                        @endphp
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height: 8px;">
                                <div class="progress-bar" style="width: {{ $p }}%; background: {{ $warna }};"></div>
                            </div>
                            <span class="fw-bold small" style="color: {{ $warna }}; min-width: 45px;">{{ number_format($p, 1) }}%</span>
                        </div>
                    </td>
                    <td class="small text-muted">{{ $guru->jumlah_siswa ?? 0 }}/{{ $guru->total_siswa ?? 0 }}</td>
                    <td class="text-center">
                        <a href="{{ route('siswa.guru.show', $guru) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye me-1"></i> Lihat
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">Tidak ada data.</td>
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
    $('#leaderboardTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
        ordering: false,
        pageLength: 10
    });
});
</script>
@endpush