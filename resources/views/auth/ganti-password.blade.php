@extends('layouts.auth')
@section('title', 'Ganti Password')

@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height: 100vh; padding: 40px 0;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-4 col-lg-5 col-md-7">
                <div class="card-custom p-4 p-md-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-shield-lock-fill" style="font-size: 3rem; color: var(--primary);"></i>
                        <h3 class="fw-bold mt-3" style="color: var(--primary); font-size: 1.5rem;">Ganti Password</h3>
                        <p class="text-muted mb-0" style="font-size: 0.9rem;">Amankan akun Anda dengan password baru</p>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success border-0 mb-3" style="background: #d1fae5; color: #065f46;">
                            <i class="bi bi-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger border-0 mb-3" style="background: #fee2e2; color: #991b1b;">
                            @foreach($errors->all() as $error)
                                <div class="small"><i class="bi bi-exclamation-triangle"></i> {{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('auth.ganti-password.post') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label font-mono" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 1px;">PASSWORD LAMA</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-lock text-muted"></i></span>
                                <input type="password" name="old_password" class="form-control border-start-0 ps-0"
                                    placeholder="Masukkan password lama" required style="border-radius: 0 8px 8px 0; padding: 0.65rem 1rem;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-mono" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 1px;">PASSWORD BARU</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-key text-muted"></i></span>
                                <input type="password" name="password" id="newPassword" class="form-control border-start-0 border-end-0 ps-0"
                                    placeholder="Minimal 8 karakter" required style="border-radius: 0; padding: 0.65rem 1rem;">
                                <button type="button" class="input-group-text bg-white border-start-0" onclick="togglePass('newPassword', 'iconNew')" style="border-radius: 0 8px 8px 0; cursor: pointer; border-left: none;">
                                    <i class="bi bi-eye text-muted" id="iconNew"></i>
                                </button>
                            </div>
                            <small class="text-muted">Minimal 8 karakter, gunakan kombinasi huruf, angka, dan simbol.</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label font-mono" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 1px;">KONFIRMASI PASSWORD</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-key-fill text-muted"></i></span>
                                <input type="password" name="password_confirmation" id="confirmPassword" class="form-control border-start-0 border-end-0 ps-0"
                                    placeholder="Ulangi password baru" required style="border-radius: 0; padding: 0.65rem 1rem;">
                                <button type="button" class="input-group-text bg-white border-start-0" onclick="togglePass('confirmPassword', 'iconConfirm')" style="border-radius: 0 8px 8px 0; cursor: pointer; border-left: none;">
                                    <i class="bi bi-eye text-muted" id="iconConfirm"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-masuk w-100 py-2" style="font-size: 1rem;">
                            <i class="bi bi-check-circle me-2"></i> Simpan Password Baru
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        <a href="{{ url()->previous() }}" class="text-muted text-decoration-none" style="font-size: 0.85rem;">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePass(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
@endsection