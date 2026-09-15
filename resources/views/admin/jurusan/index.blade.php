@extends('layouts.admin')
@section('title', 'Data Jurusan')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <div class="page-label">MANAJEMEN DATA</div>
        <h1 class="page-title">Data Jurusan</h1>
        <p class="page-subtitle mb-0">Kelola master data program keahlian dan foto/logo jurusan sekolah.</p>
    </div>
    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalTambahJurusan">
        <i class="bi bi-plus-circle me-1"></i> Tambah Jurusan Baru
    </button>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom mb-0" id="jurusanTable">
            <thead>
                <tr>
                    <th style="width: 80px;">LOGO</th>
                    <th>KODE</th>
                    <th>NAMA JURUSAN</th>
                    <th>DESKRIPSI</th>
                    <th class="text-center" style="width: 160px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jurusans as $j)
                <tr>
                    <td>
                        <img src="{{ $j->logo_url }}" alt="{{ $j->nama_jurusan }}" 
                             class="rounded shadow-sm border" style="width: 44px; height: 44px; object-fit: cover;">
                    </td>
                    <td class="font-mono fw-bold text-primary">{{ $j->kode_jurusan }}</td>
                    <td><strong>{{ $j->nama_jurusan }}</strong></td>
                    <td>
                        <span class="text-muted small">
                            {{ Str::limit($j->deskripsi ?: 'Tidak ada deskripsi.', 60) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.jurusan.edit', $j) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit Jurusan & Foto">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </a>
                        <form action="{{ route('admin.jurusan.destroy', $j) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus jurusan {{ $j->nama_jurusan }}?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Hapus Jurusan">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                        Belum ada data jurusan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Jurusan -->
<div class="modal fade" id="modalTambahJurusan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.jurusan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Jurusan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kode Jurusan <span class="text-danger">*</span></label>
                        <input type="text" name="kode_jurusan" class="form-control" required placeholder="Contoh: PPLG">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Jurusan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_jurusan" class="form-control" required placeholder="Contoh: Pengembangan Perangkat Lunak dan Gim">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="2" placeholder="Deskripsi singkat jurusan..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Foto / Logo Jurusan</label>
                        <input type="file" name="logo" class="form-control" accept="image/png, image/jpeg, image/jpg, image/webp">
                        <div class="form-text small">Mendukung format JPG, PNG, WEBP (maks. 3MB).</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom">Simpan Jurusan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    if ($.fn.DataTable.isDataTable('#jurusanTable')) {
        $('#jurusanTable').DataTable().destroy();
    }
    $('#jurusanTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
        searching: true,
        pageLength: 10
    });
});
</script>
@endpush