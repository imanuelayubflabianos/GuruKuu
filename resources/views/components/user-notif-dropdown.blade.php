@php
    $user = auth()->user();
    if (!$user) {
        $notifs = collect();
        $totalCount = 0;
    } else {
        $role = $user->role ?? 'siswa';

        // In-memory static cache agar tidak perlu query berulang antar dropdown desktop & mobile pada 1 request
        static $userNotifsStaticCache = [];
        if (isset($userNotifsStaticCache[$user->id])) {
            $notifList = $userNotifsStaticCache[$user->id];
        } else {
            $notifList = [];

            if ($role === 'siswa') {
                // 1. Status Akun: Suspensi / Banned
                if (method_exists($user, 'isDeactivated') && $user->isDeactivated()) {
                    $isPermanent = $user->isPermanentlyDeactivated();
                    $durasiText = 'Permanen';
                    if (!$isPermanent && $user->deactivated_until) {
                        $diffDays = ceil(now()->diffInDays($user->deactivated_until, false));
                        if ($diffDays > 0) {
                            $durasiText = $diffDays . ' Hari (s/d ' . $user->deactivated_until->translatedFormat('d M Y') . ')';
                        } else {
                            $durasiText = 's/d ' . $user->deactivated_until->translatedFormat('d M Y H:i');
                        }
                    }
                    $notifList[] = [
                        'id' => 'suspensi_' . $user->id,
                        'type' => 'danger',
                        'icon' => 'bi-slash-circle-fill text-danger',
                        'title' => 'Akun Dinonaktifkan (' . $durasiText . ')',
                        'desc' => 'Akun Anda dinonaktifkan ' . ($isPermanent ? 'secara permanen' : 'selama ' . $durasiText) . '. Alasan: ' . ($user->deactivated_reason ?: 'Pelanggaran tata tertib ulasan.'),
                        'time' => 'Status Akun',
                        'url' => route('siswa.dashboard'),
                        'action_label' => 'Cek Status &rarr;',
                    ];
                }

                // 2. Notifikasi Pelanggaran / Peringatan Etika Belum Dibaca
                $pelanggarans = \App\Models\Pelanggaran::where('user_id', $user->id)
                    ->where('siswa_is_read', false)
                    ->latest()
                    ->take(3)
                    ->get();
                foreach ($pelanggarans as $p) {
                    $pUrl = $p->guru_id ? route('siswa.guru.show', $p->guru_id) : route('siswa.riwayat');
                    $notifList[] = [
                        'id' => 'pelanggaran_' . $p->id,
                        'type' => 'warning',
                        'icon' => 'bi-shield-exclamation text-warning',
                        'title' => 'Peringatan Tata Tertib',
                        'desc' => $p->notifikasi_siswa ?: 'Ulasan Anda terdeteksi mengandung kata yang tidak sesuai etika sekolah.',
                        'time' => $p->created_at->diffForHumans(),
                        'url' => $pUrl,
                        'action_label' => $p->guru_id ? 'Lihat Guru &rarr;' : 'Cek Riwayat &rarr;',
                    ];
                }

                // 3. Notifikasi Guru Membalas Ulasan Anda (Diskusi Thread)
                $guruThreadReplies = \App\Models\PenilaianBalasan::where('role', 'guru')
                    ->whereHas('penilaian', function($q) use ($user) {
                        $q->where('siswa_id', $user->id);
                    })
                    ->with(['penilaian.guru'])
                    ->latest()
                    ->take(3)
                    ->get();
                foreach ($guruThreadReplies as $gtr) {
                    $namaGuru = $gtr->penilaian?->guru?->nama ?? 'Guru';
                    $guruId = $gtr->penilaian?->guru_id;
                    $targetUrl = $guruId 
                        ? (route('siswa.guru.show', $guruId) . '#ulasan-' . $gtr->penilaian_id)
                        : route('siswa.riwayat');
                    $notifList[] = [
                        'id' => 'guru_reply_' . $gtr->id,
                        'type' => 'primary',
                        'icon' => 'bi-chat-dots text-primary',
                        'title' => "Guru {$namaGuru} Membalas Ulasan",
                        'desc' => '"' . \Illuminate\Support\Str::limit($gtr->pesan, 65) . '"',
                        'time' => $gtr->created_at->diffForHumans(),
                        'url' => $targetUrl,
                        'action_label' => 'Buka Diskusi &rarr;',
                    ];
                }

                // Balasan langsung di tabel penilaian (jika ada balasan_guru dan belum masuk di thread)
                $directReplies = \App\Models\Penilaian::where('siswa_id', $user->id)
                    ->whereNotNull('balasan_guru')
                    ->with('guru')
                    ->latest('updated_at')
                    ->take(2)
                    ->get();
                foreach ($directReplies as $dr) {
                    if (!$guruThreadReplies->contains('penilaian_id', $dr->id)) {
                        $namaGuru = $dr->guru?->nama ?? 'Guru';
                        $notifList[] = [
                            'id' => 'direct_reply_' . $dr->id,
                            'type' => 'primary',
                            'icon' => 'bi-reply text-primary',
                            'title' => "Guru {$namaGuru} Menanggapi Ulasan",
                            'desc' => '"' . \Illuminate\Support\Str::limit($dr->balasan_guru, 65) . '"',
                            'time' => $dr->updated_at->diffForHumans(),
                            'url' => route('siswa.guru.show', $dr->guru_id) . '#ulasan-' . $dr->id,
                            'action_label' => 'Lihat Balasan &rarr;',
                        ];
                    }
                }

                // 4. Status Periode Penilaian (Reset / Aktif Baru)
                if ($periode = \App\Models\Periode::where('status', 'aktif')->first()) {
                    $notifList[] = [
                        'id' => 'periode_' . $periode->id,
                        'type' => 'success',
                        'icon' => 'bi-arrow-clockwise text-primary',
                        'title' => "Periode Baru: {$periode->nama_periode}",
                        'desc' => "Periode evaluasi {$periode->tahun_ajaran} (Semester {$periode->semester}) telah aktif/direset. Berikan evaluasi objektif!",
                        'time' => 'Periode Aktif',
                        'url' => route('siswa.guru.index'),
                        'action_label' => 'Mulai Menilai &rarr;',
                    ];
                }

                // 5. Balasan Pesan dari Admin (Fitur Chat Kontak)
                $identifier = $user->nis;
                $repliedChats = $identifier ? \App\Models\Kontak::where('identifier', $identifier)
                    ->whereNotNull('balasan')
                    ->latest('updated_at')
                    ->take(2)
                    ->get() : collect();
                foreach ($repliedChats as $c) {
                    $notifList[] = [
                        'id' => 'kontak_' . $c->id,
                        'type' => 'info',
                        'icon' => 'bi-headset text-primary',
                        'title' => 'Balasan dari Administrator',
                        'desc' => \Illuminate\Support\Str::limit($c->balasan, 65),
                        'time' => $c->updated_at->diffForHumans(),
                        'url' => route('siswa.pengaturan') . '#tabChat',
                        'action_label' => 'Buka Chat &rarr;',
                    ];
                }
            } elseif ($role === 'guru') {
                $guruModel = $user->guru ?? \App\Models\Guru::where('nip', $user->nis)->first();

                // 1. Status Periode Penilaian (Reset / Aktif Baru)
                if ($periode = \App\Models\Periode::where('status', 'aktif')->first()) {
                    $notifList[] = [
                        'id' => 'periode_' . $periode->id,
                        'type' => 'success',
                        'icon' => 'bi-arrow-clockwise text-primary',
                        'title' => "Periode Penilaian: {$periode->nama_periode}",
                        'desc' => "Periode {$periode->tahun_ajaran} Semester {$periode->semester} berjalan aktif. Seluruh statistik & leaderboard telah disinkronkan ke periode ini.",
                        'time' => 'Periode Aktif',
                        'url' => route('guru.dashboard'),
                        'action_label' => 'Dashboard &rarr;',
                    ];
                }

                // 2. Notifikasi Ulasan Dibalas Kembali oleh Siswa
                if ($guruModel) {
                    $siswaReplies = \App\Models\PenilaianBalasan::where('role', 'siswa')
                        ->whereHas('penilaian', function($q) use ($guruModel) {
                            $q->where('guru_id', $guruModel->id);
                        })
                        ->with('penilaian')
                        ->latest()
                        ->take(3)
                        ->get();
                    foreach ($siswaReplies as $sr) {
                        $notifList[] = [
                            'id' => 'siswa_reply_' . $sr->id,
                            'type' => 'primary',
                            'icon' => 'bi-chat-left-text text-primary',
                            'title' => 'Siswa Membalas Tanggapan Anda',
                            'desc' => 'Tanggapan siswa: "' . \Illuminate\Support\Str::limit($sr->pesan, 65) . '"',
                            'time' => $sr->created_at->diffForHumans(),
                            'url' => route('guru.ulasan') . '#ulasan-' . $sr->penilaian_id,
                            'action_label' => 'Buka Diskusi &rarr;',
                        ];
                    }

                    // 3. Ulasan Baru dari Siswa
                    $recentReviews = \App\Models\Penilaian::where('guru_id', $guruModel->id)
                        ->where(function($q) {
                            $q->where('is_censored', false)->orWhereNull('is_censored');
                        })
                        ->latest()
                        ->take(3)
                        ->get();
                    foreach ($recentReviews as $rev) {
                        $score = round(($rev->rata_rata_evaluasi / 5) * 100);
                        $notifList[] = [
                            'id' => 'guru_rev_' . $rev->id,
                            'type' => 'info',
                            'icon' => 'bi-star text-warning',
                            'title' => "Penilaian Siswa Baru ({$score}%)",
                            'desc' => $rev->kritik ?: ($rev->saran ?: 'Siswa memberikan penilaian performa pengajaran.'),
                            'time' => $rev->created_at->diffForHumans(),
                            'url' => route('guru.ulasan') . '#ulasan-' . $rev->id,
                            'action_label' => 'Tanggapi Ulasan &rarr;',
                        ];
                    }
                }

                // 4. Balasan Pesan dari Admin (Fitur Chat Kontak)
                $identifier = $guruModel?->nip ?? $user->nis;
                $repliedChats = $identifier ? \App\Models\Kontak::where('identifier', $identifier)
                    ->whereNotNull('balasan')
                    ->latest('updated_at')
                    ->take(2)
                    ->get() : collect();
                foreach ($repliedChats as $c) {
                    $notifList[] = [
                        'id' => 'guru_kontak_' . $c->id,
                        'type' => 'info',
                        'icon' => 'bi-headset text-primary',
                        'title' => 'Balasan dari Administrator',
                        'desc' => \Illuminate\Support\Str::limit($c->balasan, 65),
                        'time' => $c->updated_at->diffForHumans(),
                        'url' => route('guru.pengaturan') . '#tabChat',
                        'action_label' => 'Buka Chat &rarr;',
                    ];
                }
            }

            $userNotifsStaticCache[$user->id] = $notifList;
        }

        $notifs = collect($notifList);
        $totalCount = $notifs->count();
    }
@endphp

@php
    $btnSizeClass = $btnClass ?? 'gk-topbar-btn';
    $containerId = 'userNotifContainer_' . ($prefix ?? 'default') . '_' . (auth()->id() ?? 'guest');
@endphp

<style>
.gk-topbar-btn {
    width: 40px !important;
    height: 40px !important;
    min-width: 40px !important;
    min-height: 40px !important;
    max-width: 40px !important;
    max-height: 40px !important;
    aspect-ratio: 1 / 1 !important;
    border-radius: 50% !important;
    padding: 0 !important;
    margin: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    line-height: 1 !important;
    box-sizing: border-box !important;
    text-decoration: none !important;
}
.gk-topbar-btn-sm {
    width: 34px !important;
    height: 34px !important;
    min-width: 34px !important;
    min-height: 34px !important;
    max-width: 34px !important;
    max-height: 34px !important;
    aspect-ratio: 1 / 1 !important;
    border-radius: 50% !important;
    padding: 0 !important;
    margin: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    flex-shrink: 0 !important;
    line-height: 1 !important;
    box-sizing: border-box !important;
    text-decoration: none !important;
}
.gk-topbar-btn i { font-size: 1.15rem !important; line-height: 1 !important; }
.gk-topbar-btn-sm i { font-size: 0.95rem !important; line-height: 1 !important; }
.user-notif-item.has-link:hover {
    background-color: rgba(0, 51, 102, 0.05) !important;
}
.user-notif-item.has-link:hover .notif-item-title {
    color: var(--primary, #003366) !important;
}
.user-notif-item.has-link:hover .notif-action-text {
    text-decoration: underline !important;
}
</style>

<div class="dropdown" id="{{ $containerId }}">
    <button class="btn btn-light position-relative border shadow-sm {{ $btnSizeClass }}" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" title="Pusat Notifikasi">
        {{-- Ikon Lonceng: Kuning Pekat jika ada notif, Kuning Pudar jika 0 / sudah dicek --}}
        <i class="bi bi-bell-fill gk-bell-icon {{ $totalCount > 0 ? 'has-unread' : 'no-unread' }} user-bell-icon"></i>
        
        {{-- Badge Notifikasi: Biru (Bukan Merah) --}}
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill gk-notif-badge user-notif-badge" style="font-size: 0.65rem; {{ $totalCount > 0 ? '' : 'display: none !important;' }}">
            <span class="user-notif-count">{{ $totalCount }}</span>
            <span class="visually-hidden">notifikasi</span>
        </span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-0 mt-2" style="border-radius: 14px; width: 340px; max-width: 90vw; overflow: hidden; z-index: 1060;">
        <li class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between">
            <span class="fw-bold small text-dark d-flex align-items-center gap-1.5">
                <i class="bi bi-bell-fill" style="color: #f59e0b;"></i> Notifikasi Akun
            </span>
            <div class="d-flex align-items-center gap-1">
                <span class="badge gk-notif-badge rounded-pill user-header-badge" style="font-size: 0.7rem; {{ $totalCount > 0 ? '' : 'display: none !important;' }}">
                    <span class="user-header-count">{{ $totalCount }}</span> Baru
                </span>
                <button type="button" class="btn btn-link p-0 text-muted small text-decoration-none ms-1 btn-clear-all-notifs" title="Tandai semua telah dibaca" style="font-size: 0.68rem;">
                    Tandai Dibaca
                </button>
            </div>
        </li>

        <div class="user-notif-list" style="max-height: 320px; overflow-y: auto;">
            @forelse($notifs as $item)
                @php
                    $hasUrl = !empty($item['url']);
                    $tag = $hasUrl ? 'a' : 'div';
                @endphp
                <{{ $tag }} 
                   @if($hasUrl) href="{{ $item['url'] }}" @endif
                   data-notif-id="{{ $item['id'] }}" 
                   class="dropdown-item p-3 border-bottom text-wrap d-flex align-items-start gap-2.5 user-notif-item {{ $hasUrl ? 'has-link' : 'no-link' }}" 
                   style="white-space: normal; transition: all 0.15s ease; {{ $hasUrl ? 'cursor: pointer;' : 'cursor: default;' }}">
                    <div class="rounded-circle p-2 d-flex align-items-center justify-content-center bg-light border flex-shrink-0" style="width: 36px; height: 36px;">
                        <i class="bi {{ $item['icon'] }} fs-6"></i>
                    </div>
                    <div class="flex-grow-1" style="min-width: 0;">
                        <div class="d-flex align-items-center justify-content-between mb-0.5">
                            <strong class="text-dark small d-block text-truncate notif-item-title" style="font-size: 0.82rem;">{{ $item['title'] }}</strong>
                            <span class="badge bg-primary rounded-circle p-1 notif-unread-dot" style="display: none; width: 7px; height: 7px;"></span>
                        </div>
                        <p class="text-muted mb-1.5 small" style="font-size: 0.76rem; line-height: 1.35;">{{ $item['desc'] }}</p>
                        <div class="d-flex align-items-center justify-content-between pt-0.5">
                            <small class="text-secondary font-mono d-block" style="font-size: 0.68rem;">
                                <i class="bi bi-clock me-1"></i>{{ $item['time'] }}
                            </small>
                            @if($hasUrl)
                                <span class="text-primary small fw-semibold d-inline-flex align-items-center notif-action-text" style="font-size: 0.72rem;">
                                    {{ $item['action_label'] ?? 'Buka &rarr;' }}
                                </span>
                            @endif
                        </div>
                    </div>
                </{{ $tag }}>
            @empty
                <div class="text-center py-4 px-3 text-muted">
                    <i class="bi bi-bell-slash fs-2 opacity-50 d-block mb-1"></i>
                    <small class="d-block fw-semibold">Belum Ada Notifikasi Baru</small>
                    <span style="font-size: 0.72rem;">Semua aktivitas dan informasi akun Anda terkini.</span>
                </div>
            @endforelse
        </div>

        @if($role === 'siswa')
            <li class="p-2 text-center bg-light border-top">
                <a href="{{ route('siswa.riwayat') }}" class="small text-primary text-decoration-none fw-semibold" style="font-size: 0.75rem;">
                    Buka Riwayat Penilaian &rarr;
                </a>
            </li>
        @elseif($role === 'guru')
            <li class="p-2 text-center bg-light border-top">
                <a href="{{ route('guru.ulasan') }}" class="small text-primary text-decoration-none fw-semibold" style="font-size: 0.75rem;">
                    Lihat Semua Ulasan &rarr;
                </a>
            </li>
        @endif
    </ul>
</div>

<script>
(function() {
    const userId = "{{ auth()->id() ?? 'guest' }}";
    const storageKey = 'gk_read_notifs_' + userId;

    function getReadNotifs() {
        try {
            return JSON.parse(localStorage.getItem(storageKey) || '[]');
        } catch(e) {
            return [];
        }
    }

    function saveReadNotifs(list) {
        try {
            localStorage.setItem(storageKey, JSON.stringify(list));
        } catch(e) {}
    }

    function syncNotifUI() {
        const readList = getReadNotifs();
        const containers = document.querySelectorAll('[id^="userNotifContainer_"]');
        if (!containers.length) return;

        let unreadCount = 0;

        containers.forEach(container => {
            const items = container.querySelectorAll('.user-notif-item');
            unreadCount = 0;
            items.forEach(el => {
                const id = el.getAttribute('data-notif-id');
                const dot = el.querySelector('.notif-unread-dot');
                if (id && readList.includes(id)) {
                    el.style.opacity = '0.65';
                    el.classList.add('bg-light-subtle');
                    if (dot) dot.style.display = 'none';
                } else {
                    unreadCount++;
                    el.style.opacity = '1';
                    el.classList.remove('bg-light-subtle');
                    if (dot) dot.style.display = 'inline-block';
                }
            });
        });

        // Update semua icon lonceng user
        const bells = document.querySelectorAll('.user-bell-icon');
        const badges = document.querySelectorAll('.user-notif-badge');
        const counts = document.querySelectorAll('.user-notif-count');
        const headerCounts = document.querySelectorAll('.user-header-count');
        const headerBadges = document.querySelectorAll('.user-header-badge');

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

    // Event click per notif item
    document.addEventListener('click', function(e) {
        const item = e.target.closest('.user-notif-item');
        if (item) {
            const id = item.getAttribute('data-notif-id');
            if (id) {
                const readList = getReadNotifs();
                if (!readList.includes(id)) {
                    readList.push(id);
                    saveReadNotifs(readList);
                }
                syncNotifUI();
            }
        }

        const clearBtn = e.target.closest('.btn-clear-all-notifs');
        if (clearBtn) {
            e.preventDefault();
            e.stopPropagation();
            const items = document.querySelectorAll('.user-notif-item');
            const readList = getReadNotifs();
            items.forEach(el => {
                const id = el.getAttribute('data-notif-id');
                if (id && !readList.includes(id)) {
                    readList.push(id);
                }
            });
            saveReadNotifs(readList);
            syncNotifUI();
        }
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', syncNotifUI);
    } else {
        syncNotifUI();
    }
})();
</script>

