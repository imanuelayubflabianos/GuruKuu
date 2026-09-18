@props(['penilaian', 'currentGuruId' => null])

@php
    $user = Auth::user();
    $balasans = $penilaian->balasans ?? collect();
    $totalBalasans = $balasans->count();
    
    // Tentukan apakah user saat ini berhak membalas
    $canReply = false;
    $replyPlaceholder = 'Tulis balasan...';
    $senderBadge = '';

    if ($user) {
        if ($user->role === 'admin') {
            $canReply = true;
            $replyPlaceholder = 'Tulis tanggapan sebagai Administrator...';
            $senderBadge = 'Admin';
        } elseif ($user->role === 'guru') {
            // Cek apakah guru ini adalah target ulasan
            $guruLogged = \App\Models\Guru::where('nip', $user->nis)->orWhere('email', $user->email)->first();
            if ($guruLogged && $penilaian->guru_id == $guruLogged->id) {
                $canReply = true;
                $replyPlaceholder = 'Tulis tanggapan profesional kepada siswa...';
                $senderBadge = 'Guru';
            }
        } elseif ($user->role === 'siswa') {
            // Cek apakah siswa ini adalah pembuat ulasan
            if ($penilaian->siswa_id == $user->id) {
                $canReply = true;
                $replyPlaceholder = 'Balas tanggapan guru (Identitas Anda tetap 100% Anonim)...';
                $senderBadge = 'Penulis Ulasan (Anonim)';
            }
        }
    }

    // Optimasi anti-lag: pisahkan balasan terdahulu vs 2 balasan terbaru
    $hasManyReplies = $totalBalasans > 2;
    $olderReplies = $hasManyReplies ? $balasans->slice(0, $totalBalasans - 2) : collect();
    $recentReplies = $hasManyReplies ? $balasans->slice($totalBalasans - 2) : $balasans;
@endphp

<div class="mt-3 pt-3 border-top border-light-subtle">
    {{-- HEADER STATUS THREAD --}}
    <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="small fw-bold text-dark d-flex align-items-center gap-1.5" style="font-size: 0.82rem;">
            <i class="bi bi-chat-left-text text-primary"></i>
            Diskusi Ulasan
            @if($totalBalasans > 0)
                <span class="badge bg-primary-subtle text-primary rounded-pill font-mono px-2 py-0.5" style="font-size: 0.7rem;">
                    {{ $totalBalasans }} balasan
                </span>
            @endif
        </span>

        @if($totalBalasans === 0 && !$canReply)
            <span class="text-muted small" style="font-size: 0.75rem;">Belum ada balasan</span>
        @endif
    </div>

    {{-- DAFTAR BALASAN (THREAD BUBBLE) --}}
    @if($totalBalasans > 0)
        <div class="d-flex flex-column gap-2 mb-3">
            {{-- Tombol Buka Balasan Lama Jika Lebih Dari 2 (Anti-Lag Optimization) --}}
            @if($hasManyReplies)
                <div>
                    <button class="btn btn-sm btn-link text-primary p-0 text-decoration-none fw-semibold d-inline-flex align-items-center gap-1"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#olderReplies-{{ $penilaian->id }}"
                            aria-expanded="false"
                            style="font-size: 0.78rem;">
                        <i class="bi bi-chat-dots-fill"></i>
                        <span>Lihat {{ $olderReplies->count() }} balasan sebelumnya...</span>
                    </button>
                </div>

                <div class="collapse d-flex flex-column gap-2" id="olderReplies-{{ $penilaian->id }}">
                    @foreach($olderReplies as $b)
                        @include('components.penilaian-thread-item', ['b' => $b, 'currentUser' => $user])
                    @endforeach
                </div>
            @endif

            {{-- 2 Balasan Terbaru yang Selalu Tampil --}}
            @foreach($recentReplies as $b)
                @include('components.penilaian-thread-item', ['b' => $b, 'currentUser' => $user])
            @endforeach
        </div>
    @endif

    {{-- FORM BALAS CEPAT (INLINE REPLY) --}}
    @if($canReply)
        <div class="p-2.5 rounded-3 border bg-white shadow-xs">
            <form action="{{ route('penilaian.balasan.store', $penilaian) }}" method="POST" class="m-0">
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
                                  class="form-control form-control-sm border-0 bg-light rounded-3 px-3 py-2"
                                  style="resize: none; font-size: 0.82rem;"
                                  placeholder="{{ $replyPlaceholder }}"
                                  maxlength="1000"
                                  required></textarea>

                        <div class="d-flex align-items-center justify-content-between mt-1.5 pt-1">
                            <span class="text-muted font-mono" style="font-size: 0.7rem;">
                                @if($user->role === 'siswa')
                                    <i class="bi bi-shield-check text-success me-0.5"></i> Identitas Anda anonim
                                @else
                                    <i class="bi bi-pen me-0.5"></i> Balas sebagai {{ $senderBadge }}
                                @endif
                            </span>

                            <button type="submit" class="btn btn-primary-custom btn-sm px-3 py-1 rounded-pill fw-semibold shadow-xs d-inline-flex align-items-center gap-1" style="font-size: 0.78rem;">
                                <span>Kirim Balasan</span>
                                <i class="bi bi-send-fill" style="font-size: 0.7rem;"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    @endif
</div>
