@extends('layouts.admin')

@section('title', 'Pengaturan')

@section('content')
<style>
    .settings-page { max-width: 1180px; padding-bottom: 6rem; }
    .settings-nav { display: flex; flex-wrap: wrap; gap: .5rem; margin-bottom: 1.25rem; }
    .settings-nav__item { display: inline-flex; align-items: center; justify-content: center; gap: .45rem; min-height: 44px; padding: .6rem .85rem; color: var(--text-muted); background: var(--bg-card); border: 1px solid var(--border); border-radius: .65rem; font-size: .84rem; font-weight: 600; transition: .18s ease; flex: 1 1 auto; white-space: nowrap; }
    .settings-nav__item:hover { color: var(--primary); border-color: var(--primary); }
    .settings-nav__item.active { color: #fff; background: var(--primary); border-color: var(--primary); }
    .settings-card { padding: 1.25rem; background: var(--bg-card); border: 1px solid var(--border); border-radius: .85rem; box-shadow: var(--card-shadow); }
    .settings-card + .settings-card { margin-top: 1rem; }
    .settings-heading { font-size: 1rem; font-weight: 700; margin: 0; color: var(--text-dark); }
    .settings-heading i { color: var(--primary); }
    .settings-preview { min-height: 112px; display: flex; align-items: center; padding: 1.25rem; border: 1px solid var(--border); border-radius: .7rem; background: var(--bg-light); }
    .settings-hero { min-height: 190px; display: flex; align-items: end; padding: 1.25rem; color: #fff; background-position: center; background-size: cover; border-radius: .7rem; overflow: hidden; }
    .settings-save { position: sticky; bottom: 1rem; z-index: 100; display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: .75rem 1rem; margin-top: 1rem; background: var(--bg-card); border: 1px solid var(--border); border-radius: .75rem; box-shadow: 0 8px 24px rgba(0,0,0,0.12); }
    .settings-panel[hidden] { display: none !important; }
    .settings-panel .form-label { margin-bottom: .35rem; font-size: .82rem; font-weight: 600; }
    .settings-muted { color: var(--text-muted); font-size: .84rem; }
    .settings-summary { cursor: pointer; color: var(--primary); font-size: .84rem; font-weight: 600; }
    .settings-icon-button { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
    @media (max-width: 767.98px) { .settings-nav { display: flex; overflow-x: auto; flex-wrap: nowrap; padding-bottom: .25rem; } .settings-nav__item { flex: 0 0 auto; min-width: 105px; } }
    @media (max-width: 575.98px) { .settings-card { padding: 1rem; } .settings-save { align-items: stretch; flex-direction: column; } .settings-save .btn { width: 100%; } }
</style>

<div class="settings-page">
    <div class="page-header d-flex justify-content-between align-items-center gap-3">
        <div>
            <div class="page-label">ADMIN</div>
            <h1 class="page-title mb-0">Pengaturan</h1>
        </div>
        <a href="{{ url('/') }}" target="_blank" class="btn btn-outline-custom btn-sm settings-icon-button" title="Lihat beranda" aria-label="Lihat beranda"><i class="bi bi-box-arrow-up-right"></i></a>
    </div>

    <nav class="settings-nav" id="pengaturanTabs" aria-label="Kategori pengaturan">
        <button class="settings-nav__item active" type="button" data-target="#tabBrand"><i class="bi bi-stars"></i> Identitas</button>
        <button class="settings-nav__item" type="button" data-target="#tabHero"><i class="bi bi-image"></i> Beranda & Slider</button>
        <button class="settings-nav__item" type="button" data-target="#tabKonten"><i class="bi bi-card-checklist"></i> Panduan & Beranda</button>
        <button class="settings-nav__item" type="button" data-target="#tabVisiMisi"><i class="bi bi-bullseye"></i> Profil & Fitur</button>
        <button class="settings-nav__item" type="button" data-target="#tabFooter"><i class="bi bi-layout-text-window-reverse"></i> Footer</button>
        <button class="settings-nav__item" type="button" data-target="#tabLegal"><i class="bi bi-file-earmark-lock"></i> Legal</button>
        <button class="settings-nav__item" type="button" data-target="#tabModerasi"><i class="bi bi-shield-exclamation"></i> Moderasi</button>
        <button class="settings-nav__item" type="button" data-target="#tabPeriode"><i class="bi bi-calendar-range"></i> Periode</button>
        <button class="settings-nav__item" type="button" data-target="#tabAkun"><i class="bi bi-shield-lock"></i> Akun</button>
        <button class="settings-nav__item" type="button" data-target="#tabPerangkat"><i class="bi bi-laptop"></i> Perangkat</button>
        <button class="settings-nav__item" type="button" data-target="#tabFaq"><i class="bi bi-question-circle"></i> FAQ</button>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
            <i class="bi bi-exclamation-octagon-fill fs-5"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Terjadi kesalahan saat menyimpan pengaturan:</div>
            <ul class="mb-0 ps-3 small">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.pengaturan.landing') }}" method="POST" enctype="multipart/form-data" id="landingForm" novalidate>
        @csrf

        <section class="settings-panel" id="tabBrand">
            <div class="settings-card">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h2 class="settings-heading"><i class="bi bi-stars me-2"></i>Identitas website</h2>
                    <span class="settings-muted">Tampilan brand</span>
                </div>
                <div class="row g-4 align-items-center">
                    <div class="col-lg-5">
                        <div class="settings-preview gap-3">
                            @if($settings['site_logo'])
                                <img id="brandLogoPreview" src="{{ $settings['site_logo'] }}" alt="Logo" style="width:42px;height:42px;object-fit:contain;">
                            @else
                                <i id="brandLogoIcon" class="bi bi-mortarboard-fill fs-2 text-primary"></i>
                                <img id="brandLogoPreview" src="" alt="Logo" class="d-none" style="width:42px;height:42px;object-fit:contain;">
                            @endif
                            <span class="fw-bold fs-4" id="brandTitlePreview"><span id="previewPart1" style="color: {{ $settings['site_title_color1'] }};">{{ $settings['site_title_part1'] }}</span><span id="previewPart2" style="color: {{ $settings['site_title_color2'] }};">{{ $settings['site_title_part2'] }}</span></span>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="siteTitleInput">Nama website</label>
                                <input type="text" name="site_title" id="siteTitleInput" class="form-control" value="{{ $settings['site_title'] }}" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="part1Input">Nama bagian 1</label>
                                <div class="input-group">
                                    <input type="text" name="site_title_part1" id="part1Input" class="form-control" value="{{ $settings['site_title_part1'] }}">
                                    <input type="color" name="site_title_color1" id="color1Input" class="form-control form-control-color" value="{{ $settings['site_title_color1'] }}" title="Warna bagian 1">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label" for="part2Input">Nama bagian 2</label>
                                <div class="input-group">
                                    <input type="text" name="site_title_part2" id="part2Input" class="form-control" value="{{ $settings['site_title_part2'] }}">
                                    <input type="color" name="site_title_color2" id="color2Input" class="form-control form-control-color" value="{{ $settings['site_title_color2'] }}" title="Warna bagian 2">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="siteLogoFileInput">Logo</label>
                                <input type="file" name="site_logo_file" id="siteLogoFileInput" class="form-control" accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml">
                            </div>
                            <div class="col-12">
                                <details>
                                    <summary class="settings-summary">Opsi logo</summary>
                                    <div class="row g-3 pt-3">
                                        <div class="col-sm-8">
                                            <label class="form-label" for="siteLogoUrlInput">URL logo</label>
                                            <input type="url" name="site_logo_url" id="siteLogoUrlInput" class="form-control" value="{{ $settings['site_logo'] }}" placeholder="https://...">
                                        </div>
                                        @if($settings['site_logo'])
                                            <div class="col-sm-4 d-flex align-items-end">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="remove_logo" value="1" id="removeLogoCheck">
                                                    <label class="form-check-label small" for="removeLogoCheck">Gunakan ikon bawaan</label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </details>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="settings-panel" id="tabHero" hidden>
            {{-- 1. KONTEN TEKS HERO --}}
            <div class="settings-card mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div>
                        <h2 class="settings-heading mb-1"><i class="bi bi-card-heading me-2"></i>Konten Teks & Tombol Beranda</h2>
                        <p class="settings-muted mb-0">Atur teks utama, judul, deskripsi, dan tombol ajakan bertindak (CTA) pada banner beranda.</p>
                    </div>
                </div>

                {{-- Hero Badge --}}
                <div class="mb-3 p-3 rounded-3 bg-light border">
                    <label class="form-label fw-bold small text-uppercase" for="heroBadgeInput"><i class="bi bi-patch-check-fill text-warning me-1"></i> Teks Lencana / Badge Hero</label>
                    <input type="text" name="hero_badge" id="heroBadgeInput" class="form-control" value="{{ $settings['hero_badge'] }}" placeholder="SMK NEGERI 1 BANGSRI • JUARA">
                </div>

                <div class="row g-3">
                    <div class="col-lg-7">
                        <label class="form-label fw-bold" for="heroTitleInput">Judul Utama Hero</label>
                        <input type="text" name="hero_title" id="heroTitleInput" class="form-control" value="{{ $settings['hero_title'] }}" placeholder="Judul banner utama..." required>
                    </div>
                    <div class="col-lg-5">
                        <label class="form-label fw-bold" for="heroCtaInput">Teks Tombol Utama</label>
                        <input type="text" name="hero_cta_text" id="heroCtaInput" class="form-control" value="{{ $settings['hero_cta_text'] }}" placeholder="Contoh: Siap Memulai?">
                    </div>
                    <div class="col-lg-7">
                        <label class="form-label fw-bold" for="heroSubtitleInput">Deskripsi / Subtitle</label>
                        <textarea name="hero_subtitle" id="heroSubtitleInput" rows="2" class="form-control" placeholder="Tulis deskripsi singkat penjelas...">{{ $settings['hero_subtitle'] }}</textarea>
                    </div>
                    <div class="col-lg-5">
                        <label class="form-label fw-bold" for="heroCtaUrlInput">URL / Link Tombol</label>
                        <input type="text" name="hero_cta_url" id="heroCtaUrlInput" class="form-control" value="{{ $settings['hero_cta_url'] }}" placeholder="/login atau #panduan">
                    </div>
                </div>

                {{-- Preview Live --}}
                <div class="mt-4">
                    <label class="form-label small text-muted text-uppercase fw-semibold"><i class="bi bi-eye me-1"></i> Live Preview Banner Teks</label>
                    <div id="heroPreviewBox" class="settings-hero rounded-4 position-relative overflow-hidden shadow-sm" style="background-image:linear-gradient(rgba(10,25,47,.82),rgba(0,51,102,.75)),url('{{ $settings['hero_image'] }}'); min-height: 180px; padding: 1.75rem;">
                        <div class="mw-100" style="max-width:680px;">
                            <span class="badge bg-warning text-dark mb-2 px-2.5 py-1 fw-bold" id="previewBadge">{{ $settings['hero_badge'] }}</span>
                            <h3 class="h5 fw-bold text-white mb-2" id="previewTitle">{{ $settings['hero_title'] }}</h3>
                            <p class="mb-0 text-white-50 small" id="previewSubtitle">{{ $settings['hero_subtitle'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. GAMBAR THUMBNAIL SLIDER BERGANTI OTOMATIS --}}
            <div class="settings-card mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div>
                        <h2 class="settings-heading mb-1"><i class="bi bi-images me-2"></i>Gambar Slider Beranda (Hingga 3 Gambar Berganti Otomatis)</h2>
                        <p class="settings-muted mb-0">Cukup tentukan gambar thumbnail untuk Slide 1, 2, dan 3. Gambar akan berganti sendiri secara otomatis di latar belakang.</p>
                    </div>
                    <span class="badge bg-primary-subtle text-primary border px-3 py-1.5 rounded-pill font-mono small">
                        <i class="bi bi-play-circle me-1"></i> Autoplay Slideshow
                    </span>
                </div>

                <div class="row g-3">
                    {{-- SLOT 1 --}}
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-light h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-bold text-primary small"><i class="bi bi-1-circle-fill me-1"></i> Gambar 1 (Utama - Wajib)</span>
                                    <span class="badge bg-success" style="font-size: 0.7rem;">Wajib</span>
                                </div>
                                <div class="mb-2 text-center" id="previewHero1Wrapper">
                                    <img src="{{ $settings['hero_image'] }}" alt="Thumbnail 1" class="rounded border shadow-sm w-100" style="height: 110px; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1562774053-701939374585?w=600'">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small" for="heroImageFileInput">Upload Gambar</label>
                                    <input type="file" name="hero_image_file" id="heroImageFileInput" class="form-control form-control-sm" accept="image/png,image/jpeg,image/jpg,image/webp">
                                </div>
                                <div>
                                    <label class="form-label small" for="heroImageUrlInput">atau URL Gambar</label>
                                    <input type="url" name="hero_image_url" id="heroImageUrlInput" class="form-control form-control-sm" value="{{ $settings['hero_image'] }}" placeholder="https://...">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SLOT 2 --}}
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-light h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-bold text-secondary small"><i class="bi bi-2-circle-fill me-1"></i> Gambar 2 (Opsional)</span>
                                    @if($settings['hero_image_2'])
                                        <div class="form-check form-check-inline m-0">
                                            <input class="form-check-input" type="checkbox" name="remove_hero_2" value="1" id="removeHero2Check">
                                            <label class="form-check-label text-danger small" for="removeHero2Check">Hapus</label>
                                        </div>
                                    @else
                                        <span id="badgeHero2" class="badge bg-secondary-subtle text-secondary" style="font-size: 0.7rem;">Nonaktif</span>
                                    @endif
                                </div>
                                <div class="mb-2 text-center" id="previewHero2Wrapper">
                                    @if($settings['hero_image_2'])
                                        <img src="{{ $settings['hero_image_2'] }}" alt="Thumbnail 2" class="rounded border shadow-sm w-100" style="height: 110px; object-fit: cover;">
                                    @else
                                        <div class="rounded border border-dashed d-flex align-items-center justify-content-center text-muted small w-100 bg-white" style="height: 110px;">
                                            <span><i class="bi bi-image me-1"></i> Belum ada gambar</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small" for="heroImageFile2Input">Upload Gambar</label>
                                    <input type="file" name="hero_image_file_2" id="heroImageFile2Input" class="form-control form-control-sm" accept="image/png,image/jpeg,image/jpg,image/webp">
                                </div>
                                <div>
                                    <label class="form-label small" for="heroImageUrl2Input">atau URL Gambar</label>
                                    <input type="url" name="hero_image_url_2" id="heroImageUrl2Input" class="form-control form-control-sm" value="{{ $settings['hero_image_2'] }}" placeholder="https://...">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SLOT 3 --}}
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-light h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-bold text-secondary small"><i class="bi bi-3-circle-fill me-1"></i> Gambar 3 (Opsional)</span>
                                    @if($settings['hero_image_3'])
                                        <div class="form-check form-check-inline m-0">
                                            <input class="form-check-input" type="checkbox" name="remove_hero_3" value="1" id="removeHero3Check">
                                            <label class="form-check-label text-danger small" for="removeHero3Check">Hapus</label>
                                        </div>
                                    @else
                                        <span id="badgeHero3" class="badge bg-secondary-subtle text-secondary" style="font-size: 0.7rem;">Nonaktif</span>
                                    @endif
                                </div>
                                <div class="mb-2 text-center" id="previewHero3Wrapper">
                                    @if($settings['hero_image_3'])
                                        <img src="{{ $settings['hero_image_3'] }}" alt="Thumbnail 3" class="rounded border shadow-sm w-100" style="height: 110px; object-fit: cover;">
                                    @else
                                        <div class="rounded border border-dashed d-flex align-items-center justify-content-center text-muted small w-100 bg-white" style="height: 110px;">
                                            <span><i class="bi bi-image me-1"></i> Belum ada gambar</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small" for="heroImageFile3Input">Upload Gambar</label>
                                    <input type="file" name="hero_image_file_3" id="heroImageFile3Input" class="form-control form-control-sm" accept="image/png,image/jpeg,image/jpg,image/webp">
                                </div>
                                <div>
                                    <label class="form-label small" for="heroImageUrl3Input">atau URL Gambar</label>
                                    <input type="url" name="hero_image_url_3" id="heroImageUrl3Input" class="form-control form-control-sm" value="{{ $settings['hero_image_3'] }}" placeholder="https://...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- TAB KONTEN & PANDUAN --}}
        <section class="settings-panel" id="tabKonten" hidden>
            <div class="settings-card mb-4">
                <h2 class="settings-heading mb-1"><i class="bi bi-card-checklist me-2"></i>Panduan Penggunaan (3 Langkah)</h2>
                <p class="settings-muted mb-4">Kelola judul, subjudul, dan isi ketiga langkah panduan yang tampil di beranda publik.</p>
                
                <div class="row g-3 mb-4 p-3 bg-light rounded-3 border">
                    <div class="col-md-4">
                        <label class="form-label" for="panduanLabelInput">Label Seksi</label>
                        <input type="text" name="panduan_label" id="panduanLabelInput" class="form-control" value="{{ $settings['panduan_label'] }}">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label" for="panduanTitleInput">Judul Seksi Panduan</label>
                        <input type="text" name="panduan_title" id="panduanTitleInput" class="form-control" value="{{ $settings['panduan_title'] }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="panduanSubtitleInput">Deskripsi Singkat / Subtitle</label>
                        <input type="text" name="panduan_subtitle" id="panduanSubtitleInput" class="form-control" value="{{ $settings['panduan_subtitle'] }}">
                    </div>
                </div>

                <div class="row g-3">
                    {{-- Langkah 1 --}}
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 h-100 bg-white shadow-sm">
                            <span class="badge bg-primary mb-2">Langkah 1</span>
                            <div class="mb-3">
                                <label class="form-label small fw-bold" for="step1Title">Judul Langkah 1</label>
                                <input type="text" name="panduan_step1_title" id="step1Title" class="form-control" value="{{ $settings['panduan_step1_title'] }}">
                            </div>
                            <div>
                                <label class="form-label small fw-bold" for="step1Desc">Deskripsi Langkah 1</label>
                                <textarea name="panduan_step1_desc" id="step1Desc" rows="3" class="form-control">{{ $settings['panduan_step1_desc'] }}</textarea>
                            </div>
                        </div>
                    </div>
                    {{-- Langkah 2 --}}
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 h-100 bg-white shadow-sm">
                            <span class="badge bg-success mb-2">Langkah 2</span>
                            <div class="mb-3">
                                <label class="form-label small fw-bold" for="step2Title">Judul Langkah 2</label>
                                <input type="text" name="panduan_step2_title" id="step2Title" class="form-control" value="{{ $settings['panduan_step2_title'] }}">
                            </div>
                            <div>
                                <label class="form-label small fw-bold" for="step2Desc">Deskripsi Langkah 2</label>
                                <textarea name="panduan_step2_desc" id="step2Desc" rows="3" class="form-control">{{ $settings['panduan_step2_desc'] }}</textarea>
                            </div>
                        </div>
                    </div>
                    {{-- Langkah 3 --}}
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 h-100 bg-white shadow-sm">
                            <span class="badge bg-warning text-dark mb-2">Langkah 3</span>
                            <div class="mb-3">
                                <label class="form-label small fw-bold" for="step3Title">Judul Langkah 3</label>
                                <input type="text" name="panduan_step3_title" id="step3Title" class="form-control" value="{{ $settings['panduan_step3_title'] }}">
                            </div>
                            <div>
                                <label class="form-label small fw-bold" for="step3Desc">Deskripsi Langkah 3</label>
                                <textarea name="panduan_step3_desc" id="step3Desc" rows="3" class="form-control">{{ $settings['panduan_step3_desc'] }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="settings-card">
                <h2 class="settings-heading mb-1"><i class="bi bi-bar-chart-line me-2"></i>Teks Seksi Statistik & Leaderboard Beranda</h2>
                <p class="settings-muted mb-4">Ubah judul dan teks keterangan pada seksi data statistik dan leaderboard di beranda publik.</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-white shadow-sm">
                            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-graph-up me-1"></i> Seksi Statistik</h6>
                            <div class="mb-3">
                                <label class="form-label small" for="statsTitleInput">Judul Seksi Statistik</label>
                                <input type="text" name="stats_title" id="statsTitleInput" class="form-control" value="{{ $settings['stats_title'] }}">
                            </div>
                            <div>
                                <label class="form-label small" for="statsSubtitleInput">Deskripsi Statistik</label>
                                <input type="text" name="stats_subtitle" id="statsSubtitleInput" class="form-control" value="{{ $settings['stats_subtitle'] }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 bg-white shadow-sm">
                            <h6 class="fw-bold text-warning mb-3"><i class="bi bi-trophy me-1"></i> Seksi Leaderboard</h6>
                            <div class="mb-3">
                                <label class="form-label small" for="lbTitleInput">Judul Seksi Leaderboard</label>
                                <input type="text" name="leaderboard_title" id="lbTitleInput" class="form-control" value="{{ $settings['leaderboard_title'] }}">
                            </div>
                            <div>
                                <label class="form-label small" for="lbSubtitleInput">Deskripsi Leaderboard</label>
                                <input type="text" name="leaderboard_subtitle" id="lbSubtitleInput" class="form-control" value="{{ $settings['leaderboard_subtitle'] }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="settings-panel" id="tabVisiMisi" hidden>
            <div class="settings-card mb-4">
                <h2 class="settings-heading mb-1"><i class="bi bi-bullseye me-2"></i>Profil Beranda & Visi Misi</h2>
                <p class="settings-muted mb-4">Atur judul pengantar, teks visi, dan rincian misi sekolah.</p>
                <div class="row g-3 mb-4">
                    <div class="col-md-5">
                        <label class="form-label" for="aboutTitleInput">Judul Seksi Tentang</label>
                        <input type="text" name="about_title" id="aboutTitleInput" class="form-control" value="{{ $settings['about_title'] }}">
                    </div>
                    <div class="col-md-7">
                        <label class="form-label" for="aboutSubtitleInput">Deskripsi Singkat Tentang</label>
                        <input type="text" name="about_subtitle" id="aboutSubtitleInput" class="form-control" value="{{ $settings['about_subtitle'] }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="visiTextInput">Visi Kami</label>
                        <textarea name="visi_text" id="visiTextInput" class="form-control" rows="6" placeholder="Tulis visi...">{{ $settings['visi_text'] }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="misiTextInput">Misi Kami (Satu poin per baris)</label>
                        <textarea name="misi_text" id="misiTextInput" class="form-control" rows="6" placeholder="Satu poin per baris...">{{ $settings['misi_text'] }}</textarea>
                    </div>
                </div>
            </div>

            <div class="settings-card">
                <h2 class="settings-heading mb-1"><i class="bi bi-star-fill text-warning me-2"></i>3 Fitur Unggulan di Bawah Visi Misi</h2>
                <p class="settings-muted mb-4">Kelola judul dan deskripsi 3 kotak fitur unggulan yang tampil di bagian Tentang Kami.</p>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-white shadow-sm">
                            <span class="badge bg-primary mb-2"><i class="bi bi-shield-check me-1"></i> Fitur 1</span>
                            <div class="mb-2">
                                <label class="form-label small fw-bold" for="feat1Title">Judul Fitur 1</label>
                                <input type="text" name="feature1_title" id="feat1Title" class="form-control" value="{{ $settings['feature1_title'] }}">
                            </div>
                            <div>
                                <label class="form-label small fw-bold" for="feat1Desc">Deskripsi Fitur 1</label>
                                <textarea name="feature1_desc" id="feat1Desc" rows="3" class="form-control">{{ $settings['feature1_desc'] }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-white shadow-sm">
                            <span class="badge bg-success mb-2"><i class="bi bi-graph-up-arrow me-1"></i> Fitur 2</span>
                            <div class="mb-2">
                                <label class="form-label small fw-bold" for="feat2Title">Judul Fitur 2</label>
                                <input type="text" name="feature2_title" id="feat2Title" class="form-control" value="{{ $settings['feature2_title'] }}">
                            </div>
                            <div>
                                <label class="form-label small fw-bold" for="feat2Desc">Deskripsi Fitur 2</label>
                                <textarea name="feature2_desc" id="feat2Desc" rows="3" class="form-control">{{ $settings['feature2_desc'] }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 bg-white shadow-sm">
                            <span class="badge bg-warning text-dark mb-2"><i class="bi bi-people-fill me-1"></i> Fitur 3</span>
                            <div class="mb-2">
                                <label class="form-label small fw-bold" for="feat3Title">Judul Fitur 3</label>
                                <input type="text" name="feature3_title" id="feat3Title" class="form-control" value="{{ $settings['feature3_title'] }}">
                            </div>
                            <div>
                                <label class="form-label small fw-bold" for="feat3Desc">Deskripsi Fitur 3</label>
                                <textarea name="feature3_desc" id="feat3Desc" rows="3" class="form-control">{{ $settings['feature3_desc'] }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="settings-panel" id="tabFooter" hidden>
            <div class="settings-card">
                <h2 class="settings-heading mb-4"><i class="bi bi-layout-text-window-reverse me-2"></i>Footer</h2>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label" for="footerAboutInput">Deskripsi</label>
                        <textarea name="footer_about" id="footerAboutInput" class="form-control" rows="4">{{ $settings['footer_about'] }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="footerCopyrightInput">Hak cipta</label>
                        <input type="text" name="footer_copyright" id="footerCopyrightInput" class="form-control" value="{{ $settings['footer_copyright'] }}">
                    </div>
                </div>
            </div>
        </section>

        <section class="settings-panel" id="tabLegal" hidden>
            <div class="settings-card">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h2 class="settings-heading"><i class="bi bi-file-earmark-lock me-2"></i>Legal</h2>
                    <div class="d-flex gap-2">
                        <a href="{{ route('legal.privacy') }}" target="_blank" class="btn btn-light border btn-sm settings-icon-button" title="Lihat kebijakan privasi" aria-label="Lihat kebijakan privasi"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('legal.terms') }}" target="_blank" class="btn btn-light border btn-sm settings-icon-button" title="Lihat syarat dan ketentuan" aria-label="Lihat syarat dan ketentuan"><i class="bi bi-box-arrow-up-right"></i></a>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="form-label" for="privacyInput">Kebijakan privasi</label>
                        <textarea name="kebijakan_privasi" id="privacyInput" class="form-control font-mono" rows="13">{{ $settings['kebijakan_privasi'] }}</textarea>
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label" for="termsInput">Syarat & ketentuan</label>
                        <textarea name="syarat_ketentuan" id="termsInput" class="form-control font-mono" rows="13">{{ $settings['syarat_ketentuan'] }}</textarea>
                    </div>
                </div>
            </div>
        </section>

        <section class="settings-panel" id="tabModerasi" hidden>
            <div class="settings-card mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <div>
                        <h2 class="settings-heading mb-1"><i class="bi bi-shield-exclamation me-2"></i>Sistem Filter Moderasi Kata Kotor & Kasar</h2>
                        <p class="settings-muted mb-0">Sistem menggunakan library cerdas bawaan (Indonesia & English) plus kata tambahan kustom yang dapat diperluas oleh admin.</p>
                    </div>
                    <button type="button" class="btn btn-primary-custom btn-sm px-3 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalLihatKata">
                        <i class="bi bi-journal-text me-1"></i> Buka Kamus Library Kata ({{ count($allBadWords) }})
                    </button>
                </div>

                {{-- STATISTIK KATA BADGES --}}
                <div class="row g-3 mb-4">
                    <div class="col-sm-4">
                        <div class="p-3 bg-light rounded-3 border text-center">
                            <div class="fs-4 fw-bold text-danger">{{ count($allBadWords) }}</div>
                            <div class="text-muted small">Total Kata Terfilter Aktif</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-3 bg-light rounded-3 border text-center">
                            <div class="fs-4 fw-bold text-primary">{{ count($defaultBadWords) }}</div>
                            <div class="text-muted small">Library Bawaan Sistem</div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-3 bg-light rounded-3 border text-center">
                            <div class="fs-4 fw-bold text-warning">{{ count($customBadWords) }}</div>
                            <div class="text-muted small">Kata Tambahan Kustom Admin</div>
                        </div>
                    </div>
                </div>

                {{-- DAFTAR KATA KUSTOM AKTIF --}}
                <div class="mb-4 p-3 bg-light rounded-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold small text-dark">
                            <i class="bi bi-tags-fill text-warning me-1"></i> Daftar Kata Kustom Tersimpan ({{ count($customBadWords) }})
                        </span>
                        <span class="text-muted small">Klik <i class="bi bi-x text-danger fw-bold"></i> untuk menghapus kata</span>
                    </div>
                    @if(count($customBadWords) > 0)
                        <div class="d-flex flex-wrap gap-1.5 align-items-center">
                            @foreach($customBadWords as $cw)
                                <span class="badge bg-white text-dark border px-2.5 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
                                    <i class="bi bi-star-fill text-warning" style="font-size: 0.7rem;"></i>
                                    <span>{{ $cw }}</span>
                                    <button type="button" class="btn-close p-0 ms-1" style="font-size: 0.65rem;" onclick="deleteCustomWord('{{ $cw }}')" title="Hapus kata '{{ $cw }}'" aria-label="Hapus kata {{ $cw }}"></button>
                                </span>
                            @endforeach
                        </div>
                    @else
                        <div class="text-muted small py-2">
                            <i class="bi bi-info-circle me-1"></i> Belum ada kata kustom yang ditambahkan oleh admin.
                        </div>
                    @endif
                </div>

                {{-- FORM INPUT KATA TAMBAHAN (MERGE TANPA MENIMPA) --}}
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-bold mb-0" for="profanityWordsInput">
                            <i class="bi bi-plus-circle me-1 text-primary"></i>Tambah Kata / Frasa Baru:
                        </label>
                        <span class="badge bg-light text-muted border">Bisa pisah koma / baris baru</span>
                    </div>
                    <div class="text-muted small mb-2">
                        <i class="bi bi-shield-check text-success me-1"></i> Kata baru yang dimasukkan akan <strong>digabungkan secara otomatis</strong> dengan kata-kata yang sudah ada tanpa menimpa kata sebelumnya.
                    </div>
                    <textarea name="profanity_words" id="profanityWordsInput" class="form-control font-mono" rows="3" maxlength="10000" placeholder="Ketik kata baru di sini... contoh: kata_baru, frasa baru"></textarea>
                </div>
            </div>
        </section>

        <div id="landingSaveBar" class="settings-save">
            <span class="settings-muted"><i class="bi bi-cloud-check me-1"></i>Perubahan diterapkan setelah disimpan.</span>
            <input type="hidden" name="active_tab" id="activeTabInput" value="#tabBrand">
            <button type="submit" id="btnSaveLanding" class="btn btn-primary-custom px-4"><i class="bi bi-check2 me-1"></i>Simpan</button>
        </div>
    </form>

    {{-- Form Tersembunyi untuk Hapus Kata Kustom --}}
    <form id="deleteCustomWordForm" action="{{ route('pelanggaran.words.delete') }}" method="POST" style="display: none;">
        @csrf
        <input type="hidden" name="word" id="deleteWordInput">
        <input type="hidden" name="active_tab" value="#tabModerasi">
    </form>

    <section class="settings-panel" id="tabPeriode" hidden>
        <div class="settings-card mb-3">
            <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                <div>
                    <div class="settings-muted mb-1">PERIODE AKTIF</div>
                    <h2 class="settings-heading">{{ $periodeAktif?->nama_periode ?? 'Belum ada periode aktif' }}</h2>
                </div>
                @if($periodeAktif)
                    <div class="text-md-end small">
                        <span class="badge bg-success">Aktif</span>
                        <span class="text-muted ms-1">{{ $periodeAktif->tanggal_mulai?->format('d M Y') }} – {{ $periodeAktif->tanggal_selesai?->format('d M Y') }}</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="settings-card">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h2 class="settings-heading" id="formPeriodeTitle"><i class="bi bi-calendar-plus me-2"></i>Periode</h2>
                <button type="button" class="btn btn-light border btn-sm" onclick="resetPeriodeForm()"><i class="bi bi-plus-lg me-1"></i>Baru</button>
            </div>
            <form action="{{ route('admin.pengaturan.periode') }}" method="POST" id="formPeriode">
                @csrf
                <input type="hidden" name="periode_id" id="periodeIdInput">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="namaPeriodeInput">Nama</label>
                        <input type="text" name="nama_periode" id="namaPeriodeInput" class="form-control" placeholder="Semester Ganjil 2026/2027" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="tahunAjaranInput">Tahun ajaran</label>
                        <input type="text" name="tahun_ajaran" id="tahunAjaranInput" class="form-control" placeholder="2026/2027" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="semesterSelect">Semester</label>
                        <select name="semester" id="semesterSelect" class="form-select" required><option value="ganjil">Ganjil</option><option value="genap">Genap</option></select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label" for="tanggalMulaiInput">Mulai</label>
                        <input type="datetime-local" step="1" name="tanggal_mulai" id="tanggalMulaiInput" class="form-control" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label" for="tanggalSelesaiInput">Selesai</label>
                        <input type="datetime-local" step="1" name="tanggal_selesai" id="tanggalSelesaiInput" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label" for="statusSelect">Status</label>
                        <select name="status" id="statusSelect" class="form-select" required><option value="aktif">Aktif</option><option value="nonaktif">Nonaktif</option></select>
                    </div>
                    <div class="col-12 d-flex justify-content-end"><button type="submit" class="btn btn-primary-custom px-4" id="btnSimpanPeriode"><i class="bi bi-check2 me-1"></i>Simpan</button></div>
                </div>
            </form>
        </div>

        <div class="settings-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h2 class="settings-heading"><i class="bi bi-clock-history me-2"></i>Riwayat</h2>
                <span class="settings-muted">{{ $semuaPeriode->count() }} periode</span>
            </div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light"><tr><th>Periode</th><th>Rentang</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($semuaPeriode as $p)
                            <tr>
                                <td><div class="fw-semibold">{{ $p->nama_periode }}</div><small class="text-muted text-capitalize">{{ $p->semester }} · {{ $p->tahun_ajaran }}</small></td>
                                <td class="small text-muted">{{ $p->tanggal_mulai?->format('d/m/Y H:i') }} – {{ $p->tanggal_selesai?->format('d/m/Y H:i') }}</td>
                                <td><span class="badge {{ $p->status === 'aktif' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($p->status) }}</span></td>
                                <td class="text-end text-nowrap">
                                    @if($p->status !== 'aktif')
                                        <form action="{{ route('admin.pengaturan.periode.aktifkan', $p) }}" method="POST" class="d-inline" data-confirm="Aktifkan periode {{ $p->nama_periode }}?" data-confirm-title="Aktifkan periode" data-confirm-btn="Aktifkan" data-confirm-type="question">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success settings-icon-button" title="Aktifkan" aria-label="Aktifkan {{ $p->nama_periode }}"><i class="bi bi-check-lg"></i></button>
                                        </form>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-light border settings-icon-button" onclick='editPeriode(@json($p))' title="Edit" aria-label="Edit {{ $p->nama_periode }}"><i class="bi bi-pencil"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada periode.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="settings-panel" id="tabAkun" hidden>
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="settings-card h-100">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h2 class="settings-heading"><i class="bi bi-shield-lock me-2"></i>Keamanan akun</h2>
                            <div class="settings-muted mt-1">{{ auth()->user()->email }}</div>
                        </div>
                        <span class="badge bg-primary">Admin</span>
                    </div>
                    <form action="{{ route('admin.pengaturan.password') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12"><label class="form-label" for="currentPassword">Password saat ini</label><div class="input-group"><input type="password" name="current_password" id="currentPassword" class="form-control" required><button type="button" class="btn btn-outline-secondary toggle-password" data-target="currentPassword" aria-label="Tampilkan password saat ini"><i class="bi bi-eye"></i></button></div></div>
                            <div class="col-md-6"><label class="form-label" for="newPassword">Password baru</label><div class="input-group"><input type="password" name="password" id="newPassword" class="form-control" minlength="6" required><button type="button" class="btn btn-outline-secondary toggle-password" data-target="newPassword" aria-label="Tampilkan password baru"><i class="bi bi-eye"></i></button></div></div>
                            <div class="col-md-6"><label class="form-label" for="newPasswordConfirmation">Konfirmasi password</label><div class="input-group"><input type="password" name="password_confirmation" id="newPasswordConfirmation" class="form-control" minlength="6" required><button type="button" class="btn btn-outline-secondary toggle-password" data-target="newPasswordConfirmation" aria-label="Tampilkan konfirmasi password"><i class="bi bi-eye"></i></button></div></div>
                            <div class="col-12 d-flex justify-content-end"><button type="submit" class="btn btn-primary-custom px-4"><i class="bi bi-check2 me-1"></i>Perbarui</button></div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="settings-card h-100 d-flex flex-column justify-content-between">
                    <div><h2 class="settings-heading"><i class="bi bi-person-circle me-2"></i>{{ auth()->user()->name }}</h2><p class="settings-muted mt-2 mb-0">Sesi administrator aktif.</p></div>
                    <form action="{{ route('logout') }}" method="POST" class="mt-4">@csrf <button type="submit" class="btn btn-outline-danger w-100"><i class="bi bi-box-arrow-right me-1"></i>Logout</button></form>
                </div>
            </div>
            <div class="col-12">
                <div class="settings-card border-danger border-opacity-25 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h2 class="settings-heading text-danger"><i class="bi bi-exclamation-triangle me-2 text-danger"></i>Hapus seluruh penilaian</h2>
                        <div class="settings-muted mt-1">{{ number_format($jumlahPenilaian, 0, ',', '.') }} penilaian akan dihapus permanen. Log pelanggaran tetap disimpan sebagai audit.</div>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#resetPenilaianModal"><i class="bi bi-shield-exclamation me-1"></i>Lanjutkan</button>
                </div>
            </div>
        </div>

        <div class="modal fade" id="resetPenilaianModal" tabindex="-1" aria-labelledby="resetPenilaianModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-danger">
                    <form action="{{ route('admin.pengaturan.reset') }}" method="POST" id="resetPenilaianForm">
                        @csrf
                        <div class="modal-header border-danger border-opacity-25">
                            <div>
                                <h2 class="modal-title fs-5 text-danger" id="resetPenilaianModalLabel"><i class="bi bi-exclamation-octagon-fill me-2"></i>Konfirmasi penghapusan</h2>
                                <div class="small text-muted mt-1">Aksi ini tidak dapat dibatalkan.</div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-danger small py-2">
                                Data nilai, kritik, saran, dan balasan dari <strong>{{ number_format($jumlahPenilaian, 0, ',', '.') }}</strong> penilaian akan dihapus. Statistik seluruh guru akan kembali ke nol.
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="resetCurrentPassword">Password administrator</label>
                                <div class="input-group"><input type="password" name="reset_current_password" id="resetCurrentPassword" class="form-control @error('reset_current_password') is-invalid @enderror" autocomplete="current-password" required><button type="button" class="btn btn-outline-secondary toggle-password" data-target="resetCurrentPassword" aria-label="Tampilkan password administrator"><i class="bi bi-eye"></i></button></div>
                                @error('reset_current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="resetConfirmation">Ketik <code>HAPUS PENILAIAN</code></label>
                                <input type="text" name="reset_confirmation" id="resetConfirmation" class="form-control @error('reset_confirmation') is-invalid @enderror" autocomplete="off" required>
                                @error('reset_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-check">
                                <input class="form-check-input @error('reset_acknowledged') is-invalid @enderror" type="checkbox" name="reset_acknowledged" value="1" id="resetAcknowledged" required>
                                <label class="form-check-label small" for="resetAcknowledged">Saya memahami bahwa data penilaian tidak dapat dipulihkan.</label>
                                @error('reset_acknowledged')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger" id="resetSubmitButton" disabled><i class="bi bi-trash3 me-1"></i>Hapus permanen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="settings-panel" id="tabPerangkat" hidden>
        @include('components.device-history')
    </section>

    <section class="settings-panel" id="tabFaq" hidden>
        <div class="settings-card mb-3">
            <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom flex-wrap gap-2">
                <div>
                    <h2 class="settings-heading mb-1"><i class="bi bi-question-circle me-2"></i>Kelola Pertanyaan Umum (FAQ)</h2>
                    <p class="settings-muted mb-0">Tambah, ubah, atau hapus pertanyaan umum yang tampil di halaman bantuan siswa, guru, dan landing page publik.</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-primary-custom px-3 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahFaq">
                        <i class="bi bi-plus-lg me-1"></i> Tambah FAQ Baru
                    </button>
                    <form action="{{ route('admin.pengaturan.faq.reset') }}" method="POST" class="d-inline" onsubmit="return confirm('Kembalikan seluruh daftar FAQ ke standar sistem bawaan? FAQ kustom yang belum disimpan terpisah akan tereset.');">
                        @csrf
                        <button type="submit" class="btn btn-light border px-3 py-2 rounded-pill shadow-sm text-secondary" title="Reset ke FAQ Bawaan">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Default
                        </button>
                    </form>
                </div>
            </div>

            {{-- Role Filter Pills --}}
            <div class="d-flex gap-2 mb-3 flex-wrap align-items-center">
                <span class="small text-muted me-1 fw-semibold">Filter Sasaran:</span>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill active faq-filter-btn" data-role="all">Semua ({{ count($faqs) }})</button>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill faq-filter-btn" data-role="publik">Publik ({{ count(array_filter($faqs, fn($f) => in_array($f['role'] ?? '', ['publik', 'semua']))) }})</button>
                <button type="button" class="btn btn-sm btn-outline-success rounded-pill faq-filter-btn" data-role="siswa">Siswa ({{ count(array_filter($faqs, fn($f) => ($f['role'] ?? '') === 'siswa')) }})</button>
                <button type="button" class="btn btn-sm btn-outline-info rounded-pill faq-filter-btn" data-role="guru">Guru ({{ count(array_filter($faqs, fn($f) => ($f['role'] ?? '') === 'guru')) }})</button>
                <button type="button" class="btn btn-sm btn-outline-warning rounded-pill faq-filter-btn" data-role="admin">Admin ({{ count(array_filter($faqs, fn($f) => ($f['role'] ?? '') === 'admin')) }})</button>
            </div>

            {{-- List of FAQ Cards --}}
            <div class="row g-3" id="faqCardList">
                @forelse($faqs as $f)
                    @php $fRole = in_array($f['role'] ?? '', ['semua', 'publik']) ? 'publik' : ($f['role'] ?? 'publik'); @endphp
                    <div class="col-12 faq-item-card" data-role="{{ $fRole }}">
                        <div class="p-3 border rounded-3 bg-white shadow-sm d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                    <span class="badge rounded-pill px-2.5 py-1 text-uppercase font-mono fw-bold
                                        @if($fRole === 'siswa') bg-success-subtle text-success border border-success-subtle
                                        @elseif($fRole === 'guru') bg-info-subtle text-info border border-info-subtle
                                        @elseif($fRole === 'admin') bg-warning-subtle text-warning border border-warning-subtle
                                        @else bg-primary-subtle text-primary border border-primary-subtle
                                        @endif" style="font-size: 0.7rem;">
                                        <i class="bi {{ $fRole === 'publik' ? 'bi-globe' : 'bi-person-badge' }} me-1"></i> {{ $fRole }}
                                    </span>
                                    <span class="text-muted small"><i class="bi {{ $f['icon'] ?? 'bi-question-circle' }} text-primary me-1"></i> Ikon: <code>{{ $f['icon'] ?? 'bi-question-circle' }}</code></span>
                                </div>
                                <h6 class="fw-bold mb-1 text-dark">{{ $f['q'] }}</h6>
                                <p class="text-muted small mb-0 lh-base">{{ $f['a'] }}</p>
                            </div>
                            <div class="d-flex align-items-center gap-2 text-nowrap flex-shrink-0">
                                <button type="button" class="btn btn-sm btn-light border settings-icon-button" onclick='openEditFaq(@json($f))' title="Edit FAQ">
                                    <i class="bi bi-pencil text-primary"></i>
                                </button>
                                <form action="{{ route('admin.pengaturan.faq.destroy', $f['id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pertanyaan ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border settings-icon-button text-danger" title="Hapus FAQ">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-4">Belum ada pertanyaan FAQ.</div>
                @endforelse
            </div>
        </div>

        {{-- Live Accordion Preview --}}
        <div class="settings-card">
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <div>
                    <h6 class="settings-heading mb-1"><i class="bi bi-eye me-2"></i>Preview Tampilan Accordion FAQ Aktif</h6>
                    <p class="settings-muted mb-0">Pratinjau tampilan accordion interaktif yang dilihat pengguna.</p>
                </div>
            </div>
            @include('components.faq-accordion')
        </div>
    </section>
</div>

{{-- FORM DELETE KATA KUSTOM HIDDEN --}}
<form id="deleteCustomWordForm" action="{{ route('admin.pelanggaran.words.delete') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="word" id="deleteWordInput">
</form>

{{-- MODAL TAMBAH FAQ --}}
<div class="modal fade" id="modalTambahFaq" tabindex="-1" aria-labelledby="modalTambahFaqLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="modalTambahFaqLabel"><i class="bi bi-plus-circle text-primary me-2"></i>Tambah Pertanyaan FAQ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.pengaturan.faq.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold" for="faqQInput">Pertanyaan</label>
                            <input type="text" name="q" id="faqQInput" class="form-control" placeholder="Contoh: Bagaimana cara menilai guru?" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" for="faqRoleSelect">Sasaran Pengguna</label>
                            <select name="role" id="faqRoleSelect" class="form-select" required>
                                <option value="publik">Publik / Umum (Landing Page)</option>
                                <option value="siswa">Khusus Siswa</option>
                                <option value="guru">Khusus Guru</option>
                                <option value="admin">Khusus Admin</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold" for="faqIconInput">Bootstrap Icon Class (Opsional)</label>
                            <input type="text" name="icon" id="faqIconInput" class="form-control" value="bi-question-circle" placeholder="bi-incognito, bi-shield-check, bi-star...">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold" for="faqAInput">Jawaban Lengkap</label>
                            <textarea name="a" id="faqAInput" class="form-control" rows="5" placeholder="Tuliskan jawaban yang jelas dan praktis..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom px-4"><i class="bi bi-check2 me-1"></i>Simpan FAQ</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT FAQ --}}
<div class="modal fade" id="modalEditFaq" tabindex="-1" aria-labelledby="modalEditFaqLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="modalEditFaqLabel"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Pertanyaan FAQ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditFaq" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold" for="editFaqQ">Pertanyaan</label>
                            <input type="text" name="q" id="editFaqQ" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold" for="editFaqRole">Sasaran Pengguna</label>
                            <select name="role" id="editFaqRole" class="form-select" required>
                                <option value="publik">Publik / Umum (Landing Page)</option>
                                <option value="siswa">Khusus Siswa</option>
                                <option value="guru">Khusus Guru</option>
                                <option value="admin">Khusus Admin</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold" for="editFaqIcon">Bootstrap Icon Class</label>
                            <input type="text" name="icon" id="editFaqIcon" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold" for="editFaqA">Jawaban Lengkap</label>
                            <textarea name="a" id="editFaqA" class="form-control" rows="5" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom px-4"><i class="bi bi-check2 me-1"></i>Perbarui FAQ</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL LIHAT & CARI KAMUS KATA TERLARANG (LIBRARY VIEWER) --}}
<div class="modal fade" id="modalLihatKata" tabindex="-1" aria-labelledby="modalLihatKataLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="modalLihatKataLabel">
                        <i class="bi bi-journal-bookmark-fill text-danger me-2"></i>Library Kata & Frasa Terlarang
                    </h5>
                    <p class="text-muted small mb-0">Daftar kata kotor, kasar, dan ejekan yang langsung diblokir otomatis oleh sistem.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                {{-- Search Box & Filter Tabs --}}
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="badWordSearchInput" class="form-control border-start-0" placeholder="Ketik kata untuk memeriksa apakah dilarang...">
                    </div>
                </div>

                <div class="d-flex gap-2 mb-3 flex-wrap align-items-center">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill active word-filter-btn" data-filter="all">
                        Semua Kata ({{ count($allBadWords) }})
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-warning rounded-pill word-filter-btn" data-filter="custom">
                        Kustom Admin ({{ count($customBadWords) }})
                    </button>
                </div>

                {{-- Badges Cloud --}}
                <div class="p-3 bg-light rounded-3 border" style="min-height: 250px; max-height: 400px; overflow-y: auto;">
                    <div class="d-flex flex-wrap gap-1.5" id="badWordCloud">
                        @foreach($defaultBadWords as $bw)
                            <span class="badge bg-white text-secondary border px-2.5 py-1.5 fw-normal bad-word-tag" data-type="default" data-word="{{ strtolower($bw) }}" style="font-size: 0.82rem;">
                                {{ $bw }}
                            </span>
                        @endforeach

                        @foreach($customBadWords as $cw)
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1.5 fw-semibold bad-word-tag" data-type="custom" data-word="{{ strtolower($cw) }}" style="font-size: 0.82rem;">
                                <i class="bi bi-star-fill text-warning me-1" style="font-size: 0.7rem;"></i>{{ $cw }}
                            </span>
                        @endforeach
                    </div>
                    <div id="badWordEmptyState" class="text-center text-muted py-4 d-none">
                        <i class="bi bi-emoji-smile fs-4 d-block mb-1"></i> Tidak ditemukan kata yang cocok dengan pencarian.
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top bg-light justify-content-between">
                <span class="small text-muted">
                    <span class="badge bg-white text-secondary border me-1">Putih: Bawaan</span>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Kuning: Tambahan Admin</span>
                </span>
                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function padZero(number) { return number.toString().padStart(2, '0'); }
function formatDatetimeLocal(date) { return `${date.getFullYear()}-${padZero(date.getMonth() + 1)}-${padZero(date.getDate())}T${padZero(date.getHours())}:${padZero(date.getMinutes())}:${padZero(date.getSeconds())}`; }

function editPeriode(periode) {
    document.getElementById('periodeIdInput').value = periode.id;
    document.getElementById('namaPeriodeInput').value = periode.nama_periode || '';
    document.getElementById('tahunAjaranInput').value = periode.tahun_ajaran || '';
    document.getElementById('semesterSelect').value = periode.semester || 'ganjil';
    document.getElementById('statusSelect').value = periode.status || 'aktif';
    ['tanggal_mulai', 'tanggal_selesai'].forEach(function(field) {
        if (!periode[field]) return;
        const date = new Date(periode[field]);
        document.getElementById(field === 'tanggal_mulai' ? 'tanggalMulaiInput' : 'tanggalSelesaiInput').value = isNaN(date) ? periode[field].substring(0, 19).replace(' ', 'T') : formatDatetimeLocal(date);
    });
    const submit = document.getElementById('btnSimpanPeriode');
    submit.innerHTML = '<i class="bi bi-check2 me-1"></i>Perbarui';
    submit.className = 'btn btn-warning px-4';
    document.getElementById('formPeriode').scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function resetPeriodeForm() {
    document.getElementById('formPeriode').reset();
    document.getElementById('periodeIdInput').value = '';
    const submit = document.getElementById('btnSimpanPeriode');
    submit.innerHTML = '<i class="bi bi-check2 me-1"></i>Simpan';
    submit.className = 'btn btn-primary-custom px-4';
}

function openEditFaq(faq) {
    const form = document.getElementById('formEditFaq');
    form.action = "{{ url('/admin/pengaturan/faq') }}/" + faq.id;
    document.getElementById('editFaqQ').value = faq.q || '';
    document.getElementById('editFaqA').value = faq.a || '';
    const r = (faq.role === 'semua' || !faq.role) ? 'publik' : faq.role;
    document.getElementById('editFaqRole').value = r;
    document.getElementById('editFaqIcon').value = faq.icon || 'bi-question-circle';
    const modal = new bootstrap.Modal(document.getElementById('modalEditFaq'));
    modal.show();
}

function deleteCustomWord(word) {
    if (confirm(`Hapus kata '${word}' dari daftar kata terlarang kustom?`)) {
        document.getElementById('deleteWordInput').value = word;
        document.getElementById('deleteCustomWordForm').submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('#pengaturanTabs [data-target]');
    const panels = document.querySelectorAll('.settings-panel');
    const landingSaveBar = document.getElementById('landingSaveBar');
    const landingTargets = ['#tabBrand', '#tabHero', '#tabKonten', '#tabVisiMisi', '#tabFooter', '#tabLegal', '#tabModerasi'];
    const activeTabInput = document.getElementById('activeTabInput');

    function showTab(target, updateHash = true) {
        if (!document.querySelector(target)) return;
        panels.forEach(panel => panel.hidden = '#' + panel.id !== target);
        tabButtons.forEach(button => button.classList.toggle('active', button.dataset.target === target));
        landingSaveBar.hidden = !landingTargets.includes(target);
        if (activeTabInput) activeTabInput.value = target;
        try {
            localStorage.setItem('admin_settings_active_tab', target);
        } catch (e) {}
        if (updateHash) history.replaceState(null, '', target);
    }
    tabButtons.forEach(button => button.addEventListener('click', () => showTab(button.dataset.target)));

    // Prioritaskan tab dari hash URL, atau jika tidak ada ambil dari localStorage
    let initialTab = window.location.hash;
    if (!initialTab || !document.querySelector(initialTab)) {
        try {
            const saved = localStorage.getItem('admin_settings_active_tab');
            if (saved && document.querySelector(saved)) {
                initialTab = saved;
            }
        } catch (e) {}
    }
    if (initialTab && document.querySelector(initialTab)) {
        showTab(initialTab, false);
    }

    // FAQ Role Filter
    const faqFilterBtns = document.querySelectorAll('.faq-filter-btn');
    const faqCards = document.querySelectorAll('.faq-item-card');
    faqFilterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            faqFilterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const targetRole = this.dataset.role;
            faqCards.forEach(card => {
                if (targetRole === 'all') {
                    card.style.display = '';
                } else if (targetRole === 'publik') {
                    card.style.display = (card.dataset.role === 'publik' || card.dataset.role === 'semua') ? '' : 'none';
                } else {
                    card.style.display = card.dataset.role === targetRole ? '' : 'none';
                }
            });
        });
    });

    // Bad Words Library Live Search & Filter
    const badWordInput = document.getElementById('badWordSearchInput');
    const wordFilterBtns = document.querySelectorAll('.word-filter-btn');
    const badWordTags = document.querySelectorAll('.bad-word-tag');
    const emptyState = document.getElementById('badWordEmptyState');

    let currentWordFilter = 'all';

    function filterBadWords() {
        const query = (badWordInput.value || '').toLowerCase().trim();
        let visibleCount = 0;

        badWordTags.forEach(tag => {
            const word = tag.dataset.word;
            const type = tag.dataset.type;
            const matchesQuery = query === '' || word.includes(query);
            const matchesType = currentWordFilter === 'all' || type === currentWordFilter;

            if (matchesQuery && matchesType) {
                tag.style.display = '';
                visibleCount++;
            } else {
                tag.style.display = 'none';
            }
        });

        if (emptyState) {
            emptyState.classList.toggle('d-none', visibleCount > 0);
        }
    }

    if (badWordInput) {
        badWordInput.addEventListener('input', filterBadWords);
    }

    wordFilterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            wordFilterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentWordFilter = this.dataset.filter;
            filterBadWords();
        });
    });

    // Brand live preview
    const part1 = document.getElementById('part1Input');
    const part2 = document.getElementById('part2Input');
    const color1 = document.getElementById('color1Input');
    const color2 = document.getElementById('color2Input');
    function updateBrandPreview() {
        if (!part1 || !part2 || !color1 || !color2) return;
        document.getElementById('previewPart1').textContent = part1.value || 'Guru';
        document.getElementById('previewPart2').textContent = part2.value || 'Kuu';
        document.getElementById('previewPart1').style.color = color1.value;
        document.getElementById('previewPart2').style.color = color2.value;
    }
    [part1, part2, color1, color2].forEach(input => input?.addEventListener('input', updateBrandPreview));

    document.getElementById('siteLogoFileInput')?.addEventListener('change', function() {
        if (!this.files[0]) return;
        const reader = new FileReader();
        reader.onload = event => {
            const image = document.getElementById('brandLogoPreview');
            image.src = event.target.result;
            image.classList.remove('d-none');
            document.getElementById('brandLogoIcon')?.classList.add('d-none');
        };
        reader.readAsDataURL(this.files[0]);
    });

    const heroPreview = document.getElementById('heroPreviewBox');
    const heroTitle = document.getElementById('heroTitleInput');
    const heroSubtitle = document.getElementById('heroSubtitleInput');
    const heroUrl = document.getElementById('heroImageUrlInput');
    const heroBadge = document.getElementById('heroBadgeInput');
    if (heroBadge) heroBadge.addEventListener('input', () => { document.getElementById('previewBadge').textContent = heroBadge.value; });
    if (heroTitle) heroTitle.addEventListener('input', () => document.getElementById('previewTitle').textContent = heroTitle.value);
    if (heroSubtitle) heroSubtitle.addEventListener('input', () => document.getElementById('previewSubtitle').textContent = heroSubtitle.value);
    if (heroUrl) heroUrl.addEventListener('input', () => { if (heroUrl.value) heroPreview.style.backgroundImage = `linear-gradient(rgba(10,25,47,.82),rgba(0,51,102,.75)),url('${heroUrl.value}')`; });
    document.getElementById('heroImageFileInput')?.addEventListener('change', function() {
        if (!this.files[0]) return;
        const reader = new FileReader();
        reader.onload = event => heroPreview.style.backgroundImage = `linear-gradient(rgba(10,25,47,.82),rgba(0,51,102,.75)),url('${event.target.result}')`;
        reader.readAsDataURL(this.files[0]);
    });

    const resetPhrase = document.getElementById('resetConfirmation');
    const resetAcknowledged = document.getElementById('resetAcknowledged');
    const resetSubmitButton = document.getElementById('resetSubmitButton');
    function updateResetButton() {
        if (!resetPhrase || !resetAcknowledged || !resetSubmitButton) return;
        resetSubmitButton.disabled = resetPhrase.value !== 'HAPUS PENILAIAN' || !resetAcknowledged.checked;
    }
    resetPhrase?.addEventListener('input', updateResetButton);
    resetAcknowledged?.addEventListener('change', updateResetButton);
    document.querySelectorAll('.toggle-password').forEach(button => button.addEventListener('click', function() {
        const input = document.getElementById(this.dataset.target);
        const icon = this.querySelector('i');
        const visible = input.type === 'password';
        input.type = visible ? 'text' : 'password';
        icon.classList.toggle('bi-eye', !visible);
        icon.classList.toggle('bi-eye-slash', visible);
        this.setAttribute('aria-label', visible ? 'Sembunyikan password' : 'Tampilkan password');
    }));

    const landingForm = document.getElementById('landingForm');
    const btnSaveLanding = document.getElementById('btnSaveLanding');
    if (landingForm && btnSaveLanding) {
        landingForm.addEventListener('submit', function() {
            btnSaveLanding.disabled = true;
            btnSaveLanding.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...';
        });
    }

    // Auto adjust view if element is near the bottom of viewport (e.g. color pickers, bottom inputs)
    document.querySelectorAll('input, select, textarea, .form-control, .form-control-color').forEach(el => {
        const ensureVisibleAboveBottom = () => {
            const rect = el.getBoundingClientRect();
            if (window.innerHeight - rect.bottom < 270) {
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        };
        el.addEventListener('focus', ensureVisibleAboveBottom);
        if (el.type === 'color' || el.classList.contains('form-control-color')) {
            el.addEventListener('click', ensureVisibleAboveBottom);
        }
    });

    // Live Client-side Image Preview for Hero Slides
    function bindInstantPreview(fileInputId, previewWrapperId, badgeId) {
        const fileInput = document.getElementById(fileInputId);
        const wrapper = document.getElementById(previewWrapperId);
        if (!fileInput || !wrapper) return;

        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    wrapper.innerHTML = `<img src="${e.target.result}" class="rounded border shadow-sm w-100" style="height: 110px; object-fit: cover;">`;
                    if (badgeId) {
                        const badge = document.getElementById(badgeId);
                        if (badge) {
                            badge.className = 'badge bg-success text-white';
                            badge.innerText = 'File Terpilih (Siap Simpan)';
                        }
                    }
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    bindInstantPreview('heroImageFileInput', 'previewHero1Wrapper', null);
    bindInstantPreview('heroImageFile2Input', 'previewHero2Wrapper', 'badgeHero2');
    bindInstantPreview('heroImageFile3Input', 'previewHero3Wrapper', 'badgeHero3');

    // Live URL preview on typing
    function bindUrlPreview(urlInputId, previewWrapperId) {
        const input = document.getElementById(urlInputId);
        const wrapper = document.getElementById(previewWrapperId);
        if (!input || !wrapper) return;
        input.addEventListener('input', function() {
            const val = this.value.trim();
            if (val.startsWith('http://') || val.startsWith('https://') || val.startsWith('/')) {
                wrapper.innerHTML = `<img src="${val}" class="rounded border shadow-sm w-100" style="height: 110px; object-fit: cover;" onerror="this.onerror=null;">`;
            }
        });
    }
    bindUrlPreview('heroImageUrlInput', 'previewHero1Wrapper');
    bindUrlPreview('heroImageUrl2Input', 'previewHero2Wrapper');
    bindUrlPreview('heroImageUrl3Input', 'previewHero3Wrapper');

    @if($errors->has('reset_current_password') || $errors->has('reset_confirmation') || $errors->has('reset_acknowledged'))
        showTab('#tabAkun', false);
        const resetModalElement = document.getElementById('resetPenilaianModal');
        if (resetModalElement) bootstrap.Modal.getOrCreateInstance(resetModalElement).show();
    @elseif($errors->any() && old('active_tab'))
        showTab('{{ old("active_tab") }}', false);
    @endif
});
</script>
@endsection
