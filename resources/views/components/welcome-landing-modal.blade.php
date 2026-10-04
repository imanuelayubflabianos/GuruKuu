@php
    $showWelcomePopup = session('show_welcome_landing_popup') || request()->has('from_sipintu');
    $ssoEntryToken = session('sso_entry_token') ?: (request('from_sipintu') ? 'sipintu_param_' . (auth()->id() ?? 'guest') : null);
@endphp

@if($showWelcomePopup && $ssoEntryToken)
<div class="modal fade" id="welcomeLandingPromptModal" tabindex="-1" aria-labelledby="welcomeLandingPromptLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden; background: #ffffff;">
            {{-- Header Icon --}}
            <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-center position-relative">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 72px; height: 72px; background: linear-gradient(135deg, rgba(0, 51, 102, 0.08), rgba(2, 132, 199, 0.14)); color: #003366; border: 1.5px solid rgba(0, 51, 102, 0.12);">
                    <i class="bi bi-compass-fill" style="font-size: 2.2rem; color: #003366;"></i>
                </div>
            </div>

            {{-- Body Content --}}
            <div class="modal-body text-center px-4 pt-3 pb-4">
                <div class="badge rounded-pill px-3 py-1 mb-2 fw-semibold" style="background: rgba(0, 51, 102, 0.08); color: #003366; font-size: 0.75rem; letter-spacing: 0.5px;">
                    PORTAL INFORMASI GURUKUU
                </div>
                <h4 class="fw-bold text-dark mb-2" id="welcomeLandingPromptLabel" style="font-size: 1.3rem;">
                    Selamat Datang di GuruKuu!
                </h4>
                <p class="text-muted mb-3" style="font-size: 0.94rem; line-height: 1.6;">
                    Anda yakin untuk tetap di dashboard dan tidak mau melihat detail web kami di beranda?
                </p>

                {{-- Status Box yang Rapi dan Bersih --}}
                <div class="welcome-status-box p-3 rounded-4 mb-4 text-start d-flex align-items-start gap-3" style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.25);">
                    <div class="welcome-status-icon flex-shrink-0 d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 34px; height: 34px; background: #10b981; color: #ffffff; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.35);">
                        <i class="bi bi-check-lg" style="font-size: 1.15rem; font-weight: 800;"></i>
                    </div>
                    <div class="flex-grow-1" style="font-size: 0.85rem; line-height: 1.55;">
                        <span class="d-block fw-bold text-success mb-0.5" style="color: #059669 !important; font-size: 0.88rem;">Akun Berhasil Masuk</span>
                        <span style="color: #475569;" class="welcome-status-desc">Anda dapat kembali ke Dashboard kapan saja lewat tombol menu di Beranda.</span>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-grid gap-2">
                    <a href="{{ route('landing.index') }}" class="btn btn-primary-custom py-2.5 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-2" id="btnGoToLanding" style="border-radius: 12px; font-size: 0.92rem;">
                        <i class="bi bi-house-door-fill"></i>
                        <span>Tidak, bawa saya ke beranda publik dulu</span>
                    </a>
                    <button type="button" class="btn btn-light border py-2.5 fw-semibold text-secondary d-flex align-items-center justify-content-center gap-2" id="btnStayInDashboard" data-bs-dismiss="modal" style="border-radius: 12px; font-size: 0.92rem;">
                        <i class="bi bi-speedometer2"></i>
                        <span>Iya, tetap di dashboard</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [data-theme="dark"] #welcomeLandingPromptModal .modal-content {
        background: #1e293b !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    [data-theme="dark"] #welcomeLandingPromptModal h4 {
        color: #f8fafc !important;
    }
    [data-theme="dark"] #welcomeLandingPromptModal p.text-muted {
        color: #94a3b8 !important;
    }
    [data-theme="dark"] #welcomeLandingPromptModal .welcome-status-box {
        background: rgba(16, 185, 129, 0.12) !important;
        border-color: rgba(16, 185, 129, 0.3) !important;
    }
    [data-theme="dark"] #welcomeLandingPromptModal .welcome-status-desc {
        color: #94a3b8 !important;
    }
    [data-theme="dark"] #welcomeLandingPromptModal .welcome-status-box .text-success {
        color: #34d399 !important;
    }
    [data-theme="dark"] #welcomeLandingPromptModal .btn-light {
        background: #334155 !important;
        border-color: #475569 !important;
        color: #e2e8f0 !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('welcomeLandingPromptModal');
    if (!modalEl || typeof bootstrap === 'undefined') return;

    const currentToken = "{{ $ssoEntryToken }}";
    const storageKey = 'gk_sso_welcomed_' + "{{ auth()->id() ?? 'guest' }}";

    // Mencegah pop-up muncul kembali jika token SSO ini sudah pernah ditampilkan/ditangani (misal user bolak-balik web / refresh)
    if (sessionStorage.getItem(storageKey) === currentToken) {
        return;
    }

    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    // Segera simpan bahwa pop-up untuk sesi SSO ini sudah tampil
    sessionStorage.setItem(storageKey, currentToken);

    // Tandai saat tombol apapun di dalam modal diklik
    modalEl.querySelectorAll('a, button').forEach(btn => {
        btn.addEventListener('click', function() {
            sessionStorage.setItem(storageKey, currentToken);
        });
    });

    // Mencegah pop-up lingering atau muncul kembali saat navigasi browser Back/Forward (bfcache)
    window.addEventListener('pageshow', function(event) {
        if (event.persisted || sessionStorage.getItem(storageKey) === currentToken) {
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) {
                modalInstance.hide();
            }
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        }
    });
});
</script>
@endif
