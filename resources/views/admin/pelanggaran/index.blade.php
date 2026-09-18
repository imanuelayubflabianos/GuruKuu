@extends('layouts.admin')

@section('title', 'Log Notifikasi Pelanggaran')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="page-label">Audit & Keamanan Komunitas</div>
            <h1 class="page-title d-flex align-items-center gap-2">
                <i class="bi bi-shield-exclamation text-danger"></i> Log Notifikasi Pelanggaran
            </h1>
            <p class="page-subtitle mb-0">Catatan otomatis siswa atau pengguna yang melanggar aturan kebijakan & etika (kata kotor, ujaran kebencian, atau toxic).</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if(($stats['unread'] ?? 0) > 0)
                <form action="{{ route('admin.pelanggaran.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm">
                        <i class="bi bi-check2-all text-primary me-1"></i> Tandai Semua Dibaca
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.kritik-saran.index') }}" class="btn btn-outline-custom btn-sm rounded-pill px-3 shadow-sm">
                <i class="bi bi-chat-heart me-1"></i> Ke Kritik & Saran
            </a>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card border-start border-4 border-danger">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-card-label">Belum Ditinjau</span>
                    <span class="badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1">
                        <i class="bi bi-bell-fill me-1"></i> Perlu Tindakan
                    </span>
                </div>
                <div class="stat-card-value text-danger d-flex align-items-center gap-2">
                    {{ $stats['unread'] }}
                    @if($stats['unread'] > 0)
                        <span class="spinner-grow spinner-grow-sm text-danger" role="status"></span>
                    @endif
                </div>
                <small class="text-muted">Notifikasi baru yang belum dibuka</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card border-start border-4 border-primary">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-card-label">Total Pelanggaran</span>
                    <i class="bi bi-shield-check text-primary fs-5"></i>
                </div>
                <div class="stat-card-value">{{ $stats['total'] }}</div>
                <small class="text-muted">Akumulasi log pelanggaran tercatat</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card border-start border-4 border-warning">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-card-label">Pelanggaran Ulasan</span>
                    <i class="bi bi-chat-square-quote text-warning fs-5"></i>
                </div>
                <div class="stat-card-value">{{ $stats['penilaian'] }}</div>
                <small class="text-muted">Ujaran toxic saat menilai guru</small>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card border-start border-4 border-info">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-card-label">Pesan Kontak Kotor</span>
                    <i class="bi bi-envelope-exclamation text-info fs-5"></i>
                </div>
                <div class="stat-card-value">{{ $stats['kontak'] }}</div>
                <small class="text-muted">Kata kasar pada pesan kontak</small>
            </div>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="card-custom p-3 mb-4">
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
                    <input type="text" name="search" class="form-control" placeholder="Cari nama siswa, NIS, atau kata..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary-custom btn-sm flex-fill">Filter</button>
                @if(request()->hasAny(['status', 'tipe', 'search']))
                    <a href="{{ route('admin.pelanggaran.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- LIST TABLE --}}
    <div class="card-custom overflow-hidden">
        <div class="table-responsive">
            <table class="table table-custom table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 50px;">Status</th>
                        <th>Waktu & Tanggal</th>
                        <th>Identitas Pelanggar</th>
                        <th>Tipe & Target</th>
                        <th>Kata Terdeteksi</th>
                        <th>Isi Percobaan Komentar</th>
                        <th>Tindakan Sistem</th>
                        <th class="text-end" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pelanggarans as $p)
                        <tr class="{{ !$p->is_read ? 'bg-danger-subtle bg-opacity-10' : '' }}">
                            <td>
                                @if(!$p->is_read)
                                    <span class="badge bg-danger p-1 rounded-circle" title="Perlu Tindakan" style="width: 12px; height: 12px; display: inline-block;"></span>
                                @else
                                    <i class="bi bi-check2 text-muted" title="Sudah Ditinjau"></i>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold small text-dark">{{ $p->created_at->format('d M Y') }}</div>
                                <div class="text-muted font-mono" style="font-size: 0.72rem;">{{ $p->created_at->format('H:i') }} ({{ $p->created_at->diffForHumans() }})</div>
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
                                            NIP: <strong>{{ $p->user->nis ?? '-' }}</strong> &bull; {{ $p->user->detail_role_label }}
                                        @else
                                            NIS: <strong>{{ $p->user->nis ?? '-' }}</strong> &bull; {{ $p->user->nama_kelas }}
                                        @endif
                                    </div>

                                    <div class="d-flex flex-wrap gap-1 align-items-center">
                                        @if($p->user->is_active)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5 font-mono" style="font-size: 0.68rem;">
                                                <i class="bi bi-check-circle-fill me-0.5"></i> Akun Aktif
                                            </span>
                                        @elseif($p->user->deactivation_type === 'berkala')
                                            <span class="badge bg-warning-subtle text-dark border border-warning px-1.5 py-0.5 font-mono" style="font-size: 0.68rem;">
                                                <i class="bi bi-clock-history me-0.5"></i> Nonaktif s/d {{ $p->user->deactivated_until?->format('d M Y') }}
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-1.5 py-0.5 font-mono" style="font-size: 0.68rem;">
                                                <i class="bi bi-slash-circle me-0.5"></i> Nonaktif Permanen
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
                                        Guru Target: <span class="fw-semibold text-dark">{{ $p->guru->nama }}</span>
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
                            <td>
                                <div class="small text-break" style="max-width: 280px; line-height: 1.4;">
                                    &ldquo;{{ Str::limit($p->isi_teks, 90) }}&rdquo;
                                </div>
                                @if(strlen($p->isi_teks) > 90)
                                    <details class="mt-1">
                                        <summary class="text-primary small" style="cursor: pointer;">Lihat teks lengkap</summary>
                                        <div class="p-2 bg-light rounded small mt-1 border text-break">
                                            {{ $p->isi_teks }}
                                        </div>
                                    </details>
                                @endif
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
                            <td class="text-end">
                                <div class="d-flex align-items-center justify-content-end gap-1">
                                    @if(!$p->is_read)
                                        <form action="{{ route('admin.pelanggaran.read', $p) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-light border text-success" title="Tandai Sudah Ditinjau">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.pelanggaran.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan log pelanggaran ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger" title="Hapus Log">
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
@endsection
