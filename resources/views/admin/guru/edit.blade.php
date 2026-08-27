@extends('layouts.admin')
@section('title', 'Edit Guru')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">MANAJEMEN DATA</div>
        <h1 class="page-title">Edit Guru</h1>
        <p class="page-subtitle">Perbarui data guru: {{ $guru->nama }}</p>
    </div>
    <a href="{{ route('admin.guru.index') }}" class="btn btn-outline-custom"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-custom p-4">
            <form action="{{ route('admin.guru.update', $guru) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-12 text-center mb-3">
                        <label class="form-label font-mono small fw-bold text-muted">FOTO PROFIL</label>
                        @if($guru->photo)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $guru->photo) }}" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid var(--border);">
                            </div>
                        @endif
                        <input type="file" name="photo" class="form-control" style="border-radius: 8px; max-width: 400px; margin: 0 auto;" accept="image/*">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah foto.</small>
                        @error('photo')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">NIP <span class="text-danger">*</span></label>
                        <input type="text" name="nip" class="form-control" value="{{ old('nip', $guru->nip) }}" required style="border-radius: 8px;">
                        @error('nip')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">NAMA LENGKAP <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama', $guru->nama) }}" required style="border-radius: 8px;">
                        @error('nama')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">KATEGORI <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-select" required style="border-radius: 8px;">
                            <option value="normada" {{ old('kategori', $guru->kategori) == 'normada' ? 'selected' : '' }}>Normada</option>
                            <option value="produktif" {{ old('kategori', $guru->kategori) == 'produktif' ? 'selected' : '' }}>Produktif</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">JURUSAN</label>
                        <select name="jurusan_id" class="form-select" style="border-radius: 8px;">
                            <option value="">Pilih Jurusan (Opsional)</option>
                            @foreach($jurusan as $j)
                                <option value="{{ $j->id }}" {{ old('jurusan_id', $guru->jurusan_id) == $j->id ? 'selected' : '' }}>{{ $j->nama_jurusan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label font-mono small fw-bold text-muted">BIO / DESKRIPSI</label>
                        <textarea name="bio" class="form-control" rows="3" style="border-radius: 8px;">{{ old('bio', $guru->bio) }}</textarea>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.guru.index') }}" class="btn btn-outline-custom">Batal</a>
                    <button type="submit" class="btn btn-primary-custom"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection