@extends('layouts.auth')
@section('title', 'Login')

@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height: 100vh; padding: 40px 0;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-4 col-lg-5 col-md-7">
                <div class="card-custom p-4 p-md-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-mortarboard-fill" style="font-size: 3rem; color: var(--primary);"></i>
                        <h3 class="fw-bold mt-3" style="color: var(--primary); font-size: 1.5rem;">Masuk ke GuruKuu</h3>
                        <p class="text-muted mb-0" style="font-size: 0.9rem;">Masukkan NIS dan Tanggal Lahir Anda</p>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger border-0 mb-3" style="background: #fee2e2; color: #991b1b;">
                            @foreach($errors->all() as $error)
                                <div class="small"><i class="bi bi-exclamation-triangle"></i> {{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('login.post') }}" method="POST" id="loginForm">
                        @csrf
                        {{-- Hidden field untuk menentukan role login (default: siswa) --}}
                        <input type="hidden" name="login_role" id="loginRole" value="siswa">

                        <div class="mb-3">
                            <label class="form-label font-mono" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 1px;">NIS</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-person-badge text-muted"></i></span>
                                <input type="text" name="nis" id="nisInput" class="form-control border-start-0 ps-0" 
                                    value="{{ old('nis') }}" placeholder="Masukkan NIS" required style="border-radius: 0 8px 8px 0; padding: 0.65rem 1rem;">
                            </div>
                        </div>

                        <div class="mb-3" id="dobContainer">
                            <label class="form-label font-mono" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 1px;">TANGGAL LAHIR</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar-event text-muted"></i></span>
                                <input type="date" name="tanggal_lahir" id="dobInput" class="form-control border-start-0 ps-0" 
                                    value="{{ old('tanggal_lahir') }}" required style="border-radius: 0 8px 8px 0; padding: 0.65rem 1rem;">
                            </div>
                        </div>

                        {{-- Field Password (Awalnya disembunyikan, muncul jika pilih Admin di pop-up) --}}
                        <div class="mb-4" id="passwordContainer" style="display: none;">
                            <label class="form-label font-mono" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 1px;">PASSWORD ADMIN</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-lock text-muted"></i></span>
                                <input type="password" name="password" id="passwordInput" class="form-control border-start-0 border-end-0 ps-0" 
                                    placeholder="Masukkan Password" style="border-radius: 0; padding: 0.65rem 1rem;">
                                <button type="button" class="input-group-text bg-white border-start-0" id="togglePassword" style="border-radius: 0 8px 8px 0; cursor: pointer; border-left: none;">
                                    <i class="bi bi-eye text-muted" id="toggleIcon"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" id="submitBtn" class="btn btn-masuk w-100 py-2" style="font-size: 1rem;">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Lanjutkan
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        <small class="text-muted d-block mb-2">Ada masalah dengan login?</small>
                        <a href="{{ route('kontak.guest.page') }}" class="text-decoration-none" style="color: var(--primary); font-size: 0.85rem;">
                            <i class="bi bi-chat-dots me-1"></i> Hubungi Admin
                        </a>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('landing.index') }}" class="text-decoration-none text-muted fw-medium" style="font-size: 0.9rem;">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ✅ POP-UP PILIHAN ROLE (Hanya muncul untuk NIS 4669/4686) --}}
<div class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center p-3">
            <div class="mb-3">
                <i class="bi bi-person-badge-fill text-primary" style="font-size: 2.5rem;"></i>
                <h6 class="fw-bold mt-2 mb-1">Akun Dual-Role Terdeteksi</h6>
                <p class="text-muted small mb-0">NIS ini terdaftar sebagai Admin & Siswa.<br>Login sebagai apa?</p>
            </div>
            <div class="d-grid gap-2">
                <button type="button" class="btn btn-outline-primary" onclick="selectRole('siswa')">
                    <i class="bi bi-person-fill me-1"></i> Masuk sebagai Siswa
                </button>
                <button type="button" class="btn btn-primary" onclick="selectRole('admin')">
                    <i class="bi bi-shield-lock-fill me-1"></i> Masuk sebagai Admin
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nisInput = document.getElementById('nisInput');
    const loginForm = document.getElementById('loginForm');
    const loginRoleInput = document.getElementById('loginRole');
    const passwordContainer = document.getElementById('passwordContainer');
    const passwordInput = document.getElementById('passwordInput');
    const submitBtn = document.getElementById('submitBtn');
    const roleModal = new bootstrap.Modal(document.getElementById('roleModal'));
    
    // Daftar NIS Admin yang bisa login sebagai siswa juga
    const dualRoleNis = ['4669', '4686'];

    // 1. Logic Pop-up saat Submit
    loginForm.addEventListener('submit', function(e) {
        const nis = nisInput.value.trim();
        const role = loginRoleInput.value;

        // Jika NIS dual-role DAN belum memilih role (masih default siswa) DAN password belum muncul
        if (dualRoleNis.includes(nis) && role === 'siswa' && passwordContainer.style.display === 'none') {
            e.preventDefault(); // Hentikan submit
            roleModal.show();   // Tampilkan pop-up
        }
        // Jika sudah pilih Admin tapi password kosong
        else if (role === 'admin' && !passwordInput.value) {
            e.preventDefault();
            passwordInput.focus();
            passwordInput.classList.add('is-invalid');
        }
    });

    // 2. Fungsi saat tombol di Pop-up diklik
    window.selectRole = function(role) {
        roleModal.hide();
        loginRoleInput.value = role;

        if (role === 'admin') {
            // Tampilkan field password
            passwordContainer.style.display = 'block';
            passwordInput.setAttribute('required', 'required');
            submitBtn.innerHTML = '<i class="bi bi-shield-lock-fill me-2"></i> Masuk sebagai Admin';
            setTimeout(() => passwordInput.focus(), 300);
        } else {
            // Masuk sebagai siswa (langsung submit)
            passwordContainer.style.display = 'none';
            passwordInput.removeAttribute('required');
            submitBtn.innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i> Lanjutkan';
            loginForm.submit();
        }
    };

    // 3. Fitur Lihat/Sembunyikan Password
    const toggleBtn = document.getElementById('togglePassword');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        });
    }
});
</script>
@endpush
@endsection