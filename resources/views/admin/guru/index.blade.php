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
        <div class="dropdown">
            <button class="btn btn-outline-custom dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-download me-1"></i> Export Data
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li>
                    <a class="dropdown-item" href="{{ route('admin.export.guru.excel') }}">
                        <i class="bi bi-file-earmark-excel text-success me-2"></i> Export Excel (.xlsx)
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.export.guru.pdf') }}">
                        <i class="bi bi-file-earmark-pdf text-danger me-2"></i> Export PDF (.pdf)
                    </a>
                </li>
            </ul>
        </div>
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
        <div class="col-md-8">
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
                    <th>JURUSAN</th>
                    <th>KEPUASAN (RATING)</th>
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
                    <td>{{ $g->jurusan->nama_jurusan ?? '-' }}</td>
                    <td>
                        @php $pct = round(($g->rata_rata_nilai / 5) * 100); @endphp
                        <div class="fw-bold font-mono text-primary" style="font-size: 0.85rem;">{{ $pct }}%</div>
                        <div class="progress" style="height: 5px; width: 75px; border-radius: 10px;">
                            <div class="progress-bar bg-primary rounded-pill" style="width: {{ $pct }}%;"></div>
                        </div>
                        <small class="text-muted d-block mt-1 font-mono" style="font-size: 0.7rem;">{{ $g->total_penilaian }} ulasan</small>
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