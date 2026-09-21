@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<style>
    .admin-dashboard { max-width: 1280px; }
    .dashboard-intro { padding: 1.5rem; background: linear-gradient(135deg, var(--primary) 0%, #0b4b7b 100%); border-radius: 1rem; color: #fff; }
    .dashboard-intro .text-muted { color: rgba(255,255,255,.72) !important; }
    .dashboard-stat { display: block; height: 100%; padding: 1.1rem; color: inherit; text-decoration: none; background: var(--bg-card); border: 1px solid var(--border); border-radius: .8rem; transition: transform .18s ease, box-shadow .18s ease; }
    .dashboard-stat:hover { color: inherit; transform: translateY(-2px); box-shadow: var(--card-hover-shadow); }
    .dashboard-stat__label { color: var(--text-muted); font-size: .75rem; font-weight: 700; letter-spacing: .04em; text-transform: uppercase; }
    .dashboard-stat__value { margin-top: .4rem; color: var(--text-dark); font-size: 1.75rem; font-weight: 750; line-height: 1; }
    .dashboard-panel { height: 100%; padding: 1.25rem; background: var(--bg-card); border: 1px solid var(--border); border-radius: .85rem; box-shadow: var(--card-shadow); }
    .dashboard-panel__title { font-size: 1rem; font-weight: 700; margin: 0; }
    .dashboard-list-item { display: flex; align-items: center; gap: .85rem; padding: .8rem 0; color: inherit; text-decoration: none; border-bottom: 1px solid var(--border-subtle); }
    .dashboard-list-item:last-child { padding-bottom: 0; border-bottom: 0; }
    .dashboard-list-item:hover { color: var(--primary); }
    .dashboard-rank { width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; flex: 0 0 auto; color: var(--primary); background: var(--primary-subtle); border-radius: 50%; font-size: .78rem; font-weight: 700; }
    .dashboard-avatar { width: 38px; height: 38px; object-fit: cover; border: 1px solid var(--border); border-radius: 50%; }
    .dashboard-empty { padding: 2.5rem 1rem; color: var(--text-muted); text-align: center; }
</style>

<div class="admin-dashboard">
    <div class="dashboard-intro d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="small fw-semibold text-uppercase opacity-75 mb-1">Administrator</div>
            <h1 class="h3 fw-bold mb-1">Selamat datang, {{ auth()->user()->name }}</h1>
            <p class="text-muted mb-0">Ringkasan operasional GuruKuu.</p>
        </div>
        <div class="d-flex gap-2">
            <form action="{{ route('admin.dashboard.clear-cache') }}" method="POST" class="d-inline" onsubmit="return confirm('Bersihkan cache seluruh web sekarang?')">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm px-3"><i class="bi bi-arrow-clockwise me-1"></i>Bersihkan Cache</button>
            </form>
            <a href="{{ route('admin.pengaturan.index') }}" class="btn btn-light btn-sm px-3"><i class="bi bi-gear me-1"></i>Pengaturan</a>
            <a href="{{ route('admin.sipintu.index') }}" class="btn btn-outline-light btn-sm px-3"><i class="bi bi-cloud-arrow-down me-1"></i>SiPintu</a>
        </div>
    </div>

    @if(($unreadPelanggaranCount ?? 0) > 0)
        <div class="alert alert-danger border-0 d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
            <span><i class="bi bi-shield-exclamation me-2"></i><strong>{{ $unreadPelanggaranCount }}</strong> pelanggaran perlu ditinjau.</span>
            <a href="{{ route('admin.pelanggaran.index') }}" class="btn btn-sm btn-danger">Tinjau</a>
        </div>
    @endif

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div>
            <div class="page-label">RINGKASAN</div>
            <h2 class="h5 mb-0">Data sistem</h2>
        </div>
        <span class="badge {{ $periodeAktif ? 'bg-success' : 'bg-secondary' }} px-3 py-2">
            <i class="bi bi-calendar-check me-1"></i>{{ $periodeAktif?->nama_periode ?? 'Belum ada periode aktif' }}
        </span>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.guru.index') }}" class="dashboard-stat" data-admin-page-link>
                <div class="d-flex justify-content-between align-items-start"><span class="dashboard-stat__label">Guru</span><i class="bi bi-person-badge text-primary"></i></div>
                <div class="dashboard-stat__value">{{ number_format($totalGuru) }}</div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.siswa.index') }}" class="dashboard-stat" data-admin-page-link>
                <div class="d-flex justify-content-between align-items-start"><span class="dashboard-stat__label">Siswa</span><i class="bi bi-people text-success"></i></div>
                <div class="dashboard-stat__value">{{ number_format($totalSiswa) }}</div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.jurusan.index') }}" class="dashboard-stat" data-admin-page-link>
                <div class="d-flex justify-content-between align-items-start"><span class="dashboard-stat__label">Jurusan</span><i class="bi bi-grid text-warning"></i></div>
                <div class="dashboard-stat__value">{{ number_format($totalJurusan) }}</div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.leaderboard.index') }}" class="dashboard-stat">
                <div class="d-flex justify-content-between align-items-start"><span class="dashboard-stat__label">Penilaian</span><i class="bi bi-clipboard-check text-danger"></i></div>
                <div class="dashboard-stat__value">{{ number_format($totalPenilaian) }}</div>
            </a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <section class="dashboard-panel">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h2 class="dashboard-panel__title"><i class="bi bi-trophy text-warning me-2"></i>Guru teratas</h2>
                    <a href="{{ route('admin.leaderboard.index') }}" class="small text-decoration-none">Lihat semua</a>
                </div>
                @forelse($topGuru as $index => $guru)
                    <a href="{{ route('admin.guru.show', $guru) }}" class="dashboard-list-item">
                        <span class="dashboard-rank">{{ $index + 1 }}</span>
                        <img src="{{ $guru->photo_url }}" alt="" class="dashboard-avatar">
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-semibold text-truncate">{{ $guru->nama }}</div>
                            <small class="text-muted">{{ $guru->jurusan?->nama_jurusan ?? 'Umum' }} · {{ $guru->total_penilaian }} penilaian</small>
                        </div>
                        <strong class="text-primary">{{ number_format(($guru->rata_rata_nilai / 5) * 100, 0) }}%</strong>
                    </a>
                @empty
                    <div class="dashboard-empty"><i class="bi bi-inbox d-block fs-4 mb-2"></i>Belum ada penilaian.</div>
                @endforelse
            </section>
        </div>
        <div class="col-lg-5">
            <section class="dashboard-panel">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h2 class="dashboard-panel__title"><i class="bi bi-chat-square-text text-primary me-2"></i>Feedback terbaru</h2>
                    <a href="{{ route('admin.kritik-saran.index') }}" class="small text-decoration-none">Lihat semua</a>
                </div>
                @forelse($feedbacks as $feedback)
                    <a href="{{ route('admin.kritik-saran.index') }}" class="dashboard-list-item">
                        <span class="dashboard-rank"><i class="bi bi-chat-dots"></i></span>
                        <div class="flex-grow-1 min-w-0">
                            <div class="small text-truncate">{{ Str::limit($feedback->kritik ?: $feedback->saran, 88) }}</div>
                            <small class="text-muted">{{ $feedback->guru?->nama ?? '-' }} · {{ $feedback->created_at->diffForHumans() }}</small>
                        </div>
                    </a>
                @empty
                    <div class="dashboard-empty"><i class="bi bi-chat-square-dots d-block fs-4 mb-2"></i>Belum ada feedback.</div>
                @endforelse
            </section>
        </div>
        <div class="col-12">
            <section class="dashboard-panel d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h2 class="dashboard-panel__title"><i class="bi bi-hdd-network text-primary me-2"></i>SiPintu Gateway</h2>
                    <div id="gatewayStatus" class="small text-muted mt-1">Status belum diperiksa.</div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-custom btn-sm" id="checkGatewayButton" data-url="{{ route('admin.sipintu.check-connection') }}"><i class="bi bi-arrow-clockwise me-1"></i>Cek koneksi</button>
                    <a href="{{ route('admin.sipintu.index') }}" class="btn btn-primary-custom btn-sm">Buka Gateway</a>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const button = document.getElementById('checkGatewayButton');
    const status = document.getElementById('gatewayStatus');
    if (!button || !status) return;

    button.addEventListener('click', async function () {
        button.disabled = true;
        status.textContent = 'Memeriksa koneksi...';

        try {
            const response = await fetch(button.dataset.url, { credentials: 'same-origin', headers: { Accept: 'application/json' } });
            const result = await response.json();
            const ping = result.ping || {};
            status.textContent = ping.success
                ? `Terhubung · ${ping.latency_ms || 0} ms`
                : (ping.message || 'Gateway tidak dapat dihubungi.');
            status.className = `small mt-1 ${ping.success ? 'text-success' : 'text-danger'}`;
        } catch (_) {
            status.textContent = 'Gateway tidak dapat dihubungi.';
            status.className = 'small mt-1 text-danger';
        } finally {
            button.disabled = false;
        }
    });
});
</script>
@endpush
