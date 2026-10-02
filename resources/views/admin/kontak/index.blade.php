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
                    <th style="width: 200px;">WAKTU</th>
                    <th class="text-center" style="width: 120px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kontak as $k)
                <tr>
                    <td>
                        <a href="{{ route('admin.kontak.chat', $k->identifier) }}" class="text-decoration-none d-flex align-items-center gap-3 p-2 rounded-3 hover-bg-light transition-all" style="cursor: pointer;" title="Klik untuk membuka ruang chat & membalas pesan">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0 fw-bold" style="width: 40px; height: 40px; background: linear-gradient(135deg, #003366, #0055a5); font-size: 0.95rem;">
                                {{ strtoupper(substr($k->display_pengirim, 0, 1) ?: 'U') }}
                            </div>
                            <div>
                                <div class="fw-bold text-dark d-flex align-items-center gap-1.5">
                                    <span>{{ $k->display_pengirim }}</span>
                                    <i class="bi bi-box-arrow-up-right text-primary small" style="font-size: 0.72rem;"></i>
                                    @if(!$k->is_replied)
                                        <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger ms-1" style="font-size: 0.65rem;">Menunggu Balasan</span>
                                    @else
                                        <span class="badge rounded-pill bg-success-subtle text-success border border-success ms-1" style="font-size: 0.65rem;">Dibalas</span>
                                    @endif
                                </div>
                                <small class="text-muted font-mono" style="font-size: 0.78rem;">
                                    {{ $k->is_siswa ? 'Siswa (NIS: ' . $k->identifier . ')' : 'Pengguna / Tamu (' . (substr($k->display_pengirim, 6) ?: '-') . ')' }}
                                </small>
                            </div>
                        </a>
                    </td>
                    <td class="font-mono small align-middle text-muted">
                        <i class="bi bi-clock me-1"></i>{{ $k->created_at->format('d M Y, H:i') }}
                    </td>
                    <td class="text-center align-middle">
                        <form action="{{ route('admin.kontak.destroy', $k) }}" method="POST" onsubmit="return confirm('Hapus pesan ini secara permanen?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1.5 px-3 py-1.5 rounded-pill shadow-xs" title="Hapus Pesan">
                                <i class="bi bi-trash"></i>
                                <span>Hapus</span>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center py-5 text-muted">
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
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-bold text-dark mb-0">Tulis Tanggapan / Jawaban Administrator:</label>
                            <span id="modalBalasCounter" class="badge bg-light text-muted border small">0 / 255</span>
                        </div>
                        <textarea name="balasan" id="modalBalasTextarea" class="form-control" rows="3" maxlength="255" required placeholder="Tuliskan solusi atau jawaban resmi... (maks. 255 karakter)"></textarea>
                        <div id="modalBalasWarn" class="text-danger small mt-1 fw-bold" style="display: none;">
                            <i class="bi bi-exclamation-circle-fill me-1"></i> Anda telah mencapai batas maksimal 255 karakter!
                        </div>
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

const modalBalasTextarea = document.getElementById('modalBalasTextarea');
const modalBalasCounter = document.getElementById('modalBalasCounter');
const modalBalasWarn = document.getElementById('modalBalasWarn');

if (modalBalasTextarea) {
    modalBalasTextarea.addEventListener('input', function() {
        const len = this.value.length;
        if (modalBalasCounter) modalBalasCounter.textContent = `${len} / 255`;
        if (modalBalasWarn) modalBalasWarn.style.display = len >= 255 ? 'block' : 'none';
    });
}

function openReplyModal(k) {
    activeKontakId = k.id;
    const form = document.getElementById('formBalasKontak');
    form.action = '/admin/kontak/' + k.id + '/reply';
    
    document.getElementById('modalBalasTitle').innerText = (k.balasan ? 'Edit' : 'Kirim') + ' Balasan';
    document.getElementById('modalBalasPengirim').innerText = k.pengirim + (k.is_siswa ? ' (Siswa NIS: ' + k.identifier + ')' : ' (Pengguna/Tamu)');
    document.getElementById('modalBalasPesanAsli').innerText = '"' + k.pesan + '"';
    
    const val = k.balasan || '';
    modalBalasTextarea.value = val;
    if (modalBalasCounter) modalBalasCounter.textContent = `${val.length} / 255`;
    if (modalBalasWarn) modalBalasWarn.style.display = val.length >= 255 ? 'block' : 'none';

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
