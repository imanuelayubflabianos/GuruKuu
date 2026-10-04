@props(['penilaian', 'currentGuruId' => null])

@php
    $user = Auth::user();
    $balasans = $penilaian->balasans ?? collect();
    $totalBalasans = $balasans->count();

    // Tentukan apakah user saat ini berhak mengakses & membalas diskusi
    $isAdmin = $user && $user->role === 'admin';
    $isAuthorStudent = $user && $user->role === 'siswa' && $penilaian->siswa_id == $user->id;

    $isTargetGuru = false;
    if ($user && $user->role === 'guru') {
        $guruId = $currentGuruId;
        if (!$guruId) {
            $guruLogged = \App\Models\Guru::where('nip', $user->nis)->orWhere('email', $user->email)->first();
            $guruId = $guruLogged ? $guruLogged->id : null;
        }
        if ($guruId && $penilaian->guru_id == $guruId) {
            $isTargetGuru = true;
        }
    }

    // Diskusi bersifat privat: hanya admin, siswa pembuat ulasan, dan guru yang bersangkutan
    $canAccessDiscussion = $isAdmin || $isAuthorStudent || $isTargetGuru;
    $canReply = $canAccessDiscussion;

    $replyPlaceholder = 'Tulis balasan...';
    $senderBadge = '';
    if ($isAdmin) {
        $replyPlaceholder = 'Tulis tanggapan sebagai Administrator...';
        $senderBadge = 'Admin';
    } elseif ($isTargetGuru) {
        $replyPlaceholder = 'Tulis tanggapan profesional kepada siswa...';
        $senderBadge = 'Guru';
    } elseif ($isAuthorStudent) {
        $replyPlaceholder = 'Balas tanggapan guru (Identitas Anda tetap 100% Anonim)...';
        $senderBadge = 'Penulis Ulasan (Anonim)';
    }

    // Hitung sisa cooldown slowmode (25 detik ala Discord)
    $cooldownKey = $user ? "slowmode_reply_{$user->id}" : null;
    $remainingCooldown = ($user && !$isAdmin && \Illuminate\Support\Facades\Cache::has($cooldownKey)) 
        ? max(0, \Illuminate\Support\Facades\Cache::get($cooldownKey) - now()->timestamp) 
        : 0;

    // Pisahkan: balasan pertama tampil di awal, sisa diskusi + form balasan diciutkan di "Lihat Detail Diskusi"
    $firstReply = $balasans->first();
    $moreReplies = $totalBalasans > 1 ? $balasans->slice(1) : collect();
@endphp

@if(!$canAccessDiscussion)
    {{-- TAMPILAN UNTUK PENGGUNA LAIN / SISWA LAIN (HANYA MELIHAT 1 TANGGAPAN AWAL GURU, DISKUSI LANJUTAN PRIVAT) --}}
    @if($penilaian->balasan_guru || ($firstReply && $firstReply->role === 'guru'))
        <div class="mt-2.5 p-2.5 rounded-3 bg-primary-subtle bg-opacity-25 border border-primary-subtle shadow-xs">
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 24px; height: 24px; font-size: 0.65rem;">
                    G
                </div>
                <strong class="text-primary small" style="font-size: 0.82rem;">
                    {{ $penilaian->guru->nama ?? 'Guru Pengampu' }}
                </strong>
                <span class="badge bg-primary text-white font-mono px-1.5 py-0.5" style="font-size: 0.62rem;">TANGGAPAN GURU</span>
            </div>
            <div class="text-dark small ps-4" style="line-height: 1.5; font-size: 0.82rem;">
                {{ $firstReply ? $firstReply->pesan : $penilaian->balasan_guru }}
            </div>
        </div>
    @endif
@else
    {{-- TAMPILAN PRIVAT UNTUK SISWA PEMBUAT ULASAN, GURU TERKAIT, & ADMIN --}}
    <div class="mt-3 pt-3 border-top border-light-subtle thread-container" id="thread-container-{{ $penilaian->id }}">
        {{-- HEADER STATUS PRIVASI & THREAD --}}
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="badge bg-secondary-subtle text-secondary border rounded-pill d-inline-flex align-items-center gap-1.5 py-1 px-2.5" style="font-size: 0.72rem;">
                <i class="bi bi-shield-lock-fill text-primary"></i>
                <span>Diskusi Privat (Hanya Anda & {{ $user->role === 'siswa' ? 'Guru' : 'Siswa Penilai' }})</span>
            </span>

            @if($totalBalasans > 0)
                <span class="badge bg-primary-subtle text-primary rounded-pill font-mono px-2 py-0.5" style="font-size: 0.7rem;">
                    {{ $totalBalasans }} Pesan Diskusi
                </span>
            @endif
        </div>

        {{-- 1. BALASAN AWAL (HANYA INI YANG MUNCUL PERTAMA KALI) --}}
        @if($firstReply)
            <div class="mb-2">
                @include('components.penilaian-thread-item', ['b' => $firstReply, 'currentUser' => $user])
            </div>
        @elseif($penilaian->balasan_guru)
            <div class="mb-2 p-2.5 rounded-3 bg-primary-subtle bg-opacity-25 border border-primary-subtle shadow-xs">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 24px; height: 24px; font-size: 0.65rem;">
                        G
                    </div>
                    <strong class="text-primary small" style="font-size: 0.82rem;">
                        {{ $penilaian->guru->nama ?? 'Guru' }}
                    </strong>
                    <span class="badge bg-primary text-white font-mono px-1.5 py-0.5" style="font-size: 0.62rem;">TANGGAPAN GURU</span>
                </div>
                <div class="text-dark small ps-4" style="line-height: 1.5; font-size: 0.82rem;">
                    {{ $penilaian->balasan_guru }}
                </div>
            </div>
        @endif

        {{-- 2. TOMBOL BUKA DETAIL DISKUSI --}}
        @php
            $btnLabel = $moreReplies->count() > 0 
                ? 'Lihat Detail Diskusi (' . $moreReplies->count() . ' balasan)' 
                : ($totalBalasans > 0 ? 'Balas Diskusi' : 'Buka Diskusi / Beri Tanggapan');
        @endphp
        <div class="my-1.5">
            <button class="btn btn-sm btn-light border border-light-subtle text-primary fw-semibold px-3 py-1 rounded-pill d-inline-flex align-items-center gap-1.5 shadow-xs btn-toggle-more-replies"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#discussionDetail-{{ $penilaian->id }}"
                    data-original-label="{{ $btnLabel }}"
                    aria-expanded="false"
                    style="font-size: 0.75rem;">
                <i class="bi bi-chat-dots-fill"></i>
                <span class="toggle-text">{{ $btnLabel }}</span>
                <i class="bi bi-chevron-down toggle-arrow ms-1" style="font-size: 0.7rem; transition: transform 0.2s ease;"></i>
            </button>
        </div>

        {{-- 3. AREA DETAIL DISKUSI (TERSEMBUNYI SECARA DEFAULT, BARU NAMPIL KETIKA TOMBOL DIPENCET) --}}
        <div class="collapse" id="discussionDetail-{{ $penilaian->id }}">
            {{-- Balasan-balasan lanjutan (jika ada) --}}
            <div class="d-flex flex-column gap-2 pt-1 mb-2" id="moreReplies-list-{{ $penilaian->id }}">
                @foreach($moreReplies as $b)
                    @include('components.penilaian-thread-item', ['b' => $b, 'currentUser' => $user])
                @endforeach
            </div>

            {{-- FORM BALAS CEPAT DENGAN DISCORD-STYLE SLOWMODE --}}
            @if($canReply)
                <div class="p-2.5 rounded-3 border bg-white shadow-xs mt-2 reply-box-container">
                    <form action="{{ route('penilaian.balasan.store', $penilaian) }}" 
                          method="POST" 
                          class="m-0 thread-reply-form"
                          id="thread-form-{{ $penilaian->id }}"
                          data-penilaian-id="{{ $penilaian->id }}">
                        @csrf
                        <div class="d-flex align-items-start gap-2">
                            <div class="flex-shrink-0 mt-1">
                                @if($user->role === 'guru')
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.72rem;">
                                        G
                                    </div>
                                @elseif($user->role === 'siswa')
                                    <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.78rem;" title="Anonim">
                                        <i class="bi bi-incognito"></i>
                                    </div>
                                @else
                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.72rem;">
                                        A
                                    </div>
                                @endif
                            </div>

                            <div class="flex-grow-1">
                                <textarea name="pesan"
                                          rows="2"
                                          class="form-control form-control-sm border-0 bg-light rounded-3 px-3 py-2 thread-reply-input"
                                          style="resize: none; font-size: 0.82rem;"
                                          placeholder="{{ $replyPlaceholder }} (maks. 255 karakter)"
                                          maxlength="255"
                                          oninput="updateThreadCounter(this)"
                                          required></textarea>

                                <div class="thread-limit-warn text-danger small fw-bold mt-1" style="display: none; font-size: 0.72rem;">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i> Anda telah mencapai batas maksimal 255 karakter!
                                </div>

                                {{-- Alert Slowmode / Info Bar --}}
                                <div class="slowmode-banner small mt-1.5 p-1.5 rounded-2 bg-light border text-muted d-flex align-items-center justify-content-between" style="font-size: 0.72rem; {{ $remainingCooldown > 0 ? '' : 'display: none !important;' }}">
                                    <span class="d-flex align-items-center gap-1 text-warning-emphasis fw-semibold">
                                        <i class="bi bi-stopwatch text-warning"></i>
                                        <span>Slowmode aktif (Mirip Discord). Tunggu jeda waktu sebelum membalas lagi.</span>
                                    </span>
                                </div>

                                <div class="d-flex align-items-center justify-content-between mt-2 pt-1 border-top border-light-subtle">
                                    <span class="text-muted font-mono d-flex align-items-center gap-2" style="font-size: 0.7rem;">
                                        <span>
                                            @if($user->role === 'siswa')
                                                <i class="bi bi-shield-check text-success me-0.5"></i> Identitas Anda tetap anonim
                                            @else
                                                <i class="bi bi-pen me-0.5"></i> Balas sebagai {{ $senderBadge }}
                                            @endif
                                        </span>
                                        <span class="badge bg-light text-muted border thread-counter py-0.5 px-1.5" style="font-size: 0.68rem;">0 / 255</span>
                                    </span>

                                    <button type="submit" 
                                            class="btn btn-primary-custom btn-sm px-3 py-1 rounded-pill fw-semibold shadow-xs d-inline-flex align-items-center gap-1.5 btn-submit-thread"
                                            id="btn-submit-thread-{{ $penilaian->id }}"
                                            data-cooldown="{{ $remainingCooldown }}"
                                            {{ $remainingCooldown > 0 ? 'disabled' : '' }}
                                            style="font-size: 0.78rem;">
                                        <span class="btn-text">
                                            @if($remainingCooldown > 0)
                                                Slowmode ({{ $remainingCooldown }}d)
                                            @else
                                                Kirim Balasan
                                            @endif
                                        </span>
                                        <i class="bi {{ $remainingCooldown > 0 ? 'bi-hourglass-split' : 'bi-send-fill' }} btn-icon" style="font-size: 0.7rem;"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            @endif

            {{-- TOMBOL TUTUP / SEMBUNYIKAN DISKUSI DI BAGIAN PALING BAWAH --}}
            <div class="text-center pt-2 pb-0.5">
                <button class="btn btn-sm btn-light border border-light-subtle text-muted fw-semibold px-3 py-1 rounded-pill d-inline-flex align-items-center gap-1.5 shadow-xs"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#discussionDetail-{{ $penilaian->id }}"
                        aria-expanded="true"
                        title="Tutup riwayat diskusi ini"
                        style="font-size: 0.74rem;">
                    <i class="bi bi-chevron-up text-primary" style="font-size: 0.7rem;"></i>
                    <span class="text-secondary">Sembunyikan Diskusi</span>
                </button>
            </div>
        </div>
    </div>
@endif

@once
@push('scripts')
<script>
function updateThreadCounter(el) {
    const parent = el.closest('.flex-grow-1');
    if (!parent) return;
    const counter = parent.querySelector('.thread-counter');
    const warn = parent.querySelector('.thread-limit-warn');
    const len = el.value.length;
    if (counter) counter.textContent = `${len} / 255`;
    if (warn) warn.style.display = len >= 255 ? 'block' : 'none';
}

// Inisialisasi Discord-Style Slowmode Timers & Collapse Handler untuk semua thread
document.addEventListener('DOMContentLoaded', function () {
    // 1. Setup Slowmode Timers yang sedang aktif di backend
    const submitBtns = document.querySelectorAll('.btn-submit-thread');
    submitBtns.forEach(btn => {
        const cooldown = parseInt(btn.getAttribute('data-cooldown') || '0', 10);
        if (cooldown > 0) {
            startSlowmodeCountdown(btn, cooldown);
        }
    });

    // 2. Setup Toggle Arrow icon untuk tombol "Lihat Detail Diskusi"
    document.querySelectorAll('.btn-toggle-more-replies').forEach(btn => {
        const targetId = btn.getAttribute('data-bs-target');
        const collapseEl = document.querySelector(targetId);
        if (collapseEl) {
            collapseEl.addEventListener('show.bs.collapse', function () {
                const arrow = btn.querySelector('.toggle-arrow');
                const text = btn.querySelector('.toggle-text');
                if (arrow) arrow.classList.replace('bi-chevron-down', 'bi-chevron-up');
                if (text) text.textContent = 'Tutup Detail Diskusi';
            });
            collapseEl.addEventListener('hide.bs.collapse', function () {
                const arrow = btn.querySelector('.toggle-arrow');
                const text = btn.querySelector('.toggle-text');
                if (arrow) arrow.classList.replace('bi-chevron-up', 'bi-chevron-down');
                if (text) text.textContent = btn.getAttribute('data-original-label') || 'Lihat Detail Diskusi';
            });
        }
    });

    // 3. Handle Submit Form Thread via AJAX agar chat instan & memicu Slowmode Countdown
    document.querySelectorAll('.thread-reply-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const textarea = form.querySelector('textarea[name="pesan"]');
            const pesan = textarea ? textarea.value.trim() : '';
            if (!pesan) return;

            const btn = form.querySelector('.btn-submit-thread');
            if (btn && btn.disabled) return;

            const originalBtnHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="width: 0.75rem; height: 0.75rem;"></span> <span style="font-size: 0.75rem;">Mengirim...</span>`;

            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || form.querySelector('input[name="_token"]')?.value || ''
                },
                body: formData
            })
            .then(res => res.json().then(data => ({ status: res.status, data })))
            .then(({ status, data }) => {
                if (status === 200 && data.success) {
                    // Kosongkan textarea & reset counter
                    textarea.value = '';
                    updateThreadCounter(textarea);

                    // Tampilkan notifikasi mini jika ada toastify/swal atau alert
                    if (window.toastNotification) {
                        window.toastNotification(data.message || 'Balasan ulasan berhasil dikirimkan.', 'success');
                    }

                    // Tambahkan bubble balasan ke daftar pesan tanpa reload halaman
                    const threadContainer = form.closest('.thread-container');
                    const messagesContainer = threadContainer ? threadContainer.querySelector('[id^="moreReplies-list-"]') : null;
                    if (messagesContainer) {
                        const newBubble = document.createElement('div');
                        newBubble.className = 'p-2.5 rounded-3 border bg-light border-light-subtle shadow-xs';
                        newBubble.innerHTML = `
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 24px; height: 24px; font-size: 0.7rem;">
                                        <i class="bi bi-incognito"></i>
                                    </div>
                                    <strong class="text-dark small" style="font-size: 0.82rem;">Penulis Ulasan (Anonim)</strong>
                                    <span class="badge bg-secondary text-white font-mono px-1.5 py-0.5" style="font-size: 0.62rem;">PENULIS ULASAN</span>
                                </div>
                                <span class="text-secondary font-mono" style="font-size: 0.68rem;">
                                    <i class="bi bi-clock me-0.5"></i>baru saja
                                </span>
                            </div>
                            <div class="text-dark small ps-4" style="line-height: 1.5; font-size: 0.82rem; white-space: pre-line;">
                                ${escapeHtml(data.balasan?.pesan || pesan)}
                            </div>
                        `;
                        messagesContainer.appendChild(newBubble);
                    }

                    // Mulai countdown Slowmode (25 detik seperti Discord)
                    const cooldownTime = data.cooldown || 25;
                    startSlowmodeCountdown(btn, cooldownTime);

                } else if (status === 429) {
                    // Slowmode terpicu dari server
                    const remaining = data.remaining || 25;
                    alert(data.message || `Mode lambat (Slowmode) aktif! Harap tunggu ${remaining} detik.`);
                    startSlowmodeCountdown(btn, remaining);
                } else {
                    alert(data.message || 'Terjadi kesalahan saat mengirim balasan.');
                    btn.disabled = false;
                    btn.innerHTML = originalBtnHtml;
                }
            })
            .catch(err => {
                console.error(err);
                form.submit();
            });
        });
    });
});

function startSlowmodeCountdown(btn, seconds) {
    if (!btn) return;
    let remaining = seconds;
    btn.disabled = true;

    const banner = btn.closest('.flex-grow-1')?.querySelector('.slowmode-banner');
    if (banner) banner.style.removeProperty('display');

    function update() {
        if (remaining <= 0) {
            btn.disabled = false;
            btn.innerHTML = `<span>Kirim Balasan</span><i class="bi bi-send-fill ms-1" style="font-size: 0.7rem;"></i>`;
            if (banner) banner.style.setProperty('display', 'none', 'important');
            clearInterval(timer);
        } else {
            btn.innerHTML = `<span>Slowmode (${remaining}d)</span><i class="bi bi-hourglass-split ms-1" style="font-size: 0.7rem;"></i>`;
            remaining--;
        }
    }

    update();
    const timer = setInterval(update, 1000);
}

function escapeHtml(text) {
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}
</script>
@endpush
@endonce
