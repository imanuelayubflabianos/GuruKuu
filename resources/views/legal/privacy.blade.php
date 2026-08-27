@extends('layouts.landing')
@section('title', 'Kebijakan Privasi')

@section('content')
<div style="padding-top: 120px; padding-bottom: 80px; background: var(--bg-light); min-height: 80vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card-custom p-4 p-md-5">
                    <h2 class="fw-bold mb-4" style="color: var(--primary);">Kebijakan Privasi</h2>
                    <p class="text-muted mb-4">Terakhir diperbarui: {{ date('d F Y') }}</p>
                    
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-shield-lock me-2" style="color: var(--primary);"></i>1. Pengumpulan Data</h5>
                        <p class="text-muted mb-0">Kami hanya mengumpulkan data yang diperlukan untuk proses penilaian, yaitu NIS, nama, dan kelas siswa. Data pribadi seperti tanggal lahir hanya digunakan untuk verifikasi identitas saat login.</p>
                    </div>
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-eye-slash me-2" style="color: var(--primary);"></i>2. Anonimitas Penilaian</h5>
                        <p class="text-muted mb-0">Seluruh penilaian yang diberikan siswa bersifat <strong>anonim</strong>. Guru dan pihak lain tidak dapat mengetahui identitas siswa yang memberikan nilai tertentu. Ini menjamin kejujuran dan objektivitas dalam setiap penilaian.</p>
                    </div>
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-database me-2" style="color: var(--primary);"></i>3. Penyimpanan Data</h5>
                        <p class="text-muted mb-0">Semua data disimpan di server yang aman dengan enkripsi standar industri. Password pengguna di-hash menggunakan algoritma bcrypt yang tidak dapat dibaca kembali.</p>
                    </div>
                    <div class="mb-0">
                        <h5 class="fw-bold mb-3"><i class="bi bi-person-check me-2" style="color: var(--primary);"></i>4. Penggunaan Data</h5>
                        <p class="text-muted mb-0">Data penilaian hanya digunakan untuk keperluan internal sekolah, seperti evaluasi kinerja guru dan pengambilan keputusan oleh manajemen. Data tidak akan dibagikan kepada pihak ketiga tanpa persetujuan.</p>
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