@extends('layouts.admin')
@section('title', 'Periode Penilaian')
@section('content')
<div class="page-header"><div class="page-label">PENGATURAN WAKTU</div><h1 class="page-title">Manajemen Periode</h1></div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead><tr><th>NAMA PERIODE</th><th>TAHUN AJARAN</th><th>SEMESTER</th><th>STATUS</th><th class="text-center">AKSI</th></tr></thead>
            <tbody>
                @foreach($periode as $p)
                <tr>
                    <td class="fw-bold">{{ $p->nama_periode }}</td>
                    <td class="font-mono">{{ $p->tahun_ajaran }}</td>
                    <td><span class="badge-custom" style="background: var(--bg-light);">{{ ucfirst($p->semester) }}</span></td>
                    <td>
                        @if($p->status === 'aktif')
                            <span class="badge-custom" style="background: #d1fae5; color: #065f46;">AKTIF</span>
                        @else
                            <span class="badge-custom" style="background: #fee2e2; color: #991b1b;">NONAKTIF</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border shadow-xs rounded-circle d-inline-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 32px; height: 32px;" title="Pilihan Aksi">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-1" style="border-radius: 12px; font-size: 0.85rem; min-width: 175px; box-shadow: 0 10px 25px rgba(0,0,0,0.12) !important;">
                                <li>
                                    <form action="{{ route('admin.periode.toggle', $p) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2 {{ $p->status === 'aktif' ? 'text-danger' : 'text-success' }}">
                                            <i class="bi {{ $p->status === 'aktif' ? 'bi-lock text-danger' : 'bi-unlock text-success' }}"></i>
                                            <span>{{ $p->status === 'aktif' ? 'Nonaktifkan Periode' : 'Aktifkan Periode' }}</span>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection