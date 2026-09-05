@extends('layouts.admin')
@section('title', 'Tambah Guru')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">MANAJEMEN DATA</div>
        <h1 class="page-title">Tambah Guru</h1>
        <p class="page-subtitle">Tambahkan data guru baru ke dalam sistem.</p>
    </div>
    <a href="{{ route('admin.guru.index') }}" class="btn btn-outline-custom"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-custom p-4">
            <form action="{{ route('admin.guru.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-12 text-center mb-3">
                        <label class="form-label font-mono small fw-bold text-muted">FOTO PROFIL</label>
                        <input type="file" name="photo" class="form-control" style="border-radius: 8px; max-width: 400px; margin: 0 auto;" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG. Maksimal 2MB.</small>
                        @error('photo')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">NIP <span class="text-danger">*</span></label>
                        <input type="text" name="nip" class="form-control font-mono" value="{{ old('nip') }}" required style="border-radius: 8px;" placeholder="Contoh: 199301162022211000">
                        @error('nip')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">NAMA LENGKAP <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required style="border-radius: 8px;" placeholder="Nama lengkap beserta gelar">
                        @error('nama')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">EMAIL</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" style="border-radius: 8px;" placeholder="contoh@guru.smkn1bangsri.sch.id">
                        @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">NO. HP / WHATSAPP</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" style="border-radius: 8px;" placeholder="Contoh: 085758700025">
                        @error('phone')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">KATEGORI <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-select" required style="border-radius: 8px;">
                            <option value="">Pilih Kategori</option>
                            <option value="normada" {{ old('kategori') == 'normada' ? 'selected' : '' }}>Normada</option>
                            <option value="produktif" {{ old('kategori') == 'produktif' ? 'selected' : '' }}>Produktif</option>
                        </select>
                        @error('kategori')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">JURUSAN</label>
                        <select name="jurusan_id" class="form-select" style="border-radius: 8px;">
                            <option value="">Pilih Jurusan (Opsional)</option>
                            @foreach($jurusans as $j)
                                <option value="{{ $j->id }}" {{ old('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama_jurusan }} ({{ $j->kode_jurusan }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label font-mono small fw-bold text-muted">BIO / ALAMAT / KETERANGAN</label>
                        <textarea name="bio" class="form-control" rows="3" style="border-radius: 8px;" placeholder="Alamat atau informasi singkat...">{{ old('bio') }}</textarea>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.guru.index') }}" class="btn btn-outline-custom">Batal</a>
                    <button type="submit" class="btn btn-primary-custom"><i class="bi bi-save me-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection