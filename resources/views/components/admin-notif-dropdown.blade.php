@php
    $prefix = $prefix ?? 'main';
    $totalNotif = ($unreadPelanggaranCount ?? 0) + ($unreadChatCount ?? 0);
    $defaultTab = (($unreadPelanggaranCount ?? 0) == 0 && ($unreadChatCount ?? 0) > 0) ? 'chat' : 'pelanggaran';
@endphp

<li class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
        <i class="bi bi-bell-fill text-danger fs-6"></i>
        <span class="fw-bold text-dark small">Pusat Notifikasi</span>
    </div>
    @if($totalNotif > 0)
        <span class="badge bg-danger rounded-pill">{{ $totalNotif }} Baru</span>
    @else
        <span class="badge bg-light text-muted border">Semua Bersih</span>
    @endif
</li>

<li class="p-0 border-bottom bg-light">
    <ul class="nav nav-tabs nav-fill border-0 px-2 pt-2" id="notifTabs_{{ $prefix }}" role="tablist" style="font-size: 0.8rem;">
        <li class="nav-item" role="presentation">
            <button class="nav-link py-2 fw-semibold d-flex align-items-center justify-content-center gap-1.5 {{ $defaultTab === 'pelanggaran' ? 'active' : '' }}" 
                    id="tab-pelanggaran-{{ $prefix }}" 
                    data-bs-toggle="tab" 
                    data-bs-target="#pane-pelanggaran-{{ $prefix }}" 
                    type="button" 
                    role="tab" 
                    aria-controls="pane-pelanggaran-{{ $prefix }}" 
                    aria-selected="{{ $defaultTab === 'pelanggaran' ? 'true' : 'false' }}">
                <i class="bi bi-shield-exclamation text-danger"></i>
                <span>Pelanggaran</span>
                @if(($unreadPelanggaranCount ?? 0) > 0)
                    <span class="badge bg-danger rounded-pill px-1.5 py-0.5" style="font-size: 0.65rem;">{{ $unreadPelanggaranCount }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link py-2 fw-semibold d-flex align-items-center justify-content-center gap-1.5 {{ $defaultTab === 'chat' ? 'active' : '' }}" 
                    id="tab-chat-{{ $prefix }}" 
                    data-bs-toggle="tab" 
                    data-bs-target="#pane-chat-{{ $prefix }}" 
                    type="button" 
                    role="tab" 
                    aria-controls="pane-chat-{{ $prefix }}" 
                    aria-selected="{{ $defaultTab === 'chat' ? 'true' : 'false' }}">
                <i class="bi bi-chat-dots-fill text-primary"></i>
                <span>Pesan Chat</span>
                @if(($unreadChatCount ?? 0) > 0)
                    <span class="badge bg-primary rounded-pill px-1.5 py-0.5" style="font-size: 0.65rem;">{{ $unreadChatCount }}</span>
                @endif
            </button>
        </li>
    </ul>
</li>

<div class="tab-content" id="notifTabContent_{{ $prefix }}">
    {{-- TAB PANE: LOG PELANGGARAN --}}
    <div class="tab-pane fade {{ $defaultTab === 'pelanggaran' ? 'show active' : '' }}" 
         id="pane-pelanggaran-{{ $prefix }}" 
         role="tabpanel" 
         aria-labelledby="tab-pelanggaran-{{ $prefix }}">
        <div style="max-height: 320px; overflow-y: auto;">
            @forelse(($recentPelanggarans ?? collect()) as $notif)
                <div>
                    <a class="dropdown-item p-3 border-bottom text-wrap" href="{{ route('admin.pelanggaran.index') }}">
                        <div class="d-flex align-items-start gap-2.5">
                            <span class="badge bg-danger text-white rounded-circle p-1.5 mt-0.5 flex-shrink-0">
                                <i class="bi bi-exclamation-octagon-fill"></i>
                            </span>
                            <div class="flex-grow-1 min-w-0" style="min-width: 0;">
                                @if($notif->user)
                                    <div class="d-flex justify-content-between align-items-center mb-0.5">
                                        <strong class="text-danger small text-truncate" style="font-size: 0.85rem;">{{ $notif->user->name }}</strong>
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
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-1.5 py-0.5" style="font-size: 0.68rem;">
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
            <a href="{{ route('admin.pelanggaran.index') }}" class="btn btn-primary-custom btn-sm w-100 py-1.5 rounded-pill fw-semibold" style="font-size: 0.8rem;">
                Kelola & Tinjau Semua Pelanggaran
            </a>
        </div>
    </div>

    {{-- TAB PANE: PESAN CHAT ADMIN --}}
    <div class="tab-pane fade {{ $defaultTab === 'chat' ? 'show active' : '' }}" 
         id="pane-chat-{{ $prefix }}" 
         role="tabpanel" 
         aria-labelledby="tab-chat-{{ $prefix }}">
        <div style="max-height: 320px; overflow-y: auto;">
            @forelse(($recentChats ?? collect()) as $c)
                <div>
                    <a class="dropdown-item p-3 border-bottom text-wrap" href="{{ route('admin.kontak.chat', $c->identifier) }}">
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
