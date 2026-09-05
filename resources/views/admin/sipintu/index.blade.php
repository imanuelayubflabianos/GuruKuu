@extends('layouts.admin')
@section('title', 'SiPintu Gateway')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <div class="page-label">INTEGRASI GATEWAY</div>
        <h1 class="page-title">SiPintu Identity & API Gateway</h1>
        <p class="page-subtitle">Pusat pemantauan koneksi dan integrasi data Guru & Siswa dengan SiPintu Gateway.</p>
    </div>
    <div class="d-flex gap-2">
        <button id="btnCheckConnection" class="btn btn-outline-custom">
            <i class="bi bi-arrow-repeat me-1"></i> Uji Koneksi Live
        </button>
        <a href="{{ route('admin.sipintu.guru') }}" class="btn btn-primary-custom">
            <i class="bi bi-person-video3 me-1"></i> Data Guru
        </a>
        <a href="{{ route('admin.sipintu.siswa') }}" class="btn btn-primary-custom">
            <i class="bi bi-people me-1"></i> Data Siswa
        </a>
    </div>
</div>

{{-- ALERT JIKA OFFLINE --}}
@if(!$pingResult['success'])
<div class="alert alert-warning border-0 shadow-sm d-flex align-items-center gap-3 mb-4" role="alert" style="border-radius: 12px; background: #fff8e6; border-left: 5px solid #f59e0b !important;">
    <i class="bi bi-exclamation-triangle-fill text-warning fs-3"></i>
    <div>
        <h6 class="mb-1 fw-bold text-dark">SiPintu Gateway Belum Terhubung</h6>
        <p class="mb-0 text-muted small">
            Aplikasi tidak dapat menghubungi Gateway di <code>{{ $stats['gateway_url'] }}</code>.
            Pastikan server SiPintu sudah berjalan (misal: <code>php artisan serve --port=8000</code> di folder SiPintu).
        </p>
    </div>
</div>
@endif

{{-- STATUS STAT CARDS --}}
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="stat-card-label">Status Gateway</span>
                <span class="badge {{ $pingResult['success'] ? 'bg-success' : 'bg-danger' }}">
                    <i class="bi {{ $pingResult['success'] ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} me-1"></i>
                    {{ $pingResult['success'] ? 'ONLINE' : 'OFFLINE' }}
                </span>
            </div>
            <div class="stat-card-value text-{{ $pingResult['success'] ? 'success' : 'danger' }}" style="font-size: 1.5rem;">
                {{ $pingResult['success'] ? 'Terhubung' : 'Terputus' }}
            </div>
            <small class="text-muted">
                Latency: <strong>{{ $pingResult['latency_ms'] ?? 0 }} ms</strong>
            </small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="stat-card-label">Validasi Kredensial</span>
                <span class="badge {{ ($validationResult['success'] ?? false) ? 'bg-primary' : 'bg-secondary' }}">
                    Auth Header
                </span>
            </div>
            <div class="stat-card-value" style="font-size: 1.4rem;">
                @if(isset($validationResult['success']) && $validationResult['success'])
                    <span class="text-primary"><i class="bi bi-shield-check"></i> Valid</span>
                @elseif($pingResult['success'])
                    <span class="text-warning"><i class="bi bi-shield-exclamation"></i> Menunggu</span>
                @else
                    <span class="text-muted"><i class="bi bi-dash-circle"></i> Tidak Aktif</span>
                @endif
            </div>
            <small class="text-muted">Client ID: <code>{{ substr($stats['client_id'], 0, 8) }}...</code></small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="stat-card-label">Data Guru Lokal</span>
                <i class="bi bi-person-badge text-primary fs-5"></i>
            </div>
            <div class="stat-card-value">{{ $stats['local_guru_count'] }}</div>
            <small class="text-muted"><a href="{{ route('admin.guru.index') }}" class="text-decoration-none">Kelola di GuruKuu &rarr;</a></small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="stat-card-label">Data Siswa Lokal</span>
                <i class="bi bi-mortarboard text-success fs-5"></i>
            </div>
            <div class="stat-card-value">{{ $stats['local_siswa_count'] }}</div>
            <small class="text-muted"><a href="{{ route('admin.siswa.index') }}" class="text-decoration-none">Kelola di GuruKuu &rarr;</a></small>
        </div>
    </div>
</div>

{{-- GATEWAY CONFIGURATION & QUICK ACTIONS --}}
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card-custom p-4 h-100">
            <div class="d-flex align-items-center gap-2 mb-3">
                <div class="bg-primary text-white rounded p-2" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-gear-fill"></i>
                </div>
                <h5 class="mb-0 fw-bold">Konfigurasi Gateway SiPintu</h5>
            </div>

            <table class="table table-sm table-borderless">
                <tbody>
                    <tr>
                        <th class="text-muted" style="width: 35%;">Base URL</th>
                        <td><code>{{ $stats['gateway_url'] }}</code></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Client ID</th>
                        <td><span class="badge bg-light text-dark font-mono border">{{ $stats['client_id'] }}</span></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Auth Header</th>
                        <td><code>X-Client-ID</code> & <code>X-Client-Secret</code></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Endpoint Guru</th>
                        <td><code>GET /api/v1/sijuna/teachers</code></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Endpoint Siswa</th>
                        <td><code>GET /api/v1/sijuna/students</code></td>
                    </tr>
                    <tr>
                        <th class="text-muted">Heartbeat Ping</th>
                        <td><code>GET /api/v1/ping?client_id=...</code></td>
                    </tr>
                </tbody>
            </table>

            <div class="mt-3 p-3 bg-light rounded" style="font-size: 0.85rem;">
                <i class="bi bi-info-circle text-primary me-1"></i>
                Kredensial disimpan secara aman di file <code>.env</code> aplikasi. Anda dapat mengubah <code>SIPINTU_BASE_URL</code> jika gateway berpindah host atau port.
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card-custom p-4 h-100">
            <div class="d-flex align-items-center gap-2 mb-3">
                <div class="bg-success text-white rounded p-2" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-lightning-charge-fill"></i>
                </div>
                <h5 class="mb-0 fw-bold">Akses Cepat Data SiPintu</h5>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <div class="p-3 border rounded h-100 bg-white shadow-sm hover-elevate">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-bold mb-0 text-dark">Data Guru</h6>
                            <i class="bi bi-person-lines-fill text-primary fs-4"></i>
                        </div>
                        <p class="text-muted small mb-3">Tarik dan telusuri daftar seluruh guru terdaftar di SiPintu beserta NIP & keahlian.</p>
                        <a href="{{ route('admin.sipintu.guru') }}" class="btn btn-sm btn-primary-custom w-100">
                            Buka Data Guru &rarr;
                        </a>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="p-3 border rounded h-100 bg-white shadow-sm hover-elevate">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-bold mb-0 text-dark">Data Siswa</h6>
                            <i class="bi bi-mortarboard-fill text-success fs-4"></i>
                        </div>
                        <p class="text-muted small mb-3">Tarik dan telusuri daftar seluruh siswa terdaftar di SiPintu beserta NIS & kelas.</p>
                        <a href="{{ route('admin.sipintu.siswa') }}" class="btn btn-sm btn-outline-custom w-100">
                            Buka Data Siswa &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-top pt-3">
                <h6 class="fw-bold small text-uppercase text-muted mb-2">Artisan CLI Command</h6>
                <div class="bg-dark text-white p-2 rounded font-mono" style="font-size: 0.85rem;">
                    <code>php artisan sipintu:fetch all --sync</code>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('btnCheckConnection').addEventListener('click', function() {
    const btn = this;
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menguji...';
    btn.disabled = true;

    fetch("{{ route('admin.sipintu.check-connection') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
        if (data.ping && data.ping.success) {
            alert(`✔ Koneksi Berhasil!\n\nStatus: ONLINE\nLatency: ${data.ping.latency_ms} ms\nPesan: ${data.ping.message}`);
            location.reload();
        } else {
            alert(`✖ Gagal Terhubung!\n\nPesan: ${data.ping ? data.ping.message : 'Gateway tidak merespons'}`);
        }
    })
    .catch(err => {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
        alert('✖ Terjadi kesalahan saat menguji koneksi: ' + err.message);
    });
});
</script>
@endpush
