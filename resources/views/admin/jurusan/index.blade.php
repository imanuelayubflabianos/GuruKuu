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
    <div class="d-flex justify-content-between align-items-center px-3 px-md-4 py-3 border-bottom">
        <span class="small text-muted">Menampilkan {{ $jurusans->firstItem() ?? 0 }}–{{ $jurusans->lastItem() ?? 0 }} dari {{ $jurusans->total() }} jurusan</span>
        <span class="badge bg-light text-dark border">15 per halaman</span>
    </div>
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th style="width: 70px;">LOGO</th>
                    <th>KODE</th>
                    <th>NAMA JURUSAN</th>
                    <th class="text-center">KELAS</th>
                    <th class="text-center">SISWA</th>
                    <th>DESKRIPSI</th>
                    <th class="text-center" style="width: 220px;">AKSI</th>
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
                    <td class="text-center">
                        <span class="badge bg-light text-dark border font-mono">{{ $j->kelas_count }} Rombel</span>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-primary-subtle text-primary fw-bold font-mono">{{ $j->siswa_count }} Siswa</span>
                    </td>
                    <td>
                        <span class="text-muted small">
                            {{ Str::limit($j->deskripsi ?: 'Tidak ada deskripsi.', 50) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.jurusan.show', $j) }}" class="btn btn-sm btn-info text-white me-1" title="Lihat Detail Rombel & Siswa">
                            <i class="bi bi-eye"></i> Detail
                        </a>
                        <a href="{{ route('admin.jurusan.edit', $j) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit Jurusan & Foto">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.jurusan.destroy', $j) }}" method="POST" class="d-inline"
                              data-confirm="Yakin ingin menghapus jurusan {{ addslashes($j->nama_jurusan) }}? Data rombel dan siswa terkait akan terdampak."
                              data-confirm-title="Hapus Jurusan"
                              data-confirm-btn="Ya, Hapus"
                              data-confirm-type="danger">
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
    @if($jurusans->hasPages())
        <div class="px-3 px-md-4 py-3 border-top d-flex justify-content-center">{{ $jurusans->links() }}</div>
    @endif
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
</script>
@endpush
