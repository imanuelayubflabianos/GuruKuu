@php
    $activePeriode = \App\Models\Periode::getActivePeriode();
    $user = auth()->user();
    $role = $user?->role ?? 'siswa';
    $changedAt = \App\Models\Setting::get('active_periode_changed_at', $activePeriode?->updated_at?->timestamp ?? 0);
@endphp

@if($activePeriode)
<div class="modal fade" id="modalPeriodeNotification" tabindex="-1" aria-labelledby="modalPeriodeNotificationLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden; background: #ffffff;">
            {{-- Header Decor --}}
            <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-center position-relative">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 76px; height: 76px; background: linear-gradient(135deg, rgba(0, 51, 102, 0.1), rgba(2, 132, 199, 0.18)); color: #003366; border: 2px solid rgba(0, 51, 102, 0.12);">
                    <i class="bi bi-calendar-check-fill" style="font-size: 2.2rem; color: #003366;"></i>
                </div>
            </div>

            {{-- Body Content --}}
            <div class="modal-body text-center px-4 pt-3 pb-4">
                <div class="d-inline-flex align-items-center gap-1.5 badge rounded-pill px-3 py-1.5 mb-2 fw-bold" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-size: 0.78rem; letter-spacing: 0.5px;">
                    <i class="bi bi-broadcast"></i> PERIODE PENILAIAN TELAH DIMULAI
                </div>

                <h4 class="fw-bold text-dark mb-1" id="modalPeriodeNotificationLabel" style="font-size: 1.28rem;">
                    @if($role === 'siswa')
                        Saatnya Memberikan Penilaian!
                    @elseif($role === 'guru')
                        Periode Penilaian Baru Aktif!
                    @else
                        Periode Penilaian Sedang Berjalan
                    @endif
                </h4>

                <p class="text-muted mb-3" style="font-size: 0.92rem; line-height: 1.55;">
                    @if($role === 'siswa')
                        Halo <strong>{{ $user->name }}</strong>! Periode penilaian guru telah resmi dimulai. Suarakan aspirasi dan penilaian objektifmu untuk bapak/ibu guru pengajar.
                    @elseif($role === 'guru')
                        Halo Bapak/Ibu <strong>{{ $user->name }}</strong>! Periode evaluasi guru saat ini sedang berlangsung. Ulasan dari siswa akan tercatat dalam laporan pengajaran Anda.
                    @else
                        Halo Administrator! Periode evaluasi aktif telah diperbarui. Seluruh metrik ulasan dan leaderboard otomatis disesuaikan dengan periode ini.
                    @endif
                </p>

                {{-- Periode Information Card --}}
                <div class="p-3.5 rounded-4 mb-3.5 text-start shadow-xs" style="background: #f8fafc; border: 1.5px solid #e2e8f0;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge rounded-pill bg-primary text-white px-2.5 py-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                            {{ strtoupper($activePeriode->semester) }} &bull; {{ $activePeriode->tahun_ajaran }}
                        </span>
                        <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle font-mono" style="font-size: 0.72rem;">
                            <i class="bi bi-circle-fill me-1" style="font-size: 0.45rem;"></i>Aktif
                        </span>
                    </div>

                    <h6 class="fw-bold text-dark mb-1.5" style="font-size: 1rem;">
                        {{ $activePeriode->nama_periode }}
                    </h6>

                    <div class="d-flex flex-column gap-1 text-muted" style="font-size: 0.82rem;">
                        <div>
                            <i class="bi bi-calendar3 me-1.5 text-primary"></i>
                            <strong>Jadwal:</strong> {{ $activePeriode->tanggal_mulai ? $activePeriode->tanggal_mulai->isoFormat('D MMMM Y') : '-' }} s/d {{ $activePeriode->tanggal_selesai ? $activePeriode->tanggal_selesai->isoFormat('D MMMM Y') : '-' }}
                        </div>
                        @if($activePeriode->tanggal_selesai)
                            @php
                                $daysLeft = (int)now()->diffInDays($activePeriode->tanggal_selesai, false);
                            @endphp
                            <div class="mt-1">
                                <i class="bi bi-hourglass-split me-1.5 text-warning"></i>
                                <strong>Sisa Waktu:</strong> 
                                @if($daysLeft > 0)
                                    <span class="fw-semibold text-dark">{{ $daysLeft }} hari lagi</span>
                                @elseif($daysLeft === 0)
                                    <span class="fw-semibold text-danger">Berakhir hari ini</span>
                                @else
                                    <span class="fw-semibold text-muted">Masa evaluasi telah usai</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-grid gap-2">
                    @if($role === 'siswa')
                        <a href="{{ route('siswa.guru.index') }}" class="btn btn-primary-custom py-2.5 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2" style="border-radius: 12px; font-size: 0.92rem;" onclick="dismissPeriodeModal()">
                            <i class="bi bi-pencil-square"></i>
                            <span>Beri Penilaian Sekarang</span>
                        </a>
                    @elseif($role === 'guru')
                        <a href="{{ route('guru.dashboard') }}" class="btn btn-primary-custom py-2.5 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2" style="border-radius: 12px; font-size: 0.92rem;" onclick="dismissPeriodeModal()">
                            <i class="bi bi-speedometer2"></i>
                            <span>Lihat Statistik Saya</span>
                        </a>
                    @else
                        <a href="{{ route('admin.pengaturan.index') }}#tabPeriode" class="btn btn-primary-custom py-2.5 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2" style="border-radius: 12px; font-size: 0.92rem;" onclick="dismissPeriodeModal()">
                            <i class="bi bi-gear-fill"></i>
                            <span>Kelola Data Periode</span>
                        </a>
                    @endif
                    
                    <button type="button" class="btn btn-light border py-2.5 fw-semibold text-secondary d-flex align-items-center justify-content-center gap-2" data-bs-dismiss="modal" style="border-radius: 12px; font-size: 0.92rem;" onclick="dismissPeriodeModal()">
                        <span>Saya Mengerti</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [data-theme="dark"] #modalPeriodeNotification .modal-content {
        background: #1e293b !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    [data-theme="dark"] #modalPeriodeNotification h4,
    [data-theme="dark"] #modalPeriodeNotification h6 {
        color: #f8fafc !important;
    }
    [data-theme="dark"] #modalPeriodeNotification p.text-muted {
        color: #94a3b8 !important;
    }
    [data-theme="dark"] #modalPeriodeNotification .p-3.5 {
        background: #0f172a !important;
        border-color: #334155 !important;
    }
    [data-theme="dark"] #modalPeriodeNotification .btn-light {
        background: #334155 !important;
        border-color: #475569 !important;
        color: #e2e8f0 !important;
    }

    /* Efek bayangan gelap (dimmed backdrop shadow) agar pop-up periode tidak nyatu dengan background */
    .modal-backdrop {
        background-color: #0f172a !important;
        backdrop-filter: blur(5px) !important;
        -webkit-backdrop-filter: blur(5px) !important;
    }
    .modal-backdrop.show {
        opacity: 0.65 !important;
    }
    #modalPeriodeNotification .modal-content {
        box-shadow: 0 25px 60px -12px rgba(15, 23, 42, 0.4), 0 0 0 1px rgba(15, 23, 42, 0.08) !important;
    }
</style>

<script>
(function() {
    const storageKey = 'gk_seen_periode_{{ $activePeriode->id }}_{{ $changedAt }}_{{ $user ? $user->id : 0 }}';

    window.dismissPeriodeModal = function() {
        try {
            localStorage.setItem(storageKey, 'true');
        } catch (e) {}
    };

    function showPeriodePopupNow() {
        const modalEl = document.getElementById('modalPeriodeNotification');
        if (!modalEl || typeof bootstrap === 'undefined') return;

        // Bersihkan sisa backdrop modal sebelumnya jika ada
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');

        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const modalEl = document.getElementById('modalPeriodeNotification');
        if (!modalEl || typeof bootstrap === 'undefined') return;

        // Cek apakah user sudah melihat pemberitahuan untuk periode ini
        if (localStorage.getItem(storageKey) === 'true') {
            return;
        }

        // Cek apakah ada pop-up Selamat Datang (Welcome Landing) yang sedang aktif atau antre
        const welcomeEl = document.getElementById('welcomeLandingPromptModal');
        const welcomeKey = 'gk_sso_welcomed_' + "{{ $user ? $user->id : 'guest' }}";
        const isWelcomeActive = welcomeEl && (window.gkWelcomeModalActive === true || !sessionStorage.getItem(welcomeKey));

        if (isWelcomeActive) {
            // BERGILIR: Jangan muncul bersamaan (antre di belakang pop-up selamat datang)
            let handled = false;
            window.addEventListener('gk:welcomeModalClosed', function onWelcomeClosed() {
                if (handled) return;
                handled = true;
                window.removeEventListener('gk:welcomeModalClosed', onWelcomeClosed);

                // Jeda halus 400ms setelah modal selamat datang tertutup sempurna
                setTimeout(showPeriodePopupNow, 400);
            });
            return;
        }

        // Jika tidak ada modal selamat datang yang aktif, munculkan setelah jeda singkat
        setTimeout(showPeriodePopupNow, 500);
    });
})();
</script>
@endif
