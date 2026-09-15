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
<ul class="nav nav-pills mb-4 gap-2" id="guruPengaturanTabs" role="tablist">
    <li class="nav-item">
        <button class="nav-link active fw-bold" id="profil-tab" data-bs-toggle="pill" data-bs-target="#tabProfil" type="button">
            <i class="bi bi-person-lines-fill me-1"></i> 1. Profil & Deskripsi Diri
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-bold" id="password-tab" data-bs-toggle="pill" data-bs-target="#tabPassword" type="button">
            <i class="bi bi-shield-lock-fill me-1"></i> 2. Ganti Password
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-bold" id="chat-tab" data-bs-toggle="pill" data-bs-target="#tabChat" type="button">
            <i class="bi bi-chat-dots-fill me-1"></i> 3. Chat Admin
            @if(isset($pesanChat) && $pesanChat->whereNotNull('balasan')->count() > 0)
                <span class="badge bg-success ms-1">{{ $pesanChat->whereNotNull('balasan')->count() }} Balasan</span>
            @endif
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
                                <img id="avatarPreview" src="{{ $guru->photo_url ?? $user->photo_url }}" class="rounded-circle shadow-sm" width="140" height="140" style="object-fit: cover; border: 4px solid var(--primary);">
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
                        <p class="text-muted small mb-4">Informasi ini akan ditampilkan kepada siswa di kartu profil guru.</p>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">NIP / Identitas (Resmi)</label>
                                <input type="text" class="form-control bg-light" value="{{ $guru->nip ?? $user->nis }}" readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Departemen / Jurusan</label>
                                <input type="text" class="form-control bg-light" value="{{ $guru->jurusan?->nama_jurusan ?? 'Umum' }}" readonly disabled>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small fw-bold">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" value="{{ old('nama', $guru->nama ?? $user->name) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Alamat Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $guru->email ?? $user->email) }}" placeholder="contoh@smkn1bangsri.sch.id">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Nomor Kontak / WhatsApp</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $guru->phone ?? '') }}" placeholder="08xxxxxxxxxx">
                            </div>

                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <label class="form-label small fw-bold mb-1">Deskripsi Diri & Tentang Saya</label>
                                    <button type="button" class="btn btn-link p-0 text-decoration-none small text-primary" onclick="useDefaultBio()">
                                        <i class="bi bi-arrow-repeat me-1"></i>Gunakan Deskripsi Bawaan
                                    </button>
                                </div>
                                <textarea name="bio" id="bioTextarea" class="form-control" rows="4" placeholder="Tuliskan perkenalan singkat mengenai dedikasi mengajar, visi mendidik, atau pesan inspiratif bagi siswa...">{{ old('bio', $guru->bio ?? $defaultBio) }}</textarea>
                                <div class="form-text small">
                                    <i class="bi bi-info-circle me-1"></i> Jika dikosongkan, sistem akan otomatis menggunakan deskripsi standar: <em>"{{ $defaultBio }}"</em>
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

    {{-- TAB 2: GANTI PASSWORD --}}
    <div class="tab-pane fade" id="tabPassword">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card-custom p-4">
                    <h5 class="fw-bold mb-1" style="color: var(--text-dark);">
                        <i class="bi bi-shield-lock-fill text-warning me-2"></i>Pembaruan Password Akun
                    </h5>
                    <p class="text-muted small mb-4">Jaga kerahasiaan kata sandi portal pengajar Anda demi keamanan evaluasi.</p>

                    <form action="{{ route('guru.pengaturan.password') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Password Saat Ini</label>
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Masukkan password lama" required>
                            @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Password Baru</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ketik ulang password baru" required>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary-custom px-4">
                                <i class="bi bi-key me-1"></i> Simpan Password Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 3: CHAT ADMIN --}}
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
                            <h6 class="fw-bold mb-0 text-white">Administrator GuruKuu</h6>
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
                    <div class="flex-grow-1 p-4" style="overflow-y: auto; background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);" id="guruChatContainer">
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
                                                <i class="bi bi-check2-all" style="color: #4fc3f7;" title="Telah dibalas admin"></i>
                                            @else
                                                <i class="bi bi-check2" title="Terkirim"></i>
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
                                        <div style="background: white; border: 1px solid var(--border); padding: 0.85rem 1.25rem; border-radius: 18px 18px 18px 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
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
                    <div class="border-top p-3" style="background: white;">
                        <form action="{{ route('guru.pengaturan.chat') }}" method="POST">
                            @csrf
                            <div class="d-flex gap-2 align-items-end mb-2">
                                <textarea name="pesan" class="form-control" rows="2" placeholder="Tulis pesan atau pertanyaan Anda di sini..." required style="border-radius: 12px; resize: none; border: 2px solid var(--border);" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'">{{ old('pesan') }}</textarea>
                                <button type="submit" class="btn btn-primary-custom d-flex align-items-center justify-content-center" style="height: 48px; width: 48px; border-radius: 12px; padding: 0;" title="Kirim Pesan">
                                    <i class="bi bi-send-fill fs-5"></i>
                                </button>
                            </div>

                            {{-- KOTAK VERIFIKASI ANTI-SPAM --}}
                            <div class="p-2 rounded d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: var(--bg-light); border: 1px dashed var(--border);">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-shield-check-fill text-success"></i>
                                    <label class="form-label mb-0 small fw-bold text-muted">VERIFIKASI ANTI-SPAM: {{ $num1 }} + {{ $num2 }} =</label>
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
                    <textarea name="pesan" id="editTextareaGuru" class="form-control" rows="4" required style="border-radius: 12px; border: 2px solid var(--border);"></textarea>
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
                <i class="bi bi-box-arrow-right me-1"></i> Keluar (Logout)
            </button>
        </form>
    </div>
</div>

<script>
function useDefaultBio() {
    document.getElementById('bioTextarea').value = "{{ $defaultBio }}";
}

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
