@extends('layouts.siswa')
@section('title', 'Detail Guru - ' . $guru->nama)

@section('content')
<div class="page-header mb-4">
    <div>
        <a href="{{ route('siswa.guru.index') }}" class="btn btn-outline-custom btn-sm mb-2.5">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Guru
        </a>
        <h1 class="page-title mt-1">{{ $guru->nama }}</h1>
        <p class="page-subtitle mb-0">
            @if($guru->jurusan)
                <span class="badge bg-primary text-white">{{ $guru->jurusan->nama_jurusan }}</span>
            @else
                <span class="badge bg-secondary text-white">Guru Pengajar</span>
            @endif
        </p>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- PROFIL GURU (KOTAK BESAR DENGAN SUDUT MELENGKUNG, TANPA NIP / NO HP / EMAIL) --}}
    <div class="col-md-5 col-lg-4">
        <div class="card-custom p-4 text-center">
            <div class="position-relative d-inline-block mb-3">
                <img src="{{ $guru->photo_url }}" class="shadow-sm" style="width: 180px; height: 180px; object-fit: cover; border-radius: 16px; border: 4px solid var(--primary);">
            </div>
            <h4 class="fw-bold mb-1" style="color: var(--text-dark);">{{ $guru->nama }}</h4>
            <div class="mb-3">
                <span class="badge bg-light text-primary border border-primary border-opacity-25 px-3 py-1 font-mono">
                    <i class="bi bi-mortarboard-fill me-1"></i>{{ $guru->jurusan ? $guru->jurusan->nama_jurusan : 'Guru Pengajar' }}
                </span>
            </div>

            {{-- DESKRIPSI & TENTANG GURU --}}
            <div class="text-start p-3 rounded" style="background: var(--bg-light); border: 1px solid var(--border);">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-person-lines-fill text-primary"></i>
                    <strong class="small text-dark">Tentang Guru & Deskripsi Pengajaran</strong>
                </div>
                <p class="small text-muted mb-0" style="line-height: 1.6;">
                    {{ $guru->bio ?: 'Guru pengajar yang berdedikasi membimbing dan mendidik siswa-siswi berakhlak mulia serta berprestasi unggul di lingkungan sekolah.' }}
                </p>
            </div>
        </div>
    </div>

    {{-- STATISTIK EVALUASI PERIODE BERJALAN --}}
    <div class="col-md-7 col-lg-8">
        <div class="card-custom p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-graph-up-arrow me-2 text-primary"></i>Statistik Penilaian Periode Berjalan
                    </h5>
                    @if($periodeAktif = \App\Models\Periode::where('status', 'aktif')->first())
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
                <div class="mt-3 p-2 rounded text-center" style="background: var(--bg-light); border: 1px solid var(--border);">
                    <small class="text-muted">Total <strong>{{ $stats['total_penilaian'] }} siswa</strong> telah memberikan penilaian pada semester aktif ini</small>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-bar-chart fs-1 d-block mb-2 text-muted opacity-50"></i>
                    <p class="mb-1 fw-semibold">Belum Ada Penilaian Pada Semester Ini</p>
                    <small>Penilaian baru saja di-reset untuk semester aktif ini. Jadilah siswa pertama yang memberikan penilaian!</small>
                </div>
                @endif
            </div>

            <div class="mt-4">
                @if($periodeAktif)
                    @if($sudahMenilai)
                        <div class="alert alert-success mb-0 d-flex align-items-center">
                            <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
                            <div>
                                <strong class="text-success">Penilaian Anda Sudah Tercatat!</strong>
                                <div class="small text-muted">Anda telah memberikan penilaian untuk guru ini pada periode <strong>{{ $periodeAktif->nama_periode }}</strong>. Anda dapat memberikan penilaian kembali pada periode semester berikutnya.</div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('siswa.penilaian.create', $guru) }}" class="btn btn-primary-custom w-100 py-2 fs-6 fw-semibold">
                            <i class="bi bi-pencil-square me-1"></i> Beri Penilaian untuk Guru Ini
                        </a>
                    @endif
                @else
                    <div class="alert alert-warning mb-0 small">
                        <i class="bi bi-exclamation-triangle me-1"></i> Belum ada periode semester yang aktif saat ini.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ULASAN SISWA PERIODE BERJALAN --}}
<div class="card-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">
            <i class="bi bi-chat-left-quote me-2 text-primary"></i>Ulasan & Masukan Siswa (Periode Berjalan)
            <span class="badge bg-primary ms-2">{{ $semuaFeedback->count() }}</span>
        </h5>
        <small class="text-muted"><i class="bi bi-shield-lock-fill text-success me-1"></i>Anonimitas Terjamin</small>
    </div>

    @if($semuaFeedback->count() > 0)
        @foreach($semuaFeedback as $fb)
        @php
            $fbScore = round(($fb->rata_rata_evaluasi / 5) * 100);
            $fbColor = $fbScore >= 80 ? 'bg-success' : ($fbScore >= 60 ? 'bg-info' : ($fbScore >= 40 ? 'bg-warning' : 'bg-danger'));
        @endphp
        <div class="border rounded p-3 mb-3" style="background: #fdfdfd;">
            <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-secondary bg-opacity-10 text-secondary" style="width: 38px; height: 38px;">
                        <i class="bi bi-incognito fs-5"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <strong class="small text-dark">Siswa (Anonim)</strong>
                            <span class="badge {{ $fbColor }} text-white font-mono" style="font-size: 0.72rem;">
                                Skor: {{ $fbScore }}%
                            </span>
                        </div>
                        <small class="text-muted d-block font-mono" style="font-size: 0.72rem;">{{ $fb->created_at->diffForHumans() }}</small>
                    </div>
                </div>
                <div style="min-width: 140px;">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted" style="font-size: 0.7rem;">Nilai Diberikan:</small>
                        <small class="fw-bold font-mono text-dark" style="font-size: 0.7rem;">{{ $fbScore }}% ({{ $fb->total_nilai }}/25)</small>
                    </div>
                    <div class="progress" style="height: 6px; background-color: #dee2e6; border-radius: 3px;">
                        <div class="progress-bar {{ $fbColor }}" style="width: {{ $fbScore }}%;"></div>
                    </div>
                </div>
            </div>

            {{-- RINCIAN BINTANG PER KRITERIA --}}
            <div class="p-2 rounded mb-2" style="background: var(--bg-light, #f8fafc); border: 1px solid var(--border, #e2e8f0);">
                <div class="small fw-bold text-muted mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                    <i class="bi bi-star-fill text-warning me-1"></i> RINCIAN NILAI PER ASPEK:
                </div>
                <div class="row g-1 text-center">
                    @php
                        $aspectsFb = [
                            'Kedisiplinan' => $fb->kedisiplinan,
                            'Komunikasi' => $fb->komunikasi,
                            'Tanggung Jawab' => $fb->tanggung_jawab,
                            'Kreativitas' => $fb->kreativitas,
                            'Keramahan' => $fb->keramahan,
                        ];
                    @endphp
                    @foreach($aspectsFb as $aspLabel => $aspVal)
                        <div class="col-6 col-sm">
                            <div class="bg-white p-1.5 rounded border" style="font-size: 0.7rem;">
                                <div class="text-truncate text-muted fw-semibold" title="{{ $aspLabel }}">{{ $aspLabel }}</div>
                                <div class="text-warning mt-0.5 d-flex align-items-center justify-content-center gap-0.5">
                                    <i class="bi bi-star-fill" style="font-size: 0.7rem;"></i>
                                    <span class="fw-bold font-mono text-dark" style="font-size: 0.75rem;">{{ $aspVal ?? '-' }}<span class="text-muted fw-normal" style="font-size: 0.65rem;">/5</span></span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @if($fb->is_censored)
                <div class="p-2 rounded small mb-2 bg-light border text-muted fst-italic">
                    <i class="bi bi-shield-exclamation text-warning me-1"></i> Ulasan ini disembunyikan karena tidak memenuhi kriteria kebijakan.
                </div>
            @else
                @if($fb->kritik)
                    <div class="p-2 rounded small mb-2" style="background: #fff8e1; border-left: 3px solid #ffc107;">
                        <strong class="text-warning-emphasis">Kritik Membangun:</strong> {{ $fb->kritik }}
                    </div>
                @endif
                @if($fb->saran)
                    <div class="p-2 rounded small mb-2" style="background: #e1f5fe; border-left: 3px solid #0288d1;">
                        <strong class="text-primary">Saran Perbaikan:</strong> {{ $fb->saran }}
                    </div>
                @endif
            @endif
            @if($fb->balasan_guru)
                <div class="p-2 rounded small mt-2 bg-white border-start border-3 border-primary shadow-sm">
                    <strong class="text-primary d-block mb-1"><i class="bi bi-reply-fill me-1"></i>Tanggapan Guru ({{ $guru->nama }}):</strong>
                    <span class="text-dark">{{ $fb->balasan_guru }}</span>
                </div>
            @endif
        </div>
        @endforeach
    @else
        <div class="text-center py-4 text-muted">
            <i class="bi bi-chat-dots fs-2 d-block mb-2 text-muted opacity-50"></i>
            <p class="mb-0">Belum ada ulasan kritik dan saran pada periode berjalan.</p>
        </div>
    @endif
</div>

{{-- ARSIP PENILAIAN PERIODE LALU (HISTORI SEMESTER SEBELUMNYA) --}}
<div class="card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0 text-muted">
            <i class="bi bi-archive-fill me-2 text-secondary"></i>Arsip Penilaian Periode Lalu
            <span class="badge bg-secondary ms-2">{{ $arsipPeriode->count() }} Periode</span>
        </h5>
        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Data histori semester lampau tetap tersimpan aman</small>
    </div>

    @if($arsipPeriode->count() > 0)
        <div class="accordion" id="accordionArsip">
            @foreach($arsipPeriode as $index => $periodeLalu)
                <div class="accordion-item mb-2 border rounded overflow-hidden">
                    <h2 class="accordion-header" id="headingArsip{{ $periodeLalu->id }}">
                        <button class="accordion-button collapsed py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseArsip{{ $periodeLalu->id }}">
                            <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                <div>
                                    <strong>{{ $periodeLalu->nama_periode }}</strong>
                                    <span class="badge bg-light text-dark border ms-2 font-mono">{{ $periodeLalu->tahun_ajaran }}</span>
                                </div>
                                <span class="badge bg-secondary font-mono">{{ $periodeLalu->penilaian->count() }} Penilaian</span>
                            </div>
                        </button>
                    </h2>
                    <div id="collapseArsip{{ $periodeLalu->id }}" class="accordion-collapse collapse" data-bs-parent="#accordionArsip">
                        <div class="accordion-body bg-light">
                            @forelse($periodeLalu->penilaian as $fbLalu)
                                @php
                                    $scoreLalu = round(($fbLalu->rata_rata_evaluasi / 5) * 100);
                                @endphp
                                <div class="p-3 bg-white rounded border mb-2">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="bi bi-incognito text-muted"></i>
                                            <span class="small fw-bold">Siswa (Anonim)</span>
                                            <span class="badge bg-light text-dark border font-mono">Nilai: {{ $scoreLalu }}%</span>
                                        </div>
                                        <small class="text-muted font-mono" style="font-size: 0.72rem;">{{ $fbLalu->created_at->format('d M Y') }}</small>
                                    </div>
                                    @if($fbLalu->is_censored)
                                        <div class="small text-muted fst-italic mb-1"><i class="bi bi-shield-exclamation text-warning me-1"></i>Ulasan ini disembunyikan karena tidak memenuhi kriteria kebijakan.</div>
                                    @else
                                        @if($fbLalu->kritik)
                                            <div class="small text-muted mb-1"><strong>Kritik:</strong> {{ $fbLalu->kritik }}</div>
                                        @endif
                                        @if($fbLalu->saran)
                                            <div class="small text-muted mb-1"><strong>Saran:</strong> {{ $fbLalu->saran }}</div>
                                        @endif
                                    @endif
                                    @if($fbLalu->balasan_guru || ($fbLalu->balasans && $fbLalu->balasans->count() > 0))
                                        <x-penilaian-thread :penilaian="$fbLalu" />
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-2 text-muted small">Tidak ada ulasan teks pada periode ini.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-4 text-muted">
            <i class="bi bi-folder-x fs-2 d-block mb-2 text-muted opacity-50"></i>
            <p class="mb-0 small">Belum ada arsip ulasan dari periode lampau.</p>
        </div>
    @endif
</div>
@endsection