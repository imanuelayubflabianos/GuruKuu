@extends('layouts.admin')
@section('title', 'Ruang Chat - ' . $senderName)

@section('content')
<style>
    .chat-wrapper {
        border-radius: 16px;
        overflow: hidden;
    }
    .chat-stream-box {
        height: 55vh;
        min-height: 420px;
        overflow-y: auto;
        background: #f8fafc;
        border: 1px solid var(--border, #e2e8f0);
        border-bottom: none;
        border-radius: 16px 16px 0 0;
        padding: 1.5rem;
    }
    [data-theme="dark"] .chat-stream-box {
        background: #0b1329;
        border-color: rgba(255, 255, 255, 0.1);
    }
    .chat-footer-box {
        background: #ffffff;
        border: 1px solid var(--border, #e2e8f0);
        border-radius: 0 0 16px 16px;
        padding: 1rem 1.25rem;
    }
    [data-theme="dark"] .chat-footer-box {
        background: #152238;
        border-color: rgba(255, 255, 255, 0.1);
    }
    .bubble-admin {
        background: linear-gradient(135deg, var(--primary, #003366) 0%, #004d99 100%);
        color: white;
        padding: 0.85rem 1.15rem;
        border-radius: 18px 18px 4px 18px;
        max-width: 82%;
        box-shadow: 0 4px 12px rgba(0, 51, 102, 0.18);
    }
    .bubble-user {
        background: white;
        color: var(--text-dark, #0f172a);
        border: 1px solid var(--border, #e2e8f0);
        padding: 0.85rem 1.15rem;
        border-radius: 18px 18px 18px 4px;
        max-width: 82%;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }
    [data-theme="dark"] .bubble-user {
        background: #1e293b;
        color: #f1f5f9;
        border-color: rgba(255, 255, 255, 0.1);
    }
</style>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div>
        <div class="page-label">KOMUNIKASI LANGSUNG</div>
        <h1 class="page-title fs-4 mb-0">Percakapan dengan {{ $senderName }}</h1>
    </div>
    <div>
        <a href="{{ route('admin.kontak.index') }}" class="gk-btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Pesan
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error') || $errors->any())
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
        <i class="bi bi-exclamation-octagon-fill fs-5"></i>
        <div>{{ session('error') ?? $errors->first() }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- HEADER CHAT IDENTIK DENGAN GAMBAR 4 / PUSAT BANTUAN --}}
<div class="card-custom mb-3 overflow-hidden shadow-sm" style="border-radius: 14px; border: none;">
    <div class="p-3 d-flex align-items-center gap-3" style="background: linear-gradient(135deg, var(--primary, #003366) 0%, #004d99 100%); color: white;">
        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); flex-shrink: 0;">
            <i class="bi bi-headset fs-4 text-white"></i>
        </div>
        <div class="flex-grow-1">
            <h6 class="fw-bold mb-0 text-white">{{ $senderName }}</h6>
            <small style="opacity: 0.92;">
                <i class="bi bi-circle-fill text-success me-1" style="font-size: 0.5rem;"></i>
                Online • {{ $roleLabel }} {{ $userObj && $userObj->nama_kelas ? '• ' . $userObj->nama_kelas : '' }}
            </small>
        </div>
        <div>
            <span class="badge bg-light text-dark px-2.5 py-1.5 rounded-pill font-mono" style="font-size: 0.72rem;">
                <i class="bi bi-shield-lock-fill me-1 text-success"></i> Terenkripsi
            </span>
        </div>
    </div>
</div>

{{-- AREA PERCAKAPAN & FORM INPUT (IDENTIK DENGAN GAMBAR 4) --}}
<div class="chat-wrapper shadow-sm">
    {{-- AREA STREAM PESAN --}}
    <div class="chat-stream-box" id="chatMessagesStream">
        @forelse($riwayat as $item)
            {{-- PESAN PENGIRIM (SISWA / TAMU) --}}
            <div class="d-flex justify-content-start mb-3">
                <div class="d-flex align-items-start gap-2" style="max-width: 82%;">
                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 36px; height: 36px; background: rgba(0, 51, 102, 0.1); color: var(--primary, #003366); font-weight: 700; font-size: 0.85rem;">
                        {{ strtoupper(substr($senderName, 0, 1)) }}
                    </div>
                    <div>
                        <div class="bubble-user">
                            <div class="fw-bold mb-1" style="color: var(--primary, #003366); font-size: 0.78rem;">
                                {{ $item->pengirim }}
                            </div>
                            @if($item->pesan === '[Pesan Dihapus]')
                                <p class="mb-1 fst-italic text-muted small"><i class="bi bi-trash me-1"></i>[Pesan Dihapus]</p>
                            @else
                                <p class="mb-1" style="line-height: 1.5; word-wrap: break-word; font-size: 0.92rem;">
                                    {{ $item->pesan }}
                                </p>
                            @endif
                            <div class="text-end text-muted font-mono" style="font-size: 0.7rem;">
                                {{ $item->created_at->format('H:i') }} WIB
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- JIKA SUDAH ADA BALASAN DARI ADMIN --}}
            @if($item->is_replied && $item->balasan)
                <div class="d-flex justify-content-end mb-3">
                    <div class="bubble-admin">
                        <div class="d-flex align-items-center justify-content-between gap-3 mb-1" style="font-size: 0.75rem; opacity: 0.9;">
                            <span class="fw-bold"><i class="bi bi-patch-check-fill me-1 text-info"></i> Administrator</span>
                            <span>{{ $item->updated_at->format('H:i') }} WIB</span>
                        </div>
                        <p class="mb-1" style="line-height: 1.5; word-wrap: break-word; font-size: 0.92rem;">
                            {{ $item->balasan }}
                        </p>
                        <div class="text-end mt-1 d-flex justify-content-end align-items-center gap-1" style="font-size: 0.7rem; opacity: 0.9;">
                            <i class="bi bi-check2-all" style="color: #38bdf8; font-weight: bold; font-size: 0.9rem;" title="Terkirim & Dilihat"></i>
                        </div>
                    </div>
                </div>
            @elseif(!$item->is_replied && $item->pesan !== '[Pesan Dihapus]')
                <div class="d-flex justify-content-start mb-3 ms-5">
                    <div class="rounded-pill px-3 py-1 small" style="background: #fef3c7; color: #92400e; font-size: 0.75rem; border: 1px solid #fde68a;">
                        <i class="bi bi-hourglass-split me-1"></i> Menunggu balasan admin...
                    </div>
                </div>
            @endif
        @empty
            <div class="text-center py-5 text-muted">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 72px; height: 72px; background: rgba(0,51,102,0.08);">
                    <i class="bi bi-chat-square-text-fill fs-2" style="color: var(--primary, #003366);"></i>
                </div>
                <h6 class="fw-bold mb-1 text-dark">Belum Ada Pesan</h6>
                <p class="small mb-0">Belum ada riwayat pesan percakapan ini.</p>
            </div>
        @endforelse
    </div>

    {{-- FORM KIRIM BALASAN (PERSIS GAMBAR 4 DENGAN COUNTER & TOMBOL SENDFILL) --}}
    <div class="chat-footer-box">
        <form action="{{ route('admin.kontak.chat.send', $identifier) }}" method="POST" id="chatReplyForm" class="m-0">
            @csrf
            <div>
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label small fw-bold mb-0 text-muted" style="letter-spacing: 0.5px;">KIRIM BALASAN ADMINISTRATOR</label>
                    <span class="font-mono text-muted small" id="chatCharCounter" style="font-size: 0.72rem;">0 / 255</span>
                </div>
                <div class="d-flex gap-2 align-items-end mb-1">
                    <textarea name="balasan" 
                              id="chatInputText" 
                              maxlength="255" 
                              class="form-control" 
                              rows="2" 
                              placeholder="Tuliskan jawaban administrator... (maks. 255 karakter)" 
                              required 
                              style="border-radius: 12px; resize: none; border: 2px solid var(--border, #e2e8f0); transition: all 0.25s;"
                              onfocus="this.style.borderColor='var(--primary, #003366)'" 
                              onblur="this.style.borderColor='var(--border, #e2e8f0)'">{{ old('balasan') }}</textarea>
                    <button type="submit" 
                            class="btn btn-primary-custom d-flex align-items-center justify-content-center" 
                            style="height: 46px; width: 46px; border-radius: 12px; padding: 0; flex-shrink: 0;" 
                            title="Kirim Balasan">
                        <i class="bi bi-send-fill fs-5"></i>
                    </button>
                </div>
                <div id="charLimitWarning" class="form-text text-danger fw-bold d-none mb-0">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Anda telah mencapai batas maksimal 255 karakter!
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stream = document.getElementById('chatMessagesStream');
    if (stream) {
        stream.scrollTop = stream.scrollHeight;
    }

    const textInput = document.getElementById('chatInputText');
    const counter = document.getElementById('chatCharCounter');
    const warning = document.getElementById('charLimitWarning');

    if (textInput) {
        textInput.addEventListener('input', function() {
            const len = this.value.length;
            if (counter) counter.textContent = `${len} / 255`;
            if (warning) {
                if (len >= 255) {
                    warning.classList.remove('d-none');
                } else {
                    warning.classList.add('d-none');
                }
            }
        });

        // Submit form with Ctrl+Enter or Cmd+Enter
        textInput.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('chatReplyForm')?.submit();
            }
        });
    }
});
</script>
@endpush
@endsection
