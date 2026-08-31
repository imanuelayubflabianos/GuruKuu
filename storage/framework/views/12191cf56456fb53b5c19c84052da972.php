
<?php $__env->startSection('title', 'Laporan & Feedback'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <div class="page-label">ANALISIS DATA</div>
        <h1 class="page-title">Laporan & Feedback Siswa</h1>
        <p class="page-subtitle">Ringkasan data penilaian & umpan balik <?php echo e($periodeAktif?->nama_periode ?? '-'); ?></p>
    </div>
</div>


<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="stat-card"><div class="stat-card-label">Total Guru</div><div class="stat-card-value"><?php echo e($totalGuru); ?></div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-card-label">Total Siswa</div><div class="stat-card-value"><?php echo e($totalSiswa); ?></div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-card-label">Total Penilaian</div><div class="stat-card-value"><?php echo e($totalPenilaian); ?></div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-card-label">Rata-rata Umum</div><div class="stat-card-value" style="color: var(--secondary);"><i class="bi bi-star-fill"></i> <?php echo e($rataRataUmum); ?></div></div></div>
</div>

<div class="row g-4">
    
    <div class="col-lg-5">
        <div class="card-custom p-4 h-100">
            <h5 class="fw-bold mb-4"><i class="bi bi-star-fill me-2 text-warning"></i>Distribusi Rating</h5>
            <?php $__currentLoopData = [5,4,3,2,1]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bintang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $jumlah = $distribusiBintang[$bintang];
                    $persen = round(($jumlah / $totalUntukPersen) * 100, 1);
                    $warna = $bintang >= 4 ? '#22c55e' : ($bintang == 3 ? '#f59e0b' : '#ef4444');
                ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-bold">
                            <?php for($i = 1; $i <= 5; $i++): ?> <i class="bi bi-star-fill" style="color: <?php echo e($i <= $bintang ? '#FFC107' : '#e2e8f0'); ?>; font-size: 0.9rem;"></i> <?php endfor; ?>
                            <span class="ms-2 text-muted small">(<?php echo e($bintang); ?>)</span>
                        </span>
                        <span class="fw-bold"><?php echo e($jumlah); ?> <span class="text-muted small">(<?php echo e($persen); ?>%)</span></span>
                    </div>
                    <div class="progress" style="height: 10px; border-radius: 5px;">
                        <div class="progress-bar" style="width: <?php echo e($persen); ?>%; background: <?php echo e($warna); ?>; border-radius: 5px;"></div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <div class="col-lg-7">
        <div class="card-custom p-4 h-100">
            <h5 class="fw-bold mb-4"><i class="bi bi-chat-dots-fill me-2 text-primary"></i>Kritik & Saran Siswa</h5>
            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                <table class="table table-custom mb-0" id="feedbackTable">
                    <thead>
                        <tr>
                            <th>GURU</th>
                            <th>FEEDBACK</th>
                            <th>STATUS</th>
                            <th>TANGGAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $feedbacks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?php echo e($f->guru->photo_url); ?>" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                    <strong class="small"><?php echo e($f->guru->nama); ?></strong>
                                </div>
                            </td>
                            <td style="max-width: 300px;">
                                <?php if($f->kritik): ?><div class="mb-1 small p-1 rounded" style="background:#fef3c7;"><strong>K:</strong> <?php echo e(Str::limit($f->kritik, 60)); ?></div><?php endif; ?>
                                <?php if($f->saran): ?><div class="small p-1 rounded" style="background:#dbeafe;"><strong>S:</strong> <?php echo e(Str::limit($f->saran, 60)); ?></div><?php endif; ?>
                            </td>
                            <td>
                                <?php if($f->isToxic()): ?> <span class="badge bg-danger">TOXIC</span>
                                <?php else: ?> <span class="badge bg-success">AMAN</span> <?php endif; ?>
                            </td>
                            <td class="small text-muted"><?php echo e($f->created_at->format('d M Y')); ?></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada feedback.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    $('#feedbackTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.8/i18n/id.json' },
        searching: true, // ✅ KOLOM SEARCH AKTIF
        order: [[3, 'desc']],
        pageLength: 10
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADVAN\PROJEK\Laravel\GuruKuu\resources\views/admin/statistik/index.blade.php ENDPATH**/ ?>