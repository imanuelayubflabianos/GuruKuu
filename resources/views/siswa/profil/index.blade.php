@extends('layouts.siswa')
@section('title', 'Profil Saya')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">AKUN SAYA</div>
        <h1 class="page-title">Profil Siswa</h1>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card-custom p-4">
            <form action="{{ route('siswa.profil.update') }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width: 80px; height: 80px; font-size: 2rem;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="fw-bold fs-5 mt-2">{{ auth()->user()->name }}</div>
                    <div class="text-muted small font-mono">NIS: {{ auth()->user()->nis }}</div>
                </div>

                <div class="mb-3">
                    <label class="form-label font-mono small fw-bold text-muted">NIS</label>
                    <input type="text" class="form-control" value="{{ auth()->user()->nis }}" disabled style="background: var(--bg-light);">
                </div>
                <div class="mb-3">
                    <label class="form-label font-mono small fw-bold text-muted">KELAS</label>
                    <input type="text" class="form-control" value="{{ $kelasAktif ? $kelasAktif->nama_kelas . ' Kelas ' . $kelasAktif->tingkat : (auth()->user()->kelas ?? 'Belum ditentukan') }}" disabled style="background: var(--bg-light);">
                </div>
                <div class="mb-3">
                    <label class="form-label font-mono small fw-bold text-muted">NAMA LENGKAP</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" required style="border-radius: 8px;">
                    @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
                <div class="mt-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary-custom"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection