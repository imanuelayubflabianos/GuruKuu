@extends('layouts.admin')
@section('title', 'Ruang Chat - ' . $senderName)

@section('content')
<style>
    .chat-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 200px);
        min-height: 480px;
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid var(--border, #e2e8f0);
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    }
    [data-theme="dark"] .chat-container {
        background: #111a2e;
        border-color: rgba(255, 255, 255, 0.1);
    }
    .chat-header {
        padding: 1rem 1.5rem;
        background: var(--bg-card, #ffffff);
        border-bottom: 1px solid var(--border, #e2e8f0);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    [data-theme="dark"] .chat-header {
        background: #152238;
        border-color: rgba(255, 255, 255, 0.1);
    }
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
        background: var(--bg-light, #f8fafc);
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }
    [data-theme="dark"] .chat-messages {
        background: #0b1329;
    }
    .chat-bubble-wrapper {
        display: flex;
        flex-direction: column;
        max-width: 78%;
    }
    .chat-bubble-incoming {
        align-self: flex-start;
    }
    .chat-bubble-incoming .bubble-body {
        background: #ffffff;
        color: var(--text-dark, #0f172a);
        border: 1px solid var(--border, #e2e8f0);
        border-radius: 16px 16px 16px 4px;
        padding: 0.85rem 1.15rem;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03);
    }
    [data-theme="dark"] .chat-bubble-incoming .bubble-body {
        background: #1e293b;
        color: #f1f5f9;
        border-color: rgba(255, 255, 255, 0.1);
    }
    .chat-bubble-outgoing {
        align-self: flex-end;
    }
    .chat-bubble-outgoing .bubble-body {
        background: linear-gradient(135deg, var(--primary, #003366) 0%, #004d99 100%);
        color: #ffffff;
        border-radius: 16px 16px 4px 16px;
        padding: 0.85rem 1.15rem;
        box-shadow: 0 4px 12px rgba(0, 51, 102, 0.2);
    }
    [data-theme="dark"] .chat-bubble-outgoing .bubble-body {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    }
    .chat-footer {
        padding: 1rem 1.25rem;
        background: var(--bg-card, #ffffff);
        border-top: 1px solid var(--border, #e2e8f0);
    }
    [data-theme="dark"] .chat-footer {
        background: #152238;
        border-color: rgba(255, 255, 255, 0.1);
    }
</style>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div>
        <div class="page-label">KOMUNIKASI LANGSUNG</div>
        <h1 class="page-title fs-4 mb-0">Percakapan dengan {{ $senderName }}</h1>
    </div>
    <div>
        <a href="{{ route('admin.kontak.index') }}" class="btn btn-outline-custom btn-sm d-inline-flex align-items-center gap-1.5">
            <i class="bi bi-arrow-left"></i>
            <span>Kembali ke Daftar Pesan</span>
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

<div class="chat-container">
    {{-- HEADER PERCAKAPAN --}}
    <div class="chat-header">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 1.1rem;">
                {{ strtoupper(substr($senderName, 0, 1)) }}
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-dark">{{ $senderName }}</h6>
                <div class="d-flex align-items-center gap-2 mt-0.5">
                    <span class="badge bg-light text-secondary border font-mono" style="font-size: 0.72rem;">
                        <i class="bi bi-shield-check text-success me-0.5"></i> {{ $roleLabel }}
                    </span>
                    @if($userObj && $userObj->nama_kelas)
                        <span class="badge bg-light text-muted border font-mono" style="font-size: 0.72rem;">
                            Kelas: {{ $userObj->nama_kelas }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 text-muted small font-mono">
            <i class="bi bi-lock-fill text-success"></i>
            <span>Pesan Terenkripsi Aman</span>
        </div>
    </div>

    {{-- STREAM PESAN (THREAD BUBBLES) --}}
    <div class="chat-messages" id="chatMessagesStream">
        @forelse($riwayat as $item)
            {{-- PESAN DARI PENGGUNA (KIRI) --}}
            <div class="chat-bubble-wrapper chat-bubble-incoming">
                <div class="d-flex align-items-center gap-1.5 mb-1 px-1">
                    <span class="small fw-bold text-dark font-mono" style="font-size: 0.75rem;">{{ $item->pengirim }}</span>
                    <span class="text-muted font-mono" style="font-size: 0.7rem;">&bull; {{ $item->created_at->format('d M, H:i') }} WIB</span>
                </div>
                <div class="bubble-body">
                    <p class="mb-0" style="line-height: 1.5; word-wrap: break-word;">
                        @if($item->pesan === '[Pesan Dihapus]')
                            <span class="fst-italic text-muted">[Pesan Dihapus]</span>
                        @else
                            {{ $item->pesan }}
                        @endif
                    </p>
                </div>
            </div>

            {{-- BALASAN DARI ADMIN JIKA ADA (KANAN) --}}
            @if($item->balasan)
                <div class="chat-bubble-wrapper chat-bubble-outgoing">
                    <div class="d-flex align-items-center justify-content-end gap-1.5 mb-1 px-1">
                        <span class="badge bg-primary text-white font-mono" style="font-size: 0.68rem;">Administrator</span>
                        <span class="text-muted font-mono" style="font-size: 0.7rem;">
                            {{ $item->updated_at->format('d M, H:i') }} WIB
                        </span>
                    </div>
                    <div class="bubble-body">
                        <p class="mb-0" style="line-height: 1.5; word-wrap: break-word;">
                            {{ $item->balasan }}
                        </p>
                    </div>
                </div>
            @endif
        @empty
            <div class="my-auto text-center text-muted py-5">
                <i class="bi bi-chat-dots fs-1 d-block mb-2 opacity-50"></i>
                <p>Belum ada pesan dalam riwayat percakapan ini.</p>
            </div>
        @endforelse
    </div>

    {{-- INPUT KIRIM BALASAN CEPAT --}}
    <div class="chat-footer">
        <form action="{{ route('admin.kontak.chat.send', $identifier) }}" method="POST" class="m-0" id="chatReplyForm">
            @csrf
            <div class="d-flex flex-column gap-2">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="small text-muted fw-bold font-mono" style="font-size: 0.75rem;">
                        <i class="bi bi-reply-fill text-primary"></i> Balas ke {{ $senderName }}:
                    </span>
                    <span id="chatCharCounter" class="badge bg-light text-muted border font-mono" style="font-size: 0.72rem;">0 / 100</span>
                </div>
                <div class="d-flex gap-2 align-items-end">
                    <textarea name="balasan" 
                              id="chatInputText" 
                              class="form-control" 
                              rows="2" 
                              maxlength="100" 
                              required 
                              placeholder="Tuliskan jawaban administrator... (maks. 100 karakter, tanpa link luar)"
                              style="resize: none; border-radius: 10px;"></textarea>
                    <button type="submit" class="btn btn-primary-custom px-4 py-2.5 rounded-3 d-inline-flex align-items-center gap-1.5 fw-semibold flex-shrink-0" style="height: fit-content;">
                        <span>Kirim</span>
                        <i class="bi bi-send-fill"></i>
                    </button>
                </div>
                <div id="charLimitWarning" class="text-danger small fw-bold" style="display: none; font-size: 0.72rem;">
                    <i class="bi bi-exclamation-circle-fill me-1"></i> Batas maksimal 100 karakter telah tercapai!
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
            if (counter) counter.textContent = `${len} / 100`;
            if (warning) warning.style.display = len >= 100 ? 'block' : 'none';
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
