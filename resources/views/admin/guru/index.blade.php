@extends('layouts.admin')
@section('title', 'Data Guru')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <div class="page-label">MANAJEMEN DATA</div>
        <h1 class="page-title">Data Guru</h1>
        <p class="page-subtitle">Kelola master data guru pengajar SMK Negeri 1 Bangsri.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.sipintu.guru') }}" class="btn btn-outline-primary">
            <i class="bi bi-cloud-arrow-down me-1"></i> Tarik dari SiPintu
        </a>
        <a href="{{ route('admin.guru.create') }}" class="btn btn-primary-custom">
            <i class="bi bi-plus-circle me-1"></i> Tambah Guru
        </a>
    </div>
</div>

<div class="card-custom p-3 mb-4">
    <form method="GET" action="{{ route('admin.guru.index') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-bold text-muted">Filter Kategori</label>
            <select name="kategori" class="form-select" style="border-radius: 8px;">
                <option value="">Semua Kategori</option>
                <option value="normada" {{ request('kategori') == 'normada' ? 'selected' : '' }}>Guru Normada</option>
                <option value="produktif" {{ request('kategori') == 'produktif' ? 'selected' : '' }}>Guru Produktif</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-bold text-muted">Filter Jurusan</label>
            <select name="jurusan_id" class="form-select" style="border-radius: 8px;">
                <option value="">Semua Jurusan</option>
                @foreach($jurusans as $j)
                    <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama_jurusan }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary-custom flex-grow-1"><i class="bi bi-funnel me-1"></i> Filter</button>
            <a href="{{ route('admin.guru.index') }}" class="btn btn-outline-custom"><i class="bi bi-arrow-counterclockwise"></i></a>
        </div>
    </form>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom mb-0" id="guruTable">
            <thead>
                <tr>
                    <th>NIP</th>
                    <th>NAMA GURU</th>
                    <th>EMAIL</th>
                    <th>KONTAK / HP</th>
                    <th>KATEGORI</th>
                    <th>JURUSAN</th>
                    <th>PENILAIAN</th>
                    <th class="text-center">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($guru as $g)
                <tr>
                    <td class="font-mono fw-bold text-primary">{{ $g->nip }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $g->photo_url }}" class="rounded-circle border" style="width: 38px; height: 38px; object-fit: cover;">
                            <strong>{{ $g->nama }}</strong>
                        </div>
                    </td>
                    <td>
                        @if($g->email)
                            <span class="text-dark small"><i class="bi bi-envelope text-primary me-1"></i>{{ $g->email }}</span>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td>
                        @if($g->phone)
                            <span class="text-dark small"><i class="bi bi-telephone text-success me-1"></i>{{ $g->phone }}</span>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-{{ $g->kategori == 'normada' ? 'primary' : 'success' }}">
                            {{ ucfirst($g->kategori) }}
                        </span>
                    </td>
                    <td>{{ $g->jurusan->nama_jurusan ?? '-' }}</td>
                    <td>
                        <strong>{{ $g->total_penilaian }}</strong>
                        <small class="text-muted d-block">({{ number_format($g->rata_rata_nilai, 1) }}/5)</small>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.guru.edit', $g) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit Guru"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.guru.destroy', $g) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus guru {{ $g->nama }}?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Hapus Guru"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">Belum ada data guru.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#guruTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
        ordering: false,
        pageLength: 10
    });
});
</script>
@endpush