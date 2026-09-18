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
        warning: '#f59e0b',
        success: '#10b981',
        cancel: '#64748b'
    };

    /**
     * Show custom modern confirmation modal
     */
    window.gurukuuConfirm = function (options) {
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
            confirmColor = '#d97706';
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: confirmColor,
                cancelButtonColor: THEME.cancel,
                confirmButtonText: confirmButtonText,
                cancelButtonText: cancelButtonText,
                reverseButtons: true,
                borderRadius: '16px',
                customClass: {
                    popup: 'gurukuu-swal-popup',
                    title: 'gurukuu-swal-title',
                    confirmButton: 'gurukuu-swal-btn-confirm',
                    cancelButton: 'gurukuu-swal-btn-cancel'
                },
                didOpen: () => {
                    const popup = Swal.getPopup();
                    if (popup) {
                        popup.style.borderRadius = '16px';
                        popup.style.fontFamily = "'Inter', sans-serif";
                        popup.style.boxShadow = '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed && typeof onConfirm === 'function') {
                    onConfirm();
                }
            });
        } else {
            // Fallback if Swal is not yet loaded
            if (window.confirm(text)) {
                if (typeof onConfirm === 'function') onConfirm();
            }
        }
    };

    /**
     * Auto-intercept forms with data-confirm or legacy onsubmit="return confirm(...)"
     */
    function initConfirmInterceptors() {
        // 1. Convert legacy onsubmit="return confirm(...)" to data-confirm
        document.querySelectorAll('form').forEach(form => {
            const onsubmitAttr = form.getAttribute('onsubmit');
            if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
                const match = onsubmitAttr.match(/confirm\(\s*['"](.*?)['"]\s*\)/);
                if (match && match[1]) {
                    form.removeAttribute('onsubmit');
                    form.setAttribute('data-confirm', match[1].replace(/\\'/g, "'").replace(/\\"/g, '"'));
                    if (match[1].toLowerCase().includes('hapus') || match[1].toLowerCase().includes('delete') || match[1].toLowerCase().includes('peringatan!')) {
                        form.setAttribute('data-confirm-type', 'danger');
                        form.setAttribute('data-confirm-btn', 'Ya, Hapus');
                    }
                }
            }
        });

        // 2. Delegate submit listener for data-confirm forms
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (!form || !form.hasAttribute('data-confirm')) return;

            // If already confirmed, let it submit naturally
            if (form.getAttribute('data-confirmed') === 'true') {
                form.removeAttribute('data-confirmed');
                return;
            }

            e.preventDefault();

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
                    form.submit();
                }
            });
        }, true);

        // 3. Delegate click listener for links / buttons with data-confirm
        document.addEventListener('click', function (e) {
            const target = e.target.closest('[data-confirm-click]');
            if (!target) return;

            e.preventDefault();
            const message = target.getAttribute('data-confirm-click');
            const title = target.getAttribute('data-confirm-title') || 'Konfirmasi Tindakan';
            const href = target.getAttribute('href');

            window.gurukuuConfirm({
                title: title,
                text: message,
                icon: 'warning',
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
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Informasi',
                text: message,
                icon: 'info',
                confirmButtonColor: THEME.primary,
                confirmButtonText: 'Mengerti',
                customClass: {
                    popup: 'gurukuu-swal-popup'
                }
            });
        } else {
            originalAlert(message);
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initConfirmInterceptors);
    } else {
        initConfirmInterceptors();
    }
})();
