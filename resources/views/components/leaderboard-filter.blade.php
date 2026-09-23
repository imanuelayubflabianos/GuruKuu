<div class="card-custom p-3 mb-4" data-aos="fade-up">
    <form method="GET" action="{{ url()->current() }}" class="row g-3 align-items-end">
        <div class="col-lg-5">
            <label class="form-label small fw-bold text-muted">Pilih tampilan leaderboard</label>
            <div class="d-flex flex-wrap gap-3 pt-2">
                <label class="form-check d-flex align-items-center gap-2 mb-0">
                    <input class="form-check-input" type="radio" name="mode" value="rating" {{ $mode === 'rating' ? 'checked' : '' }}>
                    <span class="small">Rating semua guru</span>
                </label>
                <label class="form-check d-flex align-items-center gap-2 mb-0">
                    <input class="form-check-input" type="radio" name="mode" value="partisipasi" {{ $mode === 'partisipasi' ? 'checked' : '' }}>
                    <span class="small">Partisipasi berdasarkan kelas</span>
                </label>
            </div>
        </div>
        <div class="col-lg-5">
            <label class="form-label small fw-bold text-muted" for="leaderboardKelas">Kelas yang diajar</label>
            <select name="kelas_id" id="leaderboardKelas" class="form-select" {{ $mode === 'rating' ? 'disabled' : '' }}>
                <option value="">{{ $mode === 'partisipasi' ? 'Pilih kelas' : 'Semua kelas' }}</option>
                @foreach($kelasList as $kelas)
                    <option value="{{ $kelas->id }}" {{ (string) $kelasId === (string) $kelas->id ? 'selected' : '' }}>{{ $kelas->label_singkat }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary-custom flex-grow-1"><i class="bi bi-funnel me-1"></i> Terapkan</button>
            <a href="{{ url()->current() }}" class="btn btn-outline-custom" title="Reset filter" aria-label="Reset filter"><i class="bi bi-arrow-counterclockwise"></i></a>
        </div>
    </form>
</div>

<div class="small text-muted mb-4">
    @if($mode === 'partisipasi')
        <i class="bi bi-calculator me-1"></i>Persentase partisipasi = siswa yang memilih guru dibagi jumlah siswa di kelas dikali 100%.
    @else
        <i class="bi bi-star me-1"></i>Menampilkan peringkat berdasarkan rating kepuasan guru.
    @endif
</div>

@push('scripts')
<script>
document.querySelectorAll('input[name="mode"]').forEach(function (radio) {
    radio.addEventListener('change', function () {
        const select = document.getElementById('leaderboardKelas');
        if (!select) return;
        select.disabled = this.value === 'rating';
        if (this.value === 'rating') {
            this.form.submit();
        } else {
            select.focus();
            if (select.value) {
                this.form.submit();
            }
        }
    });
});

const selectKelas = document.getElementById('leaderboardKelas');
if (selectKelas) {
    selectKelas.addEventListener('change', function () {
        this.form.submit();
    });
}
</script>
@endpush
