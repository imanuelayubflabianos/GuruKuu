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
    if (form) form.submit();
};
</script>
