{{-- MODAL LAPOR / REPORT ULASAN SISWA --}}
<div class="modal fade" id="modalReportUlasan" tabindex="-1" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formReportUlasan" method="POST" class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            @csrf
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-danger" style="width: 40px; height: 40px; background: rgba(220, 53, 69, 0.1);">
                        <i class="bi bi-flag-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0">Laporkan Ulasan Ini</h5>
                        <small class="text-muted" style="font-size: 0.78rem;">Bantu menjaga komunitas tetap santun, aman, dan konstruktif</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-3">
                <div class="alert alert-light border small text-muted mb-3 py-2 px-3 rounded-3" style="font-size: 0.78rem;">
                    <i class="bi bi-shield-check text-primary me-1"></i> Laporan Anda bersifat <strong>anonim</strong> bagi pengguna lain dan akan ditinjau langsung oleh Administrator.
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark small">Pilih Alasan Pelaporan: <span class="text-danger">*</span></label>
                    <div class="d-flex flex-column gap-2">
                        <label class="form-check p-2.5 rounded border d-flex align-items-center gap-2 mb-0" style="cursor: pointer;">
                            <input class="form-check-input ms-0 mt-0" type="radio" name="alasan" value="kata_kasar" checked>
                            <div class="small">
                                <strong class="d-block text-dark">Kata Kasar / Tidak Pantas</strong>
                                <span class="text-muted" style="font-size: 0.72rem;">Mengandung kata kotor, makian, atau ujaran vulgar.</span>
                            </div>
                        </label>
                        <label class="form-check p-2.5 rounded border d-flex align-items-center gap-2 mb-0" style="cursor: pointer;">
                            <input class="form-check-input ms-0 mt-0" type="radio" name="alasan" value="ujaran_kebencian">
                            <div class="small">
                                <strong class="d-block text-dark">Ujaran Kebencian / Menghina Guru</strong>
                                <span class="text-muted" style="font-size: 0.72rem;">Menyerang personal, merendahkan martabat, atau bernada mengejek.</span>
                            </div>
                        </label>
                        <label class="form-check p-2.5 rounded border d-flex align-items-center gap-2 mb-0" style="cursor: pointer;">
                            <input class="form-check-input ms-0 mt-0" type="radio" name="alasan" value="fitnah">
                            <div class="small">
                                <strong class="d-block text-dark">Fitnah / Fakta Palsu</strong>
                                <span class="text-muted" style="font-size: 0.72rem;">Tuduhan palsu yang merugikan nama baik pengajar.</span>
                            </div>
                        </label>
                        <label class="form-check p-2.5 rounded border d-flex align-items-center gap-2 mb-0" style="cursor: pointer;">
                            <input class="form-check-input ms-0 mt-0" type="radio" name="alasan" value="spam">
                            <div class="small">
                                <strong class="d-block text-dark">Spam / Tidak Relevan</strong>
                                <span class="text-muted" style="font-size: 0.72rem;">Teks acak, promosi, atau tidak berhubungan dengan evaluasi guru.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label fw-bold text-dark small">Catatan Tambahan (Opsional):</label>
                    <textarea name="catatan" class="form-control form-control-sm" rows="2" maxlength="255" placeholder="Jelaskan secara singkat bagian yang melanggar..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-custom" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger px-3 shadow-sm d-inline-flex align-items-center gap-1.5" id="btnSubmitReport">
                    <i class="bi bi-send-fill"></i>
                    <span>Kirim Laporan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModalReportUlasan(penilaianId) {
    const form = document.getElementById('formReportUlasan');
    if (form) {
        form.action = '/ulasan/' + penilaianId + '/report';
    }
    const modalEl = document.getElementById('modalReportUlasan');
    if (modalEl) {
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }
}
</script>
