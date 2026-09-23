@extends('layouts.admin')
@section('title', 'Detail Siswa')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <div class="page-label">DATA LOKAL</div>
        <h1 class="page-title">Detail Siswa</h1>
        <p class="page-subtitle">Informasi lengkap akun, kelas, penilaian, dan log pelanggaran siswa.</p>
    </div>
    <a href="{{ route('admin.siswa.index') }}" class="btn btn-outline-custom"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <div class="card-custom p-4 h-100">
            <div class="text-center mb-4">
                <img src="{{ $siswa->photo_url }}" alt="{{ $siswa->name }}" class="rounded-circle border" style="width: 104px; height: 104px; object-fit: cover;">
                <h4 class="fw-bold mt-3 mb-1">{{ $siswa->name }}</h4>
                <span class="badge {{ $siswa->is_active ? 'bg-success' : 'bg-danger' }}">{{ $siswa->is_active ? 'Aktif' : 'Nonaktif' }}</span>
            </div>
            <dl class="row small mb-0">
                <dt class="col-5 text-muted">NIS</dt><dd class="col-7 font-mono">{{ $siswa->nis }}</dd>
                <dt class="col-5 text-muted">Email</dt><dd class="col-7 text-break">{{ $siswa->email ?: '-' }}</dd>
                <dt class="col-5 text-muted">Tanggal lahir</dt><dd class="col-7">{{ $siswa->tanggal_lahir?->format('d M Y') ?: '-' }}</dd>
                <dt class="col-5 text-muted">Jurusan</dt><dd class="col-7">{{ $siswa->jurusan?->nama_jurusan ?: '-' }}</dd>
                <dt class="col-5 text-muted">Peringatan</dt><dd class="col-7">{{ $siswa->warning_count }} kali</dd>
            </dl>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card-custom p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-door-open text-primary me-2"></i>Kelas Terhubung</h5>
            <div class="d-flex flex-wrap gap-2">
                @php
                    $kelasList = $siswa->kelasList ?? collect();
                @endphp
                @if($kelasList->isNotEmpty())
                    @foreach($kelasList as $kelas)
                        <span class="badge bg-light text-dark border px-3 py-2">{{ $kelas->label_singkat ?? ($kelas->nama_kelas . ' Kelas ' . $kelas->tingkat) }}</span>
                    @endforeach
                @elseif(is_string($siswa->kelas) && !empty($siswa->kelas))
                    <span class="badge bg-light text-dark border px-3 py-2">{{ $siswa->kelas }}</span>
                @else
                    <span class="text-muted">Belum ada kelas terhubung.</span>
                @endif
            </div>
            @if($siswa->deactivated_reason)
                <div class="alert alert-warning small mt-4 mb-0"><strong>Catatan status:</strong> {{ $siswa->deactivated_reason }}</div>
            @endif
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card-custom p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-clipboard-check text-primary me-2"></i>Riwayat Penilaian</h5>
            <div class="table-responsive">
                <table class="table table-custom align-middle mb-0">
                    <thead><tr><th>Guru</th><th>Periode</th><th>Nilai</th><th>Tanggal</th></tr></thead>
                    <tbody>
                        @forelse($penilaian as $item)
                            <tr>
                                <td class="fw-semibold">{{ $item->guru?->nama ?: '-' }}</td>
                                <td class="small">{{ $item->periode?->nama_periode ?: '-' }}<br><span class="text-muted">{{ $item->kelas?->label_singkat ?: '-' }}</span></td>
                                <td><span class="badge bg-primary">{{ $item->total_nilai }}/30</span></td>
                                <td class="small text-muted">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada penilaian.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card-custom p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-shield-exclamation text-danger me-2"></i>Log Pelanggaran</h5>
            @forelse($pelanggaran as $log)
                <div class="border-bottom pb-2 mb-2 small">
                    <div class="d-flex justify-content-between gap-2"><strong>{{ $log->tipe_label }}</strong><span class="text-muted">{{ $log->created_at->format('d/m/Y H:i') }}</span></div>
                    <div class="text-muted">{{ $log->tindakan }}</div>
                    @if($log->kata_terdeteksi)<div class="text-danger">{{ implode(', ', $log->kata_terdeteksi) }}</div>@endif
                </div>
            @empty
                <div class="text-muted small">Belum ada log pelanggaran.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
