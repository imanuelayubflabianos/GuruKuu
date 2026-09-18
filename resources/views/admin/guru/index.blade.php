@extends('layouts.admin')
@section('title', 'Data Guru')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <div class="page-label">MANAJEMEN DATA</div>
        <h1 class="page-title">Data Guru</h1>
        <p class="page-subtitle">Kelola master data guru pengajar SMK Negeri 1 Bangsri.</p>
    </div>
    <div class="d-flex gap-2">
        <div class="dropdown">
            <button class="btn btn-outline-custom dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-download me-1"></i> Export Data
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li>
                    <a class="dropdown-item" href="{{ route('admin.export.guru.excel') }}">
                        <i class="bi bi-file-earmark-excel text-success me-2"></i> Export Excel (.xlsx)
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.export.guru.pdf') }}">
                        <i class="bi bi-file-earmark-pdf text-danger me-2"></i> Export PDF (.pdf)
                    </a>
                </li>
            </ul>
        </div>
        <a href="{{ route('admin.sipintu.guru') }}" class="btn btn-outline-primary">
            <i class="bi bi-cloud-arrow-down me-1"></i> Tarik dari SiPintu
        </a>
        <a href="{{ route('admin.guru.create') }}" class="btn btn-primary-custom">
            <i class="bi bi-plus-circle me-1"></i> Tambah Guru
        </a>
    </div>
</div>

<div class="card-custom p-3 mb-4">
    <form method="GET" action="{{ route('admin.guru.index') }}" class="row g-3 align-items-end">
        <div class="col-md-8">
            <label class="form-label small fw-bold text-muted">Filter Jurusan</label>
            <select name="jurusan_id" class="form-select" style="border-radius: 8px;">
                <option value="">Semua Jurusan</option>
                @foreach($jurusans as $j)
                    <option value="{{ $j->id }}" {{ request('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama_jurusan }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary-custom flex-grow-1"><i class="bi bi-funnel me-1"></i> Filter</button>
            <a href="{{ route('admin.guru.index') }}" class="btn btn-outline-custom"><i class="bi bi-arrow-counterclockwise"></i></a>
        </div>
    </form>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom mb-0" id="guruTable">
            <thead>
                <tr>
                    <th>NIP</th>
                    <th>NAMA GURU</th>
                    <th>EMAIL</th>
                    <th>KONTAK / HP</th>
                    <th>JURUSAN</th>
                    <th>KEPUASAN (RATING)</th>
                    <th>STATUS AKUN</th>
                    <th class="text-center">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($guru as $g)
                @php $u = $g->linked_user; @endphp
                <tr>
                    <td class="font-mono fw-bold text-primary">{{ $g->nip }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $g->photo_url }}" class="rounded-circle border" style="width: 38px; height: 38px; object-fit: cover;">
                            <div>
                                <strong>{{ $g->nama }}</strong>
                                @if($u && $u->warning_count > 0)
                                    <span class="badge bg-warning text-dark font-mono ms-1" style="font-size: 0.68rem;">⚠️ {{ $u->warning_count }}x Pelanggaran</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($g->email)
                            <span class="text-dark small"><i class="bi bi-envelope text-primary me-1"></i>{{ $g->email }}</span>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td>
                        @if($g->phone)
                            <span class="text-dark small"><i class="bi bi-telephone text-success me-1"></i>{{ $g->phone }}</span>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td>{{ $g->jurusan->nama_jurusan ?? '-' }}</td>
                    <td>
                        @php $pct = round(($g->rata_rata_nilai / 5) * 100); @endphp
                        <div class="fw-bold font-mono text-primary" style="font-size: 0.85rem;">{{ $pct }}%</div>
                        <div class="progress" style="height: 5px; width: 75px; border-radius: 10px;">
                            <div class="progress-bar bg-primary rounded-pill" style="width: {{ $pct }}%;"></div>
                        </div>
                        <small class="text-muted d-block mt-1 font-mono" style="font-size: 0.7rem;">{{ $g->total_penilaian }} ulasan</small>
                    </td>
                    <td>
                        @if($u)
                            @if($u->is_active)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                    <i class="bi bi-check-circle-fill me-1"></i>Aktif
                                </span>
                            @elseif($u->deactivation_type === 'berkala')
                                <span class="badge bg-warning-subtle text-dark border border-warning px-2 py-1" title="Nonaktif s/d {{ $u->deactivated_until?->format('d M Y H:i') }}">
                                    <i class="bi bi-clock-history me-1"></i>Nonaktif Berkala
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                    <i class="bi bi-slash-circle me-1"></i>Nonaktif Permanen
                                </span>
                            @endif
                        @else
                            <span class="badge bg-secondary-subtle text-muted px-2 py-1">Belum Terdaftar</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.guru.show', $g) }}" class="btn btn-sm btn-outline-info me-1" title="Lihat Detail & Penilaian"><i class="bi bi-eye"></i></a>
                        
                        @if($u)
                            @if($u->is_active)
                                <button type="button" class="btn btn-sm btn-outline-warning me-1" onclick="openDeactivateGuruModal('{{ $g->id }}', '{{ addslashes($g->nama) }}')" title="Nonaktifkan Akun Guru">
                                    <i class="bi bi-lock"></i>
                                </button>
                            @else
                                <form action="{{ route('admin.guru.toggle', $g) }}" method="POST" class="d-inline"
                                      data-confirm="Aktifkan kembali akun guru {{ addslashes($g->nama) }}?"
                                      data-confirm-title="Aktifkan Akun Guru"
                                      data-confirm-btn="Aktifkan"
                                      data-confirm-type="question">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-success me-1" title="Aktifkan Akun Guru">
                                        <i class="bi bi-unlock"></i>
                                    </button>
                                </form>
                            @endif
                        @endif

                        <button type="button" class="btn btn-sm btn-outline-secondary me-1" onclick="openResetPasswordGuruModal('{{ $g->id }}', '{{ addslashes($g->nama) }}', '{{ $g->nip }}')" title="Reset Password Akun"><i class="bi bi-key"></i></button>
                        <a href="{{ route('admin.guru.edit', $g) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit Guru"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('admin.guru.destroy', $g) }}" method="POST" class="d-inline"
                              data-confirm="Yakin ingin menghapus data guru {{ addslashes($g->nama) }}? Tindakan ini akan menghapus data evaluasi guru terkait."
                              data-confirm-title="Hapus Data Guru"
                              data-confirm-btn="Ya, Hapus"
                              data-confirm-type="danger">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Hapus Guru"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">Belum ada data guru.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL NONAKTIFKAN GURU DENGAN PILIHAN PERMANEN / BERKALA --}}
<div class="modal fade" id="modalDeactivateGuru" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <form id="formDeactivateGuru" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header bg-danger text-white p-3">
                    <h5 class="modal-title fs-6 fw-bold"><i class="bi bi-shield-slash-fill me-2"></i>Nonaktifkan Akun Guru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="small text-muted mb-3">
                        Pilih jenis penonaktifan akun untuk guru berikut:
                    </p>
                    <div class="p-2.5 rounded-3 bg-light border mb-3">
                        <strong class="d-block text-dark" id="deactivateGuruName">Nama Guru</strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Tipe Penonaktifan:</label>
                        <div class="d-flex flex-column gap-2">
                            <div class="form-check p-2 rounded-3 border">
                                <input class="form-check-input ms-1 me-2" type="radio" name="deactivation_type" value="permanen" id="guruDeactTypePermanen" checked onchange="toggleGuruDeactDuration()">
                                <label class="form-check-label fw-semibold text-danger small" for="guruDeactTypePermanen">
                                    <i class="bi bi-slash-circle me-1"></i> Nonaktifkan Permanen
                                    <span class="d-block text-muted fw-normal" style="font-size: 0.73rem;">Akun guru dinonaktifkan tanpa batas waktu. Wajib menghubungi Admin Operator Sekolah untuk membuka akun.</span>
                                </label>
                            </div>

                            <div class="form-check p-2 rounded-3 border">
                                <input class="form-check-input ms-1 me-2" type="radio" name="deactivation_type" value="berkala" id="guruDeactTypeBerkala" onchange="toggleGuruDeactDuration()">
                                <label class="form-check-label fw-semibold text-warning small" for="guruDeactTypeBerkala">
                                    <i class="bi bi-clock-history me-1"></i> Nonaktifkan Berkala (Sementara)
                                    <span class="d-block text-muted fw-normal" style="font-size: 0.73rem;">Guru disuspen selama durasi tertentu, lalu otomatis aktif kembali saat durasi berakhir.</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="guruDeactDurationBox" class="p-3 rounded-3 bg-light border mb-3 d-none">
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
                        <textarea name="deactivated_reason" class="form-control rounded-3" rows="2" required placeholder="Contoh: Mengirimkan balasan yang melanggar etika tata tertib sekolah.">Akun dinonaktifkan oleh Admin Operator Sekolah karena pelanggaran tata tertib etika.</textarea>
                        <div class="form-text small text-muted" style="font-size: 0.72rem;">Alasan ini akan ditampilkan di portal saat guru mencoba login.</div>
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

{{-- MODAL RESET PASSWORD GURU --}}
<div class="modal fade" id="modalResetPasswordGuru" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formResetPasswordGuru" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fs-6 fw-bold"><i class="bi bi-key-fill me-2"></i>Reset Password Akun Guru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="p-2 rounded bg-light border mb-3">
                        <span class="small text-muted">Guru: </span><strong class="small text-dark" id="resetPasswordGuruName">Nama Guru</strong>
                        <br><span class="small text-muted">NIP: </span><span class="small font-mono fw-bold" id="resetPasswordGuruNip">NIP</span>
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
function openDeactivateGuruModal(id, name) {
    document.getElementById('formDeactivateGuru').action = '/admin/guru/' + id + '/toggle';
    document.getElementById('deactivateGuruName').innerText = name;
    new bootstrap.Modal(document.getElementById('modalDeactivateGuru')).show();
}

function toggleGuruDeactDuration() {
    const isBerkala = document.getElementById('guruDeactTypeBerkala').checked;
    const box = document.getElementById('guruDeactDurationBox');
    if (isBerkala) {
        box.classList.remove('d-none');
    } else {
        box.classList.add('d-none');
    }
}

function openResetPasswordGuruModal(id, name, nip) {
    document.getElementById('formResetPasswordGuru').action = '/admin/guru/' + id + '/reset-password';
    document.getElementById('resetPasswordGuruName').innerText = name;
    document.getElementById('resetPasswordGuruNip').innerText = nip;
    new bootstrap.Modal(document.getElementById('modalResetPasswordGuru')).show();
}

$(document).ready(function() {
    $('#guruTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
        ordering: false,
        pageLength: 10
    });
});
</script>
@endpush