/**
 * GuruKuu Custom Modal & Confirmation System
 * Replaces native browser alert() and confirm() dialogs with elegant, modern SweetAlert2 pop-ups.
 */

(function () {
    'use strict';

    // Theme configuration
    const THEME = {
        primary: '#003366',
        primaryHover: '#002244',
        danger: '#dc2626',
        dangerHover: '#b91c1c',
        warning: '#d97706',
        warningHover: '#b45309',
        success: '#10b981',
        cancel: '#64748b',
        cancelHover: '#475569'
    };

    // Inject stylish SweetAlert2 CSS rules matching GuruKuu Design System
    function injectCustomStyles() {
        if (document.getElementById('gurukuu-swal-styles')) return;
        const style = document.createElement('style');
        style.id = 'gurukuu-swal-styles';
        style.textContent = `
            .gurukuu-swal-popup {
                border-radius: 20px !important;
                padding: 1.75rem 1.5rem !important;
                font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
                box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25) !important;
                border: 1px solid rgba(226, 232, 240, 0.8) !important;
            }
            .gurukuu-swal-title {
                font-size: 1.25rem !important;
                font-weight: 700 !important;
                color: #0f172a !important;
                margin-bottom: 0.5rem !important;
                letter-spacing: -0.01em !important;
            }
            .gurukuu-swal-html {
                font-size: 0.95rem !important;
                color: #475569 !important;
                line-height: 1.55 !important;
                margin: 0.5rem 0 1.25rem 0 !important;
            }
            .gurukuu-swal-actions {
                gap: 0.75rem !important;
                margin-top: 1rem !important;
            }
            .gurukuu-swal-btn-confirm {
                border-radius: 12px !important;
                padding: 0.65rem 1.4rem !important;
                font-weight: 600 !important;
                font-size: 0.9rem !important;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1) !important;
                transition: all 0.2s ease !important;
            }
            .gurukuu-swal-btn-confirm:hover {
                transform: translateY(-1px) !important;
                box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.15) !important;
            }
            .gurukuu-swal-btn-cancel {
                border-radius: 12px !important;
                padding: 0.65rem 1.3rem !important;
                font-weight: 600 !important;
                font-size: 0.9rem !important;
                background-color: #f1f5f9 !important;
                color: #475569 !important;
                border: 1px solid #cbd5e1 !important;
                transition: all 0.2s ease !important;
            }
            .gurukuu-swal-btn-cancel:hover {
                background-color: #e2e8f0 !important;
                color: #1e293b !important;
                transform: translateY(-1px) !important;
            }
            .swal2-icon {
                border-width: 3px !important;
                margin: 1rem auto 0.75rem auto !important;
            }
        `;
        document.head.appendChild(style);
    }

    /**
     * Show custom modern confirmation modal
     */
    window.gurukuuConfirm = function (options) {
        injectCustomStyles();

        const {
            title = 'Konfirmasi Tindakan',
            text = 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
            icon = 'warning',
            confirmButtonText = 'Ya, Lanjutkan',
            cancelButtonText = 'Batal',
            type = 'warning', // 'warning', 'danger', 'question', 'info'
            onConfirm = null
        } = options;

        let confirmColor = THEME.primary;
        if (type === 'danger' || options.isDanger) {
            confirmColor = THEME.danger;
        } else if (type === 'warning') {
            confirmColor = THEME.warning;
        }

        if (typeof Swal !== 'undefined') {
            return Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: confirmColor,
                cancelButtonColor: THEME.cancel,
                confirmButtonText: confirmButtonText,
                cancelButtonText: cancelButtonText,
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    popup: 'gurukuu-swal-popup',
                    title: 'gurukuu-swal-title',
                    htmlContainer: 'gurukuu-swal-html',
                    actions: 'gurukuu-swal-actions',
                    confirmButton: 'gurukuu-swal-btn-confirm',
                    cancelButton: 'gurukuu-swal-btn-cancel'
                }
            }).then((result) => {
                if (result.isConfirmed && typeof onConfirm === 'function') {
                    onConfirm();
                }
                return result;
            });
        } else {
            // Fallback if Swal is not loaded
            if (window.confirm(text)) {
                if (typeof onConfirm === 'function') onConfirm();
                return Promise.resolve({ isConfirmed: true });
            }
            return Promise.resolve({ isConfirmed: false });
        }
    };

    /**
     * Convert legacy onsubmit="return confirm(...)" on a form to data-confirm
     */
    function convertLegacyOnsubmit(form) {
        if (!form) return;
        const onsubmitAttr = form.getAttribute('onsubmit');
        if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
            let msg = '';
            // Try extracting literal argument
            const match = onsubmitAttr.match(/confirm\s*\(\s*(['"`])([\s\S]*?)\1\s*\)/i);
            if (match && match[2]) {
                msg = match[2];
            } else {
                const firstParen = onsubmitAttr.indexOf('confirm(');
                if (firstParen !== -1) {
                    const sub = onsubmitAttr.slice(firstParen + 8);
                    const lastParen = sub.lastIndexOf(')');
                    if (lastParen !== -1) {
                        msg = sub.slice(0, lastParen).trim().replace(/^['"`]|['"`]$/g, '');
                    }
                }
            }

            // CRUCIAL: Remove attribute AND clear DOM property so native confirm never triggers!
            form.removeAttribute('onsubmit');
            form.onsubmit = null;

            if (msg && !form.hasAttribute('data-confirm')) {
                form.setAttribute('data-confirm', msg);
                const lower = msg.toLowerCase();
                if (lower.includes('hapus') || lower.includes('delete') || lower.includes('kosongkan') || lower.includes('peringatan') || lower.includes('nonaktif') || lower.includes('reset')) {
                    form.setAttribute('data-confirm-type', 'danger');
                    form.setAttribute('data-confirm-btn', lower.includes('hapus') ? 'Ya, Hapus' : (lower.includes('reset') ? 'Ya, Reset' : 'Ya, Lanjutkan'));
                } else if (lower.includes('keluarkan') || lower.includes('logout') || lower.includes('bersihkan')) {
                    form.setAttribute('data-confirm-type', 'warning');
                    form.setAttribute('data-confirm-btn', lower.includes('keluarkan') ? 'Ya, Keluarkan' : 'Ya, Bersihkan');
                }
            }
        }
    }

    /**
     * Auto-intercept forms with data-confirm or legacy onsubmit="return confirm(...)"
     */
    function initConfirmInterceptors() {
        injectCustomStyles();

        // 1. Initial scan: convert all legacy onsubmit on existing forms
        document.querySelectorAll('form').forEach(convertLegacyOnsubmit);

        // 2. Capture-phase click listener on submit buttons:
        // Ensures any dynamic form or freshly loaded element has its onsubmit neutralized before submit fires
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('button[type="submit"], input[type="submit"], button:not([type])');
            if (btn && btn.form) {
                convertLegacyOnsubmit(btn.form);
            }
        }, true);

        // 3. Delegate submit listener for data-confirm forms in CAPTURE phase
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (!form) return;

            // In case form still has onsubmit with confirm
            convertLegacyOnsubmit(form);

            if (!form.hasAttribute('data-confirm')) return;

            // If already confirmed by our SweetAlert2, let it submit naturally
            if (form.getAttribute('data-confirmed') === 'true') {
                form.removeAttribute('data-confirmed');
                return;
            }

            e.preventDefault();
            e.stopImmediatePropagation();

            const message = form.getAttribute('data-confirm');
            const title = form.getAttribute('data-confirm-title') || 'Konfirmasi Tindakan';
            const btnText = form.getAttribute('data-confirm-btn') || 'Ya, Lanjutkan';
            const type = form.getAttribute('data-confirm-type') || 'warning';

            let icon = 'question';
            if (type === 'danger') icon = 'warning';
            else if (type === 'warning') icon = 'warning';
            else if (type === 'info') icon = 'info';

            window.gurukuuConfirm({
                title: title,
                text: message,
                icon: icon,
                confirmButtonText: btnText,
                cancelButtonText: 'Batal',
                type: type,
                onConfirm: function () {
                    form.setAttribute('data-confirmed', 'true');
                    const submitBtn = form.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Memproses...';
                        submitBtn.classList.add('disabled');
                    }
                    form.submit();
                }
            });
        }, true);

        // 4. Delegate click listener for links / buttons with data-confirm-click
        document.addEventListener('click', function (e) {
            const target = e.target.closest('[data-confirm-click]');
            if (!target) return;

            e.preventDefault();
            const message = target.getAttribute('data-confirm-click');
            const title = target.getAttribute('data-confirm-title') || 'Konfirmasi Tindakan';
            const btnText = target.getAttribute('data-confirm-btn') || 'Ya, Lanjutkan';
            const type = target.getAttribute('data-confirm-type') || 'warning';
            const href = target.getAttribute('href');

            window.gurukuuConfirm({
                title: title,
                text: message,
                icon: type === 'danger' ? 'warning' : 'question',
                confirmButtonText: btnText,
                cancelButtonText: 'Batal',
                type: type,
                onConfirm: function () {
                    if (href && href !== '#' && !href.startsWith('javascript:')) {
                        window.location.href = href;
                    }
                }
            });
        });
    }

    // Override browser native alert to use SweetAlert2
    const originalAlert = window.alert;
    window.alert = function (message) {
        injectCustomStyles();
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Informasi',
                text: message,
                icon: 'info',
                confirmButtonColor: THEME.primary,
                confirmButtonText: 'Mengerti',
                customClass: {
                    popup: 'gurukuu-swal-popup',
                    title: 'gurukuu-swal-title',
                    htmlContainer: 'gurukuu-swal-html',
                    confirmButton: 'gurukuu-swal-btn-confirm'
                }
            });
        } else {
            originalAlert(message);
        }
    };

    /**
     * Universal Tooltips with Mobile Touch/Hold & Desktop Hover Support
     */
    function initUniversalTooltips() {
        if (typeof bootstrap === 'undefined' || !bootstrap.Tooltip) return;

        const targets = document.querySelectorAll('[data-bs-toggle="tooltip"]');

        targets.forEach(function (el) {
            if (el.closest('.sidebar-profile') || el.classList.contains('sidebar-profile')) {
                el.removeAttribute('title');
                el.removeAttribute('data-bs-toggle');
                return;
            }

            const rawTitle = el.getAttribute('title') || el.getAttribute('data-bs-title') || el.getAttribute('data-bs-original-title');
            if (!rawTitle || rawTitle.trim() === '') return;

            const clone = el.cloneNode(true);
            const icons = clone.querySelectorAll('i, svg, img');
            icons.forEach(function(i) { i.remove(); });
            const visibleText = clone.textContent.trim();
            if (visibleText.length > 3 && !el.hasAttribute('data-force-tooltip')) {
                el.removeAttribute('title');
                el.removeAttribute('data-bs-toggle');
                return;
            }

            try {
                bootstrap.Tooltip.getOrCreateInstance(el, {
                    trigger: 'hover focus',
                    delay: { show: 150, hide: 100 }
                });
            } catch (e) {
                // Ignore initialization error
            }
        });
    }

    window.initGuruKuuTooltips = initUniversalTooltips;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initConfirmInterceptors();
            initUniversalTooltips();
        });
    } else {
        initConfirmInterceptors();
        initUniversalTooltips();
    }
})();
