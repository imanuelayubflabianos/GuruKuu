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
                        <form action="{{ route('admin.periode.toggle', $p) }}" method="POST" class="d-inline">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-custom">{{ $p->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}</button></form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection