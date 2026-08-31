

<?php $__env->startSection('title', 'Pengaturan Sistem'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <div class="page-label">KONFIGURASI SISTEM</div>
        <h1 class="page-title">Pengaturan Umum</h1>
        <p class="page-subtitle">Kelola periode aktif, kategori, dan atribut sistem penilaian.</p>
    </div>
</div>

<div class="row g-4">
    
    <div class="col-md-12">
        <div class="card-custom p-4" style="background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: white; border: none;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-calendar-check me-3" style="font-size: 2.5rem;"></i>
                    <div>
                        <div class="font-mono" style="font-size: 0.75rem; letter-spacing: 2px; opacity: 0.8;">PERIODE PENILAIAN SAAT INI</div>
                        <?php if($periodeAktif): ?>
                            <h3 class="fw-bold mb-1"><?php echo e($periodeAktif->nama_periode); ?></h3>
                            <small style="opacity: 0.9;">
                                <i class="bi bi-clock"></i> <?php echo e($periodeAktif->tanggal_mulai->format('d M Y')); ?> s/d <?php echo e($periodeAktif->tanggal_selesai->format('d M Y')); ?>

                            </small>
                        <?php else: ?>
                            <h3 class="fw-bold mb-1">⚠️ Tidak Ada Periode Aktif</h3>
                            <small style="opacity: 0.9;">Siswa tidak dapat memberikan penilaian sampai periode baru diaktifkan.</small>
                        <?php endif; ?>
                    </div>
                </div>
                <a href="<?php echo e(route('admin.periode.index')); ?>" class="btn btn-light text-primary fw-bold px-4">
                    <i class="bi bi-gear"></i> Kelola Periode
                </a>
            </div>
        </div>
    </div>

    
    <div class="col-md-4">
        <a href="<?php echo e(route('admin.jurusan.index')); ?>" class="card-custom p-4 text-decoration-none h-100" style="color: inherit; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='none'; this.style.boxShadow='none'">
            <div class="d-flex align-items-center mb-3">
                <div class="rounded-circle p-3 me-3" style="background: rgba(0,51,102,0.1);">
                    <i class="bi bi-book" style="font-size: 1.5rem; color: var(--primary);"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0">Kategori / Jurusan</h5>
                    <small class="text-muted"><?php echo e($totalJurusan); ?> Data Tersedia</small>
                </div>
            </div>
            <p class="text-muted mb-0">Tambah, edit, atau hapus kategori jurusan tempat guru dan siswa berada.</p>
        </a>
    </div>

    <div class="col-md-4">
        <a href="<?php echo e(route('admin.badge.index')); ?>" class="card-custom p-4 text-decoration-none h-100" style="color: inherit; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='none'; this.style.boxShadow='none'">
            <div class="d-flex align-items-center mb-3">
                <div class="rounded-circle p-3 me-3" style="background: rgba(255,193,7,0.15);">
                    <i class="bi bi-award" style="font-size: 1.5rem; color: var(--secondary);"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0">Badge & Penghargaan</h5>
                    <small class="text-muted"><?php echo e($totalBadge); ?> Jenis Badge</small>
                </div>
            </div>
            <p class="text-muted mb-0">Kelola jenis penghargaan (icon) yang dapat diberikan kepada guru berprestasi.</p>
        </a>
    </div>

    <div class="col-md-4">
        <a href="<?php echo e(route('admin.periode.index')); ?>" class="card-custom p-4 text-decoration-none h-100" style="color: inherit; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='none'; this.style.boxShadow='none'">
            <div class="d-flex align-items-center mb-3">
                <div class="rounded-circle p-3 me-3" style="background: rgba(0,168,107,0.1);">
                    <i class="bi bi-calendar-range" style="font-size: 1.5rem; color: var(--accent);"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0">Riwayat Periode</h5>
                    <small class="text-muted"><?php echo e($semuaPeriode->count()); ?> Total Periode</small>
                </div>
            </div>
            <p class="text-muted mb-0">Lihat riwayat jadwal buka/tutup penilaian dan kelola semester baru.</p>
        </a>
    </div>
</div>


<div class="row g-4 mt-4">
    <div class="col-md-12">
        <div class="card-custom p-4" style="border-left: 4px solid #dc3545;">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-exclamation-triangle-fill text-danger me-2" style="font-size: 1.5rem;"></i>
                <h5 class="fw-bold text-danger mb-0">Zona Berbahaya: Reset Statistik & Leaderboard</h5>
            </div>
            <p class="text-muted mb-4">
                Gunakan fitur ini jika Anda ingin menghapus <strong>SEMUA data penilaian</strong> dan mengembalikan rating seluruh guru menjadi 0. 
                <br><small class="text-danger fw-bold">⚠️ Tindakan ini tidak dapat dibatalkan.</small>
            </p>

            <div class="bg-light p-3 rounded" style="max-width: 500px;">
                <form action="<?php echo e(route('admin.pengaturan.reset')); ?>" method="POST" id="formReset">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label font-mono" style="font-size: 0.75rem; font-weight: 600; letter-spacing: 1px;">
                            KONFIRMASI KEAMANAN
                        </label>
                        <input type="text" name="confirm_text" id="confirmInput" class="form-control" 
                               placeholder="Ketik RESET di sini untuk melanjutkan" 
                               style="border-radius: 8px; border: 1px solid #dc3545;">
                        <small class="text-muted">Anda wajib mengetik kata <strong>"RESET"</strong> (huruf besar semua).</small>
                    </div>
                    <button type="button" class="btn btn-danger px-4" onclick="konfirmasiReset()">
                        <i class="bi bi-trash"></i> Hapus Semua Data Penilaian
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function konfirmasiReset() {
    const input = document.getElementById('confirmInput').value;
    
    if (input !== 'RESET') {
        Swal.fire({
            icon: 'error',
            title: 'Konfirmasi Gagal',
            text: 'Anda harus mengetik "RESET" persis seperti instruksi.',
            confirmButtonColor: '#003366'
        });
        return;
    }

    Swal.fire({
        title: 'Yakin Ingin Mereset?',
        text: "Semua penilaian siswa dan rating guru akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Reset Sekarang!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formReset').submit();
        }
    });
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADVAN\PROJEK\Laravel\GuruKuu\resources\views/admin/pengaturan/index.blade.php ENDPATH**/ ?>