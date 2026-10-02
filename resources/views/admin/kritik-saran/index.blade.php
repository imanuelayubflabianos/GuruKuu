@extends('layouts.admin')
@section('title', 'Feedback & Ulasan Siswa')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">LAPORAN & FEEDBACK</div>
        <h1 class="page-title">Feedback & Ulasan Siswa</h1>
        <p class="page-subtitle">Daftar rincian nilai, rating bintang, serta kritik dan saran dari siswa untuk para guru yang telah dinilai.</p>
    </div>
</div>

{{-- RINGKASAN STATISTIK --}}
@if(isset($stats))
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card-custom p-3 d-flex align-items-center gap-3">
            <div class="rounded-3 p-3 d-flex align-items-center justify-content-center" style="background: rgba(0, 51, 102, 0.1); color: var(--primary); width: 52px; height: 52px;">
                <i class="bi bi-chat-quote-fill fs-4"></i>
            </div>
            <div>
                <span class="text-muted small text-uppercase font-mono fw-bold">Ulasan Teks Masuk</span>
                <h3 class="fw-bold mb-0 text-dark font-mono">{{ $stats['total_ulasan'] }}</h3>
                <small class="text-muted">Dari total {{ $stats['total_penilaian'] }} evaluasi</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card-custom p-3 d-flex align-items-center gap-3">
            <div class="rounded-3 p-3 d-flex align-items-center justify-content-center" style="background: rgba(245, 158, 11, 0.12); color: #d97706; width: 52px; height: 52px;">
                <i class="bi bi-star-fill fs-4"></i>
            </div>
            <div>
                <span class="text-muted small text-uppercase font-mono fw-bold">Rata-rata Rating</span>
                <div class="d-flex align-items-baseline gap-1">
                    <h3 class="fw-bold mb-0 text-dark font-mono">{{ number_format($stats['rata_rata_bintang'], 2) }}</h3>
                    <span class="text-muted small">/ 5.0</span>
                </div>
                <div class="text-warning small" style="font-size: 0.75rem;">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="bi bi-star-fill {{ $i <= round($stats['rata_rata_bintang']) ? 'text-warning' : 'text-secondary-subtle' }}"></i>
                    @endfor
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card-custom p-3 d-flex align-items-center gap-3">
            <div class="rounded-3 p-3 d-flex align-items-center justify-content-center" style="background: rgba(16, 185, 129, 0.12); color: #059669; width: 52px; height: 52px;">
                <i class="bi bi-award-fill fs-4"></i>
            </div>
            <div>
                <span class="text-muted small text-uppercase font-mono fw-bold">Bintang 5 Sempurna</span>
                <h3 class="fw-bold mb-0 text-dark font-mono">{{ $stats['bintang_5'] }}</h3>
                <small class="text-success fw-semibold">Kepuasan pengajaran maksimal</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card-custom p-3 d-flex align-items-center gap-3">
            <div class="rounded-3 p-3 d-flex align-items-center justify-content-center" style="background: rgba(239, 68, 68, 0.1); color: #dc2626; width: 52px; height: 52px;">
                <i class="bi bi-shield-exclamation fs-4"></i>
            </div>
            <div>
                <span class="text-muted small text-uppercase font-mono fw-bold">Ulasan Disensor</span>
                <h3 class="fw-bold mb-0 text-dark font-mono">{{ $stats['total_disensor'] }}</h3>
                <small class="text-muted">Perlu perhatian / peringatan</small>
            </div>
        </div>
    </div>
</div>
@endif

{{-- FILTER & PENCARIAN --}}
<div class="card-custom p-3 mb-4">
    <form method="GET" action="{{ route('admin.kritik-saran.index') }}" class="row g-2 align-items-center">
        <div class="col-md-4 col-lg-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama guru, NIP, siswa, NIS, atau isi ulasan..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-sm-6 col-md-3 col-lg-2">
            <select name="rating" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Rating Bintang</option>
                <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ 5 Bintang</option>
                <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ 4 Bintang</option>
                <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>⭐⭐⭐ 3 Bintang</option>
                <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>⭐⭐ 2 Bintang</option>
                <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>⭐ 1 Bintang</option>
            </select>
        </div>
        <div class="col-sm-6 col-md-3 col-lg-2">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Status Sensor</option>
                <option value="clean" {{ request('status') == 'clean' ? 'selected' : '' }}>Normal / Terbuka</option>
                <option value="censored" {{ request('status') == 'censored' ? 'selected' : '' }}>Disensor / Peringatan</option>
            </select>
        </div>
        <div class="col-sm-6 col-md-2 col-lg-2">
            <select name="cakupan" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="" {{ request('cakupan') != 'semua' ? 'selected' : '' }}>Ada Kritik/Saran</option>
                <option value="semua" {{ request('cakupan') == 'semua' ? 'selected' : '' }}>Semua Penilaian</option>
            </select>
        </div>
        <div class="col-sm-6 col-md-auto d-flex gap-1">
            <button type="submit" class="btn btn-sm btn-primary-custom px-3">
                <i class="bi bi-search me-1"></i>Cari
            </button>
            @if(request()->anyFilled(['search', 'rating', 'status', 'cakupan']))
                <a href="{{ route('admin.kritik-saran.index') }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<div class="card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="small text-muted">Menampilkan {{ $feedbacks->firstItem() ?? 0 }}–{{ $feedbacks->lastItem() ?? 0 }} dari {{ $feedbacks->total() }} ulasan</span>
        <span class="badge bg-light text-dark border">15 per halaman</span>
    </div>

    <div class="table-responsive">
        <table class="table table-custom align-middle mb-0" id="feedbackTable">
            <thead>
                <tr>
                    <th style="width: 45px;">NO</th>
                    <th style="min-width: 170px;">GURU</th>
                    <th style="min-width: 170px;">SISWA & KELAS</th>
                    <th style="min-width: 250px;">NILAI & RATING BINTANG</th>
                    <th style="min-width: 260px;">KRITIK & SARAN</th>
                    <th class="text-center" style="width: 130px;">AKSI MODERASI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($feedbacks as $index => $f)
                @php
                    $avgScore = round($f->total_nilai / 5, 2);
                    $pctScore = round(($f->total_nilai / 25) * 100);
                    $roundedStar = (int) round($avgScore);
                    $isCensored = $f->is_censored || str_contains($f->kritik ?? '', 'melanggar');

                    // Rincian 5 aspek
                    $aspekList = [
                        'Waktu' => ['val' => $f->kedisiplinan, 'label' => 'Ketepatan Waktu', 'icon' => 'bi-clock-history', 'color' => '#0284c7', 'bg' => '#e0f2fe'],
                        'Kehadiran' => ['val' => $f->tanggung_jawab, 'label' => 'Kehadiran di Kelas', 'icon' => 'bi-door-open', 'color' => '#16a34a', 'bg' => '#dcfce7'],
                        'Materi' => ['val' => $f->komunikasi, 'label' => 'Penyampaian Materi', 'icon' => 'bi-book-half', 'color' => '#0d9488', 'bg' => '#ccfbf1'],
                        'Interaksi' => ['val' => $f->keramahan, 'label' => 'Interaksi dengan Siswa', 'icon' => 'bi-people', 'color' => '#e11d48', 'bg' => '#ffe4e6'],
                        'Suasana' => ['val' => $f->kreativitas, 'label' => 'Keterlibatan & Suasana', 'icon' => 'bi-stars', 'color' => '#d97706', 'bg' => '#fef3c7'],
                    ];
                @endphp
                <tr>
                    <td class="text-muted font-mono">{{ $feedbacks->firstItem() + $index }}</td>
                    
                    {{-- TARGET GURU --}}
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $f->guru->photo_url }}" class="rounded-circle border shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 0.92rem;">{{ $f->guru->nama }}</div>
                                <small class="text-muted font-mono d-block" style="font-size: 0.75rem;">NIP: {{ $f->guru->nip }}</small>
                                <span class="badge mt-0.5" style="background: {{ $f->guru->kategori === 'normada' ? 'rgba(0,51,102,0.1)' : 'rgba(0,168,107,0.1)' }}; color: {{ $f->guru->kategori === 'normada' ? 'var(--primary)' : 'var(--accent)' }}; font-size: 0.65rem;">
                                    {{ strtoupper($f->guru->kategori) }}
                                    @if($f->guru->jurusan) &bull; {{ $f->guru->jurusan->nama_jurusan }} @endif
                                </span>
                            </div>
                        </div>
                    </td>

                    {{-- IDENTITAS SISWA (AKSES ADMIN) --}}
                    <td>
                        @if($f->siswa)
                            <div class="fw-bold text-dark" style="font-size: 0.92rem;">{{ $f->siswa->name }}</div>
                            <div class="small text-muted font-mono" style="font-size: 0.78rem;">NIS: {{ $f->siswa->nis }}</div>
                            <div class="mt-1 d-flex flex-wrap gap-1 align-items-center">
                                <span class="badge bg-light text-dark border" style="font-size: 0.72rem;">
                                    <i class="bi bi-mortarboard me-1 text-primary"></i>{{ $f->kelas?->label_singkat ?? ($f->siswa->kelas?->label_singkat ?? 'Kelas -') }}
                                </span>
                                <span class="badge bg-secondary-subtle text-secondary border" style="font-size: 0.65rem;" title="Identitas siswa dirahasiakan dari guru & publik, namun dapat dilihat oleh Admin">
                                    <i class="bi bi-shield-lock-fill me-0.5"></i>Anonim Publik
                                </span>
                            </div>
                        @else
                            <div class="fw-bold text-dark"><i class="bi bi-incognito me-1"></i>Siswa (Anonim)</div>
                            <small class="text-muted">Data Akun Siswa</small>
                        @endif
                    </td>

                    {{-- NILAI & RATING BINTANG SISWA (DETIL ADMIN) --}}
                    <td>
                        <div class="p-2.5 rounded-3 border" style="background: #fcfdfe;">
                            {{-- AKUMULASI SCORE & STAR RATING --}}
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-1.5">
                                    <div class="d-flex text-warning" style="font-size: 0.95rem;">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star-fill {{ $i <= $roundedStar ? 'text-warning' : 'text-black-50 opacity-25' }}"></i>
                                        @endfor
                                    </div>
                                    <span class="fw-bold font-mono text-dark" style="font-size: 0.95rem;">{{ number_format($avgScore, 1) }}</span>
                                    <span class="text-muted" style="font-size: 0.75rem;">/ 5.0</span>
                                </div>
                                <div>
                                    <span class="badge font-mono border {{ $pctScore >= 80 ? 'bg-success-subtle text-success border-success-subtle' : ($pctScore >= 60 ? 'bg-primary-subtle text-primary border-primary-subtle' : 'bg-warning-subtle text-warning border-warning-subtle') }}" style="font-size: 0.75rem;">
                                        Total: {{ $f->total_nilai }}/25 ({{ $pctScore }}%)
                                    </span>
                                </div>
                            </div>

                            {{-- RINCIAN NILAI & BINTANG 5 KRITERIA --}}
                            <div class="row g-1 text-center">
                                @foreach($aspekList as $aspekKey => $asp)
                                    @php
                                        $val = $asp['val'] ?? 0;
                                        $valColor = $val >= 5 ? '#059669' : ($val >= 4 ? '#0284c7' : ($val >= 3 ? '#d97706' : '#dc2626'));
                                        $valBg = $val >= 5 ? '#ecfdf5' : ($val >= 4 ? '#f0f9ff' : ($val >= 3 ? '#fffbeb' : '#fef2f2'));
                                    @endphp
                                    <div class="col">
                                        <div class="p-1 rounded border" style="background: {{ $valBg }}; border-color: rgba(0,0,0,0.06) !important;" title="{{ $asp['label'] }}: {{ $val }}/5 Bintang">
                                            <div class="text-truncate fw-semibold text-muted" style="font-size: 0.65rem;">
                                                {{ $aspekKey }}
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center gap-0.5 mt-0.5">
                                                <i class="bi bi-star-fill text-warning" style="font-size: 0.65rem;"></i>
                                                <strong class="font-mono" style="font-size: 0.75rem; color: {{ $valColor }};">
                                                    {{ $val }}
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- TOMBOL LIHAT RINCIAN LENGKAP --}}
                            <div class="mt-2 text-end">
                                <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none small fw-semibold text-primary d-inline-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalDetailEvaluasi-{{ $f->id }}" style="font-size: 0.75rem;">
                                    <i class="bi bi-box-arrow-up-right"></i> Rincian Nilai Lengkap
                                </button>
                            </div>
                        </div>
                    </td>

                    {{-- ISI KRITIK & SARAN --}}
                    <td>
                        @if($isCensored)
                            <div class="p-2 rounded border border-warning-subtle bg-warning-subtle text-dark small mb-2">
                                <div class="fw-bold text-warning-emphasis d-flex align-items-center gap-1 mb-1">
                                    <i class="bi bi-shield-exclamation text-warning"></i>
                                    <span>Ulasan Disembunyikan (Disensor)</span>
                                </div>
                                <div class="text-muted" style="font-size: 0.8rem;">
                                    {{ $f->censored_reason ?: 'Ulasan ini tidak memenuhi kriteria kebijakan komunitas.' }}
                                </div>
                            </div>
                            
                            <details class="mt-1">
                                <summary class="text-muted small" style="cursor: pointer; font-size: 0.78rem;">
                                    <i class="bi bi-eye-slash me-1"></i>Lihat teks tersamar
                                </summary>
                                <div class="p-2 mt-1 rounded bg-light border small">
                                    @if($f->kritik)
                                        <div class="mb-1"><strong class="text-danger small">Kritik:</strong> <span class="text-dark">{{ \App\Services\ProfanityFilterService::mask($f->kritik) }}</span></div>
                                    @endif
                                    @if($f->saran)
                                        <div><strong class="text-success small">Saran:</strong> <span class="text-dark">{{ \App\Services\ProfanityFilterService::mask($f->saran) }}</span></div>
                                    @endif
                                </div>
                            </details>
                        @else
                            @if($f->kritik)
                                <div class="p-2 rounded mb-1 border" style="background: #fffafa; border-color: #fee2e2 !important;">
                                    <div class="text-danger fw-semibold d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                        <i class="bi bi-chat-left-dots-fill"></i> Kritik Siswa:
                                    </div>
                                    <div class="text-dark mt-0.5" style="font-size: 0.83rem; line-height: 1.4;">
                                        {{ $f->kritik }}
                                    </div>
                                </div>
                            @endif
                            @if($f->saran)
                                <div class="p-2 rounded border" style="background: #f7fee7; border-color: #dcfce7 !important;">
                                    <div class="text-success fw-semibold d-flex align-items-center gap-1" style="font-size: 0.75rem;">
                                        <i class="bi bi-lightbulb-fill"></i> Saran Siswa:
                                    </div>
                                    <div class="text-dark mt-0.5" style="font-size: 0.83rem; line-height: 1.4;">
                                        {{ $f->saran }}
                                    </div>
                                </div>
                            @endif
                            @if(!$f->kritik && !$f->saran)
                                <span class="text-muted fst-italic small">Hanya memberikan rating skor & bintang tanpa ulasan teks.</span>
                            @endif
                        @endif
                        <div class="mt-1.5 d-flex align-items-center justify-content-between">
                            <small class="text-muted font-mono" style="font-size: 0.72rem;">
                                <i class="bi bi-calendar-event me-1"></i>{{ $f->created_at->format('d/m/Y H:i') }}
                            </small>
                            @if($f->periode)
                                <small class="text-muted font-mono" style="font-size: 0.7rem;">
                                    {{ $f->periode->nama_periode }}
                                </small>
                            @endif
                        </div>
                    </td>

                    {{-- AKSI MODERASI --}}
                    <td class="text-center">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border shadow-xs rounded-circle d-inline-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 32px; height: 32px;" title="Pilihan Aksi">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-1" style="border-radius: 12px; font-size: 0.85rem; min-width: 195px; box-shadow: 0 10px 25px rgba(0,0,0,0.12) !important;">
                                <li>
                                    <button type="button" class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2 text-primary" data-bs-toggle="modal" data-bs-target="#modalDetailEvaluasi-{{ $f->id }}">
                                        <i class="bi bi-eye text-primary"></i>
                                        <span>Rincian Evaluasi</span>
                                    </button>
                                </li>
                                <li>
                                    @if(!$isCensored)
                                        <form action="{{ route('admin.kritik-saran.warn', $f->id) }}" method="POST" class="d-inline"
                                              data-confirm="Beri peringatan kepada {{ $f->siswa ? $f->siswa->name : 'siswa ini' }} dan sensor ulasan agar disembunyikan dari publik & guru?"
                                              data-confirm-title="Beri Peringatan & Sensor Ulasan"
                                              data-confirm-btn="Beri Peringatan"
                                              data-confirm-type="warning">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2 text-warning-emphasis">
                                                <i class="bi bi-shield-exclamation text-warning"></i>
                                                <span>Beri Peringatan & Sensor</span>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.kritik-saran.unwarn', $f->id) }}" method="POST" class="d-inline"
                                              data-confirm="Batalkan status sensor dan tampilkan kembali ulasan ini?"
                                              data-confirm-title="Batalkan Sensor"
                                              data-confirm-btn="Buka Sensor"
                                              data-confirm-type="question">
                                            @csrf
                                            <button type="submit" class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2 text-success">
                                                <i class="bi bi-shield-check text-success"></i>
                                                <span>Buka / Batalkan Sensor</span>
                                            </button>
                                        </form>
                                    @endif
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <form action="{{ route('admin.kritik-saran.destroy', $f->id) }}" method="POST" class="d-inline"
                                          data-confirm="Apakah Anda yakin ingin menghapus permanen penilaian dan ulasan siswa ini? Rating guru akan dihitung ulang dan siswa dapat menilai kembali."
                                          data-confirm-title="Hapus Ulasan Permanen"
                                          data-confirm-btn="Hapus Permanen"
                                          data-confirm-type="danger">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item py-1.5 px-3 d-flex align-items-center gap-2 text-danger">
                                            <i class="bi bi-trash text-danger"></i>
                                            <span>Hapus Ulasan</span>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-chat-left-text fs-1 d-block mb-2 text-secondary"></i>
                        Belum ada evaluasi & ulasan kritik/saran yang sesuai dengan kriteria filter.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($feedbacks->hasPages())
        <div class="pt-3 mt-3 border-top d-flex justify-content-center">{{ $feedbacks->links() }}</div>
    @endif
</div>

{{-- MODAL DETAIL EVALUASI & PENILAIAN SISWA (DITEMPATKAN DI LUAR TABEL AGAR TIDAK KEDAP-KEDIP) --}}
@foreach($feedbacks as $f)
    @php
        $mAvgScore = $f->total_nilai > 0 ? round($f->total_nilai / 5, 2) : 0;
        $mPctScore = round(($mAvgScore / 5) * 100);
        $mRoundedStar = round($mAvgScore);
        $mAspekList = [
            'Ketepatan Waktu' => [
                'val' => $f->kedisiplinan,
                'desc' => 'Guru masuk kelas tepat waktu dan memulai pembelajaran sesuai jadwal.',
                'icon' => 'bi-clock-history',
                'color' => '#0284c7',
                'bg' => '#f0f9ff'
            ],
            'Kehadiran di Kelas' => [
                'val' => $f->kehadiran,
                'desc' => 'Guru tetap berada di kelas selama proses pembelajaran dan tidak sering meninggalkan kelas tanpa alasan yang jelas.',
                'icon' => 'bi-door-open',
                'color' => '#059669',
                'bg' => '#ecfdf5'
            ],
            'Penyampaian Materi' => [
                'val' => $f->kejelasan_materi,
                'desc' => 'Guru menyampaikan materi ajar dengan jelas, sistematis, dan mudah dipahami.',
                'icon' => 'bi-book-half',
                'color' => '#0d9488',
                'bg' => '#f0fdfa'
            ],
            'Interaksi dengan Siswa' => [
                'val' => $f->keramahan,
                'desc' => 'Guru ramah, komunikatif, bersahabat, dan memberi kesempatan siswa untuk bertanya atau berdiskusi.',
                'icon' => 'bi-chat-heart',
                'color' => '#e11d48',
                'bg' => '#fff1f2'
            ],
            'Keterlibatan & Suasana Belajar' => [
                'val' => $f->kreativitas,
                'desc' => 'Guru menciptakan suasana kelas yang menyenangkan, aktif, dan tidak membosankan.',
                'icon' => 'bi-stars',
                'color' => '#d97706',
                'bg' => '#fffbeb'
            ],
        ];
    @endphp
    <div class="modal fade" id="modalDetailEvaluasi-{{ $f->id }}" tabindex="-1" aria-labelledby="modalDetailLabel-{{ $f->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                {{-- MODAL HEADER SESUAI TEMA GURUKUU --}}
                <div class="modal-header text-white px-4 py-3" style="background: linear-gradient(135deg, #003366 0%, #004d99 100%);">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge" style="background: rgba(255,255,255,0.2); font-size: 0.72rem; letter-spacing: 0.5px;">EVALUASI SISWA</span>
                            <h5 class="modal-title fw-bold text-white mb-0" id="modalDetailLabel-{{ $f->id }}" style="font-size: 1.1rem;">
                                Rincian Evaluasi & Penilaian
                            </h5>
                        </div>
                        <div class="text-white-50 small d-flex flex-wrap align-items-center gap-2" style="font-size: 0.78rem;">
                            <span><i class="bi bi-calendar-event me-1"></i>{{ $f->created_at->isoFormat('dddd, D MMMM Y - HH:mm') }} WIB</span>
                            @if($f->periode)
                                <span>&bull;</span>
                                <span><i class="bi bi-clock-history me-1"></i>Periode: {{ $f->periode->nama_periode }}</span>
                            @endif
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                {{-- MODAL BODY --}}
                <div class="modal-body p-4" style="background: #f8fafc;">
                    {{-- INFO GURU & SISWA DENGAN TEMA BERSIH --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 border h-100 shadow-sm">
                                <small class="text-muted fw-bold d-block text-uppercase mb-2" style="font-size: 0.68rem; letter-spacing: 0.5px;">Guru yang Dinilai</small>
                                <div class="d-flex align-items-center gap-2.5">
                                    <img src="{{ $f->guru->photo_url }}" class="rounded-circle border" style="width: 44px; height: 44px; object-fit: cover;">
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $f->guru->nama }}</div>
                                        <div class="text-muted font-mono" style="font-size: 0.75rem;">NIP: {{ $f->guru->nip }}</div>
                                        <span class="badge mt-1" style="background: rgba(0,51,102,0.08); color: #003366; font-size: 0.7rem;">
                                            {{ strtoupper($f->guru->kategori) }} {{ $f->guru->jurusan ? '&bull; ' . $f->guru->jurusan->nama_jurusan : '' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 border h-100 shadow-sm">
                                <small class="text-muted fw-bold d-block text-uppercase mb-2" style="font-size: 0.68rem; letter-spacing: 0.5px;">Identitas Siswa (Khusus Admin)</small>
                                @if($f->siswa)
                                    <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $f->siswa->name }}</div>
                                    <div class="text-muted font-mono" style="font-size: 0.75rem;">NIS: {{ $f->siswa->nis }}</div>
                                    <div class="d-flex align-items-center gap-1.5 mt-1.5">
                                        <span class="badge bg-light text-dark border" style="font-size: 0.72rem;">
                                            <i class="bi bi-mortarboard me-1 text-primary"></i>{{ $f->kelas?->label_singkat ?? ($f->siswa->kelas?->label_singkat ?? 'Kelas -') }}
                                        </span>
                                        <span class="badge bg-secondary-subtle text-secondary border" style="font-size: 0.68rem;">
                                            <i class="bi bi-shield-lock-fill me-0.5"></i>Anonim Publik & Guru
                                        </span>
                                    </div>
                                @else
                                    <div class="fw-bold text-dark"><i class="bi bi-incognito me-1"></i>Siswa (Data Anonim)</div>
                                    <small class="text-muted">Data Akun Siswa</small>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- RINGKASAN SKOR DENGAN TEMA TERANG GURUKUU --}}
                    <div class="p-3.5 bg-white rounded-3 border shadow-sm mb-4" style="border-left: 4px solid #003366 !important;">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div>
                                <div class="text-uppercase fw-bold text-muted mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Skor Rata-Rata & Bintang</div>
                                <div class="d-flex align-items-baseline gap-2">
                                    <span class="fw-extrabold font-mono" style="font-size: 2.2rem; line-height: 1; color: #003366;">{{ number_format($mAvgScore, 2) }}</span>
                                    <span class="text-muted fw-semibold" style="font-size: 0.95rem;">/ 5.00</span>
                                </div>
                                <div class="d-flex text-warning mt-1.5" style="font-size: 1.15rem; gap: 3px;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star-fill {{ $i <= $mRoundedStar ? 'text-warning' : 'text-black-50 opacity-25' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <div class="text-md-end">
                                <div class="text-muted small fw-semibold mb-1">Akumulasi 5 Aspek</div>
                                <div class="fw-bold font-mono text-dark" style="font-size: 1.25rem;">
                                    {{ $f->total_nilai }} <span class="text-muted fw-normal" style="font-size: 0.85rem;">/ 25 Poin</span>
                                </div>
                                <div class="mt-1">
                                    @if($mPctScore >= 80)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1" style="font-size: 0.8rem;">
                                            <i class="bi bi-emoji-smile-fill me-1"></i>Sangat Baik ({{ $mPctScore }}%)
                                        </span>
                                    @elseif($mPctScore >= 60)
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1" style="font-size: 0.8rem;">
                                            <i class="bi bi-hand-thumbs-up-fill me-1"></i>Baik ({{ $mPctScore }}%)
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2.5 py-1" style="font-size: 0.8rem;">
                                            <i class="bi bi-exclamation-circle-fill me-1"></i>Cukup ({{ $mPctScore }}%)
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- RINCIAN PER ASPEK PENGAJARAN --}}
                    <div class="d-flex align-items-center justify-content-between mb-2.5">
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">
                            <i class="bi bi-sliders2-vertical text-primary me-1.5"></i>Rincian Nilai & Bintang per Aspek
                        </h6>
                        <span class="text-muted small">Skala Nilai 1 s/d 5</span>
                    </div>

                    <div class="d-flex flex-column gap-2.5 mb-4">
                        @foreach($mAspekList as $aspekTitle => $asp)
                            @php
                                $val = $asp['val'] ?? 0;
                                $pct = round(($val / 5) * 100);
                            @endphp
                            <div class="p-3 bg-white rounded-3 border shadow-sm">
                                <div class="d-flex align-items-center justify-content-between mb-1.5">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="rounded-2 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: {{ $asp['bg'] }}; color: {{ $asp['color'] }}; font-size: 1.1rem;">
                                            <i class="bi {{ $asp['icon'] }}"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 0.88rem;">{{ $aspekTitle }}</div>
                                            <div class="text-muted" style="font-size: 0.75rem; line-height: 1.3;">{{ $asp['desc'] }}</div>
                                        </div>
                                    </div>
                                    <div class="text-end ps-3">
                                        <div class="d-flex align-items-center justify-content-end gap-1.5">
                                            <div class="d-flex text-warning" style="font-size: 0.85rem;">
                                                @for($s = 1; $s <= 5; $s++)
                                                    <i class="bi bi-star-fill {{ $s <= $val ? 'text-warning' : 'text-black-50 opacity-25' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="fw-bold font-mono text-dark ms-1" style="font-size: 0.95rem;">{{ $val }}</span>
                                            <span class="text-muted small">/ 5</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="progress mt-2" style="height: 6px; background-color: #f1f5f9; border-radius: 999px;">
                                    <div class="progress-bar rounded-pill" role="progressbar" style="width: {{ $pct }}%; background-color: {{ $asp['color'] }};" aria-valuenow="{{ $val }}" aria-valuemin="1" aria-valuemax="5"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- ULASAN KRITIK & SARAN --}}
                    <h6 class="fw-bold text-dark mb-2.5" style="font-size: 0.95rem;">
                        <i class="bi bi-chat-quote-fill text-primary me-1.5"></i>Teks Kritik & Saran Siswa
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border h-100 shadow-sm" style="background: #fffafa; border-color: #fecdd3 !important;">
                                <div class="text-danger fw-bold d-flex align-items-center gap-1.5 mb-2" style="font-size: 0.82rem;">
                                    <i class="bi bi-chat-left-dots-fill"></i> Kritik Siswa:
                                </div>
                                <div class="text-dark" style="font-size: 0.85rem; line-height: 1.5;">
                                    {{ $f->kritik ?: 'Tidak ada teks kritik yang disertakan.' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-3 border h-100 shadow-sm" style="background: #f0fdf4; border-color: #bbf7d0 !important;">
                                <div class="text-success fw-bold d-flex align-items-center gap-1.5 mb-2" style="font-size: 0.82rem;">
                                    <i class="bi bi-lightbulb-fill"></i> Saran Siswa:
                                </div>
                                <div class="text-dark" style="font-size: 0.85rem; line-height: 1.5;">
                                    {{ $f->saran ?: 'Tidak ada teks saran yang disertakan.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- MODAL FOOTER --}}
                <div class="modal-footer bg-white border-top px-4 py-2.5">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection

