@php
    $canManage = isset($currentUser) && $currentUser && ((int)$currentUser->id === (int)$b->user_id || $currentUser->role === 'admin');
    $canEdit   = isset($currentUser) && $currentUser && ((int)$currentUser->id === (int)$b->user_id);
@endphp

<div class="p-2.5 rounded-3 border thread-item-card {{ $b->role === 'guru' ? 'bg-primary-subtle bg-opacity-25 border-primary-subtle' : 'bg-light border-light-subtle' }}" id="thread-item-{{ $b->id }}" data-id="{{ $b->id }}">
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

            @if($canManage)
                <div class="d-flex align-items-center gap-2 ms-1">
                    @if($canEdit)
                        <button type="button" 
                                class="btn btn-link text-primary p-0 border-0 btn-trigger-edit-thread" 
                                data-target="#thread-edit-box-{{ $b->id }}"
                                data-text-target="#thread-text-{{ $b->id }}"
                                title="Edit Pesan" 
                                style="font-size: 0.75rem; text-decoration: none;">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                    @endif

                    <button type="button" 
                            class="btn btn-link text-danger p-0 border-0 btn-trigger-delete-thread" 
                            data-delete-url="{{ route('penilaian.balasan.destroy', $b) }}"
                            data-target="#thread-item-{{ $b->id }}"
                            title="Hapus Pesan" 
                            style="font-size: 0.75rem; text-decoration: none;">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            @endif
        </div>
    </div>

    {{-- Tampilan Pesan --}}
    <div class="text-dark small ps-4 thread-text-display" id="thread-text-{{ $b->id }}" style="line-height: 1.5; font-size: 0.82rem; white-space: pre-line;">{{ $b->pesan }}</div>

    {{-- Form Edit Pesan (Tersembunyi Awalnya) --}}
    @if($canEdit)
        <div class="thread-edit-box ps-4 mt-2" id="thread-edit-box-{{ $b->id }}" style="display: none;">
            <form action="{{ route('penilaian.balasan.update', $b) }}" method="POST" class="form-edit-thread" data-target="#thread-text-{{ $b->id }}" data-box="#thread-edit-box-{{ $b->id }}">
                @csrf
                @method('PUT')
                <textarea name="pesan" rows="2" class="form-control form-control-sm border bg-white rounded-3 px-2 py-1.5" style="resize: none; font-size: 0.82rem;" maxlength="255" required>{{ $b->pesan }}</textarea>
                <div class="d-flex align-items-center justify-content-end gap-1.5 mt-1.5">
                    <button type="button" class="btn btn-sm btn-light border py-0.5 px-2 rounded-pill btn-cancel-edit-thread" data-target="#thread-edit-box-{{ $b->id }}" data-text-target="#thread-text-{{ $b->id }}" style="font-size: 0.72rem;">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary-custom py-0.5 px-2.5 rounded-pill btn-save-edit-thread" style="font-size: 0.72rem;">
                        <span>Simpan</span>
                        <i class="bi bi-check-lg ms-0.5"></i>
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
