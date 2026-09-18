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
        <a href="{{ route('admin.sipintu.siswa') }}" class="btn btn-outline-success">
            <i class="bi bi-cloud-arrow-down me-1"></i> Tarik dari SiPintu
        </a>
        <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary-custom">
            <i class="bi bi-plus-circle me-1"></i> Tambah Siswa
        </a>
    </div>
</div>

{{-- FILTER BERDASARKAN KELAS --}}
<div class="card-custom p-3 mb-4">
    <form method="GET" action="{{ route('admin.siswa.index') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-bold text-muted">Filter Kelas</label>
            <select name="kelas" class="form-select" style="border-radius: 8px;" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                @foreach($kelasList as $k)
                    <option value="{{ $k->id }}" {{ request('kelas') == $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kelas }} Kelas {{ $k->tingkat }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.siswa.index') }}" class="btn btn-outline-custom">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
            </a>
        </div>
        <div class="col-md-4 text-end">
            <span class="badge bg-primary px-3 py-2">
                Total: {{ $siswa->count() }} siswa
            </span>
        </div>
    </form>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom mb-0" id="siswaTable">
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
                            <img src="{{ $s->photo_url }}" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
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
                            <span class="badge bg-success">Aktif</span>
                        @elseif($s->deactivation_type === 'berkala')
                            <span class="badge bg-warning text-dark font-mono" style="font-size: 0.72rem;">
                                <i class="bi bi-clock-history me-1"></i>Berkala s/d {{ $s->deactivated_until?->format('d/m/Y') }}
                            </span>
                            @if($s->deactivated_reason)
                                <div class="text-danger small mt-1 font-italic" style="font-size: 0.72rem;" title="{{ $s->deactivated_reason }}">
                                    <i class="bi bi-info-circle me-1"></i>{{ Str::limit($s->deactivated_reason, 26) }}
                                </div>
                            @endif
                        @else
                            <span class="badge bg-danger font-mono" style="font-size: 0.72rem;">
                                <i class="bi bi-slash-circle me-1"></i>Permanen
                            </span>
                            @if($s->deactivated_reason)
                                <div class="text-danger small mt-1 font-italic" style="font-size: 0.72rem;" title="{{ $s->deactivated_reason }}">
                                    <i class="bi bi-info-circle me-1"></i>{{ Str::limit($s->deactivated_reason, 26) }}
                                </div>
                            @endif
                        @endif
                        
                        @if(isset($s->warning_count) && $s->warning_count > 0)
                            <span class="badge bg-danger ms-1">
                                <i class="bi bi-exclamation-triangle-fill"></i> {{ $s->warning_count }} Warning
                            </span>
                        @endif
                    </td>

                    <td class="text-center">
                        @if($s->is_active)
                            <button type="button" class="btn btn-sm btn-outline-warning mb-1" onclick="openDeactivateModal('{{ $s->id }}', '{{ addslashes($s->name) }}')" title="Nonaktifkan Akun Siswa">
                                <i class="bi bi-lock"></i>
                            </button>
                        @else
                            <form action="{{ route('admin.siswa.toggle', $s) }}" method="POST" class="d-inline"
                                  data-confirm="Aktifkan kembali akun siswa {{ addslashes($s->name) }}?"
                                  data-confirm-title="Aktifkan Akun Siswa"
                                  data-confirm-btn="Aktifkan"
                                  data-confirm-type="question">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-outline-success mb-1" title="Aktifkan Akun">
                                    <i class="bi bi-unlock"></i>
                                </button>
                            </form>
                        @endif

                        <button type="button" class="btn btn-sm btn-outline-secondary mb-1" onclick="openResetPasswordModal('{{ $s->id }}', '{{ addslashes($s->name) }}', '{{ $s->nis }}')" title="Reset Password Akun">
                            <i class="bi bi-key"></i>
                        </button>
                        
                        <a href="{{ route('admin.siswa.edit', $s) }}" class="btn btn-sm btn-outline-primary mb-1" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.siswa.destroy', $s) }}" method="POST" class="d-inline"
                              data-confirm="Yakin ingin menghapus siswa {{ addslashes($s->name) }} (NIS: {{ $s->nis }})? Data yang dihapus tidak dapat dipulihkan."
                              data-confirm-title="Hapus Data Siswa"
                              data-confirm-btn="Ya, Hapus"
                              data-confirm-type="danger">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger mb-1" title="Hapus Siswa">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
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
                                    <span class="d-block text-muted fw-normal" style="font-size: 0.73rem;">Akun dinonaktifkan tanpa batas waktu. Siswa wajib menghubungi Admin Operator Sekolah untuk membuka akun.</span>
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
                        <textarea name="deactivated_reason" class="form-control rounded-3" rows="2" required placeholder="Contoh: Terdeteksi mengirimkan ulasan berulang dengan bahasa tidak pantas / melanggar tata tertib.">Akun dinonaktifkan oleh Admin Operator Sekolah karena pelanggaran tata tertib ulasan.</textarea>
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


{{-- MODAL RESET PASSWORD SISWA --}}
<div class="modal fade" id="modalResetPasswordSiswa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formResetPasswordSiswa" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fs-6 fw-bold"><i class="bi bi-key-fill me-2"></i>Reset Password Akun Siswa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="p-2 rounded bg-light border mb-3">
                        <span class="small text-muted">Siswa: </span><strong class="small text-dark" id="resetPasswordSiswaName">Nama Siswa</strong>
                        <br><span class="small text-muted">NIS: </span><span class="small font-mono fw-bold" id="resetPasswordSiswaNis">NIS</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Password Baru</label>
                        <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" required placeholder="Ulangi password baru">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom btn-sm px-3 fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openDeactivateModal(id, name) {
    document.getElementById('formDeactivateSiswa').action = '/admin/siswa/' + id + '/toggle';
    document.getElementById('deactivateSiswaName').innerText = name;
    new bootstrap.Modal(document.getElementById('modalDeactivateSiswa')).show();
}

function openResetPasswordModal(id, name, nis) {
    document.getElementById('formResetPasswordSiswa').action = '/admin/siswa/' + id + '/reset-password';
    document.getElementById('resetPasswordSiswaName').innerText = name;
    document.getElementById('resetPasswordSiswaNis').innerText = nis;
    new bootstrap.Modal(document.getElementById('modalResetPasswordSiswa')).show();
}

$(document).ready(function() {
    $('#siswaTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
        searching: true,
        pageLength: 10
    });
});
</script>
@endpush