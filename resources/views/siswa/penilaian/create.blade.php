@extends('layouts.siswa')
@section('title', 'Beri Penilaian')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">PENILAIAN GURU</div>
        <h1 class="page-title">Beri Penilaian</h1>
        <p class="page-subtitle">Berikan penilaian objektif Anda untuk membantu peningkatan kualitas pengajaran.</p>
    </div>
    <a href="{{ route('siswa.guru.index') }}" class="btn btn-outline-custom">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-9">
        
        {{-- Info Guru yang akan dinilai --}}
        <div class="card-custom p-4 mb-4" style="border-left: 4px solid var(--primary);">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ $guru->photo_url }}" alt="{{ $guru->nama }}" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover; border: 3px solid var(--primary);">
                <div class="flex-grow-1">
                    <h4 class="fw-bold mb-1" style="color: var(--text-dark);">{{ $guru->nama }}</h4>
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        <span class="badge-custom" style="background: {{ $guru->kategori === 'normada' ? 'rgba(0,51,102,0.1)' : 'rgba(0,168,107,0.1)' }}; color: {{ $guru->kategori === 'normada' ? 'var(--primary)' : 'var(--accent)' }};">
                            {{ strtoupper($guru->kategori) }}
                        </span>
                        @if($guru->jurusan)
                            <span class="badge-custom" style="background: rgba(255,193,7,0.15); color: #d4a017;">{{ $guru->jurusan->nama_jurusan }}</span>
                        @endif
                        <small class="text-muted"><i class="bi bi-star-fill text-warning"></i> Rata-rata: {{ number_format($guru->rata_rata_nilai, 2) }}</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Penilaian --}}
        <form action="{{ route('siswa.penilaian.store', $guru) }}" method="POST" id="penilaianForm">
            @csrf

            {{-- 6 Kriteria Penilaian --}}
            <div class="card-custom p-4 mb-4">
                <h5 class="fw-bold mb-1" style="color: var(--text-dark);">Kriteria Penilaian</h5>
                <p class="text-muted small mb-4">Berikan rating 1-5 untuk setiap aspek pengajaran berikut.</p>

                @php
                    $kriteria = [
                        'kedisiplinan' => ['label' => 'Kedisiplinan', 'icon' => 'bi-clock-fill', 'desc' => 'Ketepatan waktu dan kehadiran'],
                        'cara_mengajar' => ['label' => 'Cara Mengajar', 'icon' => 'bi-book-fill', 'desc' => 'Kemampuan menjelaskan materi'],
                        'komunikasi' => ['label' => 'Komunikasi', 'icon' => 'bi-chat-dots-fill', 'desc' => 'Kejelasan dalam berkomunikasi'],
                        'tanggung_jawab' => ['label' => 'Tanggung Jawab', 'icon' => 'bi-shield-check', 'desc' => 'Komitmen terhadap tugas'],
                        'kreativitas' => ['label' => 'Kreativitas', 'icon' => 'bi-lightbulb-fill', 'desc' => 'Inovasi dalam pengajaran'],
                        'keramahan' => ['label' => 'Keramahan', 'icon' => 'bi-emoji-smile-fill', 'desc' => 'Sikap dan pendekatan kepada siswa'],
                    ];
                @endphp

                @foreach($kriteria as $key => $item)
                <div class="mb-4 pb-4 border-bottom" style="border-color: var(--border) !important;">
                    <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                        <div>
                            <label class="fw-bold mb-1 d-flex align-items-center gap-2" style="color: var(--text-dark);">
                                <i class="bi {{ $item['icon'] }}" style="color: var(--primary);"></i>
                                {{ $item['label'] }}
                            </label>
                            <small class="text-muted">{{ $item['desc'] }}</small>
                        </div>
                        <div class="star-rating" data-field="{{ $key }}">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star-fill star" data-value="{{ $i }}" style="font-size: 1.75rem; cursor: pointer; color: #e2e8f0; transition: all 0.2s;"></i>
                            @endfor
                            <input type="hidden" name="{{ $key }}" class="rating-input" value="0" required>
                        </div>
                    </div>
                    @error($key)
                        <div class="text-danger small mt-1"><i class="bi bi-exclamation-circle"></i> {{ $message }}</div>
                    @enderror
                </div>
                @endforeach
            </div>

            {{-- Kritik & Saran --}}
            <div class="card-custom p-4 mb-4">
                <h5 class="fw-bold mb-1" style="color: var(--text-dark);">Kritik & Saran <span class="text-muted small">(Opsional)</span></h5>
                <p class="text-muted small mb-3">Berikan masukan untuk membantu guru meningkatkan kualitas pengajaran.</p>
                
                <div class="mb-3">
                    <label class="form-label font-mono small fw-bold text-muted">KRITIK</label>
                    <textarea name="kritik" class="form-control" rows="2" placeholder="Contoh: Terkadang penjelasan terlalu cepat..." style="border-radius: 8px;">{{ old('kritik') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label font-mono small fw-bold text-muted">SARAN</label>
                    <textarea name="saran" class="form-control" rows="2" placeholder="Contoh: Akan lebih baik jika ada lebih banyak contoh praktik..." style="border-radius: 8px;">{{ old('saran') }}</textarea>
                </div>
            </div>

            {{-- Tombol Submit --}}
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('siswa.guru.index') }}" class="btn btn-outline-custom">Batal</a>
                <button type="submit" class="btn btn-primary-custom">
                    <i class="bi bi-send-fill me-1"></i> Kirim Penilaian
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Logic Star Rating Interaktif
    document.querySelectorAll('.star-rating').forEach(container => {
        const stars = container.querySelectorAll('.star');
        const input = container.querySelector('.rating-input');

        // Hover effect
        stars.forEach(star => {
            star.addEventListener('mouseenter', function() {
                const value = parseInt(this.dataset.value);
                highlightStars(stars, value, '#FFC107');
            });

            star.addEventListener('mouseleave', function() {
                const currentValue = parseInt(input.value);
                highlightStars(stars, currentValue, currentValue > 0 ? '#FFC107' : '#e2e8f0');
            });

            // Click to set rating
            star.addEventListener('click', function() {
                const value = parseInt(this.dataset.value);
                input.value = value;
                highlightStars(stars, value, '#FFC107');
            });
        });
    });

    function highlightStars(stars, value, color) {
        stars.forEach(s => {
            if (parseInt(s.dataset.value) <= value) {
                s.style.color = color;
                s.style.transform = 'scale(1.1)';
            } else {
                s.style.color = '#e2e8f0';
                s.style.transform = 'scale(1)';
            }
        });
    }

    // Validasi sebelum submit
    document.getElementById('penilaianForm').addEventListener('submit', function(e) {
        const inputs = document.querySelectorAll('.rating-input');
        let allFilled = true;
        inputs.forEach(input => {
            if (parseInt(input.value) === 0) {
                allFilled = false;
            }
        });
        if (!allFilled) {
            e.preventDefault();
            alert('Mohon lengkapi semua kriteria penilaian sebelum mengirim.');
        }
    });
});
</script>
@endpush
@endsection