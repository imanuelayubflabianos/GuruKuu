@extends('layouts.admin')
@section('title', 'Pesan Masuk')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">LAYANAN PENGADUAN</div>
        <h1 class="page-title">Pesan Masuk</h1>
    </div>
</div>

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
                @forelse($pesan as $p)
                <tr>
                    <td>
                        <strong>{{ $p->pengirim }}</strong>
                        <br><small class="text-muted font-mono">{{ $p->is_siswa ? 'NIS: '.$p->identifier : 'Tamu' }}</small>
                    </td>
                    <td style="max-width: 300px;">{{ Str::limit($p->pesan, 80) }}</td>
                    <td>
                        @if($p->is_replied) <span class="badge bg-success">Dibalas</span>
                        @else <span class="badge bg-warning text-dark">Belum</span> @endif
                    </td>
                    <td class="font-mono small">{{ $p->created_at->format('d M Y, H:i') }}</td>
                    <td class="text-center">
                        @if(!$p->is_replied)
                            <button class="btn btn-sm btn-primary-custom mb-1" data-bs-toggle="modal" data-bs-target="#modalBalas{{ $p->id }}"><i class="bi bi-reply"></i></button>
                        @else
                            <form action="{{ route('admin.kontak.destroy-reply', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus balasan?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-warning mb-1"><i class="bi bi-eraser"></i></button>
                            </form>
                        @endif
                        <form action="{{ route('admin.kontak.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus semua?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada pesan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Balas (Sama seperti sebelumnya, pastikan ID modal sesuai) --}}
@foreach($pesan as $p)
    @if(!$p->is_replied)
    <div class="modal fade" id="modalBalas{{ $p->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.kontak.reply', $p) }}" method="POST">
                    @csrf
                    <div class="modal-header"><h5 class="modal-title">Balas Pesan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <p class="small text-muted mb-2">"{{ $p->pesan }}"</p>
                        <textarea name="balasan" class="form-control" rows="3" required placeholder="Tulis balasan..."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary-custom">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#kontakTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
        searching: true, // ✅ SEARCH AKTIF
        order: [[3, 'desc']],
        pageLength: 10
    });
});
</script>
@endpush