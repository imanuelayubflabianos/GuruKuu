@php
    $prefix = $prefix ?? 'main';
    $totalNotif = ($unreadPelanggaranCount ?? 0) + ($unreadChatCount ?? 0);
    $defaultTab = (($unreadPelanggaranCount ?? 0) == 0 && ($unreadChatCount ?? 0) > 0) ? 'chat' : 'pelanggaran';
@endphp

<li class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between" onclick="event.stopPropagation();">
    <div class="d-flex align-items-center gap-2">
        <i class="bi bi-bell-fill fs-6" style="color: #f59e0b;"></i>
        <span class="fw-bold text-dark small">Pusat Notifikasi</span>
    </div>
    <div class="d-flex align-items-center gap-1.5">
        <span class="badge gk-notif-badge rounded-pill admin-header-badge" style="font-size: 0.7rem; {{ $totalNotif > 0 ? '' : 'display: none !important;' }}">
            <span class="admin-header-count">{{ $totalNotif }}</span> Baru
        </span>
        <button type="button" class="btn btn-link p-0 text-muted small text-decoration-none ms-1 btn-clear-all-admin-notifs" title="Tandai semua telah dibaca" style="font-size: 0.68rem;">
            Tandai Dibaca
        </button>
    </div>
</li>

<li class="p-0 border-bottom bg-light" onclick="event.stopPropagation();">
    <ul class="nav nav-tabs nav-fill border-0 px-2 pt-2" id="notifTabs_{{ $prefix }}" role="tablist" style="font-size: 0.8rem;" onclick="event.stopPropagation();">
        <li class="nav-item" role="presentation" onclick="event.stopPropagation();">
            <button class="nav-link py-2 fw-semibold d-flex align-items-center justify-content-center gap-1.5 {{ $defaultTab === 'pelanggaran' ? 'active' : '' }}" 
                    id="tab-pelanggaran-{{ $prefix }}" 
                    data-bs-toggle="tab" 
                    data-bs-target="#pane-pelanggaran-{{ $prefix }}" 
                    type="button" 
                    role="tab" 
                    onclick="event.stopPropagation();"
                    aria-controls="pane-pelanggaran-{{ $prefix }}" 
                    aria-selected="{{ $defaultTab === 'pelanggaran' ? 'true' : 'false' }}">
                <i class="bi bi-shield-exclamation text-primary"></i>
                <span>Pelanggaran</span>
                <span class="badge gk-notif-badge rounded-pill px-1.5 py-0.5 tab-badge-pelanggaran" style="font-size: 0.65rem; {{ ($unreadPelanggaranCount ?? 0) > 0 ? '' : 'display: none !important;' }}">
                    {{ $unreadPelanggaranCount }}
                </span>
            </button>
        </li>
        <li class="nav-item" role="presentation" onclick="event.stopPropagation();">
            <button class="nav-link py-2 fw-semibold d-flex align-items-center justify-content-center gap-1.5 {{ $defaultTab === 'chat' ? 'active' : '' }}" 
                    id="tab-chat-{{ $prefix }}" 
                    data-bs-toggle="tab" 
                    data-bs-target="#pane-chat-{{ $prefix }}" 
                    type="button" 
                    role="tab" 
                    onclick="event.stopPropagation();"
                    aria-controls="pane-chat-{{ $prefix }}" 
                    aria-selected="{{ $defaultTab === 'chat' ? 'true' : 'false' }}">
                <i class="bi bi-chat-dots-fill text-primary"></i>
                <span>Pesan Chat</span>
                <span class="badge gk-notif-badge rounded-pill px-1.5 py-0.5 tab-badge-chat" style="font-size: 0.65rem; {{ ($unreadChatCount ?? 0) > 0 ? '' : 'display: none !important;' }}">
                    {{ $unreadChatCount }}
                </span>
            </button>
        </li>
    </ul>
</li>

<div class="tab-content" id="notifTabContent_{{ $prefix }}" onclick="event.stopPropagation();">
    {{-- TAB PANE: LOG PELANGGARAN --}}
    <div class="tab-pane fade {{ $defaultTab === 'pelanggaran' ? 'show active' : '' }}" 
         id="pane-pelanggaran-{{ $prefix }}" 
         role="tabpanel" 
         aria-labelledby="tab-pelanggaran-{{ $prefix }}"
         onclick="event.stopPropagation();">
        <div style="max-height: 320px; overflow-y: auto;">
            @forelse(($recentPelanggarans ?? collect()) as $notif)
                <div class="admin-notif-row" data-notif-id="pelanggaran_{{ $notif->id }}" data-notif-type="pelanggaran">
                    <a class="dropdown-item p-3 border-bottom text-wrap admin-notif-click" 
                       href="{{ route('admin.pelanggaran.index') }}"
                       data-pelanggaran-id="{{ $notif->id }}"
                       data-read-url="{{ route('pelanggaran.read', $notif->id) }}">
                        <div class="d-flex align-items-start gap-2.5">
                            <span class="badge bg-primary text-white rounded-circle p-1.5 mt-0.5 flex-shrink-0">
                                <i class="bi bi-exclamation-octagon"></i>
                            </span>
                            <div class="flex-grow-1 min-w-0" style="min-width: 0;">
                                @if($notif->user)
                                    <div class="d-flex justify-content-between align-items-center mb-0.5">
                                        <strong class="text-dark small text-truncate" style="font-size: 0.85rem;">{{ $notif->user->name }}</strong>
                                        @if($notif->user->warning_count > 0)
                                            <span class="badge bg-warning text-dark font-mono flex-shrink-0 ms-1" style="font-size: 0.65rem;">⚠️ {{ $notif->user->warning_count }}x Sanksi</span>
                                        @endif
                                    </div>
                                    <div class="text-dark small font-mono text-truncate" style="font-size: 0.73rem;">
                                        NIS: <strong>{{ $notif->user->nis ?? '-' }}</strong> &bull; {{ $notif->user->nama_kelas }}
                                    </div>
                                @else
                                    <div class="fw-bold text-dark small">Tamu Publik (Anonim)</div>
                                    <div class="text-muted small font-mono" style="font-size: 0.7rem;">IP: {{ $notif->ip_address ?? '-' }}</div>
                                @endif

                                <div class="text-muted my-1 small text-truncate" style="font-size: 0.72rem;">
                                    <span class="badge bg-light text-secondary border me-1">{{ $notif->tipe_label }}</span>
                                    @if($notif->guru)
                                        ke Guru: <strong class="text-dark">{{ $notif->guru->nama }}</strong>
                                    @endif
                                </div>

                                @if(is_array($notif->kata_terdeteksi) && count($notif->kata_terdeteksi) > 0)
                                    <div class="mb-1 d-flex flex-wrap gap-1">
                                        @foreach($notif->kata_terdeteksi as $kw)
                                            <span class="badge bg-light text-primary border px-1.5 py-0.5" style="font-size: 0.68rem;">
                                                "{{ $kw }}"
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <span class="text-secondary font-mono" style="font-size: 0.68rem;">
                                        <i class="bi bi-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}
                                    </span>
                                    <span class="text-primary small fw-semibold" style="font-size: 0.72rem;">
                                        Lihat Detail &rarr;
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="p-4 text-center text-muted small">
                    <i class="bi bi-shield-check text-success fs-3 d-block mb-1"></i>
                    Tidak ada pelanggaran baru
                </div>
            @endforelse
        </div>
        <div class="p-2 text-center bg-light border-top">
            <a href="{{ route('admin.pelanggaran.index') }}" class="btn btn-outline-custom btn-sm w-100 py-1.5 rounded-pill fw-semibold" style="font-size: 0.8rem;">
                Kelola & Tinjau Semua Pelanggaran
            </a>
        </div>
    </div>

    {{-- TAB PANE: PESAN CHAT ADMIN --}}
    <div class="tab-pane fade {{ $defaultTab === 'chat' ? 'show active' : '' }}" 
         id="pane-chat-{{ $prefix }}" 
         role="tabpanel" 
         aria-labelledby="tab-chat-{{ $prefix }}"
         onclick="event.stopPropagation();">
        <div style="max-height: 320px; overflow-y: auto;">
            @forelse(($recentChats ?? collect()) as $c)
                <div class="admin-notif-row" data-notif-id="chat_{{ $c->id }}" data-notif-type="chat">
                    <a class="dropdown-item p-3 border-bottom text-wrap admin-notif-click" 
                       href="{{ route('admin.kontak.chat', $c->identifier) }}"
                       data-chat-id="{{ $c->id }}">
                        <div class="d-flex align-items-start gap-2.5">
                            <span class="badge bg-primary-subtle text-primary rounded-circle p-1.5 mt-0.5 flex-shrink-0">
                                <i class="bi bi-chat-quote-fill"></i>
                            </span>
                            <div class="flex-grow-1 min-w-0" style="min-width: 0;">
                                <div class="d-flex justify-content-between align-items-center mb-0.5">
                                    <strong class="text-dark small text-truncate" style="font-size: 0.85rem;">{{ $c->pengirim }}</strong>
                                    <span class="badge bg-light text-muted border font-mono flex-shrink-0 ms-1" style="font-size: 0.65rem;">{{ $c->is_siswa ? 'Siswa' : 'Pengguna/Guru' }}</span>
                                </div>
                                <p class="text-muted mb-1 small text-truncate" style="max-width: 250px; font-size: 0.78rem;">
                                    "{{ Str::limit($c->pesan, 55) }}"
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted font-mono" style="font-size: 0.68rem;">{{ $c->created_at->diffForHumans() }}</span>
                                    <span class="text-primary small fw-semibold" style="font-size: 0.72rem;">Buka Chat &rarr;</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="p-4 text-center text-muted small">
                    <i class="bi bi-chat-check text-success fs-3 d-block mb-1"></i>
                    Tidak ada pesan chat masuk baru
                </div>
            @endforelse
        </div>
        <div class="p-2 text-center bg-light border-top">
            <a href="{{ route('admin.kontak.index') }}" class="btn btn-outline-custom btn-sm w-100 py-1.5 rounded-pill fw-semibold" style="font-size: 0.8rem;">
                Lihat Semua Pesan Masuk
            </a>
        </div>
    </div>
</div>

<script>
(function() {
    const adminStorageKey = 'gk_read_admin_notifs_' + "{{ auth()->id() ?? 'admin' }}";

    function getAdminRead() {
        try { return JSON.parse(localStorage.getItem(adminStorageKey) || '[]'); }
        catch(e) { return []; }
    }

    function saveAdminRead(list) {
        try { localStorage.setItem(adminStorageKey, JSON.stringify(list)); }
        catch(e) {}
    }

    function syncAdminNotifUI() {
        const readList = getAdminRead();
        const rows = document.querySelectorAll('.admin-notif-row');
        let unreadCount = 0;

        rows.forEach(r => {
            const id = r.getAttribute('data-notif-id');
            if (id && readList.includes(id)) {
                r.style.opacity = '0.55';
            } else {
                unreadCount++;
                r.style.opacity = '1';
            }
        });

        // Update badge dan icon lonceng admin
        const bells = document.querySelectorAll('.admin-bell-icon');
        const badges = document.querySelectorAll('.admin-notif-badge');
        const counts = document.querySelectorAll('.admin-notif-count');
        const headerCounts = document.querySelectorAll('.admin-header-count');
        const headerBadges = document.querySelectorAll('.admin-header-badge');

        counts.forEach(c => c.textContent = unreadCount);
        headerCounts.forEach(c => c.textContent = unreadCount);

        if (unreadCount > 0) {
            bells.forEach(b => {
                b.classList.remove('no-unread');
                b.classList.add('has-unread');
            });
            badges.forEach(b => b.style.setProperty('display', 'inline-block', 'important'));
            headerBadges.forEach(b => b.style.setProperty('display', 'inline-block', 'important'));
        } else {
            bells.forEach(b => {
                b.classList.remove('has-unread');
                b.classList.add('no-unread');
            });
            badges.forEach(b => b.style.setProperty('display', 'none', 'important'));
            headerBadges.forEach(b => b.style.setProperty('display', 'none', 'important'));
        }
    }

    // Klik per item notifikasi admin
    document.addEventListener('click', function(e) {
        const clickLink = e.target.closest('.admin-notif-click');
        if (clickLink) {
            const row = clickLink.closest('.admin-notif-row');
            if (row) {
                const id = row.getAttribute('data-notif-id');
                if (id) {
                    const readList = getAdminRead();
                    if (!readList.includes(id)) {
                        readList.push(id);
                        saveAdminRead(readList);
                    }
                }
                const readUrl = clickLink.getAttribute('data-read-url');
                if (readUrl) {
                    fetch(readUrl, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    }).catch(() => {});
                }
            }
        }

        const clearBtn = e.target.closest('.btn-clear-all-admin-notifs');
        if (clearBtn) {
            e.preventDefault();
            e.stopPropagation();
            const rows = document.querySelectorAll('.admin-notif-row');
            const readList = getAdminRead();
            rows.forEach(r => {
                const id = r.getAttribute('data-notif-id');
                if (id && !readList.includes(id)) readList.push(id);
            });
            saveAdminRead(readList);
            syncAdminNotifUI();

            // Panggil read-all di server jika ada
            fetch("{{ route('pelanggaran.read-all') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            }).catch(() => {});
        }
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', syncAdminNotifUI);
    } else {
        syncAdminNotifUI();
    }
})();
</script>

