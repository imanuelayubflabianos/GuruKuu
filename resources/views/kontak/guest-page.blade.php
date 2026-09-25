@extends('layouts.landing')
@section('title', 'Hubungi Admin Operator Sekolah')

@section('content')
<div style="padding-top: 105px; padding-bottom: 60px; background: var(--bg-light); min-height: 85vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
                
                <div class="mb-3">
                    <a href="{{ route('landing.index') }}" class="btn btn-outline-custom btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
                    </a>
                </div>

                <div class="text-center mb-4">
                    <span class="badge rounded-pill px-3 py-1.5 mb-2 font-mono" style="background: rgba(0, 51, 102, 0.08); color: var(--primary); font-size: 0.72rem; letter-spacing: 0.5px; border: 1px solid var(--border);">
                        <i class="bi bi-headset me-1"></i> PUSAT BANTUAN OPERATOR
                    </span>
                    <h2 class="fw-bold mb-1" style="color: var(--primary);">Hubungi Admin Operator Sekolah</h2>
                    <p class="text-muted small mb-0">Punya kendala saat login, akun dinonaktifkan, atau pertanyaan lainnya? Kirim pesan di sini.</p>
                </div>

                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-sm mb-3 py-2 px-3 small rounded-3">
                        <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger border-0 shadow-sm mb-3 py-2 px-3 small rounded-3">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
                    </div>
                @endif

                {{-- HEADER CHAT PREMIUM (IDENTIK DENGAN SISWA) --}}
                <div class="card-custom mb-3 overflow-hidden shadow-sm">
                    <div class="p-3 d-flex align-items-center gap-3" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light, #004d99) 100%); color: white;">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); flex-shrink: 0;">
                            <i class="bi bi-headset fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-0">Admin Operator Sekolah</h6>
                            <small style="opacity: 0.92;"><i class="bi bi-circle-fill text-success me-1" style="font-size: 0.5rem;"></i> Online • Siap membantu Anda</small>
                        </div>
                        <div>
                            <span class="badge bg-light text-dark px-2.5 py-1.5 rounded-pill font-mono" style="font-size: 0.72rem;">
                                <i class="bi bi-shield-lock-fill me-1 text-success"></i> Terenkripsi
                            </span>
                        </div>
                    </div>
                </div>

                {{-- AREA CHAT UTAMA --}}
                <div class="card-custom shadow-sm overflow-hidden d-flex flex-column" style="height: 60vh; min-height: 420px; border: 1px solid var(--border);">
                    
                    {{-- Area Pesan Chat (Scrollable) --}}
                    <div class="flex-grow-1 p-3 p-md-4 overflow-y-auto" style="background: var(--bg-card);" id="chatContainer">
                        @if($riwayat->isEmpty())
                            <div class="text-center py-5">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 72px; height: 72px; background: rgba(0,51,102,0.08);">
                                    <i class="bi bi-chat-square-text-fill fs-2" style="color: var(--primary);"></i>
                                </div>
                                <h6 class="fw-bold mb-1">Mulai Percakapan</h6>
                                <p class="text-muted small mb-0">Tulis pesan pertama Anda ke Admin Operator di form bawah ini.</p>
                            </div>
                        @else
                            @foreach($riwayat as $chat)
                                {{-- PESAN PENGUNJUNG / TAMU (KANAN) --}}
                                <div class="d-flex justify-content-end mb-3">
                                    <div style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light, #004d99) 100%); color: white; padding: 0.85rem 1.15rem; border-radius: 18px 18px 4px 18px; max-width: 82%; box-shadow: 0 4px 12px rgba(0,51,102,0.18); position: relative;">
                                        @if($chat->pesan === '[Pesan Dihapus]')
                                            <p class="mb-1 fst-italic opacity-75 small"><i class="bi bi-trash me-1"></i>[Pesan Dihapus]</p>
                                        @else
                                            <p class="mb-1" style="line-height: 1.5; word-wrap: break-word; font-size: 0.92rem;">{{ $chat->pesan }}</p>
                                        @endif
                                        <div class="text-end mt-1 d-flex justify-content-end gap-2 align-items-center" style="font-size: 0.7rem; opacity: 0.9;">
                                            <span>{{ $chat->created_at->format('H:i') }}</span>
                                            @if($chat->is_replied)
                                                <i class="bi bi-check2-all" style="color: #4fc3f7;"></i>
                                            @else
                                                <i class="bi bi-check2"></i>
                                            @endif

                                            @if($chat->pesan !== '[Pesan Dihapus]')
                                                <button class="btn btn-sm p-0 text-white border-0 ms-1.5" onclick="openEditModal({{ $chat->id }}, '{{ addslashes($chat->pesan) }}')" title="Edit Pesan">
                                                    <i class="bi bi-pencil-fill" style="font-size: 0.72rem;"></i>
                                                </button>
                                                <form action="{{ route('kontak.guest.destroy-message', $chat) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pesan ini?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm p-0 text-white border-0" title="Hapus Pesan">
                                                        <i class="bi bi-trash-fill" style="font-size: 0.72rem;"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- BALASAN ADMIN (KIRI) --}}
                                @if($chat->is_replied && $chat->balasan)
                                <div class="d-flex justify-content-start mb-3">
                                    <div class="d-flex align-items-start gap-2" style="max-width: 82%;">
                                        <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 34px; height: 34px; background: var(--primary); color: white;">
                                            <i class="bi bi-person-badge-fill" style="font-size: 0.85rem;"></i>
                                        </div>
                                        <div style="background: white; border: 1px solid var(--border); padding: 0.85rem 1.15rem; border-radius: 18px 18px 18px 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                                            <div class="fw-bold mb-1" style="color: var(--primary); font-size: 0.78rem;">
                                                <i class="bi bi-patch-check-fill me-1"></i> Admin Operator Sekolah
                                            </div>
                                            <p class="mb-1" style="line-height: 1.5; word-wrap: break-word; font-size: 0.92rem;">{{ $chat->balasan }}</p>
                                            <div class="text-end" style="font-size: 0.7rem; color: var(--text-muted);">
                                                {{ $chat->updated_at->format('H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @elseif(!$chat->is_replied && $chat->pesan !== '[Pesan Dihapus]')
                                <div class="d-flex justify-content-start mb-3">
                                    <div class="rounded-pill px-3 py-1 small" style="background: #fef3c7; color: #92400e; font-size: 0.75rem; border: 1px solid #fde68a;">
                                        <i class="bi bi-hourglass-split me-1"></i> Menunggu balasan admin...
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        @endif
                    </div>

                    {{-- FORM KIRIM PESAN DENGAN CAPTCHA DI BAWAH --}}
                    <div class="border-top bg-white">
                        @if($errors->any())
                            <div class="alert alert-danger border-0 mb-0 py-2 px-3 small">
                                @foreach($errors->all() as $error)
                                    <div><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ route('kontak.guest.store') }}" method="POST">
                            @csrf
                            <div style="display:none !important; position:absolute; left:-9999px;" aria-hidden="true">
                                <input type="text" name="website_hp" id="website_hp" tabindex="-1" autocomplete="off">
                            </div>
                            <input type="hidden" name="_device_id" value="{{ $deviceId }}">

                            <div class="p-3">
                                <div class="d-flex gap-2 align-items-end mb-2.5">
                                    <textarea name="pesan" maxlength="1000" class="form-control" rows="2" placeholder="Jelaskan masalah Anda secara jelas (maks. 1000 karakter)..." required style="border-radius: 12px; resize: none; border: 2px solid var(--border); transition: all 0.25s;" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'">{{ old('pesan') }}</textarea>
                                    <button type="submit" class="btn btn-primary-custom d-flex align-items-center justify-content-center" style="height: 46px; width: 46px; border-radius: 12px; padding: 0; flex-shrink: 0;" title="Kirim Pesan">
                                        <i class="bi bi-send-fill fs-5"></i>
                                    </button>
                                </div>

                                <div class="p-2 rounded" style="background: var(--bg-light); border: 1px dashed var(--border);">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <div class="d-flex align-items-center gap-1.5">
                                            <i class="bi bi-shield-lock-fill text-primary"></i>
                                            <label class="form-label mb-0 small fw-bold text-muted" style="font-size: 0.78rem;">VERIFIKASI: {{ $num1 }} + {{ $num2 }} =</label>
                                        </div>
                                        <div class="d-flex gap-2 align-items-center">
                                            <input type="number" name="captcha" class="form-control form-control-sm" placeholder="?" required style="border-radius: 8px; max-width: 80px; font-weight: 700; text-align: center; border: 2px solid var(--border);" value="{{ old('captcha') }}">
                                            <small class="text-muted mb-0" style="font-size: 0.7rem;">Verifikasi</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i> Identitas Anda disimpan anonim berdasarkan browser perangkat ini.
                    </small>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT PESAN GUEST --}}
<div class="modal fade" id="editGuestModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <form id="editGuestForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Pesan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea name="pesan" id="editGuestTextarea" maxlength="1000" class="form-control" rows="4" required style="border-radius: 12px; border: 2px solid var(--border);"></textarea>
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
    window.addEventListener('load', function() { 
        const c = document.getElementById('chatContainer'); 
        if (c) c.scrollTop = c.scrollHeight; 
    });

    function openEditModal(id, text) {
        document.getElementById('editGuestForm').action = '/hubungi-admin/' + id;
        document.getElementById('editGuestTextarea').value = text;
        new bootstrap.Modal(document.getElementById('editGuestModal')).show();
    }
</script>
@endpush
@endsection