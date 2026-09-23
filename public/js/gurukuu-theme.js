/**
 * GuruKuu - Modern Human-Crafted Design System & Performance Engine
 * Official Portal for SMK Negeri 1 Bangsri
 * Supports: Dark/Light Mode, Bootstrap 5 Auto-Sync, Mobile Sidebar Drawer, Game Display Settings
 */

(function() {
    'use strict';

    // 1. Initial Fast-Boot (Zero Flicker / Instant Exec before render)
    const savedTheme = localStorage.getItem('gk_theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    document.documentElement.setAttribute('data-theme', savedTheme);
    document.documentElement.setAttribute('data-bs-theme', savedTheme);
    if (savedTheme === 'dark') {
        document.documentElement.classList.add('dark-theme');
    } else {
        document.documentElement.classList.remove('dark-theme');
    }

    const savedAnim = localStorage.getItem('gk_anim') ?? '1';
    const savedBlur = localStorage.getItem('gk_blur') ?? '1';
    const savedShadow = localStorage.getItem('gk_shadow') ?? '1';
    const savedCompact = localStorage.getItem('gk_compact') ?? '0';

    if (savedAnim === '0') document.documentElement.classList.add('no-anim');
    if (savedBlur === '0') document.documentElement.classList.add('no-blur');
    if (savedShadow === '0') document.documentElement.classList.add('no-shadow');
    if (savedCompact === '1') document.documentElement.classList.add('compact-mode');

    window.GuruKuuTheme = {
        // Toggle Dark / Light Theme
        toggleTheme: function() {
            const current = document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            this.setTheme(next);
        },

        setTheme: function(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            document.documentElement.setAttribute('data-bs-theme', theme);
            if (theme === 'dark') {
                document.documentElement.classList.add('dark-theme');
            } else {
                document.documentElement.classList.remove('dark-theme');
            }
            localStorage.setItem('gk_theme', theme);
            this.updateIcons();
        },

        updateIcons: function() {
            const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
            document.querySelectorAll('.gk-theme-icon').forEach(icon => {
                if (isDark) {
                    icon.className = 'bi bi-sun-fill gk-theme-icon';
                    icon.style.color = '#fbbf24';
                } else {
                    icon.className = 'bi bi-moon-stars-fill gk-theme-icon';
                    icon.style.color = '#475569';
                }
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

        // Close sidebar when clicking links on mobile
        document.querySelectorAll('.sidebar .sidebar-link, .sidebar .sidebar-menu a').forEach(link => {
            link.addEventListener('click', function() {
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
