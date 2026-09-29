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
        <i class="bi bi-trophy-fill text-warning"></i>
        <span>Menampilkan seluruh guru sekolah berdasarkan rating kepuasan evaluasi tertinggi.</span>
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
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    box-shadow: 0 10px 28px -6px rgba(0, 0, 0, 0.06);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.gk-podium-card-revised:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 36px -8px rgba(0, 0, 0, 0.1);
}
.gk-podium-card-revised.is-first {
    border: 2px solid rgba(217, 119, 6, 0.28) !important;
    box-shadow: 0 16px 36px -8px rgba(217, 119, 6, 0.16) !important;
}
.gk-avatar-red-wrap {
    width: 104px;
    height: 104px;
    border-radius: 50%;
    background: #ef4444;
    padding: 3px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.75rem;
    box-shadow: 0 6px 16px -3px rgba(239, 68, 68, 0.35);
}
.gk-avatar-red-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}
.gk-badge-mini-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 700;
    border: 1px solid;
    transition: transform 0.15s ease;
    text-decoration: none;
    line-height: 1;
}
.gk-badge-mini-icon:hover {
    transform: scale(1.15);
}
.gk-progress-pill {
    background: #475569;
    border-radius: 50px;
    color: #ffffff;
    font-weight: 700;
    font-size: 0.85rem;
    padding: 0.35rem 1rem;
    display: inline-block;
    min-width: 130px;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.gk-progress-pill-fill {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    background: rgba(255, 255, 255, 0.25);
    border-radius: 50px;
}
</style>
