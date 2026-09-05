@extends('layouts.admin')
@section('title', 'Data Siswa SiPintu')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <div class="page-label">GATEWAY SIPINTU</div>
        <h1 class="page-title">Data Siswa SiPintu</h1>
        <p class="page-subtitle">Daftar data siswa aktif yang diambil langsung dari SiPintu Gateway (SIJUNA).</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.sipintu.index') }}" class="btn btn-outline-custom">
            <i class="bi bi-speedometer2 me-1"></i> Dashboard Gateway
        </a>
        <a href="{{ route('admin.siswa.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-mortarboard me-1"></i> Data Siswa Lokal
        </a>
        @if($result['success'] && !empty($students))
        <form action="{{ route('admin.sipintu.siswa.sync-all') }}" method="POST" onsubmit="return confirm('Sinkronkan seluruh data siswa aktif dari SiPintu ke database GuruKuu?')">
            @csrf
            <button type="submit" class="btn btn-success">
                <i class="bi bi-cloud-arrow-down-fill me-1"></i> Sinkronkan Semua Siswa Aktif
            </button>
        </form>
        @endif
    </div>
</div>

{{-- STATUS GATEWAY NOTIFICATION --}}
@if(!$result['success'])
<div class="alert alert-warning border-0 shadow-sm d-flex align-items-center gap-3 mb-4" style="border-radius: 12px; background: #fff8e6; border-left: 5px solid #f59e0b !important;">
    <i class="bi bi-exclamation-triangle-fill text-warning fs-3"></i>
    <div class="flex-grow-1">
        <h6 class="mb-1 fw-bold text-dark">Gagal Terhubung ke Gateway SiPintu</h6>
        <p class="mb-0 text-muted small">{{ $result['message'] }}</p>
    </div>
    <a href="{{ route('admin.sipintu.siswa', ['refresh' => 1]) }}" class="btn btn-sm btn-outline-warning">
        <i class="bi bi-arrow-clockwise me-1"></i> Coba Lagi
    </a>
</div>
@endif

{{-- FILTER & SEARCH BOX --}}
<div class="card-custom p-3 mb-4">
    <form method="GET" action="{{ route('admin.sipintu.siswa') }}" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label small fw-bold text-muted">Cari Nama / NIS / Email</label>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Kata kunci...">
            </div>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-bold text-muted">Filter NIS</label>
            <input type="text" name="nis" value="{{ request('nis') }}" class="form-control" placeholder="NIS siswa...">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold text-muted">Status Siswa</label>
            <select name="only_active" class="form-select" onchange="this.form.submit()">
                <option value="1" {{ ($onlyActive ?? true) ? 'selected' : '' }}>Hanya Siswa Aktif (Punya Kelas)</option>
                <option value="0" {{ !($onlyActive ?? true) ? 'selected' : '' }}>Semua Siswa (Termasuk Alumni/Nonaktif)</option>
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary-custom flex-grow-1">
                <i class="bi bi-funnel me-1"></i> Cari
            </button>
            <a href="{{ route('admin.sipintu.siswa', ['refresh' => 1]) }}" class="btn btn-outline-success" title="Segarkan Data dari Server SiPintu">
                <i class="bi bi-arrow-repeat"></i> Segarkan
            </a>
            <a href="{{ route('admin.sipintu.siswa') }}" class="btn btn-outline-custom" title="Reset Filter">
                <i class="bi bi-arrow-counterclockwise"></i>
            </a>
            <span class="badge bg-light text-dark border align-self-center px-3 py-2">
                Total: <strong>{{ count($students) }}</strong> siswa
            </span>
        </div>
    </form>
</div>

{{-- DATA TABLE SISWA --}}
<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom mb-0" id="sipintuSiswaTable">
            <thead>
                <tr>
                    <th style="width: 50px;">FOTO</th>
                    <th>NIS</th>
                    <th>NAMA SISWA</th>
                    <th>KELAS</th>
                    <th>JURUSAN</th>
                    <th>EMAIL</th>
                    <th>KONTAK / HP</th>
                    <th>STATUS LOKAL</th>
                    <th class="text-center" style="width: 130px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $index => $s)
                @php
                    $nis = (string) ($s['nis'] ?? $s['nisn'] ?? $s['nis_siswa'] ?? '-');
                    $nama = $s['nama'] ?? $s['name'] ?? $s['nama_siswa'] ?? 'Tanpa Nama';
                    $email = $s['user']['email'] ?? $s['email'] ?? ($nis !== '-' ? "{$nis}@smkn1bangsri.sch.id" : '-');
                    $phone = $s['hp'] ?? $s['phone'] ?? $s['telepon'] ?? $s['no_hp'] ?? null;
                    $isLocal = in_array($nis, $localNisList);
                    $kelas = $s['kelas'] ?? (is_array($s['classroom'] ?? null) ? ($s['classroom']['name'] ?? '-') : '-');
                    $jurusan = $s['jurusan'] ?? '-';
                    $photo = $s['photo'] ?? $s['foto'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($nama) . '&background=00A86B&color=fff';
                    $nisn = $s['nisn'] ?? null;
                @endphp
                <tr>
                    <td>
                        <img src="{{ $photo }}" class="rounded-circle border" style="width: 38px; height: 38px; object-fit: cover;" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($nama) }}&background=00A86B&color=fff'">
                    </td>
                    <td class="font-mono fw-bold text-success">{{ $nis }}</td>
                    <td>
                        <strong>{{ $nama }}</strong>
                        @if($nisn)
                            <br><small class="text-muted font-mono">NISN: {{ $nisn }}</small>
                        @endif
                    </td>
                    <td>
                        @if($kelas !== '-')
                            <span class="badge bg-light text-dark border"><i class="bi bi-door-open me-1 text-primary"></i>{{ $kelas }}</span>
                        @else
                            <span class="text-muted small">Tanpa Kelas</span>
                        @endif
                    </td>
                    <td>
                        <span class="text-muted small">{{ $jurusan }}</span>
                    </td>
                    <td>
                        <small class="text-dark"><i class="bi bi-envelope text-primary me-1"></i>{{ $email }}</small>
                    </td>
                    <td>
                        @if($phone)
                            <small class="text-dark"><i class="bi bi-telephone text-success me-1"></i>{{ $phone }}</small>
                        @else
                            <small class="text-muted">-</small>
                        @endif
                    </td>
                    <td>
                        @if($isLocal)
                            <span class="badge bg-success-subtle text-success border border-success">
                                <i class="bi bi-check2-circle me-1"></i> Ada di GuruKuu
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border">
                                <i class="bi bi-cloud me-1"></i> Belum Disinkron
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-1">
                            <button type="button" class="btn btn-sm btn-outline-info" onclick="showStudentDetail({{ json_encode($s) }}, {{ $isLocal ? 'true' : 'false' }})" title="Lihat Detail Lengkap">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success" onclick="openImportModal({{ json_encode($s) }})" title="{{ $isLocal ? 'Perbarui Data di GuruKuu' : 'Impor ke Database GuruKuu' }}">
                                <i class="bi {{ $isLocal ? 'bi-arrow-repeat' : 'bi-download' }}"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                @endforelse
            </tbody>
        </table>

        @if(empty($students))
        <div class="text-center py-5 text-muted">
            <i class="bi bi-people fs-1 d-block mb-3 text-secondary"></i>
            @if(!$result['success'])
                <p class="mb-1 fw-bold text-dark">{{ $result['message'] }}</p>
                <small class="text-muted">Pastikan server SiPintu Gateway sedang aktif dan URL di <code>.env</code> sudah benar.</small>
            @else
                Tidak ada data siswa yang sesuai dengan filter.
            @endif
        </div>
        @endif
    </div>
</div>

{{-- MODAL DETAIL SISWA --}}
<div class="modal fade" id="modalStudentDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-mortarboard me-2"></i> Detail Lengkap Siswa (SiPintu)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom">
                    <img id="detailStudentPhoto" src="" class="rounded-circle border" style="width: 70px; height: 70px; object-fit: cover;">
                    <div>
                        <h4 id="detailStudentName" class="fw-bold mb-1">-</h4>
                        <span id="detailStudentNis" class="badge bg-light text-dark font-mono border me-2">-</span>
                        <span id="detailStudentKelas" class="badge bg-success">-</span>
                    </div>
                </div>

                <ul class="nav nav-tabs mb-3" id="studentDetailTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabStudentInfo"><i class="bi bi-info-circle me-1"></i> Informasi Siswa</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabStudentRaw"><i class="bi bi-code-slash me-1"></i> Payload JSON Mentah</button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tabStudentInfo">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Nomor Induk Siswa (NIS)</label>
                                <p id="dtNis" class="fw-bold font-mono text-success mb-2">-</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">NISN</label>
                                <p id="dtNisn" class="font-mono mb-2">-</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Email Siswa</label>
                                <p id="dtStudentEmail" class="mb-2 text-primary fw-semibold">-</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">No. HP / WhatsApp Siswa</label>
                                <p id="dtStudentPhone" class="mb-2 text-success fw-semibold">-</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Kelas di SiPintu</label>
                                <p id="dtKelas" class="mb-2">-</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Jurusan</label>
                                <p id="dtStudentJurusan" class="mb-2">-</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Jenis Kelamin</label>
                                <p id="dtGender" class="mb-2">-</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Tanggal Lahir</label>
                                <p id="dtTanggalLahir" class="mb-2">-</p>
                            </div>
                            <div class="col-12">
                                <label class="text-muted small fw-bold">Alamat / Domisili</label>
                                <p id="dtStudentAddress" class="p-2 bg-light rounded text-muted mb-0">-</p>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tabStudentRaw">
                        <pre id="dtStudentRawJson" class="bg-dark text-light p-3 rounded font-mono small mb-0" style="max-height: 250px; overflow-y: auto;"></pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" id="btnStudentDetailSync" class="btn btn-success">
                    <i class="bi bi-download me-1"></i> Impor / Simpan ke GuruKuu
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL IMPORT SISWA --}}
<div class="modal fade" id="modalImportStudent" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.sipintu.siswa.import') }}" method="POST" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-cloud-arrow-down me-2"></i> Impor Data Siswa ke GuruKuu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="nis" id="impSiswaNis">
                <input type="hidden" name="nama" id="impSiswaName">
                <input type="hidden" name="email" id="impSiswaEmail">
                <input type="hidden" name="tanggal_lahir" id="impSiswaTglLahir">

                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Siswa</label>
                    <input type="text" id="impSiswaNameDisplay" class="form-control" readonly disabled>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">NIS</label>
                        <input type="text" id="impSiswaNisDisplay" class="form-control font-mono text-success" readonly disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Kelas SiPintu</label>
                        <input type="text" id="impSiswaKelasDisplay" class="form-control" readonly disabled>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input type="text" id="impSiswaEmailDisplay" class="form-control text-primary" readonly disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Pilih Kelas di GuruKuu (Otomatis)</label>
                    <select name="kelas_id" id="impKelasSelect" class="form-select">
                        <option value="">-- Otomatis Cocokkan Kelas dari SiPintu --</option>
                        @foreach($kelasList as $k)
                            <option value="{{ $k->id }}">Tingkat {{ $k->tingkat }} - {{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Jika dibiarkan default, sistem akan otomatis mencocokkan kelas berdasarkan nama kelas SiPintu.</small>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle me-1"></i> Simpan ke Database GuruKuu
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentStudent = null;

function showStudentDetail(student, isLocal) {
    currentStudent = student;
    const nis = student.nis || student.nisn || student.nis_siswa || '-';
    const nama = student.nama || student.name || student.nama_siswa || 'Tanpa Nama';
    const photo = student.photo || student.foto || `https://ui-avatars.com/api/?name=${encodeURIComponent(nama)}&background=00A86B&color=fff`;
    const email = (student.user && student.user.email) ? student.user.email : (student.email || (nis !== '-' ? (nis + '@smkn1bangsri.sch.id') : '-'));
    const phone = student.hp || student.phone || student.telepon || student.no_hp || '-';

    let kelasName = student.kelas || '-';
    if (typeof student.classroom === 'object' && student.classroom !== null) {
        kelasName = student.classroom.name || student.classroom.nama || kelasName;
    }

    let jurusanName = student.jurusan || '-';

    document.getElementById('detailStudentPhoto').src = photo;
    document.getElementById('detailStudentName').textContent = nama;
    document.getElementById('detailStudentNis').textContent = 'NIS: ' + nis;
    document.getElementById('detailStudentKelas').textContent = kelasName;
    document.getElementById('dtNis').textContent = nis;
    document.getElementById('dtNisn').textContent = student.nisn || '-';
    document.getElementById('dtStudentEmail').textContent = email;
    document.getElementById('dtStudentPhone').textContent = phone;
    document.getElementById('dtTanggalLahir').textContent = student.tanggal_lahir || student.birth_date || student.tgl_lahir || '-';
    document.getElementById('dtKelas').textContent = kelasName;
    document.getElementById('dtStudentJurusan').textContent = jurusanName;
    document.getElementById('dtGender').textContent = student.jk_text || (student.jk == 2 ? 'Perempuan' : 'Laki-laki');
    document.getElementById('dtStudentAddress').textContent = student.alamat || student.address || 'Tidak ada data alamat.';
    document.getElementById('dtStudentRawJson').textContent = JSON.stringify(student, null, 2);

    const btnSync = document.getElementById('btnStudentDetailSync');
    btnSync.onclick = function() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalStudentDetail'));
        if (modal) modal.hide();
        openImportModal(student);
    };

    const modal = new bootstrap.Modal(document.getElementById('modalStudentDetail'));
    modal.show();
}

function openImportModal(student) {
    const nis = student.nis || student.nisn || student.nis_siswa || '';
    const name = student.nama || student.name || student.nama_siswa || '';
    const email = (student.user && student.user.email) ? student.user.email : (student.email || (nis ? (nis + '@smkn1bangsri.sch.id') : ''));
    const tglLahir = student.tanggal_lahir || student.birth_date || student.tgl_lahir || '2007-01-01';

    let kelasName = student.kelas || '';
    if (typeof student.classroom === 'object' && student.classroom !== null) {
        kelasName = student.classroom.name || student.classroom.nama || kelasName;
    }

    document.getElementById('impSiswaNis').value = nis;
    document.getElementById('impSiswaName').value = name;
    document.getElementById('impSiswaEmail').value = email;
    document.getElementById('impSiswaTglLahir').value = tglLahir;
    document.getElementById('impSiswaNameDisplay').value = name;
    document.getElementById('impSiswaNisDisplay').value = nis;
    document.getElementById('impSiswaEmailDisplay').value = email;
    document.getElementById('impSiswaKelasDisplay').value = kelasName || '-';

    const modal = new bootstrap.Modal(document.getElementById('modalImportStudent'));
    modal.show();
}

$(document).ready(function() {
    @if(count($students) > 0)
    $('#sipintuSiswaTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
        ordering: false,
        pageLength: 10
    });
    @endif
});
</script>
@endpush
