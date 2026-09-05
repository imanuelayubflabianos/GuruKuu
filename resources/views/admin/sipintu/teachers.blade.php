@extends('layouts.admin')
@section('title', 'Data Guru SiPintu')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <div class="page-label">GATEWAY SIPINTU</div>
        <h1 class="page-title">Data Guru SiPintu</h1>
        <p class="page-subtitle">Daftar seluruh data guru yang diambil langsung dari SiPintu Gateway (SIJUNA).</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.sipintu.index') }}" class="btn btn-outline-custom">
            <i class="bi bi-speedometer2 me-1"></i> Dashboard Gateway
        </a>
        <a href="{{ route('admin.guru.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-person-check me-1"></i> Data Guru Lokal
        </a>
        @if($result['success'] && !empty($teachers))
        <form action="{{ route('admin.sipintu.guru.sync-all') }}" method="POST" onsubmit="return confirm('Sinkronkan seluruh data guru dari SiPintu ke database GuruKuu?')">
            @csrf
            <button type="submit" class="btn btn-success">
                <i class="bi bi-cloud-arrow-down-fill me-1"></i> Sinkronkan Semua Guru
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
    <a href="{{ route('admin.sipintu.guru', ['refresh' => 1]) }}" class="btn btn-sm btn-outline-warning">
        <i class="bi bi-arrow-clockwise me-1"></i> Coba Lagi
    </a>
</div>
@endif

{{-- FILTER & SEARCH BOX --}}
<div class="card-custom p-3 mb-4">
    <form method="GET" action="{{ route('admin.sipintu.guru') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-bold text-muted">Cari Nama / Email / NIP</label>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Masukkan kata kunci...">
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold text-muted">Filter Berdasarkan NIP</label>
            <input type="text" name="nip" value="{{ request('nip') }}" class="form-control" placeholder="Contoh: 19850101...">
        </div>
        <div class="col-md-5 d-flex gap-2">
            <button type="submit" class="btn btn-primary-custom flex-grow-1">
                <i class="bi bi-funnel me-1"></i> Cari di SiPintu
            </button>
            <a href="{{ route('admin.sipintu.guru', ['refresh' => 1]) }}" class="btn btn-outline-success" title="Segarkan Cache Data SiPintu">
                <i class="bi bi-arrow-repeat"></i> Segarkan
            </a>
            <a href="{{ route('admin.sipintu.guru') }}" class="btn btn-outline-custom" title="Reset Filter">
                <i class="bi bi-arrow-counterclockwise"></i>
            </a>
            <span class="badge bg-light text-dark border align-self-center px-3 py-2">
                Total: <strong>{{ count($teachers) }}</strong> guru
            </span>
        </div>
    </form>
</div>

{{-- DATA TABLE GURU --}}
<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom mb-0" id="sipintuGuruTable">
            <thead>
                <tr>
                    <th style="width: 60px;">FOTO</th>
                    <th>NIP</th>
                    <th>NAMA GURU</th>
                    <th>EMAIL</th>
                    <th>NO. KONTAK / HP</th>
                    <th>KATEGORI</th>
                    <th>JURUSAN / KEAHLIAN</th>
                    <th>STATUS LOKAL</th>
                    <th class="text-center" style="width: 140px;">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teachers as $index => $t)
                @php
                    $nip = (string) ($t['nip'] ?? $t['nik'] ?? $t['nip_guru'] ?? '-');
                    $nama = $t['nama'] ?? $t['name'] ?? $t['nama_guru'] ?? 'Tanpa Nama';
                    $kategori = strtolower($t['kategori'] ?? $t['category'] ?? 'normada');
                    $email = $t['user']['email'] ?? $t['email'] ?? null;
                    $phone = $t['hp'] ?? $t['phone'] ?? $t['telepon'] ?? $t['no_hp'] ?? null;
                    $isLocal = in_array($nip, $localNips);
                    $jurusan = is_array($t['jurusan'] ?? null) ? ($t['jurusan']['nama_jurusan'] ?? $t['jurusan']['nama'] ?? '-') : ($t['jurusan'] ?? '-');
                    $photo = $t['photo'] ?? $t['foto'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($nama) . '&background=003366&color=fff';
                @endphp
                <tr>
                    <td>
                        <img src="{{ $photo }}" class="rounded-circle border" style="width: 40px; height: 40px; object-fit: cover;" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($nama) }}&background=003366&color=fff'">
                    </td>
                    <td class="font-mono fw-bold text-primary">{{ $nip }}</td>
                    <td>
                        <strong>{{ $nama }}</strong>
                    </td>
                    <td>
                        @if($email)
                            <span class="text-dark small"><i class="bi bi-envelope text-primary me-1"></i>{{ $email }}</span>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td>
                        @if($phone)
                            <span class="text-dark small"><i class="bi bi-telephone text-success me-1"></i>{{ $phone }}</span>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-{{ $kategori === 'produktif' ? 'success' : 'primary' }}">
                            {{ ucfirst($kategori) }}
                        </span>
                    </td>
                    <td>
                        <span class="text-muted">{{ $jurusan !== '-' ? $jurusan : ($t['mapel'] ?? '-') }}</span>
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
                            <button type="button" class="btn btn-sm btn-outline-info" onclick="showTeacherDetail({{ json_encode($t) }}, {{ $isLocal ? 'true' : 'false' }})" title="Lihat Detail Lengkap">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="openImportModal({{ json_encode($t) }})" title="{{ $isLocal ? 'Perbarui Data di GuruKuu' : 'Impor ke Database GuruKuu' }}">
                                <i class="bi {{ $isLocal ? 'bi-arrow-repeat' : 'bi-download' }}"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                @endforelse
            </tbody>
        </table>

        @if(empty($teachers))
        <div class="text-center py-5 text-muted">
            <i class="bi bi-person-x fs-1 d-block mb-3 text-secondary"></i>
            @if(!$result['success'])
                <p class="mb-1 fw-bold text-dark">{{ $result['message'] }}</p>
                <small class="text-muted">Pastikan server SiPintu Gateway sedang aktif dan URL di <code>.env</code> sudah benar.</small>
            @else
                Tidak ada data guru yang ditemukan dari SiPintu.
            @endif
        </div>
        @endif
    </div>
</div>

{{-- MODAL DETAIL GURU --}}
<div class="modal fade" id="modalTeacherDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-vcard me-2"></i> Detail Lengkap Guru (SiPintu)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center gap-3 pb-3 mb-3 border-bottom">
                    <img id="detailTeacherPhoto" src="" class="rounded-circle border" style="width: 70px; height: 70px; object-fit: cover;">
                    <div>
                        <h4 id="detailTeacherName" class="fw-bold mb-1">-</h4>
                        <span id="detailTeacherNip" class="badge bg-light text-dark font-mono border me-2">-</span>
                        <span id="detailTeacherKategori" class="badge bg-primary">-</span>
                    </div>
                </div>

                <ul class="nav nav-tabs mb-3" id="detailTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabInfo"><i class="bi bi-info-circle me-1"></i> Informasi Profil</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabRaw"><i class="bi bi-code-slash me-1"></i> Payload JSON Mentah</button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tabInfo">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Nomor Induk Pegawai (NIP)</label>
                                <p id="dtNip" class="fw-bold font-mono mb-2">-</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Email</label>
                                <p id="dtEmail" class="mb-2 text-primary fw-semibold">-</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">No. Telepon / WhatsApp / HP</label>
                                <p id="dtPhone" class="mb-2 text-success fw-semibold">-</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Kategori Guru</label>
                                <p id="dtKategori" class="mb-2">-</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Jurusan / Bidang Keahlian</label>
                                <p id="dtJurusan" class="mb-2">-</p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small fw-bold">Mata Pelajaran (Mapel)</label>
                                <p id="dtMapel" class="mb-2">-</p>
                            </div>
                            <div class="col-12">
                                <label class="text-muted small fw-bold">Alamat / Keterangan</label>
                                <p id="dtBio" class="p-2 bg-light rounded text-muted mb-0">-</p>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tabRaw">
                        <pre id="dtRawJson" class="bg-dark text-light p-3 rounded font-mono small mb-0" style="max-height: 250px; overflow-y: auto;"></pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" id="btnDetailSync" class="btn btn-primary-custom">
                    <i class="bi bi-download me-1"></i> Impor / Simpan ke GuruKuu
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL IMPORT GURU --}}
<div class="modal fade" id="modalImportTeacher" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.sipintu.guru.import') }}" method="POST" class="modal-content border-0 shadow">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-cloud-arrow-down me-2"></i> Impor Data Guru ke GuruKuu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" name="nip" id="impNip">
                <input type="hidden" name="nama" id="impNama">
                <input type="hidden" name="email" id="impEmail">
                <input type="hidden" name="phone" id="impPhone">
                <input type="hidden" name="kategori" id="impKategori">
                <input type="hidden" name="bio" id="impBio">

                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Guru</label>
                    <input type="text" id="impNamaDisplay" class="form-control" readonly disabled>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">NIP</label>
                        <input type="text" id="impNipDisplay" class="form-control font-mono" readonly disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">No. HP / WhatsApp</label>
                        <input type="text" id="impPhoneDisplay" class="form-control font-mono text-success" readonly disabled>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input type="text" id="impEmailDisplay" class="form-control text-primary" readonly disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Pilih Jurusan di GuruKuu (Opsional)</label>
                    <select name="jurusan_id" id="impJurusanSelect" class="form-select">
                        <option value="">-- Tanpa Jurusan / Umum --</option>
                        @foreach($jurusans as $j)
                            <option value="{{ $j->id }}">{{ $j->nama_jurusan }} ({{ $j->kode_jurusan }})</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Jika guru produktif, pilih jurusan yang sesuai di GuruKuu.</small>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary-custom">
                    <i class="bi bi-check-circle me-1"></i> Simpan ke Database GuruKuu
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentTeacher = null;

function showTeacherDetail(teacher, isLocal) {
    currentTeacher = teacher;
    const nip = teacher.nip || teacher.nik || teacher.nip_guru || '-';
    const nama = teacher.nama || teacher.name || teacher.nama_guru || 'Tanpa Nama';
    const kategori = (teacher.kategori || teacher.category || 'normada').toUpperCase();
    const photo = teacher.photo || teacher.foto || `https://ui-avatars.com/api/?name=${encodeURIComponent(nama)}&background=003366&color=fff`;
    const email = (teacher.user && teacher.user.email) ? teacher.user.email : (teacher.email || '-');
    const phone = teacher.hp || teacher.phone || teacher.telepon || teacher.no_hp || '-';

    document.getElementById('detailTeacherPhoto').src = photo;
    document.getElementById('detailTeacherName').textContent = nama;
    document.getElementById('detailTeacherNip').textContent = 'NIP: ' + nip;
    document.getElementById('detailTeacherKategori').textContent = kategori;
    document.getElementById('dtNip').textContent = nip;
    document.getElementById('dtEmail').textContent = email;
    document.getElementById('dtPhone').textContent = phone;
    document.getElementById('dtKategori').textContent = kategori;
    
    let jurusanName = '-';
    if (typeof teacher.jurusan === 'object' && teacher.jurusan !== null) {
        jurusanName = teacher.jurusan.nama_jurusan || teacher.jurusan.nama || '-';
    } else if (teacher.jurusan) {
        jurusanName = teacher.jurusan;
    }
    document.getElementById('dtJurusan').textContent = jurusanName;
    document.getElementById('dtMapel').textContent = teacher.mapel || teacher.mata_pelajaran || '-';
    document.getElementById('dtBio').textContent = teacher.bio || teacher.alamat || teacher.keterangan || 'Tidak ada deskripsi.';
    document.getElementById('dtRawJson').textContent = JSON.stringify(teacher, null, 2);

    const btnSync = document.getElementById('btnDetailSync');
    btnSync.onclick = function() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalTeacherDetail'));
        if (modal) modal.hide();
        openImportModal(teacher);
    };

    const modal = new bootstrap.Modal(document.getElementById('modalTeacherDetail'));
    modal.show();
}

function openImportModal(teacher) {
    const nip = teacher.nip || teacher.nik || teacher.nip_guru || '';
    const nama = teacher.nama || teacher.name || teacher.nama_guru || '';
    const email = (teacher.user && teacher.user.email) ? teacher.user.email : (teacher.email || '');
    const phone = teacher.hp || teacher.phone || teacher.telepon || teacher.no_hp || '';
    const kategori = (teacher.kategori || teacher.category || 'normada').toLowerCase();
    const bio = teacher.bio || teacher.alamat || teacher.mapel || '';

    document.getElementById('impNip').value = nip;
    document.getElementById('impNama').value = nama;
    document.getElementById('impEmail').value = email;
    document.getElementById('impPhone').value = phone;
    document.getElementById('impKategori').value = kategori;
    document.getElementById('impBio').value = bio;
    document.getElementById('impNamaDisplay').value = nama;
    document.getElementById('impNipDisplay').value = nip;
    document.getElementById('impEmailDisplay').value = email || '-';
    document.getElementById('impPhoneDisplay').value = phone || '-';

    const modal = new bootstrap.Modal(document.getElementById('modalImportTeacher'));
    modal.show();
}

$(document).ready(function() {
    @if(count($teachers) > 0)
    $('#sipintuGuruTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
        ordering: false,
        pageLength: 10
    });
    @endif
});
</script>
@endpush
