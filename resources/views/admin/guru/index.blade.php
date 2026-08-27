@extends('layouts.admin')
@section('title', 'Data Guru')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">MANAJEMEN DATA</div>
        <h1 class="page-title">Data Guru</h1>
        <p class="page-subtitle">Kelola data guru normada dan produktif.</p>
    </div>
    <a href="{{ route('admin.guru.create') }}" class="btn btn-primary-custom">
        <i class="bi bi-plus-circle me-1"></i> Tambah Guru
    </a>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom mb-0" id="guruTable">
            <thead>
                <tr>
                    <th>FOTO</th>
                    <th>NIP</th>
                    <th>NAMA</th>
                    <th>KATEGORI</th>
                    <th>JURUSAN</th>
                    <th class="text-center">RATING</th>
                    <th class="text-center">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($guru as $g)
                <tr>
                    <td>
                        <img src="{{ $g->photo_url }}" alt="{{ $g->nama }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                    </td>
                    <td class="font-mono small">{{ $g->nip }}</td>
                    <td><strong>{{ $g->nama }}</strong></td>
                    <td>
                        <span class="badge-custom" style="background: {{ $g->kategori === 'normada' ? 'rgba(0,51,102,0.1)' : 'rgba(0,168,107,0.1)' }}; color: {{ $g->kategori === 'normada' ? 'var(--primary)' : 'var(--accent)' }};">
                            {{ strtoupper($g->kategori) }}
                        </span>
                    </td>
                    <td>{{ $g->jurusan?->nama_jurusan ?? '-' }}</td>
                    <td class="text-center">
                        <span class="fw-bold" style="color: var(--secondary);">
                            <i class="bi bi-star-fill"></i> {{ number_format($g->rata_rata_nilai, 2) }}
                        </span>
                        <br><small class="text-muted">{{ $g->total_penilaian }} vote</small>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.guru.edit', $g) }}" class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.guru.destroy', $g) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus guru ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                        Belum ada data guru.
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
    $('#guruTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
        responsive: true,
        pageLength: 10
    });
});
</script>
@endpush