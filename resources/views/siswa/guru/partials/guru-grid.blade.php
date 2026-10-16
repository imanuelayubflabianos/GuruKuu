@if($teachers->isEmpty())
    <div class="alert alert-info text-center py-4">
        <i class="bi bi-info-circle fs-3 d-block mb-2"></i>
        Tidak ada data guru yang sesuai dengan kriteria pencarian.
    </div>
@else
    <div class="row g-4">
        @foreach($teachers as $g)
            @php
                $myPenilaian = isset($penilaianSayaMap) ? $penilaianSayaMap->get($g->id) : null;
                $isSudahDinilai = (bool) $myPenilaian;
                $isGuruKelas = isset($guruKelasSayaIds) ? in_array($g->id, $guruKelasSayaIds) : true;

                if ($isSudahDinilai) {
                    $myRating = $myPenilaian->rata_rata_evaluasi ?? round(($myPenilaian->total_nilai / 5), 2);
                    $persen = round(($myRating / 5) * 100);
                    $valGrid = $persen / 20;
                    $starsGrid = round($valGrid * 2) / 2;
                } else {
                    $myRating = 0;
                    $persen = 0;
                    $valGrid = 0;
                    $starsGrid = 0;
                }
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
                            @if($g->kategori)
                                <span class="badge bg-light text-muted border font-mono text-uppercase" style="font-size: 0.68rem; padding: 0.35rem 0.55rem;">
                                    {{ $g->kategori }}
                                </span>
                            @endif
                        </div>

                        <div class="text-center mb-3">
                            <img src="{{ $g->photo_url }}" alt="{{ $g->nama }}" class="rounded-circle shadow-sm flex-shrink-0" 
                                 loading="lazy" decoding="async"
                                 style="width: 90px; height: 90px; min-width: 90px; min-height: 90px; aspect-ratio: 1 / 1; object-fit: cover; border: 3px solid var(--primary); flex-shrink: 0;"
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($g->nama) }}&background=003366&color=fff'">
                        </div>

                        <h5 class="fw-bold text-center mb-3" style="color: var(--text-dark);">{{ $g->nama }}</h5>

                        {{-- Rating Khusus Siswa Ini (0% jika belum menilai, atau Skor Siswa jika sudah menilai) --}}
                        <div class="p-3 rounded mb-3" style="background: var(--bg-light); border: 1px solid var(--border);">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted font-mono fw-semibold" style="font-size: 0.72rem;">
                                    {{ $isSudahDinilai ? 'NILAI DARI ANDA' : 'PENILAIAN ANDA' }}
                                </small>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge {{ $isSudahDinilai ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-secondary-subtle text-muted border' }} font-mono fw-bold px-2 py-0.5 rounded" style="font-size: 0.8rem;">
                                        {{ $persen }}%
                                    </span>
                                    <span class="fw-bold {{ $isSudahDinilai ? 'text-dark' : 'text-muted' }} font-mono" style="font-size: 0.88rem;">
                                        {{ $isSudahDinilai ? number_format($myRating, 1) : '0.0' }}
                                    </span>
                                    <span class="text-warning d-inline-flex align-items-center gap-0.5" style="font-size: 0.8rem;">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($starsGrid >= $i)
                                                <i class="bi bi-star-fill"></i>
                                            @elseif($starsGrid >= ($i - 0.5))
                                                <i class="bi bi-star-half"></i>
                                            @else
                                                <i class="bi bi-star text-muted opacity-25"></i>
                                            @endif
                                        @endfor
                                    </span>
                                </div>
                            </div>
                            <div class="progress" style="height: 7px; background-color: #e2e8f0; border-radius: 10px;">
                                <div class="progress-bar {{ $isSudahDinilai ? 'bg-primary' : 'bg-secondary opacity-25' }} rounded-pill" role="progressbar" style="width: {{ $persen }}%;" aria-valuenow="{{ $persen }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                                <span class="text-muted fw-semibold" style="font-size: 0.76rem;">Status:</span>
                                @if($isSudahDinilai)
                                    <span class="badge bg-success px-3 py-1.5 rounded-pill fw-bold d-inline-flex align-items-center gap-2 shadow-xs" style="font-size: 0.76rem; letter-spacing: 0.2px;">
                                        <i class="bi bi-check-circle-fill"></i> Sudah Dinilai
                                    </span>
                                @elseif(!$isGuruKelas)
                                    <span class="badge bg-secondary-subtle text-muted border px-3 py-1.5 rounded-pill fw-semibold d-inline-flex align-items-center gap-2" style="font-size: 0.76rem; letter-spacing: 0.2px;">
                                        <i class="bi bi-slash-circle"></i> Bukan Guru Kelas
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill fw-bold d-inline-flex align-items-center gap-2 shadow-xs" style="font-size: 0.76rem; letter-spacing: 0.2px;">
                                        <i class="bi bi-hourglass-split"></i> Belum Dinilai
                                    </span>
                                @endif
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
                        @elseif(!$isGuruKelas)
                            <button type="button" class="btn btn-light border text-muted flex-grow-1 btn-sm" disabled title="Guru ini tidak mengajar di kelas Anda">
                                <i class="bi bi-slash-circle me-1"></i> Luar Kelas
                            </button>
                        @elseif(isset($mInfo) && $mInfo['is_active'])
                            <button type="button" class="btn btn-warning flex-grow-1 btn-sm text-dark fw-semibold" onclick="showMaintenanceModal()">
                                <i class="bi bi-tools me-1"></i> Nilai
                            </button>
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
