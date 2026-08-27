@extends('layouts.landing')
@section('title', 'Syarat & Ketentuan')

@section('content')
<div style="padding-top: 120px; padding-bottom: 80px; background: var(--bg-light); min-height: 80vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card-custom p-4 p-md-5">
                    <h2 class="fw-bold mb-4" style="color: var(--primary);">Syarat & Ketentuan</h2>
                    <p class="text-muted mb-4">Terakhir diperbarui: {{ date('d F Y') }}</p>
                    
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-check-circle me-2" style="color: var(--accent);"></i>1. Eligibilitas</h5>
                        <p class="text-muted mb-0">Platform ini hanya dapat digunakan oleh siswa dan guru yang terdaftar resmi di sekolah. Akun harus diaktifkan oleh administrator sekolah sebelum dapat digunakan.</p>
                    </div>
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-exclamation-triangle me-2" style="color: var(--accent);"></i>2. Tanggung Jawab Pengguna</h5>
                        <p class="text-muted mb-0">Siswa wajib memberikan penilaian secara <strong>jujur dan objektif</strong>. Dilarang memberikan penilaian berdasarkan dendam pribadi, SARA, atau konten yang tidak pantas.</p>
                    </div>
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-lock me-2" style="color: var(--accent);"></i>3. Keamanan Akun</h5>
                        <p class="text-muted mb-0">Pengguna bertanggung jawab penuh atas kerahasiaan password akun mereka. Dilarang membagikan password kepada orang lain.</p>
                    </div>
                    <div class="mb-0">
                        <h5 class="fw-bold mb-3"><i class="bi bi-telephone me-2" style="color: var(--accent);"></i>4. Kontak & Pengaduan</h5>
                        <p class="text-muted mb-0">Jika Anda menemukan pelanggaran atau memiliki keluhan, silakan hubungi administrator sekolah melalui fitur <strong>Chat Admin</strong> yang tersedia di footer website ini.</p>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <a href="{{ route('landing.index') }}" class="btn btn-outline-custom"><i class="bi bi-arrow-left"></i> Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection