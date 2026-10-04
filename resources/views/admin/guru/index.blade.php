@extends('layouts.admin')
@section('title', 'Data Guru')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <div class="page-label">MANAJEMEN DATA</div>
        <h1 class="page-title">Data Guru</h1>
        <p class="page-subtitle mb-0">Kelola master data guru pengajar SMK Negeri 1 Bangsri.</p>
    </div>
    <div class="d-flex gap-2 align-items-center flex-wrap">
        <button type="button" class="btn btn-outline-custom d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalImportJadwalGuru">
            <i class="bi bi-calendar2-range text-primary"></i>
            <span>Import Jadwal Kelas</span>
        </button>
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
        <div class="btn-group shadow-sm">
            <form action="{{ route('admin.sipintu.guru.sync-all') }}" method="POST" id="formSyncGuru" class="d-inline" data-confirm="Tarik dan sinkronkan seluruh data guru dari SiPintu Gateway ke database lokal GuruKuu sekarang?" data-confirm-title="Sinkronkan Data Guru?" data-confirm-btn="Ya, Sinkronkan" data-confirm-type="info">
                @csrf
                <button type="submit" class="btn btn-primary-custom" id="btnSyncGuru" title="Tarik dan sinkronkan seluruh data guru dari SiPintu ke GuruKuu" style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                    <i class="bi bi-cloud-arrow-down me-1"></i> Tarik dari SiPintu
                </button>
            </form>
            <a href="{{ route('admin.sipintu.guru') }}" class="btn btn-outline-primary" title="Buka Halaman Data Guru SiPintu Gateway" style="border-top-left-radius: 0; border-bottom-left-radius: 0; border-left: 0;">
                <i class="bi bi-box-arrow-up-right"></i>
            </a>
        </div>
    </div>
</div>

@if(session('import_details'))
    @php $details = session('import_details'); @endphp
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4 border-0" role="alert" style="border-radius: 12px; background: #e8f5e9; color: #1b5e20;">
        <div class="d-flex align-items-start gap-3">
            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center flex-shrink-0 mt-1" style="width: 36px; height: 36px;">
                <i class="bi bi-check-lg fs-5"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-1">Hasil Import Jadwal & Pembagian Kelas Guru</h6>
                <p class="small mb-2">
                    {{ $details['message'] ?? 'Proses import spreadsheet selesai.' }}
                </p>
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <span class="badge bg-success-subtle text-success border border-success">
                        <i class="bi bi-person-check me-1"></i> {{ $details['updated_count'] ?? 0 }} Guru Terpetakan
                    </span>
                    <span class="badge bg-primary-subtle text-primary border border-primary">
                        <i class="bi bi-building me-1"></i> {{ $details['total_classes_assigned'] ?? 0 }} Total Rombel Mengajar
                    </span>
                    @if(!empty($details['unmatched_teachers']))
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning">
                            <i class="bi bi-exclamation-triangle me-1"></i> {{ count($details['unmatched_teachers']) }} Guru di File Belum Terdaftar
                        </span>
                    @endif
                </div>

                @if(!empty($details['unmatched_teachers']))
                    <div class="small mt-2 p-2 bg-white rounded border">
                        <div class="fw-bold text-dark mb-1">Daftar nama guru di file yang belum terdaftar di GuruKuu:</div>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach(array_slice($details['unmatched_teachers'], 0, 8) as $um)
                                <span class="badge bg-light text-muted border font-mono">{{ $um['raw_name'] }} ({{ $um['classes_count'] }} kelas)</span>
                            @endforeach
                            @if(count($details['unmatched_teachers']) > 8)
                                <span class="text-muted small">...dan {{ count($details['unmatched_teachers']) - 8 }} lainnya</span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

<div class="card-custom p-3 mb-4">
    <form method="GET" action="{{ route('admin.guru.index') }}" class="row g-3 align-items-end">
        <div class="col-md-10">
            <label class="form-label small fw-bold text-muted">Filter Kelas yang Diajar</label>
            <select name="kelas_id" class="form-select" style="border-radius: 8px;" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                @foreach($kelasList as $kelas)
                    <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>{{ $kelas->label_singkat }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <a href="{{ route('admin.guru.index') }}" class="btn btn-outline-custom w-100" title="Reset filter" aria-label="Reset filter"><i class="bi bi-arrow-counterclockwise"></i></a>
        </div>
    </form>
</div>

<div class="card-custom">
    <div class="d-flex justify-content-between align-items-center px-3 px-md-4 py-3 border-bottom">
        <span class="small text-muted">Menampilkan {{ $guru->firstItem() ?? 0 }}–{{ $guru->lastItem() ?? 0 }} dari {{ $guru->total() }} guru</span>
        <span class="badge bg-light text-dark border">15 per halaman</span>
    </div>
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>NIP</th>
                    <th>NAMA GURU</th>
                    <th>EMAIL</th>
                    <th>KONTAK / HP</th>
                    <th>JURUSAN</th>
                    <th>KELAS DIAJAR</th>
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
                            <img src="{{ $g->photo_url }}" class="rounded-circle border flex-shrink-0" style="width: 38px; height: 38px; min-width: 38px; min-height: 38px; aspect-ratio: 1 / 1; object-fit: cover; flex-shrink: 0;">
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
                        @forelse($g->kelas as $kelas)
                            <span class="badge bg-light text-dark border mb-1">{{ $kelas->label_singkat }}</span>
                        @empty
                            <span class="text-muted small">Belum diatur</span>
                        @endforelse
                    </td>
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
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 fw-semibold">
                                    <i class="bi bi-check-circle-fill me-1"></i>Aktif
                                </span>
                            @elseif($u->deactivation_type === 'berkala')
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 font-mono" style="font-size: 0.72rem;" title="Nonaktif s/d {{ $u->deactivated_until?->format('d M Y H:i') }}">
                                    <i class="bi bi-clock-history me-1"></i>Nonaktif s/d {{ $u->deactivated_until?->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1 font-mono" style="font-size: 0.72rem;">
                                    <i class="bi bi-slash-circle me-1"></i>Dinonaktifkan
                                </span>
                            @endif
                        @else
                            <span class="badge bg-secondary-subtle text-muted rounded-pill px-2.5 py-1" style="font-size: 0.75rem;">Belum Terdaftar</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border shadow-xs rounded-circle d-inline-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 32px; height: 32px;" title="Pilihan Aksi">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-1" style="border-radius: 12px; font-size: 0.85rem; min-width: 180px; box-shadow: 0 10px 25px rgba(0,0,0,0.12) !important;">
                                <li>
                                    <a class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2" href="{{ route('admin.guru.show', $g) }}">
                                        <i class="bi bi-eye text-info"></i>
                                        <span>Lihat Detail</span>
                                    </a>
                                </li>
                                @if($u)
                                    @if($u->is_active)
                                        <li>
                                            <button type="button" class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2 text-warning-emphasis" onclick="openDeactivateGuruModal('{{ $g->id }}', '{{ addslashes($g->nama) }}')">
                                                <i class="bi bi-lock text-warning"></i>
                                                <span>Nonaktifkan Akun</span>
                                            </button>
                                        </li>
                                    @else
                                        <li>
                                            <form action="{{ route('admin.guru.toggle', $g) }}" method="POST" class="d-inline"
                                                  data-confirm="Aktifkan kembali akun guru {{ addslashes($g->nama) }}?"
                                                  data-confirm-title="Aktifkan Akun Guru"
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
                                @endif
                                <li>
                                    <a class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2 text-primary" href="{{ route('admin.guru.edit', $g) }}">
                                        <i class="bi bi-pencil text-primary"></i>
                                        <span>Edit Profil Guru</span>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <form action="{{ route('admin.guru.destroy', $g) }}" method="POST" class="d-inline"
                                          data-confirm="Yakin ingin menghapus data guru {{ addslashes($g->nama) }}? Tindakan ini akan menghapus data evaluasi guru terkait."
                                          data-confirm-title="Hapus Data Guru"
                                          data-confirm-btn="Ya, Hapus"
                                          data-confirm-type="danger">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2 text-danger">
                                            <i class="bi bi-trash text-danger"></i>
                                            <span>Hapus Data Guru</span>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-5 text-muted">Belum ada data guru.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($guru->hasPages())
        <div class="px-3 px-md-4 py-3 border-top d-flex justify-content-center">{{ $guru->links() }}</div>
    @endif
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
                                    <span class="d-block text-muted fw-normal" style="font-size: 0.73rem;">Akun guru dinonaktifkan tanpa batas waktu. Wajib menghubungi Admin untuk membuka akun.</span>
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
                        <textarea name="deactivated_reason" class="form-control rounded-3" rows="2" required placeholder="Contoh: Mengirimkan balasan yang melanggar etika tata tertib sekolah.">Akun dinonaktifkan oleh Admin karena pelanggaran tata tertib etika.</textarea>
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

{{-- MODAL IMPORT JADWAL & KELAS GURU --}}
<div class="modal fade" id="modalImportJadwalGuru" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('admin.guru.import-jadwal') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            @csrf
            <div class="modal-header border-bottom px-4 py-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(0, 51, 102, 0.08); color: var(--primary);">
                        <i class="bi bi-calendar2-check fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0">Import Jadwal & Kelas Mengajar</h5>
                        <small class="text-muted" style="font-size: 0.78rem;">Petakan rombel kelas mengajar guru otomatis dari dokumen SK / jadwal</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="p-3 mb-4 rounded-3 border" style="background: rgba(0, 51, 102, 0.03); border-color: rgba(0, 51, 102, 0.12) !important;">
                    <div class="d-flex gap-2.5 align-items-start">
                        <i class="bi bi-info-circle-fill text-primary fs-5 flex-shrink-0 mt-0.5"></i>
                        <div class="small">
                            <strong class="d-block text-dark mb-1">Mendukung Format Matrix SK & Tabel Jadwal Sekolah</strong>
                            Sistem secara cerdas mengenali format dokumen:
                            <ul class="mb-0 ps-3 mt-1 text-muted" style="line-height: 1.6;">
                                <li><strong>Matrix SK Jam Belajar:</strong> Baris nama guru dengan kolom pembagian jam per kelas (misal: <code>X TO 1</code>, <code>X PPLG 1</code>, <code>XI AKL 2</code>, dll. seperti template SK Kurikulum).</li>
                                <li><strong>Daftar Kolom Biasa:</strong> Format tabel dengan kolom <code>Nama Guru</code>, <code>Kelas</code> (dipisah koma), dan <code>Mata Pelajaran</code>.</li>
                                <li>Gelar akademik (seperti <em>S.Pd, M.Pd, S.T</em>) otomatis dibersihkan saat pencocokan ke nama guru di GuruKuu.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small">Pilih Dokumen File Jadwal / SK <span class="text-danger">*</span></label>
                    <div class="p-4 text-center rounded-3 border border-2 border-dashed bg-light" style="cursor: pointer;" onclick="document.getElementById('inputJadwalFile').click()">
                        <i class="bi bi-cloud-arrow-up text-primary fs-1 d-block mb-1"></i>
                        <span class="fw-semibold text-dark d-block" id="fileNameDisplay">Klik untuk memilih file dokumen jadwal</span>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">Mendukung format spreadsheet (.xlsx, .xls, .csv)</small>
                    </div>
                    <input type="file" name="file" id="inputJadwalFile" class="d-none" accept=".xlsx,.xls,.csv" required onchange="displaySelectedFileName(this)">
                </div>

                <div class="p-3 bg-light rounded-3 border">
                    <label class="form-label fw-bold text-dark small mb-2">Opsi Sinkronisasi:</label>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="replace_existing" value="1" id="chkReplaceExisting" checked>
                        <label class="form-check-label small fw-semibold text-dark" for="chkReplaceExisting">
                            Perbarui / timpa kelas mengajar sebelumnya
                            <span class="d-block text-muted fw-normal" style="font-size: 0.72rem;">Kelas mengajar guru akan disesuaikan dengan isi file terbaru ini.</span>
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="create_if_not_found" value="1" id="chkCreateIfNotFound">
                        <label class="form-check-label small fw-semibold text-dark" for="chkCreateIfNotFound">
                            Otomatis tambahkan guru baru jika belum ada di database GuruKuu
                            <span class="d-block text-muted fw-normal" style="font-size: 0.72rem;">Jika tidak dicentang, sistem hanya memetakan guru yang sudah terdaftar di GuruKuu.</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light px-4 py-3 border-top">
                <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary-custom px-4 shadow-sm" id="btnSubmitImportJadwal">
                    <i class="bi bi-cloud-arrow-up me-1"></i> Mulai Proses Import
                </button>
            </div>
        </form>
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

function confirmSyncGuru(e) {
    e.preventDefault();
    window.gurukuuConfirm({
        title: 'Sinkronkan Data Guru?',
        text: 'Tarik dan sinkronkan seluruh data guru dari SiPintu Gateway ke database lokal GuruKuu sekarang?',
        icon: 'question',
        confirmButtonText: 'Ya, Sinkronkan',
        type: 'info',
        onConfirm: function() {
            const btn = document.getElementById('btnSyncGuru');
            if (btn) {
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menarik Data...';
                btn.classList.add('disabled');
            }
            document.getElementById('formSyncGuru').submit();
        }
    });
    return false;
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

function displaySelectedFileName(input) {
    const display = document.getElementById('fileNameDisplay');
    if (input.files && input.files[0]) {
        display.innerHTML = '<span class="text-primary fw-bold"><i class="bi bi-file-earmark-check me-1"></i>' + input.files[0].name + '</span> <span class="text-muted small">(' + (input.files[0].size / 1024).toFixed(1) + ' KB)</span>';
    } else {
        display.innerText = 'Klik untuk memilih file dokumen jadwal';
    }
}
</script>
@endpush
