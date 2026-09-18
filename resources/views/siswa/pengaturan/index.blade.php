@extends('layouts.siswa')
@section('title', 'Pengaturan Akun Siswa')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <div class="page-label">MANAJEMEN AKUN</div>
        <h1 class="page-title">Pengaturan Akun Siswa</h1>
        <p class="page-subtitle">Kelola informasi data diri, perbarui kata sandi, komunikasi bantuan dengan admin, dan sesi akun Anda.</p>
    </div>
</div>

{{-- NAV PILLS --}}
<ul class="nav nav-pills mb-4 gap-2" id="siswaPengaturanTabs" role="tablist">
    <li class="nav-item">
        <button class="nav-link active fw-bold" id="info-tab" data-bs-toggle="pill" data-bs-target="#tabInfo" type="button">
            <i class="bi bi-person-badge-fill me-1"></i> 1. Informasi Akun
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-bold" id="chat-tab" data-bs-toggle="pill" data-bs-target="#tabChat" type="button">
            <i class="bi bi-chat-dots-fill me-1"></i> 2. Hubungi Admin Operator Sekolah
            @if(isset($pesanChat) && $pesanChat->whereNotNull('balasan')->count() > 0)
                <span class="badge bg-success ms-1">{{ $pesanChat->whereNotNull('balasan')->count() }} Balasan</span>
            @endif

        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-bold" id="legal-tab" data-bs-toggle="pill" data-bs-target="#tabLegalSiswa" type="button">
            <i class="bi bi-shield-check me-1"></i> 3. Kebijakan Privasi & Ketentuan
        </button>
    </li>
</ul>

<div class="tab-content mb-5">
    {{-- TAB 1: INFORMASI AKUN --}}
    <div class="tab-pane fade show active" id="tabInfo">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card-custom p-4 h-100">
                    <h5 class="fw-bold mb-3 d-flex align-items-center">
                        <i class="bi bi-person-vcard text-primary me-2"></i>Data Resmi Terdaftar
                    </h5>
                    <p class="text-muted small mb-4">Informasi data diri Anda yang tersinkronisasi dengan master data siswa sekolah.</p>

                    <div class="mb-3">
                        <label class="form-label small text-muted font-mono" style="letter-spacing: 1px;">NAMA LENGKAP</label>
                        <div class="fw-bold fs-6 p-2 rounded bg-light border">{{ $user->name }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted font-mono" style="letter-spacing: 1px;">NIS (NOMOR INDUK SISWA)</label>
                        <div class="fw-bold font-mono p-2 rounded bg-light border text-primary">{{ $user->nis ?? '-' }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted font-mono" style="letter-spacing: 1px;">KELAS AKTIF</label>
                        <div class="fw-bold p-2 rounded bg-light border">{{ $kelasAktif ? $kelasAktif->nama_kelas . ' Kelas ' . $kelasAktif->tingkat : ($user->kelas ?? 'Kelas Siswa') }}</div>
                    </div>

                    @if($user->jurusan && $user->jurusan->nama_jurusan && !str_contains(strtolower($user->jurusan->nama_jurusan), 'semua jurusan'))
                    <div>
                        <label class="form-label small text-muted font-mono" style="letter-spacing: 1px;">JURUSAN</label>
                        <div class="fw-bold p-2 rounded bg-light border text-primary">{{ $user->jurusan->nama_jurusan }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card-custom p-4 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-3 d-flex align-items-center">
                            <i class="bi bi-shield-check text-success me-2"></i>Status & Kebijakan Anonimitas
                        </h5>
                        <p class="text-muted small mb-4">Keamanan data dan privasi Anda dalam memberikan evaluasi guru.</p>

                        <div class="p-3 rounded mb-3" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                <strong class="text-success">Akun Siswa Aktif Terverifikasi</strong>
                            </div>
                            <small class="text-muted">Akun Anda memiliki hak akses penuh untuk memberikan penilaian pada periode aktif saat ini.</small>
                        </div>

                        <div class="p-3 rounded" style="background: #eff6ff; border: 1px solid #bfdbfe;">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="bi bi-incognito text-primary fs-5"></i>
                                <strong class="text-primary">100% Anonim Bagi Guru</strong>
                            </div>
                            <small class="text-muted">Nama, NIS, dan identitas Anda dirahasiakan sepenuhnya dari guru yang Anda nilai demi objektivitas.</small>
                        </div>
                    </div>

                    <div class="text-muted small mt-4 pt-3 border-top">
                        Jika terdapat kesalahan nama, NIS, atau rombel kelas, silakan hubungi melalui tab <strong>Hubungi Admin Operator Sekolah</strong>.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 2: HUBUNGI ADMIN OPERATOR SEKOLAH --}}
    <div class="tab-pane fade" id="tabChat">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                {{-- HEADER CHAT DENGAN ADMIN OPERATOR --}}
                <div class="card-custom mb-3" style="overflow: hidden;">
                    <div class="p-3 d-flex align-items-center gap-3" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white;">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px);">
                            <i class="bi bi-headset-fill fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-0 text-white">Admin Operator Sekolah GuruKuu</h6>
                            <small style="opacity: 0.9;"><i class="bi bi-circle-fill text-success me-1" style="font-size: 0.5rem;"></i> Online • Siap membantu kendala Anda</small>
                        </div>
                        <div class="d-none d-md-block">
                            <span class="badge bg-light text-dark px-3 py-2">
                                <i class="bi bi-shield-lock-fill me-1 text-primary"></i> Terenkripsi & Aman
                            </span>
                        </div>
                    </div>
                </div>

                {{-- AREA CHAT MESSENGER --}}
                <div class="card-custom mb-4" style="overflow: hidden; display: flex; flex-direction: column; height: 60vh; border: 1px solid var(--border);">
                    {{-- Area Pesan Chat (Scrollable) --}}
                    <div class="flex-grow-1 p-4" style="overflow-y: auto; background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);" id="siswaChatContainer">
                        @if($pesan->isEmpty())
                            <div class="text-center py-5">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 70px; height: 70px; background: rgba(0,51,102,0.08);">
                                    <i class="bi bi-chat-square-dots-fill fs-2" style="color: var(--primary);"></i>
                                </div>
                                <h6 class="fw-bold mb-1">Mulai Percakapan dengan Admin Operator Sekolah</h6>
                                <p class="text-muted small mb-0">Tanyakan kendala penilaian, verifikasi kelas, atau bantuan akun di formulir bawah ini.</p>
                            </div>

                        @else
                            @foreach($pesan as $chat)
                                {{-- PESAN SISWA (KANAN) --}}
                                <div class="d-flex justify-content-end mb-3">
                                    <div style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); color: white; padding: 0.85rem 1.25rem; border-radius: 18px 18px 4px 18px; max-width: 75%; box-shadow: 0 4px 12px rgba(0,51,102,0.15); position: relative;">
                                        @if($chat->pesan === '[Pesan Dihapus]')
                                            <p class="mb-1 fst-italic opacity-75"><i class="bi bi-trash me-1"></i>[Pesan Dihapus]</p>
                                        @else
                                            <p class="mb-1" style="line-height: 1.5; word-wrap: break-word;">{{ $chat->pesan }}</p>
                                        @endif
                                        <div class="text-end mt-2 d-flex justify-content-end gap-2 align-items-center" style="font-size: 0.7rem; opacity: 0.9;">
                                            <span>{{ $chat->created_at->format('H:i') }}</span>
                                            @if($chat->is_replied)
                                                <i class="bi bi-check2-all" style="color: #4fc3f7;" title="Telah dibalas admin"></i>
                                            @else
                                                <i class="bi bi-check2" title="Terkirim"></i>
                                            @endif
                                            @if($chat->pesan !== '[Pesan Dihapus]')
                                                <button type="button" class="btn btn-sm p-0 text-white border-0 ms-1" onclick="openEditModalSiswa({{ $chat->id }}, '{{ addslashes($chat->pesan) }}')" title="Edit Pesan">
                                                    <i class="bi bi-pencil-fill" style="font-size: 0.72rem;"></i>
                                                </button>
                                                <form action="{{ route('siswa.kontak.destroy-message', $chat) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pesan ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm p-0 text-white border-0" title="Hapus Pesan">
                                                        <i class="bi bi-trash-fill" style="font-size: 0.72rem;"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- BALASAN ADMIN (KIRI) --}}
                                @if($chat->balasan)
                                <div class="d-flex justify-content-start mb-3">
                                    <div class="d-flex align-items-start gap-2" style="max-width: 75%;">
                                        <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" style="width: 36px; height: 36px; background: var(--primary); color: white;">
                                            <i class="bi bi-person-badge-fill" style="font-size: 0.9rem;"></i>
                                        </div>
                                        <div style="background: var(--bg-card); border: 1px solid var(--border); padding: 0.85rem 1.25rem; border-radius: 18px 18px 18px 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                                            <div class="fw-bold mb-1" style="color: var(--primary); font-size: 0.8rem;">
                                                <i class="bi bi-patch-check-fill me-1 text-primary"></i> Administrator Sekolah
                                            </div>
                                            <p class="mb-1" style="line-height: 1.5; word-wrap: break-word;">{{ $chat->balasan }}</p>
                                            <div class="text-end" style="font-size: 0.7rem; color: var(--text-muted);">
                                                {{ $chat->balasan_at ? \Carbon\Carbon::parse($chat->balasan_at)->format('H:i') : $chat->updated_at->format('H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        @endif
                    </div>

                    {{-- FORM INPUT PESAN & VERIFIKASI ANTI-SPAM --}}
                    <div class="border-top p-3" style="background: var(--bg-card);">
                        <form action="{{ route('siswa.pengaturan.chat') }}" method="POST">
                            @csrf
                            <div class="d-flex gap-2 align-items-end mb-2">
                                <textarea name="pesan" class="form-control" rows="2" placeholder="Tulis pesan atau kendala Anda di sini..." required style="border-radius: 12px; resize: none; border: 2px solid var(--border);" onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'">{{ old('pesan') }}</textarea>
                                <button type="submit" class="btn btn-primary-custom d-flex align-items-center justify-content-center" style="height: 48px; width: 48px; border-radius: 12px; padding: 0;" title="Kirim Pesan">
                                    <i class="bi bi-send-fill fs-5"></i>
                                </button>
                            </div>

                            {{-- KOTAK VERIFIKASI ANTI-SPAM --}}
                            <div class="p-2 rounded d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: var(--bg-light); border: 1px dashed var(--border);">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-shield-check-fill text-success"></i>
                                    <label class="form-label mb-0 small fw-bold text-muted">VERIFIKASI ANTI-SPAM: {{ $num1 }} + {{ $num2 }} =</label>
                                </div>
                                <div class="d-flex gap-2 align-items-center">
                                    <input type="number" name="captcha" class="form-control form-control-sm text-center fw-bold" placeholder="?" required style="width: 75px; border-radius: 8px; border: 2px solid var(--border);" value="{{ old('captcha') }}">
                                    <small class="text-muted" style="font-size: 0.72rem;">Verifikasi</small>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB 3: KEBIJAKAN PRIVASI & SYARAT KETENTUAN --}}
    <div class="tab-pane fade" id="tabLegalSiswa">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card-custom p-4 h-100">
                    <h5 class="fw-bold mb-3 d-flex align-items-center text-primary">
                        <i class="bi bi-shield-lock-fill me-2"></i>Kebijakan Privasi
                    </h5>
                    <p class="text-muted small mb-3">Ketentuan perlindungan data dan privasi penilaian siswa.</p>
                    <div class="p-3 bg-light rounded border text-muted small" style="line-height: 1.8; max-height: 480px; overflow-y: auto;">
                        @php
                            $privacy = \App\Models\Setting::get('kebijakan_privasi', "1. Pengumpulan Data\nKami hanya mengumpulkan data yang diperlukan untuk proses penilaian, yaitu NIS, nama, dan kelas siswa. Data pribadi seperti tanggal lahir hanya digunakan untuk verifikasi identitas saat login.\n\n2. Anonimitas Penilaian\nSeluruh penilaian yang diberikan siswa bersifat anonim. Guru dan pihak lain tidak dapat mengetahui identitas siswa yang memberikan nilai tertentu. Ini menjamin kejujuran dan objektivitas dalam setiap penilaian.\n\n3. Penyimpanan Data\nSemua data disimpan di server yang aman dengan enkripsi standar industri. Password pengguna di-hash menggunakan algoritma bcrypt yang tidak dapat dibaca kembali.\n\n4. Penggunaan Data\nData penilaian hanya digunakan untuk keperluan internal sekolah, seperti evaluasi kinerja guru dan pengambilan keputusan oleh manajemen. Data tidak akan dibagikan kepada pihak ketiga tanpa persetujuan.");
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
                    <p class="text-muted small mb-3">Aturan penggunaan platform evaluasi GuruKuu bagi siswa.</p>
                    <div class="p-3 bg-light rounded border text-muted small" style="line-height: 1.8; max-height: 480px; overflow-y: auto;">
                        @php
                            $terms = \App\Models\Setting::get('syarat_ketentuan', "1. Eligibilitas\nPlatform ini hanya dapat digunakan oleh siswa dan guru yang terdaftar resmi di sekolah. Akun harus diaktifkan oleh administrator sekolah sebelum dapat digunakan.\n\n2. Tanggung Jawab Pengguna\nSiswa wajib memberikan penilaian secara jujur dan objektif. Dilarang memberikan penilaian berdasarkan dendam pribadi, SARA, atau konten yang tidak pantas.\n\n3. Keamanan Akun\nPengguna bertanggung jawab penuh atas kerahasiaan password akun mereka. Dilarang membagikan password kepada orang lain.\n\n4. Kontak & Pengaduan\nJika Anda menemukan pelanggaran atau memiliki keluhan, silakan hubungi administrator sekolah melalui fitur Chat Admin yang tersedia di footer website ini.");
                        @endphp
                        {!! nl2br(e($terms)) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT PESAN SISWA --}}
<div class="modal fade" id="editModalSiswa" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <form id="editFormSiswa" method="POST">
                @csrf @method('PUT')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Pesan Chat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea name="pesan" id="editTextareaSiswa" class="form-control" rows="4" required style="border-radius: 12px; border: 2px solid var(--border);"></textarea>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                    <button type="submit" class="btn btn-primary-custom" style="border-radius: 8px;">
                        <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- DEDICATED LOGOUT CARD (SESI AKUN SISWA) --}}
<div class="card-custom p-4 border-danger border-opacity-25" style="background: #fffafa;">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h5 class="fw-bold text-danger mb-1"><i class="bi bi-box-arrow-right me-2"></i>Keluar Sesi Akun Siswa</h5>
            <p class="text-muted small mb-0">Pastikan Anda telah selesai memberikan penilaian sebelum keluar, terutama di lab komputer sekolah atau perangkat bersama.</p>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-danger px-4 py-2 fw-semibold">
                <i class="bi bi-box-arrow-right me-1"></i> Keluar (Logout)
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function scrollSiswaChat() {
        const c = document.getElementById('siswaChatContainer');
        if (c) c.scrollTop = c.scrollHeight;
    }

    window.addEventListener('DOMContentLoaded', function() {
        scrollSiswaChat();

        if (window.location.hash === '#tabChat') {
            const chatTrigger = document.querySelector('button[data-bs-target="#tabChat"]');
            if (chatTrigger) {
                bootstrap.Tab.getOrCreateInstance(chatTrigger).show();
                setTimeout(scrollSiswaChat, 150);
            }
        }

        const chatTabBtn = document.querySelector('button[data-bs-target="#tabChat"]');
        if (chatTabBtn) {
            chatTabBtn.addEventListener('shown.bs.tab', function() {
                setTimeout(scrollSiswaChat, 100);
            });
        }
    });

    function openEditModalSiswa(id, text) {
        document.getElementById('editFormSiswa').action = '/siswa/kontak/' + id;
        document.getElementById('editTextareaSiswa').value = text;
        new bootstrap.Modal(document.getElementById('editModalSiswa')).show();
    }
</script>
@endpush
@endsection
