@extends('layouts.admin')

@section('title', 'Log & Peringkat Pelanggaran')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="page-label">AUDIT & KEAMANAN SISTEM</div>
            <h1 class="page-title">
                Log & Peringkat Pelanggaran
            </h1>
            <p class="page-subtitle mb-0">Pantau catatan insiden kata terlarang, peringkat pelanggar siswa & guru, serta kelola tindakan sanksi akun.</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if(($stats['unread'] ?? 0) > 0)
                <form action="{{ route('admin.pelanggaran.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-custom btn-sm">
                        <i class="bi bi-check2-all text-primary me-1"></i> Tandai Semua Dibaca
                    </button>
                </form>
            @endif

            @if(($stats['total'] ?? 0) > 0)
                <div class="dropdown d-inline-block">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle d-inline-flex align-items-center gap-1.5" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-trash3 text-danger"></i>
                        <span>Bersihkan Log</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-2" style="border-radius: 12px; font-size: 0.85rem; min-width: 245px; box-shadow: 0 10px 30px rgba(0,0,0,0.12) !important;">
                        <li>
                            <form action="{{ route('admin.pelanggaran.clean-logs') }}" method="POST" data-confirm="Hapus semua catatan log insiden yang statusnya SUDAH DIBACA? Catatan yang belum dibaca akan tetap tersimpan." data-confirm-title="Bersihkan Log Terbaca?" data-confirm-btn="Ya, Bersihkan" data-confirm-type="warning">
                                @csrf
                                <input type="hidden" name="mode" value="read">
                                <button type="submit" class="dropdown-item py-2 px-3 d-flex align-items-center gap-2">
                                    <i class="bi bi-check2-circle text-success fs-6"></i>
                                    <span>Hapus Log Terbaca</span>
                                </button>
                            </form>
                        </li>
                        <li>
                            <form action="{{ route('admin.pelanggaran.clean-logs') }}" method="POST" data-confirm="Hapus seluruh log insiden yang sudah berumur lebih dari 30 hari?" data-confirm-title="Bersihkan Log Lama?" data-confirm-btn="Ya, Bersihkan" data-confirm-type="warning">
                                @csrf
                                <input type="hidden" name="mode" value="old">
                                <button type="submit" class="dropdown-item py-2 px-3 d-flex align-items-center gap-2">
                                    <i class="bi bi-clock-history text-warning fs-6"></i>
                                    <span>Hapus Log Lama (> 30 Hari)</span>
                                </button>
                            </form>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form action="{{ route('admin.pelanggaran.clean-logs') }}" method="POST" data-confirm="PERINGATAN: Kosongkan SEMUA riwayat catatan log pelanggaran sekarang? Status sanksi akun pengguna tidak akan diubah." data-confirm-title="Kosongkan Semua Log Pelanggaran?" data-confirm-btn="Ya, Kosongkan Semua" data-confirm-type="danger">
                                @csrf
                                <input type="hidden" name="mode" value="all">
                                <button type="submit" class="dropdown-item py-2 px-3 d-flex align-items-center gap-2 text-danger">
                                    <i class="bi bi-trash3-fill text-danger fs-6"></i>
                                    <span>Kosongkan Semua Catatan Log</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>

                <button type="button" class="btn btn-outline-danger btn-sm d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#modalResetAllPelanggaran">
                    <i class="bi bi-arrow-counterclockwise"></i>
                    <span>Reset Log & Pulihkan Akun</span>
                </button>
            @endif
        </div>
    </div>

    {{-- STATS CARDS DENGAN GARIS TEPI KHAS TEMA GURUKUU --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl">
            <div class="card-custom p-3 h-100 shadow-xs position-relative" style="border: 1px solid #e2e8f0; border-top: 3.5px solid #dc3545 !important; background: #ffffff;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small fw-bold text-muted font-mono" style="font-size: 0.72rem; letter-spacing: 0.5px;">VERIFIKASI TINDAKAN</span>
                    @if($stats['unread'] > 0)
                        <span class="badge bg-danger text-white rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">Perlu Ditinjau</span>
                    @else
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">Semua Bersih</span>
                    @endif
                </div>
                <div class="fs-3 fw-bold mb-1 d-flex align-items-center gap-2">
                    <span class="{{ $stats['unread'] > 0 ? 'text-danger' : 'text-dark' }}">{{ $stats['unread'] }}</span>
                    @if($stats['unread'] > 0)
                        <span class="spinner-grow spinner-grow-sm text-danger" role="status"></span>
                    @endif
                </div>
                <small class="text-muted d-block" style="font-size: 0.75rem;">Insiden baru yang belum diproses admin</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl">
            <div class="card-custom p-3 h-100 shadow-xs position-relative" style="border: 1px solid #e2e8f0; border-top: 3.5px solid #003366 !important; background: #ffffff;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small fw-bold text-muted font-mono" style="font-size: 0.72rem; letter-spacing: 0.5px;">AKUMULASI INSIDEN</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-primary" style="width: 26px; height: 26px; background: rgba(0, 51, 102, 0.08);">
                        <i class="bi bi-shield-lock-fill" style="font-size: 0.85rem;"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-dark mb-1">{{ $stats['total'] }}</div>
                <small class="text-muted d-block" style="font-size: 0.75rem;">Total pelanggaran etika yang berhasil dicegah</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl">
            <div class="card-custom p-3 h-100 shadow-xs position-relative" style="border: 1px solid #e2e8f0; border-top: 3.5px solid #ff6600 !important; background: #ffffff;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small fw-bold text-muted font-mono" style="font-size: 0.72rem; letter-spacing: 0.5px;">MODERASI ULASAN</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 26px; height: 26px; background: rgba(255, 102, 0, 0.1); color: #ff6600;">
                        <i class="bi bi-chat-quote-fill" style="font-size: 0.85rem;"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-dark mb-1">{{ $stats['penilaian'] }}</div>
                <small class="text-muted d-block" style="font-size: 0.75rem;">Komentar terblokir kata toxic</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl">
            <div class="card-custom p-3 h-100 shadow-xs position-relative" style="border: 1px solid #e2e8f0; border-top: 3.5px solid #004d99 !important; background: #ffffff;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small fw-bold text-muted font-mono" style="font-size: 0.72rem; letter-spacing: 0.5px;">PESAN BANTUAN</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-primary" style="width: 26px; height: 26px; background: rgba(0, 77, 153, 0.1);">
                        <i class="bi bi-envelope-exclamation-fill" style="font-size: 0.85rem;"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-dark mb-1">{{ $stats['kontak'] }}</div>
                <small class="text-muted d-block" style="font-size: 0.75rem;">Pengaduan terdeteksi tidak pantas</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl">
            <div class="card-custom p-3 h-100 shadow-xs position-relative" style="border: 1px solid #e2e8f0; border-top: 3.5px solid #ff6600 !important; background: #ffffff;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small fw-bold text-muted font-mono" style="font-size: 0.72rem; letter-spacing: 0.5px;">LAPORAN DARI USER</span>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 26px; height: 26px; background: rgba(255, 102, 0, 0.1); color: #ff6600;">
                        <i class="bi bi-flag-fill" style="font-size: 0.85rem;"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-dark mb-1">{{ $stats['reports'] ?? 0 }}</div>
                <small class="text-muted d-block" style="font-size: 0.75rem;">Dari {{ $stats['reported_items'] ?? 0 }} ulasan dilaporkan</small>
            </div>
        </div>
    </div>

    {{-- NAV TABS PERINGKAT & LOG --}}
    <ul class="nav nav-pills mb-4 gap-2 border-bottom pb-3" id="pelanggaranTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" id="tab-log-btn" data-bs-toggle="pill" data-bs-target="#tab-log-pane" type="button" role="tab" aria-selected="true">
                <i class="bi bi-card-list"></i>
                <span>Semua Log Pelanggaran</span>
                <span class="badge bg-secondary-subtle text-dark rounded-pill ms-1">{{ $pelanggarans->total() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" id="tab-siswa-btn" data-bs-toggle="pill" data-bs-target="#tab-siswa-pane" type="button" role="tab" aria-selected="false">
                <i class="bi bi-person-x text-danger"></i>
                <span>Peringkat Siswa Melanggar</span>
                <span class="badge bg-danger-subtle text-danger rounded-pill ms-1">{{ count($topSiswa) }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" id="tab-guru-btn" data-bs-toggle="pill" data-bs-target="#tab-guru-pane" type="button" role="tab" aria-selected="false">
                <i class="bi bi-person-workspace text-primary"></i>
                <span>Peringkat Guru Melanggar</span>
                <span class="badge bg-primary-subtle text-primary rounded-pill ms-1">{{ count($topGuru) }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-3 py-1.5 fw-semibold d-inline-flex align-items-center gap-1.5" id="tab-report-btn" data-bs-toggle="pill" data-bs-target="#tab-report-pane" type="button" role="tab" aria-selected="false">
                <i class="bi bi-flag-fill text-warning"></i>
                <span>Laporan dari User</span>
                <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill ms-1">{{ $reportedReviews->total() }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="pelanggaranTabContent">
        {{-- ==================== TAB 1: LOG PELANGGARAN ==================== --}}
        <div class="tab-pane fade show active" id="tab-log-pane" role="tabpanel" tabindex="0">
            {{-- FILTER BAR --}}
            <div class="card-custom p-3 mb-4" style="border: 1px solid #e2e8f0;">
                <form method="GET" action="{{ route('admin.pelanggaran.index') }}" class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Semua Status Notifikasi</option>
                            <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>🔴 Belum Dibaca (Baru)</option>
                            <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>⚪ Sudah Ditinjau</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="tipe" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Semua Tipe Pelanggaran</option>
                            <option value="penilaian_toxic" {{ request('tipe') === 'penilaian_toxic' ? 'selected' : '' }}>Ulasan Guru Kasar / Toxic</option>
                            <option value="kontak_toxic" {{ request('tipe') === 'kontak_toxic' ? 'selected' : '' }}>Pesan Kontak Terlarang</option>
                            <option value="komentar_disensor" {{ request('tipe') === 'komentar_disensor' ? 'selected' : '' }}>Ulasan Disensor Manual</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari nama siswa, guru, NIS, atau kata..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex gap-1">
                        <button type="submit" class="btn btn-primary-custom btn-sm flex-fill"><i class="bi bi-search me-1"></i>Cari</button>
                        @if(request()->hasAny(['status', 'tipe', 'search']))
                            <a href="{{ route('admin.pelanggaran.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset filter">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- LIST TABLE --}}
            <div class="card-custom overflow-hidden" style="border: 1px solid #e2e8f0;">
                <div class="table-responsive">
                    <table class="table table-custom table-hover align-middle mb-0">
                        <thead>
                            <tr class="text-muted font-mono" style="font-size: 0.74rem; letter-spacing: 0.5px; background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                <th class="text-center py-3" style="width: 50px;">STATUS</th>
                                <th class="py-3" style="width: 140px;">WAKTU</th>
                                <th class="py-3" style="min-width: 200px;">IDENTITAS PELANGGAR</th>
                                <th class="py-3" style="min-width: 160px;">TIPE & SASARAN</th>
                                <th class="py-3" style="min-width: 130px;">KATA TERDETEKSI</th>
                                <th class="text-center py-3" style="width: 130px;">ISI ULASAN</th>
                                <th class="py-3" style="min-width: 140px;">TINDAKAN</th>
                                <th class="text-center py-3" style="width: 85px;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pelanggarans as $p)
                                <tr class="{{ !$p->is_read ? 'bg-danger-subtle bg-opacity-10' : '' }}">
                                    <td class="text-center">
                                        @if(!$p->is_read)
                                            <span class="badge bg-danger p-1 rounded-circle" title="Perlu Tindakan (Baru)" style="width: 10px; height: 10px; display: inline-block;"></span>
                                        @else
                                            <i class="bi bi-check2 text-muted" title="Sudah Ditinjau"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold small text-dark">{{ $p->created_at->format('d M Y') }}</div>
                                        <div class="text-muted font-mono" style="font-size: 0.72rem;">{{ $p->created_at->format('H:i') }} WIB <br>({{ $p->created_at->diffForHumans() }})</div>
                                    </td>
                                    <td>
                                        @if($p->user)
                                            <div class="d-flex align-items-center gap-1.5 mb-1">
                                                @if($p->user->role === 'guru')
                                                    <span class="badge bg-primary text-white px-1.5 py-0.5 font-mono" style="font-size: 0.68rem;">GURU</span>
                                                @else
                                                    <span class="badge bg-warning text-dark px-1.5 py-0.5 font-mono" style="font-size: 0.68rem;">SISWA</span>
                                                @endif
                                                <span class="fw-bold text-dark" style="font-size: 0.92rem;">{{ $p->user->name }}</span>
                                            </div>

                                            <div class="small text-muted font-mono mb-1" style="font-size: 0.75rem;">
                                                @if($p->user->role === 'guru')
                                                    NIP: <strong>{{ $p->user->nis ?? '-' }}</strong> &bull; {{ $p->user->jurusan?->nama_jurusan ?? 'Guru Pengajar' }}
                                                @else
                                                    NIS: <strong>{{ $p->user->nis ?? '-' }}</strong> &bull; {{ $p->user->nama_kelas }}
                                                @endif
                                            </div>

                                            <div class="d-flex flex-wrap gap-1 align-items-center">
                                                @if($p->user->is_active)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5 font-mono" style="font-size: 0.68rem;">
                                                        <i class="bi bi-check-circle-fill me-0.5"></i> Aktif
                                                    </span>
                                                @elseif($p->user->deactivation_type === 'berkala')
                                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-0.5 font-mono" style="font-size: 0.68rem;">
                                                        <i class="bi bi-clock-history me-0.5"></i> Nonaktif s/d {{ $p->user->deactivated_until?->format('d M Y') }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5 font-mono" style="font-size: 0.68rem;">
                                                        <i class="bi bi-slash-circle me-0.5"></i> Dinonaktifkan
                                                    </span>
                                                @endif

                                                @if($p->user->warning_count > 0)
                                                    <span class="badge bg-warning text-dark px-1.5 py-0.5 font-mono" style="font-size: 0.68rem;">
                                                        ⚠️ {{ $p->user->warning_count }}x Peringatan
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="badge bg-secondary">Tamu Publik (Anonim)</span>
                                            <div class="text-muted font-mono" style="font-size: 0.75rem;">IP: {{ $p->ip_address ?? '-' }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $p->tipe_badge_class }} mb-1" style="font-size: 0.75rem;">
                                            {{ $p->tipe_label }}
                                        </span>
                                        @if($p->guru)
                                            <div class="small text-muted">
                                                Sasaran: <span class="fw-semibold text-dark">{{ $p->guru->nama }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if(is_array($p->kata_terdeteksi) && count($p->kata_terdeteksi) > 0)
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($p->kata_terdeteksi as $kata)
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-semibold px-2">
                                                        {{ $kata }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button type="button" 
                                            class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1.5 shadow-xs" 
                                            onclick="openModalDetailUlasan(this)"
                                            data-nama="{{ e($p->user?->name ?? 'Anonim') }}"
                                            data-role="{{ e(ucfirst($p->user?->role ?? 'Tamu')) }}"
                                            data-nis="{{ e($p->user?->nis ?? '-') }}"
                                            data-kelas="{{ e($p->user?->role === 'guru' ? ($p->user?->jurusan?->nama_jurusan ?? 'Guru') : ($p->user?->nama_kelas ?? '-')) }}"
                                            data-sasaran="{{ e($p->guru?->nama ?? '-') }}"
                                            data-tipe="{{ e($p->tipe_label) }}"
                                            data-waktu="{{ e($p->created_at->translatedFormat('d F Y, H:i')) }} WIB ({{ e($p->created_at->diffForHumans()) }})"
                                            data-kata='@json($p->kata_terdeteksi ?? [])'
                                            data-isi="{{ e($p->isi_teks) }}"
                                            data-tindakan="{{ e($p->tindakan) }}"
                                            title="Klik untuk melihat teks ulasan lengkap">
                                            <i class="bi bi-chat-quote-fill"></i>
                                            <span class="fw-semibold" style="font-size: 0.78rem;">Lihat Ulasan</span>
                                        </button>
                                    </td>
                                    <td>
                                        @if($p->tindakan === 'dinonaktifkan_permanen')
                                            <span class="badge bg-danger text-white border"><i class="bi bi-slash-circle me-1"></i>Nonaktif Permanen</span>
                                        @elseif($p->tindakan === 'dinonaktifkan_berkala')
                                            <span class="badge bg-warning text-dark border border-warning"><i class="bi bi-clock-history me-1"></i>Nonaktif Berkala</span>
                                        @elseif($p->tindakan === 'diaktifkan_kembali')
                                            <span class="badge bg-success text-white border"><i class="bi bi-check-circle me-1"></i>Diaktifkan Kembali</span>
                                        @elseif($p->tindakan === 'diberi_peringatan')
                                            <span class="badge bg-warning-subtle text-warning border border-warning">Diberi Peringatan</span>
                                        @elseif($p->tindakan === 'diblokir_otomatis')
                                            <span class="badge bg-secondary-subtle text-secondary border">Diblokir Sistem</span>
                                        @else
                                            <span class="badge bg-light text-dark border">{{ $p->tindakan }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            @if(!$p->is_read)
                                                <form action="{{ route('admin.pelanggaran.read', $p) }}" method="POST" class="d-inline" title="Tandai Ditinjau">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-light border text-success rounded-circle p-1" style="width: 28px; height: 28px;">
                                                        <i class="bi bi-check-lg"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('admin.pelanggaran.destroy', $p) }}" method="POST" class="d-inline" data-confirm="Hapus catatan log pelanggaran ini?" data-confirm-title="Hapus Log Pelanggaran?" data-confirm-btn="Ya, Hapus" data-confirm-type="danger" title="Hapus Log">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light border text-danger rounded-circle p-1" style="width: 28px; height: 28px;">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-shield-check text-success display-4 d-block mb-3"></i>
                                        <h6 class="fw-bold">Tidak Ada Log Pelanggaran</h6>
                                        <p class="small text-muted mb-0">Platform dalam kondisi aman dan tertib. Belum ada catatan pelanggaran etika atau kata terlarang.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($pelanggarans->hasPages())
                    <div class="p-3 border-top d-flex justify-content-end">
                        {{ $pelanggarans->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- ==================== TAB 2: PERINGKAT SISWA MELANGGAR ==================== --}}
        <div class="tab-pane fade" id="tab-siswa-pane" role="tabpanel" tabindex="0">
            <div class="card-custom overflow-hidden" style="border: 1px solid #e2e8f0;">
                <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Daftar Peringkat Siswa Pelanggar Aturan Terbanyak</h6>
                        <small class="text-muted">Diurutkan berdasarkan frekuensi pelanggaran kata kotor / ujaran tidak pantas</small>
                    </div>
                    <span class="badge bg-danger rounded-pill px-3 py-1">{{ count($topSiswa) }} Siswa Tercatat</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center" style="width: 60px;">Peringkat</th>
                                <th>Nama & Identitas Siswa</th>
                                <th class="text-center" style="width: 140px;">Total Pelanggaran</th>
                                <th style="width: 160px;">Terakhir Melanggar</th>
                                <th>Sampel Kata Terdeteksi</th>
                                <th>Status Akun</th>
                                <th class="text-center" style="width: 180px;">Aksi Moderasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topSiswa as $idx => $ts)
                                @php $siswaUser = $ts->user; @endphp
                                <tr>
                                    <td class="text-center">
                                        @if($idx === 0)
                                            <span class="badge rounded-circle p-2 fw-bold text-white shadow-xs" style="width: 32px; height: 32px; background: #dc3545; font-size: 0.9rem;">1</span>
                                        @elseif($idx === 1)
                                            <span class="badge rounded-circle p-2 fw-bold text-dark shadow-xs" style="width: 32px; height: 32px; background: #ffc107; font-size: 0.9rem;">2</span>
                                        @elseif($idx === 2)
                                            <span class="badge rounded-circle p-2 fw-bold text-dark shadow-xs" style="width: 32px; height: 32px; background: #e0e0e0; font-size: 0.9rem;">3</span>
                                        @else
                                            <span class="fw-bold text-muted font-mono">{{ $idx + 1 }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($siswaUser)
                                            <div class="fw-bold text-dark fs-6">{{ $siswaUser->name }}</div>
                                            <div class="small text-muted font-mono">
                                                NIS: <strong>{{ $siswaUser->nis ?? '-' }}</strong> &bull; {{ $siswaUser->nama_kelas }} &bull; {{ $siswaUser->jurusan?->nama_jurusan ?? '-' }}
                                            </div>
                                        @else
                                            <div class="fw-bold text-muted">Akun Siswa Dihapus</div>
                                            <small class="text-muted">User ID #{{ $ts->user_id }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger text-white rounded-pill px-3 py-1 fs-6">
                                            {{ $ts->total_pelanggaran }}x
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold text-dark">{{ \Carbon\Carbon::parse($ts->latest_violation)->format('d M Y, H:i') }}</div>
                                        <div class="text-muted font-mono small">{{ \Carbon\Carbon::parse($ts->latest_violation)->diffForHumans() }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($ts->pelanggaran_list as $pl)
                                                @if(is_array($pl->kata_terdeteksi))
                                                    @foreach($pl->kata_terdeteksi as $kt)
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-mono">{{ $kt }}</span>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        @if($siswaUser)
                                            @if($siswaUser->is_active)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-semibold">
                                                    <i class="bi bi-check-circle-fill me-1"></i>Aktif
                                                </span>
                                            @elseif($siswaUser->deactivation_type === 'berkala')
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 font-mono" style="font-size: 0.72rem;">
                                                    <i class="bi bi-clock-history me-1"></i>Nonaktif s/d {{ $siswaUser->deactivated_until?->format('d M Y') }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 font-mono" style="font-size: 0.72rem;">
                                                    <i class="bi bi-slash-circle me-1"></i>Dinonaktifkan
                                                </span>
                                            @endif

                                            @if($siswaUser->warning_count > 0)
                                                <span class="badge bg-warning text-dark border border-warning ms-1">
                                                    {{ $siswaUser->warning_count }}x Peringatan
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($siswaUser)
                                            <div class="d-flex align-items-center justify-content-center gap-1.5">
                                                <form action="{{ route('admin.pelanggaran.reset-user', $siswaUser) }}" method="POST" data-confirm="Pulihkan akun siswa {{ $siswaUser->name }} dan bersihkan seluruh catatan pelanggarannya?" data-confirm-title="Pulihkan Akun Siswa?" data-confirm-btn="Ya, Pulihkan" data-confirm-type="warning">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1 rounded-pill px-2.5 py-1" title="Pulihkan Akun Siswa">
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                        <span>Pulihkan</span>
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1 rounded-pill px-2.5 py-1" onclick="openModalTindakUser({{ $siswaUser->id }}, '{{ addslashes($siswaUser->name) }}', 'siswa', {{ $siswaUser->is_active ? 'true' : 'false' }})" title="Tindak / Sanksi Akun Siswa">
                                                    <i class="bi bi-shield-slash"></i>
                                                    <span>Sanksi</span>
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bi bi-emoji-smile fs-1 d-block mb-2 text-success"></i>
                                        Tidak ada catatan siswa yang melanggar aturan saat ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ==================== TAB 3: PERINGKAT GURU MELANGGAR ==================== --}}
        <div class="tab-pane fade" id="tab-guru-pane" role="tabpanel" tabindex="0">
            <div class="card-custom overflow-hidden" style="border: 1px solid #e2e8f0;">
                <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center bg-light">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Daftar Peringkat Guru yang Terdeteksi Melanggar Aturan</h6>
                        <small class="text-muted">Catatan guru pengajar yang terdeteksi kata kotor / tidak pantas saat membalas ulasan atau berkomunikasi</small>
                    </div>
                    <span class="badge bg-primary rounded-pill px-3 py-1">{{ count($topGuru) }} Guru Tercatat</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center" style="width: 60px;">Peringkat</th>
                                <th>Nama & Identitas Guru</th>
                                <th class="text-center" style="width: 140px;">Total Pelanggaran</th>
                                <th style="width: 160px;">Terakhir Melanggar</th>
                                <th>Sampel Kata Terdeteksi</th>
                                <th>Status Akun</th>
                                <th class="text-center" style="width: 180px;">Aksi Moderasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topGuru as $idx => $tg)
                                @php $guruUser = $tg->user; @endphp
                                <tr>
                                    <td class="text-center">
                                        @if($idx === 0)
                                            <span class="badge rounded-circle p-2 fw-bold text-white shadow-xs" style="width: 32px; height: 32px; background: #dc3545; font-size: 0.9rem;">1</span>
                                        @elseif($idx === 1)
                                            <span class="badge rounded-circle p-2 fw-bold text-dark shadow-xs" style="width: 32px; height: 32px; background: #ffc107; font-size: 0.9rem;">2</span>
                                        @elseif($idx === 2)
                                            <span class="badge rounded-circle p-2 fw-bold text-dark shadow-xs" style="width: 32px; height: 32px; background: #e0e0e0; font-size: 0.9rem;">3</span>
                                        @else
                                            <span class="fw-bold text-muted font-mono">{{ $idx + 1 }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($guruUser)
                                            <div class="fw-bold text-dark fs-6">{{ $guruUser->name }}</div>
                                            <div class="small text-muted font-mono">
                                                NIP: <strong>{{ $guruUser->nis ?? '-' }}</strong> &bull; {{ $guruUser->jurusan?->nama_jurusan ?? 'Guru Pengajar' }}
                                            </div>
                                        @else
                                            <div class="fw-bold text-muted">Akun Guru Terhapus</div>
                                            <small class="text-muted">User ID #{{ $tg->user_id }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger text-white rounded-pill px-3 py-1 fs-6">
                                            {{ $tg->total_pelanggaran }}x
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold text-dark">{{ \Carbon\Carbon::parse($tg->latest_violation)->format('d M Y, H:i') }}</div>
                                        <div class="text-muted font-mono small">{{ \Carbon\Carbon::parse($tg->latest_violation)->diffForHumans() }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($tg->pelanggaran_list as $gl)
                                                @if(is_array($gl->kata_terdeteksi))
                                                    @foreach($gl->kata_terdeteksi as $kt)
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle font-mono">{{ $kt }}</span>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        @if($guruUser)
                                            @if($guruUser->is_active)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-semibold">
                                                    <i class="bi bi-check-circle-fill me-1"></i>Aktif
                                                </span>
                                            @elseif($guruUser->deactivation_type === 'berkala')
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 font-mono" style="font-size: 0.72rem;">
                                                    <i class="bi bi-clock-history me-1"></i>Nonaktif s/d {{ $guruUser->deactivated_until?->format('d M Y') }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 font-mono" style="font-size: 0.72rem;">
                                                    <i class="bi bi-slash-circle me-1"></i>Dinonaktifkan
                                                </span>
                                            @endif

                                            @if($guruUser->warning_count > 0)
                                                <span class="badge bg-warning text-dark border border-warning ms-1">
                                                    {{ $guruUser->warning_count }}x Peringatan
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($guruUser)
                                            <div class="d-flex align-items-center justify-content-center gap-1.5">
                                                <form action="{{ route('admin.pelanggaran.reset-user', $guruUser) }}" method="POST" data-confirm="Pulihkan akun guru {{ $guruUser->name }} dan bersihkan seluruh catatan pelanggarannya?" data-confirm-title="Pulihkan Akun Guru?" data-confirm-btn="Ya, Pulihkan" data-confirm-type="warning">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1 rounded-pill px-2.5 py-1" title="Pulihkan Akun Guru">
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                        <span>Pulihkan</span>
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1 rounded-pill px-2.5 py-1" onclick="openModalTindakUser({{ $guruUser->id }}, '{{ addslashes($guruUser->name) }}', 'guru', {{ $guruUser->is_active ? 'true' : 'false' }})" title="Tindak / Sanksi Akun Guru">
                                                    <i class="bi bi-shield-slash"></i>
                                                    <span>Sanksi</span>
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bi bi-check-circle fs-1 d-block mb-2 text-success"></i>
                                        Tidak ada catatan guru yang melanggar aturan saat ini. Semua interaksi guru bersih dan tertib.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ==================== TAB 4: LAPORAN DARI USER ==================== --}}
        <div class="tab-pane fade" id="tab-report-pane" role="tabpanel" tabindex="0">
            <div class="card-custom p-4 mb-4" style="border: 1px solid #e2e8f0;">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3 pb-3 border-bottom">
                    <div>
                        <h6 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-flag-fill text-warning"></i>
                            <span>Daftar Ulasan yang Dilaporkan Pengguna</span>
                        </h6>
                        <small class="text-muted">
                            Sistem anti-spam aktif: Setiap pengguna hanya dapat mengirim 1 kali laporan per ulasan. Menampilkan ringkasan ulasan, jumlah pelapor, rincian alasan, serta tindakan sensor/hapus ulasan.
                        </small>
                    </div>
                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-1.5 rounded-pill font-mono">
                        {{ $reportedReviews->total() }} Ulasan Dilaporkan
                    </span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-light text-muted small">
                            <tr>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th style="min-width: 280px;">Ulasan & Siswa yang Dilaporkan</th>
                                <th style="min-width: 140px;" class="text-center">Guru Sasaran</th>
                                <th style="min-width: 150px;" class="text-center">Akumulasi Laporan</th>
                                <th style="min-width: 250px;">Rincian Pelapor & Alasan</th>
                                <th style="min-width: 140px;" class="text-center">Laporan Terakhir</th>
                                <th style="min-width: 110px;" class="text-center">Aksi Moderasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reportedReviews as $index => $rep)
                                @php
                                    $pen = $rep->penilaian;
                                    $siswaUser = $pen?->siswa;
                                    $guruTarget = $pen?->guru;
                                @endphp
                                <tr>
                                    <td class="text-center fw-bold text-muted font-mono">
                                        {{ $reportedReviews->firstItem() + $index }}
                                    </td>
                                    <td>
                                        @if($pen)
                                            <div class="d-flex align-items-center gap-2 mb-1.5">
                                                <div class="avatar-circle-sm bg-light text-dark fw-bold border">
                                                    {{ strtoupper(substr($siswaUser->name ?? 'A', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <span class="fw-bold text-dark d-block" style="font-size: 0.85rem;">
                                                        {{ $siswaUser->name ?? 'Siswa (Anonim)' }}
                                                    </span>
                                                    <small class="text-muted font-mono" style="font-size: 0.72rem;">
                                                        NIS: {{ $siswaUser->nis ?? '-' }} • {{ $siswaUser->kelas->nama_kelas ?? 'Kelas -' }}
                                                    </small>
                                                </div>
                                                @if($pen->is_censored)
                                                    <span class="badge bg-secondary-subtle text-secondary border rounded-pill ms-auto" style="font-size: 0.68rem;">
                                                        <i class="bi bi-eye-slash-fill me-1"></i>Tersensor
                                                    </span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success border rounded-pill ms-auto" style="font-size: 0.68rem;">
                                                        <i class="bi bi-eye-fill me-1"></i>Tayang Publik
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="p-2.5 rounded bg-light border small">
                                                @if($pen->kritik)
                                                    <div class="mb-1">
                                                        <strong class="text-warning-emphasis">Kritik:</strong>
                                                        <span class="text-dark">{{ $pen->kritik }}</span>
                                                    </div>
                                                @endif
                                                @if($pen->saran)
                                                    <div>
                                                        <strong class="text-info">Saran:</strong>
                                                        <span class="text-dark">{{ $pen->saran }}</span>
                                                    </div>
                                                @endif
                                                @if(!$pen->kritik && !$pen->saran)
                                                    <span class="text-muted fst-italic">Hanya memberikan rating bintang (tanpa kritik/saran tertulis).</span>
                                                @endif
                                            </div>
                                        @else
                                            <div class="text-muted fst-italic small">
                                                <i class="bi bi-exclamation-triangle text-warning me-1"></i> Ulasan asli telah dihapus.
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($guruTarget)
                                            <span class="fw-semibold text-dark d-block">{{ $guruTarget->nama }}</span>
                                            <small class="text-muted font-mono" style="font-size: 0.72rem;">{{ $guruTarget->mapel ?? 'Guru' }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1 rounded-pill fw-bold font-mono fs-7">
                                            <i class="bi bi-flag-fill me-1"></i> {{ $rep->total_reports }} Laporan
                                        </div>
                                        <small class="text-muted d-block mt-1 font-mono" style="font-size: 0.7rem;">
                                            1 Report/User Anti-Spam
                                        </small>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1.5" style="max-height: 140px; overflow-y: auto;">
                                            @foreach($rep->reports_detail as $detail)
                                                <div class="p-1.5 px-2 bg-white rounded border small d-flex flex-column">
                                                    <div class="d-flex align-items-center justify-content-between gap-1">
                                                        <span class="fw-semibold text-dark text-truncate" style="max-width: 140px; font-size: 0.78rem;">
                                                            {{ $detail->user->name ?? 'Guest / Pengguna' }}
                                                        </span>
                                                        <span class="badge bg-light text-dark border font-mono" style="font-size: 0.65rem;">
                                                            {{ $detail->alasan_label }}
                                                        </span>
                                                    </div>
                                                    @if($detail->catatan)
                                                        <small class="text-muted mt-0.5 fst-italic" style="font-size: 0.72rem;">
                                                            "{{ $detail->catatan }}"
                                                        </small>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="text-center font-mono small text-muted">
                                        {{ \Carbon\Carbon::parse($rep->latest_report_at)->diffForHumans() }}
                                        <small class="d-block text-muted" style="font-size: 0.7rem;">
                                            {{ \Carbon\Carbon::parse($rep->latest_report_at)->format('d M Y H:i') }}
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        {{-- MEATBALLS MENU (TITIK TIGA) --}}
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Menu Aksi">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 p-1.5" style="font-size: 0.8rem; min-width: 180px;">
                                                @if($pen && !$pen->is_censored)
                                                    <li>
                                                        <form action="{{ route('admin.pelanggaran.report.censor', $pen->id) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item py-1.5 rounded-2 d-flex align-items-center gap-2 text-warning-emphasis">
                                                                <i class="bi bi-eye-slash"></i>
                                                                <span>Sensor Ulasan</span>
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                                @if($pen && $siswaUser)
                                                    <li>
                                                        <button type="button" class="dropdown-item py-1.5 rounded-2 d-flex align-items-center gap-2 text-danger" onclick="openModalTindakUser({{ $siswaUser->id }}, '{{ addslashes($siswaUser->name) }}', 'siswa', {{ $siswaUser->is_active ? 'true' : 'false' }})">
                                                            <i class="bi bi-shield-slash"></i>
                                                            <span>Sanksi Siswa</span>
                                                        </button>
                                                    </li>
                                                @endif
                                                @if($pen)
                                                    <li>
                                                        <form action="{{ route('admin.pelanggaran.report.delete-review', $pen->id) }}" method="POST" data-confirm="Apakah Anda yakin ingin MENGHAPUS PERMANEN ulasan ini beserta riwayat laporannya?" data-confirm-title="Hapus Ulasan Permanen?" data-confirm-btn="Ya, Hapus Ulasan" data-confirm-type="danger">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="dropdown-item py-1.5 rounded-2 d-flex align-items-center gap-2 text-danger">
                                                                <i class="bi bi-trash"></i>
                                                                <span>Hapus Ulasan</span>
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                                <li><hr class="dropdown-divider my-1"></li>
                                                <li>
                                                    <form action="{{ route('admin.pelanggaran.report.dismiss', $rep->penilaian_id) }}" method="POST" data-confirm="Tolak/Abaikan laporan ini dan bersihkan dari daftar laporan?" data-confirm-title="Tolak Laporan Ulasan?" data-confirm-btn="Ya, Tolak Laporan" data-confirm-type="warning">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="dropdown-item py-1.5 rounded-2 d-flex align-items-center gap-2 text-muted">
                                                            <i class="bi bi-x-circle"></i>
                                                            <span>Tolak / Bersihkan</span>
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bi bi-shield-check fs-1 d-block mb-2 text-success"></i>
                                        Tidak ada ulasan siswa yang sedang dilaporkan oleh pengguna. Semua ulasan kondusif dan tertib.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($reportedReviews->hasPages())
                    <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <small class="text-muted">
                            Menampilkan {{ $reportedReviews->firstItem() ?? 0 }} - {{ $reportedReviews->lastItem() ?? 0 }} dari {{ $reportedReviews->total() }} ulasan dilaporkan
                        </small>
                        {{ $reportedReviews->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- MODAL TINDAK / SANKSI USER (UNTUK SISWA & GURU) --}}
<div class="modal fade" id="modalTindakUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formTindakUser" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            @csrf
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-danger" style="width: 40px; height: 40px; background: rgba(220, 53, 69, 0.1);">
                        <i class="bi bi-shield-slash-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0">Tindakan Sanksi Akun</h5>
                        <small class="text-muted" id="tindakTargetRoleLabel">Kelola status aktif akun</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-3">
                <div class="p-3 bg-light rounded-3 border mb-3">
                    <div class="small text-muted mb-0">Target Akun:</div>
                    <div class="fw-bold text-dark fs-6" id="tindakUserName">-</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small">Pilih Jenis Tindakan / Sanksi:</label>
                    <div class="d-flex flex-column gap-2">
                        <label class="form-check p-2.5 rounded border d-flex align-items-center gap-2 mb-0" style="cursor: pointer;">
                            <input class="form-check-input ms-0 mt-0" type="radio" name="action_type" value="hanya_peringatan" checked onchange="toggleTindakOptions(this.value)">
                            <div class="small">
                                <strong class="d-block text-dark">Beri Peringatan Saja (+1 Peringatan)</strong>
                                <span class="text-muted" style="font-size: 0.75rem;">Akun tetap aktif, hanya mencatat pelanggaran dan menambah akumulasi peringatan.</span>
                            </div>
                        </label>
                        <label class="form-check p-2.5 rounded border d-flex align-items-center gap-2 mb-0" style="cursor: pointer;">
                            <input class="form-check-input ms-0 mt-0" type="radio" name="action_type" value="nonaktif_berkala" onchange="toggleTindakOptions(this.value)">
                            <div class="small">
                                <strong class="d-block text-warning-emphasis">Nonaktifkan Sementara (Berkala)</strong>
                                <span class="text-muted" style="font-size: 0.75rem;">Akun tidak dapat login selama durasi tertentu dan aktif otomatis setelahnya.</span>
                            </div>
                        </label>
                        <label class="form-check p-2.5 rounded border d-flex align-items-center gap-2 mb-0" style="cursor: pointer;">
                            <input class="form-check-input ms-0 mt-0" type="radio" name="action_type" value="nonaktif_permanen" onchange="toggleTindakOptions(this.value)">
                            <div class="small">
                                <strong class="d-block text-danger">Nonaktifkan Secara Permanen (Dinonaktifkan Penuh)</strong>
                                <span class="text-muted" style="font-size: 0.75rem;">Akun diblokir penuh dan hanya bisa dibuka kembali melalui tindakan Admin.</span>
                            </div>
                        </label>
                        <label class="form-check p-2.5 rounded border d-flex align-items-center gap-2 mb-0" id="optionAktifkanBox" style="cursor: pointer;">
                            <input class="form-check-input ms-0 mt-0" type="radio" name="action_type" value="aktifkan_kembali" onchange="toggleTindakOptions(this.value)">
                            <div class="small">
                                <strong class="d-block text-success">Aktifkan Kembali Akun (Lepas Sanksi)</strong>
                                <span class="text-muted" style="font-size: 0.75rem;">Mencabut status sanksi nonaktif dan memulihkan akses login akun normal.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div id="boxDurasiBerkala" class="mb-3 d-none">
                    <label class="form-label fw-bold text-dark small">Pilih Durasi Nonaktif Sementara:</label>
                    <select name="duration_days" class="form-select form-select-sm">
                        <option value="5_hours">5 Jam</option>
                        <option value="1">1 Hari</option>
                        <option value="3" selected>3 Hari</option>
                        <option value="7">7 Hari (1 Minggu)</option>
                        <option value="14">14 Hari (2 Minggu)</option>
                        <option value="30">30 Hari (1 Bulan)</option>
                    </select>
                </div>

                <div class="mb-2">
                    <label class="form-label fw-bold text-dark small">Alasan Sanksi / Catatan (Opsional):</label>
                    <input type="text" name="deactivated_reason" class="form-control form-control-sm" placeholder="Contoh: Menggunakan kata kasar pada balasan ulasan...">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary-custom px-3 shadow-sm">
                    Simpan & Terapkan Sanksi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL RESET SEMUA PELANGGARAN --}}
<div class="modal fade" id="modalResetAllPelanggaran" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.pelanggaran.reset-all') }}" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            @csrf
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-danger" style="width: 40px; height: 40px; background: rgba(220, 53, 69, 0.1);">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    </div>
                    <h5 class="modal-title fw-bold text-dark">Reset Seluruh Log Pelanggaran</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-3">
                <p class="text-muted small mb-3">
                    Tindakan ini akan <strong>menghapus seluruh catatan log pelanggaran</strong> dan mengembalikan semua akun siswa & guru yang sedang dinonaktifkan / diberi sanksi uji coba menjadi <strong>aktif & bersih normal</strong>.
                </p>
                <div class="p-3 bg-light rounded-3 border small text-muted">
                    <i class="bi bi-info-circle text-primary me-1"></i> Sangat cocok digunakan setelah Anda selesai melakukan pengujian filter kata kotor atau uji coba penalti.
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger px-3 shadow-sm">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Ya, Reset Semua Data
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL POPUP LIHAT ULASAN LENGKAP --}}
<div class="modal fade" id="modalDetailUlasanPelanggar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom py-3 px-4" style="background: #f8fafc;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-primary" style="width: 38px; height: 38px; background: rgba(0, 51, 102, 0.08);">
                        <i class="bi bi-chat-quote-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0">Isi Ulasan / Pesan Melanggar</h5>
                        <small class="text-muted font-mono" id="modalUlasanWaktu">-</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                {{-- INFO PELANGGAR & SASARAN --}}
                <div class="row g-3 p-3 rounded-3 bg-light border mb-3">
                    <div class="col-sm-6">
                        <span class="text-muted small d-block font-mono mb-1" style="font-size: 0.72rem;">IDENTITAS PENGIRIM:</span>
                        <div class="d-flex align-items-center gap-1.5 mb-1">
                            <span class="badge bg-primary text-white font-mono px-2 py-0.5" id="modalUlasanRole" style="font-size: 0.7rem;">SISWA</span>
                            <strong class="text-dark fs-6" id="modalUlasanNama">-</strong>
                        </div>
                        <small class="text-muted font-mono d-block" id="modalUlasanIdentitas">-</small>
                    </div>
                    <div class="col-sm-6 border-start-sm">
                        <span class="text-muted small d-block font-mono mb-1" style="font-size: 0.72rem;">SASARAN & TIPE INSIDEN:</span>
                        <div class="fw-semibold text-dark mb-1" id="modalUlasanSasaran">-</div>
                        <span class="badge bg-secondary-subtle text-secondary border font-mono" id="modalUlasanTipe">-</span>
                    </div>
                </div>

                {{-- KATA TERDETEKSI --}}
                <div class="mb-3" id="modalUlasanKataContainer">
                    <label class="form-label font-mono small fw-bold text-danger mb-1.5" style="font-size: 0.72rem; letter-spacing: 0.5px;">KATA / FRASA TERDETEKSI SISTEM:</label>
                    <div class="d-flex flex-wrap gap-1.5" id="modalUlasanKataBadges"></div>
                </div>

                {{-- ISI LENGKAP ULASAN / TEKS --}}
                <div class="mb-3">
                    <label class="form-label font-mono small fw-bold text-dark mb-1.5" style="font-size: 0.72rem; letter-spacing: 0.5px;">TEKS LENGKAP PERCOBAAN KOMENTAR / ULASAN:</label>
                    <div class="p-3 rounded-3 border position-relative" style="background: #ffffff; border-left: 4px solid var(--primary, #003366) !important; font-size: 0.95rem; line-height: 1.6; color: #1e293b;">
                        <i class="bi bi-quote text-muted opacity-25 position-absolute" style="font-size: 3rem; right: 10px; bottom: -5px; pointer-events: none;"></i>
                        <p class="mb-0 text-break font-sans" id="modalUlasanTeks" style="white-space: pre-wrap; font-size: 0.92rem;"></p>
                    </div>
                </div>

                {{-- STATUS TINDAKAN SISTEM --}}
                <div class="d-flex align-items-center justify-content-between p-2.5 bg-light rounded-3 border">
                    <span class="text-muted small font-mono">Tindakan Sistem Terpasang:</span>
                    <span class="badge bg-dark px-2.5 py-1 font-mono" id="modalUlasanTindakan">-</span>
                </div>
            </div>
            <div class="modal-footer border-top py-2.5 px-4 bg-light justify-content-end">
                <button type="button" class="btn btn-secondary btn-sm px-3 rounded-pill" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openModalTindakUser(userId, userName, role, isActive) {
    document.getElementById('formTindakUser').action = '/admin/pelanggaran/user/' + userId + '/tindak';
    document.getElementById('tindakUserName').innerText = userName;
    document.getElementById('tindakTargetRoleLabel').innerText = (role === 'guru' ? 'Kelola Sanksi Akun Guru' : 'Kelola Sanksi Akun Siswa');
    
    // reset selection
    document.querySelector('input[name="action_type"][value="hanya_peringatan"]').checked = true;
    toggleTindakOptions('hanya_peringatan');

    new bootstrap.Modal(document.getElementById('modalTindakUser')).show();
}

function openModalDetailUlasan(btn) {
    const nama = btn.dataset.nama || '-';
    const role = btn.dataset.role || 'Siswa';
    const nis = btn.dataset.nis || '-';
    const kelas = btn.dataset.kelas || '-';
    const sasaran = btn.dataset.sasaran || '-';
    const tipe = btn.dataset.tipe || '-';
    const waktu = btn.dataset.waktu || '-';
    const isi = btn.dataset.isi || '';
    const tindakan = btn.dataset.tindakan || '-';
    
    let kata = [];
    try {
        kata = JSON.parse(btn.dataset.kata || '[]');
    } catch(e) {
        kata = [];
    }

    document.getElementById('modalUlasanNama').innerText = nama;
    document.getElementById('modalUlasanRole').innerText = role.toUpperCase();
    document.getElementById('modalUlasanRole').className = 'badge font-mono px-2 py-0.5 ' + (role.toLowerCase() === 'guru' ? 'bg-primary text-white' : 'bg-warning text-dark');
    document.getElementById('modalUlasanIdentitas').innerText = (role.toLowerCase() === 'guru' ? 'NIP: ' : 'NIS: ') + nis + ' • ' + kelas;
    document.getElementById('modalUlasanSasaran').innerText = 'Sasaran: ' + sasaran;
    document.getElementById('modalUlasanTipe').innerText = tipe;
    document.getElementById('modalUlasanWaktu').innerText = waktu;
    document.getElementById('modalUlasanTeks').innerText = isi;
    document.getElementById('modalUlasanTindakan').innerText = tindakan;

    const badgesContainer = document.getElementById('modalUlasanKataBadges');
    badgesContainer.innerHTML = '';
    if (kata && kata.length > 0) {
        document.getElementById('modalUlasanKataContainer').classList.remove('d-none');
        kata.forEach(k => {
            const badge = document.createElement('span');
            badge.className = 'badge bg-danger text-white px-2 py-1 font-mono';
            badge.style.fontSize = '0.78rem';
            badge.innerText = k;
            badgesContainer.appendChild(badge);
        });
    } else {
        document.getElementById('modalUlasanKataContainer').classList.add('d-none');
    }

    const modal = new bootstrap.Modal(document.getElementById('modalDetailUlasanPelanggar'));
    modal.show();
}

function toggleTindakOptions(val) {
    const box = document.getElementById('boxDurasiBerkala');
    if (val === 'nonaktif_berkala') {
        box.classList.remove('d-none');
    } else {
        box.classList.add('d-none');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('page_reports') || window.location.hash === '#tab-report-pane') {
        const reportBtn = document.getElementById('tab-report-btn');
        if (reportBtn) {
            bootstrap.Tab.getOrCreateInstance(reportBtn).show();
        }
    }
});
</script>
@endpush

@endsection
