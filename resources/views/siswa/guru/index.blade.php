@extends('layouts.siswa')
@section('title', 'Daftar Guru')

@section('content')
<div class="page-header">
    <div>
        <div class="page-label">EVALUASI GURU</div>
        <h1 class="page-title">Daftar Guru</h1>
        @if($kelasAktif)
            <p class="page-subtitle">
                <span class="badge bg-primary me-2">{{ $kelasAktif->nama_kelas }} - Tingkat {{ $kelasAktif->tingkat }}</span>
                Angka utama menunjukkan persentase siswa yang telah memberikan evaluasi.
            </p>
        @else
            <p class="page-subtitle text-danger">Anda belum terdaftar di kelas manapun.</p>
        @endif
    </div>
</div>

@if(!$kelasAktif)
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle me-2"></i>
        Anda belum terdaftar di kelas manapun. Silakan hubungi admin.
    </div>
@else
    @php
        $periodeId = \App\Models\Periode::where('status', 'aktif')->value('id');
        $guruNormada = $guru->where('kategori', 'normada');
        $guruProduktif = $guru->where('kategori', 'produktif');
    @endphp

    <ul class="nav nav-pills mb-4" id="guruTab" role="tablist">
        <li class="nav-item">
            <button class="nav-link active d-flex align-items-center gap-2" data-bs-toggle="pill" data-bs-target="#normada" type="button">
                <i class="bi bi-book-fill"></i> Guru Normada 
                <span class="badge bg-light text-dark ms-1">{{ $guruNormada->count() }}</span>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link d-flex align-items-center gap-2" data-bs-toggle="pill" data-bs-target="#produktif" type="button">
                <i class="bi bi-briefcase-fill"></i> Guru Produktif 
                <span class="badge bg-light text-dark ms-1">{{ $guruProduktif->count() }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="guruTabContent">
        
        {{-- TAB GURU NORMADA --}}
        <div class="tab-pane fade show active" id="normada" role="tabpanel">
            @if($guruNormada->isEmpty())
                <div class="alert alert-info"><i class="bi bi-info-circle-fill me-2"></i>Tidak ada guru normada di kelas Anda.</div>
            @else
                <div class="row g-4">
                    @foreach($guruNormada as $g)
                        @php
                            $persentase = $g->getPersentasePartisipasiDiKelas($kelasAktif->id, $periodeId);
                            $jumlahMenilai = $g->getJumlahSiswaMenilaiDiKelas($kelasAktif->id, $periodeId);
                            $totalSiswa = $kelasAktif->jumlah_siswa;
                            $warna = $persentase >= 80 ? '#22c55e' : ($persentase >= 50 ? '#f59e0b' : '#ef4444');
                        @endphp
                        <div class="col-md-6 col-lg-4">
                            <div class="card-custom p-4 h-100" style="transition: all 0.3s; border-top: 4px solid var(--primary);" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                                <div class="text-center mb-3">
                                    <img src="{{ $g->photo_url }}" alt="{{ $g->nama }}" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover; border: 4px solid var(--primary);">
                                </div>
                                <h5 class="fw-bold text-center mb-2">{{ $g->nama }}</h5>
                                <div class="text-center mb-3">
                                    <span class="badge-custom" style="background: rgba(0,51,102,0.1); color: var(--primary);">NORMADA</span>
                                </div>
                                
                                <div class="text-center mb-3 p-3 rounded" style="background: {{ $warna }}15; border: 2px solid {{ $warna }};">
                                    <div class="text-muted small mb-1">Partisipasi Evaluasi</div>
                                    <div class="fw-bold" style="color: {{ $warna }}; font-size: 2rem; line-height: 1;">
                                        {{ number_format($persentase, 1) }}%
                                    </div>
                                    <div class="text-muted small mt-1">
                                        {{ $jumlahMenilai }} dari {{ $totalSiswa }} siswa
                                    </div>
                                </div>

                                @php $rataEvaluasi = $g->getRataRataEvaluasiDiKelas($kelasAktif->id, $periodeId); @endphp
                                @if($rataEvaluasi > 0)
                                <div class="small text-muted text-center mb-3">
                                    <div class="mb-1"><strong>Hasil Evaluasi:</strong></div>
                                    <div class="d-flex justify-content-around flex-wrap" style="font-size: 0.75rem;">
                                        <span>📚 {{ number_format($rataEvaluasi, 1) }}/5</span>
                                    </div>
                                </div>
                                @endif

                                <a href="{{ route('siswa.guru.show', $g) }}" class="btn btn-primary-custom w-100">
                                    <i class="bi bi-eye me-1"></i> Lihat Detail & Beri Evaluasi
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- TAB GURU PRODUKTIF --}}
        <div class="tab-pane fade" id="produktif" role="tabpanel">
            @if($guruProduktif->isEmpty())
                <div class="alert alert-info"><i class="bi bi-info-circle-fill me-2"></i>Tidak ada guru produktif di kelas Anda.</div>
            @else
                <div class="row g-4">
                    @foreach($guruProduktif as $g)
                        @php
                            $persentase = $g->getPersentasePartisipasiDiKelas($kelasAktif->id, $periodeId);
                            $jumlahMenilai = $g->getJumlahSiswaMenilaiDiKelas($kelasAktif->id, $periodeId);
                            $totalSiswa = $kelasAktif->jumlah_siswa;
                            $warna = $persentase >= 80 ? '#22c55e' : ($persentase >= 50 ? '#f59e0b' : '#ef4444');
                        @endphp
                        <div class="col-md-6 col-lg-4">
                            <div class="card-custom p-4 h-100" style="transition: all 0.3s; border-top: 4px solid var(--accent);" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                                <div class="text-center mb-3">
                                    <img src="{{ $g->photo_url }}" alt="{{ $g->nama }}" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover; border: 4px solid var(--accent);">
                                </div>
                                <h5 class="fw-bold text-center mb-2">{{ $g->nama }}</h5>
                                <div class="text-center mb-3">
                                    <span class="badge-custom" style="background: rgba(0,168,107,0.1); color: var(--accent);">PRODUKTIF</span>
                                    @if($g->jurusan)
                                        <span class="badge-custom" style="background: rgba(255,193,7,0.15); color: #d4a017;">{{ $g->jurusan->nama_jurusan }}</span>
                                    @endif
                                </div>
                                
                                <div class="text-center mb-3 p-3 rounded" style="background: {{ $warna }}15; border: 2px solid {{ $warna }};">
                                    <div class="text-muted small mb-1">Partisipasi Evaluasi</div>
                                    <div class="fw-bold" style="color: {{ $warna }}; font-size: 2rem; line-height: 1;">
                                        {{ number_format($persentase, 1) }}%
                                    </div>
                                    <div class="text-muted small mt-1">
                                        {{ $jumlahMenilai }} dari {{ $totalSiswa }} siswa
                                    </div>
                                </div>

                                @php $rataEvaluasi = $g->getRataRataEvaluasiDiKelas($kelasAktif->id, $periodeId); @endphp
                                @if($rataEvaluasi > 0)
                                <div class="small text-muted text-center mb-3">
                                    <div class="mb-1"><strong>Hasil Evaluasi:</strong></div>
                                    <div class="d-flex justify-content-around flex-wrap" style="font-size: 0.75rem;">
                                        <span>📚 {{ number_format($rataEvaluasi, 1) }}/5</span>
                                    </div>
                                </div>
                                @endif

                                <a href="{{ route('siswa.guru.show', $g) }}" class="btn btn-primary-custom w-100">
                                    <i class="bi bi-eye me-1"></i> Lihat Detail & Beri Evaluasi
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
@endif
@endsection