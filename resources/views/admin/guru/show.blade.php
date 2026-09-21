@extends('layouts.admin')
@section('title', 'Detail Guru - ' . $guru->nama)

@section('content')
<div class="page-header mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <a href="{{ route('admin.guru.index') }}" class="text-decoration-none text-muted small">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Data Guru
            </a>
            <h1 class="page-title mt-2">{{ $guru->nama }}</h1>
            <p class="page-subtitle mb-0">
                <span class="badge bg-primary me-1">{{ $guru->jurusan ? $guru->jurusan->nama_jurusan : 'Guru Pengajar' }}</span>
                <span class="badge bg-secondary font-mono">NIP: {{ $guru->nip }}</span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.guru.edit', $guru) }}" class="btn btn-warning">
                <i class="bi bi-pencil-square me-1"></i> Edit Data & Foto Guru
            </a>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- PROFIL GURU --}}
    <div class="col-md-5 col-lg-4">
        <div class="card-custom p-4 text-center">
            <div class="position-relative d-inline-block mb-3">
                <img src="{{ $guru->photo_url }}" class="shadow-sm" style="width: 180px; height: 180px; object-fit: cover; border-radius: 16px; border: 4px solid var(--primary);">
            </div>
            <h4 class="fw-bold mb-1 text-dark">{{ $guru->nama }}</h4>
            <p class="text-muted small font-mono mb-3">NIP: {{ $guru->nip }}</p>

            {{-- DESKRIPSI & TENTANG GURU --}}
            <div class="text-start p-3 rounded mb-3" style="background: var(--bg-light); border: 1px solid var(--border);">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-person-lines-fill text-primary"></i>
                    <strong class="small text-dark">Tentang Guru & Deskripsi Pengajaran</strong>
                </div>
                <p class="small text-muted mb-0" style="line-height: 1.6;">
                    {{ $guru->bio ?: 'Belum ada deskripsi profil atau bio yang diisi.' }}
                </p>
            </div>

            {{-- INFORMASI KONTAK (KHUSUS ADMIN) --}}
            <div class="text-start small text-muted border rounded p-3 bg-white">
                <div class="fw-bold text-dark mb-2"><i class="bi bi-shield-lock text-primary me-1"></i>Data Kontak (Admin Only)</div>
                <div class="mb-2 d-flex align-items-center gap-2">
                    <i class="bi bi-envelope text-primary"></i>
                    <span class="font-mono text-dark">{{ $guru->email ?: '-' }}</span>
                </div>
                <div class="mb-2 d-flex align-items-center gap-2">
                    <i class="bi bi-telephone text-success"></i>
                    <span class="font-mono text-dark">{{ $guru->phone ?: '-' }}</span>
                </div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-mortarboard text-info"></i>
                    <span class="text-dark">{{ $guru->jurusan ? $guru->jurusan->nama_jurusan : 'Semua Jurusan / Umum' }}</span>
                </div>
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-easel text-info"></i>
                    <div class="text-dark">
                        <div class="fw-semibold mb-1">Kelas yang diajar</div>
                        @forelse($guru->kelas as $kelas)
                            <span class="badge bg-light text-dark border me-1 mb-1">{{ $kelas->label_singkat }}</span>
                        @empty
                            <span class="text-muted">Belum diatur</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- STATISTIK EVALUASI --}}
    <div class="col-md-7 col-lg-8">
        <div class="card-custom p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-graph-up me-2 text-primary"></i>Statistik Evaluasi Periode Berjalan
                </h5>
                @if($periodeAktif)
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                        <i class="bi bi-calendar-check me-1"></i>{{ $periodeAktif->nama_periode }}
                    </span>
                @endif
            </div>

            @if($stats['total_penilaian'] > 0)
            <div class="row g-3">
                @php
                    $aspects = [
                        'Kedisiplinan' => $stats['rata_kedisiplinan'],
                        'Cara Mengajar' => $stats['rata_cara_mengajar'],
                        'Komunikasi' => $stats['rata_komunikasi'],
                        'Tanggung Jawab' => $stats['rata_tanggung_jawab'],
                        'Kreativitas' => $stats['rata_kreativitas'],
                        'Keramahan' => $stats['rata_keramahan'],
                    ];
                @endphp
                @foreach($aspects as $label => $value)
                @php $valPct = round(($value / 5) * 100); @endphp
                <div class="col-md-6">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="fw-bold text-dark">{{ $label }}</small>
                        <small class="text-primary fw-bold font-mono">{{ $valPct }}%</small>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 10px; background-color: #e9ecef;">
                        <div class="progress-bar rounded-pill" style="width: {{ $valPct }}%; background: var(--primary);"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-4 p-3 rounded text-center" style="background: var(--bg-light); border: 1px solid var(--border);">
                <span class="text-muted">Total <strong>{{ $stats['total_penilaian'] }} siswa</strong> telah memberikan penilaian pada periode aktif saat ini</span>
            </div>
            @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-bar-chart fs-1 d-block mb-2 opacity-50"></i>
                Belum ada evaluasi masuk untuk guru ini pada periode aktif saat ini.
            </div>
            @endif
        </div>
    </div>
</div>

{{-- DAFTAR ULASAN SISWA --}}
<div class="card-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">
            <i class="bi bi-chat-left-quote me-2 text-primary"></i>Ulasan Siswa (Periode Aktif)
            <span class="badge bg-primary ms-2">{{ $semuaFeedback->count() }}</span>
        </h5>
        <span class="badge bg-light text-dark border">Anonimitas Siswa</span>
    </div>

    @forelse($semuaFeedback as $fb)
    @php
        $fbScore = round(($fb->total_nilai / 30) * 100);
        $fbColor = $fbScore >= 80 ? 'bg-success' : ($fbScore >= 60 ? 'bg-info' : ($fbScore >= 40 ? 'bg-warning' : 'bg-danger'));
    @endphp
    <div class="border rounded p-3 mb-3 bg-light">
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-white border" style="width: 38px; height: 38px;">
                    <i class="bi bi-person-check-fill fs-5 text-primary"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        @if($fb->siswa)
                            <strong class="small text-dark">{{ $fb->siswa->name }}</strong>
                            <span class="badge bg-secondary-subtle text-secondary font-mono" style="font-size: 0.65rem;">NIS: {{ $fb->siswa->nis }}</span>
                        @else
                            <strong class="small text-dark">Siswa (Anonim)</strong>
                        @endif
                        <span class="badge {{ $fbColor }} text-white font-mono" style="font-size: 0.72rem;">
                            Skor: {{ $fbScore }}%
                        </span>
                    </div>
                    <small class="text-muted font-mono" style="font-size: 0.72rem;">{{ $fb->created_at->format('d M Y H:i') }}</small>
                </div>
            </div>
            <div style="min-width: 140px;">
                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted" style="font-size: 0.7rem;">Nilai Siswa:</small>
                    <small class="fw-bold font-mono text-dark" style="font-size: 0.7rem;">{{ $fbScore }}% ({{ $fb->total_nilai }}/30)</small>
                </div>
                <div class="progress" style="height: 6px; background-color: #dee2e6; border-radius: 3px;">
                    <div class="progress-bar {{ $fbColor }}" style="width: {{ $fbScore }}%;"></div>
                </div>
            </div>
        </div>

        @if($fb->is_censored)
            <div class="p-2 rounded small mb-2 bg-warning-subtle border border-warning-subtle text-dark">
                <i class="bi bi-shield-exclamation text-warning me-1"></i><strong>Ulasan Disembunyikan (Disensor):</strong> Tidak memenuhi kriteria kebijakan.
                <details class="mt-1">
                    <summary class="text-muted small cursor-pointer" style="cursor: pointer;">Lihat teks asli</summary>
                    <div class="mt-1 small text-muted"><strong>Kritik:</strong> {{ $fb->kritik ?: '-' }} | <strong>Saran:</strong> {{ $fb->saran ?: '-' }}</div>
                </details>
            </div>
        @else
            @if($fb->kritik)
                <div class="p-2 rounded small mb-2 bg-white border-start border-3 border-warning">
                    <strong class="text-warning-emphasis">Kritik:</strong> {{ $fb->kritik }}
                </div>
            @endif
            @if($fb->saran)
                <div class="p-2 rounded small mb-2 bg-white border-start border-3 border-info">
                    <strong class="text-info">Saran:</strong> {{ $fb->saran }}
                </div>
            @endif
        @endif
        @if($fb->balasan_guru)
            <div class="p-2 rounded small mt-2 bg-white border-start border-3 border-primary shadow-sm">
                <strong class="text-primary d-block mb-1"><i class="bi bi-reply-fill me-1"></i>Balasan Guru:</strong>
                <span class="text-dark">{{ $fb->balasan_guru }}</span>
            </div>
        @endif
    </div>
    @empty
    <div class="text-center py-4 text-muted">
        <i class="bi bi-chat-dots fs-2 d-block mb-2 opacity-50"></i>
        Belum ada ulasan dari siswa untuk guru ini pada periode aktif.
    </div>
    @endforelse
</div>

{{-- ARSIP HISTORI PENILAIAN PERIODE LALU --}}
<div class="card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0 text-muted">
            <i class="bi bi-archive-fill me-2 text-secondary"></i>Arsip Riwayat Penilaian Periode Lalu
            <span class="badge bg-secondary ms-2">{{ $arsipPeriode->count() }} Periode</span>
        </h5>
    </div>

    @if($arsipPeriode->count() > 0)
        <div class="accordion" id="accordionArsipAdmin">
            @foreach($arsipPeriode as $periodeLalu)
                <div class="accordion-item mb-2 border rounded overflow-hidden">
                    <h2 class="accordion-header" id="headingArsipAdmin{{ $periodeLalu->id }}">
                        <button class="accordion-button collapsed py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseArsipAdmin{{ $periodeLalu->id }}">
                            <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                <div>
                                    <strong>{{ $periodeLalu->nama_periode }}</strong>
                                    <span class="badge bg-light text-dark border ms-2 font-mono">{{ $periodeLalu->tahun_ajaran }}</span>
                                </div>
                                <span class="badge bg-secondary font-mono">{{ $periodeLalu->penilaian->count() }} Penilaian</span>
                            </div>
                        </button>
                    </h2>
                    <div id="collapseArsipAdmin{{ $periodeLalu->id }}" class="accordion-collapse collapse" data-bs-parent="#accordionArsipAdmin">
                        <div class="accordion-body bg-light">
                            @forelse($periodeLalu->penilaian as $fbLalu)
                                @php $scoreLalu = round(($fbLalu->total_nilai / 30) * 100); @endphp
                                <div class="p-3 bg-white rounded border mb-2">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            @if($fbLalu->siswa)
                                                <i class="bi bi-person-fill text-primary"></i>
                                                <span class="small fw-bold text-dark">{{ $fbLalu->siswa->name }}</span>
                                                <span class="badge bg-secondary-subtle text-secondary font-mono" style="font-size: 0.65rem;">{{ $fbLalu->siswa->nis }}</span>
                                            @else
                                                <i class="bi bi-incognito text-muted"></i>
                                                <span class="small fw-bold">Siswa (Anonim)</span>
                                            @endif
                                            <span class="badge bg-light text-dark border font-mono">Nilai: {{ $scoreLalu }}%</span>
                                        </div>
                                        <small class="text-muted font-mono" style="font-size: 0.72rem;">{{ $fbLalu->created_at->format('d M Y H:i') }}</small>
                                    </div>
                                    @if($fbLalu->kritik)
                                        <div class="small text-muted mb-1"><strong>Kritik:</strong> {{ $fbLalu->kritik }}</div>
                                    @endif
                                    @if($fbLalu->saran)
                                        <div class="small text-muted mb-1"><strong>Saran:</strong> {{ $fbLalu->saran }}</div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-2 text-muted small">Tidak ada ulasan pada periode ini.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-4 text-muted">
            <p class="mb-0 small">Belum ada arsip riwayat dari periode semester sebelumnya.</p>
        </div>
    @endif
</div>
@endsection
