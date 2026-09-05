@extends('layouts.siswa')
@section('title', 'Leaderboard')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">PERINGKAT GURU</div>
        <h1 class="page-title">Leaderboard Partisipasi Penilaian</h1>
        <p class="page-subtitle">Peringkat guru berdasarkan persentase partisipasi evaluasi dari siswa.</p>
    </div>
</div>

<div class="card-custom p-3 mb-4">
    <form method="GET" action="{{ route('siswa.leaderboard.index') }}" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label small fw-bold text-muted">Kategori</label>
            <select name="kategori" class="form-select form-select-sm">
                <option value="semua" {{ $filterKategori == 'semua' ? 'selected' : '' }}>Semua Kategori</option>
                <option value="normada" {{ $filterKategori == 'normada' ? 'selected' : '' }}>Normada</option>
                <option value="produktif" {{ $filterKategori == 'produktif' ? 'selected' : '' }}>Produktif</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold text-muted">Kelas</label>
            <select name="kelas_id" class="form-select form-select-sm">
                <option value="">Semua Kelas</option>
                @foreach($semuaKelas as $kelas)
                    <option value="{{ $kelas->id }}" {{ $filterKelasId == $kelas->id ? 'selected' : '' }}>
                        {{ $kelas->nama_kelas }} (Tingkat {{ $kelas->tingkat }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary-custom btn-sm flex-grow-1"><i class="bi bi-funnel"></i> Filter</button>
            <a href="{{ route('siswa.leaderboard.index') }}" class="btn btn-outline-custom btn-sm" title="Reset Filter"><i class="bi bi-arrow-counterclockwise"></i></a>
        </div>
    </form>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom mb-0" id="leaderboardTable">
            <thead>
                <tr>
                    <th class="text-center" style="width: 70px;">PERINGKAT</th>
                    <th>NAMA GURU</th>
                    <th>KATEGORI</th>
                    <th>PARTISIPASI SISWA</th>
                    <th>PROGRESS</th>
                    <th class="text-center" style="width: 150px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rankedGuru as $index => $guru)
                <tr>
                    <td class="text-center fw-bold">
                        @if($index == 0)
                            <span class="fs-5">🥇</span> <span class="badge bg-warning text-dark">#1</span>
                        @elseif($index == 1)
                            <span class="fs-5">🥈</span> <span class="badge bg-secondary text-white">#2</span>
                        @elseif($index == 2)
                            <span class="fs-5">🥉</span> <span class="badge bg-danger bg-opacity-75 text-white">#3</span>
                        @else
                            <span class="badge bg-light text-dark border">#{{ $index + 1 }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $guru->photo_url }}" class="rounded-circle border" style="width: 42px; height: 42px; object-fit: cover;">
                            <div>
                                <div class="fw-bold text-dark">{{ $guru->nama }}</div>
                                <small class="text-muted">{{ $guru->jurusan?->nama_jurusan ?? 'Umum / Normada' }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-{{ $guru->kategori === 'normada' ? 'info' : 'success' }} bg-opacity-10 text-{{ $guru->kategori === 'normada' ? 'info' : 'success' }} border border-{{ $guru->kategori === 'normada' ? 'info' : 'success' }}-subtle px-2 py-1">
                            {{ ucfirst($guru->kategori) }}
                        </span>
                    </td>
                    <td>
                        <div class="fw-bold text-dark">{{ $guru->jumlah_siswa ?? 0 }} / {{ $guru->total_siswa ?? 0 }} Siswa</div>
                        <small class="text-muted">Partisipasi Kelas</small>
                    </td>
                    <td>
                        @php
                            $p = $guru->persentase ?? 0;
                            $warna = $p >= 70 ? '#22c55e' : ($p >= 40 ? '#f59e0b' : '#ef4444');
                        @endphp
                        <div class="d-flex align-items-center gap-2" style="min-width: 140px;">
                            <div class="progress flex-grow-1" style="height: 8px;">
                                <div class="progress-bar" style="width: {{ $p }}%; background: {{ $warna }};"></div>
                            </div>
                            <span class="fw-bold small" style="color: {{ $warna }}; min-width: 45px;">{{ number_format($p, 1) }}%</span>
                        </div>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('siswa.guru.show', $guru->id) }}" class="btn btn-sm btn-primary-custom d-inline-flex align-items-center gap-1">
                            <i class="bi bi-eye"></i> Lihat Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        Tidak ada data guru untuk kriteria filter ini.
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
    $('#leaderboardTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
        ordering: false,
        pageLength: 10
    });
});
</script>
@endpush