@extends('layouts.guru')
@section('title', 'Dashboard Guru')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">DASHBOARD GURU</div>
        <h1 class="page-title">Selamat Datang, {{ $guru->nama }}! 👋</h1>
        <p class="page-subtitle">Berikut adalah ringkasan evaluasi performa dan ulasan siswa pada periode {{ $periodeAktif->nama_periode ?? 'Aktif' }}.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('guru.leaderboard') }}" class="btn btn-outline-custom">
            <i class="bi bi-trophy me-1"></i> Lihat Leaderboard
        </a>
        <a href="{{ url('/') }}" class="btn btn-primary-custom">
            <i class="bi bi-globe me-1"></i> Ke Beranda Publik
        </a>
    </div>
</div>

{{-- Statistik Utama (3 Kartu Ringan) --}}
<div class="row g-4 mb-4">
    {{-- Rata-rata Nilai --}}
    <div class="col-md-4">
        <div class="card-custom p-4 h-100" style="border-left: 4px solid var(--secondary);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small font-mono mb-1" style="letter-spacing: 1px;">RATA-RATA EVALUASI</div>
                    <h2 class="fw-bold mb-0" style="color: var(--primary);">{{ number_format($guru->rata_rata_nilai ?? 0, 2) }}</h2>
                    <div class="text-warning mt-1">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= round($guru->rata_rata_nilai ?? 0) ? '-fill' : '' }}"></i>
                        @endfor
                    </div>
                </div>
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                    <i class="bi bi-star-fill text-primary fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Penilaian Siswa --}}
    <div class="col-md-4">
        <div class="card-custom p-4 h-100" style="border-left: 4px solid var(--accent);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small font-mono mb-1" style="letter-spacing: 1px;">TOTAL PENILAIAN</div>
                    <h2 class="fw-bold mb-0">{{ $guru->total_penilaian ?? 0 }}</h2>
                    <small class="text-muted">Total siswa yang menilai</small>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                    <i class="bi bi-people-fill text-success fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Feedback Masuk --}}
    <div class="col-md-4">
        <div class="card-custom p-4 h-100" style="border-left: 4px solid #ec4899;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small font-mono mb-1" style="letter-spacing: 1px;">TOTAL ULASAN & SARAN</div>
                    <h2 class="fw-bold mb-0">{{ $ulasanTerbaru->count() }}</h2>
                    <small class="text-muted">Kritik & masukan konstruktif</small>
                </div>
                <div class="bg-danger bg-opacity-10 p-3 rounded-circle">
                    <i class="bi bi-chat-quote-fill text-danger fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Detail Profil Guru & Informasi Akun --}}
<div class="card-custom p-4 mb-4">
    <div class="row align-items-center">
        <div class="col-md-8">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; font-size: 1.8rem; flex-shrink: 0;">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1">{{ $guru->nama }}</h4>
                    <div class="text-muted small font-mono">
                        NIP: <strong>{{ $guru->nip }}</strong> | Departemen: <strong>{{ $guru->jurusan?->nama_jurusan ?? 'Umum' }}</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ route('guru.pengaturan') }}" class="btn btn-outline-custom btn-sm">
                <i class="bi bi-gear me-1"></i> Pengaturan Akun
            </a>
        </div>
    </div>
</div>

{{-- Section Feedback / Ulasan Siswa (100% Anonim & Bisa Dibalas Langsung) --}}
<div class="card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-0">
                <i class="bi bi-chat-heart-fill me-2 text-danger"></i>Ulasan & Saran dari Siswa
            </h5>
            <small class="text-muted">Identitas seluruh siswa disamarkan secara anonim untuk menjaga objektivitas.</small>
        </div>
        <span class="badge bg-primary px-3 py-2">{{ $ulasanTerbaru->count() }} Ulasan Masuk</span>
    </div>

    @if($ulasanTerbaru->isNotEmpty())
        <div class="row g-4">
            @foreach($ulasanTerbaru as $review)
            <div class="col-md-6">
                <div class="p-3 rounded border h-100 d-flex flex-column justify-content-between" style="background: var(--bg-light);">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-secondary">
                                <i class="bi bi-incognito me-1"></i> Siswa (Anonim)
                            </span>
                            <small class="text-muted font-mono" style="font-size: 0.75rem;">
                                {{ $review->created_at->format('d M Y, H:i') }}
                            </small>
                        </div>

                        {{-- Rating Bintang --}}
                        <div class="mb-2" style="color: var(--secondary); font-size: 0.85rem;">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= round($review->total_nilai / 6) ? '-fill' : '' }}"></i>
                            @endfor
                            <span class="text-muted ms-1 font-mono">({{ number_format($review->total_nilai / 6, 1) }}/5.0)</span>
                        </div>

                        @if($review->kritik)
                            <div class="mb-2">
                                <small class="text-danger fw-bold d-block mb-1"><i class="bi bi-chat-left-dots me-1"></i>Kritik / Catatan:</small>
                                <p class="mb-0 small text-dark p-2 bg-white rounded border border-danger-subtle">{{ $review->kritik }}</p>
                            </div>
                        @endif

                        @if($review->saran)
                            <div class="mb-2">
                                <small class="text-success fw-bold d-block mb-1"><i class="bi bi-lightbulb me-1"></i>Saran & Harapan:</small>
                                <p class="mb-0 small text-dark p-2 bg-white rounded border border-success-subtle">{{ $review->saran }}</p>
                            </div>
                        @endif

                        {{-- Tampilan Balasan Guru (Jika Sudah Ada) --}}
                        @if($review->balasan_guru)
                            <div class="mt-3 p-2 rounded bg-white border-start border-4 border-primary shadow-sm">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="text-primary small"><i class="bi bi-reply-fill me-1"></i>Balasan Anda:</strong>
                                    <small class="text-muted font-mono" style="font-size: 0.7rem;">{{ $review->balasan_guru_at?->diffForHumans() }}</small>
                                </div>
                                <p class="mb-0 small text-dark">{{ $review->balasan_guru }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Form Balas Ulasan Langsung --}}
                    <div class="mt-3 pt-2 border-top">
                        <button class="btn btn-sm btn-outline-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#replyForm-{{ $review->id }}">
                            <i class="bi bi-reply me-1"></i> {{ $review->balasan_guru ? 'Ubah Balasan' : 'Balas Ulasan Ini' }}
                        </button>
                        <div class="collapse mt-2" id="replyForm-{{ $review->id }}">
                            <form action="{{ route('guru.penilaian.reply', $review->id) }}" method="POST">
                                @csrf
                                <div class="mb-2">
                                    <textarea name="balasan_guru" class="form-control form-control-sm" rows="2" placeholder="Tulis tanggapan atau ucapan terima kasih kepada siswa..." required>{{ old('balasan_guru', $review->balasan_guru) }}</textarea>
                                </div>
                                <div class="d-flex justify-content-end gap-1">
                                    <button type="button" class="btn btn-sm btn-light border" data-bs-toggle="collapse" data-bs-target="#replyForm-{{ $review->id }}">Batal</button>
                                    <button type="submit" class="btn btn-sm btn-primary-custom">Kirim Balasan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-chat-square-text fs-1 d-block mb-2 text-secondary"></i>
            Belum ada ulasan atau kritik dan saran yang masuk dari siswa untuk periode ini.
        </div>
    @endif
</div>
@endsection