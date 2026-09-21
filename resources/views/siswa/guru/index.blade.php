@extends('layouts.siswa')
@section('title', 'Daftar Guru')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <div class="page-label">EVALUASI GURU</div>
        <h1 class="page-title">Daftar Guru</h1>
        <p class="page-subtitle mb-0">
            Pilih guru yang mengajar kelas Anda atau lihat seluruh guru sekolah.
            @if($kelasAktif)
                <span class="badge bg-primary ms-2">{{ $kelasAktif->nama_kelas }} Kelas {{ $kelasAktif->tingkat }}</span>
            @endif
        </p>
    </div>
    <div>
        <span class="badge bg-white text-dark border px-3 py-2 font-mono" style="font-size: 0.85rem;">
            Total: <strong>{{ $guru->count() }} Guru</strong>
        </span>
    </div>
</div>

{{-- SEARCH BAR --}}
<div class="card-custom p-3 mb-4">
    <form method="GET" action="{{ route('siswa.guru.index') }}" class="row g-3 align-items-end mb-3">
        <div class="col-md-8">
            <label class="form-label small fw-bold text-muted">Cakupan daftar guru</label>
            <div class="d-flex flex-wrap gap-3">
                <label class="form-check d-flex align-items-center gap-2 mb-0">
                    <input class="form-check-input" type="radio" name="mode" value="kelas" {{ $mode === 'kelas' ? 'checked' : '' }} onchange="this.form.submit()">
                    <span class="small">Guru yang mengajar kelas saya</span>
                </label>
                <label class="form-check d-flex align-items-center gap-2 mb-0">
                    <input class="form-check-input" type="radio" name="mode" value="semua" {{ $mode === 'semua' ? 'checked' : '' }} onchange="this.form.submit()">
                    <span class="small">Semua guru</span>
                </label>
            </div>
        </div>
    </form>
    <form method="GET" action="{{ route('siswa.guru.index') }}" class="row g-2 align-items-center">
        <input type="hidden" name="mode" value="{{ $mode }}">
        <div class="col-md-10">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama guru..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary-custom flex-grow-1"><i class="bi bi-search me-1"></i> Cari</button>
            @if(request('search'))
                <a href="{{ route('siswa.guru.index') }}" class="btn btn-outline-custom" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
            @endif
        </div>
    </form>
</div>

{{-- GRID GURU UNIFIED --}}
@include('siswa.guru.partials.guru-grid', ['teachers' => $guru])

@endsection