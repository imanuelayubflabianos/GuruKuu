@extends('layouts.landing')
@section('title', 'Hubungi Admin')

@section('content')
<div style="padding-top: 120px; padding-bottom: 80px; background: var(--bg-light); min-height: 80vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h2 class="fw-bold" style="color: var(--primary);">Hubungi Admin</h2>
                    <p class="text-muted">Punya masalah saat login atau pertanyaan lainnya? Kirim pesan di sini.</p>
                </div>

                {{-- NOTIFIKASI SUKSES --}}
                @if(session('success'))
                <div class="alert alert-success border-0 mb-4">
                    <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
                </div>
                @endif

                {{-- AREA RIWAYAT CHAT --}}
                @if($riwayat->isNotEmpty())
                <div class="card-custom p-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2"></i>Riwayat Percakapan Anda</h5>
                    <div style="max-height: 400px; overflow-y: auto; padding-right: 10px;" id="chatHistory">
                        @foreach($riwayat as $chat)
                            {{-- Pesan Tamu (Kanan) --}}
                            <div class="d-flex justify-content-end mb-3">
                                <div style="background: var(--primary); color: white; padding: 1rem; border-radius: 12px 12px 0 12px; max-width: 80%;">
                                    <p class="mb-1">{{ $chat->pesan }}</p>
                                    <small style="opacity: 0.8;">{{ $chat->created_at->format('d M Y, H:i') }}</small>
                                </div>
                            </div>

                            {{-- Balasan Admin (Kiri) --}}
                            @if($chat->is_replied && $chat->balasan)
                            <div class="d-flex justify-content-start mb-3">
                                <div style="background: white; border: 1px solid var(--border); padding: 1rem; border-radius: 12px 12px 12px 0; max-width: 80%;">
                                    <div class="fw-bold mb-1" style="color: var(--primary); font-size: 0.85rem;">
                                        <i class="bi bi-person-badge"></i> Admin
                                    </div>
                                    <p class="mb-1">{{ $chat->balasan }}</p>
                                    <small class="text-muted">{{ $chat->updated_at->format('d M Y, H:i') }}</small>
                                </div>
                            </div>
                            @else
                            <div class="d-flex justify-content-start mb-3">
                                <div style="background: #fef3c7; padding: 0.75rem 1rem; border-radius: 12px 12px 12px 0; font-size: 0.85rem; color: #92400e;">
                                    <i class="bi bi-hourglass-split me-1"></i> Menunggu balasan admin...
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @else
                <div class="card-custom p-4 mb-4 text-center text-muted">
                    <i class="bi bi-chat-square-text fs-1 d-block mb-2"></i>
                    <p class="mb-0">Belum ada riwayat percakapan. Silakan kirim pesan di bawah ini.</p>
                </div>
                @endif

                {{-- AREA FORM KIRIM PESAN --}}
                <div class="card-custom p-4">
                    @if($errors->any())
                        <div class="alert alert-danger border-0 mb-3">
                            @foreach($errors->all() as $error)
                                <div class="small"><i class="bi bi-exclamation-triangle"></i> {{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('kontak.guest.store') }}" method="POST">
                        @csrf
                        {{-- ✅ HIDDEN FIELD: Backup device ID jika cookie gagal --}}
                        <input type="hidden" name="_device_id" value="{{ $deviceId }}">
                        
                        <div class="mb-3">
                            <label class="form-label font-mono" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 1px;">PESAN ANDA</label>
                            <textarea name="pesan" class="form-control" rows="4" placeholder="Jelaskan masalah Anda (misal: Tidak bisa login dengan NIS...)..." required style="border-radius: 8px;">{{ old('pesan') }}</textarea>
                            <small class="text-muted">* Identitas Anda akan disimpan secara anonim berdasarkan perangkat ini.</small>
                            @error('pesan')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        {{-- CAPTCHA MATEMATIKA --}}
                        <div class="mb-3">
                            <label class="form-label font-mono" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 1px;">VERIFIKASI: Berapa {{ $num1 }} + {{ $num2 }}?</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="number" name="captcha" class="form-control" placeholder="Jawaban..." required style="border-radius: 8px; max-width: 150px;" value="{{ old('captcha') }}">
                                <small class="text-muted">* Untuk mencegah spam otomatis.</small>
                            </div>
                            @error('captcha')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn btn-primary-custom w-100">
                            <i class="bi bi-send"></i> Kirim Pesan
                        </button>
                    </form>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('landing.index') }}" class="text-muted text-decoration-none">
                        <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto scroll ke bawah saat halaman dimuat agar pesan terbaru terlihat
    window.onload = function() {
        const chatHistory = document.getElementById('chatHistory');
        if (chatHistory) {
            chatHistory.scrollTop = chatHistory.scrollHeight;
        }
    };
</script>
@endpush
@endsection