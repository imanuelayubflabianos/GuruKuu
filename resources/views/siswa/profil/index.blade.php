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
                    <img src="{{ auth()->user()->photo_url }}" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover; border: 4px solid var(--primary);">
                    <div>
                        <label class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-camera"></i> Ganti Foto
                            <input type="file" name="photo" class="d-none" accept="image/*" onchange="this.form.submit()">
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label font-mono small fw-bold text-muted">NIS</label>
                    <input type="text" class="form-control" value="{{ auth()->user()->nis }}" disabled style="background: var(--bg-light);">
                </div>
                <div class="mb-3">
                    <label class="form-label font-mono small fw-bold text-muted">KELAS</label>
                    <input type="text" class="form-control" value="{{ $kelasAktif ? $kelasAktif->nama_kelas . ' - Tingkat ' . $kelasAktif->tingkat : (auth()->user()->kelas ?? 'Belum ditentukan') }}" disabled style="background: var(--bg-light);">
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