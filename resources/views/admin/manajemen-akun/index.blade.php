@extends('layouts.admin')
@section('title', 'Manajemen Akun')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">KEAMANAN & AKUN</div>
        <h1 class="page-title">Manajemen Akun Siswa</h1>
        <p class="page-subtitle">Aktivasi, generate password, dan reset akun siswa.</p>
    </div>
</div>

{{-- Statistik --}}
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-card-label">TOTAL SISWA</div>
            <div class="stat-card-value">{{ $stats['total'] }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="border-left: 4px solid var(--accent);">
            <div class="stat-card-label">AKUN AKTIF</div>
            <div class="stat-card-value" style="color: var(--accent);">{{ $stats['aktif'] }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card" style="border-left: 4px solid #ef4444;">
            <div class="stat-card-label">DINONAKTIFKAN</div>
            <div class="stat-card-value" style="color: #ef4444;">{{ $stats['belum'] }}</div>
        </div>
    </div>
</div>

{{-- Notifikasi Password Baru (Hanya muncul 1x) --}}
@if(session('generated_password'))
<div class="alert alert-warning border-0 shadow-sm mb-4" style="background: #fef3c7; border-left: 4px solid #f59e0b;">
    <div class="d-flex align-items-start">
        <i class="bi bi-key-fill fs-3 me-3" style="color: #f59e0b;"></i>
        <div class="flex-grow-1">
            <h6 class="fw-bold mb-1">Password Berhasil Di-generate!</h6>
            <p class="mb-2 small">Untuk: <strong>{{ session('generated_password_for') }}</strong></p>
            <div class="d-flex align-items-center gap-2">
                <code class="fs-5 fw-bold px-3 py-2 rounded" style="background: white; color: var(--primary); letter-spacing: 2px;">
                    {{ session('generated_password') }}
                </code>
                <button type="button" class="btn btn-sm btn-outline-custom" onclick="navigator.clipboard.writeText('{{ session('generated_password') }}'); this.innerHTML='<i class=\'bi bi-check\'></i> Tersalin!';">
                    <i class="bi bi-clipboard"></i> Salin
                </button>
            </div>
            <small class="text-danger d-block mt-2">
                <i class="bi bi-exclamation-triangle"></i> 
                <strong>SEGERA sampaikan password ini ke siswa secara langsung!</strong> 
                Password ini hanya ditampilkan satu kali.
            </small>
        </div>
    </div>
</div>
@endif

{{-- Filter & Search --}}
<div class="card-custom p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label font-mono small">CARI SISWA</label>
            <input type="text" name="search" class="form-control" placeholder="NIS atau nama..." value="{{ request('search') }}" style="border-radius: 8px;">
        </div>
        <div class="col-md-3">
            <label class="form-label font-mono small">STATUS</label>
            <select name="filter" class="form-select" style="border-radius: 8px;" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('filter') === 'aktif' ? 'selected' : '' }}>✅ Aktif</option>
                <option value="belum" {{ request('filter') === 'belum' || request('filter') === 'nonaktif' ? 'selected' : '' }}>⛔ Dinonaktifkan</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary-custom w-100"><i class="bi bi-search"></i> Cari</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.manajemen-akun.index') }}" class="btn btn-outline-custom w-100">Reset</a>
        </div>
    </form>
</div>

{{-- Tabel Daftar Siswa --}}
<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>NIS</th>
                    <th>NAMA SISWA</th>
                    <th>KELAS</th>
                    <th>STATUS</th>
                    <th>TERAKHIR DIAKTIFKAN</th>
                    <th class="text-center">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswa as $s)
                <tr>
                    <td class="font-mono" style="font-size: 0.85rem;">{{ $s->nis }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <img src="{{ $s->photo_url }}" class="rounded-circle me-2 flex-shrink-0" width="36" height="36" style="width: 36px; height: 36px; min-width: 36px; min-height: 36px; aspect-ratio: 1 / 1; object-fit: cover; flex-shrink: 0;">
                            <div>
                                <div class="fw-bold">{{ $s->name }}</div>
                                <small class="text-muted">{{ $s->jurusan?->nama_jurusan ?? '-' }}</small>
                            </div>
                        </div>
                    </td>
                    <td class="font-mono">{{ $s->kelas ?? '-' }}</td>
                    <td>
                        @if($s->is_active)
                            @if($s->force_change_password)
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 font-mono" style="font-size: 0.72rem;">
                                    <i class="bi bi-clock-history me-1"></i>Belum Login Pertama
                                </span>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-semibold">
                                    <i class="bi bi-check-circle-fill me-1"></i>Aktif
                                </span>
                            @endif
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 font-mono" style="font-size: 0.72rem;">
                                <i class="bi bi-slash-circle me-1"></i>Dinonaktifkan
                            </span>
                        @endif
                    </td>
                    <td class="font-mono" style="font-size: 0.8rem;">
                        {{ $s->activated_at ? $s->activated_at->format('d M Y') : '-' }}
                    </td>
                    <td class="text-center">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border shadow-xs rounded-circle d-inline-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 32px; height: 32px;" title="Pilihan Aksi">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-1" style="border-radius: 12px; font-size: 0.85rem; min-width: 175px; box-shadow: 0 10px 25px rgba(0,0,0,0.12) !important;">
                                @if(!$s->is_active)
                                    <li>
                                        <form action="{{ route('admin.manajemen-akun.activate', $s) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2 text-success">
                                                <i class="bi bi-unlock text-success"></i>
                                                <span>Aktifkan Akun</span>
                                            </button>
                                        </form>
                                    </li>
                                @else
                                    <li>
                                        <form action="{{ route('admin.manajemen-akun.deactivate', $s) }}" method="POST" class="d-inline"
                                              data-confirm="Nonaktifkan akun {{ $s->name }}?" data-confirm-title="Nonaktifkan Akun?" data-confirm-btn="Ya, Nonaktifkan" data-confirm-type="danger">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2 text-danger">
                                                <i class="bi bi-lock text-danger"></i>
                                                <span>Nonaktifkan Akun</span>
                                            </button>
                                        </form>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                        Tidak ada data siswa.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($siswa->hasPages())
    <div class="p-3 border-top">
        {{ $siswa->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection