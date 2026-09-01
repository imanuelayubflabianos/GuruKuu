
<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <div class="page-label">DASHBOARD</div>
        <h1 class="page-title">Selamat Datang, <?php echo e(auth()->user()->name); ?>!</h1>
        <p class="page-subtitle">Berikut adalah ringkasan data sekolah hari ini.</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-label">Total Guru</div>
            <div class="stat-card-value"><?php echo e($totalGuru ?? 0); ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-label">Total Siswa</div>
            <div class="stat-card-value"><?php echo e($totalSiswa ?? 0); ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-card-label">Total Penilaian</div>
            <div class="stat-card-value"><?php echo e($totalPenilaian ?? 0); ?></div>
        </div>
    </div>
    <div class="col-md-3">
        <a href="<?php echo e(route('admin.kontak.index')); ?>" class="text-decoration-none">
            <div class="stat-card" style="border-left:4px solid var(--accent);">
                <div class="stat-card-label">PESAN MASUK</div>
                <div class="stat-card-value" style="color:var(--accent); font-size:1.2rem;">
                    <i class="bi bi-envelope-fill"></i> Lihat Pesan
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card-custom p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-trophy-fill me-2 text-warning"></i>Top 3 Guru Terbaik</h5>
            <?php if(isset($topGuru) && count($topGuru) > 0): ?>
                <?php $__currentLoopData = $topGuru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="d-flex align-items-center mb-3 p-2 rounded" style="background:var(--bg-light);">
                    <div class="fs-4 me-3"><?php echo e($index == 0 ? '🥇' : ($index == 1 ? '🥈' : '🥉')); ?></div>
                    <img src="<?php echo e($g->photo_url); ?>" class="rounded-circle me-3" style="width:40px; height:40px; object-fit:cover;">
                    <div class="flex-grow-1">
                        <div class="fw-bold"><?php echo e($g->nama); ?></div>
                        <small class="text-muted"><?php echo e($g->jurusan->nama_jurusan ?? 'Umum'); ?></small>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-warning"><?php echo e(number_format($g->rata_rata_nilai, 1)); ?></div>
                        <small class="text-muted"><?php echo e($g->total_penilaian); ?> penilaian</small>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <p class="text-muted text-center">Belum ada data penilaian.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card-custom p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-chat-dots-fill me-2 text-primary"></i>Kritik & Saran Terbaru</h5>
            <?php if(isset($feedbacks) && count($feedbacks) > 0): ?>
                <?php $__currentLoopData = $feedbacks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="mb-3 p-2 rounded" style="background:var(--bg-light);">
                    <div class="d-flex justify-content-between">
                        <strong class="small"><?php echo e($f->siswa->name ?? 'Anonim'); ?></strong>
                        <small class="text-muted"><?php echo e($f->created_at->format('d M Y')); ?></small>
                    </div>
                    <p class="mb-0 small"><?php echo e(Str::limit($f->kritik ?? $f->saran, 80)); ?></p>
                    <small class="text-muted">Untuk: <?php echo e($f->guru->nama ?? '-'); ?></small>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('admin.kritik-saran.index')); ?>" class="btn btn-sm btn-outline-primary w-100 mt-2">Lihat Semua</a>
            <?php else: ?>
                <p class="text-muted text-center">Belum ada kritik & saran.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADVAN\PROJEK\Laravel\GuruKuu\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>