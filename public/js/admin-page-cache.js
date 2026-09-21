/*
 * Cache navigasi ringan untuk halaman Data Lokal.
 * Data tetap dimuat dari server pada kunjungan pertama, kemudian disimpan
 * pada sessionStorage selama tab browser masih terbuka.
 */
(function () {
    'use strict';

    const cacheablePaths = ['/admin/guru', '/admin/siswa', '/admin/jurusan'];
    const maxAge = 5 * 60 * 1000;
    const maxEntries = 9;
    const userId = document.body.dataset.adminCacheUser || 'guest';
    const keyPrefix = `gurukuu:admin-page:${userId}:`;

    function normalizedUrl(value) {
        const url = new URL(value, window.location.origin);
        return `${url.pathname}${url.search}`;
    }

    function isCacheable(value) {
        const url = new URL(value, window.location.origin);
        return cacheablePaths.includes(url.pathname);
    }

    function storageKey(value) {
        return keyPrefix + normalizedUrl(value);
    }

    function getPageScripts(root) {
        return Array.from(root.querySelectorAll('script:not([src])'))
            .map(script => script.textContent.trim())
            .filter(Boolean);
    }

    function readCache(value) {
        try {
            const raw = sessionStorage.getItem(storageKey(value));
            if (!raw) return null;

            const payload = JSON.parse(raw);
            if (!payload.expiresAt || payload.expiresAt < Date.now()) {
                sessionStorage.removeItem(storageKey(value));
                return null;
            }

            return payload;
        } catch (_) {
            return null;
        }
    }

    function pruneCache() {
        try {
            const keys = Object.keys(sessionStorage)
                .filter(key => key.startsWith(keyPrefix))
                .map(key => ({ key, cachedAt: JSON.parse(sessionStorage.getItem(key)).cachedAt || 0 }))
                .sort((a, b) => a.cachedAt - b.cachedAt);

            while (keys.length >= maxEntries) {
                sessionStorage.removeItem(keys.shift().key);
            }
        } catch (_) {
            // sessionStorage tidak wajib tersedia untuk navigasi normal.
        }
    }

    function saveCache(value, payload) {
        if (!isCacheable(value)) return;

        try {
            pruneCache();
            sessionStorage.setItem(storageKey(value), JSON.stringify({
                ...payload,
                cachedAt: Date.now(),
                expiresAt: Date.now() + maxAge,
            }));
        } catch (_) {
            // Jika penyimpanan penuh/dinonaktifkan, halaman tetap bekerja normal.
        }
    }

    function saveCurrentPage() {
        if (!isCacheable(window.location.href)) return;

        const main = document.querySelector('.main-content');
        if (!main) return;

        saveCache(window.location.href, {
            html: main.innerHTML,
            title: document.title,
            scripts: getPageScripts(document),
        });
    }

    function closeOpenModal() {
        document.querySelectorAll('.modal.show').forEach(modal => {
            window.bootstrap?.Modal.getInstance(modal)?.hide();
        });
        document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('padding-right');
    }

    function runScripts(scripts) {
        scripts.forEach(source => {
            const script = document.createElement('script');
            script.textContent = source;
            document.body.appendChild(script);
            script.remove();
        });
    }

    function updateNavigation(value) {
        const current = normalizedUrl(value);
        document.querySelectorAll('[data-admin-page-link]').forEach(link => {
            link.classList.toggle('active', normalizedUrl(link.href) === current);
        });

        if (isCacheable(value)) {
            document.getElementById('menuData')?.classList.add('show');
        }
    }

    function renderPage(payload, value) {
        const main = document.querySelector('.main-content');
        if (!main || !payload.html) {
            window.location.assign(value);
            return;
        }

        closeOpenModal();
        main.innerHTML = payload.html;
        document.title = payload.title || document.title;
        updateNavigation(value);
        runScripts(payload.scripts || []);
        window.scrollTo({ top: 0, behavior: 'instant' });
    }

    async function visit(value, pushState = true) {
        const target = new URL(value, window.location.origin);
        if (!isCacheable(target.href)) {
            window.location.assign(target.href);
            return;
        }

        const cached = readCache(target.href);
        if (cached) {
            saveCurrentPage();
            renderPage(cached, target.href);
            if (pushState) window.history.pushState({ adminPageCache: true }, '', target.href);
            return;
        }

        const main = document.querySelector('.main-content');
        main?.setAttribute('aria-busy', 'true');
        if (main) main.style.opacity = '.55';

        try {
            const response = await fetch(target.href, {
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!response.ok) throw new Error('Gagal memuat halaman');

            const documentResponse = new DOMParser().parseFromString(await response.text(), 'text/html');
            const responseMain = documentResponse.querySelector('.main-content');
            if (!responseMain) throw new Error('Konten halaman tidak ditemukan');

            const payload = {
                html: responseMain.innerHTML,
                title: documentResponse.title,
                scripts: getPageScripts(documentResponse),
            };

            saveCurrentPage();
            saveCache(target.href, payload);
            renderPage(payload, target.href);
            if (pushState) window.history.pushState({ adminPageCache: true }, '', target.href);
        } catch (_) {
            window.location.assign(target.href);
        } finally {
            if (main) {
                main.style.removeProperty('opacity');
                main.removeAttribute('aria-busy');
            }
        }
    }

    document.addEventListener('click', event => {
        const link = event.target.closest('[data-admin-page-link]');
        if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || link.target === '_blank') return;

        event.preventDefault();
        visit(link.href);
    });

    // Perubahan data tidak boleh meninggalkan cache tabel yang basi.
    document.addEventListener('submit', event => {
        const form = event.target;
        if ((form.method || 'get').toLowerCase() === 'get') return;

        try {
            Object.keys(sessionStorage)
                .filter(key => key.startsWith(keyPrefix))
                .forEach(key => sessionStorage.removeItem(key));
        } catch (_) {}
    }, true);

    window.addEventListener('popstate', () => visit(window.location.href, false));

    document.addEventListener('DOMContentLoaded', () => {
        saveCurrentPage();
        updateNavigation(window.location.href);
        window.history.replaceState({ adminPageCache: true }, '', window.location.href);
    });
})();
