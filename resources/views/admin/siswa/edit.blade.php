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

                    {{-- OPSI TAMBAH BADGE --}}
                    <div class="col-12 mt-3">
                        <label class="form-label font-mono small fw-bold text-muted d-flex align-items-center justify-content-between mb-2">
                            <span><i class="bi bi-award-fill text-warning me-1"></i> SEMATKAN LENCANA & BADGE</span>
                            <span class="badge bg-light text-muted border font-mono fw-normal">Pilih badge yang dimiliki siswa</span>
                        </label>
                        <div class="row g-2">
                            @foreach($badges as $b)
                                @php
                                    $isAssigned = in_array($b->id, old('badge_ids', $assignedBadgeIds ?? []));
                                @endphp
                                <div class="col-sm-6 col-md-4">
                                    <label class="p-2.5 rounded-3 border d-flex align-items-center gap-2.5 w-100 {{ $isAssigned ? 'bg-primary-subtle border-primary' : 'bg-light' }}" style="cursor: pointer; transition: all 0.2s;">
                                        <input type="checkbox" name="badge_ids[]" value="{{ $b->id }}" class="form-check-input mt-0 flex-shrink-0" {{ $isAssigned ? 'checked' : '' }}>
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
                    <button type="submit" class="btn btn-primary-custom"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection