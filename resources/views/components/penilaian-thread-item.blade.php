<div class="p-2.5 rounded-3 border {{ $b->role === 'guru' ? 'bg-primary-subtle bg-opacity-25 border-primary-subtle' : 'bg-light border-light-subtle' }}">
    <div class="d-flex align-items-center justify-content-between mb-1">
        <div class="d-flex align-items-center gap-2">
            @if($b->role === 'guru')
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 24px; height: 24px; font-size: 0.65rem;">
                    G
                </div>
                <strong class="text-primary small" style="font-size: 0.82rem;">
                    {{ $b->author_name }}
                </strong>
                <span class="badge bg-primary text-white font-mono px-1.5 py-0.5" style="font-size: 0.62rem;">GURU</span>
            @elseif($b->role === 'siswa')
                <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 24px; height: 24px; font-size: 0.7rem;">
                    <i class="bi bi-incognito"></i>
                </div>
                <strong class="text-dark small" style="font-size: 0.82rem;">
                    {{ $b->author_name }}
                </strong>
                <span class="badge bg-secondary text-white font-mono px-1.5 py-0.5" style="font-size: 0.62rem;">PENULIS ULASAN</span>
            @else
                <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 24px; height: 24px; font-size: 0.65rem;">
                    A
                </div>
                <strong class="text-dark small" style="font-size: 0.82rem;">
                    Administrator
                </strong>
                <span class="badge bg-dark text-white font-mono px-1.5 py-0.5" style="font-size: 0.62rem;">ADMIN</span>
            @endif
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="text-secondary font-mono" style="font-size: 0.68rem;">
                <i class="bi bi-clock me-0.5"></i>{{ $b->created_at->diffForHumans() }}
            </span>

            @if(isset($currentUser) && $currentUser && ($currentUser->id === $b->user_id || $currentUser->role === 'admin'))
                <form action="{{ route('penilaian.balasan.destroy', $b) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus balasan ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-link text-danger p-0 border-0" title="Hapus Balasan" style="font-size: 0.72rem;">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="text-dark small ps-4" style="line-height: 1.5; font-size: 0.82rem; white-space: pre-line;">
        {{ $b->pesan }}
    </div>
</div>
