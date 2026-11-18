@extends('layouts.admin')
@section('title', 'Pengaturan Akun Wakil')

@section('content')
<style>
    @media (max-width: 767.98px) {
        #wakilPengaturanTabs {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 0.4rem;
            gap: 0.45rem;
            scrollbar-width: none;
        }
        #wakilPengaturanTabs::-webkit-scrollbar {
            display: none;
        }
        #wakilPengaturanTabs .nav-item {
            flex: 0 0 auto;
        }
        #wakilPengaturanTabs .nav-link {
            white-space: nowrap !important;
            padding: 0.55rem 0.9rem;
            font-size: 0.84rem;
            border-radius: 0.65rem;
        }
    }
</style>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
    <div>
        <h1 class="page-title mb-1">Pengaturan Akun Wakil</h1>
        <p class="page-subtitle mb-0">Kelola akun, keamanan sesi, riwayat perangkat, dan pusat bantuan.</p>
    </div>
</div>

{{-- NAV PILLS --}}
<ul class="nav nav-pills mb-4" id="wakilPengaturanTabs" role="tablist">
    <li class="nav-item">
        <button class="nav-link active" id="info-tab" data-bs-toggle="pill" data-bs-target="#tabInfo" type="button">
            <i class="bi bi-person-badge"></i> Akun & Keamanan
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" id="device-tab" data-bs-toggle="pill" data-bs-target="#tabDevice" type="button">
            <i class="bi bi-laptop"></i> Riwayat Perangkat
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" id="faq-tab" data-bs-toggle="pill" data-bs-target="#tabFaq" type="button">
            <i class="bi bi-question-circle"></i> FAQ
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" id="chat-tab" data-bs-toggle="pill" data-bs-target="#tabChat" type="button">
            <i class="bi bi-headset"></i> Chat Admin
            @if(isset($pesan) && $pesan->whereNotNull('balasan')->count() > 0)
                <span class="badge bg-success ms-1">{{ $pesan->whereNotNull('balasan')->count() }} Balasan</span>
            @endif
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" id="legal-tab" data-bs-toggle="pill" data-bs-target="#tabLegal" type="button">
            <i class="bi bi-shield-lock"></i> Kebijakan & Privasi
        </button>
    </li>
</ul>

<div class="tab-content mb-4">
    {{-- TAB 1: INFORMASI AKUN & GANTI PASSWORD --}}
    <div class="tab-pane fade show active" id="tabInfo" role="tabpanel">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card-custom p-4 text-center">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3 text-white fw-bold shadow-sm" style="width: 72px; height: 72px; background: linear-gradient(135deg, #0d6efd, #003366); font-size: 1.6rem;">
                        WS
                    </div>
                    <h5 class="fw-bold mb-1">{{ auth()->user()->name }}</h5>
                    <span class="badge bg-info-subtle text-info border font-mono px-3 py-1 mb-3">Role: Wakil</span>
                    <div class="text-muted small border-top pt-3 text-start">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Username / NIS:</span>
                            <strong class="font-mono text-dark">{{ auth()->user()->nis }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Email Akun:</span>
                            <span class="font-mono text-dark">{{ auth()->user()->email ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Status Akun:</span>
                            <span class="badge bg-success-subtle text-success">Aktif</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card-custom p-4">
                    <h5 class="fw-bold mb-3 d-flex align-items-center text-primary">
                        <i class="bi bi-shield-lock-fill me-2"></i>Perbarui Kata Sandi Akun
                    </h5>
                    <p class="text-muted small mb-4">Ganti kata sandi login secara berkala untuk menjaga keamanan akses dashboard monitoring Wakil.</p>

                    <form action="{{ route('admin.pengaturan.wakil.password') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Password Saat Ini</label>
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Masukkan password sekarang" required>
                            @error('current_password')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Password Baru</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter" required>
                            @error('password')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-semibold">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru" required>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary-custom px-4">
                                <i class="bi bi-key-fill me-1"></i> Perbarui Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 2: RIWAYAT PERANGKAT --}}
    <div class="tab-pane fade" id="tabDevice" role="tabpanel">
        @include('components.device-history')
    </div>

    {{-- TAB 3: FAQ --}}
    <div class="tab-pane fade" id="tabFaq" role="tabpanel">
        @include('components.faq-accordion')
    </div>

    {{-- TAB 4: CHAT ADMIN (MESSENGER MODERN) --}}
    <div class="tab-pane fade" id="tabChat" role="tabpanel">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                {{-- HEADER CHAT DENGAN ADMIN --}}
                <div class="card-custom mb-3" style="overflow: hidden;">
                    <div class="p-3 d-flex align-items-center gap-3" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white;">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); flex-shrink: 0;">
                            <i class="bi bi-headset fs-4 text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-0 text-white">Administrator {{ $siteTitle ?? 'GuruKuu' }}</h6>
                            <small style="opacity: 0.9;"><i class="bi bi-circle-fill text-success me-1" style="font-size: 0.5rem;"></i> Online • Siap membantu koordinasi pengawasan & data evaluasi</small>
                        </div>
                        <div class="d-none d-md-block">
                            <span class="badge bg-light text-dark px-3 py-2">
                                <i class="bi bi-shield-lock-fill me-1 text-primary"></i> Jalur Komunikasi Resmi
                            </span>
                        </div>
                    </div>
                </div>

                {{-- AREA CHAT MESSENGER --}}
                <div class="card-custom mb-4" style="overflow: hidden; display: flex; flex-direction: column; height: 60vh; border: 1px solid var(--border);">
                    {{-- Area Pesan Chat (Scrollable) --}}
                    <div class="flex-grow-1 p-4" style="overflow-y: auto; background: var(--bg-card, #f8fafc);" id="wakilChatContainer">
                        @if($pesan->isEmpty())
                            <div class="text-center py-5">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 70px; height: 70px; background: rgba(0,51,102,0.08);">
                                    <i class="bi bi-chat-square-dots-fill fs-2" style="color: var(--primary);"></i>
                                </div>
                                <h6 class="fw-bold mb-1">Mulai Percakapan dengan Administrator</h6>
                                <p class="text-muted small mb-0">Sampaikan koordinasi, pertanyaan, atau masukan ke admin sekolah melalui pesan di bawah.</p>
                            </div>
                        @else
                            @foreach($pesan as $chat)
                                {{-- PESAN WAKIL (KANAN) --}}
                                <div class="d-flex justify-content-end mb-3">
                                    <div style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white; padding: 0.85rem 1.25rem; border-radius: 18px 18px 4px 18px; max-width: 75%; box-shadow: 0 4px 12px rgba(0,51,102,0.15); position: relative;">
                                        @if($chat->pesan === '[Pesan Dihapus]')
                                            <p class="mb-1 fst-italic d-flex align-items-center gap-1.5" style="font-size: 0.88rem; opacity: 0.8; color: rgba(255,255,255,0.85);">
                                                <i class="bi bi-ban"></i>
                                                <span>Pesan ini telah dihapus</span>
                                            </p>
                                        @else
                                            <p class="mb-1" style="line-height: 1.5; word-wrap: break-word;">{{ $chat->pesan }}</p>
                                        @endif
                                        <div class="text-end mt-2 d-flex justify-content-end gap-2 align-items-center" style="font-size: 0.7rem; opacity: 0.9;">
                                            <span>{{ $chat->created_at->format('H:i') }}</span>
                                            @if($chat->is_replied)
                                                <i class="bi bi-check2-all" style="color: #38bdf8; font-weight: bold;" title="Telah dibalas admin"></i>
                                            @else
                                                <i class="bi bi-check2-all" style="color: rgba(255,255,255,0.7);" title="Terkirim"></i>
                                            @endif
                                            @if($chat->pesan !== '[Pesan Dihapus]')
                                                <button type="button" class="btn btn-sm p-0 text-white border-0 ms-1" onclick="openEditModalWakil({{ $chat->id }}, '{{ addslashes($chat->pesan) }}')" title="Edit Pesan">
                                                    <i class="bi bi-pencil-fill" style="font-size: 0.72rem;"></i>
                                                </button>
                                                <form action="{{ route('admin.pengaturan.wakil.chat.destroy', $chat) }}" method="POST" class="d-inline" data-confirm="Hapus pesan ini?" data-confirm-title="Hapus Pesan?" data-confirm-btn="Ya, Hapus" data-confirm-type="danger">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm p-0 text-white border-0" title="Hapus Pesan">
                                                        <i class="bi bi-trash-fill" style="font-size: 0.72rem;"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- BALASAN ADMIN (KIRI) --}}
                                @if($chat->balasan)
                                <div class="d-flex justify-content-start mb-3">
                                    <div class="d-flex align-items-start gap-2" style="max-width: 75%;">
                                        <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 36px; height: 36px; background: var(--primary); color: white;">
                                            <i class="bi bi-headset" style="font-size: 0.95rem;"></i>
                                        </div>
                                        <div style="background: white; border: 1px solid var(--border); padding: 0.85rem 1.25rem; border-radius: 18px 18px 18px 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                                            <div class="fw-bold mb-1" style="color: var(--primary); font-size: 0.8rem;">
                                                <i class="bi bi-patch-check-fill me-1 text-primary"></i> Administrator Sekolah
                                            </div>
                                            @if($chat->balasan === '[Pesan Dihapus]')
                                                <p class="mb-1 fst-italic text-muted d-flex align-items-center gap-1.5" style="font-size: 0.88rem; opacity: 0.8;">
                                                    <i class="bi bi-ban"></i>
                                                    <span>Pesan ini telah dihapus</span>
                                                </p>
                                            @else
                                                <p class="mb-1" style="line-height: 1.5; word-wrap: break-word;">{{ $chat->balasan }}</p>
                                            @endif
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

                    {{-- FORM INPUT PESAN & VERIFIKASI ANTI-SPAM --}}
                    <div class="border-top p-3" style="background: white;">
                        <form action="{{ route('admin.pengaturan.wakil.chat') }}" method="POST">
                            @csrf
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label small fw-bold mb-0 text-muted">KIRIM PESAN KOORDINASI</label>
                                <span class="font-mono text-muted small" id="wakilChatCounter" style="font-size: 0.72rem;">0 / 255</span>
                            </div>
                            <div class="d-flex gap-2 align-items-end mb-2">
                                <textarea name="pesan" id="wakilChatInput" maxlength="255" class="form-control" rows="2" placeholder="Tulis pesan atau masukan koordinasi di sini (maks. 255 karakter)..." required style="border-radius: 12px; resize: none; border: 2px solid var(--border);" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'">{{ old('pesan') }}</textarea>
                                <button type="submit" class="btn btn-primary-custom d-flex align-items-center justify-content-center" style="height: 48px; width: 48px; border-radius: 12px; padding: 0;" title="Kirim Pesan">
                                    <i class="bi bi-send-fill fs-5"></i>
                                </button>
                            </div>
                            <div class="d-flex align-items-center gap-2 pt-1">
                                <div class="badge bg-light text-dark border px-2.5 py-1.5 font-mono" style="font-size: 0.78rem;">
                                    <i class="bi bi-shield-check text-primary me-1"></i> Verifikasi Anti-Spam: <strong>{{ $num1 }} + {{ $num2 }} = ?</strong>
                                </div>
                                <div style="max-width: 110px;">
                                    <input type="number" name="captcha" class="form-control form-control-sm text-center font-mono @error('captcha') is-invalid @enderror" placeholder="Hasil" required style="border-radius: 8px;">
                                </div>
                                @error('captcha')
                                    <small class="text-danger" style="font-size: 0.75rem;">{{ $message }}</small>
                                @enderror
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 5: KEBIJAKAN & PRIVASI --}}
    <div class="tab-pane fade" id="tabLegal" role="tabpanel">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card-custom p-4 h-100">
                    <h5 class="fw-bold mb-3 d-flex align-items-center text-primary">
                        <i class="bi bi-shield-lock-fill me-2"></i>Kebijakan Privasi
                    </h5>
                    <p class="text-muted small mb-3">Ketentuan perlindungan data dan privasi penilaian siswa.</p>
                    <div class="p-3 bg-light rounded border text-muted small" style="line-height: 1.8; max-height: 480px; overflow-y: auto;">
                        @php
                            $privacy = \App\Models\Setting::get('kebijakan_privasi', "1. Pengumpulan Data\nKami hanya mengumpulkan data yang diperlukan untuk proses penilaian, yaitu NIS, nama, dan kelas siswa. Data pribadi seperti tanggal lahir hanya digunakan untuk verifikasi identitas saat login.\n\n2. Anonimitas Penilaian\nSeluruh penilaian yang diberikan siswa bersifat anonim bagi guru, sesama siswa, dan akun monitoring Wakil untuk menjamin objektivitas dan independensi.\n\n3. Penyimpanan Data\nSemua data disimpan di server yang aman dengan enkripsi standar industri. Password pengguna di-hash menggunakan algoritma bcrypt yang tidak dapat dibaca kembali.\n\n4. Penggunaan Data\nData penilaian hanya digunakan untuk keperluan internal sekolah, seperti evaluasi kinerja guru dan pengambilan keputusan oleh manajemen. Data tidak akan dibagikan kepada pihak ketiga tanpa persetujuan.");
                        @endphp
                        {!! nl2br(e($privacy)) !!}
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card-custom p-4 h-100">
                    <h5 class="fw-bold mb-3 d-flex align-items-center text-primary">
                        <i class="bi bi-file-earmark-text-fill me-2"></i>Syarat & Ketentuan
                    </h5>
                    <p class="text-muted small mb-3">Aturan penggunaan platform evaluasi {{ $siteTitle ?? 'GuruKuu' }}.</p>
                    <div class="p-3 bg-light rounded border text-muted small" style="line-height: 1.8; max-height: 480px; overflow-y: auto;">
                        @php
                            $terms = \App\Models\Setting::get('syarat_ketentuan', "1. Eligibilitas\nPlatform ini hanya dapat digunakan oleh warga sekolah yang terdaftar resmi. Akun harus diaktifkan oleh administrator sekolah sebelum dapat digunakan.\n\n2. Tanggung Jawab Pengguna\nSeluruh evaluasi dan ulasan wajib disampaikan secara santun, beretika, dan bertanggung jawab tanpa mengandung ujaran kebencian.\n\n3. Keamanan Akun\nPengguna bertanggung jawab penuh atas kerahasiaan kata sandi akun masing-masing.\n\n4. Kontak & Pengaduan\nJika Anda menemukan kendala atau permohonan koordinasi, silakan hubungi administrator sekolah melalui fitur Chat Admin.");
                        @endphp
                        {!! nl2br(e($terms)) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT PESAN WAKIL --}}
<div class="modal fade" id="editModalWakil" tabindex="-1" aria-labelledby="editModalWakilLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <form id="editFormWakil" method="POST">
                @csrf @method('PUT')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="editModalWakilLabel"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Pesan Chat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <textarea name="pesan" id="editTextareaWakil" class="form-control" rows="3" maxlength="255" required style="border-radius: 12px; border: 2px solid var(--border);"></textarea>
                    <div class="form-text small text-muted">Maksimal 255 karakter.</div>
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

{{-- DEDICATED LOGOUT CARD (SESI AKUN WAKIL) --}}
<div class="card-custom p-4 border-danger border-opacity-25 mb-4" style="background: #fffafa;">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h5 class="fw-bold text-danger mb-1"><i class="bi bi-box-arrow-right me-2"></i>Keluar Sesi Akun Wakil</h5>
            <p class="text-muted small mb-0">Pastikan Anda telah selesai memantau data peringkat dan koordinasi sebelum keluar, terutama di komputer bersama.</p>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-danger px-4 py-2 fw-semibold">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function scrollWakilChat() {
        const container = document.getElementById('wakilChatContainer');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    function openEditModalWakil(id, pesan) {
        const form = document.getElementById('editFormWakil');
        const textarea = document.getElementById('editTextareaWakil');
        if (form && textarea) {
            form.action = '{{ url("/admin/pengaturan/wakil/chat") }}/' + id;
            textarea.value = pesan;
            const modal = new bootstrap.Modal(document.getElementById('editModalWakil'));
            modal.show();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Tab routing via hash
        const activeHash = window.location.hash;
        if (activeHash) {
            const triggerEl = document.querySelector(`#wakilPengaturanTabs button[data-bs-target="${activeHash}"]`);
            if (triggerEl && window.bootstrap && window.bootstrap.Tab) {
                const tab = new bootstrap.Tab(triggerEl);
                tab.show();
            }
            if (activeHash === '#tabChat') {
                setTimeout(scrollWakilChat, 150);
            }
        }

        // Auto-scroll when Chat tab is clicked
        const chatTabBtn = document.getElementById('chat-tab');
        if (chatTabBtn) {
            chatTabBtn.addEventListener('shown.bs.tab', function () {
                setTimeout(scrollWakilChat, 100);
            });
        }

        // Live character counter for chat input
        const chatInput = document.getElementById('wakilChatInput');
        const chatCounter = document.getElementById('wakilChatCounter');
        if (chatInput && chatCounter) {
            chatInput.addEventListener('input', function () {
                chatCounter.innerText = this.value.length + ' / 255';
            });
            chatCounter.innerText = chatInput.value.length + ' / 255';
        }
    });
</script>
@endpush
