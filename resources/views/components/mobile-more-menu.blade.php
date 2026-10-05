<div class="dropdown">
    <button class="btn btn-light border rounded-circle shadow-sm gk-topbar-btn-sm d-flex align-items-center justify-content-center" 
            type="button" 
            data-bs-toggle="dropdown" 
            data-bs-auto-close="true"
            aria-expanded="false" 
            title="Menu Lainnya">
        <i class="bi bi-three-dots-vertical text-dark" style="font-size: 1.05rem;"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 p-2 gk-mobile-more-dropdown mt-2" 
        style="min-width: 215px; border-radius: 14px; z-index: 1060;">
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
