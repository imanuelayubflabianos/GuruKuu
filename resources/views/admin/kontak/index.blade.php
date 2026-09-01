@extends('layouts.admin')
@section('title', 'Pesan Masuk')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">LAYANAN PENGADUAN</div>
        <h1 class="page-title">Pesan Masuk</h1>
        <p class="page-subtitle">Kelola pesan masuk dari siswa atau tamu.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom mb-0" id="kontakTable">
            <thead>
                <tr>
                    <th>PENGIRIM</th>
                    <th>PESAN</th>
                    <th>STATUS</th>
                    <th>WAKTU</th>
                    <th class="text-center">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kontak as $k)
                <tr>
                    <td>
                        <strong>{{ $k->pengirim }}</strong>
                        <br><small class="text-muted font-mono">{{ $k->is_siswa ? 'Siswa (NIS: ' . $k->identifier . ')' : 'Tamu' }}</small>
                    </td>
                    <td style="max-width: 300px;">
                        @if($k->pesan === '[Pesan Dihapus]')
                            <span class="fst-italic text-muted">[Pesan Dihapus]</span>
                        @else
                            {{ Str::limit($k->pesan, 80) }}
                        @endif
                        
                        @if($k->balasan)
                            <div class="mt-2 p-2 small rounded" style="background: #d1e7dd; border-left: 3px solid #198754;">
                                <strong>Balasan:</strong><br>
                                {{ Str::limit($k->balasan, 60) }}
                            </div>
                        @endif
                    </td>
                    <td>
                        @if($k->is_replied) 
                            <span class="badge bg-success">Dibalas</span>
                        @else 
                            <span class="badge bg-warning text-dark">Belum</span> 
                        @endif
                    </td>
                    <td class="font-mono small">{{ $k->created_at->format('d M Y, H:i') }}</td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-primary-custom mb-1" data-bs-toggle="modal" data-bs-target="#modalBalas{{ $k->id }}" title="Balas / Edit">
                            <i class="bi bi-reply"></i> {{ $k->balasan ? 'Edit' : 'Balas' }}
                        </button>
                        <form action="{{ route('admin.kontak.destroy', $k) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pesan ini secara permanen?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Hapus Pesan"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>

                {{-- MODAL BALAS / EDIT BALASAN --}}
                <div class="modal fade" id="modalBalas{{ $k->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form action="{{ route('admin.kontak.reply', $k) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">{{ $k->balasan ? 'Edit' : 'Kirim' }} Balasan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="small text-muted mb-2"><strong>Pesan Asli:</strong><br>"{{ $k->pesan }}"</p>
                                    <textarea name="balasan" class="form-control" rows="3" required placeholder="Tulis balasan...">{{ $k->balasan }}</textarea>
                                </div>
                                <div class="modal-footer">
                                    @if($k->balasan)
                                        <form action="{{ route('admin.kontak.destroy-reply', $k) }}" method="POST" class="me-auto" onsubmit="return confirm('Hapus balasan ini?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm">Hapus Balasan</button>
                                        </form>
                                    @endif
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary-custom">Kirim</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                        Belum ada pesan masuk.
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
    $('#kontakTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
        searching: true,
        order: [[3, 'desc']],
        pageLength: 10
    });
});
</script>
@endpush