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

    <form action="{{ route('admin.pengaturan.landing') }}" method="POST" enctype="multipart/form-data" id="landingForm">
        @csrf
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
                            <label class="form-label small fw-bold">Nama Website Penuh (Title Bar)</label>
                            <input type="text" name="site_title" id="siteTitleInput" class="form-control" value="{{ $settings['site_title'] }}" placeholder="GuruKuu" required>
                            <div class="form-text small">Nama ini tampil pada title bar tab browser dan metadata web.</div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Kata Bagian 1 & Warna</label>
                                <div class="input-group">
                                    <input type="text" name="site_title_part1" id="part1Input" class="form-control" value="{{ $settings['site_title_part1'] ?? 'Guru' }}" placeholder="Guru">
                                    <input type="color" name="site_title_color1" id="color1Input" class="form-control form-control-color" value="{{ $settings['site_title_color1'] ?? '#003366' }}" title="Pilih warna bagian 1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Kata Bagian 2 & Warna</label>
                                <div class="input-group">
                                    <input type="text" name="site_title_part2" id="part2Input" class="form-control" value="{{ $settings['site_title_part2'] ?? 'Kuu' }}" placeholder="Kuu">
                                    <input type="color" name="site_title_color2" id="color2Input" class="form-control form-control-color" value="{{ $settings['site_title_color2'] ?? '#FFC107' }}" title="Pilih warna bagian 2">
                                </div>
                            </div>
                        </div>

                        <div class="p-3 rounded border" style="background: var(--bg-light);">
                            <label class="form-label small fw-bold mb-2">Live Preview Navbar Brand:</label>
                            <div class="p-3 bg-white border rounded d-flex align-items-center gap-2">
                                <img id="brandLogoPreview" src="{{ $settings['site_logo'] ?: 'data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'32\' height=\'32\' fill=\'%23003366\' class=\'bi bi-mortarboard-fill\' viewBox=\'0 0 16 16\'><path d=\'M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917z\'/></svg>' }}" 
                                     style="height: 36px; object-fit: contain;">
                                <span class="fw-bold fs-4" id="brandTitlePreview">
                                    <span id="previewPart1" style="color: {{ $settings['site_title_color1'] ?? '#003366' }};">{{ $settings['site_title_part1'] ?? 'Guru' }}</span><span id="previewPart2" style="color: {{ $settings['site_title_color2'] ?? '#FFC107' }};">{{ $settings['site_title_part2'] ?? 'Kuu' }}</span>
                                </span>
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
                        <textarea name="footer_about" class="form-control" rows="3">{{ $settings['footer_about'] ?? '' }}</textarea>
                        <div class="form-text small">Teks ini tampil di bawah nama brand pada footer beranda publik.</div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Teks Hak Cipta (Copyright)</label>
                        <input type="text" name="footer_copyright" class="form-control" value="{{ $settings['footer_copyright'] ?? '' }}">
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
    <div id="landingSaveBar" class="card-custom p-3 d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <span class="text-muted small">
            <i class="bi bi-info-circle me-1"></i> Perubahan yang Anda simpan akan langsung diterapkan pada beranda publik.
        </span>
        <button type="submit" class="btn btn-primary-custom px-4">
            <i class="bi bi-check2-circle me-1"></i> Simpan Seluruh Pengaturan
        </button>
    </div>
</form>

{{-- TAB CONTENT UNTUK PERIODE DAN KEAMANAN AKUN --}}
<div class="tab-content mb-4" id="adminUtilityTabContent">
{{-- TAB 6: PERIODE SEMESTER --}}
<div class="tab-pane fade" id="tabPeriode">
    {{-- STATUS PERIODE AKTIF SAAT INI --}}
    <div class="card-custom p-4 mb-4 border-success border-opacity-25" style="background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-success text-white" style="width: 52px; height: 52px; font-size: 1.5rem;">
                    <i class="bi bi-calendar2-check-fill"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h4 class="fw-bold mb-0 text-dark">{{ $periodeAktif ? $periodeAktif->nama_periode : 'Belum Ada Periode Aktif' }}</h4>
                        @if($periodeAktif)
                            <span class="badge bg-success px-3 py-1 fw-bold">
                                <i class="bi bi-circle-fill me-1" style="font-size: 0.55rem;"></i>Aktif Berjalan
                            </span>
                        @else
                            <span class="badge bg-danger px-3 py-1">Nonaktif</span>
                        @endif
                    </div>
                    @if($periodeAktif)
                        <div class="text-muted small">
                            Tahun Ajaran: <strong>{{ $periodeAktif->tahun_ajaran }}</strong> &bull; Semester: <strong class="text-capitalize">{{ $periodeAktif->semester }}</strong> &bull; Rentang: <span class="font-mono text-dark">{{ $periodeAktif->tanggal_mulai ? $periodeAktif->tanggal_mulai->format('d M Y H:i:s') : '-' }} s/d {{ $periodeAktif->tanggal_selesai ? $periodeAktif->tanggal_selesai->format('d M Y H:i:s') : '-' }}</span>
                        </div>
                    @else
                        <div class="text-muted small">Silakan pilih atau tambahkan periode semester baru di bawah untuk mengaktifkan penilaian siswa.</div>
                    @endif
                </div>
            </div>
            @if($periodeAktif)
                <div class="d-flex gap-2">
                    <span class="badge bg-white text-dark border p-2 font-mono">
                        <i class="bi bi-file-earmark-text text-primary me-1"></i>{{ $periodeAktif->penilaian()->count() }} Penilaian Masuk
                    </span>
                </div>
            @endif
        </div>
    </div>

    {{-- KARTU FORM ATUR & UBAH PERIODE --}}
    <div class="card-custom p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div>
                <h5 class="fw-bold mb-1" id="formPeriodeTitle">
                    <i class="bi bi-sliders2-vertical text-primary me-2"></i>Atur & Tambah Periode Semester
                </h5>
                <p class="text-muted small mb-0">Tentukan periode evaluasi. Siswa hanya dapat menilai 1 kali per periode aktif.</p>
            </div>
            {{-- TEMPLATE PRESET CEPAT --}}
            <div class="d-flex gap-2 flex-wrap">
                <button type="button" class="btn btn-sm btn-outline-primary fw-semibold" onclick="applyPeriodePreset('ganjil')">
                    <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Template: Ganjil (01 Juli - 31 Des)
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary fw-semibold" onclick="applyPeriodePreset('genap')">
                    <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Template: Genap (01 Jan - 30 Juni)
                </button>
                <button type="button" class="btn btn-sm btn-outline-success fw-semibold" onclick="applyPeriodePreset('test_1min')">
                    <i class="bi bi-stopwatch text-success me-1"></i> Uji Coba: 1 Menit Kedepan
                </button>
            </div>
        </div>

        <form action="{{ route('admin.pengaturan.periode') }}" method="POST" id="formPeriode">
            @csrf
            <input type="hidden" name="periode_id" id="periodeIdInput" value="">
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Nama Periode</label>
                    <input type="text" name="nama_periode" id="namaPeriodeInput" class="form-control" placeholder="Contoh: Semester Ganjil 2026/2027" required>
                    <div class="form-text small">Nama resmi yang tampil di header dan portal siswa.</div>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold">Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" id="tahunAjaranInput" class="form-control font-mono" placeholder="2026/2027" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold">Semester</label>
                    <select name="semester" id="semesterSelect" class="form-select" required>
                        <option value="ganjil">Semester Ganjil</option>
                        <option value="genap">Semester Genap</option>
                    </select>
                </div>

                <div class="col-md-5">
                    <label class="form-label small fw-bold">Waktu Mulai (Tanggal, Jam, Menit, Detik)</label>
                    <input type="datetime-local" step="1" name="tanggal_mulai" id="tanggalMulaiInput" class="form-control font-mono" required>
                    <div class="form-text small">Penilaian dimulai sejak waktu ini.</div>
                </div>

                <div class="col-md-5">
                    <label class="form-label small fw-bold">Waktu Berakhir (Tanggal, Jam, Menit, Detik)</label>
                    <input type="datetime-local" step="1" name="tanggal_selesai" id="tanggalSelesaiInput" class="form-control font-mono" required>
                    <div class="form-text small">Setelah waktu ini, form penilaian terkunci.</div>
                </div>

                <div class="col-md-2">
                    <label class="form-label small fw-bold">Status Periode</label>
                    <select name="status" id="statusSelect" class="form-select" required>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>

                <div class="col-12 mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetPeriodeForm()">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Form (Buat Baru)
                    </button>
                    <button type="submit" class="btn btn-primary-custom px-4" id="btnSimpanPeriode">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Periode
                    </button>
                </div>
            </div>
        </form>

        <div class="mt-4 p-3 rounded bg-light border">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-info-circle-fill text-primary mt-1"></i>
                <div class="small text-muted" style="line-height: 1.6;">
                    <strong>Aturan Otomasi Semester & Reset Nilai:</strong><br>
                    1. Saat semester baru diaktifkan, statistik & persentase guru pada dashboard dan leaderboard akan dihitung khusus untuk periode tersebut (otomatis mulai dari 0% jika belum ada ulasan).<br>
                    2. Seluruh ulasan, kritik, saran, dan nilai siswa pada periode-periode sebelumnya tetap tersimpan aman di database sebagai histori permanen.<br>
                    3. Setiap siswa hanya dapat memberikan nilai 1 kali per guru pada semester yang aktif.
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL SEMUA PERIODE --}}
    <div class="card-custom p-4">
        <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2 text-primary"></i>Daftar Riwayat Periode Semester</h5>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>NAMA PERIODE</th>
                        <th>SEMESTER & TAHUN</th>
                        <th>RENTANG WAKTU</th>
                        <th class="text-center">PENILAIAN MASUK</th>
                        <th class="text-center">STATUS</th>
                        <th class="text-end">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($semuaPeriode as $p)
                    <tr class="{{ $p->status === 'aktif' ? 'table-success bg-opacity-10' : '' }}">
                        <td>
                            <strong class="text-dark">{{ $p->nama_periode }}</strong>
                        </td>
                        <td>
                            <span class="badge {{ $p->semester === 'ganjil' ? 'bg-primary' : 'bg-info text-dark' }} text-uppercase">{{ $p->semester }}</span>
                            <span class="font-mono small ms-1">{{ $p->tahun_ajaran }}</span>
                        </td>
                        <td class="small font-mono">
                            <div>{{ $p->tanggal_mulai ? $p->tanggal_mulai->format('d/m/Y H:i:s') : '-' }}</div>
                            <div class="text-muted">s/d {{ $p->tanggal_selesai ? $p->tanggal_selesai->format('d/m/Y H:i:s') : '-' }}</div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border">{{ $p->penilaian()->count() }} Penilaian</span>
                        </td>
                        <td class="text-center">
                            @if($p->status === 'aktif')
                                <span class="badge bg-success"><i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                @if($p->status !== 'aktif')
                                    <form action="{{ route('admin.pengaturan.periode.aktifkan', $p->id) }}" method="POST" class="d-inline"
                                          data-confirm="Aktifkan periode {{ $p->nama_periode }}? Statistik leaderboard akan dihitung berdasarkan periode ini."
                                          data-confirm-title="Aktifkan Periode Penilaian"
                                          data-confirm-btn="Aktifkan"
                                          data-confirm-type="question">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Jadikan Aktif">
                                            <i class="bi bi-check-lg me-1"></i> Aktifkan
                                        </button>
                                    </form>
                                @endif
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick='editPeriode(@json($p))' title="Ubah Data">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada data periode semester.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- TAB 7: KEAMANAN & AKUN --}}
<div class="tab-pane fade" id="tabAkun">
    {{-- INFORMASI AKUN ADMIN AKTIF --}}
    <div class="card-custom p-4 mb-4 border-primary border-opacity-25" style="background: linear-gradient(135deg, #f0f7ff 0%, #ffffff 100%);">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 56px; height: 56px; font-size: 1.6rem;">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h4 class="fw-bold mb-0 text-dark">{{ auth()->user()->name }}</h4>
                        <span class="badge bg-primary px-2 py-1">Administrator Sistem</span>
                        <span class="badge bg-success px-2 py-1"><i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>Aktif</span>
                    </div>
                    <div class="text-muted small">
                        Email Login: <strong class="text-dark font-mono">{{ auth()->user()->email }}</strong> &bull; Hak Akses: <strong>Superadmin (Manajemen Penuh Sistem)</strong>
                    </div>
                </div>
            </div>
            <span class="badge bg-light text-dark border p-2 font-mono">
                <i class="bi bi-clock-history me-1 text-primary"></i> Sesi Aktif
            </span>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card-custom p-4 h-100">
                <h5 class="fw-bold mb-1" style="color: var(--text-dark);">
                    <i class="bi bi-key-fill text-primary me-2"></i>Ganti Password Administrator
                </h5>
                <p class="text-muted small mb-4">Perbarui password akun administrator Anda secara berkala demi keamanan data evaluasi sekolah.</p>

                <form action="{{ route('admin.pengaturan.password') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold">Password Saat Ini <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" class="form-control" required placeholder="Masukkan password lama Anda">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Password Baru <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required placeholder="Ulangi password baru">
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary-custom px-4">
                                <i class="bi bi-check2-circle me-1"></i> Perbarui Password Sekarang
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
                        <i class="bi bi-info-circle me-1"></i> Pastikan seluruh perubahan pengaturan telah Anda simpan sebelum keluar.
                    </div>
                </div>
                <div class="mt-3">
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
            <form action="{{ route('admin.pengaturan.reset') }}" method="POST"
                  data-confirm="PERINGATAN! Seluruh data penilaian, skor, kritik, dan saran siswa akan dihapus permanen dan statistik guru direset ke 0. Lanjutkan?"
                  data-confirm-title="Reset Seluruh Data Penilaian"
                  data-confirm-btn="Ya, Reset Semua Data"
                  data-confirm-type="danger">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-trash3 me-1"></i> Reset Data Penilaian
                </button>
            </form>
        </div>
    </div>
</div>
</div> {{-- Tutup adminUtilityTabContent --}}

<script>
function setHeroPreset(url) {
    document.getElementById('heroImageUrlInput').value = url;
    document.getElementById('heroPreviewBox').style.backgroundImage = "linear-gradient(rgba(0,51,102,0.85), rgba(0,51,102,0.7)), url('" + url + "')";
}

function padZero(num) {
    return num.toString().padStart(2, '0');
}

function formatDatetimeLocal(date) {
    const year = date.getFullYear();
    const month = padZero(date.getMonth() + 1);
    const day = padZero(date.getDate());
    const hours = padZero(date.getHours());
    const minutes = padZero(date.getMinutes());
    const seconds = padZero(date.getSeconds());
    return `${year}-${month}-${day}T${hours}:${minutes}:${seconds}`;
}

function applyPeriodePreset(type) {
    const now = new Date();
    const curYear = now.getFullYear();
    const namaInp = document.getElementById('namaPeriodeInput');
    const thInp = document.getElementById('tahunAjaranInput');
    const smtSelect = document.getElementById('semesterSelect');
    const startInp = document.getElementById('tanggalMulaiInput');
    const endInp = document.getElementById('tanggalSelesaiInput');
    const statusSelect = document.getElementById('statusSelect');

    if (type === 'ganjil') {
        const nextYear = curYear + 1;
        namaInp.value = `Semester Ganjil ${curYear}/${nextYear}`;
        thInp.value = `${curYear}/${nextYear}`;
        smtSelect.value = 'ganjil';
        startInp.value = `${curYear}-07-01T00:00:00`;
        endInp.value = `${curYear}-12-31T23:59:59`;
        statusSelect.value = 'aktif';
    } else if (type === 'genap') {
        const nextYear = curYear + 1;
        namaInp.value = `Semester Genap ${curYear}/${nextYear}`;
        thInp.value = `${curYear}/${nextYear}`;
        smtSelect.value = 'genap';
        startInp.value = `${nextYear}-01-01T00:00:00`;
        endInp.value = `${nextYear}-06-30T23:59:59`;
        statusSelect.value = 'aktif';
    } else if (type === 'test_1min') {
        const nextYear = curYear + 1;
        const endTime = new Date(now.getTime() + 60 * 1000); // 1 minute in the future
        namaInp.value = `Uji Coba 1 Menit (${padZero(now.getHours())}:${padZero(now.getMinutes())}:${padZero(now.getSeconds())})`;
        thInp.value = `${curYear}/${nextYear}`;
        smtSelect.value = 'ganjil';
        startInp.value = formatDatetimeLocal(now);
        endInp.value = formatDatetimeLocal(endTime);
        statusSelect.value = 'aktif';
    }
}

function editPeriode(p) {
    document.getElementById('periodeIdInput').value = p.id;
    document.getElementById('namaPeriodeInput').value = p.nama_periode || '';
    document.getElementById('tahunAjaranInput').value = p.tahun_ajaran || '';
    document.getElementById('semesterSelect').value = p.semester || 'ganjil';
    
    // Format datetime string for input
    if (p.tanggal_mulai) {
        const dStart = new Date(p.tanggal_mulai);
        document.getElementById('tanggalMulaiInput').value = !isNaN(dStart) ? formatDatetimeLocal(dStart) : p.tanggal_mulai.substring(0, 19).replace(' ', 'T');
    }
    if (p.tanggal_selesai) {
        const dEnd = new Date(p.tanggal_selesai);
        document.getElementById('tanggalSelesaiInput').value = !isNaN(dEnd) ? formatDatetimeLocal(dEnd) : p.tanggal_selesai.substring(0, 19).replace(' ', 'T');
    }
    document.getElementById('statusSelect').value = p.status || 'aktif';

    const btn = document.getElementById('btnSimpanPeriode');
    btn.innerHTML = '<i class="bi bi-pencil-square me-1"></i> Perbarui Periode #' + p.id;
    btn.className = 'btn btn-warning px-4 fw-bold';

    // Scroll to form
    document.getElementById('formPeriode').scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function resetPeriodeForm() {
    document.getElementById('periodeIdInput').value = '';
    document.getElementById('formPeriode').reset();
    const btn = document.getElementById('btnSimpanPeriode');
    btn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Simpan Periode';
    btn.className = 'btn btn-primary-custom px-4';
}

document.addEventListener('DOMContentLoaded', function() {
    // Tab hash persistence
    const hash = window.location.hash;
    if (hash) {
        const triggerEl = document.querySelector(`button[data-bs-target="${hash}"]`);
        if (triggerEl) {
            const tab = new bootstrap.Tab(triggerEl);
            tab.show();
        }
    }
    document.querySelectorAll('button[data-bs-toggle="pill"]').forEach(tabBtn => {
        tabBtn.addEventListener('shown.bs.tab', (e) => {
            const target = e.target.getAttribute('data-bs-target');
            if (target) history.replaceState(null, null, target);
        });
    });

    // Dual-Color Brand live preview
    const part1Inp = document.getElementById('part1Input');
    const part2Inp = document.getElementById('part2Input');
    const color1Inp = document.getElementById('color1Input');
    const color2Inp = document.getElementById('color2Input');
    const previewPart1 = document.getElementById('previewPart1');
    const previewPart2 = document.getElementById('previewPart2');

    function updateBrandPreview() {
        if (previewPart1 && part1Inp && color1Inp) {
            previewPart1.innerText = part1Inp.value || 'Guru';
            previewPart1.style.color = color1Inp.value;
        }
        if (previewPart2 && part2Inp && color2Inp) {
            previewPart2.innerText = part2Inp.value || 'Kuu';
            previewPart2.style.color = color2Inp.value;
        }
    }

    if (part1Inp) part1Inp.addEventListener('input', updateBrandPreview);
    if (part2Inp) part2Inp.addEventListener('input', updateBrandPreview);
    if (color1Inp) color1Inp.addEventListener('input', updateBrandPreview);
    if (color2Inp) color2Inp.addEventListener('input', updateBrandPreview);

    // Site logo file live preview
    const siteLogoFileInput = document.getElementById('siteLogoFileInput');
    const brandLogoPreview = document.getElementById('brandLogoPreview');
    if (siteLogoFileInput && brandLogoPreview) {
        siteLogoFileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    brandLogoPreview.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // Hero Live Previews
    const titleInp = document.getElementById('heroTitleInput');
    const subInp = document.getElementById('heroSubtitleInput');
    const ctaInp = document.getElementById('heroCtaInput');
    const imgUrlInp = document.getElementById('heroImageUrlInput');
    const imgFileInput = document.getElementById('heroImageFileInput');
    const previewBox = document.getElementById('heroPreviewBox');

    if (titleInp) titleInp.addEventListener('input', () => document.getElementById('previewTitle').innerText = titleInp.value);
    if (subInp) subInp.addEventListener('input', () => document.getElementById('previewSubtitle').innerText = subInp.value);
    if (ctaInp) ctaInp.addEventListener('input', () => document.getElementById('previewBtn').innerText = ctaInp.value);

    if (imgUrlInp && previewBox) {
        imgUrlInp.addEventListener('input', () => {
            if (imgUrlInp.value) {
                previewBox.style.backgroundImage = "linear-gradient(rgba(0,51,102,0.85), rgba(0,51,102,0.7)), url('" + imgUrlInp.value + "')";
            }
        });
    }

    if (imgFileInput && previewBox) {
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

    // Tab Switching & Save Bar Visibility Management
    const tabButtons = document.querySelectorAll('#pengaturanTabs button[data-bs-toggle="pill"]');
    const landingSaveBar = document.getElementById('landingSaveBar');
    const landingTabs = ['#tabBrand', '#tabHero', '#tabVisiMisi', '#tabFooter', '#tabLegal'];

    function activateTabByTarget(target) {
        // Toggle visibility of landing save bar
        if (landingSaveBar) {
            landingSaveBar.style.display = landingTabs.includes(target) ? 'flex' : 'none';
        }

        // Deactivate all tab panes across all tab containers
        document.querySelectorAll('.tab-pane').forEach(p => {
            p.classList.remove('show', 'active');
        });

        // Activate the target pane
        const activePane = document.querySelector(target);
        if (activePane) {
            activePane.classList.add('show', 'active');
        }

        // Activate the button
        tabButtons.forEach(btn => {
            if (btn.getAttribute('data-bs-target') === target) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    tabButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const target = this.getAttribute('data-bs-target');
            activateTabByTarget(target);
            if (history.pushState) {
                history.pushState(null, null, target);
            } else {
                window.location.hash = target;
            }
        });
    });

    // Handle hash on initial load (e.g. #tabAkun or #tabPeriode)
    const initialHash = window.location.hash;
    if (initialHash && document.querySelector(initialHash)) {
        activateTabByTarget(initialHash);
    }
});
</script>
@endsection