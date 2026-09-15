@if($teachers->isEmpty())
    <div class="alert alert-info text-center py-4">
        <i class="bi bi-info-circle fs-3 d-block mb-2"></i>
        Tidak ada data guru yang sesuai dengan kriteria pencarian.
    </div>
@else
    <div class="row g-4">
        @foreach($teachers as $g)
            @php
                $isSudahDinilai = in_array($g->id, $sudahMenilaiIds ?? []);
                $persen = round(($g->rata_rata_nilai / 5) * 100);
            @endphp
            <div class="col-md-6 col-lg-4">
                <div class="card-custom p-4 h-100 d-flex flex-column justify-content-between" 
                     style="transition: all 0.3s; border-top: 4px solid var(--primary);" 
                     onmouseover="this.style.transform='translateY(-5px)'" 
                     onmouseout="this.style.transform='translateY(0)'">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-light text-primary border font-mono" style="font-size: 0.72rem; padding: 0.35rem 0.65rem;">
                                <i class="bi bi-mortarboard me-1"></i> {{ $g->jurusan?->nama_jurusan ?? 'Guru Pengajar' }}
                            </span>
                            @if($isSudahDinilai)
                                <span class="badge bg-success d-flex align-items-center gap-1">
                                    <i class="bi bi-check-circle-fill"></i> Sudah Dinilai
                                </span>
                            @else
                                <span class="badge bg-warning text-dark d-flex align-items-center gap-1">
                                    <i class="bi bi-hourglass-split"></i> Belum Dinilai
                                </span>
                            @endif
                        </div>

                        <div class="text-center mb-3">
                            <img src="{{ $g->photo_url }}" alt="{{ $g->nama }}" class="rounded-circle shadow-sm" 
                                 style="width: 90px; height: 90px; object-fit: cover; border: 3px solid var(--primary);"
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($g->nama) }}&background=003366&color=fff'">
                        </div>

                        <h5 class="fw-bold text-center mb-1" style="color: var(--text-dark);">{{ $g->nama }}</h5>
                        <div class="text-muted small text-center mb-2 font-mono">NIP: {{ $g->nip }}</div>

                        @if($g->bio)
                            <p class="small text-muted text-center mb-3" style="min-height: 38px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $g->bio }}
                            </p>
                        @else
                            <div class="mb-3" style="min-height: 20px;"></div>
                        @endif

                        {{-- Rating Summary Bar Persen --}}
                        <div class="p-3 rounded mb-3" style="background: var(--bg-light); border: 1px solid var(--border);">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted font-mono fw-semibold" style="font-size: 0.72rem;">RATING KEPUASAN</small>
                                <span class="fw-bold text-primary font-mono" style="font-size: 0.9rem;">{{ $persen }}%</span>
                            </div>
                            <div class="progress" style="height: 7px; background-color: #e2e8f0; border-radius: 10px;">
                                <div class="progress-bar bg-primary rounded-pill" role="progressbar" style="width: {{ $persen }}%;" aria-valuenow="{{ $persen }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2 pt-1 border-top" style="font-size: 0.75rem;">
                                <span class="text-muted">Total Penilaian:</span>
                                <span class="fw-bold text-dark font-mono">{{ $g->total_penilaian }} Ulasan</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-2">
                        <a href="{{ route('siswa.guru.show', $g) }}" class="btn btn-outline-custom flex-grow-1 btn-sm">
                            <i class="bi bi-info-circle me-1"></i> Detail
                        </a>
                        @if($isSudahDinilai)
                            <a href="{{ route('siswa.guru.show', $g) }}" class="btn btn-secondary flex-grow-1 btn-sm disabled">
                                <i class="bi bi-check2 me-1"></i> Selesai
                            </a>
                        @else
                            <a href="{{ route('siswa.penilaian.create', $g) }}" class="btn btn-primary-custom flex-grow-1 btn-sm">
                                <i class="bi bi-pencil-square me-1"></i> Nilai
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
