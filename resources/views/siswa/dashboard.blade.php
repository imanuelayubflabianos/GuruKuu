@extends('layouts.siswa')
@section('title', 'Dashboard')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <div class="page-label">PORTAL SISWA</div>
        <h1 class="page-title">Halo, {{ auth()->user()->name }}! 👋</h1>
        @if(!empty($kelasAktif))
            <p class="page-subtitle">Kelas: <strong>{{ $kelasAktif->nama_kelas }} Kelas {{ $kelasAktif->tingkat }}</strong> ({{ $kelasAktif->jurusan->nama_jurusan }})</p>
        @else
            <p class="page-subtitle text-muted">Akses seluruh data guru sekolah untuk evaluasi pengajaran yang objektif dan transparan.</p>
        @endif
    </div>
    <div>
        <span class="badge rounded-pill px-3 py-2 shadow-sm" style="background: var(--bg-card); color: var(--text-dark); border: 1px solid var(--border); font-size: 0.85rem;">
            <i class="bi bi-calendar-check-fill text-primary me-1.5"></i> {{ $periodeAktif?->nama_periode ?? 'Semester Aktif' }}
        </span>
    </div>
</div>

@php
    $pctSelesai = $totalGuru > 0 ? round(($jumlahSudah / $totalGuru) * 100) : 0;
@endphp

@if(($notifikasiPelanggaran ?? collect())->isNotEmpty())
    <div class="alert alert-warning border-warning shadow-sm mb-4">
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="bi bi-shield-exclamation fs-5"></i>
            <strong>Notifikasi pelanggaran</strong>
        </div>
        @foreach($notifikasiPelanggaran as $notifikasi)
            <div class="d-flex align-items-start justify-content-between gap-3 border-top border-warning-subtle pt-2 mt-2">
                <span class="small">{{ $notifikasi->notifikasi_siswa ?: 'Anda menerima notifikasi pelanggaran dari sistem.' }}</span>
                <form action="{{ route('siswa.notifikasi.read', $notifikasi) }}" method="POST" class="flex-shrink-0">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-sm btn-outline-dark">Mengerti</button>
                </form>
            </div>
        @endforeach
    </div>
@endif

{{-- STATISTIK CEPAT (FROSTED GLASS CARDS) --}}
<div class="row g-2 g-md-4 mb-3 mb-md-4">
    <div class="col-4 col-md-4">
        <div class="card-glass p-2.5 p-md-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(0, 51, 102, 0.08); color: var(--primary);">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>
                    <span class="badge rounded-pill d-none d-sm-inline" style="background: rgba(0, 51, 102, 0.08); color: var(--primary); font-size: 0.7rem; font-weight: 600;">Guru</span>
                </div>
                <div class="text-muted small font-mono" style="font-size: 0.62rem; letter-spacing: 0.5px;">TOTAL GURU</div>
                <div class="fw-bold fs-3 text-dark mt-0.5" style="font-feature-settings: 'tnum';">{{ $totalGuru }}</div>
            </div>
            <small class="text-muted font-mono d-none d-md-block mt-1" style="font-size: 0.72rem;">Daftar guru aktif sekolah</small>
        </div>
    </div>
    <div class="col-4 col-md-4">
        <div class="card-glass p-2.5 p-md-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(0, 168, 107, 0.1); color: var(--accent);">
                        <i class="bi bi-check2-circle fs-5"></i>
                    </div>
                    <span class="badge rounded-pill d-none d-sm-inline" style="background: rgba(0, 168, 107, 0.1); color: var(--accent); font-size: 0.7rem; font-weight: 600;">{{ $pctSelesai }}%</span>
                </div>
                <div class="text-muted small font-mono" style="font-size: 0.62rem; letter-spacing: 0.5px;">SUDAH DINILAI</div>
                <div class="fw-bold fs-3 text-dark mt-0.5" style="font-feature-settings: 'tnum';">
                    {{ $jumlahSudah }} <span class="text-muted fs-6 fw-normal d-none d-sm-inline">/ {{ $totalGuru }}</span>
                </div>
            </div>
            <div class="progress mt-1.5" style="height: 5px; border-radius: 10px; background: rgba(0,0,0,0.06);">
                <div class="progress-bar bg-success rounded-pill" style="width: {{ $pctSelesai }}%;"></div>
            </div>
        </div>
    </div>
    <div class="col-4 col-md-4">
        <div class="card-glass p-2.5 p-md-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(245, 158, 11, 0.12); color: #d97706;">
                        <i class="bi bi-hourglass-split fs-5"></i>
                    </div>
                    <span class="badge rounded-pill d-none d-sm-inline" style="background: rgba(245, 158, 11, 0.12); color: #d97706; font-size: 0.7rem; font-weight: 600;">Sisa</span>
                </div>
                <div class="text-muted small font-mono" style="font-size: 0.62rem; letter-spacing: 0.5px;">SISA PENILAIAN</div>
                <div class="fw-bold fs-3 text-dark mt-0.5" style="font-feature-settings: 'tnum';">{{ max(0, $totalGuru - $jumlahSudah) }}</div>
            </div>
            <small class="text-muted font-mono d-none d-md-block mt-1" style="font-size: 0.72rem;">Menunggu evaluasi Anda</small>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- TOP GURU SEKOLAH (PODIUM GLASS 2-1-3) --}}
    <div class="col-lg-8">
        <div class="card-glass p-3 p-md-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3 mb-md-4 flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold mb-0 text-dark fs-5"><i class="bi bi-trophy-fill text-warning me-2"></i>Top Guru Sekolah</h6>
                    <small class="text-muted" style="font-size: 0.75rem;">Peringkat guru favorit berdasarkan rating siswa</small>
                </div>
                <a href="{{ route('siswa.leaderboard.index') }}" class="btn btn-sm btn-outline-custom rounded-pill px-2.5 py-1" style="font-size: 0.75rem;">Leaderboard</a>
            </div>

            @if($topGuru->isEmpty())
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                    <p class="mb-0 small">Belum ada data evaluasi guru untuk periode ini.</p>
                </div>
            @else
                @php
                    $g1 = $topGuru->get(0);
                    $g2 = $topGuru->get(1);
                    $g3 = $topGuru->get(2);
                @endphp
                <div class="row g-2 g-md-3 align-items-end pt-1 pb-1 gk-podium-row">
                    {{-- #2 PERAK (KIRI) --}}
                    <div class="col-4 order-1 order-md-1 gk-podium-col gk-podium-2">
                        @if($g2)
                        <div class="card-custom gk-podium-card p-2 p-md-3 text-center h-100" style="border: 1px solid rgba(148, 163, 184, 0.4); box-shadow: 0 4px 16px rgba(0,0,0,0.03);">
                            <div class="mb-1 mb-md-2">
                                <span class="badge rounded-pill px-2 py-0.5" style="background: #94a3b8; color: #fff; font-size: 0.68rem;">
                                    <i class="bi bi-award-fill me-1"></i> #2 PERAK
                                </span>
                            </div>
                            <div class="position-relative d-inline-block mb-1 mb-md-2">
                                <img src="{{ $g2->photo_url }}" class="rounded-circle shadow-sm gk-podium-avatar-2" style="width: 54px; height: 54px; object-fit: cover; border: 2.5px solid #94a3b8;">
                            </div>
                            <h6 class="fw-bold mb-0.5 text-truncate gk-podium-nama" title="{{ $g2->nama }}" style="font-size: 0.82rem;">{{ $g2->nama }}</h6>
                            <small class="text-muted d-block mb-1.5 text-truncate font-mono gk-podium-jurusan" style="font-size: 0.65rem;">{{ strtoupper($g2->jurusan?->nama_jurusan ?? 'UMUM') }}</small>
                            <div class="d-flex justify-content-between font-mono mb-1 gk-podium-statbox" style="font-size: 0.7rem;">
                                <span class="text-muted">Skor</span>
                                <span class="fw-bold text-primary gk-podium-score">{{ round(($g2->rata_rata_nilai / 5) * 100) }}%</span>
                            </div>
                            <div class="progress mb-1.5" style="height: 4px; border-radius: 4px;">
                                <div class="progress-bar bg-primary rounded-pill" style="width: {{ round(($g2->rata_rata_nilai / 5) * 100) }}%;"></div>
                            </div>
                            <a href="{{ route('siswa.guru.show', $g2) }}" class="btn btn-sm btn-outline-custom btn-podium w-100 rounded-pill py-1" style="font-size: 0.72rem;">
                                Detail
                            </a>
                        </div>
                        @endif
                    </div>

                    {{-- #1 EMAS (TENGAH - ELEVATED PODIUM) --}}
                    <div class="col-4 order-2 order-md-2 mb-0 gk-podium-col gk-podium-1">
                        @if($g1)
                        <div class="card-custom gk-podium-card p-2 p-md-3 text-center h-100 position-relative" style="border: 2px solid #f59e0b; box-shadow: 0 12px 32px rgba(245, 158, 11, 0.18); background: var(--bg-card);">
                            <div class="mb-1 mb-md-2">
                                <span class="badge rounded-pill px-2 px-md-3 py-0.5 py-md-1 fw-bold shadow-sm" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: #ffffff; font-size: 0.72rem;">
                                    <i class="bi bi-trophy-fill me-1"></i> #1 EMAS
                                </span>
                            </div>
                            <div class="position-relative d-inline-block mb-1 mb-md-2">
                                <img src="{{ $g1->photo_url }}" class="rounded-circle shadow gk-podium-avatar-1" style="width: 64px; height: 64px; object-fit: cover; border: 3px solid #f59e0b;">
                            </div>
                            <h6 class="fw-bold mb-0.5 text-truncate text-dark gk-podium-nama" title="{{ $g1->nama }}" style="font-size: 0.88rem;">{{ $g1->nama }}</h6>
                            <small class="d-block mb-1.5 text-truncate font-mono text-warning gk-podium-jurusan" style="font-size: 0.68rem;">{{ strtoupper($g1->jurusan?->nama_jurusan ?? 'UMUM') }}</small>
                            <div class="d-flex justify-content-between font-mono mb-1 gk-podium-statbox" style="font-size: 0.72rem;">
                                <span class="text-dark fw-bold">Skor</span>
                                <span class="fw-bold text-warning gk-podium-score">{{ round(($g1->rata_rata_nilai / 5) * 100) }}%</span>
                            </div>
                            <div class="progress mb-1.5" style="height: 5px; border-radius: 4px;">
                                <div class="progress-bar bg-warning rounded-pill" style="width: {{ round(($g1->rata_rata_nilai / 5) * 100) }}%;"></div>
                            </div>
                            <a href="{{ route('siswa.guru.show', $g1) }}" class="btn btn-sm btn-primary-custom btn-podium w-100 rounded-pill py-1 fw-bold" style="font-size: 0.75rem;">
                                Detail
                            </a>
                        </div>
                        @endif
                    </div>

                    {{-- #3 PERUNGGU (KANAN) --}}
                    <div class="col-4 order-3 order-md-3 gk-podium-col gk-podium-3">
                        @if($g3)
                        <div class="card-custom gk-podium-card p-2 p-md-3 text-center h-100" style="border: 1px solid rgba(217, 119, 6, 0.3); box-shadow: 0 4px 16px rgba(0,0,0,0.03);">
                            <div class="mb-1 mb-md-2">
                                <span class="badge rounded-pill px-2 py-0.5" style="background: #d97706; color: #fff; font-size: 0.68rem;">
                                    <i class="bi bi-award-fill me-1"></i> #3 PERUNGGU
                                </span>
                            </div>
                            <div class="position-relative d-inline-block mb-1 mb-md-2">
                                <img src="{{ $g3->photo_url }}" class="rounded-circle shadow-sm gk-podium-avatar-3" style="width: 54px; height: 54px; object-fit: cover; border: 2.5px solid #d97706;">
                            </div>
                            <h6 class="fw-bold mb-0.5 text-truncate gk-podium-nama" title="{{ $g3->nama }}" style="font-size: 0.82rem;">{{ $g3->nama }}</h6>
                            <small class="text-muted d-block mb-1.5 text-truncate font-mono gk-podium-jurusan" style="font-size: 0.65rem;">{{ strtoupper($g3->jurusan?->nama_jurusan ?? 'UMUM') }}</small>
                            <div class="d-flex justify-content-between font-mono mb-1 gk-podium-statbox" style="font-size: 0.7rem;">
                                <span class="text-muted">Skor</span>
                                <span class="fw-bold text-primary gk-podium-score">{{ round(($g3->rata_rata_nilai / 5) * 100) }}%</span>
                            </div>
                            <div class="progress mb-1.5" style="height: 4px; border-radius: 4px;">
                                <div class="progress-bar bg-primary rounded-pill" style="width: {{ round(($g3->rata_rata_nilai / 5) * 100) }}%;"></div>
                            </div>
                            <a href="{{ route('siswa.guru.show', $g3) }}" class="btn btn-sm btn-outline-custom btn-podium w-100 rounded-pill py-1" style="font-size: 0.72rem;">
                                Detail
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- RIWAYAT PENILAIAN TERAKHIR --}}
    <div class="col-lg-4">
        <div class="card-glass p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-primary me-2"></i>Riwayat Terakhir</h5>
                <a href="{{ route('siswa.riwayat') }}" class="btn btn-sm btn-outline-custom rounded-pill px-3 py-1">Semua</a>
            </div>

            @if($riwayatTerakhir->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-journal-x fs-1 d-block mb-2 opacity-50"></i>
                    <p class="mb-2 small">Anda belum memberikan penilaian periode ini.</p>
                    <a href="{{ route('siswa.guru.index') }}" class="btn btn-sm btn-primary-custom rounded-pill px-3">Mulai Menilai</a>
                </div>
            @else
                <div class="d-flex flex-column gap-2.5">
                    @foreach($riwayatTerakhir as $riwayat)
                        @php
                            $rwScore = round(($riwayat->total_nilai / 30) * 100);
                            $rwBadge = $rwScore >= 80 ? 'bg-success' : ($rwScore >= 60 ? 'bg-info' : 'bg-warning');
                        @endphp
                        <div class="d-flex align-items-center gap-3 p-2.5 rounded-3 border" style="background: var(--bg-card); border-color: var(--border) !important;">
                            <img src="{{ $riwayat->guru->photo_url }}" class="rounded-circle flex-shrink-0 shadow-sm" style="width: 42px; height: 42px; object-fit: cover; border: 2px solid var(--border);">
                            <div class="flex-grow-1 overflow-hidden">
                                <h6 class="fw-bold mb-0 text-truncate text-dark" style="font-size: 0.88rem;">{{ $riwayat->guru->nama }}</h6>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="badge {{ $rwBadge }} font-mono px-2 py-0.5" style="font-size: 0.7rem;">
                                        {{ $rwScore }}%
                                    </span>
                                    <div class="progress flex-grow-1" style="height: 4px; border-radius: 2px;">
                                        <div class="progress-bar {{ $rwBadge }}" style="width: {{ $rwScore }}%;"></div>
                                    </div>
                                    <small class="text-muted font-mono text-nowrap" style="font-size: 0.68rem;">{{ $riwayat->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

{{-- TOMBOL AKSI CEPAT --}}
@if($totalGuru > $jumlahSudah)
<div class="mt-4 text-center">
    <a href="{{ route('siswa.guru.index') }}" class="btn btn-cta rounded-pill px-5 py-3 shadow-lg" style="font-size: 1rem;">
        <i class="bi bi-pencil-square me-2"></i> Lanjutkan Penilaian Guru Sekarang
    </a>
</div>
@endif
@endsection