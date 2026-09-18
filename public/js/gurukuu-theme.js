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

        // Game-Style Display / Graphics Settings
        openDisplayModal: function() {
            let modalEl = document.getElementById('gkDisplayModal');
            if (!modalEl) {
                this.injectModalHtml();
                modalEl = document.getElementById('gkDisplayModal');
            }
            if (typeof bootstrap !== 'undefined') {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        },

        applyPreset: function(preset) {
            if (preset === 'performance') {
                // Ultra ringan / HP kentang
                this.setToggle('anim', false);
                this.setToggle('blur', false);
                this.setToggle('shadow', false);
            } else if (preset === 'quality') {
                // Grafis Penuh
                this.setToggle('anim', true);
                this.setToggle('blur', true);
                this.setToggle('shadow', true);
            }
            this.syncModalInputs();
        },

        setToggle: function(key, active) {
            localStorage.setItem(`gk_${key}`, active ? '1' : '0');
            const classMap = {
                anim: 'no-anim',
                blur: 'no-blur',
                shadow: 'no-shadow',
                compact: 'compact-mode'
            };
            const cls = classMap[key];
            if (!cls) return;

            if (key === 'compact') {
                if (active) document.documentElement.classList.add(cls);
                else document.documentElement.classList.remove(cls);
            } else {
                if (!active) document.documentElement.classList.add(cls);
                else document.documentElement.classList.remove(cls);
            }
        },

        syncModalInputs: function() {
            const isAnim = localStorage.getItem('gk_anim') !== '0';
            const isBlur = localStorage.getItem('gk_blur') !== '0';
            const isShadow = localStorage.getItem('gk_shadow') !== '0';
            const isCompact = localStorage.getItem('gk_compact') === '1';

            const chkAnim = document.getElementById('gkChkAnim');
            const chkBlur = document.getElementById('gkChkBlur');
            const chkShadow = document.getElementById('gkChkShadow');
            const chkCompact = document.getElementById('gkChkCompact');

            if (chkAnim) chkAnim.checked = isAnim;
            if (chkBlur) chkBlur.checked = isBlur;
            if (chkShadow) chkShadow.checked = isShadow;
            if (chkCompact) chkCompact.checked = isCompact;
        },

        injectModalHtml: function() {
            const isAnim = localStorage.getItem('gk_anim') !== '0';
            const isBlur = localStorage.getItem('gk_blur') !== '0';
            const isShadow = localStorage.getItem('gk_shadow') !== '0';
            const isCompact = localStorage.getItem('gk_compact') === '1';

            const html = `
            <div class="modal fade" id="gkDisplayModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="modal-header bg-primary text-white border-0 py-3">
                            <h6 class="modal-title fw-bold d-flex align-items-center gap-2">
                                <i class="bi bi-sliders2-vertical fs-5 text-warning"></i>
                                Pengaturan Grafis & Performa Layar
                            </h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <p class="text-muted small mb-3">Sesuaikan kinerja visual antarmuka agar mulus, hemat kuota dan baterai, khususnya di perangkat ponsel (HP).</p>
                            
                            <!-- PRESET QUICK BUTTONS -->
                            <div class="mb-4 p-3 bg-light rounded-3 border">
                                <label class="fw-bold small d-block mb-2 text-dark">Pilihan Profil Cepat (Preset):</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <button type="button" class="btn btn-outline-primary btn-sm w-100 fw-semibold" onclick="GuruKuuTheme.applyPreset('performance')">
                                            ⚡ Ultra Ringan (HP)
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button type="button" class="btn btn-outline-secondary btn-sm w-100 fw-semibold" onclick="GuruKuuTheme.applyPreset('quality')">
                                            ✨ Grafis Penuh
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- INDIVIDUAL TOGGLES -->
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="fw-bold text-dark small">Efek Animasi & Transisi</div>
                                        <small class="text-muted">Animasi perpindahan menu, tombol, dan modal</small>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="gkChkAnim" ${isAnim ? 'checked' : ''} onchange="GuruKuuTheme.setToggle('anim', this.checked)">
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="fw-bold text-dark small">Efek Kaca & Blur (Glassmorphism)</div>
                                        <small class="text-muted">Matikan untuk meringankan kerja prosesor & GPU HP</small>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="gkChkBlur" ${isBlur ? 'checked' : ''} onchange="GuruKuuTheme.setToggle('blur', this.checked)">
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="fw-bold text-dark small">Bayangan Lembut (Shadows)</div>
                                        <small class="text-muted">Kedalaman visual kartu dan tombol</small>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="gkChkShadow" ${isShadow ? 'checked' : ''} onchange="GuruKuuTheme.setToggle('shadow', this.checked)">
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                    <div>
                                        <div class="fw-bold text-dark small">Tampilan Padat (Compact Mode)</div>
                                        <small class="text-muted">Optimalkan ruang layar agar memuat lebih banyak konten di HP</small>
                                    </div>
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" id="gkChkCompact" ${isCompact ? 'checked' : ''} onchange="GuruKuuTheme.setToggle('compact', this.checked)">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light py-2 px-4 border-0">
                            <button type="button" class="btn btn-primary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Selesai</button>
                        </div>
                    </div>
                </div>
            </div>`;
            document.body.insertAdjacentHTML('beforeend', html);
        }
    };

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
