@extends('layouts.admin')
@section('title', 'Pengaturan')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">PENGATURAN</div>
        <h1 class="page-title">Pengaturan Sistem</h1>
    </div>
</div>

<div class="card-custom p-4">
    <h5 class="fw-bold mb-3">Informasi Periode</h5>
    @if(isset($periodeAktif) && $periodeAktif)
        <div class="alert alert-info">
            <strong>Periode Aktif:</strong> {{ $periodeAktif->nama_periode }} ({{ $periodeAktif->tahun_ajaran }})
        </div>
    @else
        <div class="alert alert-warning">
            Belum ada periode aktif yang diatur.
        </div>
    @endif

    <form action="{{ route('admin.pengaturan.reset') }}" method="POST" class="mt-4">
        @csrf
        <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin mereset data penilaian?')">
            Reset Data Penilaian
        </button>
    </form>
</div>
@endsection