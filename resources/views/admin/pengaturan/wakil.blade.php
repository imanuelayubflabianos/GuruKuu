@extends('layouts.admin')
@section('title', 'Pengaturan Akun Wakil')

@section('content')
<style>
    @media (max-width: 767.98px) {
        #wakilPengaturanTabs {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 0.4rem;
            gap: 0.45rem;
            scrollbar-width: none;
        }
        #wakilPengaturanTabs::-webkit-scrollbar {
            display: none;
        }
        #wakilPengaturanTabs .nav-item {
            flex: 0 0 auto;
        }
        #wakilPengaturanTabs .nav-link {
            white-space: nowrap !important;
            padding: 0.55rem 0.9rem;
            font-size: 0.84rem;
            border-radius: 0.65rem;
        }
    }
</style>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
    <div>
        <h1 class="page-title mb-1">Pengaturan Akun Wakil</h1>
        <p class="page-subtitle mb-0">Kelola akun, keamanan sesi, riwayat perangkat, dan pusat bantuan.</p>
    </div>
</div>

{{-- NAV PILLS --}}
<ul class="nav nav-pills mb-4" id="wakilPengaturanTabs" role="tablist">
    <li class="nav-item">
        <button class="nav-link active" id="info-tab" data-bs-toggle="pill" data-bs-target="#tabInfoWakil" type="button">
            <i class="bi bi-person-badge"></i> Akun & Keamanan
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" id="device-tab" data-bs-toggle="pill" data-bs-target="#tabDeviceWakil" type="button">
            <i class="bi bi-laptop"></i> Riwayat Perangkat
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" id="faq-tab" data-bs-toggle="pill" data-bs-target="#tabFaqWakil" type="button">
            <i class="bi bi-question-circle"></i> FAQ
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" id="chat-tab" data-bs-toggle="pill" data-bs-target="#tabChatWakil" type="button">
            <i class="bi bi-headset"></i> Chat Admin
            @if(isset($pesan) && $pesan->whereNotNull('balasan')->count() > 0)
                <span class="badge bg-success ms-1">{{ $pesan->whereNotNull('balasan')->count() }} Balasan</span>
            @endif
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link" id="legal-tab" data-bs-toggle="pill" data-bs-target="#tabLegalWakil" type="button">
            <i class="bi bi-file-earmark-lock"></i> Kebijakan & Privasi
        </button>
    </li>
</ul>

<div class="tab-content mb-5">
    {{-- TAB 1: INFORMASI AKUN & GANTI PASSWORD --}}
    <div class="tab-pane fade show active" id="tabInfoWakil" role="tabpanel">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card-custom p-4 text-center">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3 text-white fw-bold shadow-sm" style="width: 72px; height: 72px; background: linear-gradient(135deg, #0d6efd, #003366); font-size: 1.6rem;">
                        WS
                    </div>
                    <h5 class="fw-bold mb-1">{{ auth()->user()->name }}</h5>
                    <span class="badge bg-info-subtle text-info border font-mono px-3 py-1 mb-3">Role: Wakil</span>
                    <div class="text-muted small border-top pt-3 text-start">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Username / NIS:</span>
                            <strong class="font-mono text-dark">{{ auth()->user()->nis }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Email Akun:</span>
                            <span class="font-mono text-dark">{{ auth()->user()->email ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Status Akun:</span>
                            <span class="badge bg-success-subtle text-success">Aktif</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card-custom p-4">
                    <h5 class="fw-bold mb-3 d-flex align-items-center text-primary">
                        <i class="bi bi-shield-lock-fill me-2"></i>Perbarui Kata Sandi Akun
                    </h5>
                    <p class="text-muted small mb-4">Ganti kata sandi login secara berkala untuk menjaga keamanan akses dashboard monitoring Wakil.</p>

                    <form action="{{ route('admin.pengaturan.wakil.password') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Password Saat Ini</label>
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Masukkan password sekarang" required>
                            @error('current_password')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Password Baru</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter" required>
                            @error('password')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-semibold">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru" required>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary-custom px-4">
                                <i class="bi bi-key-fill me-1"></i> Perbarui Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 2: RIWAYAT PERANGKAT --}}
    <div class="tab-pane fade" id="tabDeviceWakil" role="tabpanel">
        @include('components.device-history')
    </div>

    {{-- TAB 3: FAQ --}}
    <div class="tab-pane fade" id="tabFaqWakil" role="tabpanel">
        <div class="card-custom p-4">
            @include('components.faq-accordion')
        </div>
    </div>

    {{-- TAB 4: CHAT ADMIN --}}
    <div class="tab-pane fade" id="tabChatWakil" role="tabpanel">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card-custom p-4">
                    <h5 class="fw-bold mb-3 d-flex align-items-center text-primary">
                        <i class="bi bi-chat-dots-fill me-2"></i>Kirim Pesan ke Admin
                    </h5>
                    <p class="text-muted small mb-3">Sampaikan masukan, koordinasi, atau permohonan ke administrator sekolah.</p>

                    <form action="{{ route('admin.pengaturan.wakil.chat') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Isi Pesan</label>
                            <textarea name="pesan" class="form-control @error('pesan') is-invalid @enderror" rows="4" maxlength="255" placeholder="Tuliskan pesan koordinasi..." required>{{ old('pesan') }}</textarea>
                            @error('pesan')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                            <div class="form-text small text-muted">Maksimal 255 karakter.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-semibold">Verifikasi: {{ $num1 }} + {{ $num2 }} = ?</label>
                            <input type="number" name="captcha" class="form-control @error('captcha') is-invalid @enderror" placeholder="Jawaban" required>
                            @error('captcha')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary-custom w-100">
                            <i class="bi bi-send-fill me-1"></i> Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card-custom p-4">
                    <h5 class="fw-bold mb-3 d-flex align-items-center text-primary">
                        <i class="bi bi-clock-history me-2"></i>Riwayat Pesan Anda
                    </h5>

                    @forelse($pesan as $p)
                        <div class="border rounded p-3 mb-3 bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-secondary-subtle text-secondary font-mono" style="font-size: 0.7rem;">
                                    {{ $p->created_at?->format('d M Y H:i') }}
                                </span>
                                @if(!empty($p->balasan))
                                    <span class="badge bg-success-subtle text-success border">Dibalas Admin</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis border">Menunggu Balasan</span>
                                @endif
                            </div>
                            <p class="small text-dark mb-2 fw-medium">{{ $p->pesan }}</p>
                            @if(!empty($p->balasan))
                                <div class="bg-white rounded p-2.5 border mt-2">
                                    <div class="d-flex align-items-center gap-1.5 text-primary small fw-bold mb-1">
                                        <i class="bi bi-reply-fill"></i> Balasan Admin:
                                    </div>
                                    <p class="small text-muted mb-0">{{ $p->balasan }}</p>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-chat-square-dots fs-1 d-block mb-2 opacity-50"></i>
                            Belum ada riwayat pesan yang dikirimkan.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 5: KEBIJAKAN & PRIVASI --}}
    <div class="tab-pane fade" id="tabLegalWakil" role="tabpanel">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card-custom p-4 h-100">
                    <h5 class="fw-bold mb-3 d-flex align-items-center text-primary">
                        <i class="bi bi-shield-check me-2"></i>Kebijakan Privasi
                    </h5>
                    <p class="text-muted small mb-3">Ketentuan perlindungan data dan privasi penilaian siswa.</p>
                    <div class="p-3 bg-light rounded border text-muted small" style="line-height: 1.8; max-height: 480px; overflow-y: auto;">
                        @php
                            $privacy = \App\Models\Setting::get('kebijakan_privasi', "1. Pengumpulan Data\nKami hanya mengumpulkan data yang diperlukan untuk proses penilaian, yaitu NIS, nama, dan kelas siswa. Data pribadi hanya digunakan untuk verifikasi identitas saat login.\n\n2. Anonimitas Penilaian\nSeluruh penilaian yang diberikan siswa bersifat anonim bagi guru, sesama siswa, dan akun monitoring Wakil untuk menjamin objektivitas dan independensi.\n\n3. Penyimpanan Data\nSemua data disimpan di server yang aman dengan enkripsi standar industri.\n\n4. Penggunaan Data\nData penilaian hanya digunakan untuk keperluan internal sekolah, seperti evaluasi kinerja guru dan pengambilan keputusan apresiasi.");
                        @endphp
                        {!! nl2br(e($privacy)) !!}
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card-custom p-4 h-100">
                    <h5 class="fw-bold mb-3 d-flex align-items-center text-primary">
                        <i class="bi bi-file-earmark-text-fill me-2"></i>Syarat & Ketentuan
                    </h5>
                    <p class="text-muted small mb-3">Aturan penggunaan platform evaluasi {{ $siteTitle ?? 'GuruKuu' }}.</p>
                    <div class="p-3 bg-light rounded border text-muted small" style="line-height: 1.8; max-height: 480px; overflow-y: auto;">
                        @php
                            $terms = \App\Models\Setting::get('syarat_ketentuan', "1. Eligibilitas\nPlatform ini hanya dapat digunakan oleh warga sekolah yang terdaftar resmi. Akun harus diaktifkan sebelum dapat digunakan.\n\n2. Tanggung Jawab Pengguna\nSeluruh evaluasi dan ulasan wajib disampaikan secara santun, beretika, dan bertanggung jawab tanpa mengandung ujaran kebencian.\n\n3. Keamanan Akun\nPengguna bertanggung jawab penuh atas kerahasiaan kata sandi akun masing-masing.");
                        @endphp
                        {!! nl2br(e($terms)) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const hash = window.location.hash;
        if (hash) {
            const triggerEl = document.querySelector(`#wakilPengaturanTabs button[data-bs-target="${hash}"]`);
            if (triggerEl && window.bootstrap && window.bootstrap.Tab) {
                const tab = new bootstrap.Tab(triggerEl);
                tab.show();
            }
        }
    });
</script>
@endpush
