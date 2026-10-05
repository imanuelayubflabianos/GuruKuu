@php
    $user = auth()->user();
    $userName = $user ? trim($user->name) : 'User';
    $nameParts = array_values(array_filter(explode(' ', $userName)));
    if (count($nameParts) >= 2) {
        $userInitials = strtoupper(mb_substr($nameParts[0], 0, 1) . mb_substr(end($nameParts), 0, 1));
    } elseif (count($nameParts) === 1 && mb_strlen($nameParts[0]) > 0) {
        $userInitials = strtoupper(mb_substr($nameParts[0], 0, 1));
    } else {
        $userInitials = 'U';
    }
@endphp

<div class="dropdown">
    <button class="btn btn-light border rounded-pill shadow-sm d-flex align-items-center gap-1.5 p-1 pe-2" 
            type="button" 
            data-bs-toggle="dropdown" 
            data-bs-auto-close="true"
            aria-expanded="false" 
            title="Menu Akun & Navigasi"
            style="height: 36px; background: #f8fafc; border-color: #e2e8f0 !important; cursor: pointer; transition: all 0.2s ease;">
        <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold shadow-sm flex-shrink-0"
             style="width: 28px; height: 28px; font-size: 0.75rem; background: linear-gradient(135deg, #0d6efd, #003366); letter-spacing: 0.5px;">
            {{ $userInitials }}
        </div>
        <i class="bi bi-chevron-down text-secondary" style="font-size: 0.72rem; margin-left: 2px;"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 gk-mobile-more-dropdown mt-2" 
        style="min-width: 220px; border-radius: 14px; z-index: 1060;">
        {{-- INFO IDENTITAS AKUN --}}
        <li class="px-2.5 py-1.5 mb-1 rounded-3 bg-light">
            <div class="fw-bold text-dark text-truncate small" style="line-height: 1.25;">{{ $userName }}</div>
            <div class="text-muted" style="font-size: 0.68rem;">
                @if($user && $user->role === 'siswa')
                    Siswa {{ $user->nis ? '• NIS ' . $user->nis : '' }}
                @elseif($user && $user->role === 'guru')
                    Guru {{ $user->nip ? '• NIP ' . $user->nip : '' }}
                @elseif($user && $user->role === 'admin')
                    Administrator
                @else
                    {{ ucfirst($user->role ?? 'Pengguna') }}
                @endif
            </div>
        </li>
        <li><hr class="dropdown-divider my-1 opacity-50"></li>

        {{-- TOMBOL LOGOUT (DI PALING ATAS) --}}
        <li>
            <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                @csrf
                <button type="submit" class="dropdown-item d-flex align-items-center gap-2.5 py-2 px-2.5 rounded-3 text-danger border-0 bg-transparent w-100 text-start" style="cursor: pointer;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                         style="width: 32px; height: 32px; background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                        <i class="bi bi-box-arrow-right fs-6"></i>
                    </div>
                    <div>
                        <div class="fw-bold small text-danger" style="line-height: 1.2;">Logout</div>
                        <div class="text-muted" style="font-size: 0.68rem;">Akhiri sesi akun Anda</div>
                    </div>
                </button>
            </form>
        </li>
        <li><hr class="dropdown-divider my-1.5 opacity-50"></li>

        {{-- BERANDA PUBLIK --}}
        <li>
            <a class="dropdown-item d-flex align-items-center gap-2.5 py-2 px-2.5 rounded-3 text-decoration-none" 
               href="{{ url('/') }}" 
               target="_blank">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                     style="width: 32px; height: 32px; background: rgba(0, 51, 102, 0.08); color: #003366;">
                    <i class="bi bi-globe2 fs-6"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark small" style="line-height: 1.2;">Beranda Publik</div>
                    <div class="text-muted" style="font-size: 0.68rem;">Halaman utama GuruKuu</div>
                </div>
            </a>
        </li>
        <li><hr class="dropdown-divider my-1.5 opacity-50"></li>
        <li>
            <a class="dropdown-item d-flex align-items-center gap-2.5 py-2 px-2.5 rounded-3 text-decoration-none" 
               href="{{ config('services.sipintu.base_url', 'https://sipintu.smkn1bangsri.sch.id') }}" 
               target="_blank">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-light border flex-shrink-0" 
                     style="width: 32px; height: 32px;">
                    <img src="{{ asset('images/sipintu-logo.png') }}" alt="SiPintu" style="width: 18px; height: 18px; object-fit: contain;">
                </div>
                <div>
                    <div class="fw-bold text-dark small" style="line-height: 1.2;">Portal SiPintu</div>
                    <div class="text-muted" style="font-size: 0.68rem;">SMKN 1 Bangsri</div>
                </div>
            </a>
        </li>
    </ul>
</div>
