@extends('layouts.admin')
@section('title', 'Edit Jurusan - ' . $jurusan->nama_jurusan)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <div class="page-label">MANAJEMEN DATA</div>
        <h1 class="page-title">Edit Jurusan</h1>
        <p class="page-subtitle">Perbarui data jurusan dan unggah foto/logo keahlian.</p>
    </div>
    <a href="{{ route('admin.jurusan.index') }}" class="btn btn-outline-custom">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-custom p-4">
            <form action="{{ route('admin.jurusan.update', $jurusan) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Kode Jurusan <span class="text-danger">*</span></label>
                        <input type="text" name="kode_jurusan" class="form-control" value="{{ old('kode_jurusan', $jurusan->kode_jurusan) }}" required placeholder="Contoh: PPLG">
                        @error('kode_jurusan')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-bold">Nama Jurusan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_jurusan" class="form-control" value="{{ old('nama_jurusan', $jurusan->nama_jurusan) }}" required placeholder="Contoh: Pengembangan Perangkat Lunak dan Gim">
                        @error('nama_jurusan')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Deskripsi Singkat</label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Jelaskan bidang keahlian jurusan ini...">{{ old('deskripsi', $jurusan->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold">Foto / Logo Jurusan</label>
                        
                        <div class="d-flex align-items-center gap-3 mb-3 p-3 rounded border" style="background: var(--bg-light);">
                            <img id="logoPreview" src="{{ $jurusan->logo_url }}" alt="Logo {{ $jurusan->nama_jurusan }}" 
                                 class="rounded shadow-sm" style="width: 80px; height: 80px; object-fit: cover; border: 2px solid var(--border);">
                            <div>
                                <strong>Logo / Foto Saat Ini</strong>
                                <p class="text-muted small mb-0">Format JPG, PNG, atau WEBP. Maksimal 3 MB.</p>
                            </div>
                        </div>

                        <input type="file" name="logo" id="logoFileInput" class="form-control" accept="image/png, image/jpeg, image/jpg, image/webp">
                        @error('logo')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('admin.jurusan.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary-custom px-4">
                        <i class="bi bi-check-circle-fill me-1"></i> Simpan Perubahan Jurusan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('logoFileInput')?.addEventListener('change', function() {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logoPreview').src = e.target.result;
        };
        reader.readAsDataURL(this.files[0]);
    }
});
</script>
@endsection
