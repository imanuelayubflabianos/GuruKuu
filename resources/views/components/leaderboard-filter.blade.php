<div class="card-custom p-3 p-md-4 mb-4 shadow-sm" style="border-radius: 16px;" data-aos="fade-up">
    <form method="GET" action="{{ url()->current() }}" id="leaderboardFilterForm" class="row g-3 align-items-center">
        <input type="hidden" name="mode" id="leaderboardModeInput" value="{{ $mode }}">

        <div class="col-12 col-lg-7">
            <label class="form-label small fw-bold text-muted mb-2 d-flex align-items-center gap-1">
                <i class="bi bi-funnel-fill text-primary"></i>
                <span>Kategori Leaderboard</span>
            </label>
            <div class="d-flex flex-wrap gap-2">
                {{-- Tombol 1: Semua Guru --}}
                <button type="button" 
                        class="btn {{ $mode === 'rating' ? 'btn-primary-custom shadow-sm' : 'btn-outline-custom' }} d-inline-flex align-items-center gap-2 py-2 px-3 rounded-3 border fw-semibold"
                        style="font-size: 0.86rem;"
                        onclick="setLeaderboardMode('rating')">
                    <i class="bi bi-people-fill {{ $mode === 'rating' ? 'text-white' : 'text-primary' }}"></i>
                    <span>Semua Guru</span>
                </button>

                {{-- Tombol 2: Guru yang Mengajar Berdasarkan Kelas --}}
                <button type="button" 
                        class="btn {{ $mode === 'partisipasi' ? 'btn-primary-custom shadow-sm' : 'btn-outline-custom' }} d-inline-flex align-items-center gap-2 py-2 px-3 rounded-3 border fw-semibold"
                        style="font-size: 0.86rem;"
                        onclick="setLeaderboardMode('partisipasi')">
                    <i class="bi bi-door-open-fill {{ $mode === 'partisipasi' ? 'text-white' : 'text-primary' }}"></i>
                    <span>Guru yang Mengajar Berdasarkan Kelas</span>
                </button>
            </div>
        </div>

        {{-- Dropdown Kelas di Samping --}}
        <div class="col-12 col-lg-5">
            <label class="form-label small fw-bold text-muted mb-2 d-flex align-items-center justify-content-between" for="leaderboardKelas">
                <span><i class="bi bi-mortarboard me-1 text-primary"></i>Pilih Kelas:</span>
                @if($mode === 'rating')
                    <span class="badge bg-light text-muted border" style="font-size: 0.7rem; font-weight: normal;">Aktif saat filter kelas dipilih</span>
                @endif
            </label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 {{ $mode === 'partisipasi' ? 'text-primary' : 'text-muted' }}">
                    <i class="bi bi-building"></i>
                </span>
                <select name="kelas_id" 
                        id="leaderboardKelas" 
                        class="form-select border-start-0 fw-semibold {{ $mode === 'partisipasi' ? 'bg-white' : 'bg-light text-muted' }}" 
                        {{ $mode === 'rating' ? 'disabled' : '' }}
                        onchange="onSelectKelasChange(this)"
                        style="border-radius: 0 10px 10px 0;">
                    <option value="" disabled {{ empty($kelasId) ? 'selected' : '' }}>-- Pilih Kelas --</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ (string)$kelasId === (string)$kelas->id ? 'selected' : '' }}>
                            Kelas {{ $kelas->label_singkat }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>
</div>

<div class="small text-muted mb-4 d-flex align-items-center gap-1.5">
    @if($mode === 'partisipasi' && !empty($kelasId))
        <i class="bi bi-door-open-fill text-primary"></i>
        <span>Menampilkan guru yang mengajar <strong>Kelas {{ $kelasList->firstWhere('id', $kelasId)?->label_singkat }}</strong> berdasarkan persentase partisipasi siswa di kelas tersebut.</span>
    @else
    @endif
</div>

<script>
window.setLeaderboardMode = function(mode) {
    const modeInput = document.getElementById('leaderboardModeInput');
    const selectKelas = document.getElementById('leaderboardKelas');
    const form = document.getElementById('leaderboardFilterForm');
    if (!modeInput || !selectKelas || !form) return;

    modeInput.value = mode;

    if (mode === 'rating') {
        selectKelas.value = '';
        selectKelas.disabled = true;
        form.submit();
    } else {
        selectKelas.disabled = false;
        selectKelas.classList.remove('bg-light', 'text-muted');
        selectKelas.classList.add('bg-white');

        if (selectKelas.value) {
            form.submit();
        } else {
            if (selectKelas.options.length > 1) {
                selectKelas.selectedIndex = 1;
                form.submit();
            } else {
                selectKelas.focus();
            }
        }
    }
};

window.onSelectKelasChange = function(select) {
    const modeInput = document.getElementById('leaderboardModeInput');
    const form = document.getElementById('leaderboardFilterForm');
    if (modeInput) modeInput.value = 'partisipasi';
};
</script>

<style>
.gk-podium-card-revised {
    background: #ffffff;
    border: none !important;
    border-radius: 22px;
    box-shadow: 0 10px 25px -4px rgba(15, 23, 42, 0.08), 0 4px 10px -2px rgba(15, 23, 42, 0.04);
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.gk-podium-card-revised:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 35px -6px rgba(15, 23, 42, 0.12), 0 8px 16px -3px rgba(15, 23, 42, 0.06);
}
.gk-podium-card-revised.is-first {
    border: none !important;
    box-shadow: 0 20px 45px -8px rgba(15, 23, 42, 0.16), 0 8px 18px -4px rgba(15, 23, 42, 0.08) !important;
}

/* 🌟 TAMPILAN PODIUM BERTINGKAT (DASAR BAWAH SEJAJAR, TINGGI KE ATAS BERTINGKAT) */
@media (min-width: 768px) {
    .gk-podium-row {
        display: flex !important;
        align-items: flex-end !important;
    }
    .gk-podium-col.gk-podium-1,
    .gk-podium-1 {
        z-index: 10;
        transform: none;
    }
    .gk-podium-col.gk-podium-2,
    .gk-podium-2 {
        z-index: 5;
        transform: none;
    }
    .gk-podium-col.gk-podium-3,
    .gk-podium-3 {
        z-index: 2;
        transform: none;
    }

    .gk-podium-col.gk-podium-1:hover .gk-podium-card-revised {
        transform: translateY(-6px);
        box-shadow: 0 25px 50px -8px rgba(15, 23, 42, 0.2) !important;
    }
    .gk-podium-col.gk-podium-2:hover .gk-podium-card-revised {
        transform: translateY(-5px);
        box-shadow: 0 18px 38px -6px rgba(15, 23, 42, 0.15) !important;
    }
    .gk-podium-col.gk-podium-3:hover .gk-podium-card-revised {
        transform: translateY(-5px);
        box-shadow: 0 16px 32px -6px rgba(15, 23, 42, 0.12) !important;
    }

    .gk-podium-1 .gk-podium-card-revised {
        min-height: 480px !important;
        border: none !important;
        box-shadow: 0 20px 45px -8px rgba(15, 23, 42, 0.16), 0 8px 18px -4px rgba(15, 23, 42, 0.08) !important;
    }
    .gk-podium-2 .gk-podium-card-revised {
        min-height: 440px !important;
        border: none !important;
        box-shadow: 0 14px 32px -6px rgba(15, 23, 42, 0.11), 0 6px 14px -3px rgba(15, 23, 42, 0.05) !important;
    }
    .gk-podium-3 .gk-podium-card-revised {
        min-height: 410px !important;
        border: none !important;
        box-shadow: 0 10px 25px -4px rgba(15, 23, 42, 0.08), 0 4px 10px -2px rgba(15, 23, 42, 0.04) !important;
    }
}
@media (max-width: 767.98px) {
    .gk-podium-col.gk-podium-1,
    .gk-podium-col.gk-podium-2,
    .gk-podium-col.gk-podium-3 {
        transform: none !important;
    }
}
.gk-avatar-red-wrap,
.gk-avatar-clean-wrap {
    border-radius: 50% !important;
    background: transparent !important;
    padding: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin: 0 auto 0.75rem !important;
    box-shadow: 0 8px 20px -3px rgba(15, 23, 42, 0.12) !important;
    border: none !important;
}
.gk-avatar-red-wrap img,
.gk-avatar-clean-wrap img {
    object-fit: cover !important;
    border-radius: 50% !important;
    border: 3px solid #ffffff !important;
    box-shadow: 0 0 0 1px rgba(15, 23, 42, 0.08) !important;
}
.gk-badge-mini-icon {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 14px !important;
    height: 14px !important;
    min-width: 14px !important;
    border-radius: 3px !important;
    font-size: 0.55rem !important;
    line-height: 1 !important;
    border: 0.8px solid rgba(0, 0, 0, 0.08) !important;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease !important;
    text-decoration: none !important;
    cursor: pointer !important;
    vertical-align: middle !important;
    padding: 0 !important;
}
.gk-badge-mini-icon i {
    font-size: 0.52rem !important;
    line-height: 1 !important;
}
.gk-badge-mini-icon:hover {
    transform: translateY(-1px) scale(1.4) !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
    z-index: 5 !important;
}
.gk-progress-pill {
    background: #0f172a !important;
    border-radius: 50px !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    font-size: 0.75rem !important;
    padding: 0 0.6rem !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    min-width: 120px !important;
    max-width: 100% !important;
    height: 23px !important;
    line-height: 23px !important;
    text-align: center !important;
    position: relative !important;
    overflow: hidden !important;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.12) !important;
}
.gk-progress-pill-fill {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    border-radius: 50px;
    background: linear-gradient(90deg, #0284c7, #38bdf8);
    transition: width 1.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.gk-pill-rank-1,
.gk-pill-rank-2,
.gk-pill-rank-3 {
    background: #0f172a !important;
    border: 1px solid rgba(59, 130, 246, 0.4) !important;
}
.gk-table-progress-wrap {
    background: #e2e8f0 !important;
    border: 1px solid rgba(15, 23, 42, 0.08) !important;
    border-radius: 99px !important;
    height: 8px !important;
    overflow: hidden;
    box-shadow: inset 0 1px 2px rgba(0,0,0,0.06);
}
.gk-table-progress-fill {
    height: 100%;
    border-radius: 99px;
    background: linear-gradient(90deg, #0284c7, #38bdf8) !important;
    transition: width 0.8s ease;
}
.gk-bar-blue-high {
    background: linear-gradient(90deg, #003366, #2563eb) !important;
}
.gk-bar-blue-mid {
    background: linear-gradient(90deg, #0284c7, #38bdf8) !important;
}
.gk-bar-blue-low {
    background: linear-gradient(90deg, #38bdf8, #93c5fd) !important;
}
</style>
