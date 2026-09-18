@extends('layouts.admin')
@section('title', 'Detail Jurusan - ' . $jurusan->nama_jurusan)

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <div class="page-label">MANAJEMEN DATA</div>
        <h1 class="page-title">{{ $jurusan->nama_jurusan }} ({{ $jurusan->kode_jurusan }})</h1>
        <p class="page-subtitle mb-0">Rincian master jurusan, data rombel kelas, serta daftar seluruh siswa yang terdaftar.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.jurusan.index') }}" class="btn btn-outline-custom">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Data Jurusan
        </a>
        <a href="{{ route('admin.jurusan.edit', $jurusan) }}" class="btn btn-primary-custom">
            <i class="bi bi-pencil me-1"></i> Edit Jurusan
        </a>
    </div>
</div>

{{-- STATISTIK JURUSAN --}}
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card-custom p-4 d-flex align-items-center gap-3">
            <img src="{{ $jurusan->logo_url }}" alt="{{ $jurusan->nama_jurusan }}" 
                 class="rounded shadow-sm border" style="width: 64px; height: 64px; object-fit: cover;">
            <div>
                <span class="badge bg-primary text-uppercase font-mono mb-1">{{ $jurusan->kode_jurusan }}</span>
                <h5 class="fw-bold mb-0 text-dark">{{ $jurusan->nama_jurusan }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom p-4 d-flex align-items-center justify-content-between">
            <div>
                <div class="text-muted small font-mono">TOTAL KELAS</div>
                <div class="fw-bold fs-3 text-primary">{{ $jurusan->kelas_count }} <span class="fs-6 text-muted fw-normal">Rombel</span></div>
            </div>
            <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary fs-3">
                <i class="bi bi-diagram-3-fill"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-custom p-4 d-flex align-items-center justify-content-between">
            <div>
                <div class="text-muted small font-mono">TOTAL SISWA TERHUBUNG</div>
                <div class="fw-bold fs-3 text-success">{{ $jurusan->siswa_count }} <span class="fs-6 text-muted fw-normal">Siswa</span></div>
            </div>
            <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success fs-3">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- DAFTAR KELAS DALAM JURUSAN --}}
    <div class="col-lg-5">
        <div class="card-custom p-4 h-100">
            <h5 class="fw-bold mb-3 d-flex align-items-center">
                <i class="bi bi-building me-2 text-primary"></i>Daftar Rombel Kelas
            </h5>
            <p class="text-muted small mb-3">Kelas yang terdaftar di bawah jurusan <strong>{{ $jurusan->kode_jurusan }}</strong>.</p>
            
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>NAMA KELAS</th>
                            <th class="text-center">TINGKAT</th>
                            <th class="text-end">JUMLAH SISWA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kelas as $k)
                        <tr>
                            <td>
                                <strong class="text-dark">{{ $k->nama_kelas }} Kelas {{ $k->tingkat }}</strong>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border">Kelas {{ $k->tingkat }}</span>
                            </td>
                            <td class="text-end">
                                <span class="badge bg-primary-subtle text-primary fw-bold">{{ $k->siswa_count }} Siswa</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">Belum ada data rombel kelas untuk jurusan ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- DAFTAR SISWA DALAM JURUSAN --}}
    <div class="col-lg-7">
        <div class="card-custom p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 d-flex align-items-center">
                    <i class="bi bi-mortarboard-fill me-2 text-success"></i>Daftar Siswa Jurusan
                </h5>
                <span class="badge bg-light text-dark border">{{ $jurusan->siswa_count }} Terdaftar</span>
            </div>
            <p class="text-muted small mb-3">Siswa aktif yang terhubung dengan jurusan <strong>{{ $jurusan->nama_jurusan }}</strong>.</p>

            <div class="table-responsive">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr>
                            <th>NIS</th>
                            <th>NAMA SISWA</th>
                            <th>KELAS BERAPA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswa as $s)
                        <tr>
                            <td class="font-mono text-primary fw-bold">{{ $s->nis }}</td>
                            <td>
                                <div class="fw-bold">{{ $s->name }}</div>
                                <small class="text-muted">{{ $s->email }}</small>
                            </td>
                            <td>
                                @php 
                                    $kelasRel = $s->relationLoaded('kelas') ? $s->getRelation('kelas') : null;
                                    $k = ($kelasRel && $kelasRel->isNotEmpty()) ? $kelasRel->first() : null;
                                @endphp
                                @if($k)
                                    <span class="badge bg-light text-dark border fw-bold">{{ $k->nama_kelas }} Kelas {{ $k->tingkat }}</span>
                                @elseif(is_string($s->kelas) && $s->kelas)
                                    <span class="badge bg-light text-dark border fw-bold">{{ $s->kelas }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">Belum ada siswa yang terhubung dengan jurusan ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($siswa->hasPages())
                <div class="mt-3">
                    {{ $siswa->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
