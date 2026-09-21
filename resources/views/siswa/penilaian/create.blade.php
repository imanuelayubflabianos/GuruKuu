@extends('layouts.siswa')
@section('title', 'Beri Penilaian - ' . $guru->nama)

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">EVALUASI PENGAJARAN</div>
        <h1 class="page-title">Beri Penilaian Guru</h1>
        <p class="page-subtitle">Suarakan aspirasi dan evaluasi objektif Anda demi peningkatan kualitas belajar mengajar.</p>
    </div>
    <a href="{{ route('siswa.guru.index') }}" class="btn btn-outline-custom">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Guru
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-9">

        {{-- JAMINAN ANONIMITAS --}}
        <div class="p-3 mb-4 rounded-3 d-flex align-items-center gap-3 border shadow-sm" style="background: linear-gradient(135deg, #003366 0%, #004d99 100%); color: white;">
            <div class="rounded-circle p-2 d-flex align-items-center justify-content-center bg-white text-primary" style="width: 44px; height: 44px; font-size: 1.3rem;">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold small text-uppercase font-mono" style="letter-spacing: 1px; color: #FFC107;">
                    <i class="bi bi-eye-slash-fill me-1"></i> Anonimitas 100% Terjamin
                </div>
                <div class="small opacity-90" style="line-height: 1.4;">
                    Identitas Anda (Nama & NIS) <strong>tidak pernah ditampilkan</strong> kepada guru maupun siswa lain. Nilai dan saran Anda murni untuk evaluasi mutu sekolah.
                </div>
            </div>
        </div>

        {{-- INFO GURU TARGET --}}
        <div class="card-custom p-4 mb-4 border-start border-4" style="border-left-color: var(--primary) !important;">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <img src="{{ $guru->photo_url }}" alt="{{ $guru->nama }}" class="rounded-circle border" style="width: 84px; height: 84px; object-fit: cover; border-width: 3px !important; border-color: var(--border) !important;">
                <div class="flex-grow-1">
                    <h3 class="fw-bold mb-1" style="color: var(--text-dark);">{{ $guru->nama }}</h3>
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        <span class="badge" style="background: {{ $guru->kategori === 'normada' ? 'rgba(0,51,102,0.1)' : 'rgba(0,168,107,0.1)' }}; color: {{ $guru->kategori === 'normada' ? 'var(--primary)' : 'var(--accent)' }};">
                            {{ strtoupper($guru->kategori) }}
                        </span>
                        @if($guru->jurusan)
                            <span class="badge" style="background: rgba(255,193,7,0.15); color: #b45309;">
                                <i class="bi bi-mortarboard me-1"></i>{{ $guru->jurusan->nama_jurusan }}
                            </span>
                        @endif
                        <span class="badge bg-light text-dark border">
                            <i class="bi bi-patch-check-fill text-primary me-1"></i> Kepuasan: {{ round(($guru->rata_rata_nilai / 5) * 100) }}% ({{ number_format($guru->rata_rata_nilai, 2) }}/5.0)
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- FORM PENILAIAN --}}
        <form action="{{ route('siswa.penilaian.store', $guru) }}" method="POST" id="penilaianForm">
            @csrf

            {{-- LIVE REAL-TIME SCORE METER --}}
            <div class="card-custom p-3 mb-4 shadow-sm" style="background: #fafcff; border: 1px solid #dbeafe;">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                    <div>
                        <span class="text-muted small text-uppercase font-mono fw-bold">Akumulasi Nilai Sementara</span>
                        <div class="d-flex align-items-baseline gap-2">
                            <h2 class="fw-bold mb-0 text-primary font-mono" id="liveScoreText">0 <span class="text-muted fs-6 fw-normal">/ 30</span></h2>
                            <span class="badge bg-secondary font-mono" id="liveScorePct">0%</span>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge px-3 py-2 fs-6 shadow-sm" id="liveScoreBadge" style="background: #e2e8f0; color: #475569;">
                            Belum Lengkap
                        </span>
                    </div>
                </div>
                <div class="progress" style="height: 8px; border-radius: 6px; background: #e2e8f0;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" id="liveProgressBar" style="width: 0%; transition: width 0.4s ease;"></div>
                </div>
                <small class="text-muted mt-2 d-block text-end" style="font-size: 0.75rem;">
                    *Pilih rating bintang pada keenam kriteria di bawah ini untuk mengaktifkan pengiriman.
                </small>
            </div>

            {{-- 6 KRITERIA CARD TILES --}}
            <div class="card-custom p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-0" style="color: var(--text-dark);">
                            <i class="bi bi-ui-checks me-2 text-primary"></i>Kriteria Evaluasi Pengajaran
                        </h5>
                        <small class="text-muted">Klik jumlah bintang (1 s/d 5) yang paling mencerminkan kualitas pengajaran guru.</small>
                    </div>
                </div>

                @php
                    $kriteriaList = [
                        'kedisiplinan'   => ['label' => 'Kedisiplinan', 'desc' => 'Ketepatan waktu masuk kelas, keteraturan jam pelajaran, dan komitmen kehadiran.', 'icon' => 'bi-clock-history', 'color' => '#0284c7', 'bg' => '#e0f2fe'],
                        'cara_mengajar'  => ['label' => 'Cara Mengajar', 'desc' => 'Kemampuan menyampaikan materi dengan jelas, mudah dipahami, dan terstruktur.', 'icon' => 'bi-mortarboard-fill', 'color' => '#6366f1', 'bg' => '#e0e7ff'],
                        'komunikasi'     => ['label' => 'Komunikasi', 'desc' => 'Kejelasan instruksi, interaksi dua arah, dan keterbukaan dalam mendengarkan siswa.', 'icon' => 'bi-chat-dots-fill', 'color' => '#0d9488', 'bg' => '#ccfbf1'],
                        'tanggung_jawab' => ['label' => 'Tanggung Jawab', 'desc' => 'Tanggung jawab terhadap penugasan, keadilan penilaian, dan bimbingan tugas.', 'icon' => 'bi-shield-check', 'color' => '#16a34a', 'bg' => '#dcfce7'],
                        'kreativitas'    => ['label' => 'Kreativitas', 'desc' => 'Penggunaan metode belajar yang variatif, media digital, dan tidak monoton.', 'icon' => 'bi-lightbulb-fill', 'color' => '#d97706', 'bg' => '#fef3c7'],
                        'keramahan'      => ['label' => 'Keramahan & Sikap', 'desc' => 'Sikap menghargai siswa, kesabaran dalam membimbing, dan empati di kelas.', 'icon' => 'bi-emoji-smile-fill', 'color' => '#e11d48', 'bg' => '#ffe4e6'],
                    ];
                @endphp

                <div class="row g-3">
                    @foreach($kriteriaList as $key => $item)
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border h-100 kriteria-card" style="background: var(--bg-light); transition: all 0.25s;" id="card-{{ $key }}">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="rounded-3 p-2 d-flex align-items-center justify-content-center" style="background: {{ $item['bg'] }}; color: {{ $item['color'] }}; width: 38px; height: 38px;">
                                    <i class="bi {{ $item['icon'] }} fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">{{ $item['label'] }}</h6>
                                </div>
                            </div>
                            <p class="text-muted small mb-3" style="font-size: 0.8rem; line-height: 1.4; min-height: 38px;">
                                {{ $item['desc'] }}
                            </p>

                            {{-- STAR RATING INTERFACE --}}
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <div class="star-rating" data-field="{{ $key }}">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star-fill star" data-value="{{ $i }}" style="font-size: 1.6rem; cursor: pointer; color: #cbd5e1; margin-right: 3px; transition: transform 0.15s, color 0.15s;"></i>
                                    @endfor
                                    <input type="hidden" name="{{ $key }}" class="rating-input" id="input-{{ $key }}" value="0" required>
                                </div>
                                <span class="badge sentiment-badge" id="sentiment-{{ $key }}" style="background: #e2e8f0; color: #64748b; font-size: 0.72rem;">
                                    Belum Dinilai
                                </span>
                            </div>

                            @error($key)
                                <div class="text-danger small mt-2"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- KRITIK & SARAN DENGAN QUICK PRAISE CHIPS --}}
            <div class="card-custom p-4 mb-4">
                <h5 class="fw-bold mb-1" style="color: var(--text-dark);">
                    <i class="bi bi-pencil-square me-2 text-primary"></i>Kritik & Saran Membangun <span class="text-muted small fw-normal">(Opsional)</span>
                </h5>
                <p class="text-muted small mb-3">Tuliskan masukan santun yang dapat membantu guru menjadi lebih hebat dalam mengajar.</p>

                {{-- QUICK CHIPS INSPIRATION --}}
                <div class="mb-3 p-3 rounded bg-light border">
                    <small class="fw-bold text-muted d-block mb-2 font-mono" style="font-size: 0.75rem;">
                        <i class="bi bi-magic me-1"></i> CONTOH INSPIRASI (Klik untuk menambahkan ke teks):
                    </small>
                    <div class="d-flex flex-wrap gap-1">
                        <button type="button" class="btn btn-sm btn-outline-secondary quick-chip py-1 px-2" data-target="saran" data-text="Penjelasan materi sangat jelas, terstruktur, dan mudah dipahami." style="font-size: 0.78rem;">
                            ✨ Penjelasan Sangat Jelas
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary quick-chip py-1 px-2" data-target="saran" data-text="Guru sangat sabar, ramah, dan selalu siap membimbing siswa yang kesulitan." style="font-size: 0.78rem;">
                            😊 Sabar & Ramah
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary quick-chip py-1 px-2" data-target="saran" data-text="Suasana belajar di kelas sangat seru, interaktif, dan tidak membosankan." style="font-size: 0.78rem;">
                            🎯 Kelas Seru & Interaktif
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary quick-chip py-1 px-2" data-target="saran" data-text="Akan lebih menarik jika ada lebih banyak latihan praktik atau studi kasus nyata." style="font-size: 0.78rem;">
                            💡 Perbanyak Contoh Praktik
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary quick-chip py-1 px-2" data-target="kritik" data-text="Terkadang tempo penjelasan materi terasa agak cepat sehingga perlu sedikit diperlambat." style="font-size: 0.78rem;">
                            ⏳ Tempo Kadang Terlalu Cepat
                        </button>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-danger">
                            <i class="bi bi-chat-dots me-1"></i>KRITIK / EVALUASI KEKURANGAN
                        </label>
                        <textarea name="kritik" id="kritikInput" class="form-control" rows="3" placeholder="Sampaikan hal-hal yang perlu diperbaiki secara sopan dan konstruktif..." style="border-radius: 8px;">{{ old('kritik') }}</textarea>
                        <div class="form-text small text-muted">Gunakan bahasa yang santun tanpa merendahkan.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label font-mono small fw-bold text-success">
                            <i class="bi bi-lightbulb me-1"></i>SARAN & HARAPAN PERBAIKAN
                        </label>
                        <textarea name="saran" id="saranInput" class="form-control" rows="3" placeholder="Sampaikan ide, harapan, atau pujian Anda untuk guru tercinta..." style="border-radius: 8px;">{{ old('saran') }}</textarea>
                        <div class="form-text small text-muted">Saran yang baik membantu guru berinovasi.</div>
                    </div>
                </div>
            </div>

            {{-- TOMBOL AKSI --}}
            <div class="d-flex justify-content-between align-items-center pt-2">
                <a href="{{ route('siswa.guru.index') }}" class="btn btn-outline-custom">
                    <i class="bi bi-x-circle me-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-primary-custom px-4 py-2 fw-semibold" id="submitBtn">
                    <i class="bi bi-send-fill me-1"></i> Kirim Penilaian Sekarang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sentiments = {
        1: { text: '😞 Perlu Peningkatan', color: '#dc2626', bg: '#fee2e2' },
        2: { text: '😐 Cukup', color: '#d97706', bg: '#fef3c7' },
        3: { text: '🙂 Baik', color: '#2563eb', bg: '#dbeafe' },
        4: { text: '😊 Sangat Baik', color: '#4f46e5', bg: '#e0e7ff' },
        5: { text: '🤩 Luar Biasa!', color: '#059669', bg: '#d1fae5' }
    };

    const ratingContainers = document.querySelectorAll('.star-rating');

    ratingContainers.forEach(container => {
        const stars = container.querySelectorAll('.star');
        const input = container.querySelector('.rating-input');
        const fieldName = container.dataset.field;
        const badge = document.getElementById('sentiment-' + fieldName);
        const card = document.getElementById('card-' + fieldName);

        // Mouse hover preview
        stars.forEach(star => {
            star.addEventListener('mouseenter', function() {
                const val = parseInt(this.dataset.value);
                paintStars(stars, val, '#FFC107');
                if (sentiments[val] && badge) {
                    badge.innerText = sentiments[val].text;
                    badge.style.color = sentiments[val].color;
                    badge.style.background = sentiments[val].bg;
                }
            });

            star.addEventListener('mouseleave', function() {
                const currentVal = parseInt(input.value);
                if (currentVal > 0) {
                    paintStars(stars, currentVal, '#FFC107');
                    badge.innerText = sentiments[currentVal].text;
                    badge.style.color = sentiments[currentVal].color;
                    badge.style.background = sentiments[currentVal].bg;
                } else {
                    paintStars(stars, 0, '#cbd5e1');
                    badge.innerText = 'Belum Dinilai';
                    badge.style.color = '#64748b';
                    badge.style.background = '#e2e8f0';
                }
            });

            // Click to lock in value
            star.addEventListener('click', function() {
                const val = parseInt(this.dataset.value);
                input.value = val;
                paintStars(stars, val, '#FFC107');
                if (badge) {
                    badge.innerText = sentiments[val].text;
                    badge.style.color = sentiments[val].color;
                    badge.style.background = sentiments[val].bg;
                }
                if (card) {
                    card.style.borderColor = '#93c5fd';
                    card.style.background = '#ffffff';
                }
                updateOverallScore();
            });
        });
    });

    function paintStars(stars, value, color) {
        stars.forEach(s => {
            const starVal = parseInt(s.dataset.value);
            if (starVal <= value) {
                s.style.color = '#FFC107';
                s.style.transform = 'scale(1.15)';
            } else {
                s.style.color = '#cbd5e1';
                s.style.transform = 'scale(1)';
            }
        });
    }

    function updateOverallScore() {
        const inputs = document.querySelectorAll('.rating-input');
        let total = 0;
        let countFilled = 0;

        inputs.forEach(inp => {
            const v = parseInt(inp.value) || 0;
            if (v > 0) {
                total += v;
                countFilled++;
            }
        });

        const pct = Math.round((total / 30) * 100);
        document.getElementById('liveScoreText').innerHTML = `${total} <span class="text-muted fs-6 fw-normal">/ 30</span>`;
        document.getElementById('liveScorePct').innerText = `${pct}%`;
        document.getElementById('liveProgressBar').style.width = `${pct}%`;

        const badge = document.getElementById('liveScoreBadge');
        if (countFilled < 6) {
            badge.innerText = `${countFilled} dari 6 Kriteria`;
            badge.style.background = '#f1f5f9';
            badge.style.color = '#475569';
            document.getElementById('liveProgressBar').className = 'progress-bar progress-bar-striped progress-bar-animated bg-secondary';
        } else {
            if (total >= 26) {
                badge.innerText = '🤩 Luar Biasa';
                badge.style.background = '#d1fae5';
                badge.style.color = '#065f46';
                document.getElementById('liveProgressBar').className = 'progress-bar bg-success';
            } else if (total >= 21) {
                badge.innerText = '😊 Sangat Baik';
                badge.style.background = '#e0e7ff';
                badge.style.color = '#3730a3';
                document.getElementById('liveProgressBar').className = 'progress-bar bg-primary';
            } else if (total >= 16) {
                badge.innerText = '🙂 Baik';
                badge.style.background = '#dbeafe';
                badge.style.color = '#1e40af';
                document.getElementById('liveProgressBar').className = 'progress-bar bg-info';
            } else if (total >= 11) {
                badge.innerText = '😐 Cukup';
                badge.style.background = '#fef3c7';
                badge.style.color = '#92400e';
                document.getElementById('liveProgressBar').className = 'progress-bar bg-warning';
            } else {
                badge.innerText = '😞 Perlu Ditingkatkan';
                badge.style.background = '#fee2e2';
                badge.style.color = '#991b1b';
                document.getElementById('liveProgressBar').className = 'progress-bar bg-danger';
            }
        }
    }

    // Quick chip appender
    document.querySelectorAll('.quick-chip').forEach(chip => {
        chip.addEventListener('click', function() {
            const target = this.dataset.target === 'kritik' ? document.getElementById('kritikInput') : document.getElementById('saranInput');
            const textToAdd = this.dataset.text;
            if (target) {
                if (target.value.trim() === '') {
                    target.value = textToAdd;
                } else {
                    target.value += ' ' + textToAdd;
                }
                target.focus();
            }
        });
    });

    // Form submit validation
    document.getElementById('penilaianForm').addEventListener('submit', function(e) {
        const inputs = document.querySelectorAll('.rating-input');
        let unfilled = [];
        inputs.forEach(inp => {
            if (parseInt(inp.value) === 0) {
                const label = inp.closest('.kriteria-card').querySelector('h6').innerText;
                unfilled.push(label);
            }
        });

        if (unfilled.length > 0) {
            e.preventDefault();
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Penilaian Belum Lengkap',
                    html: `Mohon beri rating bintang untuk kriteria berikut:<br><strong class="text-danger">${unfilled.join(', ')}</strong>`,
                    icon: 'warning',
                    confirmButtonColor: '#003366',
                    confirmButtonText: 'Lengkapi Sekarang'
                });
            } else {
                alert('Mohon lengkapi semua kriteria penilaian sebelum mengirim: ' + unfilled.join(', '));
            }
            return;
        }

    });
});
</script>
@endpush