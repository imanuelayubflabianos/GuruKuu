<?php $__env->startSection('title', 'Leaderboard'); ?>

<?php $__env->startSection('content'); ?>
<section style="background: var(--bg-light); padding: 140px 0 80px; min-height: 100vh;">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <div class="section-label">PENCAPAIAN TERTINGGI</div>
            <h1 class="section-title">Leaderboard Guru</h1>
            <p class="text-muted">Peringkat guru terbaik berdasarkan penilaian dan ulasan siswa</p>
        </div>

        
        <?php
            $normadaList = $leaderboardNormada ?? collect();
            $produktifList = $leaderboardProduktif ?? collect();
            $all = $normadaList->merge($produktifList)
                ->sortByDesc('rata_rata_nilai')
                ->take(3)
                ->values();
            $top1 = $all[0] ?? null;
            $top2 = $all[1] ?? null;
            $top3 = $all[2] ?? null;
        ?>

        <div class="row g-4 mb-5 align-items-end">
            
            <div class="col-md-4" data-aos="fade-right">
                <?php if($top2): ?>
                <div class="card-custom p-4 text-center">
                    <div class="position-relative d-inline-block mb-3">
                        <img src="<?php echo e($top2->photo_url); ?>" class="rounded-circle" width="100" height="100" style="object-fit: cover; border: 4px solid var(--border);">
                        <span class="position-absolute bottom-0 end-0 bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: 700;">2</span>
                    </div>
                    <h5 class="fw-bold mb-1"><?php echo e($top2->nama); ?></h5>
                    <div class="font-mono" style="font-size: 0.7rem; color: var(--text-muted); letter-spacing: 1px;"><?php echo e(strtoupper($top2->jurusan?->nama_jurusan ?? 'UMUM')); ?></div>
                    <div class="mt-2" style="color: var(--secondary);">
                        <i class="bi bi-star-fill"></i> <?php echo e(number_format($top2->rata_rata_nilai, 1)); ?>

                    </div>
                </div>
                <?php endif; ?>
            </div>

            
            <div class="col-md-4" data-aos="zoom-in">
                <?php if($top1): ?>
                <div class="card-custom p-5 text-center" style="background: var(--primary); color: white; border: none;">
                    <div class="position-relative d-inline-block mb-3">
                        <img src="<?php echo e($top1->photo_url); ?>" class="rounded-circle" width="120" height="120" style="object-fit: cover; border: 4px solid var(--secondary);">
                        <span class="position-absolute bottom-0 end-0 rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: 700; background: var(--secondary); color: var(--primary);">1</span>
                    </div>
                    <h4 class="fw-bold mb-1"><?php echo e($top1->nama); ?></h4>
                    <div class="font-mono" style="font-size: 0.75rem; letter-spacing: 2px; color: var(--secondary);"><?php echo e(strtoupper($top1->jurusan?->nama_jurusan ?? 'UMUM')); ?></div>
                    <div class="row g-3 mt-3">
                        <div class="col-6">
                            <div class="font-mono" style="font-size: 0.65rem; letter-spacing: 1px; opacity: 0.8;">RATING</div>
                            <div class="fw-bold fs-4"><?php echo e(number_format($top1->rata_rata_nilai, 1)); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="font-mono" style="font-size: 0.65rem; letter-spacing: 1px; opacity: 0.8;">ULASAN</div>
                            <div class="fw-bold fs-4"><?php echo e($top1->total_penilaian); ?></div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            
            <div class="col-md-4" data-aos="fade-left">
                <?php if($top3): ?>
                <div class="card-custom p-4 text-center">
                    <div class="position-relative d-inline-block mb-3">
                        <img src="<?php echo e($top3->photo_url); ?>" class="rounded-circle" width="100" height="100" style="object-fit: cover; border: 4px solid var(--border);">
                        <span class="position-absolute bottom-0 end-0 bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: 700;">3</span>
                    </div>
                    <h5 class="fw-bold mb-1"><?php echo e($top3->nama); ?></h5>
                    <div class="font-mono" style="font-size: 0.7rem; color: var(--text-muted); letter-spacing: 1px;"><?php echo e(strtoupper($top3->jurusan?->nama_jurusan ?? 'UMUM')); ?></div>
                    <div class="mt-2" style="color: var(--secondary);">
                        <i class="bi bi-star-fill"></i> <?php echo e(number_format($top3->rata_rata_nilai, 1)); ?>

                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        
        <ul class="nav nav-pills justify-content-center mb-4" data-aos="fade-up">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#normada" style="border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 600;">
                    Guru Normada
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="pill" data-bs-target="#produktif" style="border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 600;">
                    Guru Produktif
                </button>
            </li>
        </ul>

        
        <div class="tab-content" data-aos="fade-up">
            <?php $__currentLoopData = ['normada' => $normadaList, 'produktif' => $produktifList]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="tab-pane fade <?php echo e($key === 'normada' ? 'show active' : ''); ?>" id="<?php echo e($key); ?>">
                <div class="card-custom">
                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">RANKING</th>
                                    <th>NAMA GURU</th>
                                    <th>DEPARTEMEN</th>
                                    <th class="text-center">RATING</th>
                                    <th class="text-center">ULASAN</th>
                                    <th class="text-center" style="width: 140px;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td class="text-center">
                                        <?php if($index === 0): ?> <span class="fs-4">🥇</span>
                                        <?php elseif($index === 1): ?> <span class="fs-4">🥈</span>
                                        <?php elseif($index === 2): ?> <span class="fs-4">🥉</span>
                                        <?php else: ?> <span class="badge bg-light text-dark border">#<?php echo e($index + 1); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo e($g->photo_url); ?>" class="rounded-circle me-3" width="48" height="48" style="object-fit: cover;">
                                            <div>
                                                <strong><?php echo e($g->nama); ?></strong>
                                                <div class="text-muted small font-mono"><?php echo e($g->nip); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="font-mono" style="font-size: 0.75rem;"><?php echo e(strtoupper($g->jurusan?->nama_jurusan ?? 'Umum')); ?></td>
                                    <td class="text-center">
                                        <strong style="color: var(--secondary);">
                                            <i class="bi bi-star-fill"></i> <?php echo e(number_format($g->rata_rata_nilai, 1)); ?>

                                        </strong>
                                    </td>
                                    <td class="text-center"><?php echo e($g->total_penilaian); ?></td>
                                    <td class="text-center">
                                        <a href="<?php echo e(route('landing.guru.detail', $g->id)); ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i> Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada data guru pada kategori ini.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.landing', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADVAN\PROJEK\Laravel\GuruKuu\resources\views/landing/leaderboard.blade.php ENDPATH**/ ?>