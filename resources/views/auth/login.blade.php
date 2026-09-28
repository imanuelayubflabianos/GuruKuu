@extends('layouts.auth')
@section('title', 'Login')

@section('content')
<style>
    .auth-desktop-wrapper {
        min-height: 100vh;
        width: 100%;
        display: flex;
        flex: 1;
    }
    .auth-hero-side {
        background: linear-gradient(145deg, #002244 0%, #003366 50%, #004d99 100%);
        color: #ffffff;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 3.5rem 3.5rem;
    }
    .auth-hero-glow-1 {
        position: absolute;
        top: -10%;
        right: -10%;
        width: 420px;
        height: 420px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(245, 158, 11, 0.25), transparent 70%);
        pointer-events: none;
    }
    .auth-hero-glow-2 {
        position: absolute;
        bottom: -15%;
        left: -10%;
        width: 500px;
        height: 500px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(56, 189, 248, 0.2), transparent 70%);
        pointer-events: none;
    }
    .auth-form-side {
        background: var(--bg-card, #ffffff);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 3rem 2rem;
        overflow-y: auto;
    }
    [data-theme="dark"] .auth-form-side {
        background: #0f172a;
    }
    .auth-form-container {
        width: 100%;
        max-width: 440px;
    }
    .role-segmented-control {
        background: rgba(0, 0, 0, 0.05);
        border-radius: 14px;
        padding: 4px;
        display: flex;
        gap: 4px;
        border: 1px solid var(--border, #e2e8f0);
    }
    [data-theme="dark"] .role-segmented-control {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.1);
    }
    .role-segmented-btn {
        flex: 1;
        border: none;
        background: transparent;
        padding: 0.75rem 1rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.92rem;
        color: var(--text-muted);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        cursor: pointer;
    }
    .btn-check:checked + .role-segmented-btn {
        background: var(--primary, #003366);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 51, 102, 0.25);
    }
    [data-theme="dark"] .btn-check:checked + .role-segmented-btn {
        background: #38bdf8;
        color: #0f172a;
        box-shadow: 0 4px 12px rgba(56, 189, 248, 0.3);
    }
    .input-auth-group {
        border-radius: 12px;
        overflow: hidden;
        border: 1.5px solid var(--border, #cbd5e1);
        background: var(--bg-card, #ffffff);
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    [data-theme="dark"] .input-auth-group {
        border-color: rgba(255, 255, 255, 0.15);
        background: #1e293b;
    }
    .input-auth-group:focus-within {
        border-color: var(--primary, #003366);
        box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.15);
    }
    [data-theme="dark"] .input-auth-group:focus-within {
        border-color: #38bdf8;
        box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2);
    }
    .input-auth-group .input-group-text {
        background: transparent;
        border: none;
        padding-left: 1rem;
        padding-right: 0.5rem;
    }
    .input-auth-group .form-control {
        background: transparent;
        border: none;
        padding: 0.75rem 1rem 0.75rem 0.5rem;
        color: var(--text-dark, #0f172a);
        font-size: 0.95rem;
    }
    [data-theme="dark"] .input-auth-group .form-control {
        color: #f8fafc;
    }
    .input-auth-group .form-control:focus {
        box-shadow: none;
    }
    .btn-auth-submit {
        background: linear-gradient(135deg, #003366 0%, #004d99 100%);
        color: #ffffff;
        border: none;
        padding: 0.85rem 1.5rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1rem;
        box-shadow: 0 8px 20px -4px rgba(0, 51, 102, 0.35);
        transition: all 0.25s ease;
    }
    .btn-auth-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px -4px rgba(0, 51, 102, 0.45);
        color: #ffffff;
    }
    [data-theme="dark"] .btn-auth-submit {
        background: linear-gradient(135deg, #38bdf8, #0284c7);
        color: #0f172a;
        box-shadow: 0 8px 20px -4px rgba(56, 189, 248, 0.4);
    }
    [data-theme="dark"] .btn-auth-submit:hover {
        color: #0f172a;
    }
    .hero-feature-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .hero-feature-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #f59e0b;
        flex-shrink: 0;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
</style>

<div class="auth-desktop-wrapper">
    <div class="row g-0 w-100 flex-grow-1">
        {{-- SISI KIRI (DESKTOP INSTITUTIONAL HERO) --}}
        <div class="col-lg-6 col-xl-7 d-none d-lg-flex auth-hero-side">
            <div class="auth-hero-glow-1"></div>
            <div class="auth-hero-glow-2"></div>

            {{-- HEADER BRANDING --}}
            <div class="position-relative" style="z-index: 2;">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-3" style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25); backdrop-filter: blur(10px);">
                    <i class="bi bi-patch-check-fill text-warning"></i>
                    <span class="small fw-bold letter-spacing-1">SMK NEGERI 1 BANGSRI • JUARA</span>
                </div>
                <div class="d-flex align-items-center gap-3 mt-2">
                    @if(!empty($siteLogo))
                        <img src="{{ $siteLogo }}" alt="{{ $siteTitle ?? 'GuruKuu' }}" style="height: 44px; max-width: 140px; object-fit: contain;">
                    @else
                        <div class="rounded-circle bg-white text-primary p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="bi bi-mortarboard-fill fs-4"></i>
                        </div>
                    @endif
                    <h2 class="fw-bold mb-0 text-white font-mono tracking-tight" style="font-size: 2rem;">
                        {{ $siteTitlePart1 ?? 'Guru' }}<span class="text-warning">{{ $siteTitlePart2 ?? 'Kuu' }}</span>
                    </h2>
                </div>
            </div>

            {{-- MID HIGHLIGHTS --}}
            <div class="position-relative my-auto py-5" style="z-index: 2; max-width: 600px;">
                <h1 class="fw-bold text-white mb-3" style="font-size: 2.35rem; line-height: 1.25;">
                    Evaluasi Pendidik Berkarakter JUARA
                </h1>
                <p class="text-white-50 mb-4" style="font-size: 1.05rem; line-height: 1.7;">
                    Wadah aspirasi dan penilaian kinerja guru yang objektif, transparan, dan terpercaya bagi kemajuan pembelajaran siswa di SMKN 1 Bangsri.
                </p>

                <div class="mt-4">
                    <div class="hero-feature-item">
                        <div class="hero-feature-icon">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-white mb-1">Kerahasiaan Terjamin & Anonim</h6>
                            <small class="text-white-50">Identitas siswa aman tanpa rasa khawatir untuk menyampaikan masukan objektif.</small>
                        </div>
                    </div>

                    <div class="hero-feature-item">
                        <div class="hero-feature-icon">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-white mb-1">Evaluasi 5 Aspek Kompetensi</h6>
                            <small class="text-white-50">Menilai kedisiplinan, komunikasi, tanggung jawab, kreativitas, dan keramahan guru.</small>
                        </div>
                    </div>

                    <div class="hero-feature-item mb-0">
                        <div class="hero-feature-icon">
                            <i class="bi bi-cloud-check-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-white mb-1">Terintegrasi Gateway SiPintu</h6>
                            <small class="text-white-50">Otentikasi aman menggunakan NIS siswa dan NIP guru terdaftar resmi.</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FOOTER KIRI --}}
            <div class="position-relative d-flex justify-content-between align-items-center" style="z-index: 2;">
                <a href="{{ route('landing.index') }}" class="btn btn-outline-light rounded-pill px-3.5 py-2 small fw-semibold d-inline-flex align-items-center gap-2" style="border: 1px solid rgba(255,255,255,0.3); backdrop-filter: blur(8px);">
                    <i class="bi bi-arrow-left"></i> Kembali ke Beranda Utama
                </a>
                <span class="text-white-50 small">&copy; {{ date('Y') }} SMK Negeri 1 Bangsri</span>
            </div>
        </div>

        {{-- SISI KANAN (FORM LOGIN FULL VIEW) --}}
        <div class="col-12 col-lg-6 col-xl-5 auth-form-side">
            <div class="auth-form-container">
                {{-- MOBILE TOP HEADER --}}
                <div class="text-center d-lg-none mb-4">
                    <div class="d-inline-flex align-items-center gap-1.5 px-3 py-1 rounded-pill mb-2" style="background: rgba(0, 51, 102, 0.08); border: 1px solid var(--border);">
                        <i class="bi bi-patch-check-fill text-warning small"></i>
                        <span class="font-mono small fw-bold text-primary" style="font-size: 0.72rem;">SMKN 1 BANGSRI • JUARA</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                        @if(!empty($siteLogo))
                            <img src="{{ $siteLogo }}" alt="{{ $siteTitle ?? 'GuruKuu' }}" style="height: 38px; max-width: 120px; object-fit: contain;">
                        @else
                            <i class="bi bi-mortarboard-fill text-primary fs-3"></i>
                        @endif
                        <h3 class="fw-bold mb-0 font-mono" style="color: var(--primary);">
                            {{ $siteTitlePart1 ?? 'Guru' }}<span style="color: var(--siteTitleColor2, #f59e0b);">{{ $siteTitlePart2 ?? 'Kuu' }}</span>
                        </h3>
                    </div>
                </div>

                {{-- FORM GREETING --}}
                <div class="mb-4">
                    <h3 class="fw-bold mb-1 text-dark" style="font-size: 1.65rem;">
                        Masuk ke Akun
                    </h3>
                    <p class="text-muted small mb-0" style="font-size: 0.88rem;">
                        Pilih peran Anda dan masukkan kredensial akun untuk mengakses sistem.
                    </p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger border-0 mb-3 py-2.5 px-3 rounded-3 shadow-xs">
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

                    {{-- PILIH PERAN LOGIN (SISWA & GURU) --}}
                    <div class="mb-3">
                        <label class="form-label font-mono small fw-bold text-muted mb-1.5" style="font-size: 0.72rem; letter-spacing: 0.8px;">PILIH PERAN PENGGUNA</label>
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
                    <div class="mb-3">
                        <label class="form-label font-mono small fw-bold text-muted mb-1.5" id="labelNis" style="font-size: 0.72rem; letter-spacing: 0.8px;">NIS SISWA</label>
                        <div class="input-group input-auth-group">
                            <span class="input-group-text"><i class="bi bi-person text-muted fs-5"></i></span>
                            <input type="text" name="nis" id="inputNis" class="form-control"
                                value="{{ old('nis') }}" placeholder="Masukkan NIS siswa" required autocomplete="username">
                        </div>
                    </div>

                    {{-- INPUT PASSWORD --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1.5">
                            <label class="form-label font-mono small fw-bold text-muted mb-0" style="font-size: 0.72rem; letter-spacing: 0.8px;">KATA SANDI</label>
                        </div>
                        <div class="input-group input-auth-group">
                            <span class="input-group-text"><i class="bi bi-lock text-muted fs-5"></i></span>
                            <input type="password" name="password" id="inputPassword" class="form-control"
                                placeholder="Masukkan kata sandi" required autocomplete="current-password">
                            <button type="button" class="input-group-text border-0 bg-transparent" id="togglePassword" 
                                style="cursor: pointer;" title="Tampilkan/Sembunyikan Kata Sandi">
                                <i class="bi bi-eye text-muted fs-5" id="iconEye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" id="btnSubmit" class="btn btn-auth-submit w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk sebagai Siswa
                    </button>
                </form>

                {{-- BANTUAN KENDALA LOGIN --}}
                <div class="text-center mt-4 pt-3 border-top" style="border-color: var(--border) !important;">
                    <span class="text-muted d-block small mb-1">Ada kendala masuk atau akun terkunci?</span>
                    <a href="{{ route('kontak.guest.page') }}" class="text-decoration-none fw-semibold small d-inline-flex align-items-center gap-1" style="color: var(--primary);">
                        <i class="bi bi-chat-dots-fill"></i>
                        <span>Bantuan & Hubungi Administrator</span>
                    </a>
                </div>

                {{-- MOBILE BACK BUTTON --}}
                <div class="text-center mt-3 d-lg-none">
                    <a href="{{ route('landing.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 d-inline-flex align-items-center gap-1 text-decoration-none">
                        <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateForm() {
    const roleSiswa = document.getElementById('roleSiswa');
    const labelNis = document.getElementById('labelNis');
    const inputNis = document.getElementById('inputNis');
    const btnSubmit = document.getElementById('btnSubmit');

    if (!roleSiswa || !labelNis || !inputNis || !btnSubmit) return;

    if (roleSiswa.checked) {
        labelNis.textContent = 'NIS SISWA';
        inputNis.placeholder = 'Masukkan NIS siswa';
        btnSubmit.innerHTML = '<i class="bi bi-box-arrow-in-right"></i> Masuk sebagai Siswa';
    } else {
        labelNis.textContent = 'NIP / IDENTITAS GURU';
        inputNis.placeholder = 'Masukkan NIP guru';
        btnSubmit.innerHTML = '<i class="bi bi-box-arrow-in-right"></i> Masuk sebagai Guru';
    }
}

document.getElementById('togglePassword')?.addEventListener('click', function() {
    const passwordInput = document.getElementById('inputPassword');
    const iconEye = document.getElementById('iconEye');
    if (!passwordInput || !iconEye) return;
    
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