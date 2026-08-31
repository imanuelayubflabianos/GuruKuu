@extends('layouts.siswa')
@section('title', 'Leaderboard')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">PERSENTASE PARTISIPASI</div>
        <h1 class="page-title">Leaderboard</h1>
        <p class="page-subtitle">Persentase siswa yang memberikan evaluasi kepada guru</p>
    </div>
</div>

{{-- FILTER --}}
<div class="card-custom p-4 mb-4">
    <form method="GET" action="{{ route('siswa.leaderboard.index') }}" class="row g-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label font-mono small fw-bold text-muted">KATEGORI</label>
            <select name="kategori" class="form-select" style="border-radius: 8px;">
                <option value="semua" {{ $filterKategori == 'semua' ? 'selected' : '' }}>Semua Guru</option>
                <option value="normada" {{ $filterKategori == 'normada' ? 'selected' : '' }}>Guru Normada</option>
                <option value="produktif" {{ $filterKategori == 'produktif' ? 'selected' : '' }}>Guru Produktif</option>
            </select>
        </div>
        <div class="col-md-5">
            <label class="form-label font-mono small fw-bold text-muted">KELAS</label>
            <select name="kelas_id" class="form-select" style="border-radius: 8px;">
                <option value="">Semua Kelas</option>
                @foreach($semuaKelas as $kelas)
                    <option value="{{ $kelas->id }}" {{ $filterKelasId == $kelas->id ? 'selected' : '' }}>
                        Tingkat {{ $kelas->tingkat }} - {{ $kelas->nama_kelas }} ({{ $kelas->jurusan->nama_jurusan }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary-custom w-100">
                <i class="bi bi-funnel me-1"></i> Filter
            </button>
        </div>
    </form>
</div>

{{-- TOP 3 PODIUM --}}
@if($top3->count() >= 3)
@php
    $top1 = $top3[0];
    $top2 = $top3[1];
    $top3guru = $top3[2];
@endphp
<div class="row g-4 mb-5 align-items-end justify-content-center">
    {{-- TOP 2 --}}
    <div class="col-md-3 order-md-1">
        <div class="card-custom p-4 text-center h-100" style="border-top: 4px solid #C0C0C0;">
            <div class="position-relative d-inline-block mb-3">
                <img src="{{ $top2->photo_url }}" class="rounded-circle" style="width: 90px; height: 90px; object-fit: cover; border: 4px solid #C0C0C0;">
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background: #C0C0C0; color: #000; font-size: 0.9rem; padding: 0.5rem 0.75rem;">🥈 #2</span>
            </div>
            <h6 class="fw-bold mb-3">{{ $top2->nama }}</h6>
            
            {{-- Progress Bar --}}
            <div class="mb-2">
                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted">Partisipasi</small>
                    <small class="fw-bold">{{ number_format($top2->persentase, 1) }}%</small>
                </div>
                <div class="progress" style="height: 8px; border-radius: 4px;">
                    <div class="progress-bar" style="width: {{ $top2->persentase }}%; background: #C0C0C0;"></div>
                </div>
                <small class="text-muted mt-1">{{ $top2->jumlah_siswa }} dari {{ $top2->total_siswa }} siswa</small>
            </div>
        </div>
    </div>

    {{-- TOP 1 --}}
    <div class="col-md-4 order-md-2 mt-md-4">
        <div class="card-custom p-5 text-center h-100" style="background: var(--primary); color: white; border-top: 4px solid #FFD700;">
            <div class="position-relative d-inline-block mb-3">
                <img src="{{ $top1->photo_url }}" class="rounded-circle" style="width: 130px; height: 130px; object-fit: cover; border: 5px solid #FFD700;">
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background: #FFD700; color: #000; font-size: 1.1rem; padding: 0.6rem 0.9rem;">🏆 #1</span>
            </div>
            <h4 class="fw-bold mb-3">{{ $top1->nama }}</h4>
            
            {{-- Progress Bar --}}
            <div class="mb-2">
                <div class="d-flex justify-content-between mb-1">
                    <small style="opacity: 0.85;">Partisipasi</small>
                    <small class="fw-bold">{{ number_format($top1->persentase, 1) }}%</small>
                </div>
                <div class="progress" style="height: 10px; border-radius: 5px; background: rgba(255,255,255,0.2);">
                    <div class="progress-bar" style="width: {{ $top1->persentase }}%; background: #FFD700;"></div>
                </div>
                <small style="opacity: 0.85;" class="mt-1">{{ $top1->jumlah_siswa }} dari {{ $top1->total_siswa }} siswa</small>
            </div>
        </div>
    </div>

    {{-- TOP 3 --}}
    <div class="col-md-3 order-md-3">
        <div class="card-custom p-4 text-center h-100" style="border-top: 4px solid #CD7F32;">
            <div class="position-relative d-inline-block mb-3">
                <img src="{{ $top3guru->photo_url }}" class="rounded-circle" style="width: 90px; height: 90px; object-fit: cover; border: 4px solid #CD7F32;">
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background: #CD7F32; color: #fff; font-size: 0.9rem; padding: 0.5rem 0.75rem;">🥉 #3</span>
            </div>
            <h6 class="fw-bold mb-3">{{ $top3guru->nama }}</h6>
            
            {{-- Progress Bar --}}
            <div class="mb-2">
                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted">Partisipasi</small>
                    <small class="fw-bold">{{ number_format($top3guru->persentase, 1) }}%</small>
                </div>
                <div class="progress" style="height: 8px; border-radius: 4px;">
                    <div class="progress-bar" style="width: {{ $top3guru->persentase }}%; background: #CD7F32;"></div>
                </div>
                <small class="text-muted mt-1">{{ $top3guru->jumlah_siswa }} dari {{ $top3guru->total_siswa }} siswa</small>
            </div>
        </div>
    </div>
</div>
@endif

{{-- DAFTAR LENGKAP --}}
@if($rankedGuru->count() > 3)
<div class="card-custom p-4">
    <h5 class="fw-bold mb-4"><i class="bi bi-list-ol me-2"></i>Peringkat Lengkap ({{ $rankedGuru->count() }} Guru)</h5>
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th style="width: 80px;">PERINGKAT</th>
                    <th>GURU</th>
                    <th style="width: 300px;">PARTISIPASI</th>
                    <th style="width: 100px;" class="text-center">DETAIL</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rankedGuru as $index => $guru)
                <tr>
                    <td>
                        @if($index < 3)
                            @php $medals = ['🥇', '🥈', '🥉']; @endphp
                            <span class="fs-5">{{ $medals[$index] }}</span>
                        @else
                            <span class="text-muted fw-bold">#{{ $index + 1 }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $guru->photo_url }}" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                            <div>
                                <div class="fw-bold">{{ $guru->nama }}</div>
                                <small class="text-muted">{{ $guru->kategori === 'normada' ? 'Normada' : 'Produktif' }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        {{-- Progress Bar --}}
                        <div class="d-flex align-items-center gap-2">
                            <div class="flex-grow-1">
                                <div class="progress" style="height: 8px; border-radius: 4px;">
                                    <div class="progress-bar" 
                                         style="width: {{ $guru->persentase }}%; background: {{ $guru->persentase >= 80 ? '#22c55e' : ($guru->persentase >= 50 ? '#f59e0b' : '#ef4444') }};">
                                    </div>
                                </div>
                            </div>
                            <span class="fw-bold" style="min-width: 50px; color: {{ $guru->persentase >= 80 ? '#22c55e' : ($guru->persentase >= 50 ? '#f59e0b' : '#ef4444') }};">
                                {{ number_format($guru->persentase, 1) }}%
                            </span>
                        </div>
                    </td>
                    <td class="text-center">
                        <small class="text-muted">{{ $guru->jumlah_siswa }}/{{ $guru->total_siswa }}</small>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@elseif($rankedGuru->count() == 0)
<div class="card-custom p-5 text-center">
    <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
    <p class="text-muted mb-0">Tidak ada data guru untuk filter yang dipilih.</p>
</div>
@endif
@endsection