@extends('layouts.admin')

@section('title', 'Pengaturan')

@section('content')
<style>
    .settings-page { max-width: 1180px; }
    .settings-nav { display: grid; grid-template-columns: repeat(auto-fill, minmax(105px, 1fr)); gap: .5rem; margin-bottom: 1.25rem; }
    .settings-nav__item { display: flex; align-items: center; justify-content: center; gap: .45rem; min-height: 44px; padding: .6rem .75rem; color: var(--text-muted); background: var(--bg-card); border: 1px solid var(--border); border-radius: .65rem; font-size: .84rem; font-weight: 600; transition: .18s ease; }
    .settings-nav__item:hover { color: var(--primary); border-color: var(--primary); }
    .settings-nav__item.active { color: #fff; background: var(--primary); border-color: var(--primary); }
    .settings-card { padding: 1.25rem; background: var(--bg-card); border: 1px solid var(--border); border-radius: .85rem; box-shadow: var(--card-shadow); }
    .settings-card + .settings-card { margin-top: 1rem; }
    .settings-heading { font-size: 1rem; font-weight: 700; margin: 0; color: var(--text-dark); }
    .settings-heading i { color: var(--primary); }
    .settings-preview { min-height: 112px; display: flex; align-items: center; padding: 1.25rem; border: 1px solid var(--border); border-radius: .7rem; background: var(--bg-light); }
    .settings-hero { min-height: 190px; display: flex; align-items: end; padding: 1.25rem; color: #fff; background-position: center; background-size: cover; border-radius: .7rem; overflow: hidden; }
    .settings-save { position: sticky; bottom: 1rem; z-index: 10; display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: .75rem 1rem; margin-top: 1rem; background: var(--bg-card); border: 1px solid var(--border); border-radius: .75rem; box-shadow: var(--card-shadow); }
    .settings-panel[hidden] { display: none !important; }
    .settings-panel .form-label { margin-bottom: .35rem; font-size: .82rem; font-weight: 600; }
    .settings-muted { color: var(--text-muted); font-size: .84rem; }
    .settings-summary { cursor: pointer; color: var(--primary); font-size: .84rem; font-weight: 600; }
    .settings-icon-button { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
    @media (max-width: 991.98px) { .settings-nav { display: flex; overflow-x: auto; padding-bottom: .25rem; } .settings-nav__item { flex: 0 0 auto; min-width: 110px; } }
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
        <button class="settings-nav__item" type="button" data-target="#tabHero"><i class="bi bi-image"></i> Beranda</button>
        <button class="settings-nav__item" type="button" data-target="#tabVisiMisi"><i class="bi bi-bullseye"></i> Profil</button>
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
            <div class="settings-card">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h2 class="settings-heading"><i class="bi bi-image me-2"></i>Banner beranda</h2>
                    <span class="settings-muted">Preview langsung</span>
                </div>
                <div id="heroPreviewBox" class="settings-hero mb-4" style="background-image:linear-gradient(rgba(0,51,102,.82),rgba(0,51,102,.66)),url('{{ $settings['hero_image'] }}');">
                    <div class="mw-100" style="max-width:680px;">
                        <h3 class="h4 fw-bold mb-2" id="previewTitle">{{ $settings['hero_title'] }}</h3>
                        <p class="mb-0 opacity-75 small" id="previewSubtitle">{{ $settings['hero_subtitle'] }}</p>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-lg-6">
                        <label class="form-label" for="heroTitleInput">Judul</label>
                        <input type="text" name="hero_title" id="heroTitleInput" class="form-control" value="{{ $settings['hero_title'] }}">
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label" for="heroCtaInput">Teks tombol</label>
                        <input type="text" name="hero_cta_text" id="heroCtaInput" class="form-control" value="{{ $settings['hero_cta_text'] }}">
                    </div>
                    <div class="col-lg-8">
                        <label class="form-label" for="heroSubtitleInput">Deskripsi</label>
                        <textarea name="hero_subtitle" id="heroSubtitleInput" rows="3" class="form-control">{{ $settings['hero_subtitle'] }}</textarea>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label" for="heroCtaUrlInput">Link tombol</label>
                        <input type="text" name="hero_cta_url" id="heroCtaUrlInput" class="form-control" value="{{ $settings['hero_cta_url'] }}">
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label" for="heroImageFileInput">Gambar baru</label>
                        <input type="file" name="hero_image_file" id="heroImageFileInput" class="form-control" accept="image/png,image/jpeg,image/jpg,image/webp">
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label" for="heroImageUrlInput">atau URL gambar</label>
                        <input type="url" name="hero_image_url" id="heroImageUrlInput" class="form-control" value="{{ $settings['hero_image'] }}" placeholder="https://...">
                    </div>
                </div>
            </div>
        </section>

        <section class="settings-panel" id="tabVisiMisi" hidden>
            <div class="settings-card">
                <h2 class="settings-heading mb-4"><i class="bi bi-bullseye me-2"></i>Profil beranda</h2>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="visiTextInput">Visi</label>
                        <textarea name="visi_text" id="visiTextInput" class="form-control" rows="8" placeholder="Tulis visi...">{{ $settings['visi_text'] }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="misiTextInput">Misi</label>
                        <textarea name="misi_text" id="misiTextInput" class="form-control" rows="8" placeholder="Satu poin per baris...">{{ $settings['misi_text'] }}</textarea>
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
            <div class="settings-card">
                <h2 class="settings-heading"><i class="bi bi-shield-exclamation me-2"></i>Kata Filter Moderasi</h2>
                <p class="settings-muted mt-1">Tambahkan kata atau frasa yang harus diblokir saat siswa mengirim penilaian. Satu kata/frasa per baris. Daftar bawaan tetap aktif.</p>
                <label class="form-label" for="profanityWordsInput">Kata/frasa tambahan</label>
                <textarea name="profanity_words" id="profanityWordsInput" class="form-control" rows="10" maxlength="10000" placeholder="contoh kata\ncontoh frasa">{{ $settings['profanity_words'] }}</textarea>
            </div>
        </section>

        <div id="landingSaveBar" class="settings-save">
            <span class="settings-muted"><i class="bi bi-cloud-check me-1"></i>Perubahan diterapkan setelah disimpan.</span>
            <input type="hidden" name="active_tab" id="activeTabInput" value="#tabBrand">
            <button type="submit" id="btnSaveLanding" class="btn btn-primary-custom px-4"><i class="bi bi-check2 me-1"></i>Simpan</button>
        </div>
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
        @include('components.faq-accordion')
    </section>
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

document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('#pengaturanTabs [data-target]');
    const panels = document.querySelectorAll('.settings-panel');
    const landingSaveBar = document.getElementById('landingSaveBar');
    const landingTargets = ['#tabBrand', '#tabHero', '#tabVisiMisi', '#tabFooter', '#tabLegal', '#tabModerasi'];
    const activeTabInput = document.getElementById('activeTabInput');
    function showTab(target, updateHash = true) {
        if (!document.querySelector(target)) return;
        panels.forEach(panel => panel.hidden = '#' + panel.id !== target);
        tabButtons.forEach(button => button.classList.toggle('active', button.dataset.target === target));
        landingSaveBar.hidden = !landingTargets.includes(target);
        if (activeTabInput && landingTargets.includes(target)) activeTabInput.value = target;
        if (updateHash) history.replaceState(null, '', target);
    }
    tabButtons.forEach(button => button.addEventListener('click', () => showTab(button.dataset.target)));
    if (window.location.hash && document.querySelector(window.location.hash)) showTab(window.location.hash, false);

    const part1 = document.getElementById('part1Input');
    const part2 = document.getElementById('part2Input');
    const color1 = document.getElementById('color1Input');
    const color2 = document.getElementById('color2Input');
    function updateBrandPreview() {
        document.getElementById('previewPart1').textContent = part1.value || 'Guru';
        document.getElementById('previewPart2').textContent = part2.value || 'Kuu';
        document.getElementById('previewPart1').style.color = color1.value;
        document.getElementById('previewPart2').style.color = color2.value;
    }
    [part1, part2, color1, color2].forEach(input => input.addEventListener('input', updateBrandPreview));

    document.getElementById('siteLogoFileInput').addEventListener('change', function() {
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
    heroTitle.addEventListener('input', () => document.getElementById('previewTitle').textContent = heroTitle.value);
    heroSubtitle.addEventListener('input', () => document.getElementById('previewSubtitle').textContent = heroSubtitle.value);
    heroUrl.addEventListener('input', () => { if (heroUrl.value) heroPreview.style.backgroundImage = `linear-gradient(rgba(0,51,102,.82),rgba(0,51,102,.66)),url('${heroUrl.value}')`; });
    document.getElementById('heroImageFileInput').addEventListener('change', function() {
        if (!this.files[0]) return;
        const reader = new FileReader();
        reader.onload = event => heroPreview.style.backgroundImage = `linear-gradient(rgba(0,51,102,.82),rgba(0,51,102,.66)),url('${event.target.result}')`;
        reader.readAsDataURL(this.files[0]);
    });

    const resetModalElement = document.getElementById('resetPenilaianModal');
    const resetPhrase = document.getElementById('resetConfirmation');
    const resetAcknowledged = document.getElementById('resetAcknowledged');
    const resetSubmitButton = document.getElementById('resetSubmitButton');
    function updateResetButton() {
        resetSubmitButton.disabled = resetPhrase.value !== 'HAPUS PENILAIAN' || !resetAcknowledged.checked;
    }
    resetPhrase.addEventListener('input', updateResetButton);
    resetAcknowledged.addEventListener('change', updateResetButton);
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

    @if($errors->has('reset_current_password') || $errors->has('reset_confirmation') || $errors->has('reset_acknowledged'))
        showTab('#tabAkun', false);
        bootstrap.Modal.getOrCreateInstance(resetModalElement).show();
    @elseif($errors->any() && old('active_tab'))
        showTab('{{ old("active_tab") }}', false);
    @endif
});
</script>
@endsection
