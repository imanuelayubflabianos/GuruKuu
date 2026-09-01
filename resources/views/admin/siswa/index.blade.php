@extends('layouts.admin')
@section('title', 'Data Siswa')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">MANAJEMEN DATA</div>
        <h1 class="page-title">Data Siswa</h1>
        <p class="page-subtitle">Kelola data siswa dan filter berdasarkan kelas.</p>
    </div>
    <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary-custom">
        <i class="bi bi-plus-circle me-1"></i> Tambah Siswa
    </a>
</div>

{{-- FILTER BERDASARKAN KELAS --}}
<div class="card-custom p-3 mb-4">
    <form method="GET" action="{{ route('admin.siswa.index') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-bold text-muted">Filter Kelas</label>
            <select name="kelas" class="form-select" style="border-radius: 8px;" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                @foreach($kelasList as $k)
                    <option value="{{ $k->id }}" {{ request('kelas') == $k->id ? 'selected' : '' }}>
                        Tingkat {{ $k->tingkat }} - {{ $k->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.siswa.index') }}" class="btn btn-outline-custom">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
            </a>
        </div>
        <div class="col-md-4 text-end">
            <span class="badge bg-primary px-3 py-2">
                Total: {{ $siswa->count() }} siswa
            </span>
        </div>
    </form>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom mb-0" id="siswaTable">
            <thead>
                <tr>
                    <th>NIS</th>
                    <th>NAMA</th>
                    <th>KELAS</th>
                    <th>STATUS</th>
                    <th class="text-center">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswa as $s)
                <tr>
                    <td class="font-mono fw-bold">{{ $s->nis }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $s->photo_url }}" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                            <div>
                                <strong>{{ $s->name }}</strong>
                                <br><small class="text-muted">{{ $s->email }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        @php
                            // Ambil relasi kelas secara paksa untuk menghindari bentrok dengan kolom string 'kelas'
                            $kelasRel = $s->getRelation('kelas');
                        @endphp
                        
                        @if($kelasRel && $kelasRel->isNotEmpty())
                            @foreach($kelasRel as $kelas)
                                <span class="badge bg-light text-dark border">
                                    {{ $kelas->nama_kelas }} (Tingkat {{ $kelas->tingkat }})
                                </span>
                            @endforeach
                        @elseif(is_string($s->kelas) && $s->kelas)
                            {{-- Fallback jika menggunakan kolom string biasa --}}
                            <span class="badge bg-light text-dark border">{{ $s->kelas }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($s->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                        
                        @if(isset($s->warning_count) && $s->warning_count > 0)
                            <span class="badge bg-danger ms-1">
                                <i class="bi bi-exclamation-triangle-fill"></i> {{ $s->warning_count }} Warning
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if(Route::has('admin.siswa.toggle'))
                        <form action="{{ route('admin.siswa.toggle', $s) }}" method="POST" class="d-inline">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm {{ $s->is_active ? 'btn-outline-warning' : 'btn-outline-success' }} mb-1" title="{{ $s->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                <i class="bi {{ $s->is_active ? 'bi-lock' : 'bi-unlock' }}"></i>
                            </button>
                        </form>
                        @endif
                        
                        <a href="{{ route('admin.siswa.edit', $s) }}" class="btn btn-sm btn-outline-primary mb-1" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.siswa.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus siswa ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger mb-1" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                        Belum ada data siswa.
                    </td>
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
    $('#siswaTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
        searching: true,
        pageLength: 10
    });
});
</script>
@endpush