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
                        <p class="text-muted mb-0" style="font-size: 0.9rem;">Pilih peran dan masukkan kredensial Anda</p>
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

                        <div class="mb-3">
                            <label class="form-label font-mono" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 1px;">PERAN LOGIN</label>
                            <div class="d-flex gap-2">
                                <input type="radio" class="btn-check" name="login_role" id="roleSiswa" value="siswa" checked onchange="updateForm()">
                                <label class="btn btn-outline-primary flex-fill py-2" for="roleSiswa">
                                    <i class="bi bi-person-fill me-1"></i> Siswa
                                </label>

                                <input type="radio" class="btn-check" name="login_role" id="roleGuru" value="guru" onchange="updateForm()">
                                <label class="btn btn-outline-primary flex-fill py-2" for="roleGuru">
                                    <i class="bi bi-chalkboard-teacher me-1"></i> Guru 
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-mono" id="labelNis" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 1px;">NIS</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-person-badge text-muted"></i></span>
                                <input type="text" name="nis" id="inputNis" class="form-control border-start-0 ps-0"
                                    value="{{ old('nis') }}" placeholder="Masukkan NIS" required 
                                    style="border-radius: 0 8px 8px 0; padding: 0.65rem 1rem;">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label font-mono" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 1px;">PASSWORD</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-lock text-muted"></i></span>
                                <input type="password" name="password" id="inputPassword" class="form-control border-start-0 border-end-0 ps-0"
                                    placeholder="Masukkan Password" required 
                                    style="border-radius: 0; padding: 0.65rem 1rem;">
                                <button type="button" class="input-group-text bg-white border-start-0" id="togglePassword" 
                                    style="border-radius: 0 8px 8px 0; cursor: pointer; border-left: none;">
                                    <i class="bi bi-eye text-muted" id="iconEye"></i>
                                </button>
                            </div>
                            <!-- <small class="text-muted mt-1 d-block" id="hintPassword">
                                Password default: <strong>password</strong>
                            </small> -->
                        </div>

                        <button type="submit" id="btnSubmit" class="btn btn-masuk w-100 py-2" style="font-size: 1rem;">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Masuk sebagai Siswa
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

<script>
function updateForm() {
    const roleSiswa = document.getElementById('roleSiswa');
    const roleGuru = document.getElementById('roleGuru');
    const labelNis = document.getElementById('labelNis');
    const inputNis = document.getElementById('inputNis');
    const hintPassword = document.getElementById('hintPassword');
    const btnSubmit = document.getElementById('btnSubmit');

    if (roleSiswa.checked) {
        labelNis.textContent = 'NIS';
        inputNis.placeholder = 'Masukkan NIS';
        btnSubmit.innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i> Masuk sebagai Siswa';
    } else {
        labelNis.textContent = 'NIP';
        inputNis.placeholder = 'Masukkan NIP';
        btnSubmit.innerHTML = '<i class="bi bi-chalkboard-teacher me-2"></i> Masuk sebagai Guru';
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