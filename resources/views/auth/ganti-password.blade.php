@extends('layouts.landing')
@section('title', 'Ganti Password')

@section('content')
<div class="d-flex align-items-center justify-content-center" style="min-height: 100vh; background-color: var(--bg-light); padding: 100px 0 60px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-4 col-lg-5 col-md-7">
                <div class="card-custom p-4 p-md-5 shadow-sm">
                    <div class="text-center mb-4">
                        <i class="bi bi-shield-lock-fill" style="font-size: 3rem; color: var(--secondary);"></i>
                        <h3 class="fw-bold mt-3" style="color: var(--primary); font-size: 1.5rem;">Ganti Password</h3>
                        <p class="text-muted mb-0" style="font-size: 0.9rem;">
                            Ini adalah login pertama Anda.<br>
                            <strong>Wajib</strong> mengganti password sebelum melanjutkan.
                        </p>
                    </div>

                    @if(session('info'))
                        <div class="alert alert-info border-0 mb-3" style="background: #dbeafe; color: #1e40af;">
                            <i class="bi bi-info-circle"></i> {{ session('info') }}
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
                            <label class="form-label font-mono" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 1px;">PASSWORD BARU</label>
                            <input type="password" name="password" id="newPasswordField" class="form-control" 
                                placeholder="Minimal 8 karakter" required style="border-radius: 8px; padding: 0.65rem 1rem;">
                            <small class="text-muted">Gunakan kombinasi huruf, angka, dan simbol untuk keamanan optimal.</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label font-mono" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 1px;">KONFIRMASI PASSWORD</label>
                            <input type="password" name="password_confirmation" class="form-control" 
                                placeholder="Ulangi password baru" required style="border-radius: 8px; padding: 0.65rem 1rem;">
                        </div>

                        <button type="submit" class="btn btn-masuk w-100 py-2" style="font-size: 1rem;">
                            <i class="bi bi-check-circle me-2"></i> Simpan Password Baru
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-muted text-decoration-none" style="font-size: 0.85rem;">
                            <i class="bi bi-box-arrow-left me-1"></i> Batal & Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection