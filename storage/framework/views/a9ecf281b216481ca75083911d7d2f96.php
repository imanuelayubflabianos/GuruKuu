
<?php $__env->startSection('title', 'Data Siswa'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <div class="page-label">MANAJEMEN DATA</div>
        <h1 class="page-title">Data Siswa</h1>
        <p class="page-subtitle">Kelola data siswa dan filter berdasarkan kelas.</p>
    </div>
    <a href="<?php echo e(route('admin.siswa.create')); ?>" class="btn btn-primary-custom">
        <i class="bi bi-plus-circle me-1"></i> Tambah Siswa
    </a>
</div>


<div class="card-custom p-3 mb-4">
    <form method="GET" action="<?php echo e(route('admin.siswa.index')); ?>" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-bold text-muted">Filter Kelas</label>
            <select name="kelas" class="form-select" style="border-radius: 8px;" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                <?php $__currentLoopData = $kelasList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($k->id); ?>" <?php echo e(request('kelas') == $k->id ? 'selected' : ''); ?>>
                        Tingkat <?php echo e($k->tingkat); ?> - <?php echo e($k->nama_kelas); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-4">
            <a href="<?php echo e(route('admin.siswa.index')); ?>" class="btn btn-outline-custom">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter
            </a>
        </div>
        <div class="col-md-4 text-end">
            <span class="badge bg-primary px-3 py-2">
                Total: <?php echo e($siswa->count()); ?> siswa
            </span>
        </div>
    </form>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom mb-0" id="siswaTable">
            <thead>
                <tr>
                    <th>NIS</th>
                    <th>NAMA</th>
                    <th>KELAS</th>
                    <th>STATUS</th>
                    <th class="text-center">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $siswa; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="font-mono fw-bold"><?php echo e($s->nis); ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="<?php echo e($s->photo_url); ?>" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                            <div>
                                <strong><?php echo e($s->name); ?></strong>
                                <br><small class="text-muted"><?php echo e($s->email); ?></small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?php
                            // Ambil relasi kelas secara paksa untuk menghindari bentrok dengan kolom string 'kelas'
                            $kelasRel = $s->getRelation('kelas');
                        ?>
                        
                        <?php if($kelasRel && $kelasRel->isNotEmpty()): ?>
                            <?php $__currentLoopData = $kelasRel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="badge bg-light text-dark border">
                                    <?php echo e($kelas->nama_kelas); ?> (Tingkat <?php echo e($kelas->tingkat); ?>)
                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php elseif(is_string($s->kelas) && $s->kelas): ?>
                            
                            <span class="badge bg-light text-dark border"><?php echo e($s->kelas); ?></span>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($s->is_active): ?>
                            <span class="badge bg-success">Aktif</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Nonaktif</span>
                        <?php endif; ?>
                        
                        <?php if(isset($s->warning_count) && $s->warning_count > 0): ?>
                            <span class="badge bg-danger ms-1">
                                <i class="bi bi-exclamation-triangle-fill"></i> <?php echo e($s->warning_count); ?> Warning
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <?php if(Route::has('admin.siswa.toggle')): ?>
                        <form action="<?php echo e(route('admin.siswa.toggle', $s)); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                            <button class="btn btn-sm <?php echo e($s->is_active ? 'btn-outline-warning' : 'btn-outline-success'); ?> mb-1" title="<?php echo e($s->is_active ? 'Nonaktifkan' : 'Aktifkan'); ?>">
                                <i class="bi <?php echo e($s->is_active ? 'bi-lock' : 'bi-unlock'); ?>"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                        
                        <a href="<?php echo e(route('admin.siswa.edit', $s)); ?>" class="btn btn-sm btn-outline-primary mb-1" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="<?php echo e(route('admin.siswa.destroy', $s)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus siswa ini?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-sm btn-outline-danger mb-1" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                        Belum ada data siswa.
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
    $('#siswaTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
        searching: true,
        pageLength: 10
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADVAN\PROJEK\Laravel\GuruKuu\resources\views/admin/siswa/index.blade.php ENDPATH**/ ?>