{{-- resources/views/landing/guru-detail.blade.php --}}
@extends('layouts.landing')
@section('title', $guru->nama)

@section('content')
<section style="background: var(--bg-light); padding: 140px 0 80px; min-height: 100vh;">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb" style="font-size: 0.85rem;">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none" style="color: var(--text-muted);">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('landing.leaderboard') }}" class="text-decoration-none" style="color: var(--text-muted);">Leaderboard</a></li>
                <li class="breadcrumb-item active">{{ $guru->nama }}</li>
            </ol>
        </nav>

        <div class="row g-4">
            <div class="col-lg-4">
                <img src="{{ $guru->photo_url }}" class="rounded w-100 mb-3" style="height: 400px; object-fit: cover;">
                <div class="card-custom p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="font-mono" style="font-size: 0.7rem; font-weight: 600; color: var(--text-muted); letter-spacing: 2px;">TINGKAT KEPUASAN</div>
                        <div class="text-primary font-mono fw-bold" style="font-size: 1.1rem;">
                            {{ round(($guru->rata_rata_nilai / 5) * 100) }}%
                        </div>
                    </div>
                    <div class="progress" style="height: 8px; border-radius: 6px;">
                        <div class="progress-bar bg-primary" style="width: {{ round(($guru->rata_rata_nilai / 5) * 100) }}%;"></div>
                    </div>
                    <small class="text-muted mt-2 d-block">Berdasarkan {{ $guru->total_penilaian }} penilaian siswa</small>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="mb-3">
                    <span class="badge-custom" style="background: var(--primary); color: white; padding: 0.5rem 1rem; border-radius: 6px; font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; letter-spacing: 1px;">
                        {{ strtoupper($guru->jurusan?->nama_jurusan ?? 'GURU PENGAJAR') }}
                    </span>
                    @if($guru->jurusan)
                    <span class="badge-custom" style="background: var(--bg-light); color: var(--text-dark); border: 1px solid var(--border); padding: 0.5rem 1rem; border-radius: 6px; font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; letter-spacing: 1px;">
                        {{ strtoupper($guru->jurusan->kode_jurusan) }}
                    </span>
                    @endif
                </div>

                <h1 class="fw-bold mb-2" style="font-size: 2.5rem;">{{ $guru->nama }}</h1>
                <h5 class="mb-4" style="color: var(--primary); font-weight: 600;">{{ $guru->jurusan?->nama_jurusan }}</h5>

                <p class="text-muted mb-4" style="line-height: 1.7; font-size: 1.05rem;">
                    {{ $guru->bio ?? 'Berdedikasi dalam mendidik siswa SMK untuk menjadi tenaga profesional di bidangnya.' }}
                </p>

                <div class="row g-3 mb-4">
                    <div class="col-md-3 col-6">
                        <div class="card-custom p-3 text-center">
                            <div class="font-mono" style="font-size: 0.65rem; color: var(--text-muted); letter-spacing: 1px;">KEPUASAN</div>
                            <div class="fw-bold fs-5 text-primary">{{ round(($guru->rata_rata_nilai / 5) * 100) }}%</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card-custom p-3 text-center">
                            <div class="font-mono" style="font-size: 0.65rem; color: var(--text-muted); letter-spacing: 1px;">TOTAL ULASAN</div>
                            <div class="fw-bold fs-5">{{ $guru->total_penilaian }}</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card-custom p-3 text-center">
                            <div class="font-mono" style="font-size: 0.65rem; color: var(--text-muted); letter-spacing: 1px;">STATUS</div>
                            <div class="fw-bold fs-6 text-success">Aktif Mengajar</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="card-custom p-3 text-center">
                            <div class="font-mono" style="font-size: 0.65rem; color: var(--text-muted); letter-spacing: 1px;">BADGE</div>
                            <div class="fw-bold fs-5">{{ $guru->penghargaan->count() }}</div>
                        </div>
                    </div>
                </div>

                @auth
                    @if(auth()->user()->role === 'siswa')
                        <a href="{{ route('siswa.penilaian.create', $guru->id) }}" class="btn btn-primary-custom" style="padding: 0.75rem 2rem;">
                            <i class="bi bi-pencil-square me-1"></i> Beri Penilaian untuk Guru Ini
                        </a>
                    @elseif(auth()->user()->role === 'guru' && (auth()->user()->nis === $guru->nip || auth()->user()->email === $guru->email))
                        <span class="badge bg-success px-3 py-2 fs-6">
                            <i class="bi bi-person-check-fill me-1"></i> Ini adalah Profil Anda (Guru)
                        </span>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary-custom" style="padding: 0.75rem 2rem;">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login untuk Memberi Penilaian
                    </a>
                @endauth

                {{-- Penilaian Terbaru (100% Anonim & Guru Bisa Balas Langsung) --}}
                @php
                    $isCurrentGuru = auth()->check() && auth()->user()->role === 'guru' && (auth()->user()->nis === $guru->nip || auth()->user()->email === $guru->email);
                @endphp

                @if($guru->penilaian->isNotEmpty())
                <h4 class="fw-bold mt-5 mb-3">Penilaian & Ulasan Siswa</h4>
                @foreach($guru->penilaian->take(15) as $p)
                <div class="card-custom p-4 mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                <i class="bi bi-incognito fs-5"></i>
                            </div>
                            <div>
                                <div class="fw-bold">Siswa (Anonim)</div>
                                <div class="font-mono" style="font-size: 0.7rem; color: var(--text-muted); letter-spacing: 1px;">
                                    {{ $p->created_at->format('d M Y') }}
                                </div>
                            </div>
                        </div>
                        @php
                            $pScore = round(($p->total_nilai / 30) * 100);
                            $pBadge = $pScore >= 80 ? 'bg-success' : ($pScore >= 60 ? 'bg-info' : ($pScore >= 40 ? 'bg-warning' : 'bg-danger'));
                        @endphp
                        <div class="text-end" style="min-width: 120px;">
                            <span class="badge {{ $pBadge }} text-white font-mono mb-1">Nilai: {{ $pScore }}%</span>
                            <div class="progress" style="height: 5px; background: #e2e8f0; border-radius: 3px;">
                                <div class="progress-bar {{ $pBadge }}" style="width: {{ $pScore }}%;"></div>
                            </div>
                        </div>
                    </div>
                    @if($p->kritik)
                        <p class="mb-2" style="font-size: 0.95rem;">"{{ $p->kritik }}"</p>
                    @endif
                    @if($p->saran)
                        <p class="mb-0 text-muted" style="font-size: 0.9rem;"><strong>Saran:</strong> {{ $p->saran }}</p>
                    @endif

                    {{-- Balasan Guru (Jika Ada) --}}
                    @if($p->balasan_guru)
                    <div class="mt-3 p-3 rounded bg-light border-start border-4 border-primary">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong class="text-primary small">
                                <i class="bi bi-reply-fill me-1"></i>Balasan dari {{ $guru->nama }}
                            </strong>
                            <small class="text-muted font-mono" style="font-size: 0.7rem;">
                                {{ $p->balasan_guru_at?->diffForHumans() }}
                            </small>
                        </div>
                        <p class="mb-0 small text-dark">{{ $p->balasan_guru }}</p>
                    </div>
                    @endif

                    {{-- Form Balas Langsung Bagi Guru Yang Bersangkutan --}}
                    @if($isCurrentGuru)
                    <div class="mt-3 pt-2 border-top">
                        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#replyBox-{{ $p->id }}">
                            <i class="bi bi-reply me-1"></i> {{ $p->balasan_guru ? 'Ubah Balasan' : 'Balas Ulasan Siswa' }}
                        </button>
                        <div class="collapse mt-2" id="replyBox-{{ $p->id }}">
                            <form action="{{ route('guru.penilaian.reply', $p->id) }}" method="POST">
                                @csrf
                                <div class="mb-2">
                                    <textarea name="balasan_guru" class="form-control form-control-sm" rows="2" placeholder="Tulis tanggapan atau apresiasi Anda kepada siswa..." required>{{ old('balasan_guru', $p->balasan_guru) }}</textarea>
                                </div>
                                <div class="d-flex justify-content-end gap-1">
                                    <button type="button" class="btn btn-sm btn-light border" data-bs-toggle="collapse" data-bs-target="#replyBox-{{ $p->id }}">Batal</button>
                                    <button type="submit" class="btn btn-sm btn-primary-custom">Kirim Balasan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</section>
@endsection