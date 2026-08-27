@extends('layouts.admin')
@section('title', 'Data Siswa')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">MANAJEMEN PENGGUNA</div>
        <h1 class="page-title">Data Siswa</h1>
        <p class="page-subtitle">Kelola data siswa dan akun login mereka.</p>
    </div>
    <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary-custom">
        <i class="bi bi-plus-lg"></i> Tambah Siswa
    </a>
</div>

<div class="card-custom">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom mb-0" id="siswaTable">
                <thead>
                    <tr>
                        <th>NIS</th>
                        <th>NAMA SISWA</th>
                        <th>KELAS</th>
                        <th>JURUSAN</th>
                        <th class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siswa as $s)
                    <tr>
                        <td class="font-mono" style="font-size: 0.85rem;">{{ $s->nis }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ $s->photo_url }}" class="rounded-circle me-2" width="36" height="36" style="object-fit: cover;">
                                <div>
                                    <div class="fw-bold">{{ $s->name }}</div>
                                    <small class="text-muted">{{ $s->email ?? '-' }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="font-mono">{{ $s->kelas ?? '-' }}</td>
                        <td>{{ $s->jurusan?->nama_jurusan ?? '-' }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.siswa.edit', $s) }}" class="btn btn-sm btn-outline-custom me-1"><i class="bi bi-pencil"></i></a>
                            <button class="btn btn-sm btn-danger btn-delete" data-url="{{ route('admin.siswa.destroy', $s) }}" data-name="{{ $s->name }}"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#siswaTable').DataTable({ language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' } });
    $('.btn-delete').on('click', function() {
        const url = $(this).data('url'); const name = $(this).data('name');
        Swal.fire({ title: 'Hapus Siswa?', text: `Hapus "${name}"?`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Ya, Hapus!' }).then((r) => {
            if (r.isConfirmed) { const f = document.createElement('form'); f.method='POST'; f.action=url; f.innerHTML='@csrf @method("DELETE")'; document.body.appendChild(f); f.submit(); }
        });
    });
});
</script>
@endpush