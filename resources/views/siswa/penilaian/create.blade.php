@extends('layouts.siswa')
@section('title', 'Beri Penilaian - ' . $guru->nama)

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">EVALUASI PENGAJARAN</div>
        <h1 class="page-title">Beri Penilaian Guru</h1>
        <p class="page-subtitle">Suarakan aspirasi dan evaluasi objektif Anda demi peningkatan kualitas belajar mengajar.</p>
    </div>
    <a href="{{ route('siswa.guru.index') }}" class="gk-btn-back">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Guru
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
                    Identitas Anda (Nama & NIS) <strong>tidak pernah ditampilkan</strong> kepada guru maupun siswa lain (namun tidak anonim untuk Administrator demi pemantauan etika & keamanan sistem). Nilai dan saran Anda murni untuk evaluasi mutu sekolah.
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
                            <h2 class="fw-bold mb-0 text-primary font-mono" id="liveScoreText">0 <span class="text-muted fs-6 fw-normal">/ 25</span></h2>
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
                    *Pilih rating bintang pada kelima kriteria di bawah ini untuk mengaktifkan pengiriman.
                </small>
            </div>

            {{-- 5 KRITERIA CARD TILES --}}
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
                        'kedisiplinan'   => ['label' => 'Ketepatan Waktu', 'desc' => 'Guru masuk kelas tepat waktu dan memulai pembelajaran sesuai jadwal.', 'icon' => 'bi-clock-history', 'color' => '#0284c7', 'bg' => '#e0f2fe'],
                        'komunikasi'     => ['label' => 'Kehadiran di Kelas', 'desc' => 'Guru tetap berada di kelas selama proses pembelajaran dan tidak sering meninggalkan kelas tanpa alasan yang jelas.', 'icon' => 'bi-person-check-fill', 'color' => '#0d9488', 'bg' => '#ccfbf1'],
                        'tanggung_jawab' => ['label' => 'Penyampaian Materi', 'desc' => 'Guru menyampaikan materi ajar dengan jelas, sistematis, dan mudah dipahami.', 'icon' => 'bi-book-half', 'color' => '#16a34a', 'bg' => '#dcfce7'],
                        'kreativitas'    => ['label' => 'Interaksi dengan Siswa', 'desc' => 'Guru berinteraksi dengan baik, memberikan kesempatan bertanya/berpendapat, dan merespons siswa dengan baik.', 'icon' => 'bi-chat-dots-fill', 'color' => '#d97706', 'bg' => '#fef3c7'],
                        'keramahan'      => ['label' => 'Keterlibatan & Suasana Belajar', 'desc' => 'Guru menciptakan pembelajaran yang menarik, melibatkan siswa secara aktif, dan membuat suasana belajar nyaman.', 'icon' => 'bi-emoji-smile-fill', 'color' => '#e11d48', 'bg' => '#ffe4e6'],
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
                                <div class="star-rating" data-field="{{ $key }}" style="touch-action: pan-y; -webkit-user-select: none; user-select: none; cursor: pointer; display: inline-flex; align-items: center;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star-fill star" data-value="{{ $i }}" style="font-size: 1.65rem; cursor: pointer; color: #cbd5e1; margin-right: 4px; transition: transform 0.12s, color 0.12s; display: inline-block;"></i>
                                    @endfor
                                    <input type="hidden" name="{{ $key }}" class="rating-input" id="input-{{ $key }}" value="{{ old($key, 0) }}" required>
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

                <style>
                .quick-chip {
                    font-size: 0.74rem;
                    transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
                    font-weight: 500;
                }
                .btn-chip-idle {
                    background-color: #ffffff;
                    border: 1px solid #cbd5e1;
                    color: #475569;
                }
                .btn-chip-idle:hover {
                    background-color: #f1f5f9;
                    border-color: #003366;
                    color: #003366;
                }
                .btn-chip-active {
                    background-color: #003366 !important;
                    border-color: #003366 !important;
                    color: #ffffff !important;
                    box-shadow: 0 2px 6px rgba(0, 51, 102, 0.25);
                }
                </style>

                {{-- QUICK CHIPS INSPIRATION --}}
                <div class="mb-4 p-3 rounded-3 border" style="background: #f8fafc; border-color: #e2e8f0 !important;">
                    <div class="d-flex justify-content-between align-items-center mb-2.5 flex-wrap gap-2">
                        <small class="fw-bold text-dark font-mono d-flex align-items-center gap-1.5" style="font-size: 0.76rem;">
                            <i class="bi bi-chat-square-quote text-primary"></i> PILIHAN TEMPLATE MASUKAN:
                        </small>
                        <small class="text-muted" style="font-size: 0.72rem;">*Klik untuk menyisipkan ke kolom teks, klik lagi untuk membatalkan</small>
                    </div>

                    {{-- 1. TEMPLATE KRITIK --}}
                    <div class="mb-3 pb-2.5 border-bottom" style="border-color: #e2e8f0 !important;">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge rounded-pill border fw-semibold" style="background: rgba(0, 51, 102, 0.08); color: #003366; border-color: rgba(0, 51, 102, 0.15) !important; font-size: 0.72rem;">
                                <i class="bi bi-pencil-square me-1"></i>Template Kritik
                            </span>
                            <small class="text-muted" style="font-size: 0.7rem;">&bull; Disisipkan ke kolom Kritik</small>
                        </div>
                        <div class="d-flex flex-wrap gap-1.5">
                            <button type="button" class="btn btn-sm btn-chip-idle quick-chip py-1 px-2.5 rounded-pill" data-target="kritik" data-text="Mohon izin agar tempo penyampaian materi dapat sedikit diperlambat agar lebih mudah dipahami.">
                                <i class="bi bi-speedometer2 me-1 opacity-75"></i>Tempo Mengajar
                            </button>
                            <button type="button" class="btn btn-sm btn-chip-idle quick-chip py-1 px-2.5 rounded-pill" data-target="kritik" data-text="Mohon perbanyak sesi diskusi dan tanya jawab sebelum beralih ke materi selanjutnya.">
                                <i class="bi bi-chat-dots me-1 opacity-75"></i>Sesi Tanya Jawab
                            </button>
                            <button type="button" class="btn btn-sm btn-chip-idle quick-chip py-1 px-2.5 rounded-pill" data-target="kritik" data-text="Mohon penjelasan materi yang rumit dapat diberikan analogi atau contoh yang lebih sederhana.">
                                <i class="bi bi-lightbulb me-1 opacity-75"></i>Contoh Analogi
                            </button>
                            <button type="button" class="btn btn-sm btn-chip-idle quick-chip py-1 px-2.5 rounded-pill" data-target="kritik" data-text="Mohon tulisan atau materi presentasi di depan kelas dapat diperjelas lagi saat menerangkan.">
                                <i class="bi bi-display me-1 opacity-75"></i>Keterbacaan Materi
                            </button>
                        </div>
                    </div>

                    {{-- 2. TEMPLATE SARAN --}}
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge rounded-pill border fw-semibold" style="background: rgba(0, 51, 102, 0.08); color: #003366; border-color: rgba(0, 51, 102, 0.15) !important; font-size: 0.72rem;">
                                <i class="bi bi-lightbulb me-1"></i>Template Saran
                            </span>
                            <small class="text-muted" style="font-size: 0.7rem;">&bull; Disisipkan ke kolom Saran</small>
                        </div>
                        <div class="d-flex flex-wrap gap-1.5">
                            <button type="button" class="btn btn-sm btn-chip-idle quick-chip py-1 px-2.5 rounded-pill" data-target="saran" data-text="Diharapkan materi dapat diperkaya dengan contoh praktik nyata dan studi kasus yang aplikatif.">
                                <i class="bi bi-laptop me-1 opacity-75"></i>Praktik Nyata
                            </button>
                            <button type="button" class="btn btn-sm btn-chip-idle quick-chip py-1 px-2.5 rounded-pill" data-target="saran" data-text="Bagus jika memanfaatkan media visual, video, atau kuis interaktif agar suasana belajar semakin hidup.">
                                <i class="bi bi-play-circle me-1 opacity-75"></i>Media Interaktif
                            </button>
                            <button type="button" class="btn btn-sm btn-chip-idle quick-chip py-1 px-2.5 rounded-pill" data-target="saran" data-text="Guru sangat ramah dan sabar, diharapkan terus memotivasi dan mendampingi siswa saat belajar.">
                                <i class="bi bi-heart me-1 opacity-75"></i>Bimbingan Siswa
                            </button>
                            <button type="button" class="btn btn-sm btn-chip-idle quick-chip py-1 px-2.5 rounded-pill" data-target="saran" data-text="Akan sangat bermanfaat jika diadakan ulasan atau rangkuman inti materi di akhir jam pelajaran.">
                                <i class="bi bi-journal-check me-1 opacity-75"></i>Rangkuman Materi
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label font-mono small fw-bold text-dark mb-0">
                                <i class="bi bi-chat-left-text text-primary me-1"></i>KRITIK / MASUKAN SANTUN
                            </label>
                            <span class="font-mono text-muted small" id="kritikCounter" style="font-size: 0.72rem;">0 / 255</span>
                        </div>
                        <textarea name="kritik" id="kritikInput" class="form-control" rows="3" maxlength="255" placeholder="Sampaikan masukan perbaikan secara sopan, objektif, dan konstruktif..." style="border-radius: 8px;">{{ old('kritik') }}</textarea>
                        <div class="form-text small text-muted">Maksimal 255 karakter. Sampaikan masukan secara santun.</div>
                        <div class="form-text text-danger fw-bold d-none" id="kritikLimitWarn">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>Anda telah mencapai batas maksimal 255 karakter!
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label font-mono small fw-bold text-dark mb-0">
                                <i class="bi bi-lightbulb text-primary me-1"></i>SARAN & HARAPAN PERBAIKAN
                            </label>
                            <span class="font-mono text-muted small" id="saranCounter" style="font-size: 0.72rem;">0 / 255</span>
                        </div>
                        <textarea name="saran" id="saranInput" class="form-control" rows="3" maxlength="255" placeholder="Sampaikan ide, harapan, atau apresiasi Anda untuk guru tercinta..." style="border-radius: 8px;">{{ old('saran') }}</textarea>
                        <div class="form-text small text-muted">Maksimal 255 karakter. Saran yang baik membantu guru berinovasi.</div>
                        <div class="form-text text-danger fw-bold d-none" id="saranLimitWarn">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>Anda telah mencapai batas maksimal 255 karakter!
                        </div>
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
        const stars = Array.from(container.querySelectorAll('.star'));
        const input = container.querySelector('.rating-input');
        const fieldName = container.dataset.field;
        const badge = document.getElementById('sentiment-' + fieldName);
        const card = document.getElementById('card-' + fieldName);

        function setRating(val, haptic = false) {
            val = Math.max(1, Math.min(5, val));
            input.value = val;
            paintStars(stars, val, '#FFC107');
            if (badge && sentiments[val]) {
                badge.innerText = sentiments[val].text;
                badge.style.color = sentiments[val].color;
                badge.style.background = sentiments[val].bg;
            }
            if (card) {
                card.style.borderColor = '#93c5fd';
                card.style.background = '#ffffff';
            }
            if (haptic && 'vibrate' in navigator) {
                try { navigator.vibrate(15); } catch(e) {}
            }
            updateOverallScore();
        }

        // Desktop mouse hover preview
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
                setRating(val, true);
            });
        });

        // Mobile touch & drag gesture support
        let isTouching = false;
        let lastVal = 0;

        function getValFromTouch(touch) {
            const touchX = touch.clientX;
            let chosen = 1;
            for (let i = 0; i < stars.length; i++) {
                const sRect = stars[i].getBoundingClientRect();
                // When finger touches or passes the left border of star
                if (touchX >= sRect.left - 6) {
                    chosen = i + 1;
                }
            }
            return chosen;
        }

        container.addEventListener('touchstart', function(e) {
            if (e.touches.length !== 1) return;
            isTouching = true;
            const touch = e.touches[0];
            const val = getValFromTouch(touch);
            lastVal = val;
            setRating(val, true);
        }, { passive: true });

        container.addEventListener('touchmove', function(e) {
            if (!isTouching || e.touches.length !== 1) return;
            const touch = e.touches[0];
            const cRect = container.getBoundingClientRect();
            // Allow vertical threshold of 60px above and below stars
            if (touch.clientY < cRect.top - 60 || touch.clientY > cRect.bottom + 60) {
                return;
            }
            const val = getValFromTouch(touch);
            if (val !== lastVal) {
                lastVal = val;
                setRating(val, true);
            }
        }, { passive: true });

        const endTouchHandler = function() {
            if (!isTouching) return;
            isTouching = false;
            if (lastVal > 0) {
                setRating(lastVal, false);
            }
        };

        container.addEventListener('touchend', endTouchHandler);
        container.addEventListener('touchcancel', endTouchHandler);

        // Inisialisasi nilai lama (jika ada nilai bintang sebelumnya / redirect setelah toxic review)
        const initialVal = parseInt(input.value) || 0;
        if (initialVal > 0) {
            setRating(initialVal, false);
        }
    });

    // Jalankan update overall score saat halaman dimuat
    updateOverallScore();

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

        const pct = Math.round((total / 25) * 100);
        document.getElementById('liveScoreText').innerHTML = `${total} <span class="text-muted fs-6 fw-normal">/ 25</span>`;
        document.getElementById('liveScorePct').innerText = `${pct}%`;
        document.getElementById('liveProgressBar').style.width = `${pct}%`;

        const badge = document.getElementById('liveScoreBadge');
        if (countFilled < 5) {
            badge.innerText = `${countFilled} dari 5 Kriteria`;
            badge.style.background = '#f1f5f9';
            badge.style.color = '#475569';
            document.getElementById('liveProgressBar').className = 'progress-bar progress-bar-striped progress-bar-animated bg-secondary';
        } else {
            if (total >= 22) {
                badge.innerText = '🤩 Luar Biasa';
                badge.style.background = '#d1fae5';
                badge.style.color = '#065f46';
                document.getElementById('liveProgressBar').className = 'progress-bar bg-success';
            } else if (total >= 18) {
                badge.innerText = '😊 Sangat Baik';
                badge.style.background = '#e0e7ff';
                badge.style.color = '#3730a3';
                document.getElementById('liveProgressBar').className = 'progress-bar bg-primary';
            } else if (total >= 14) {
                badge.innerText = '🙂 Baik';
                badge.style.background = '#dbeafe';
                badge.style.color = '#1e40af';
                document.getElementById('liveProgressBar').className = 'progress-bar bg-info';
            } else if (total >= 10) {
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

    // Quick chip toggle & Anti-spam logic
    const chips = document.querySelectorAll('.quick-chip');
    const kritikInput = document.getElementById('kritikInput');
    const saranInput = document.getElementById('saranInput');
    const kritikCounter = document.getElementById('kritikCounter');
    const saranCounter = document.getElementById('saranCounter');

    const kritikLimitWarn = document.getElementById('kritikLimitWarn');
    const saranLimitWarn = document.getElementById('saranLimitWarn');

    function updateCounters() {
        if (kritikInput && kritikCounter) {
            const len = kritikInput.value.length;
            kritikCounter.innerText = `${len} / 255`;
            if (len >= 255) {
                kritikCounter.classList.add('text-danger', 'fw-bold');
                if (kritikLimitWarn) kritikLimitWarn.classList.remove('d-none');
            } else {
                kritikCounter.classList.remove('text-danger', 'fw-bold');
                if (kritikLimitWarn) kritikLimitWarn.classList.add('d-none');
            }
        }
        if (saranInput && saranCounter) {
            const len = saranInput.value.length;
            saranCounter.innerText = `${len} / 255`;
            if (len >= 255) {
                saranCounter.classList.add('text-danger', 'fw-bold');
                if (saranLimitWarn) saranLimitWarn.classList.remove('d-none');
            } else {
                saranCounter.classList.remove('text-danger', 'fw-bold');
                if (saranLimitWarn) saranLimitWarn.classList.add('d-none');
            }
        }
        // Update chip active visual
        chips.forEach(chip => {
            const target = chip.dataset.target === 'kritik' ? kritikInput : saranInput;
            const text = chip.dataset.text;
            if (target && target.value.includes(text)) {
                chip.classList.remove('btn-chip-idle');
                chip.classList.add('btn-chip-active');
            } else {
                chip.classList.remove('btn-chip-active');
                chip.classList.add('btn-chip-idle');
            }
        });
    }

    if (kritikInput) {
        kritikInput.addEventListener('input', updateCounters);
    }
    if (saranInput) {
        saranInput.addEventListener('input', updateCounters);
    }

    chips.forEach(chip => {
        chip.addEventListener('click', function() {
            const target = this.dataset.target === 'kritik' ? kritikInput : saranInput;
            const textToAdd = this.dataset.text;
            if (!target) return;

            let curVal = target.value.trim();
            // Anti-spam toggle: If already present, remove it cleanly
            if (curVal.includes(textToAdd)) {
                curVal = curVal.replace(textToAdd, '').replace(/\s+/g, ' ').trim();
                target.value = curVal;
            } else {
                // Check 255 limit
                const newLength = curVal ? (curVal.length + 1 + textToAdd.length) : textToAdd.length;
                if (newLength > 255) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Batas Karakter Tercapai',
                            text: 'Pesan masukan/saran Anda melebihi batas maksimal 255 karakter.',
                            icon: 'warning'
                        });
                    } else {
                        alert('Pesan masukan/saran Anda melebihi batas maksimal 255 karakter.');
                    }
                    return;
                }
                target.value = curVal ? (curVal + ' ' + textToAdd) : textToAdd;
            }
            updateCounters();
            target.focus();
        });
    });

    updateCounters();

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