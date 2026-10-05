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
    @media (min-width: 992px) {
        html, body.auth-page {
            height: 100vh;
            max-height: 100vh;
            overflow: hidden !important;
        }
        .auth-container-wrapper {
            height: 100vh;
            max-height: 100vh;
            overflow: hidden !important;
        }
        .auth-desktop-wrapper {
            height: 100vh;
            max-height: 100vh;
            overflow: hidden !important;
        }
        .auth-hero-side {
            height: 100vh;
            max-height: 100vh;
            padding: 2.75rem 3rem 2rem !important;
            overflow: hidden !important;
        }
        .auth-form-side {
            height: 100vh;
            max-height: 100vh;
            padding: 2rem 2.5rem !important;
            overflow-y: auto;
        }
    }
    .auth-hero-side {
        background: linear-gradient(180deg, #38bdf8 0%, #0284c7 18%, #003366 58%, #001228 100%);
        color: #ffffff;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 3rem 3rem;
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
        padding: 2.5rem 2rem;
        overflow-y: auto;
    }
    [data-theme="dark"] .auth-form-side {
        background: #0f172a;
    }
    .auth-form-container {
        width: 100%;
        max-width: 420px;
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
        padding: 0.65rem 1rem;
        border-radius: 10px;
        font-weight: 700;
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
        padding: 0.7rem 1rem 0.7rem 0.5rem;
        color: var(--text-dark, #0f172a);
        font-size: 0.92rem;
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
        padding: 0.8rem 1.5rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.98rem;
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
        gap: 0.8rem;
        margin-bottom: 0.65rem;
    }
    .hero-feature-icon {
        width: 36px;
        height: 36px;
        border-radius: 9px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: #f59e0b;
        flex-shrink: 0;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .btn-back-container {
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        border: 1.5px solid var(--border, #cbd5e1);
        background: rgba(0, 0, 0, 0.03);
        color: var(--text-dark, #334155);
        font-weight: 600;
        font-size: 0.84rem;
        text-decoration: none;
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-back-container:hover {
        background: #ffffff;
        border-color: var(--primary, #003366);
        color: var(--primary, #003366);
        transform: translateX(-3px);
        box-shadow: 0 4px 12px rgba(0, 51, 102, 0.12);
    }
    [data-theme="dark"] .btn-back-container {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.15);
        color: #e2e8f0;
    }
    [data-theme="dark"] .btn-back-container:hover {
        background: #1e293b;
        border-color: #38bdf8;
        color: #38bdf8;
    }
</style>

<div class="auth-desktop-wrapper">
    <div class="row g-0 w-100 flex-grow-1">
        {{-- SISI KIRI (DESKTOP INSTITUTIONAL HERO - MENGACU PADA SETTING MENGAPA GURUKUU ADA) --}}
        <div class="col-lg-6 col-xl-7 d-none d-lg-flex auth-hero-side">
            <div class="auth-hero-glow-1"></div>
            <div class="auth-hero-glow-2"></div>

            {{-- HEADER BRANDING --}}
            <div class="position-relative mb-4 d-flex flex-column align-items-start gap-2.5 pt-2" style="z-index: 2;">
                <div class="d-flex align-items-center gap-3">
                    @if(!empty($siteLogo))
                        <img src="{{ $siteLogo }}" alt="{{ $siteTitle ?? 'GuruKuu' }}" style="height: 44px; max-width: 140px; object-fit: contain;">
                    @else
                        <div class="rounded-circle bg-white p-2 d-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px;">
                            <i class="bi bi-mortarboard-fill fs-4" style="color: {{ $siteTitleColor1 ?? '#003366' }};"></i>
                        </div>
                    @endif
                    <div>
                        <h2 class="fw-bold mb-0 font-mono tracking-tight" style="font-size: 1.85rem; line-height: 1.15;">
                            <span style="color: {{ $siteTitleColor1 ?? '#003366' }} !important;">{{ $siteTitlePart1 ?? 'Guru' }}</span><span style="color: {{ $siteTitleColor2 ?? '#FFC107' }} !important;">{{ $siteTitlePart2 ?? 'Kuu' }}</span>
                        </h2>
                    </div>
                </div>

                <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill" style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25); backdrop-filter: blur(10px);">
                    <i class="bi bi-patch-check-fill text-warning"></i>
                    <span class="small fw-bold letter-spacing-1 text-white">{{ \App\Models\Setting::get('about_label', 'SMK NEGERI 1 BANGSRI • JUARA') }}</span>
                </div>
            </div>

            {{-- MID HIGHLIGHTS (DINAMIS DARI PENGATURAN TENTANG / MENGAPA GURUKUU ADA) --}}
            <div class="position-relative my-auto py-2" style="z-index: 2; max-width: 580px;">
                <h1 class="fw-bold text-white mb-2" style="font-size: 2.1rem; line-height: 1.25;">
                    {{ \App\Models\Setting::get('about_title', 'Mengapa GuruKuu Ada?') }}
                </h1>
                <p class="text-white-50 mb-3" style="font-size: 0.95rem; line-height: 1.6;">
                    {{ \App\Models\Setting::get('about_subtitle', 'Platform evaluasi terintegrasi untuk SMK yang membangun jembatan komunikasi positif antara siswa, guru, dan manajemen sekolah.') }}
                </p>

                <div class="mt-2.5">
                    {{-- 1. VISI KAMI --}}
                    <div class="hero-feature-item">
                        <div class="hero-feature-icon">
                            <i class="bi bi-bullseye"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-white mb-0.5" style="font-size: 0.92rem;">Visi Kami</h6>
                            <small class="text-white-50" style="font-size: 0.8rem; line-height: 1.4; display: block;">{{ \App\Models\Setting::get('visi_text', 'Menjadi standar nasional evaluasi pengajaran berbasis data untuk ekosistem pendidikan yang responsif & berkelanjutan.') }}</small>
                        </div>
                    </div>

                    {{-- 2. MISI KAMI --}}
                    <div class="hero-feature-item">
                        <div class="hero-feature-icon">
                            <i class="bi bi-file-earmark-check"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-white mb-0.5" style="font-size: 0.92rem;">Misi Kami</h6>
                            <small class="text-white-50" style="font-size: 0.8rem; line-height: 1.4; display: block;">{{ \App\Models\Setting::get('misi_summary', 'Saluran aspirasi aman bagi siswa, analitik valid bagi sekolah, & evaluasi objektif bagi pendidik.') }}</small>
                        </div>
                    </div>

                    {{-- 3. ANONIMITAS --}}
                    <div class="hero-feature-item">
                        <div class="hero-feature-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-white mb-0.5" style="font-size: 0.92rem;">{{ \App\Models\Setting::get('feature1_title', 'Anonimitas') }}</h6>
                            <small class="text-white-50" style="font-size: 0.8rem; line-height: 1.4; display: block;">{{ \App\Models\Setting::get('feature1_desc', 'Identitas siswa aman dengan enkripsi tanpa tekanan.') }}</small>
                        </div>
                    </div>

                    {{-- 4. BERBASIS DATA --}}
                    <div class="hero-feature-item">
                        <div class="hero-feature-icon">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-white mb-0.5" style="font-size: 0.92rem;">{{ \App\Models\Setting::get('feature2_title', 'Berbasis Data') }}</h6>
                            <small class="text-white-50" style="font-size: 0.8rem; line-height: 1.4; display: block;">{{ \App\Models\Setting::get('feature2_desc', 'Data statistik valid & terukur untuk setiap apresiasi.') }}</small>
                        </div>
                    </div>

                    {{-- 5. KOLABORATIF --}}
                    <div class="hero-feature-item mb-0">
                        <div class="hero-feature-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-white mb-0.5" style="font-size: 0.92rem;">{{ \App\Models\Setting::get('feature3_title', 'Kolaboratif') }}</h6>
                            <small class="text-white-50" style="font-size: 0.8rem; line-height: 1.4; display: block;">{{ \App\Models\Setting::get('feature3_desc', 'Membangun komunikasi positif siswa, guru, & sekolah.') }}</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FOOTER KIRI --}}
            <div class="position-relative d-flex justify-content-between align-items-center pt-2" style="z-index: 2;">
                <span class="text-white-50 small">
                    @php
                        $rawCopyright = \App\Models\Setting::get('footer_copyright', 'Hak Cipta Dilindungi.');
                        if (str_contains($rawCopyright, '©') || str_contains($rawCopyright, '&copy;')) {
                            $finalCopyright = $rawCopyright;
                        } else {
                            $finalCopyright = '&copy; ' . date('Y') . ' ' . \App\Models\Setting::get('site_title', 'GuruKuu') . ' - SMK Negeri 1 Bangsri. ' . $rawCopyright;
                        }
                    @endphp
                    {!! $finalCopyright !!}
                </span>
            </div>
        </div>

        {{-- SISI KANAN (FORM LOGIN FULL VIEW) --}}
        <div class="col-12 col-lg-6 col-xl-5 auth-form-side">
            <div class="auth-form-container">
                {{-- MOBILE TOP HEADER --}}
                <div class="text-center d-lg-none mb-3">
                    <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                        @if(!empty($siteLogo))
                            <img src="{{ $siteLogo }}" alt="{{ $siteTitle ?? 'GuruKuu' }}" style="height: 36px; max-width: 120px; object-fit: contain;">
                        @else
                            <i class="bi bi-mortarboard-fill text-primary fs-3"></i>
                        @endif
                        <h3 class="fw-bold mb-0 font-mono">
                            <span style="color: {{ $siteTitleColor1 ?? '#003366' }} !important;">{{ $siteTitlePart1 ?? 'Guru' }}</span><span style="color: {{ $siteTitleColor2 ?? '#FFC107' }} !important;">{{ $siteTitlePart2 ?? 'Kuu' }}</span>
                        </h3>
                    </div>
                    <div class="d-inline-flex align-items-center gap-1.5 px-3 py-1 rounded-pill mb-1" style="background: rgba(0, 51, 102, 0.08); border: 1px solid var(--border);">
                        <i class="bi bi-patch-check-fill text-warning small"></i>
                        <span class="font-mono small fw-bold text-primary" style="font-size: 0.72rem;">{{ \App\Models\Setting::get('about_label', 'SMK NEGERI 1 BANGSRI • JUARA') }}</span>
                    </div>
                </div>

                {{-- FORM GREETING --}}
                <div class="mb-3">
                    <h3 class="fw-bold mb-1 text-dark" style="font-size: 1.55rem;">
                        Masuk ke Akun
                    </h3>
                    <p class="text-muted small mb-0" style="font-size: 0.85rem;">
                        Pilih peran Anda dan masukkan kredensial akun untuk mengakses sistem.
                    </p>
                </div>

                @if(session('logout'))
                    <div class="alert alert-danger border-0 mb-3 p-3 rounded-3 shadow-xs d-flex align-items-center gap-2" style="background: #fff1f2; border-left: 4px solid #ef4444 !important; color: #991b1b;">
                        <i class="bi bi-dash-circle-fill text-danger fs-5 flex-shrink-0"></i>
                        <span class="small fw-semibold">{{ session('logout') }}</span>
                    </div>
                @elseif(session('success'))
                    @if(str_contains(session('success'), 'keluar'))
                        <div class="alert alert-danger border-0 mb-3 p-3 rounded-3 shadow-xs d-flex align-items-center gap-2" style="background: #fff1f2; border-left: 4px solid #ef4444 !important; color: #991b1b;">
                            <i class="bi bi-dash-circle-fill text-danger fs-5 flex-shrink-0"></i>
                            <span class="small fw-semibold">Anda telah keluar dari akun.</span>
                        </div>
                    @else
                        <div class="alert alert-success border-0 mb-3 p-3 rounded-3 shadow-xs d-flex align-items-center gap-2" style="background: #f0fdf4; border-left: 4px solid #22c55e !important; color: #15803d;">
                            <i class="bi bi-check-circle-fill text-success fs-5 flex-shrink-0"></i>
                            <span class="small fw-semibold">{{ session('success') }}</span>
                        </div>
                    @endif
                @endif

                @if(session('info'))
                    <div class="alert alert-info border-0 mb-3 p-3 rounded-3 shadow-xs d-flex align-items-center gap-2" style="background: #f0f9ff; border-left: 4px solid #0284c7 !important; color: #0369a1;">
                        <i class="bi bi-info-circle-fill text-info fs-5 flex-shrink-0"></i>
                        <span class="small fw-semibold">{{ session('info') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger border-0 mb-3 p-3 rounded-3 shadow-xs d-flex align-items-center gap-2" style="background: #fff1f2; border-left: 4px solid #ef4444 !important; color: #991b1b;">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-5 flex-shrink-0"></i>
                        <span class="small fw-semibold">{{ session('error') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger border-0 mb-3 p-3 rounded-3 shadow-xs" style="background: #fff1f2; border-left: 4px solid #ef4444 !important; color: #991b1b;">
                        @foreach($errors->all() as $error)
                            <div class="d-flex align-items-start gap-2.5 mb-1.5 last:mb-0">
                                <i class="bi bi-exclamation-triangle-fill text-danger fs-5 flex-shrink-0 mt-0.5"></i>
                                <div class="small text-start" style="line-height: 1.55; color: #881337; font-weight: 500;">
                                    {{ $error }}
                                </div>
                            </div>
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
                    <div class="mb-2.5">
                        <label class="form-label font-mono small fw-bold text-muted mb-1" style="font-size: 0.7rem; letter-spacing: 0.8px;">PILIH PERAN PENGGUNA</label>
                        <div class="role-segmented-control">
                            <input type="radio" class="btn-check" name="login_role" id="roleSiswa" value="siswa" checked onchange="updateForm()">
                            <label class="role-segmented-btn py-1.5" for="roleSiswa">
                                <i class="bi bi-person-fill"></i> Siswa
                            </label>

                            <input type="radio" class="btn-check" name="login_role" id="roleGuru" value="guru" onchange="updateForm()">
                            <label class="role-segmented-btn py-1.5" for="roleGuru">
                                <i class="bi bi-person-badge-fill"></i> Guru
                            </label>
                        </div>
                    </div>

                    {{-- INPUT IDENTITAS (NIS / NIP) --}}
                    <div class="mb-2.5">
                        <label class="form-label font-mono small fw-bold text-muted mb-1" id="labelNis" style="font-size: 0.7rem; letter-spacing: 0.8px;">NIS SISWA</label>
                        <div class="input-group input-auth-group">
                            <span class="input-group-text"><i class="bi bi-person text-muted fs-5"></i></span>
                            <input type="text" name="nis" id="inputNis" class="form-control"
                                value="{{ old('nis') }}" placeholder="Masukkan NIS siswa" required autocomplete="username">
                        </div>
                    </div>

                    {{-- INPUT PASSWORD --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label font-mono small fw-bold text-muted mb-0" style="font-size: 0.7rem; letter-spacing: 0.8px;">KATA SANDI</label>
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

                {{-- SSO SIPINTU BUTTON --}}
                <div class="d-flex align-items-center my-3">
                    <hr class="flex-grow-1 border-secondary opacity-25 m-0">
                    <span class="px-2 text-muted small" style="font-size: 0.75rem;">ATAU</span>
                    <hr class="flex-grow-1 border-secondary opacity-25 m-0">
                </div>

                <a href="{{ route('login.sipintu') }}" class="btn btn-outline-light border w-100 py-2.5 d-flex align-items-center justify-content-center gap-2 text-dark shadow-xs" style="border-radius: 12px; font-size: 0.88rem; background: #f8fafc;">
                    <img src="{{ asset('images/sipintu-logo.png') }}" alt="SiPintu" style="width: 20px; height: 20px; object-fit: contain;">
                    <span class="fw-semibold text-dark">Masuk dengan Portal SiPintu</span>
                </a>

                {{-- BANTUAN KENDALA LOGIN & TOMBOL KEMBALI DI BAWAH HUBUNGI ADMIN --}}
                <div class="text-center mt-3 pt-3 border-top" style="border-color: var(--border) !important;">
                    <span class="text-muted d-block small mb-1" style="font-size: 0.8rem;">Ada kendala masuk?</span>
                    <a href="{{ route('kontak.guest.page') }}" class="text-decoration-none fw-semibold small d-inline-flex align-items-center gap-1.5" style="color: var(--primary);">
                        <i class="bi bi-headset"></i>
                        <span>Hubungi Admin</span>
                    </a>

                    {{-- TOMBOL KEMBALI DI BAWAH HUBUNGI ADMIN DENGAN CONTAINER & JARAK LEGA --}}
                    <div class="mt-3 pt-1">
                        <a href="{{ route('landing.index') }}" class="gk-btn-back">
                            <i class="bi bi-arrow-left"></i>
                            <span>Kembali ke Beranda</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const roleInputs = {
    siswa: { nis: document.getElementById('inputNis')?.value || '', password: '' },
    guru: { nis: '', password: '' }
};
let activeRole = 'siswa';

function updateForm() {
    const roleSiswa = document.getElementById('roleSiswa');
    const labelNis = document.getElementById('labelNis');
    const inputNis = document.getElementById('inputNis');
    const inputPassword = document.getElementById('inputPassword');
    const btnSubmit = document.getElementById('btnSubmit');

    if (!roleSiswa || !labelNis || !inputNis || !btnSubmit || !inputPassword) return;

    const newRole = roleSiswa.checked ? 'siswa' : 'guru';

    if (newRole !== activeRole) {
        // Simpan input saat ini ke peran aktif sebelumnya
        roleInputs[activeRole].nis = inputNis.value;
        roleInputs[activeRole].password = inputPassword.value;

        // Terapkan nilai tersimpan dari peran baru (kosong jika belum diisi)
        activeRole = newRole;
        inputNis.value = roleInputs[newRole].nis;
        inputPassword.value = roleInputs[newRole].password;
    }

    if (activeRole === 'siswa') {
        labelNis.textContent = 'NIS SISWA';
        inputNis.placeholder = 'Masukkan NIS siswa';
        btnSubmit.innerHTML = '<i class="bi bi-box-arrow-in-right"></i> Masuk sebagai Siswa';
    } else {
        labelNis.textContent = 'NIP GURU';
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