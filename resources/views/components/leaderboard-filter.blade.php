<div class="card-custom p-3 p-md-4 mb-4 shadow-sm" style="border-radius: 16px;" data-aos="fade-up">
    <form method="GET" action="{{ url()->current() }}" id="leaderboardFilterForm" class="row g-3 align-items-center">
        <input type="hidden" name="mode" id="leaderboardModeInput" value="{{ $mode }}">

        <div class="col-12 {{ $mode === 'partisipasi' ? 'col-lg-7' : 'col-lg-12' }}" id="leaderboardCategoryCol">
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

        {{-- Dropdown Kelas: Hanya muncul saat memilih berdasarkan kelas --}}
        <div class="col-12 col-lg-5" id="leaderboardKelasCol" style="{{ $mode === 'partisipasi' ? '' : 'display: none;' }}">
            <label class="form-label small fw-bold text-muted mb-2 d-flex align-items-center" for="leaderboardKelas">
                <i class="bi bi-mortarboard me-1 text-primary"></i>
                <span>Pilih Kelas:</span>
            </label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 text-primary">
                    <i class="bi bi-building"></i>
                </span>
                <select name="kelas_id" 
                        id="leaderboardKelas" 
                        class="form-select border-start-0 fw-semibold bg-white" 
                        {{ $mode === 'rating' ? 'disabled' : '' }}
                        onchange="onSelectKelasChange(this)"
                        style="border-radius: 0 10px 10px 0;">
                    <option value="" disabled {{ empty($kelasId) ? 'selected' : '' }}>-- Pilih Kelas --</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ (string)$kelasId === (string)$kelas->id ? 'selected' : '' }}>
                            {{ $kelas->label_singkat }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>
</div>

<script>
window.setLeaderboardMode = function(mode) {
    const modeInput = document.getElementById('leaderboardModeInput');
    const selectKelas = document.getElementById('leaderboardKelas');
    const kelasCol = document.getElementById('leaderboardKelasCol');
    const categoryCol = document.getElementById('leaderboardCategoryCol');
    const form = document.getElementById('leaderboardFilterForm');
    if (!modeInput || !selectKelas || !form) return;

    modeInput.value = mode;

    if (mode === 'rating') {
        selectKelas.value = '';
        selectKelas.disabled = true;
        if (kelasCol) kelasCol.style.display = 'none';
        if (categoryCol) {
            categoryCol.className = 'col-12 col-lg-12';
        }
        form.submit();
    } else {
        if (kelasCol) kelasCol.style.display = 'block';
        if (categoryCol) {
            categoryCol.className = 'col-12 col-lg-7';
        }
        selectKelas.disabled = false;

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

document.addEventListener('DOMContentLoaded', function() {
    const podiumRows = document.querySelectorAll('.gk-podium-row');
    podiumRows.forEach(function(row) {
        const cols = row.querySelectorAll('.gk-podium-col, .podium-anim-item, .col-4');
        cols.forEach(function(col) {
            col.addEventListener('pointerenter', function() {
                cols.forEach(c => c.classList.remove('is-touch-focused'));
                col.classList.add('is-touch-focused');
            });
            col.addEventListener('touchstart', function(e) {
                cols.forEach(c => c.classList.remove('is-touch-focused'));
                col.classList.add('is-touch-focused');
            }, {passive: true});
        });
        document.addEventListener('click', function(e) {
            if (!row.contains(e.target)) {
                cols.forEach(c => c.classList.remove('is-touch-focused'));
            }
        });
    });
});
</script>

<style>
.gk-podium-card-revised {
    background: #ffffff;
    border-radius: 22px;
    position: relative !important;
    overflow: visible !important;
    padding-top: 2.35rem !important;
    transition: opacity 0.35s cubic-bezier(0.16, 1, 0.3, 1), 
                filter 0.35s cubic-bezier(0.16, 1, 0.3, 1), 
                transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), 
                box-shadow 0.35s cubic-bezier(0.16, 1, 0.3, 1),
                border-color 0.3s ease !important;
    will-change: opacity, filter, transform;
}

/* 🌿 MAHKOTA DAUN LAUREL ROMAWI & BADGE #1 #2 #3 DI ATAS GARIS TEPI */
.gk-podium-header-badge {
    position: absolute;
    top: 0;
    left: 50%;
    transform: translate(-50%, -50%);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    z-index: 15;
    pointer-events: none;
    white-space: nowrap;
}
.gk-laurel-badge {
    width: 28px;
    height: 24px;
    flex-shrink: 0;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.12));
}
.gk-laurel-badge-right {
    transform: scaleX(-1);
}

/* 🌿 MAHKOTA DAUN LAUREL ROMAWI DI BAWAH & SAMPING KONTAINER (MENYELIMUTI KARTU) */
.gk-laurel-bottom-cradle {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    pointer-events: none;
    z-index: 8;
    overflow: visible;
}
.gk-laurel-bottom-branch {
    position: absolute;
    bottom: -8px;
    width: 48px;
    height: 125px;
    pointer-events: none;
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), filter 0.35s ease, opacity 0.35s ease;
}
.gk-laurel-bottom-left {
    left: -14px;
}
.gk-laurel-bottom-right {
    right: -14px;
    transform: scaleX(-1);
}
.gk-laurel-bottom-center {
    position: absolute;
    bottom: -6px;
    left: 50%;
    transform: translateX(-50%);
    width: 46px;
    height: 18px;
    opacity: 0.92;
    pointer-events: none;
    transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), filter 0.35s ease;
}

/* 🌟 GARIS TEPI EMAS & CAHAYA EMAS (TOP 1) */
.gk-podium-1 .gk-podium-card-revised,
.podium-rank-1 .gk-podium-card-revised,
.gk-podium-card-revised.is-first {
    border: 1.8px solid #f59e0b !important;
    box-shadow: 0 14px 36px -6px rgba(245, 158, 11, 0.32), 0 0 22px rgba(251, 191, 36, 0.22) !important;
}
.gk-podium-1 .gk-laurel-bottom-cradle,
.podium-rank-1 .gk-laurel-bottom-cradle,
.gk-rank-1.gk-laurel-bottom-cradle {
    color: #f59e0b !important;
    filter: drop-shadow(0 4px 10px rgba(245, 158, 11, 0.45));
}
.gk-podium-1 .gk-laurel-bottom-branch,
.podium-rank-1 .gk-laurel-bottom-branch {
    width: 48px;
    height: 130px;
    bottom: -8px;
}
.gk-podium-1 .gk-laurel-bottom-left,
.podium-rank-1 .gk-laurel-bottom-left {
    left: -14px;
}
.gk-podium-1 .gk-laurel-bottom-right,
.podium-rank-1 .gk-laurel-bottom-right {
    right: -14px;
}
.gk-rank-1 .gk-rank-badge-pill {
    background: linear-gradient(135deg, #fef08a, #fde047) !important;
    color: #854d0e !important;
    border: 1.5px solid #eab308 !important;
    box-shadow: 0 4px 14px rgba(234, 179, 8, 0.4) !important;
    font-weight: 800 !important;
    padding: 0.24rem 0.95rem !important;
    font-size: 0.82rem !important;
    letter-spacing: 0.5px;
}

/* 🌟 GARIS TEPI SILVER & CAHAYA SILVER (TOP 2) */
.gk-podium-2 .gk-podium-card-revised,
.podium-rank-2 .gk-podium-card-revised {
    border: 1.8px solid #94a3b8 !important;
    box-shadow: 0 12px 30px -6px rgba(148, 163, 184, 0.3), 0 0 18px rgba(203, 213, 225, 0.24) !important;
}
.gk-podium-2 .gk-laurel-bottom-cradle,
.podium-rank-2 .gk-laurel-bottom-cradle,
.gk-rank-2.gk-laurel-bottom-cradle {
    color: #94a3b8 !important;
    filter: drop-shadow(0 4px 10px rgba(148, 163, 184, 0.45));
}
.gk-rank-2 .gk-rank-badge-pill {
    background: linear-gradient(135deg, #ffffff, #e2e8f0) !important;
    color: #334155 !important;
    border: 1.5px solid #94a3b8 !important;
    box-shadow: 0 4px 14px rgba(148, 163, 184, 0.35) !important;
    font-weight: 800 !important;
    padding: 0.22rem 0.85rem !important;
    font-size: 0.78rem !important;
    letter-spacing: 0.5px;
}

/* 🌟 GARIS TEPI PERAK / BRONZE & CAHAYA PERAK (TOP 3) */
.gk-podium-3 .gk-podium-card-revised,
.podium-rank-3 .gk-podium-card-revised {
    border: 1.8px solid #cd7f32 !important;
    box-shadow: 0 10px 28px -6px rgba(205, 127, 50, 0.28), 0 0 16px rgba(217, 119, 6, 0.18) !important;
}
.gk-podium-3 .gk-laurel-bottom-cradle,
.podium-rank-3 .gk-laurel-bottom-cradle,
.gk-rank-3.gk-laurel-bottom-cradle {
    color: #cd7f32 !important;
    filter: drop-shadow(0 4px 10px rgba(205, 127, 50, 0.45));
}
.gk-rank-3 .gk-rank-badge-pill {
    background: linear-gradient(135deg, #ffedd5, #fed7aa) !important;
    color: #9a3412 !important;
    border: 1.5px solid #cd7f32 !important;
    box-shadow: 0 4px 14px rgba(205, 127, 50, 0.35) !important;
    font-weight: 800 !important;
    padding: 0.2rem 0.8rem !important;
    font-size: 0.74rem !important;
    letter-spacing: 0.5px;
}

/* 🌟 INTERAKSI FOKUS & BURAM (DEFAULT VS HOVER / SENTUH) */
/* Saat TIDAK disentuh / di-hover:
   - Top 1: Tidak blur / Jelas penuh (100%)
   - Top 2: Sedang (88%, blur halus 0.4px)
   - Top 3: Paling blur (60%, blur 1.4px)
*/
.gk-podium-row:not(:hover) .gk-podium-1 .gk-podium-card-revised,
.gk-podium-row:not(:hover) .podium-rank-1 .gk-podium-card-revised {
    opacity: 1 !important;
    filter: none !important;
}
.gk-podium-row:not(:hover) .gk-podium-2 .gk-podium-card-revised,
.gk-podium-row:not(:hover) .podium-rank-2 .gk-podium-card-revised {
    opacity: 0.88 !important;
    filter: blur(0.4px) !important;
}
.gk-podium-row:not(:hover) .gk-podium-3 .gk-podium-card-revised,
.gk-podium-row:not(:hover) .podium-rank-3 .gk-podium-card-revised {
    opacity: 0.60 !important;
    filter: blur(1.4px) !important;
}

/* Saat AREA PODIUM DI-HOVER: Seluruh kartu di row meredup & agak buram */
.gk-podium-row:hover .gk-podium-card-revised {
    opacity: 0.48 !important;
    filter: blur(1.5px) !important;
    transform: scale(0.985);
}

/* KECUALI kartu yang spesifik sedang di-hover / disentuh: JELAS 100%, TIDAK BURAM, TIMBUL POP UP */
.gk-podium-row .gk-podium-col:hover .gk-podium-card-revised,
.gk-podium-row .podium-anim-item:hover .gk-podium-card-revised,
.gk-podium-row .gk-podium-card-revised:hover,
.gk-podium-row .gk-podium-col.is-touch-focused .gk-podium-card-revised,
.gk-podium-row .podium-anim-item.is-touch-focused .gk-podium-card-revised {
    opacity: 1 !important;
    filter: none !important;
    transform: translateY(-8px) scale(1.025) !important;
    z-index: 35 !important;
}
.gk-podium-row .gk-podium-1:hover .gk-podium-card-revised,
.gk-podium-row .podium-rank-1:hover .gk-podium-card-revised {
    box-shadow: 0 24px 55px -6px rgba(245, 158, 11, 0.45), 0 0 32px rgba(251, 191, 36, 0.35) !important;
}
.gk-podium-row .gk-podium-2:hover .gk-podium-card-revised,
.gk-podium-row .podium-rank-2:hover .gk-podium-card-revised {
    box-shadow: 0 20px 48px -6px rgba(148, 163, 184, 0.45), 0 0 28px rgba(203, 213, 225, 0.38) !important;
}
.gk-podium-row .gk-podium-3:hover .gk-podium-card-revised,
.gk-podium-row .podium-rank-3:hover .gk-podium-card-revised {
    box-shadow: 0 18px 42px -6px rgba(205, 127, 50, 0.45), 0 0 26px rgba(217, 119, 6, 0.32) !important;
}

/* 🌟 TAMPILAN PODIUM BERTINGKAT (LEBAR CARD SAMA SEMUA, TINGGI BERTINGKAT JELAS, JARAK TIDAK DEMPET) */
@media (min-width: 768px) {
    .gk-podium-row {
        display: flex !important;
        align-items: flex-end !important;
        justify-content: center !important;
        gap: 1.65rem !important; /* Jarak renggang antar kartu */
        margin-bottom: 1.5rem !important;
    }
    .gk-podium-col,
    .podium-anim-item {
        flex: 0 0 275px !important;
        width: 275px !important;
        max-width: 275px !important;
        min-width: 275px !important;
    }
    .gk-podium-1,
    .podium-rank-1 {
        z-index: 10;
    }
    .gk-podium-2,
    .podium-rank-2 {
        z-index: 5;
    }
    .gk-podium-3,
    .podium-rank-3 {
        z-index: 2;
    }
    .gk-podium-card-revised,
    .gk-podium-1 .gk-podium-card-revised,
    .gk-podium-2 .gk-podium-card-revised,
    .gk-podium-3 .gk-podium-card-revised,
    .podium-rank-1 .gk-podium-card-revised,
    .podium-rank-2 .gk-podium-card-revised,
    .podium-rank-3 .gk-podium-card-revised {
        width: 275px !important;
        max-width: 275px !important;
        min-width: 275px !important;
        margin-left: auto !important;
        margin-right: auto !important;
        box-sizing: border-box !important;
        padding-left: 0.95rem !important;
        padding-right: 0.95rem !important;
    }
    .gk-podium-1 .gk-podium-card-revised,
    .podium-rank-1 .gk-podium-card-revised {
        min-height: 415px !important;
        padding-top: 1.85rem !important;
        padding-bottom: 1.15rem !important;
    }
    .gk-podium-2 .gk-podium-card-revised,
    .podium-rank-2 .gk-podium-card-revised {
        min-height: 355px !important;
        padding-top: 1.65rem !important;
        padding-bottom: 1rem !important;
    }
    .gk-podium-3 .gk-podium-card-revised,
    .podium-rank-3 .gk-podium-card-revised {
        min-height: 310px !important;
        padding-top: 1.45rem !important;
        padding-bottom: 0.95rem !important;
    }
    .gk-podium-row .gk-avatar-clean-wrap {
        margin: 0 auto 0.55rem !important;
    }
    .gk-podium-1 .gk-avatar-clean-wrap,
    .podium-rank-1 .gk-avatar-clean-wrap {
        width: 84px !important;
        height: 84px !important;
    }
    .gk-podium-2 .gk-avatar-clean-wrap,
    .podium-rank-2 .gk-avatar-clean-wrap {
        width: 72px !important;
        height: 72px !important;
    }
    .gk-podium-3 .gk-avatar-clean-wrap,
    .podium-rank-3 .gk-avatar-clean-wrap {
        width: 64px !important;
        height: 64px !important;
    }
    .gk-podium-row h4,
    .gk-podium-row h5 {
        font-size: 0.98rem !important;
        margin-bottom: 0.2rem !important;
        line-height: 1.25 !important;
    }
    .gk-podium-1 h4 {
        font-size: 1.12rem !important;
    }
    .gk-podium-row .text-muted.small {
        margin-bottom: 0.25rem !important;
        font-size: 0.74rem !important;
    }
    .gk-podium-row .gk-podium-badges-row {
        margin: 0.15rem auto 0.45rem !important;
        min-height: 24px !important;
        gap: 5px !important;
    }
    .gk-podium-row .text-warning {
        margin-bottom: 0.45rem !important;
        font-size: 0.88rem !important;
    }
    .gk-podium-1 .text-warning {
        font-size: 1rem !important;
    }
    .gk-podium-row .gk-progress-pill {
        height: 22px !important;
        line-height: 22px !important;
        font-size: 0.76rem !important;
        min-width: 110px !important;
    }
    .gk-podium-row small.text-muted.font-mono {
        margin-bottom: 0.5rem !important;
        font-size: 0.74rem !important;
    }
    .gk-podium-row .gk-btn-podium-profile {
        padding: 0.26rem 0.95rem !important;
        font-size: 0.8rem !important;
        margin-bottom: 0.15rem !important;
        margin-top: 0.45rem !important;
    }
    .gk-podium-row .gk-podium-card-revised:hover .gk-laurel-bottom-branch,
    .gk-podium-row .is-touch-focused .gk-laurel-bottom-branch {
        filter: drop-shadow(0 6px 16px currentColor);
    }
    .gk-podium-row .gk-podium-card-revised:hover .gk-laurel-bottom-left,
    .gk-podium-row .is-touch-focused .gk-laurel-bottom-left {
        transform: scale(1.06);
    }
    .gk-podium-row .gk-podium-card-revised:hover .gk-laurel-bottom-right,
    .gk-podium-row .is-touch-focused .gk-laurel-bottom-right {
        transform: scaleX(-1) scale(1.06);
    }
}

@media (max-width: 767.98px) {
    .gk-podium-row {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        align-items: flex-end !important;
        justify-content: center !important;
        gap: 10px !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        padding: 0 8px !important;
        margin-bottom: 1.5rem !important;
    }
    .gk-podium-col,
    .podium-anim-item {
        flex: 1 1 0% !important;
        width: 0 !important;
        max-width: 33.333% !important;
        min-width: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: flex-end !important;
        opacity: 1 !important;
        transform: none !important;
    }
    .gk-podium-card-revised {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        border-radius: 14px !important;
        box-sizing: border-box !important;
        margin: 0 !important;
        transform: none !important;
        opacity: 1 !important;
        filter: none !important;
        background: #ffffff !important;
        position: relative !important;
    }
    .podium-rank-1 .gk-podium-card-revised,
    .gk-podium-1 .gk-podium-card-revised,
    .gk-podium-card-revised.is-first {
        min-height: 242px !important;
        padding: 1.05rem 4px 0.55rem !important;
        border: 1.8px solid #f59e0b !important;
        box-shadow: 0 10px 25px -4px rgba(245, 158, 11, 0.40), 0 0 16px rgba(251, 191, 36, 0.28) !important;
    }
    .podium-rank-2 .gk-podium-card-revised,
    .gk-podium-2 .gk-podium-card-revised {
        min-height: 220px !important;
        padding: 0.95rem 4px 0.55rem !important;
        border: 1.8px solid #94a3b8 !important;
        box-shadow: 0 8px 20px -4px rgba(148, 163, 184, 0.32), 0 0 12px rgba(203, 213, 225, 0.22) !important;
    }
    .podium-rank-3 .gk-podium-card-revised,
    .gk-podium-3 .gk-podium-card-revised {
        min-height: 202px !important;
        padding: 0.90rem 4px 0.55rem !important;
        border: 1.8px solid #cd7f32 !important;
        box-shadow: 0 8px 18px -4px rgba(205, 127, 50, 0.30), 0 0 10px rgba(217, 119, 6, 0.20) !important;
    }
    .gk-podium-card-revised:hover,
    .podium-anim-item.is-touch-focused .gk-podium-card-revised,
    .gk-podium-col.is-touch-focused .gk-podium-card-revised {
        transform: none !important;
        opacity: 1 !important;
        filter: none !important;
    }
    .gk-avatar-clean-wrap img,
    .gk-avatar-red-wrap img,
    .gk-podium-row .gk-avatar-clean-wrap img {
        width: 100% !important;
        height: 100% !important;
        max-width: 100% !important;
        max-height: 100% !important;
        object-fit: cover !important;
    }
    .podium-rank-1 .gk-avatar-clean-wrap,
    .gk-podium-1 .gk-avatar-clean-wrap {
        width: 44px !important;
        height: 44px !important;
        margin: 0 auto 0.2rem !important;
        border: 2px solid #f59e0b !important;
        border-radius: 50% !important;
    }
    .podium-rank-2 .gk-avatar-clean-wrap,
    .gk-podium-2 .gk-avatar-clean-wrap {
        width: 38px !important;
        height: 38px !important;
        margin: 0 auto 0.2rem !important;
        border: 2px solid #94a3b8 !important;
        border-radius: 50% !important;
    }
    .podium-rank-3 .gk-avatar-clean-wrap,
    .gk-podium-3 .gk-avatar-clean-wrap {
        width: 34px !important;
        height: 34px !important;
        margin: 0 auto 0.2rem !important;
        border: 2px solid #cd7f32 !important;
        border-radius: 50% !important;
    }
    .gk-podium-row h4,
    .gk-podium-row h5 {
        font-size: 0.66rem !important;
        font-weight: 700 !important;
        line-height: 1.15 !important;
        height: 1.55rem !important;
        overflow: hidden !important;
        display: -webkit-box !important;
        -webkit-line-clamp: 2 !important;
        -webkit-box-orient: vertical !important;
        margin-bottom: 2px !important;
        text-align: center !important;
        word-break: break-word !important;
    }
    .podium-rank-1 h4,
    .podium-rank-1 h5,
    .gk-podium-1 h4,
    .gk-podium-1 h5 {
        font-size: 0.72rem !important;
    }
    .gk-podium-row .text-muted.small,
    .gk-podium-row .small.text-muted {
        font-size: 0.50rem !important;
        height: 0.68rem !important;
        line-height: 1.1 !important;
        margin-bottom: 2px !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        text-align: center !important;
        display: block !important;
    }
    .gk-podium-row .gk-podium-badges-row {
        display: flex !important;
        justify-content: center !important;
        gap: 2px !important;
        min-height: 14px !important;
        margin: 0 auto 2px !important;
    }
    .gk-badge-mini-icon {
        width: 14px !important;
        height: 14px !important;
        font-size: 0.55rem !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .gk-podium-row .text-warning {
        font-size: 0.54rem !important;
        height: 0.68rem !important;
        line-height: 1 !important;
        letter-spacing: 0.3px !important;
        margin-bottom: 2px !important;
        text-align: center !important;
        white-space: nowrap !important;
        display: block !important;
    }
    .gk-podium-row .mb-2,
    .gk-podium-row .mb-3 {
        margin-bottom: 2px !important;
    }
    .gk-podium-row .gk-progress-pill {
        width: 86% !important;
        max-width: 62px !important;
        min-width: 0 !important;
        height: 16px !important;
        line-height: 16px !important;
        font-size: 0.58rem !important;
        padding: 0 2px !important;
        margin: 0 auto 2px !important;
        border-radius: 50px !important;
    }
    .gk-podium-row small.text-muted.font-mono {
        font-size: 0.50rem !important;
        height: 0.68rem !important;
        line-height: 1 !important;
        margin-bottom: 0.15rem !important;
        display: block !important;
        text-align: center !important;
    }
    .gk-podium-row .mt-3,
    .gk-podium-row .mt-2 {
        margin-top: 0.15rem !important;
        margin-bottom: 0 !important;
    }
    .gk-podium-row .gk-btn-podium-profile {
        padding: 2px 4px !important;
        font-size: 0.62rem !important;
        line-height: 1.2 !important;
        margin: 0 auto !important;
        width: 88% !important;
        border-radius: 6px !important;
        white-space: nowrap !important;
    }
    .gk-podium-header-badge {
        position: absolute !important;
        top: 0 !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        margin: 0 !important;
        padding: 0 !important;
        line-height: 1 !important;
        z-index: 25 !important;
    }
    .gk-podium-header-badge .gk-rank-badge-pill,
    .gk-rank-badge-pill {
        transform: none !important;
        margin: 0 !important;
        padding: 0.16rem 0.65rem !important;
        font-size: 0.68rem !important;
        letter-spacing: 0.3px !important;
        line-height: 1 !important;
    }
    .podium-rank-1 .gk-podium-header-badge .gk-rank-badge-pill,
    .gk-podium-1 .gk-podium-header-badge .gk-rank-badge-pill {
        padding: 0.20rem 0.75rem !important;
        font-size: 0.74rem !important;
    }
    /* 🌿 Bottom Roman Laurel Wreath Cradle (Mahkota Daun Romawi persis Desktop) */
    .gk-laurel-bottom-cradle {
        display: block !important;
        position: absolute !important;
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;
        pointer-events: none !important;
        z-index: 12 !important;
        overflow: visible !important;
    }
    .gk-laurel-bottom-branch {
        display: block !important;
        position: absolute !important;
        bottom: -5px !important;
        width: 24px !important;
        height: 62px !important;
        pointer-events: none !important;
    }
    .gk-laurel-bottom-left {
        left: -7px !important;
    }
    .gk-laurel-bottom-right {
        right: -7px !important;
        transform: scaleX(-1) !important;
    }
    .gk-laurel-bottom-center {
        display: block !important;
        position: absolute !important;
        bottom: -3px !important;
        left: 50% !important;
        transform: translateX(-50%) !important;
        width: 26px !important;
        height: 10px !important;
        opacity: 0.92 !important;
        pointer-events: none !important;
    }
    .podium-rank-1 .gk-laurel-bottom-cradle,
    .gk-podium-1 .gk-laurel-bottom-cradle {
        color: #f59e0b !important;
        filter: drop-shadow(0 2px 6px rgba(245, 158, 11, 0.5)) !important;
    }
    .podium-rank-2 .gk-laurel-bottom-cradle,
    .gk-podium-2 .gk-laurel-bottom-cradle {
        color: #94a3b8 !important;
        filter: drop-shadow(0 2px 5px rgba(148, 163, 184, 0.45)) !important;
    }
    .podium-rank-3 .gk-laurel-bottom-cradle,
    .gk-podium-3 .gk-laurel-bottom-cradle {
        color: #cd7f32 !important;
        filter: drop-shadow(0 2px 5px rgba(205, 127, 50, 0.45)) !important;
    }
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
    overflow: hidden !important;
    aspect-ratio: 1 / 1 !important;
    flex-shrink: 0 !important;
}
.gk-avatar-red-wrap img,
.gk-avatar-clean-wrap img {
    width: 100% !important;
    height: 100% !important;
    max-width: 100% !important;
    max-height: 100% !important;
    aspect-ratio: 1 / 1 !important;
    object-fit: cover !important;
    object-position: center top !important;
    border-radius: 50% !important;
    border: 3px solid #ffffff !important;
    box-shadow: 0 0 0 1px rgba(15, 23, 42, 0.08) !important;
    flex-shrink: 0 !important;
    display: block !important;
}

/* ANTI-GEPENG (SQUISH PROTECTION) UNTUK SEMUA FOTO GURU DI TABEL & KARTU */
.table img.rounded-circle,
.table-custom img.rounded-circle,
img.rounded-circle {
    aspect-ratio: 1 / 1 !important;
    object-fit: cover !important;
    object-position: center top !important;
    flex-shrink: 0 !important;
}

/* UNIFIED PODIUM PROFILE BUTTON (TOP 1, 2, 3 - PANJANG KOTAK BORDER RADIUS KECIL) */
.gk-btn-podium-profile {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 0.45rem !important;
    width: 100% !important;
    max-width: 280px !important;
    padding: 0.5rem 1.25rem !important;
    font-size: 0.86rem !important;
    font-weight: 600 !important;
    background-color: #f8fafc !important;
    color: var(--primary, #003366) !important;
    border: 1.5px solid #003366 !important;
    border-radius: 8px !important;
    text-decoration: none !important;
    box-shadow: 0 2px 6px rgba(0, 51, 102, 0.08) !important;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    cursor: pointer !important;
    line-height: 1.4 !important;
    margin: 0.65rem auto 0.25rem !important;
}
.gk-btn-podium-profile:hover {
    background-color: var(--primary, #003366) !important;
    color: #ffffff !important;
    border-color: var(--primary, #003366) !important;
    box-shadow: 0 4px 12px rgba(0, 51, 102, 0.2) !important;
    transform: translateY(-2px) !important;
}
.gk-btn-podium-profile:hover i {
    color: #ffffff !important;
}

[data-bs-theme="dark"] .gk-btn-podium-profile,
[data-theme="dark"] .gk-btn-podium-profile,
.dark-theme .gk-btn-podium-profile {
    background-color: #1e293b !important;
    color: #38bdf8 !important;
    border-color: #38bdf8 !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3) !important;
}
[data-bs-theme="dark"] .gk-btn-podium-profile:hover,
[data-theme="dark"] .gk-btn-podium-profile:hover,
.dark-theme .gk-btn-podium-profile:hover {
    background-color: #38bdf8 !important;
    color: #0f172a !important;
}
[data-bs-theme="dark"] .gk-btn-podium-profile:hover i,
[data-theme="dark"] .gk-btn-podium-profile:hover i,
.dark-theme .gk-btn-podium-profile:hover i {
    color: #0f172a !important;
}
.gk-badge-mini-icon {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 22px !important;
    height: 22px !important;
    min-width: 22px !important;
    border-radius: 6px !important;
    font-size: 0.72rem !important;
    line-height: 1 !important;
    border: 1px solid rgba(0, 0, 0, 0.1) !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06) !important;
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease !important;
    text-decoration: none !important;
    cursor: pointer !important;
    vertical-align: middle !important;
    padding: 0 !important;
}
.gk-badge-mini-icon i {
    font-size: 0.72rem !important;
    line-height: 1 !important;
}
.gk-badge-mini-icon:hover {
    transform: translateY(-2px) scale(1.25) !important;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.18) !important;
    z-index: 5 !important;
}

.table .gk-badge-mini-icon,
.table-custom .gk-badge-mini-icon {
    width: 16px !important;
    height: 16px !important;
    min-width: 16px !important;
    border-radius: 4px !important;
    font-size: 0.6rem !important;
}
.table .gk-badge-mini-icon i,
.table-custom .gk-badge-mini-icon i {
    font-size: 0.58rem !important;
}

.gk-podium-badges-row {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 6px !important;
    flex-wrap: wrap !important;
    margin: 0.4rem auto 0.75rem !important;
    min-height: 26px !important;
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
