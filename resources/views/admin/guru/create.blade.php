@extends('layouts.admin')
@section('title', 'Tambah Guru')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">MANAJEMEN DATA</div>
        <h1 class="page-title">Tambah Guru</h1>
        <p class="page-subtitle">Tambahkan data guru baru ke dalam sistem.</p>
    </div>
    <a href="{{ route('admin.guru.index') }}" class="gk-btn-back"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-custom p-4">
            <form action="{{ route('admin.guru.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-12 text-center mb-3">
                        <label class="form-label font-mono small fw-bold text-muted">FOTO PROFIL</label>
                        <input type="file" name="photo" class="form-control" style="border-radius: 8px; max-width: 400px; margin: 0 auto;" accept="image/*">
                        <small class="text-muted">Format: JPG, PNG. Maksimal 2MB.</small>
                        @error('photo')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">NIP <span class="text-danger">*</span></label>
                        <input type="text" name="nip" class="form-control font-mono" value="{{ old('nip') }}" required style="border-radius: 8px;" placeholder="Contoh: 199301162022211000">
                        @error('nip')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">NAMA LENGKAP <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required style="border-radius: 8px;" placeholder="Nama lengkap beserta gelar">
                        @error('nama')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">EMAIL</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" style="border-radius: 8px;" placeholder="contoh@guru.smkn1bangsri.sch.id">
                        @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">NO. HP / WHATSAPP</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" style="border-radius: 8px;" placeholder="Contoh: 085758700025">
                        @error('phone')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-muted">JURUSAN</label>
                        <select name="jurusan_id" class="form-select" style="border-radius: 8px;">
                            <option value="">Pilih Jurusan (Opsional)</option>
                            @foreach($jurusans as $j)
                                <option value="{{ $j->id }}" {{ old('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->nama_jurusan }} ({{ $j->kode_jurusan }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="card border p-3 bg-light-subtle rounded-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                                <div>
                                    <label class="form-label font-mono small fw-bold text-dark mb-0">
                                        <i class="bi bi-mortarboard text-primary me-1"></i> KELAS YANG DIAJAR (Bisa Pilih Lebih dari 1)
                                    </label>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        Centang semua kelas yang diajar guru ini (klik langsung tanpa tahan tombol Ctrl).
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary rounded-pill px-2.5 py-1.5" id="countKelasSelected">
                                        0 Kelas Dipilih
                                    </span>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0.5 px-2" style="font-size: 0.75rem;" onclick="selectAllKelas(true)">
                                        Pilih Semua
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0.5 px-2" style="font-size: 0.75rem;" onclick="selectAllKelas(false)">
                                        Batal Semua
                                    </button>
                                </div>
                            </div>

                            <div class="mb-2">
                                <input type="text" id="searchKelasInput" class="form-control form-control-sm" placeholder="🔍 Cari nama kelas (misal: AKL, PPLG, 10, TO)..." onkeyup="filterKelasList()">
                            </div>

                            <div class="row g-2 overflow-auto p-1" style="max-height: 280px;" id="kelasListContainer">
                                @php
                                    $groupedKelas = ($kelasList ?? collect())->groupBy('tingkat');
                                    $assignedIds = old('kelas_ids', []);
                                @endphp
                                @foreach($groupedKelas as $tingkat => $items)
                                    <div class="col-12 mt-2 mb-1 tingkat-group-title">
                                        <span class="badge bg-secondary-subtle text-secondary border fw-bold" style="font-size: 0.72rem;">
                                            TINGKAT {{ $tingkat }}
                                        </span>
                                    </div>
                                    @foreach($items as $kelas)
                                        @php $isSelected = in_array($kelas->id, $assignedIds); @endphp
                                        <div class="col-sm-6 col-md-4 col-lg-3 kelas-item" data-text="{{ strtolower($kelas->label_singkat . ' ' . $kelas->nama_kelas) }}">
                                            <label class="p-2 rounded-2 border d-flex align-items-center gap-2 w-100 {{ $isSelected ? 'border-primary bg-primary-subtle text-primary fw-semibold' : 'bg-white text-dark' }}" style="cursor: pointer; font-size: 0.82rem; transition: all 0.15s;" id="label_kelas_{{ $kelas->id }}">
                                                <input type="checkbox" name="kelas_ids[]" value="{{ $kelas->id }}" class="form-check-input mt-0 flex-shrink-0 kelas-checkbox" {{ $isSelected ? 'checked' : '' }} onchange="toggleKelasCard(this, '{{ $kelas->id }}')">
                                                <span class="text-truncate">{{ $kelas->label_singkat }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label font-mono small fw-bold text-muted">BIO / ALAMAT / KETERANGAN</label>
                        <textarea name="bio" class="form-control" rows="3" style="border-radius: 8px;" placeholder="Alamat atau informasi singkat...">{{ old('bio') }}</textarea>
                    </div>

                    {{-- OPSI TAMBAH BADGE --}}
                    <div class="col-12 mt-3">
                        <label class="form-label font-mono small fw-bold text-muted d-flex align-items-center justify-content-between mb-2">
                            <span><i class="bi bi-award-fill text-warning me-1"></i> SEMATKAN LENCANA & BADGE</span>
                            <span class="badge bg-light text-muted border font-mono fw-normal">Opsional, pilih satu atau lebih</span>
                        </label>
                        <div class="row g-2">
                            @foreach($badges as $b)
                                <div class="col-sm-6 col-md-4">
                                    <label class="p-2.5 rounded-3 border d-flex align-items-center gap-2.5 w-100 bg-light" style="cursor: pointer; transition: all 0.2s;">
                                        <input type="checkbox" name="badge_ids[]" value="{{ $b->id }}" class="form-check-input mt-0 flex-shrink-0" {{ in_array($b->id, old('badge_ids', [])) ? 'checked' : '' }}>
                                        <div class="d-flex align-items-center justify-content-center rounded-2 flex-shrink-0" style="width: 32px; height: 32px; background: {{ $b->warna ?: '#003366' }}18; color: {{ $b->warna ?: '#003366' }}; font-size: 1.15rem; border: 1px solid {{ $b->warna ?: '#003366' }}33;">
                                            @if(str_starts_with($b->icon, 'bi-'))
                                                <i class="bi {{ $b->icon }}"></i>
                                            @else
                                                {{ $b->icon }}
                                            @endif
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="fw-bold small text-dark text-truncate">{{ $b->nama_badge }}</div>
                                            <div class="text-muted text-truncate" style="font-size: 0.72rem;">{{ $b->deskripsi ?: 'Badge Guru' }}</div>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.guru.index') }}" class="btn btn-outline-custom">Batal</a>
                    <button type="submit" class="btn btn-primary-custom"><i class="bi bi-save me-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleKelasCard(cb, id) {
    const lbl = document.getElementById('label_kelas_' + id);
    if (!lbl) return;
    if (cb.checked) {
        lbl.classList.remove('bg-white', 'text-dark');
        lbl.classList.add('border-primary', 'bg-primary-subtle', 'text-primary', 'fw-semibold');
    } else {
        lbl.classList.remove('border-primary', 'bg-primary-subtle', 'text-primary', 'fw-semibold');
        lbl.classList.add('bg-white', 'text-dark');
    }
    updateKelasCount();
}

function selectAllKelas(check) {
    document.querySelectorAll('.kelas-checkbox').forEach(cb => {
        const item = cb.closest('.kelas-item');
        if (!item || item.style.display !== 'none') {
            cb.checked = check;
            toggleKelasCard(cb, cb.value);
        }
    });
}

function updateKelasCount() {
    const checked = document.querySelectorAll('.kelas-checkbox:checked').length;
    const badge = document.getElementById('countKelasSelected');
    if (badge) {
        badge.textContent = checked + ' Kelas Dipilih';
    }
}

function filterKelasList() {
    const query = (document.getElementById('searchKelasInput')?.value || '').toLowerCase().trim();
    document.querySelectorAll('.kelas-item').forEach(item => {
        const text = item.getAttribute('data-text') || '';
        item.style.display = (query === '' || text.includes(query)) ? '' : 'none';
    });
}
document.addEventListener('DOMContentLoaded', updateKelasCount);
</script>
@endpush