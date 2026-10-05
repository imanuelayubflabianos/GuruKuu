/**
 * GuruKuu - Modern Human-Crafted Design System & Performance Engine
 * Official Portal for SMK Negeri 1 Bangsri
 * Supports: Dark/Light Mode, Bootstrap 5 Auto-Sync, Mobile Sidebar Drawer, Game Display Settings
 */

(function() {
    'use strict';

    // 1. Mode Terang Permanen (Dark Mode Dihapus Sepenuhnya Sesuai Permintaan)
    function applyTheme() {
        document.documentElement.setAttribute('data-theme', 'light');
        document.documentElement.setAttribute('data-bs-theme', 'light');
        document.documentElement.classList.remove('dark-theme');
        document.documentElement.style.colorScheme = 'light';
    }

    // Bersihkan seluruh pengaturan mode gelap lokal
    try {
        localStorage.removeItem('gk_theme');
        localStorage.removeItem('theme');
    } catch (e) {}

    // Terapkan langsung ke HTML
    applyTheme();

    const savedAnim = localStorage.getItem('gk_anim') ?? '1';
    const savedBlur = localStorage.getItem('gk_blur') ?? '1';
    const savedShadow = localStorage.getItem('gk_shadow') ?? '1';
    const savedCompact = localStorage.getItem('gk_compact') ?? '0';

    if (savedAnim === '0') document.documentElement.classList.add('no-anim');
    if (savedBlur === '0') document.documentElement.classList.add('no-blur');
    if (savedShadow === '0') document.documentElement.classList.add('no-shadow');
    if (savedCompact === '1') document.documentElement.classList.add('compact-mode');

    window.GuruKuuTheme = {
        getSystemTheme: function() { return 'light'; },
        applyTheme: applyTheme,

        // No-op kompatibilitas: Selalu tetap light mode
        toggleTheme: function() {
            applyTheme();
        },

        setTheme: function() {
            applyTheme();
        },

        updateIcons: function() {
            // Sembunyikan atau netralkan ikon tema jika ada
            document.querySelectorAll('.gk-theme-icon').forEach(icon => {
                icon.style.display = 'none';
            });
        },

        // Mobile Sidebar Drawer
        toggleSidebar: function() {
            const sidebar = document.querySelector('.sidebar');
            let backdrop = document.querySelector('.gk-sidebar-backdrop');
            if (!sidebar) return;

            if (!backdrop) {
                backdrop = document.createElement('div');
                backdrop.className = 'gk-sidebar-backdrop';
                document.body.appendChild(backdrop);
                backdrop.addEventListener('click', () => this.closeSidebar());
            }

            const isOpen = sidebar.classList.contains('show');
            if (isOpen) {
                this.closeSidebar();
            } else {
                sidebar.classList.add('show');
                backdrop.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
        },

        closeSidebar: function() {
            const sidebar = document.querySelector('.sidebar');
            const backdrop = document.querySelector('.gk-sidebar-backdrop');
            if (sidebar) sidebar.classList.remove('show');
            if (backdrop) backdrop.classList.remove('show');
            document.body.style.overflow = '';
        },

        // Auto-Hardware Performance Detection (Zero-config, adapts automatically to device)
        detectAndApplyOptimalPerformance: function() {
            try {
                const isReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                const isLowCpu = navigator.hardwareConcurrency && navigator.hardwareConcurrency <= 4;
                const isLowMemory = navigator.deviceMemory && navigator.deviceMemory <= 4;
                const isLowEnd = isReducedMotion || isLowCpu || isLowMemory;

                if (isLowEnd) {
                    document.documentElement.classList.add('no-anim', 'no-blur', 'no-shadow');
                }
            } catch (e) {}
        },

        // Legacy safe no-op
        openDisplayModal: function() {}
    };

    // Auto-run optimal performance detection
    GuruKuuTheme.detectAndApplyOptimalPerformance();

    // DOM Ready setup
    document.addEventListener('DOMContentLoaded', function() {
        GuruKuuTheme.updateIcons();

        // Close sidebar on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                GuruKuuTheme.closeSidebar();
            }
        });

        // Close sidebar when clicking actual navigation links on mobile (exclude collapse/dropdown toggles)
        document.querySelectorAll('.sidebar .sidebar-link, .sidebar .sidebar-menu a, .sidebar .sidebar-submenu a').forEach(link => {
            link.addEventListener('click', function(e) {
                // Jangan tutup sidebar jika ini adalah tombol toggle accordion / dropdown
                if (this.hasAttribute('data-bs-toggle') || 
                    this.closest('[data-bs-toggle]') ||
                    this.tagName.toLowerCase() !== 'a' ||
                    !this.getAttribute('href') ||
                    this.getAttribute('href').startsWith('#') ||
                    this.getAttribute('href').startsWith('javascript:')) {
                    return;
                }

                if (window.innerWidth < 992) {
                    GuruKuuTheme.closeSidebar();
                }
            });
        });

        // Anti-Abuse: Prevent double/spam form submission across entire site
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form && form.tagName === 'FORM') {
                if (form.dataset.submitting === 'true') {
                    e.preventDefault();
                    return false;
                }
                form.dataset.submitting = 'true';
                const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                if (submitBtn) {
                    setTimeout(() => {
                        submitBtn.classList.add('disabled');
                        submitBtn.style.pointerEvents = 'none';
                        submitBtn.style.opacity = '0.75';
                    }, 50);
                }
                // Fallback unlock after 4 seconds (e.g., if submission was blocked by invalid HTML5 constraint)
                setTimeout(() => {
                    delete form.dataset.submitting;
                    if (submitBtn) {
                        submitBtn.classList.remove('disabled');
                        submitBtn.style.pointerEvents = '';
                        submitBtn.style.opacity = '';
                    }
                }, 4000);
            }
        }, true);
    });
})();
