{{-- GuruKuu Global Page Transition & 100% Solid White Full Cover Loader --}}
<style>
#gk-page-veil {
    position: fixed !important;
    inset: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    background: #ffffff !important;
    z-index: 999999999 !important;
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: all !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    transition: opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.25s ease !important;
    will-change: opacity, visibility;
}

/* Selalu full putih solid 100% menutup layar */
[data-bs-theme="dark"] #gk-page-veil,
[data-theme="dark"] #gk-page-veil,
.dark-theme #gk-page-veil {
    background: #ffffff !important;
}

#gk-page-veil.gk-veil-hidden {
    opacity: 0 !important;
    visibility: hidden !important;
    pointer-events: none !important;
}

/* Saat aktif / navigasi: seketika langsung full putih 100% tanpa jeda transparan */
#gk-page-veil.gk-veil-active {
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: all !important;
    background: #ffffff !important;
    transition: none !important;
}

/* Top Progress Line */
#gk-page-veil .gk-veil-bar {
    position: fixed;
    top: 0;
    left: 0;
    height: 3.5px;
    width: 0%;
    background: linear-gradient(90deg, #003366, #0284c7, #ff6600);
    box-shadow: 0 0 10px rgba(0, 51, 102, 0.4);
    transition: width 0.35s ease;
    z-index: 1000000000;
}
#gk-page-veil.gk-veil-active .gk-veil-bar {
    width: 82%;
    transition: width 0.6s cubic-bezier(0.2, 0.8, 0.2, 1);
}
#gk-page-veil.gk-veil-hidden .gk-veil-bar {
    width: 100%;
    opacity: 0;
    transition: width 0.15s ease, opacity 0.2s ease 0.05s;
}

/* Spinner Modern di Tengah Layar Putih */
.gk-veil-loader-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 16px;
    user-select: none;
}
.gk-veil-spinner {
    width: 46px;
    height: 46px;
    border: 4px solid rgba(0, 51, 102, 0.12);
    border-top-color: #003366;
    border-radius: 50%;
    animation: gkSpinnerRotate 0.75s linear infinite;
}
@keyframes gkSpinnerRotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
.gk-veil-brand {
    font-weight: 800;
    font-size: 1.25rem;
    color: #003366;
    letter-spacing: -0.5px;
}
</style>

<div id="gk-page-veil" aria-hidden="true">
    <div class="gk-veil-bar"></div>
    <div class="gk-veil-loader-box">
        <div class="gk-veil-spinner"></div>
        <div class="gk-veil-brand">
            Guru<span style="color: #ff6600;">Kuu</span>
        </div>
    </div>
</div>

<script>
(function() {
    const veil = document.getElementById('gk-page-veil');
    if (!veil) return;

    let navTimer = null;

    function hideVeil() {
        if (veil) {
            veil.classList.add('gk-veil-hidden');
            veil.classList.remove('gk-veil-active');
        }
        if (navTimer) {
            clearTimeout(navTimer);
            navTimer = null;
        }
    }

    function showVeil() {
        if (veil) {
            veil.classList.remove('gk-veil-hidden');
            veil.classList.add('gk-veil-active');
        }
        // Safety timeout: max 3.5s so user is NEVER stuck
        navTimer = setTimeout(hideVeil, 3500);
    }

    // Hide smoothly once DOM and initial paint are ready
    if (document.readyState === 'complete') {
        setTimeout(hideVeil, 100);
    } else {
        window.addEventListener('load', function() {
            setTimeout(hideVeil, 100);
        });
    }

    // Handle back/forward cache (pageshow)
    window.addEventListener('pageshow', function() {
        hideVeil();
    });

    // Intercept clicks specifically for entering user dashboard or login
    document.addEventListener('click', function(e) {
        if (e.button !== 0 || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;
        const anchor = e.target.closest('a');
        if (!anchor) return;

        if (anchor.target && anchor.target !== '_self') return;
        if (anchor.hasAttribute('data-no-loader') || anchor.classList.contains('no-loader')) return;

        const href = anchor.getAttribute('href') || '';
        if (anchor.classList.contains('btn-dashboard') || 
            anchor.classList.contains('btn-masuk') ||
            /\/admin\/dashboard|\/guru\/dashboard|\/siswa\/dashboard/i.test(href)) {
            showVeil();
        }
    }, true);

    // Intercept only login form submit
    document.addEventListener('submit', function(e) {
        if (e.defaultPrevented) return;
        const form = e.target;
        if (!form) return;

        if (form.target && form.target !== '_self') return;
        if (form.hasAttribute('data-no-loader') || form.classList.contains('no-loader')) return;

        const action = (form.getAttribute('action') || '').toLowerCase();
        // Only trigger when user is logging in
        if (action.includes('login') || form.id === 'loginForm' || (form.querySelector('input[name="password"]') && !action.includes('password'))) {
            showVeil();
        }
    }, true);
})();
</script>
