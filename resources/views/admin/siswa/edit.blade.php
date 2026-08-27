@extends('layouts.admin')
@section('title', 'Edit Siswa')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">MANAJEMEN DATA</div>
        <h1 class="page-title">Edit Siswa</h1>
        <p class="page-subtitle">Perbarui data siswa: {{ $siswa->name }}</p>
    </div>
    <a href="{{ route('admin.siswa.index') }}" class="btn btn-outline-custom"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-custom p-4">
            <form action="{{ route('admin.siswa.update', $siswa) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-12 text-center mb-3">
                        <label class="form-label font-mono small fw-bold text-muted">FOTO PROFIL</label>
                        @if($siswa->photo)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $siswa->photo) }}" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid var(--border);">
                            </div>
                        @endif
                        <input type="file" name="photo" class="form-control" style="border-radius: 8px; max-width: 400px; margin: 0 auto;" accept="image/*">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah foto.</small>
                        @error('photo')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">NIS <span class="text-danger">*</span></label>
                        <input type="text" name="nis" class="form-control" value="{{ old('nis', $siswa->nis) }}" required style="border-radius: 8px;">
                        @error('nis')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">NAMA LENGKAP <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $siswa->name) }}" required style="border-radius: 8px;">
                        @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">KELAS <span class="text-danger">*</span></label>
                        <input type="text" name="kelas" class="form-control" value="{{ old('kelas', $siswa->kelas) }}" required style="border-radius: 8px;">
                        @error('kelas')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">JURUSAN</label>
                        <select name="jurusan_id" class="form-select" style="border-radius: 8px;">
                            <option value="">Pilih Jurusan (Opsional)</option>
                            @foreach($jurusan as $j)
                                <option value="{{ $j->id }}" {{ old('jurusan_id', $siswa->jurusan_id) == $j->id ? 'selected' : '' }}>{{ $j->nama_jurusan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">TANGGAL LAHIR <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $siswa->tanggal_lahir?->format('Y-m-d')) }}" required style="border-radius: 8px;">
                        @error('tanggal_lahir')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.siswa.index') }}" class="btn btn-outline-custom">Batal</a>
                    <button type="submit" class="btn btn-primary-custom"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection