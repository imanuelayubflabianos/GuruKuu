<?php $__env->startSection('title', 'Dashboard Guru'); ?>

<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div>
        <div class="page-label">DASHBOARD GURU</div>
        <h1 class="page-title">Selamat Datang, <?php echo e($guru->nama); ?>! 👋</h1>
        <p class="page-subtitle">Berikut adalah ringkasan performa, kelas yang Anda ampu, dan ulasan siswa pada periode <?php echo e($periodeAktif->nama_periode ?? 'Aktif'); ?>.</p>
    </div>
</div>


<div class="row g-4 mb-4">
    
    <div class="col-md-3">
        <div class="card-custom p-4 h-100" style="border-left: 4px solid var(--secondary);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small font-mono mb-1" style="letter-spacing: 1px;">RATA-RATA NILAI</div>
                    <h2 class="fw-bold mb-0" style="color: var(--primary);"><?php echo e(number_format($guru->rata_rata_nilai ?? 0, 2)); ?></h2>
                    <div class="text-warning mt-1">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <i class="bi bi-star<?php echo e($i <= round($guru->rata_rata_nilai ?? 0) ? '-fill' : ''); ?>"></i>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                    <i class="bi bi-star-fill text-primary fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-md-3">
        <div class="card-custom p-4 h-100" style="border-left: 4px solid var(--accent);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small font-mono mb-1" style="letter-spacing: 1px;">TOTAL PENILAIAN</div>
                    <h2 class="fw-bold mb-0"><?php echo e($guru->total_penilaian ?? 0); ?></h2>
                    <small class="text-muted">Total siswa yang menilai</small>
                </div>
                <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                    <i class="bi bi-people-fill text-success fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-md-3">
        <div class="card-custom p-4 h-100" style="border-left: 4px solid #6366f1;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small font-mono mb-1" style="letter-spacing: 1px;">KELAS DIAMPU</div>
                    <h2 class="fw-bold mb-0"><?php echo e($kelasList->count() ?? 0); ?></h2>
                    <small class="text-muted">Kelas aktif</small>
                </div>
                <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                    <i class="bi bi-book-fill text-info fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-md-3">
        <div class="card-custom p-4 h-100" style="border-left: 4px solid #ec4899;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="text-muted small font-mono mb-1" style="letter-spacing: 1px;">TOTAL ULASAN</div>
                    <h2 class="fw-bold mb-0"><?php echo e($ulasanTerbaru->count()); ?></h2>
                    <small class="text-muted">Kritik & saran masuk</small>
                </div>
                <div class="bg-danger bg-opacity-10 p-3 rounded-circle">
                    <i class="bi bi-chat-quote-fill text-danger fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    
    <div class="col-lg-8">
        <div class="card-custom p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-journal-bookmark-fill me-2" style="color: var(--primary);"></i>Kelas yang Diampu</h5>
            <?php if(isset($kelasList) && $kelasList->isNotEmpty()): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50">No</th>
                                <th>Nama Kelas</th>
                                <th>Mata Pelajaran</th>
                                <th class="text-center">Siswa Menilai</th>
                                <th class="text-center">Partisipasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $kelasList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $kelas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($index + 1); ?></td>
                                <td class="fw-bold">
                                    <?php echo e($kelas->nama_kelas); ?> 
                                    <span class="badge bg-light text-dark border">Tingkat <?php echo e($kelas->tingkat); ?></span>
                                </td>
                                <td><span class="badge bg-primary"><?php echo e($kelas->pivot->mata_pelajaran ?? 'Umum'); ?></span></td>
                                <td class="text-center"><?php echo e($kelas->sudah_menilai ?? 0); ?> / <?php echo e($kelas->jumlah_siswa ?? 0); ?></td>
                                <td class="text-center">
                                    <?php
                                        $p = $kelas->partisipasi ?? 0;
                                        $warna = $p >= 70 ? 'bg-success' : ($p >= 40 ? 'bg-warning' : 'bg-danger');
                                    ?>
                                    <div class="d-inline-flex align-items-center gap-2">
                                        <div class="progress" style="width: 80px; height: 8px;">
                                            <div class="progress-bar <?php echo e($warna); ?>" style="width: <?php echo e($p); ?>%;"></div>
                                        </div>
                                        <span class="small fw-bold"><?php echo e(number_format($p, 1)); ?>%</span>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    Belum ada data kelas yang diampu.
                </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="col-lg-4">
        <div class="card-custom p-4 text-center h-100">
            <img src="<?php echo e($guru->photo_url); ?>" alt="Foto Guru" class="rounded-circle mb-3 border border-3 border-white shadow-sm" width="100" height="100" style="object-fit: cover;">
            <h5 class="fw-bold mb-1"><?php echo e($guru->nama); ?></h5>
            <p class="text-muted small mb-2"><?php echo e($guru->jurusan?->nama_jurusan ?? 'Normada / Umum'); ?></p>
            <span class="badge bg-<?php echo e($guru->kategori === 'normada' ? 'info' : 'success'); ?> px-3 py-2 mb-3">
                Guru <?php echo e(ucfirst($guru->kategori)); ?>

            </span>
            <div class="pt-3 border-top text-start">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">NIP / NIY:</span>
                    <span class="fw-bold font-mono"><?php echo e($guru->nip); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Email:</span>
                    <span class="fw-bold"><?php echo e($guru->email ?? '-'); ?></span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted small">Telepon:</span>
                    <span class="fw-bold"><?php echo e($guru->phone ?? '-'); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-chat-heart-fill me-2 text-danger"></i>Ulasan & Saran dari Siswa
                </h5>
                <span class="badge bg-light text-dark border"><?php echo e($ulasanTerbaru->count()); ?> Ulasan</span>
            </div>

            <?php if($ulasanTerbaru->isNotEmpty()): ?>
                <div class="row g-3">
                    <?php $__currentLoopData = $ulasanTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-6">
                        <div class="p-3 rounded border h-100" style="background: var(--bg-light);">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-secondary">
                                    <i class="bi bi-building me-1"></i><?php echo e($review->kelas?->nama_kelas ?? 'Kelas Siswa'); ?>

                                </span>
                                <small class="text-muted"><?php echo e($review->created_at->format('d M Y, H:i')); ?></small>
                            </div>

                            <?php if($review->kritik): ?>
                                <div class="mb-2">
                                    <small class="text-danger fw-bold d-block mb-1"><i class="bi bi-chat-left-dots me-1"></i>Kritik / Catatan:</small>
                                    <p class="mb-0 small text-dark p-2 bg-white rounded border border-danger-subtle"><?php echo e($review->kritik); ?></p>
                                </div>
                            <?php endif; ?>

                            <?php if($review->saran): ?>
                                <div>
                                    <small class="text-success fw-bold d-block mb-1"><i class="bi bi-lightbulb me-1"></i>Saran & Harapan:</small>
                                    <p class="mb-0 small text-dark p-2 bg-white rounded border border-success-subtle"><?php echo e($review->saran); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-chat-square-text fs-1 d-block mb-2 text-secondary"></i>
                    Belum ada ulasan atau kritik dan saran yang masuk dari siswa untuk periode ini.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.guru', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ADVAN\PROJEK\Laravel\GuruKuu\resources\views/guru/dashboard.blade.php ENDPATH**/ ?>