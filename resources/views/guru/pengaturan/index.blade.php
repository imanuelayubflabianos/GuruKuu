@extends('layouts.guru')
@section('title', 'Pengaturan Profil & Akun')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <div class="page-label">MANAJEMEN GURU</div>
        <h1 class="page-title">Pengaturan Profil & Akun</h1>
        <p class="page-subtitle">Kelola informasi publik, foto pengajar, deskripsi diri, keamanan akun, dan komunikasi dengan admin.</p>
    </div>
</div>

{{-- NAV PILLS --}}
<ul class="nav nav-pills mb-4" id="guruPengaturanTabs" role="tablist">
    <li class="nav-item">
        <button class="nav-link active" id="profil-tab" data-bs-toggle="pill" data-bs-target="#tabProfil" type="button">
            <i class="bi bi-person-lines-fill"></i> Profil Pengajar
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" id="device-tab" data-bs-toggle="pill" data-bs-target="#tabDeviceGuru" type="button">
            <i class="bi bi-laptop"></i> Riwayat Perangkat
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" id="faq-tab" data-bs-toggle="pill" data-bs-target="#tabFaqGuru" type="button">
            <i class="bi bi-question-circle"></i> FAQ
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" id="chat-tab" data-bs-toggle="pill" data-bs-target="#tabChat" type="button">
            <i class="bi bi-chat-dots"></i> Chat Admin
            @if(isset($pesanChat) && $pesanChat->whereNotNull('balasan')->count() > 0)
                <span class="badge bg-success ms-1">{{ $pesanChat->whereNotNull('balasan')->count() }} Balasan</span>
            @endif
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" id="legal-tab" data-bs-toggle="pill" data-bs-target="#tabLegalGuru" type="button">
            <i class="bi bi-file-earmark-lock"></i> Kebijakan & Privasi
        </button>
    </li>
</ul>

<div class="tab-content mb-5">
    {{-- TAB 1: PROFIL & BIODATA GURU --}}
    <div class="tab-pane fade show active" id="tabProfil">
        <form action="{{ route('guru.pengaturan.profil') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-4">
                {{-- Foto Profil Guru --}}
                <div class="col-lg-4">
                    <div class="card-custom p-4 text-center h-100 d-flex flex-column justify-content-between">
                        <div>
                            <h6 class="fw-bold mb-3 font-mono text-muted text-uppercase" style="letter-spacing: 1px;">Foto Profil Pengajar</h6>
                            <div class="position-relative d-inline-block mb-3">
                                <img id="avatarPreview" src="{{ $guru->photo_url ?? $user->photo_url }}" class="shadow-sm" width="160" height="160" style="object-fit: cover; border-radius: 16px; border: 4px solid var(--primary);">
                            </div>
                            <p class="text-muted small mb-3">Gunakan foto resmi atau formal dengan format PNG/JPG (Maks 2MB).</p>
                        </div>
                        <div>
                            <input type="file" name="photo" id="photoInput" class="form-control form-control-sm" accept="image/png, image/jpeg, image/jpg, image/webp">
                        </div>
                    </div>
                </div>

                {{-- Form Biodata --}}
                <div class="col-lg-8">
                    <div class="card-custom p-4">
                        <h5 class="fw-bold mb-1" style="color: var(--text-dark);">
                            <i class="bi bi-person-vcard text-primary me-2"></i>Informasi & Deskripsi Pengajar
                        </h5>
                        <p class="text-muted small mb-3">Informasi profil ini ditampilkan kepada siswa. Sesuai kebijakan sekolah, data resmi dikelola oleh Admin/SiPintu.</p>

                        <div class="alert alert-info py-2 px-3 small border-0 d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-info-circle-fill text-primary"></i>
                            <span>Data resmi Nama, NIP, Jurusan, dan Email dikelola oleh Administrator/SiPintu. Anda dapat memperbarui <strong>Foto Profil</strong> dan <strong>Deskripsi Diri</strong> di bawah ini.</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">NIP / Identitas (Resmi) <i class="bi bi-lock-fill text-muted ms-1"></i></label>
                                <input type="text" class="form-control bg-light" value="{{ $guru->nip ?? $user->nis }}" readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Departemen / Jurusan <i class="bi bi-lock-fill text-muted ms-1"></i></label>
                                <input type="text" class="form-control bg-light" value="{{ $guru->jurusan?->nama_jurusan ?? 'Umum' }}" readonly disabled>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Nama Lengkap & Gelar <i class="bi bi-lock-fill text-muted ms-1"></i></label>
                                <input type="text" class="form-control bg-light" value="{{ $guru->nama ?? $user->name }}" readonly disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Alamat Email <i class="bi bi-lock-fill text-muted ms-1"></i></label>
                                <input type="email" class="form-control bg-light" value="{{ $guru->email ?? $user->email }}" readonly disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Nomor Kontak / WhatsApp <i class="bi bi-lock-fill text-muted ms-1"></i></label>
                                <input type="text" class="form-control bg-light" value="{{ $guru->phone ?? '-' }}" readonly disabled>
                            </div>

                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label small fw-bold mb-0">Deskripsi Diri & Tentang Saya <span class="text-primary">*</span></label>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="font-mono text-muted small" id="bioCounter">0 / 100</span>
                                        <button type="button" class="btn btn-link p-0 text-decoration-none small text-primary" onclick="useDefaultBio()">
                                            <i class="bi bi-arrow-repeat me-1"></i>Bawaan
                                        </button>
                                    </div>
                                </div>
                                <textarea name="bio" id="bioTextarea" class="form-control" rows="3" maxlength="100" placeholder="Tuliskan perkenalan singkat mengenai dedikasi mengajar (maks. 100 karakter)...">{{ old('bio', $guru->bio ?? $defaultBio) }}</textarea>
                                <div class="form-text small text-muted">Maksimal 100 karakter. Ditampilkan pada profil guru saat dinilai siswa.</div>
                                <div class="form-text text-danger fw-bold d-none" id="bioLimitWarn">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>Anda telah mencapai batas maksimal 100 karakter!
                                </div>
                            </div>

                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-primary-custom px-4">
                                    <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan Profil
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- TAB: RIWAYAT PERANGKAT GURU --}}
    <div class="tab-pane fade" id="tabDeviceGuru">
        @include('components.device-history')
    </div>

    {{-- TAB: FAQ GURU --}}
    <div class="tab-pane fade" id="tabFaqGuru">
        @include('components.faq-accordion')
    </div>

    {{-- TAB 2: CHAT ADMIN --}}
    <div class="tab-pane fade" id="tabChat">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                {{-- HEADER CHAT DENGAN ADMIN --}}
                <div class="card-custom mb-3" style="overflow: hidden;">
                    <div class="p-3 d-flex align-items-center gap-3" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white;">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px);">
                            <i class="bi bi-headset-fill fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-0 text-white">Administrator {{ $siteTitle ?? 'GuruKuu' }}</h6>
                            <small style="opacity: 0.9;"><i class="bi bi-circle-fill text-success me-1" style="font-size: 0.5rem;"></i> Online • Siap membantu kendala teknis & manajemen pengajaran</small>
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
                    <div class="flex-grow-1 p-4" style="overflow-y: auto; background: var(--bg-card);" id="guruChatContainer">
                        @if($pesan->isEmpty())
                            <div class="text-center py-5">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 70px; height: 70px; background: rgba(0,51,102,0.08);">
                                    <i class="bi bi-chat-square-dots-fill fs-2" style="color: var(--primary);"></i>
                                </div>
                                <h6 class="fw-bold mb-1">Mulai Percakapan dengan Admin Sekolah</h6>
                                <p class="text-muted small mb-0">Sampaikan pertanyaan, permintaan perubahan data, atau kendala portal di bawah ini.</p>
                            </div>
                        @else
                            @foreach($pesan as $chat)
                                {{-- PESAN GURU (KANAN) --}}
                                <div class="d-flex justify-content-end mb-3">
                                    <div style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white; padding: 0.85rem 1.25rem; border-radius: 18px 18px 4px 18px; max-width: 75%; box-shadow: 0 4px 12px rgba(0,51,102,0.15); position: relative;">
                                        @if($chat->pesan === '[Pesan Dihapus]')
                                            <p class="mb-1 fst-italic opacity-75"><i class="bi bi-trash me-1"></i>[Pesan Dihapus]</p>
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
                                                <button type="button" class="btn btn-sm p-0 text-white border-0 ms-1" onclick="openEditModalGuru({{ $chat->id }}, '{{ addslashes($chat->pesan) }}')" title="Edit Pesan">
                                                    <i class="bi bi-pencil-fill" style="font-size: 0.72rem;"></i>
                                                </button>
                                                <form action="{{ route('guru.pengaturan.chat.destroy', $chat) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pesan ini?')">
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
                                            <i class="bi bi-person-badge-fill" style="font-size: 0.9rem;"></i>
                                        </div>
                                        <div style="background: var(--bg-card); border: 1px solid var(--border); padding: 0.85rem 1.25rem; border-radius: 18px 18px 18px 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                                            <div class="fw-bold mb-1" style="color: var(--primary); font-size: 0.8rem;">
                                                <i class="bi bi-patch-check-fill me-1 text-primary"></i> Administrator Sekolah
                                            </div>
                                            <p class="mb-1" style="line-height: 1.5; word-wrap: break-word;">{{ $chat->balasan }}</p>
                                            <div class="text-end" style="font-size: 0.7rem; color: var(--text-muted);">
                                                {{ $chat->balasan_at ? \Carbon\Carbon::parse($chat->balasan_at)->format('H:i') : $chat->updated_at->format('H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        @endif
                    </div>

                    {{-- FORM INPUT PESAN & VERIFIKASI ANTI-SPAM --}}
                    <div class="border-top p-3" style="background: var(--bg-card);">
                        <form action="{{ route('guru.pengaturan.chat') }}" method="POST">
                            @csrf
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label small fw-bold mb-0 text-muted">KIRIM PESAN BANTUAN</label>
                                <span class="font-mono text-muted small" id="guruChatCounter" style="font-size: 0.72rem;">0 / 100</span>
                            </div>
                            <div class="d-flex gap-2 align-items-end mb-1">
                                <textarea name="pesan" id="guruChatInput" maxlength="100" class="form-control" rows="2" placeholder="Tulis pesan atau pertanyaan Anda di sini (maks. 100 karakter)..." required style="border-radius: 12px; resize: none; border: 2px solid var(--border);" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'">{{ old('pesan') }}</textarea>
                                <button type="submit" class="btn btn-primary-custom d-flex align-items-center justify-content-center" style="height: 48px; width: 48px; border-radius: 12px; padding: 0;" title="Kirim Pesan">
                                    <i class="bi bi-send-fill fs-5"></i>
                                </button>
                            </div>
                            <div class="form-text text-danger fw-bold d-none mb-2" id="guruChatLimitWarn">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>Anda telah mencapai batas maksimal 100 karakter!
                            </div>

                            {{-- KOTAK VERIFIKASI --}}
                            <div class="p-2 rounded d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: var(--bg-light); border: 1px dashed var(--border);">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-shield-check-fill text-success"></i>
                                    <label class="form-label mb-0 small fw-bold text-muted">VERIFIKASI: {{ $num1 }} + {{ $num2 }} =</label>
                                </div>
                                <div class="d-flex gap-2 align-items-center">
                                    <input type="number" name="captcha" class="form-control form-control-sm text-center fw-bold" placeholder="?" required style="width: 75px; border-radius: 8px; border: 2px solid var(--border);" value="{{ old('captcha') }}">
                                    <small class="text-muted" style="font-size: 0.72rem;">Verifikasi</small>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 3: KEBIJAKAN PRIVASI & SYARAT KETENTUAN --}}
    <div class="tab-pane fade" id="tabLegalGuru">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card-custom p-4 h-100">
                    <h5 class="fw-bold mb-3 d-flex align-items-center text-primary">
                        <i class="bi bi-shield-lock-fill me-2"></i>Kebijakan Privasi
                    </h5>
                    <p class="text-muted small mb-3">Ketentuan perlindungan data dan privasi penilaian guru & siswa.</p>
                    <div class="p-3 bg-light rounded border text-muted small" style="line-height: 1.8; max-height: 480px; overflow-y: auto;">
                        @php
                            $privacy = \App\Models\Setting::get('kebijakan_privasi', "1. Pengumpulan Data\nKami hanya mengumpulkan data yang diperlukan untuk proses penilaian, yaitu NIS, nama, dan kelas siswa. Data pribadi seperti tanggal lahir hanya digunakan untuk verifikasi identitas saat login.\n\n2. Anonimitas Penilaian\nSeluruh penilaian yang diberikan siswa bersifat anonim. Guru dan pihak lain tidak dapat mengetahui identitas siswa yang memberikan nilai tertentu. Ini menjamin kejujuran dan objektivitas dalam setiap penilaian.\n\n3. Penyimpanan Data\nSemua data disimpan di server yang aman dengan enkripsi standar industri. Password pengguna di-hash menggunakan algoritma bcrypt yang tidak dapat dibaca kembali.\n\n4. Penggunaan Data\nData penilaian hanya digunakan untuk keperluan internal sekolah, seperti evaluasi kinerja guru dan pengambilan keputusan oleh manajemen. Data tidak akan dibagikan kepada pihak ketiga tanpa persetujuan.");
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
                    <p class="text-muted small mb-3">Aturan penggunaan platform evaluasi {{ $siteTitle ?? 'GuruKuu' }} bagi tenaga pendidik.</p>
                    <div class="p-3 bg-light rounded border text-muted small" style="line-height: 1.8; max-height: 480px; overflow-y: auto;">
                        @php
                            $terms = \App\Models\Setting::get('syarat_ketentuan', "1. Eligibilitas\nPlatform ini hanya dapat digunakan oleh siswa dan guru yang terdaftar resmi di sekolah. Akun harus diaktifkan oleh administrator sekolah sebelum dapat digunakan.\n\n2. Tanggung Jawab Pengguna\nSiswa wajib memberikan penilaian secara jujur dan objektif. Dilarang memberikan penilaian berdasarkan dendam pribadi, SARA, atau konten yang tidak pantas.\n\n3. Keamanan Akun\nPengguna bertanggung jawab penuh atas kerahasiaan password akun mereka. Dilarang membagikan password kepada orang lain.\n\n4. Kontak & Pengaduan\nJika Anda menemukan pelanggaran atau memiliki keluhan, silakan hubungi administrator sekolah melalui fitur Chat Admin yang tersedia di footer website ini.");
                        @endphp
                        {!! nl2br(e($terms)) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT PESAN GURU --}}
<div class="modal fade" id="editModalGuru" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <form id="editFormGuru" method="POST">
                @csrf @method('PUT')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Pesan Chat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea name="pesan" id="editTextareaGuru" class="form-control" rows="3" maxlength="100" required style="border-radius: 12px; border: 2px solid var(--border);"></textarea>
                    <div class="form-text small text-muted">Maksimal 100 karakter.</div>
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

{{-- DEDICATED LOGOUT CARD (SESI AKUN) --}}
<div class="card-custom p-4 border-danger border-opacity-25" style="background: #fffafa;">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h5 class="fw-bold text-danger mb-1"><i class="bi bi-box-arrow-right me-2"></i>Keluar Sesi Akun Guru</h5>
            <p class="text-muted small mb-0">Akhiri sesi penggunaan portal guru jika Anda sedang menggunakan perangkat bersama atau komputer sekolah.</p>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-danger px-4 py-2 fw-semibold">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </button>
        </form>
    </div>
</div>

<script>
function updateBioCounter() {
    const el = document.getElementById('bioTextarea');
    const counter = document.getElementById('bioCounter');
    const warn = document.getElementById('bioLimitWarn');
    if (!el || !counter) return;
    const len = el.value.length;
    counter.innerText = `${len} / 100`;
    if (len >= 100) {
        counter.classList.add('text-danger', 'fw-bold');
        if (warn) warn.classList.remove('d-none');
    } else {
        counter.classList.remove('text-danger', 'fw-bold');
        if (warn) warn.classList.add('d-none');
    }
}

function updateGuruChatCounter() {
    const el = document.getElementById('guruChatInput');
    const counter = document.getElementById('guruChatCounter');
    const warn = document.getElementById('guruChatLimitWarn');
    if (!el || !counter) return;
    const len = el.value.length;
    counter.innerText = `${len} / 100`;
    if (len >= 100) {
        counter.classList.add('text-danger', 'fw-bold');
        if (warn) warn.classList.remove('d-none');
    } else {
        counter.classList.remove('text-danger', 'fw-bold');
        if (warn) warn.classList.add('d-none');
    }
}

function useDefaultBio() {
    const el = document.getElementById('bioTextarea');
    if (el) {
        el.value = "{{ $defaultBio }}";
        updateBioCounter();
    }
}

document.getElementById('bioTextarea')?.addEventListener('input', updateBioCounter);
document.getElementById('guruChatInput')?.addEventListener('input', updateGuruChatCounter);

document.getElementById('photoInput')?.addEventListener('change', function() {
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
        };
        reader.readAsDataURL(this.files[0]);
    }
});

function scrollGuruChat() {
    const c = document.getElementById('guruChatContainer');
    if (c) c.scrollTop = c.scrollHeight;
}

window.addEventListener('DOMContentLoaded', function() {
    updateBioCounter();
    updateGuruChatCounter();
    scrollGuruChat();

    if (window.location.hash === '#tabChat') {
        const chatTrigger = document.querySelector('button[data-bs-target="#tabChat"]');
        if (chatTrigger) {
            bootstrap.Tab.getOrCreateInstance(chatTrigger).show();
            setTimeout(scrollGuruChat, 150);
        }
    }

    const chatTabBtn = document.querySelector('button[data-bs-target="#tabChat"]');
    if (chatTabBtn) {
        chatTabBtn.addEventListener('shown.bs.tab', function() {
            setTimeout(scrollGuruChat, 100);
        });
    }
});

function openEditModalGuru(id, text) {
    document.getElementById('editFormGuru').action = '/guru/pengaturan/chat/' + id;
    document.getElementById('editTextareaGuru').value = text;
    new bootstrap.Modal(document.getElementById('editModalGuru')).show();
}
</script>
@endsection
