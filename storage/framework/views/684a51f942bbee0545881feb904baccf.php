
<?php $__env->startSection('title', 'Leaderboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <div class="page-label">PERINGKAT & STATISTIK</div>
        <h1 class="page-title">Leaderboard Guru</h1>
        <p class="page-subtitle">Peringkat guru terbaik berdasarkan penilaian siswa</p>
    </div>
    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalTop10">
        <i class="bi bi-list-ul me-1"></i> Lihat Top 10 Semua Kelas
    </button>
</div>


<?php if($top3Global->count() >= 3): ?>
<?php $top1 = $top3Global[0]; $top2 = $top3Global[1]; $top3 = $top3Global[2]; ?>
<div class="row g-4 mb-5 align-items-end justify-content-center">
    <div class="col-md-3 order-md-1">
        <div class="card-custom p-4 text-center h-100">
            <div class="position-relative d-inline-block mb-3">
                <img src="<?php echo e($top2->photo_url); ?>" class="rounded-circle" style="width: 90px; height: 90px; object-fit: cover; border: 4px solid #C0C0C0;">
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background: #C0C0C0; color: #000;">#2</span>
            </div>
            <h6 class="fw-bold mb-1"><?php echo e($top2->nama); ?></h6>
            <p class="text-muted small mb-2"><?php echo e($top2->jurusan?->nama_jurusan ?? 'Umum'); ?></p>
            <div style="color: var(--secondary); font-weight: 700;"><i class="bi bi-star-fill"></i> <?php echo e(number_format($top2->rata_rata_nilai, 2)); ?></div>
            <small class="text-muted"><?php echo e($top2->total_penilaian); ?> vote</small>
        </div>
    </div>
    <div class="col-md-4 order-md-2 mt-md-4">
        <div class="card-custom p-5 text-center h-100" style="background: var(--primary); color: white;">
            <div class="position-relative d-inline-block mb-3">
                <img src="<?php echo e($top1->photo_url); ?>" class="rounded-circle" style="width: 130px; height: 130px; object-fit: cover; border: 5px solid var(--secondary);">
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background: #FFD700; color: #000;">🏆 #1</span>
            </div>
            <h4 class="fw-bold mb-1"><?php echo e($top1->nama); ?></h4>
            <p class="mb-2" style="opacity: 0.8;"><?php echo e($top1->jurusan?->nama_jurusan ?? 'Umum'); ?></p>
            <div style="color: var(--secondary); font-weight: 700; font-size: 1.75rem;"><i class="bi bi-star-fill"></i> <?php echo e(number_format($top1->rata_rata_nilai, 2)); ?></div>
            <small style="opacity: 0.8;"><?php echo e($top1->total_penilaian); ?> vote</small>
        </div>
    </div>
    <div class="col-md-3 order-md-3">
        <div class="card-custom p-4 text-center h-100">
            <div class="position-relative d-inline-block mb-3">
                <img src="<?php echo e($top3->photo_url); ?>" class="rounded-circle" style="width: 90px; height: 90px; object-fit: cover; border: 4px solid #CD7F32;">
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background: #CD7F32; color: #fff;">#3</span>
            </div>
            <h6 class="fw-bold mb-1"><?php echo e($top3->nama); ?></h6>
            <p class="text-muted small mb-2"><?php echo e($top3->jurusan?->nama_jurusan ?? 'Umum'); ?></p>
            <div style="color: var(--secondary); font-weight: 700;"><i class="bi bi-star-fill"></i> <?php echo e(number_format($top3->rata_rata_nilai, 2)); ?></div>
            <small class="text-muted"><?php echo e($top3->total_penilaian); ?> vote</small>
        </div>
    </div>
</div>
<?php endif; ?>

<hr class="my-5">


<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card-custom p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-building me-2 text-primary"></i>Pilih Kelas</h5>
            <form method="GET" action="<?php echo e(route('admin.leaderboard.index')); ?>">
                <select name="kelas_id" class="form-select mb-3" onchange="this.form.submit()" style="border-radius: 8px;">
                    <?php $__currentLoopData = $kelasList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kelas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($kelas->id); ?>" <?php echo e($filterKelasId == $kelas->id ? 'selected' : ''); ?>>
                            Tingkat <?php echo e($kelas->tingkat); ?> - <?php echo e($kelas->nama_kelas); ?> (<?php echo e($kelas->jurusan->nama_jurusan); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </form>
            <?php if($kelasAktifStats): ?>
            <div class="mt-3 p-3 rounded" style="background: var(--bg-light);">
                <div class="d-flex justify-content-between mb-2"><span class="small text-muted">Guru Normada</span><strong style="color: var(--primary);"><?php echo e($kelasAktifStats['normada']->count()); ?></strong></div>
                <div class="d-flex justify-content-between mb-2"><span class="small text-muted">Guru Produktif</span><strong style="color: var(--accent);"><?php echo e($kelasAktifStats['produktif']->count()); ?></strong></div>
                <div class="d-flex justify-content-between"><span class="small text-muted">Total Siswa</span><strong><?php echo e($kelasAktifStats['kelas']->jumlah_siswa); ?></strong></div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-8">
        <?php if($kelasAktifStats): ?>
        <div class="card-custom p-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Hasil Polling: Tingkat <?php echo e($kelasAktifStats['kelas']->tingkat); ?> - <?php echo e($kelasAktifStats['kelas']->nama_kelas); ?></h5>
            <ul class="nav nav-pills mb-4" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tabN" type="button">Guru Normada (<?php echo e($kelasAktifStats['normada']->count()); ?>)</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tabP" type="button">Guru Produktif (<?php echo e($kelasAktifStats['produktif']->count()); ?>)</button></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="tabN">
                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead><tr><th>#</th><th>NAMA GURU</th><th>MAPEL</th><th class="text-center">VOTE</th><th class="text-center">RATA-RATA</th></tr></thead>
                            <tbody>
                                <?php $__currentLoopData = $kelasAktifStats['normada']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <?php if($i===0): ?><span class="badge" style="background:#FFD700;color:#000;">🥇</span>
                                        <?php elseif($i===1): ?><span class="badge" style="background:#C0C0C0;color:#000;">🥈</span>
                                        <?php elseif($i===2): ?><span class="badge" style="background:#CD7F32;color:#fff;">🥉</span>
                                        <?php else: ?> #<?php echo e($i+1); ?> <?php endif; ?>
                                    </td>
                                    <td><strong><?php echo e($g->nama); ?></strong></td>
                                    <td><small class="text-muted"><?php echo e($g->pivot->mata_pelajaran ?? '-'); ?></small></td>
                                    <td class="text-center fw-bold"><?php echo e($g->penilaian_count); ?></td>
                                    <td class="text-center fw-bold" style="color: var(--secondary);"><i class="bi bi-star-fill"></i> <?php echo e(number_format($g->rata_rata_nilai, 2)); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="tabP">
                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead><tr><th>#</th><th>NAMA GURU</th><th>MAPEL</th><th class="text-center">VOTE</th><th class="text-center">RATA-RATA</th></tr></thead>
                            <tbody>
                                <?php $__currentLoopData = $kelasAktifStats['produktif']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <?php if($i===0): ?><span class="badge" style="background:#FFD700;color:#000;">🥇</span>
                                        <?php elseif($i===1): ?><span class="badge" style="background:#C0C0C0;color:#000;">🥈</span>
                                        <?php elseif($i===2): ?><span class="badge" style="background:#CD7F32;color:#fff;">🥉</span>
                                        <?php else: ?> #<?php echo e($i+1); ?> <?php endif; ?>
                                    </td>
                                    <td><strong><?php echo e($g->nama); ?></strong></td>
                                    <td><small class="text-muted"><?php echo e($g->pivot->mata_pelajaran ?? '-'); ?></small></td>
                                    <td class="text-center fw-bold"><?php echo e($g->penilaian_count); ?></td>
                                    <td class="text-center fw-bold" style="color: var(--secondary);"><i class="bi bi-star-fill"></i> <?php echo e(number_format($g->rata_rata_nilai, 2)); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>


<div class="modal fade" id="modalTop10" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-trophy-fill text-warning me-2"></i>Top 10 Guru Terbaik</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="list-group list-group-flush">
                    <?php $__currentLoopData = $top10Global; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                        <div class="d-flex align-items-center gap-3">
                            <span class="fw-bold fs-5" style="width:30px;"><?php echo e($i+1); ?>.</span>
                            <img src="<?php echo e($g->photo_url); ?>" class="rounded-circle" style="width:45px;height:45px;object-fit:cover;">
                            <div><h6 class="mb-0 fw-bold"><?php echo e($g->nama); ?></h6><small class="text-muted"><?php echo e($g->jurusan?->nama_jurusan ?? 'Umum'); ?></small></div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold" style="color: var(--secondary);"><i class="bi bi-star-fill"></i> <?php echo e(number_format($g->rata_rata_nilai, 2)); ?></div>
                            <small class="text-muted"><?php echo e($g->total_penilaian); ?> vote</small>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADVAN\PROJEK\Laravel\GuruKuu\resources\views/admin/leaderboard/index.blade.php ENDPATH**/ ?>