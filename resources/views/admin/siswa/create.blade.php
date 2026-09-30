@extends('layouts.admin')
@section('title', 'Tambah Siswa')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">MANAJEMEN DATA</div>
        <h1 class="page-title">Tambah Siswa</h1>
        <p class="page-subtitle">Tambahkan data siswa baru ke dalam sistem.</p>
    </div>
    <a href="{{ route('admin.siswa.index') }}" class="gk-btn-back">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-custom p-4">
            <form action="{{ route('admin.siswa.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-12 text-center mb-3">
                        <label class="form-label font-mono small fw-bold text-muted">FOTO PROFIL</label>
                        <input type="file" name="photo" class="form-control" style="border-radius: 8px; max-width: 400px; margin: 0 auto;" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG. Maksimal 2MB.</small>
                        @error('photo')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">NIS <span class="text-danger">*</span></label>
                        <input type="text" name="nis" class="form-control" value="{{ old('nis') }}" required style="border-radius: 8px;">
                        @error('nis')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">NAMA LENGKAP <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required style="border-radius: 8px;">
                        @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">KELAS <span class="text-danger">*</span></label>
                        <input type="text" name="kelas" class="form-control" value="{{ old('kelas') }}" placeholder="Contoh: PPLG 1" required style="border-radius: 8px;">
                        @error('kelas')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">JURUSAN</label>
                        <select name="jurusan_id" class="form-select" style="border-radius: 8px;">
                            <option value="">Pilih Jurusan (Opsional)</option>
                            @foreach($jurusan as $j)
                                <option value="{{ $j->id }}" {{ old('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama_jurusan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">TANGGAL LAHIR <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}" required style="border-radius: 8px;">
                        @error('tanggal_lahir')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    {{-- OPSI TAMBAH BADGE --}}
                    <div class="col-12 mt-3">
                        <label class="form-label font-mono small fw-bold text-muted d-flex align-items-center justify-content-between mb-2">
                            <span><i class="bi bi-award-fill text-warning me-1"></i> SEMATKAN LENCANA & BADGE</span>
                            <span class="badge bg-light text-muted border font-mono fw-normal">Opsional, pilih satu atau lebih</span>
                        </label>
                        <div class="row g-2">
                            @foreach($badges as $b)
                                <div class="col-sm-6 col-md-4">
                                    <label class="p-2.5 rounded-3 border d-flex align-items-center gap-2.5 w-100 bg-light" style="cursor: pointer; transition: all 0.2s;">
                                        <input type="checkbox" name="badge_ids[]" value="{{ $b->id }}" class="form-check-input mt-0 flex-shrink-0" {{ in_array($b->id, old('badge_ids', [])) ? 'checked' : '' }}>
                                        <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0" style="width: 32px; height: 32px; background: {{ $b->warna ?: '#003366' }}18; color: {{ $b->warna ?: '#003366' }}; font-size: 1.15rem; border: 1px solid {{ $b->warna ?: '#003366' }}33;">
                                            @if(str_starts_with($b->icon, 'bi-'))
                                                <i class="bi {{ $b->icon }}"></i>
                                            @else
                                                {{ $b->icon }}
                                            @endif
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="fw-bold small text-dark text-truncate">{{ $b->nama_badge }}</div>
                                            <div class="text-muted text-truncate" style="font-size: 0.72rem;">{{ $b->deskripsi ?: 'Badge Siswa' }}</div>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.siswa.index') }}" class="btn btn-outline-custom">Batal</a>
                    <button type="submit" class="btn btn-primary-custom"><i class="bi bi-save me-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection