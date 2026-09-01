@extends('layouts.siswa')
@section('title', 'Chat dengan Admin')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">PUSAT BANTUAN</div>
        <h1 class="page-title">Chat dengan Admin</h1>
        <p class="page-subtitle">Sampaikan kendala atau pertanyaan Anda langsung kepada Admin.</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        
        {{-- HEADER CHAT PREMIUM --}}
        <div class="card-custom mb-3" style="overflow: hidden;">
            <div class="p-3 d-flex align-items-center gap-3" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white;">
                <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px);">
                    <i class="bi bi-headset-fill fs-4"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-0">Administrator GuruKuu</h6>
                    <small style="opacity: 0.9;"><i class="bi bi-circle-fill text-success me-1" style="font-size: 0.5rem;"></i> Online • Siap membantu Anda</small>
                </div>
                <div class="d-none d-md-block">
                    <span class="badge bg-light text-dark px-3 py-2">
                        <i class="bi bi-shield-lock-fill me-1"></i> Terenkripsi
                    </span>
                </div>
            </div>
        </div>

        {{-- AREA CHAT UTAMA --}}
        <div class="card-custom" style="overflow: hidden; display: flex; flex-direction: column; height: 65vh; border: 1px solid var(--border);">
            
            {{-- Area Pesan Chat (Scrollable) --}}
            <div class="flex-grow-1 p-4" style="overflow-y: auto; background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);" id="chatContainer">
                @if($pesan->isEmpty())
                    <div class="text-center py-5">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 80px; height: 80px; background: rgba(0,51,102,0.1);">
                            <i class="bi bi-chat-square-text-fill fs-1" style="color: var(--primary);"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Mulai Percakapan</h5>
                        <p class="text-muted small mb-0">Kirim pesan pertama Anda ke Admin di bawah ini.</p>
                    </div>
                @else
                    @foreach($pesan as $chat)
                        {{-- PESAN SISWA (KANAN) --}}
                        <div class="d-flex justify-content-end mb-3">
                            <div style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white; padding: 0.85rem 1.25rem; border-radius: 18px 18px 4px 18px; max-width: 75%; box-shadow: 0 4px 12px rgba(0,51,102,0.2); position: relative;">
                                @if($chat->pesan === '[Pesan Dihapus]')
                                    <p class="mb-1 fst-italic opacity-75"><i class="bi bi-trash me-1"></i>[Pesan Dihapus]</p>
                                @else
                                    <p class="mb-1" style="line-height: 1.5; word-wrap: break-word;">{{ $chat->pesan }}</p>
                                @endif
                                <div class="text-end mt-2 d-flex justify-content-end gap-2 align-items-center" style="font-size: 0.7rem; opacity: 0.9;">
                                    <span>{{ $chat->created_at->format('H:i') }}</span>
                                    @if($chat->is_replied)
                                        <i class="bi bi-check2-all" style="color: #4fc3f7;"></i>
                                    @else
                                        <i class="bi bi-check2"></i>
                                    @endif
                                    @if($chat->pesan !== '[Pesan Dihapus]')
                                        <button class="btn btn-sm p-0 text-white border-0 ms-1" onclick="openEditModal({{ $chat->id }}, '{{ addslashes($chat->pesan) }}')" title="Edit">
                                            <i class="bi bi-pencil-fill" style="font-size: 0.75rem;"></i>
                                        </button>
                                        <form action="{{ route('siswa.kontak.destroy-message', $chat) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pesan ini?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm p-0 text-white border-0" title="Hapus">
                                                <i class="bi bi-trash-fill" style="font-size: 0.75rem;"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- BALASAN ADMIN (KIRI) --}}
                        @if($chat->is_replied && $chat->balasan)
                        <div class="d-flex justify-content-start mb-3">
                            <div class="d-flex align-items-start gap-2" style="max-width: 75%;">
                                <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 36px; height: 36px; background: var(--primary); color: white;">
                                    <i class="bi bi-person-badge-fill" style="font-size: 0.9rem;"></i>
                                </div>
                                <div style="background: white; border: 1px solid var(--border); padding: 0.85rem 1.25rem; border-radius: 18px 18px 18px 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                                    <div class="fw-bold mb-1" style="color: var(--primary); font-size: 0.8rem;">
                                        <i class="bi bi-patch-check-fill me-1"></i> Admin
                                    </div>
                                    <p class="mb-1" style="line-height: 1.5; word-wrap: break-word;">{{ $chat->balasan }}</p>
                                    <div class="text-end" style="font-size: 0.7rem; color: var(--text-muted);">
                                        {{ $chat->updated_at->format('H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                @endif
            </div>

            {{-- FORM KIRIM PESAN DENGAN CAPTCHA DI BAWAH --}}
            <div class="border-top" style="background: white;">
                @if(session('success'))
                    <div class="alert alert-success border-0 mb-0 py-2 px-3 small d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger border-0 mb-0 py-2 px-3 small">
                        @foreach($errors->all() as $error)
                            <div><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                {{-- PERBAIKAN: route('siswa.kontak.store') --}}
                <form action="{{ route('siswa.kontak.store') }}" method="POST">
                    @csrf
                    <div class="p-3">
                        <div class="d-flex gap-2 align-items-end mb-3">
                            <textarea name="pesan" class="form-control" rows="2" placeholder="Ketik pesan Anda di sini..." required style="border-radius: 12px; resize: none; border: 2px solid var(--border); transition: all 0.3s;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'">{{ old('pesan') }}</textarea>
                            <button type="submit" class="btn btn-primary-custom d-flex align-items-center justify-content-center" style="height: 46px; width: 46px; border-radius: 12px; padding: 0;">
                                <i class="bi bi-send-fill fs-5"></i>
                            </button>
                        </div>

                        <div class="p-2 rounded" style="background: var(--bg-light); border: 1px dashed var(--border);">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-shield-lock-fill" style="color: var(--primary);"></i>
                                    <label class="form-label mb-0 small fw-bold text-muted">VERIFIKASI: {{ $num1 }} + {{ $num2 }} =</label>
                                </div>
                                <div class="d-flex gap-2 align-items-center">
                                    <input type="number" name="captcha" class="form-control form-control-sm" placeholder="?" required style="border-radius: 8px; max-width: 80px; font-weight: 700; text-align: center; border: 2px solid var(--border);" value="{{ old('captcha') }}">
                                    <small class="text-muted mb-0" style="font-size: 0.7rem;">Anti-spam</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="text-center mt-3">
            <small class="text-muted">
                <i class="bi bi-info-circle me-1"></i> Pesan Anda akan dijawab oleh Admin dalam waktu 1x24 jam
            </small>
        </div>
    </div>
</div>

{{-- MODAL EDIT PESAN --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <form id="editForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Pesan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea name="pesan" id="editTextarea" class="form-control" rows="4" required style="border-radius: 12px; border: 2px solid var(--border);"></textarea>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                    <button type="submit" class="btn btn-primary-custom" style="border-radius: 8px;">
                        <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.onload = () => { 
        const c = document.getElementById('chatContainer'); 
        if(c) c.scrollTop = c.scrollHeight; 
    };

    function openEditModal(id, text) {
        // PERBAIKAN: Route edit siswa adalah siswa.kontak.edit
        document.getElementById('editForm').action = '/siswa/kontak/' + id;
        document.getElementById('editTextarea').value = text;
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }
</script>
@endpush
@endsection