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
                        <div class="mb-2">
                            <img src="{{ $guru->photo_url }}" class="rounded-circle" style="width: 90px; height: 90px; object-fit: cover; border: 3px solid var(--border);">
                        </div>
                        <input type="file" name="photo" class="form-control" style="border-radius: 8px; max-width: 400px; margin: 0 auto;" accept="image/*">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah foto.</small>
                        @error('photo')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">NIP <span class="text-danger">*</span></label>
                        <input type="text" name="nip" class="form-control font-mono" value="{{ old('nip', $guru->nip) }}" required style="border-radius: 8px;">
                        @error('nip')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">NAMA LENGKAP <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama', $guru->nama) }}" required style="border-radius: 8px;">
                        @error('nama')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">EMAIL</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $guru->email) }}" style="border-radius: 8px;" placeholder="contoh@guru.smkn1bangsri.sch.id">
                        @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">NO. HP / WHATSAPP</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $guru->phone) }}" style="border-radius: 8px;" placeholder="Contoh: 085758700025">
                        @error('phone')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">KATEGORI PENGAJAR</label>
                        <select name="kategori" class="form-select" style="border-radius: 8px;">
                            <option value="normada" {{ old('kategori', $guru->kategori) === 'normada' ? 'selected' : '' }}>Normada</option>
                            <option value="produktif" {{ old('kategori', $guru->kategori) === 'produktif' ? 'selected' : '' }}>Produktif</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label font-mono small fw-bold text-muted">KELAS YANG DIAJAR</label>
                        <select name="kelas_ids[]" class="form-select" multiple size="6" style="border-radius: 8px;">
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}" {{ $guru->kelas->contains($kelas->id) ? 'selected' : '' }}>
                                    {{ $kelas->label_singkat }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih satu atau beberapa kelas. Tahan Ctrl untuk memilih lebih dari satu.</small>
                    </div>
                    <div class="col-12">
                        <label class="form-label font-mono small fw-bold text-muted">BIO / ALAMAT / KETERANGAN</label>
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