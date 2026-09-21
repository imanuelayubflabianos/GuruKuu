@extends('layouts.admin')
@section('title', 'Pesan Masuk')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">LAYANAN PENGADUAN</div>
        <h1 class="page-title">Pesan Masuk & Chat</h1>
        <p class="page-subtitle">Kelola pesan masuk dan komunikasi bantuan dari siswa, guru, atau tamu.</p>
    </div>
</div>

<div class="card-custom">
    <div class="d-flex justify-content-between align-items-center px-3 px-md-4 py-3 border-bottom">
        <span class="small text-muted">Menampilkan {{ $kontak->firstItem() ?? 0 }}–{{ $kontak->lastItem() ?? 0 }} dari {{ $kontak->total() }} pesan</span>
        <span class="badge bg-light text-dark border">15 per halaman</span>
    </div>
    <div class="table-responsive">
        <table class="table table-custom mb-0">
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
                        <br><small class="text-muted font-mono">{{ $k->is_siswa ? 'Siswa (NIS: ' . $k->identifier . ')' : 'Pengguna / Tamu' }}</small>
                    </td>
                    <td style="max-width: 320px;">
                        @if($k->pesan === '[Pesan Dihapus]')
                            <span class="fst-italic text-muted">[Pesan Dihapus]</span>
                        @else
                            {{ Str::limit($k->pesan, 80) }}
                        @endif
                        
                        @if($k->balasan)
                            <div class="mt-2 p-2 small rounded" style="background: #d1e7dd; border-left: 3px solid #198754;">
                                <strong class="text-success">Balasan:</strong><br>
                                {{ Str::limit($k->balasan, 70) }}
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
                        <button type="button" class="btn btn-sm btn-primary-custom mb-1" 
                                onclick='openReplyModal(@json($k))' 
                                title="Balas / Edit Balasan">
                            <i class="bi bi-reply"></i> {{ $k->balasan ? 'Edit' : 'Balas' }}
                        </button>
                        <form action="{{ route('admin.kontak.destroy', $k) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pesan ini secara permanen?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger mb-1" title="Hapus Pesan"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
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
    @if($kontak->hasPages())
        <div class="px-3 px-md-4 py-3 border-top d-flex justify-content-center">{{ $kontak->links() }}</div>
    @endif
</div>

{{-- MODAL BALAS PESAN (DI LUAR TABEL & DI LUAR CARD UNTUK MENCEGAH FLICKER / BACKDROP BLINKING) --}}
<div class="modal fade" id="modalBalasKontak" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fs-6 fw-bold" id="modalBalasTitle">Balas Pesan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form id="formBalasKontak" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="p-3 bg-light rounded border mb-3">
                        <small class="text-muted d-block mb-1">
                            <strong id="modalBalasPengirim" class="text-dark">Nama Pengirim</strong>
                        </small>
                        <p class="small text-muted mb-0 font-italic" id="modalBalasPesanAsli" style="max-height: 120px; overflow-y: auto;">
                            "Pesan asli..."
                        </p>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark">Tulis Tanggapan / Jawaban Administrator:</label>
                        <textarea name="balasan" id="modalBalasTextarea" class="form-control" rows="4" required placeholder="Tuliskan solusi atau jawaban resmi..."></textarea>
                    </div>
                </div>
                
                <div class="modal-footer bg-light d-flex justify-content-between align-items-center">
                    <div>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="btnHapusBalasan" style="display: none;" onclick="submitHapusBalasan()">
                            <i class="bi bi-trash me-1"></i> Hapus Balasan
                        </button>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary-custom btn-sm px-3 fw-semibold">
                            <i class="bi bi-send me-1"></i> Kirim Balasan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- FORM TERPISAH UNTUK HAPUS BALASAN (MENCEGAH NESTED FORM) --}}
<form id="formHapusBalasan" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
let activeKontakId = null;

function openReplyModal(k) {
    activeKontakId = k.id;
    const form = document.getElementById('formBalasKontak');
    form.action = '/admin/kontak/' + k.id + '/reply';
    
    document.getElementById('modalBalasTitle').innerText = (k.balasan ? 'Edit' : 'Kirim') + ' Balasan';
    document.getElementById('modalBalasPengirim').innerText = k.pengirim + (k.is_siswa ? ' (Siswa NIS: ' + k.identifier + ')' : ' (Pengguna/Tamu)');
    document.getElementById('modalBalasPesanAsli').innerText = '"' + k.pesan + '"';
    document.getElementById('modalBalasTextarea').value = k.balasan || '';

    const btnHapus = document.getElementById('btnHapusBalasan');
    if (k.balasan) {
        btnHapus.style.display = 'inline-block';
    } else {
        btnHapus.style.display = 'none';
    }

    new bootstrap.Modal(document.getElementById('modalBalasKontak')).show();
}

function submitHapusBalasan() {
    if (!activeKontakId) return;
    if (confirm('Yakin ingin menghapus balasan pesan ini?')) {
        const delForm = document.getElementById('formHapusBalasan');
        delForm.action = '/admin/kontak/' + activeKontakId + '/reply';
        delForm.submit();
    }
}

</script>
@endpush
