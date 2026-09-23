@extends('layouts.auth')
@section('title', 'Login')

@section('content')
<style>
    .auth-card-glass {
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 24px 48px -12px rgba(0, 0, 0, 0.35);
        border-radius: 24px;
        transition: all 0.3s ease;
    }
    [data-theme="dark"] .auth-card-glass {
        background: rgba(17, 26, 48, 0.88);
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 24px 48px -12px rgba(0, 0, 0, 0.7);
    }
    .role-segmented-control {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 14px;
        padding: 4px;
        display: flex;
        gap: 4px;
    }
    [data-theme="dark"] .role-segmented-control {
        background: rgba(255, 255, 255, 0.05);
    }
    .role-segmented-btn {
        flex: 1;
        border: none;
        background: transparent;
        padding: 0.65rem 1rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--text-muted);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        cursor: pointer;
    }
    .btn-check:checked + .role-segmented-btn {
        background: var(--primary);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 51, 102, 0.25);
    }
    [data-theme="dark"] .btn-check:checked + .role-segmented-btn {
        background: #38bdf8;
        color: #0f172a;
        box-shadow: 0 4px 12px rgba(56, 189, 248, 0.3);
    }
    .input-glass-group {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--border);
        background: var(--bg-card);
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .input-glass-group:focus-within {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.15);
    }
    [data-theme="dark"] .input-glass-group:focus-within {
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2);
    }
    .input-glass-group .input-group-text {
        background: transparent;
        border: none;
        padding-left: 1rem;
        padding-right: 0.5rem;
    }
    .input-glass-group .form-control {
        background: transparent;
        border: none;
        padding: 0.75rem 1rem 0.75rem 0.5rem;
        color: var(--text-dark);
    }
    .input-glass-group .form-control:focus {
        box-shadow: none;
    }
    .btn-auth-submit {
        background: linear-gradient(135deg, var(--primary), var(--primary-light, #004d99));
        color: white;
        border: none;
        padding: 0.8rem 1.5rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.95rem;
        box-shadow: 0 8px 20px -4px rgba(0, 51, 102, 0.4);
        transition: all 0.25s ease;
    }
    .btn-auth-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px -4px rgba(0, 51, 102, 0.5);
        color: white;
    }
    [data-theme="dark"] .btn-auth-submit {
        background: linear-gradient(135deg, #38bdf8, #0284c7);
        color: #0f172a;
        box-shadow: 0 8px 20px -4px rgba(56, 189, 248, 0.4);
    }
    [data-theme="dark"] .btn-auth-submit:hover {
        color: #0f172a;
    }
    .btn-glass-back {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.35);
        color: #ffffff;
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        text-decoration: none;
    }
    .btn-glass-back:hover {
        background: rgba(255, 255, 255, 0.35);
        color: #ffffff;
        transform: translateY(-1px);
    }
</style>

<div class="container-fluid d-flex flex-column align-items-center justify-content-center px-2 py-1">
    <div style="width: 100%; max-width: 420px;">
        <div class="auth-card-glass p-3.5 p-sm-4">
            <div class="text-center mb-3">
                @if(!empty($siteLogo))
                    <img src="{{ $siteLogo }}" alt="{{ $siteTitle ?? 'GuruKuu' }}" style="max-height: 48px; max-width: 160px; object-fit: contain;" class="mb-1">
                @else
                    <div class="d-inline-flex p-2.5 rounded-circle shadow-sm mb-1" style="background: rgba(0, 51, 102, 0.1); color: var(--primary);">
                        <i class="bi bi-mortarboard-fill fs-3"></i>
                    </div>
                @endif
                <div class="d-block mt-1">
                    <span class="badge rounded-pill px-2.5 py-1" style="background: rgba(0, 51, 102, 0.08); color: var(--primary); font-size: 0.7rem; letter-spacing: 0.5px; border: 1px solid var(--border);">
                        🏫 SMK NEGERI 1 BANGSRI • JUARA
                    </span>
                </div>
                <h3 class="fw-bold mt-1.5 mb-0.5" style="font-size: 1.35rem;">
                    Masuk ke <span style="color: {{ $siteTitleColor1 }};">{{ $siteTitlePart1 }}</span><span style="color: {{ $siteTitleColor2 }};">{{ $siteTitlePart2 }}</span>
                </h3>
                <p class="text-muted mb-0 small" style="font-size: 0.8rem;">Pilih peran dan masukkan kredensial akun Anda</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger border-0 mb-3 py-2 px-3 rounded-3">
                    @foreach($errors->all() as $error)
                        <div class="small d-flex align-items-center gap-1.5"><i class="bi bi-exclamation-triangle-fill"></i> {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" id="loginForm">
                @csrf
                {{-- 🛡️ Anti-Bot Honeypot Protection --}}
                <div style="display:none !important; position:absolute; left:-9999px;" aria-hidden="true">
                    <input type="text" name="website_hp" id="website_hp" tabindex="-1" autocomplete="off">
                </div>

                {{-- PERAN LOGIN: SISWA & GURU (Tanpa teks admin) --}}
                <div class="mb-2.5">
                    <label class="form-label font-mono small fw-bold text-muted mb-1" style="font-size: 0.72rem; letter-spacing: 1px;">PILIH PERAN</label>
                    <div class="role-segmented-control">
                        <input type="radio" class="btn-check" name="login_role" id="roleSiswa" value="siswa" checked onchange="updateForm()">
                        <label class="role-segmented-btn py-2" for="roleSiswa">
                            <i class="bi bi-person-fill"></i> Siswa
                        </label>

                        <input type="radio" class="btn-check" name="login_role" id="roleGuru" value="guru" onchange="updateForm()">
                        <label class="role-segmented-btn py-2" for="roleGuru">
                            <i class="bi bi-person-badge-fill"></i> Guru
                        </label>
                    </div>
                </div>

                {{-- INPUT IDENTITAS (NIS / NIP) --}}
                <div class="mb-2.5">
                    <label class="form-label font-mono small fw-bold text-muted mb-1" id="labelNis" style="font-size: 0.72rem; letter-spacing: 1px;">NIS SISWA</label>
                    <div class="input-group input-glass-group">
                        <span class="input-group-text"><i class="bi bi-person text-muted"></i></span>
                        <input type="text" name="nis" id="inputNis" class="form-control py-2"
                            value="{{ old('nis') }}" placeholder="Masukkan NIS" required autocomplete="username">
                    </div>
                </div>

                {{-- INPUT PASSWORD --}}
                <div class="mb-3">
                    <label class="form-label font-mono small fw-bold text-muted mb-1" style="font-size: 0.72rem; letter-spacing: 1px;">PASSWORD</label>
                    <div class="input-group input-glass-group">
                        <span class="input-group-text"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" name="password" id="inputPassword" class="form-control py-2"
                            placeholder="Masukkan password" required autocomplete="current-password">
                        <button type="button" class="input-group-text border-0 bg-transparent" id="togglePassword" 
                            style="cursor: pointer;" title="Tampilkan/Sembunyikan Password">
                            <i class="bi bi-eye text-muted" id="iconEye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" id="btnSubmit" class="btn btn-auth-submit w-100 py-2 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk sebagai Siswa
                </button>
            </form>

            <div class="text-center mt-3 pt-2 border-top" style="border-color: var(--border) !important;">
                <small class="text-muted d-block mb-1" style="font-size: 0.75rem;">Ada kendala saat login atau akun dinonaktifkan?</small>
                <a href="{{ route('kontak.guest.page') }}" class="text-decoration-none fw-semibold small" style="color: var(--primary); font-size: 0.8rem;">
                    <i class="bi bi-chat-dots me-1"></i> Hubungi Admin Operator Sekolah
                </a>
            </div>

        </div>

        <div class="text-center mt-4 pt-1 pb-2">
            <a href="{{ route('landing.index') }}" class="btn-glass-back py-2 px-3.5 d-inline-flex align-items-center gap-1.5" style="font-size: 0.85rem; text-decoration: none;">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali ke Beranda Utama</span>
            </a>
        </div>
    </div>
</div>

<script>
function updateForm() {
    const roleSiswa = document.getElementById('roleSiswa');
    const labelNis = document.getElementById('labelNis');
    const inputNis = document.getElementById('inputNis');
    const btnSubmit = document.getElementById('btnSubmit');

    if (roleSiswa.checked) {
        labelNis.textContent = 'NIS SISWA';
        inputNis.placeholder = 'Masukkan NIS';
        btnSubmit.innerHTML = '<i class="bi bi-box-arrow-in-right"></i> Masuk sebagai Siswa';
    } else {
        labelNis.textContent = 'NIP GURU';
        inputNis.placeholder = 'Masukkan NIP';
        btnSubmit.innerHTML = '<i class="bi bi-box-arrow-in-right"></i> Masuk sebagai Guru';
    }
}

document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('inputPassword');
    const iconEye = document.getElementById('iconEye');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        iconEye.classList.remove('bi-eye');
        iconEye.classList.add('bi-eye-slash');
    } else {
        passwordInput.type = 'password';
        iconEye.classList.remove('bi-eye-slash');
        iconEye.classList.add('bi-eye');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    updateForm();
});
</script>
@endsection