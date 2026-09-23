@extends('layouts.siswa')
@section('title', 'Daftar Guru')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <div class="page-label">EVALUASI GURU</div>
        <h1 class="page-title">Daftar Guru</h1>
        <p class="page-subtitle mb-0">
            Pilih guru yang mengajar kelas Anda atau lihat seluruh guru sekolah.
            @if($kelasAktif)
                <span class="badge bg-primary ms-2">{{ $kelasAktif->nama_kelas }} Kelas {{ $kelasAktif->tingkat }}</span>
            @endif
        </p>
    </div>
    <div>
        <span class="badge bg-white text-dark border px-3 py-2 font-mono" style="font-size: 0.85rem;">
            Total: <strong>{{ $guru->count() }} Guru</strong>
        </span>
    </div>
</div>

{{-- SEARCH BAR --}}
<div class="card-custom p-3 mb-4 position-relative" style="z-index: 50; overflow: visible;">
    <form method="GET" action="{{ route('siswa.guru.index') }}" class="row g-3 align-items-end mb-3">
        <div class="col-md-8">
            <label class="form-label small fw-bold text-muted mb-2"><i class="bi bi-funnel text-primary me-1"></i>Cakupan Daftar Guru</label>
            <div class="d-flex flex-wrap gap-2">
                <label class="btn {{ $mode === 'kelas' ? 'btn-primary-custom' : 'btn-outline-custom' }} d-inline-flex align-items-center gap-2 py-2 px-3 rounded-3 shadow-sm border" style="cursor: pointer; font-size: 0.85rem; font-weight: 600;">
                    <input class="d-none" type="radio" name="mode" value="kelas" {{ $mode === 'kelas' ? 'checked' : '' }} onchange="this.form.submit()">
                    <i class="bi bi-door-open-fill {{ $mode === 'kelas' ? 'text-white' : 'text-primary' }}"></i>
                    <span>Guru yang Mengajar Kelas Saya</span>
                </label>
                <label class="btn {{ $mode === 'semua' ? 'btn-primary-custom' : 'btn-outline-custom' }} d-inline-flex align-items-center gap-2 py-2 px-3 rounded-3 shadow-sm border" style="cursor: pointer; font-size: 0.85rem; font-weight: 600;">
                    <input class="d-none" type="radio" name="mode" value="semua" {{ $mode === 'semua' ? 'checked' : '' }} onchange="this.form.submit()">
                    <i class="bi bi-people-fill {{ $mode === 'semua' ? 'text-white' : 'text-primary' }}"></i>
                    <span>Semua Guru Sekolah</span>
                </label>
            </div>
        </div>
    </form>
    <form method="GET" action="{{ route('siswa.guru.index') }}" class="row g-2 align-items-center" id="searchTeacherForm">
        <input type="hidden" name="mode" value="{{ $mode }}">
        <div class="col-md-10 position-relative" style="z-index: 55;">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" id="guruSearchInput" class="form-control border-start-0" placeholder="Cari nama guru..." value="{{ request('search') }}" autocomplete="off">
            </div>
            <div id="guruSearchSuggestions" class="list-group position-absolute w-100 shadow-lg border mt-1" style="display: none; z-index: 1060; max-height: 250px; overflow-y: auto; border-radius: 10px; background: var(--bg-card, #ffffff);"></div>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary-custom flex-grow-1"><i class="bi bi-search me-1"></i> Cari</button>
            @if(request('search'))
                <a href="{{ route('siswa.guru.index') }}" class="btn btn-outline-custom" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
            @endif
        </div>
    </form>
</div>

{{-- GRID GURU UNIFIED --}}
@include('siswa.guru.partials.guru-grid', ['teachers' => $guru])

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('guruSearchInput');
    const suggestions = document.getElementById('guruSearchSuggestions');
    const form = document.getElementById('searchTeacherForm');
    const names = @json($allTeacherNames ?? []);

    if (!input || !suggestions || !form) return;

    function renderSuggestions(query) {
        const q = query.trim().toLowerCase();
        if (!q) {
            suggestions.style.display = 'none';
            suggestions.innerHTML = '';
            return;
        }

        const matches = names.filter(function (name) {
            return name.toLowerCase().includes(q);
        }).slice(0, 8);

        if (matches.length === 0) {
            suggestions.style.display = 'none';
            suggestions.innerHTML = '';
            return;
        }

        suggestions.innerHTML = matches.map(function (name) {
            // Highlight matching part
            const regex = new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
            const highlighted = name.replace(regex, '<span class="text-primary fw-bold">$1</span>');
            return `<button type="button" class="list-group-item list-group-item-action py-2 px-3 small d-flex align-items-center justify-content-between text-start" data-name="${name.replace(/"/g, '&quot;')}">
                <span><i class="bi bi-person me-2 text-muted"></i>${highlighted}</span>
                <i class="bi bi-arrow-return-left text-muted small"></i>
            </button>`;
        }).join('');

        suggestions.style.display = 'block';

        suggestions.querySelectorAll('button[data-name]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                input.value = this.getAttribute('data-name');
                suggestions.style.display = 'none';
                form.submit();
            });
        });
    }

    input.addEventListener('input', function () {
        renderSuggestions(this.value);
    });

    input.addEventListener('focus', function () {
        if (this.value) {
            renderSuggestions(this.value);
        }
    });

    document.addEventListener('click', function (e) {
        if (!input.contains(e.target) && !suggestions.contains(e.target)) {
            suggestions.style.display = 'none';
        }
    });
});
</script>
@endpush

@endsection