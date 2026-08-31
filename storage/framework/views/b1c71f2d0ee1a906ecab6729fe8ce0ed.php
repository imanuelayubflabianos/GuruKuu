
<?php $__env->startSection('title', 'Pesan Masuk'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <div class="page-label">LAYANAN PENGADUAN</div>
        <h1 class="page-title">Pesan Masuk</h1>
        <p class="page-subtitle">Kelola pesan masuk dari siswa atau tamu.</p>
    </div>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom mb-0" id="kontakTable">
            <thead>
                <tr>
                    <th style="width: 20%;">PENGIRIM</th>
                    <th style="width: 40%;">PESAN</th>
                    <th style="width: 10%;">STATUS</th>
                    <th style="width: 15%;">WAKTU</th>
                    <th style="width: 15%;" class="text-center">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $kontak; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <strong><?php echo e($k->pengirim); ?></strong>
                        <br><small class="text-muted font-mono"><?php echo e($k->is_siswa ? 'Siswa (NIS: ' . $k->identifier . ')' : 'Tamu'); ?></small>
                    </td>
                    <td><?php echo e(Str::limit($k->pesan, 80)); ?></td>
                    <td>
                        <?php if($k->is_replied): ?> 
                            <span class="badge bg-success">Dibalas</span>
                        <?php else: ?> 
                            <span class="badge bg-warning text-dark">Belum</span> 
                        <?php endif; ?>
                    </td>
                    <td class="font-mono small"><?php echo e($k->created_at->format('d M Y, H:i')); ?></td>
                    <td class="text-center">
                        <?php if(!$k->is_replied): ?>
                            <button class="btn btn-sm btn-primary-custom mb-1" data-bs-toggle="modal" data-bs-target="#modalBalas<?php echo e($k->id); ?>" title="Balas">
                                <i class="bi bi-reply"></i>
                            </button>
                        <?php else: ?>
                            <form action="<?php echo e(route('admin.kontak.destroy-reply', $k)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus balasan?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="btn btn-sm btn-outline-warning mb-1" title="Hapus Balasan"><i class="bi bi-eraser"></i></button>
                            </form>
                        <?php endif; ?>
                        <form action="<?php echo e(route('admin.kontak.destroy', $k)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus pesan ini secara permanen?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-sm btn-outline-danger" title="Hapus Pesan"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>

                <?php if(!$k->is_replied): ?>
                <div class="modal fade" id="modalBalas<?php echo e($k->id); ?>" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form action="<?php echo e(route('admin.kontak.reply', $k)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="modal-header">
                                    <h5 class="modal-title">Balas Pesan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="small text-muted mb-2"><strong>Pesan Asli:</strong><br>"<?php echo e($k->pesan); ?>"</p>
                                    <textarea name="balasan" class="form-control" rows="3" required placeholder="Tulis balasan..."></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary-custom">Kirim</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                        Belum ada pesan masuk.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    $('#kontakTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
        searching: true,
        order: [[3, 'desc']],
        pageLength: 10
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADVAN\PROJEK\Laravel\GuruKuu\resources\views/admin/kontak/index.blade.php ENDPATH**/ ?>