
<?php $__env->startSection('title', 'Data Jurusan'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <div class="page-label">MANAJEMEN DATA</div>
        <h1 class="page-title">Data Jurusan</h1>
        <p class="page-subtitle">Kelola data jurusan dan logo jurusan.</p>
    </div>
    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalTambahJurusan">
        <i class="bi bi-plus-circle me-1"></i> Tambah Jurusan
    </button>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom mb-0" id="jurusanTable">
            <thead>
                <tr>
                    <th>KODE</th>
                    <th>NAMA JURUSAN</th>
                    <th>LOGO</th>
                    <th class="text-center">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $jurusans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="font-mono fw-bold"><?php echo e($j->kode_jurusan); ?></td>
                    <td><?php echo e($j->nama_jurusan); ?></td>
                    <td>
                        <?php if($j->logo): ?>
                            <img src="<?php echo e(asset('storage/' . $j->logo)); ?>" alt="Logo" style="height: 40px; border-radius: 4px;">
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#modalEditJurusan<?php echo e($j->id); ?>">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                        <form action="<?php echo e(route('admin.jurusan.destroy', $j->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus jurusan ini?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>

                <!-- Modal Edit -->
                <div class="modal fade" id="modalEditJurusan<?php echo e($j->id); ?>" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <!-- Perhatikan: Menggunakan $j->id, bukan $jurusans -->
                            <form action="<?php echo e(route('admin.jurusan.update', $j->id)); ?>" method="POST" enctype="multipart/form-data">
                                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Jurusan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Kode Jurusan</label>
                                        <input type="text" name="kode_jurusan" class="form-control" value="<?php echo e($j->kode_jurusan); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Nama Jurusan</label>
                                        <input type="text" name="nama_jurusan" class="form-control" value="<?php echo e($j->nama_jurusan); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Logo Jurusan</label>
                                        <input type="file" name="logo" class="form-control" accept="image/*">
                                        <?php if($j->logo): ?>
                                            <div class="mt-2">
                                                <img src="<?php echo e(asset('storage/' . $j->logo)); ?>" alt="Logo Saat Ini" style="max-height: 60px;">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary-custom">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="4" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                        Belum ada data jurusan.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambahJurusan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?php echo e(route('admin.jurusan.store')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Jurusan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Jurusan</label>
                        <input type="text" name="kode_jurusan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Jurusan</label>
                        <input type="text" name="nama_jurusan" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Logo Jurusan</label>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary-custom">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    $('#jurusanTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
        searching: true,
        pageLength: 10
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADVAN\PROJEK\Laravel\GuruKuu\resources\views/admin/jurusan/index.blade.php ENDPATH**/ ?>