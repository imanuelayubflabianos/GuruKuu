@extends('layouts.admin')
@section('title', 'Edit Jurusan')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">MANAJEMEN DATA</div>
        <h1 class="page-title">Edit Jurusan</h1>
    </div>
    <a href="{{ route('admin.jurusan.index') }}" class="btn btn-outline-custom"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card-custom p-4">
            <form action="{{ route('admin.jurusan.update', $jurusan) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label font-mono small fw-bold text-muted">KODE JURUSAN <span class="text-danger">*</span></label>
                    <input type="text" name="kode_jurusan" class="form-control" value="{{ old('kode_jurusan', $jurusan->kode_jurusan) }}" required style="border-radius: 8px;">
                    @error('kode_jurusan')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label class="form-label font-mono small fw-bold text-muted">NAMA JURUSAN <span class="text-danger">*</span></label>
                    <input type="text" name="nama_jurusan" class="form-control" value="{{ old('nama_jurusan', $jurusan->nama_jurusan) }}" required style="border-radius: 8px;">
                    @error('nama_jurusan')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.jurusan.index') }}" class="btn btn-outline-custom">Batal</a>
                    <button type="submit" class="btn btn-primary-custom"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection