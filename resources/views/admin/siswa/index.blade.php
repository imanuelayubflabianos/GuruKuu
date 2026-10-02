@extends('layouts.admin')
@section('title', 'Data Siswa')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <div class="page-label">MANAJEMEN DATA</div>
        <h1 class="page-title">Data Siswa</h1>
        <p class="page-subtitle">Kelola data siswa dan filter berdasarkan kelas.</p>
    </div>
    <div class="d-flex gap-2">
        <div class="dropdown">
            <button class="btn btn-outline-custom dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-download me-1"></i> Export Siswa
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li>
                    <a class="dropdown-item" href="{{ route('admin.export.siswa.excel') }}">
                        <i class="bi bi-file-earmark-excel text-success me-2"></i> Export Excel (.xlsx)
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.export.siswa.pdf') }}">
                        <i class="bi bi-file-earmark-pdf text-danger me-2"></i> Export PDF (.pdf)
                    </a>
                </li>
            </ul>
        </div>
        <div class="btn-group shadow-sm">
            <form action="{{ route('admin.sipintu.siswa.sync-all') }}" method="POST" id="formSyncSiswa" class="d-inline" onsubmit="return confirmSyncSiswa(event)">
                @csrf
                <button type="submit" class="btn btn-success" id="btnSyncSiswa" title="Tarik dan sinkronkan seluruh data siswa dari SiPintu ke GuruKuu" style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                    <i class="bi bi-cloud-arrow-down me-1"></i> Tarik dari SiPintu
                </button>
            </form>
            <a href="{{ route('admin.sipintu.siswa') }}" class="btn btn-outline-success" title="Buka Halaman Data Siswa SiPintu Gateway" style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-left: 0;">
                <i class="bi bi-box-arrow-up-right"></i>
            </a>
        </div>
    </div>
</div>

{{-- FILTER BERDASARKAN KELAS, STATUS, & PENCARIAN --}}
<div class="card-custom p-3 mb-4">
    <form method="GET" action="{{ route('admin.siswa.index') }}" class="row g-2 align-items-center">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama atau NIS siswa..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="kelas" class="form-select" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                @foreach($kelasList as $k)
                    <option value="{{ $k->id }}" {{ request('kelas') == $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kelas }} Kelas {{ $k->tingkat }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>✅ Aktif</option>
                <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>⛔ Dinonaktifkan</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2 justify-content-md-end">
            <button type="submit" class="btn btn-primary-custom px-3">
                <i class="bi bi-funnel me-1"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'kelas', 'status']))
                <a href="{{ route('admin.siswa.index') }}" class="btn btn-outline-custom" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<div class="card-custom">
    <div class="d-flex justify-content-between align-items-center px-3 px-md-4 py-3 border-bottom">
        <span class="small text-muted">Menampilkan {{ $siswa->firstItem() ?? 0 }}–{{ $siswa->lastItem() ?? 0 }} dari {{ $siswa->total() }} siswa</span>
        <span class="badge bg-light text-dark border font-mono">Total: {{ $siswa->total() }} Siswa</span>
    </div>
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>NIS</th>
                    <th>NAMA</th>
                    <th>KELAS</th>
                    <th>STATUS</th>
                    <th class="text-center">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswa as $s)
                <tr>
                    <td class="font-mono fw-bold">{{ $s->nis }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $s->photo_url }}" class="rounded-circle flex-shrink-0" style="width: 36px; height: 36px; min-width: 36px; min-height: 36px; aspect-ratio: 1 / 1; object-fit: cover; flex-shrink: 0;">
                            <div>
                                <strong>{{ $s->name }}</strong>
                                <br><small class="text-muted">{{ $s->email }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        @php
                            // Ambil relasi kelas secara paksa untuk menghindari bentrok dengan kolom string 'kelas'
                            $kelasRel = $s->getRelation('kelas');
                        @endphp
                        
                        @if($kelasRel && $kelasRel->isNotEmpty())
                            @foreach($kelasRel as $kelas)
                                <span class="badge bg-light text-dark border">
                                    {{ $kelas->nama_kelas }} Kelas {{ $kelas->tingkat }}
                                </span>
                            @endforeach
                        @elseif(is_string($s->kelas) && $s->kelas)
                            {{-- Fallback jika menggunakan kolom string biasa --}}
                            <span class="badge bg-light text-dark border">{{ $s->kelas }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        @if($s->is_active)
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-semibold">
                                <i class="bi bi-check-circle-fill me-1"></i>Aktif
                            </span>
                        @elseif($s->deactivation_type === 'berkala')
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 font-mono" style="font-size: 0.72rem;">
                                <i class="bi bi-clock-history me-1"></i>Nonaktif s/d {{ $s->deactivated_until?->format('d/m/Y') }}
                            </span>
                            @if($s->deactivated_reason)
                                <div class="text-danger small mt-1 font-italic" style="font-size: 0.72rem;" title="{{ $s->deactivated_reason }}">
                                    <i class="bi bi-info-circle me-1"></i>{{ Str::limit($s->deactivated_reason, 26) }}
                                </div>
                            @endif
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 font-mono" style="font-size: 0.72rem;">
                                <i class="bi bi-slash-circle me-1"></i>Dinonaktifkan
                            </span>
                            @if($s->deactivated_reason)
                                <div class="text-danger small mt-1 font-italic" style="font-size: 0.72rem;" title="{{ $s->deactivated_reason }}">
                                    <i class="bi bi-info-circle me-1"></i>{{ Str::limit($s->deactivated_reason, 26) }}
                                </div>
                            @endif
                        @endif
                        
                        @if(isset($s->warning_count) && $s->warning_count > 0)
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5 ms-1">
                                <i class="bi bi-exclamation-triangle-fill"></i> {{ $s->warning_count }}x Peringatan
                            </span>
                        @endif
                    </td>

                    <td class="text-center">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border shadow-xs rounded-circle d-inline-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 32px; height: 32px;" title="Pilihan Aksi">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-1" style="border-radius: 12px; font-size: 0.85rem; min-width: 180px; box-shadow: 0 10px 25px rgba(0,0,0,0.12) !important;">
                                <li>
                                    <a class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2" href="{{ route('admin.siswa.show', $s) }}">
                                        <i class="bi bi-eye text-info"></i>
                                        <span>Lihat Detail</span>
                                    </a>
                                </li>
                                @if($s->is_active)
                                    <li>
                                        <button type="button" class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2 text-warning-emphasis" onclick="openDeactivateModal('{{ $s->id }}', '{{ addslashes($s->name) }}')">
                                            <i class="bi bi-lock text-warning"></i>
                                            <span>Nonaktifkan Akun</span>
                                        </button>
                                    </li>
                                @else
                                    <li>
                                        <form action="{{ route('admin.siswa.toggle', $s) }}" method="POST" class="d-inline"
                                              data-confirm="Aktifkan kembali akun siswa {{ addslashes($s->name) }}?"
                                              data-confirm-title="Aktifkan Akun Siswa"
                                              data-confirm-btn="Aktifkan"
                                              data-confirm-type="question">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2 text-success">
                                                <i class="bi bi-unlock text-success"></i>
                                                <span>Aktifkan Akun</span>
                                            </button>
                                        </form>
                                    </li>
                                @endif
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <form action="{{ route('admin.siswa.destroy', $s) }}" method="POST" class="d-inline"
                                          data-confirm="Yakin ingin menghapus siswa {{ addslashes($s->name) }} (NIS: {{ $s->nis }})? Data yang dihapus tidak dapat dipulihkan."
                                          data-confirm-title="Hapus Data Siswa"
                                          data-confirm-btn="Ya, Hapus"
                                          data-confirm-type="danger">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2 text-danger">
                                            <i class="bi bi-trash text-danger"></i>
                                            <span>Hapus Siswa</span>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                        Belum ada data siswa.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($siswa->hasPages())
        <div class="px-3 px-md-4 py-3 border-top d-flex justify-content-center">{{ $siswa->links() }}</div>
    @endif
</div>

{{-- MODAL NONAKTIFKAN SISWA DENGAN PILIHAN PERMANEN / BERKALA --}}
<div class="modal fade" id="modalDeactivateSiswa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form id="formDeactivateSiswa" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header bg-danger text-white p-3">
                    <h5 class="modal-title fs-6 fw-bold"><i class="bi bi-shield-slash-fill me-2"></i>Nonaktifkan Akun Siswa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="small text-muted mb-3">
                        Pilih jenis penonaktifan akun untuk siswa berikut:
                    </p>
                    <div class="p-2.5 rounded-3 bg-light border mb-3">
                        <strong class="d-block text-dark" id="deactivateSiswaName">Nama Siswa</strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Tipe Penonaktifan:</label>
                        <div class="d-flex flex-column gap-2">
                            <div class="form-check p-2 rounded-3 border">
                                <input class="form-check-input ms-1 me-2" type="radio" name="deactivation_type" value="permanen" id="deactTypePermanen" checked onchange="toggleSiswaDeactDuration()">
                                <label class="form-check-label fw-semibold text-danger small" for="deactTypePermanen">
                                    <i class="bi bi-slash-circle me-1"></i> Nonaktifkan Permanen
                                    <span class="d-block text-muted fw-normal" style="font-size: 0.73rem;">Akun dinonaktifkan tanpa batas waktu. Siswa wajib menghubungi Admin untuk membuka akun.</span>
                                </label>
                            </div>

                            <div class="form-check p-2 rounded-3 border">
                                <input class="form-check-input ms-1 me-2" type="radio" name="deactivation_type" value="berkala" id="deactTypeBerkala" onchange="toggleSiswaDeactDuration()">
                                <label class="form-check-label fw-semibold text-warning small" for="deactTypeBerkala">
                                    <i class="bi bi-clock-history me-1"></i> Nonaktifkan Berkala (Sementara)
                                    <span class="d-block text-muted fw-normal" style="font-size: 0.73rem;">Siswa disuspen selama durasi tertentu, lalu otomatis aktif kembali saat durasi berakhir.</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="siswaDeactDurationBox" class="p-3 rounded-3 bg-light border mb-3 d-none">
                        <label class="form-label small fw-bold text-dark mb-1">Pilih Durasi Suspensi:</label>
                        <select name="duration_days" class="form-select form-select-sm rounded-3 mb-2">
                            <option value="1">1 Hari (24 Jam)</option>
                            <option value="3" selected>3 Hari</option>
                            <option value="7">7 Hari (1 Minggu)</option>
                            <option value="14">14 Hari (2 Minggu)</option>
                            <option value="30">30 Hari (1 Bulan)</option>
                        </select>
                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-muted" style="font-size: 0.75rem;">Atau hingga tanggal:</span>
                            <input type="date" name="custom_until" class="form-control form-control-sm rounded-3 w-auto" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-bold text-dark">Alasan Penonaktifan Akun:</label>
                        <textarea name="deactivated_reason" class="form-control rounded-3" rows="2" required placeholder="Contoh: Terdeteksi mengirimkan ulasan berulang dengan bahasa tidak pantas / melanggar tata tertib.">Akun dinonaktifkan oleh Admin karena pelanggaran tata tertib ulasan.</textarea>
                        <div class="form-text small text-muted" style="font-size: 0.72rem;">Alasan ini akan ditampilkan di portal saat siswa mencoba login.</div>
                    </div>
                </div>
                <div class="modal-footer bg-light p-2.5">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3.5 fw-semibold shadow-sm">
                        <i class="bi bi-lock me-1"></i> Konfirmasi Nonaktifkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleSiswaDeactDuration() {
        const isBerkala = document.getElementById('deactTypeBerkala').checked;
        const box = document.getElementById('siswaDeactDurationBox');
        if (isBerkala) {
            box.classList.remove('d-none');
        } else {
            box.classList.add('d-none');
        }
    }
</script>


@endsection

@push('scripts')
<script>
function openDeactivateModal(id, name) {
    document.getElementById('formDeactivateSiswa').action = '/admin/siswa/' + id + '/toggle';
    document.getElementById('deactivateSiswaName').innerText = name;
    new bootstrap.Modal(document.getElementById('modalDeactivateSiswa')).show();
}

function confirmSyncSiswa(e) {
    if (!confirm('Tarik dan sinkronkan seluruh data siswa aktif dari SiPintu Gateway ke database lokal GuruKuu sekarang?')) {
        e.preventDefault();
        return false;
    }
    const btn = document.getElementById('btnSyncSiswa');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menarik Data...';
    btn.classList.add('disabled');
    return true;
}

</script>
@endpush
