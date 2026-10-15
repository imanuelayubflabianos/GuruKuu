/**
 * GuruKuu Universal Image Cropper & Live Preview
 * Memberikan fitur potong/crop foto & live preview seketika sebelum form disimpan.
 */
(function() {
    'use strict';

    // 1. Pastikan CSS Cropper.js tersedia
    if (!document.getElementById('cropperjs-css')) {
        const link = document.createElement('link');
        link.id = 'cropperjs-css';
        link.rel = 'stylesheet';
        link.href = 'https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css';
        document.head.appendChild(link);
    }

    // 2. Load JS Cropper.js jika belum ada
    function ensureCropperLoaded(callback) {
        if (typeof Cropper !== 'undefined') {
            callback();
            return;
        }
        let script = document.getElementById('cropperjs-script');
        if (!script) {
            script = document.createElement('script');
            script.id = 'cropperjs-script';
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js';
            script.onload = callback;
            document.head.appendChild(script);
        } else {
            script.addEventListener('load', callback);
        }
    }

    // 3. Bangun Modal Pemotong Foto Reusable
    function ensureModalExists() {
        if (document.getElementById('gurukuuCropModal')) return;

        const modalHtml = `
        <div class="modal fade" id="gurukuuCropModal" tabindex="-1" aria-labelledby="gurukuuCropModalLabel" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content shadow-lg border-0" style="border-radius: 16px; overflow: hidden;">
                    <div class="modal-header border-bottom py-3 px-4 bg-light">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="bi bi-crop"></i>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold text-dark fs-6 mb-0" id="gurukuuCropModalLabel">Sesuaikan & Potong Foto</h5>
                                <span class="text-muted" style="font-size: 0.76rem;">Geser dan sesuaikan area gambar sesuai proporsi yang diinginkan</span>
                            </div>
                        </div>
                        <button type="button" class="btn-close" id="btnCancelCropX" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body p-3 bg-dark d-flex align-items-center justify-content-center" style="min-height: 380px; max-height: 65vh; overflow: hidden;">
                        <div style="max-width: 100%; max-height: 60vh;">
                            <img id="gurukuuCropImage" src="" alt="Area Potong" style="max-width: 100%; display: block;">
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-light py-2.5 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-1.5" id="cropControlButtons">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCropRotateLeft" title="Putar Kiri"><i class="bi bi-arrow-counterclockwise"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCropRotateRight" title="Putar Kanan"><i class="bi bi-arrow-clockwise"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnCropReset" title="Reset"><i class="bi bi-aspect-ratio"></i> Reset</button>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-light border btn-sm px-3" id="btnCancelCrop">Batal</button>
                            <button type="button" class="btn btn-primary-custom btn-sm px-4 fw-semibold" id="btnApplyCrop">
                                <i class="bi bi-check2 me-1"></i> Potong & Gunakan Foto
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    let activeCropper = null;
    let currentInput = null;
    let currentPreviewSelector = null;
    let currentAspectRatio = NaN;

    /**
     * Buka modal crop untuk file input yang dipilih
     */
    function openCropDialog(inputElement, file, aspectRatio, previewSelector) {
        ensureModalExists();
        ensureCropperLoaded(() => {
            const modalEl = document.getElementById('gurukuuCropModal');
            const imgEl = document.getElementById('gurukuuCropImage');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

            currentInput = inputElement;
            currentPreviewSelector = previewSelector;
            currentAspectRatio = aspectRatio;

            const reader = new FileReader();
            reader.onload = function(e) {
                imgEl.src = e.target.result;
                modal.show();

                modalEl.addEventListener('shown.bs.modal', function onShown() {
                    modalEl.removeEventListener('shown.bs.modal', onShown);
                    if (activeCropper) activeCropper.destroy();

                    activeCropper = new Cropper(imgEl, {
                        aspectRatio: isNaN(aspectRatio) ? NaN : aspectRatio,
                        viewMode: 1,
                        autoCropArea: 0.95,
                        responsive: true,
                        restore: false,
                        checkCrossOrigin: false,
                        guides: true,
                        center: true,
                        highlight: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                    });
                });
            };
            reader.readAsDataURL(file);
        });
    }

    // Event listener untuk tombol kontrol di modal
    document.addEventListener('click', function(e) {
        if (!activeCropper) return;
        if (e.target.closest('#btnCropRotateLeft')) {
            activeCropper.rotate(-90);
        } else if (e.target.closest('#btnCropRotateRight')) {
            activeCropper.rotate(90);
        } else if (e.target.closest('#btnCropReset')) {
            activeCropper.reset();
        } else if (e.target.closest('#btnCancelCrop') || e.target.closest('#btnCancelCropX')) {
            const modalEl = document.getElementById('gurukuuCropModal');
            bootstrap.Modal.getInstance(modalEl)?.hide();
            if (activeCropper) {
                activeCropper.destroy();
                activeCropper = null;
            }
        } else if (e.target.closest('#btnApplyCrop')) {
            if (!activeCropper || !currentInput) return;

            // Dapatkan canvas hasil potongan
            const canvas = activeCropper.getCroppedCanvas({
                maxWidth: 1920,
                maxHeight: 1920,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            if (!canvas) return;

            canvas.toBlob(function(blob) {
                if (!blob) return;

                const fileName = (currentInput.files[0]?.name || 'foto_cropped.jpg').replace(/\.[^/.]+$/, "") + ".jpg";
                const croppedFile = new File([blob], fileName, { type: 'image/jpeg', lastModified: Date.now() });

                // Masukkan file cropped ke dalam input file via DataTransfer
                try {
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(croppedFile);
                    currentInput.files = dataTransfer.files;
                } catch (err) {
                    console.warn('DataTransfer not fully supported:', err);
                }

                // Update Live Preview gambar
                const dataUrl = canvas.toDataURL('image/jpeg', 0.92);
                if (currentPreviewSelector) {
                    const targets = typeof currentPreviewSelector === 'string'
                        ? document.querySelectorAll(currentPreviewSelector)
                        : [currentPreviewSelector];

                    targets.forEach(el => {
                        if (el.tagName === 'IMG') {
                            el.src = dataUrl;
                            el.classList.remove('d-none');
                            el.style.display = '';
                        } else if (el) {
                            el.innerHTML = `<img src="${dataUrl}" class="rounded w-100 shadow-sm" style="height: 100%; object-fit: cover;">`;
                        }
                    });
                }

                // Sembunyikan placeholder/inisial jika ada
                const iconPlaceholder = currentInput.closest('.card, .card-custom, .settings-card, form')?.querySelector('.guru-avatar-placeholder, #brandLogoIcon');
                if (iconPlaceholder) iconPlaceholder.style.display = 'none';

                // Tutup modal
                const modalEl = document.getElementById('gurukuuCropModal');
                bootstrap.Modal.getInstance(modalEl)?.hide();
                if (activeCropper) {
                    activeCropper.destroy();
                    activeCropper = null;
                }
            }, 'image/jpeg', 0.92);
        }
    });

    /**
     * Inisialisasi otomatis pada input file yang mendukung crop
     */
    function initCropperInputs() {
        document.querySelectorAll('input[type="file"][data-crop]').forEach(input => {
            if (input._cropperAttached) return;
            input._cropperAttached = true;

            input.addEventListener('change', function() {
                if (!this.files || !this.files[0]) return;
                const file = this.files[0];

                // Skip non-image
                if (!file.type.startsWith('image/')) return;

                // Rasio crop dari data-crop: "1:1", "16:9", "free"
                const cropSpec = this.getAttribute('data-crop') || '1:1';
                let ratio = NaN;
                if (cropSpec === '1:1') ratio = 1;
                else if (cropSpec === '16:9') ratio = 16 / 9;
                else if (cropSpec === '4:3') ratio = 4 / 3;

                const previewTarget = this.getAttribute('data-crop-preview');
                openCropDialog(this, file, ratio, previewTarget);
            });
        });
    }

    // Jalankan saat DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCropperInputs);
    } else {
        initCropperInputs();
    }

    // Expose fungsi ke window agar dapat dipanggil manual
    window.GuruKuuCropper = {
        open: openCropDialog,
        init: initCropperInputs
    };
})();
