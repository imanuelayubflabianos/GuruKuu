@extends('layouts.admin')
@section('title', 'Pengaturan Sistem & Konten Beranda')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <div class="page-label">PENGATURAN</div>
        <h1 class="page-title">Pengaturan Sistem & Konten Beranda</h1>
        <p class="page-subtitle mb-0">Kelola identitas brand, logo, hero banner, visi & misi, footer, serta kebijakan privasi tanpa perlu mengubah kode.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ url('/') }}" target="_blank" class="btn btn-outline-custom">
            <i class="bi bi-box-arrow-up-right me-1"></i> Buka Beranda Publik
        </a>
    </div>
</div>

<form action="{{ route('admin.pengaturan.landing') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- NAV TABS PENGATURAN --}}
    <ul class="nav nav-pills mb-4 gap-2 flex-wrap" id="pengaturanTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active fw-bold" id="brand-tab" data-bs-toggle="pill" data-bs-target="#tabBrand" type="button">
                <i class="bi bi-stars me-1"></i> 1. Brand & Logo
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold" id="hero-tab" data-bs-toggle="pill" data-bs-target="#tabHero" type="button">
                <i class="bi bi-image me-1"></i> 2. Banner Hero
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold" id="visimisi-tab" data-bs-toggle="pill" data-bs-target="#tabVisiMisi" type="button">
                <i class="bi bi-bullseye me-1"></i> 3. Visi & Misi
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold" id="footer-tab" data-bs-toggle="pill" data-bs-target="#tabFooter" type="button">
                <i class="bi bi-layout-text-window-reverse me-1"></i> 4. Footer Web
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold" id="legal-tab" data-bs-toggle="pill" data-bs-target="#tabLegal" type="button">
                <i class="bi bi-shield-check me-1"></i> 5. Privasi & Ketentuan
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold text-success border-success border-opacity-25" id="periode-tab" data-bs-toggle="pill" data-bs-target="#tabPeriode" type="button">
                <i class="bi bi-calendar-range-fill me-1"></i> 6. Periode Semester
                @if($periodeAktif)
                    <span class="badge bg-success ms-1"><i class="bi bi-circle-fill me-1" style="font-size: 0.55rem;"></i>{{ $periodeAktif->semester == 'ganjil' ? 'Ganjil' : 'Genap' }}</span>
                @else
                    <span class="badge bg-danger ms-1">Nonaktif</span>
                @endif
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold text-dark border" id="akun-tab" data-bs-toggle="pill" data-bs-target="#tabAkun" type="button">
                <i class="bi bi-shield-lock-fill me-1 text-primary"></i> 7. Keamanan & Akun
            </button>
        </li>
    </ul>

    <div class="tab-content mb-4">
        {{-- TAB 1: IDENTITAS BRAND & LOGO --}}
        <div class="tab-pane fade show active" id="tabBrand">
            <div class="card-custom p-4">
                <h5 class="fw-bold mb-1" style="color: var(--text-dark);">
                    <i class="bi bi-shield-shaded text-primary me-2"></i>Identitas Brand & Logo Website
                </h5>
                <p class="text-muted small mb-4">Kustomisasi nama website dan logo yang tampil pada navbar atas dan footer.</p>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Website / Title Header</label>
                            <input type="text" name="site_title" id="siteTitleInput" class="form-control" value="{{ $settings['site_title'] }}" placeholder="GuruKuu" required>
                            <div class="form-text small">Nama ini akan tampil di sudut kiri atas navbar dan judul halaman web.</div>
                        </div>

                        <div class="p-3 rounded border" style="background: var(--bg-light);">
                            <label class="form-label small fw-bold mb-2">Live Preview Navbar Brand:</label>
                            <div class="p-3 bg-white border rounded d-flex align-items-center gap-2">
                                <img id="brandLogoPreview" src="{{ $settings['site_logo'] ?: 'data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'32\' height=\'32\' fill=\'%23003366\' class=\'bi bi-mortarboard-fill\' viewBox=\'0 0 16 16\'><path d=\'M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917z\'/></svg>' }}" 
                                     style="height: 36px; object-fit: contain;">
                                <span class="fw-bold fs-4 text-primary" id="brandTitlePreview">{{ $settings['site_title'] }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="p-3 rounded border h-100" style="background: var(--bg-light);">
                            <h6 class="fw-bold mb-3"><i class="bi bi-image text-primary me-2"></i>Logo / Ikon Website</h6>
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Unggah File Logo Baru</label>
                                <input type="file" name="site_logo_file" id="siteLogoFileInput" class="form-control" accept="image/png, image/jpeg, image/jpg, image/webp, image/svg+xml">
                                <div class="form-text small">Mendukung format PNG, JPG, WEBP, SVG (Maksimal 2 MB).</div>
                            </div>

                            <div class="text-center my-2 text-muted small fw-bold">— ATAU —</div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Gunakan URL Logo Eksternal</label>
                                <input type="url" name="site_logo_url" id="siteLogoUrlInput" class="form-control" value="{{ $settings['site_logo'] }}" placeholder="https://domain.com/logo.png">
                            </div>

                            @if($settings['site_logo'])
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="remove_logo" value="1" id="removeLogoCheck">
                                <label class="form-check-label text-danger small fw-bold" for="removeLogoCheck">
                                    Hapus logo kustom (gunakan logo/ikon default sistem)
                                </label>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 2: HERO BANNER & THUMBNAIL --}}
        <div class="tab-pane fade" id="tabHero">
            <div class="card-custom p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 border-bottom pb-3 mb-4">
                    <div>
                        <h5 class="fw-bold mb-1" style="color: var(--text-dark);">
                            <i class="bi bi-image-fill text-primary me-2"></i>Thumbnail & Hero Banner Beranda
                        </h5>
                        <p class="text-muted small mb-0">Sesuaikan foto latar belakang banner dan pesan sambutan di beranda.</p>
                    </div>
                </div>

                {{-- LIVE PREVIEW CARD --}}
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted">PREVIEW TAMPILAN BANNER SAAT INI</label>
                    <div id="heroPreviewBox" class="rounded-3 p-4 position-relative overflow-hidden shadow-sm" 
                         style="background: linear-gradient(rgba(0,51,102,0.85), rgba(0,51,102,0.7)), url('{{ $settings['hero_image'] }}') center/cover no-repeat; min-height: 240px; display: flex; align-items: center; color: white;">
                        <div style="max-width: 650px;">
                            <span class="badge bg-warning text-dark px-2 py-1 mb-2 font-mono small">PREVIEW HERO BANNER</span>
                            <h3 class="fw-bold mb-2" id="previewTitle" style="font-size: 1.6rem; line-height: 1.2;">{{ $settings['hero_title'] }}</h3>
                            <p class="small opacity-75 mb-3" id="previewSubtitle">{{ $settings['hero_subtitle'] }}</p>
                            <button type="button" class="btn btn-warning btn-sm fw-bold px-3 py-2 disabled text-dark" id="previewBtn">
                                {{ $settings['hero_cta_text'] }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    {{-- Thumbnail Foto --}}
                    <div class="col-lg-6">
                        <div class="p-3 border rounded h-100" style="background: var(--bg-light);">
                            <h6 class="fw-bold mb-3"><i class="bi bi-cloud-arrow-up-fill text-primary me-2"></i>Ganti Foto Thumbnail Hero</h6>
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Opsi A: Unggah Foto Baru</label>
                                <input type="file" name="hero_image_file" id="heroImageFileInput" class="form-control" accept="image/png, image/jpeg, image/jpg, image/webp">
                                <div class="form-text small">Mendukung JPG, PNG, WEBP (Maksimal 5 MB). Disarankan rasio 16:9.</div>
                            </div>

                            <div class="text-center my-2 text-muted small fw-bold">— ATAU —</div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Opsi B: Masukkan URL Gambar Eksternal</label>
                                <input type="url" name="hero_image_url" id="heroImageUrlInput" class="form-control" value="{{ $settings['hero_image'] }}" placeholder="https://images.unsplash.com/...">
                            </div>

                            <div>
                                <label class="form-label small fw-bold">Preset Gambar Cepat:</label>
                                <div class="d-flex gap-2 flex-wrap">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setHeroPreset('https://images.unsplash.com/photo-1562774053-701939374585?w=1920')">
                                        Gedung Modern
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setHeroPreset('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1920')">
                                        Kampus Akademik
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setHeroPreset('https://images.unsplash.com/photo-1509062522246-3755977927d7?w=1920')">
                                        Ruang Kelas
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Teks Hero --}}
                    <div class="col-lg-6">
                        <div class="p-3 border rounded h-100" style="background: var(--bg-light);">
                            <h6 class="fw-bold mb-3"><i class="bi bi-fonts text-primary me-2"></i>Kustomisasi Teks Banner</h6>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Judul Utama (Hero Title)</label>
                                <input type="text" name="hero_title" id="heroTitleInput" class="form-control" value="{{ $settings['hero_title'] }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Subjudul / Deskripsi Singkat</label>
                                <textarea name="hero_subtitle" id="heroSubtitleInput" rows="3" class="form-control" required>{{ $settings['hero_subtitle'] }}</textarea>
                            </div>

                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Teks Tombol CTA</label>
                                    <input type="text" name="hero_cta_text" id="heroCtaInput" class="form-control" value="{{ $settings['hero_cta_text'] }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">Link Tombol CTA</label>
                                    <input type="text" name="hero_cta_url" class="form-control" value="{{ $settings['hero_cta_url'] }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 3: VISI & MISI --}}
        <div class="tab-pane fade" id="tabVisiMisi">
            <div class="card-custom p-4">
                <h5 class="fw-bold mb-1" style="color: var(--text-dark);">
                    <i class="bi bi-bullseye text-primary me-2"></i>Visi & Misi Beranda
                </h5>
                <p class="text-muted small mb-4">Ubah konten visi dan misi sekolah yang ditampilkan pada seksi "Tentang Kami" di beranda.</p>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="p-3 border rounded h-100" style="background: var(--bg-light);">
                            <h6 class="fw-bold mb-2"><i class="bi bi-eye-fill text-primary me-2"></i>Visi Sekolah / Platform</h6>
                            <textarea name="visi_text" class="form-control" rows="6" placeholder="Tuliskan visi...">{{ $settings['visi_text'] }}</textarea>
                            <div class="form-text small">Teks visi akan tampil di kolom sebelah kiri pada seksi Tentang Kami.</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 border rounded h-100" style="background: var(--bg-light);">
                            <h6 class="fw-bold mb-2"><i class="bi bi-rocket-takeoff-fill text-success me-2"></i>Misi Sekolah / Platform</h6>
                            <textarea name="misi_text" class="form-control" rows="6" placeholder="Tuliskan poin-poin misi (pisahkan dengan baris baru)...">{{ $settings['misi_text'] }}</textarea>
                            <div class="form-text small">Setiap baris baru akan otomatis ditampilkan sebagai poin peluru (bullet point).</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 4: FOOTER & INFORMASI HAK CIPTA --}}
        <div class="tab-pane fade" id="tabFooter">
            <div class="card-custom p-4">
                <h5 class="fw-bold mb-1" style="color: var(--text-dark);">
                    <i class="bi bi-layout-text-window-reverse text-primary me-2"></i>Konten Footer Website
                </h5>
                <p class="text-muted small mb-4">Kelola teks penjelasan di footer dan hak cipta website.</p>

                <div class="row g-4">
                    <div class="col-md-8">
                        <label class="form-label small fw-bold">Deskripsi Singkat Footer</label>
                        <textarea name="footer_about" class="form-control" rows="3">{{ $settings['footer_about'] }}</textarea>
                        <div class="form-text small">Teks ini tampil di bawah nama brand pada footer beranda publik.</div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Teks Hak Cipta (Copyright)</label>
                        <input type="text" name="footer_copyright" class="form-control" value="{{ $settings['footer_copyright'] }}">
                        <div class="form-text small">Contoh: All rights reserved.</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 5: KEBIJAKAN PRIVASI & SYARAT KETENTUAN --}}
        <div class="tab-pane fade" id="tabLegal">
            <div class="card-custom p-4">
                <h5 class="fw-bold mb-1" style="color: var(--text-dark);">
                    <i class="bi bi-file-earmark-lock-fill text-primary me-2"></i>Kebijakan Privasi & Syarat Ketentuan
                </h5>
                <p class="text-muted small mb-4">Kelola isi teks lengkap halaman <code>/kebijakan-privasi</code> dan <code>/syarat-ketentuan</code>.</p>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="p-3 border rounded h-100" style="background: var(--bg-light);">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">Kebijakan Privasi (Privacy Policy)</h6>
                                <a href="{{ route('legal.privacy') }}" target="_blank" class="small text-decoration-none">Lihat Halaman</a>
                            </div>
                            <textarea name="kebijakan_privasi" class="form-control font-mono" rows="12" placeholder="Kosongkan jika ingin memakai format bawaan sistem...">{{ $settings['kebijakan_privasi'] }}</textarea>
                            <div class="form-text small">Mendukung teks biasa atau tag HTML sederhana (&lt;p&gt;, &lt;h5&gt;, &lt;ul&gt;, &lt;li&gt;). Jika dikosongkan, halaman akan menampilkan format bawaan.</div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="p-3 border rounded h-100" style="background: var(--bg-light);">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">Syarat & Ketentuan (Terms & Conditions)</h6>
                                <a href="{{ route('legal.terms') }}" target="_blank" class="small text-decoration-none">Lihat Halaman</a>
                            </div>
                            <textarea name="syarat_ketentuan" class="form-control font-mono" rows="12" placeholder="Kosongkan jika ingin memakai format bawaan sistem...">{{ $settings['syarat_ketentuan'] }}</textarea>
                            <div class="form-text small">Mendukung teks biasa atau tag HTML sederhana (&lt;p&gt;, &lt;h5&gt;, &lt;ul&gt;, &lt;li&gt;). Jika dikosongkan, halaman akan menampilkan format bawaan.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TOMBOL SIMPAN SEMUA PENGATURAN --}}
    <div class="card-custom p-3 d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <span class="text-muted small">
            <i class="bi bi-info-circle me-1"></i> Perubahan yang Anda simpan akan langsung diterapkan pada beranda publik.
        </span>
        <button type="submit" class="btn btn-primary-custom px-4">
            <i class="bi bi-check2-circle me-1"></i> Simpan Seluruh Pengaturan
        </button>
    </div>
</form>

{{-- KEAMANAN AKUN, GANTI PASSWORD & LOGOUT --}}
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card-custom p-4">
            <h5 class="fw-bold mb-1" style="color: var(--text-dark);">
                <i class="bi bi-shield-lock-fill text-primary me-2"></i>Ganti Password Administrator
            </h5>
            <p class="text-muted small mb-4">Perbarui password akun administrator Anda secara aman.</p>

            <form action="{{ route('admin.pengaturan.password') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-bold">Password Saat Ini</label>
                        <input type="password" name="current_password" class="form-control" required placeholder="Masukkan password lama">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Password Baru</label>
                        <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" required placeholder="Ulangi password baru">
                    </div>
                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary-custom px-4">
                            <i class="bi bi-key me-1"></i> Perbarui Password
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card-custom p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <h5 class="fw-bold mb-1 text-danger">
                    <i class="bi bi-box-arrow-right me-2"></i>Keluar Sesi Akun
                </h5>
                <p class="text-muted small mb-3">Akhiri sesi login administrator di perangkat ini.</p>
                <div class="alert alert-warning border-0 small py-2">
                    <i class="bi bi-info-circle me-1"></i> Pastikan seluruh perubahan pengaturan landing/brand telah Anda simpan sebelum keluar.
                </div>
            </div>
            <div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger w-100 py-2 fw-semibold">
                        <i class="bi bi-box-arrow-right me-1"></i> Keluar (Logout)
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- RESET DATA PENILAIAN --}}
<div class="card-custom p-4 border-danger border-opacity-25" style="background: #fff5f5;">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h6 class="fw-bold text-danger mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Reset Seluruh Data Penilaian Siswa</h6>
            <p class="text-muted small mb-0">Tindakan ini akan mengosongkan seluruh skor, kritik, saran, dan mereset statistik guru ke 0.</p>
        </div>
        <form action="{{ route('admin.pengaturan.reset') }}" method="POST" onsubmit="return confirm('PERINGATAN! Seluruh data penilaian siswa akan dihapus permanen. Lanjutkan?')">
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-trash3 me-1"></i> Reset Data Penilaian
            </button>
        </form>
    </div>
</div>

<script>
function setHeroPreset(url) {
    document.getElementById('heroImageUrlInput').value = url;
    document.getElementById('heroPreviewBox').style.backgroundImage = "linear-gradient(rgba(0,51,102,0.85), rgba(0,51,102,0.7)), url('" + url + "')";
}

document.addEventListener('DOMContentLoaded', function() {
    const titleInp = document.getElementById('heroTitleInput');
    const subInp = document.getElementById('heroSubtitleInput');
    const ctaInp = document.getElementById('heroCtaInput');
    const imgUrlInp = document.getElementById('heroImageUrlInput');
    const imgFileInput = document.getElementById('heroImageFileInput');
    const previewBox = document.getElementById('heroPreviewBox');

    const siteTitleInp = document.getElementById('siteTitleInput');
    const brandTitlePreview = document.getElementById('brandTitlePreview');

    if (siteTitleInp && brandTitlePreview) {
        siteTitleInp.addEventListener('input', () => brandTitlePreview.innerText = siteTitleInp.value || 'GuruKuu');
    }

    if (titleInp) titleInp.addEventListener('input', () => document.getElementById('previewTitle').innerText = titleInp.value);
    if (subInp) subInp.addEventListener('input', () => document.getElementById('previewSubtitle').innerText = subInp.value);
    if (ctaInp) ctaInp.addEventListener('input', () => document.getElementById('previewBtn').innerText = ctaInp.value);

    if (imgUrlInp) {
        imgUrlInp.addEventListener('input', () => {
            if (imgUrlInp.value) {
                previewBox.style.backgroundImage = "linear-gradient(rgba(0,51,102,0.85), rgba(0,51,102,0.7)), url('" + imgUrlInp.value + "')";
            }
        });
    }

    if (imgFileInput) {
        imgFileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewBox.style.backgroundImage = "linear-gradient(rgba(0,51,102,0.85), rgba(0,51,102,0.7)), url('" + e.target.result + "')";
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
});
</script>
@endsection