<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Admin'); ?> - GuruKuu</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        :root { --primary:#003366; --primary-light:#004080; --secondary:#FFC107; --accent:#00A86B; --bg-light:#f5f7fa; --text-dark:#1a1a2e; --text-muted:#64748b; --border:#e2e8f0; }
        body { background-color:var(--bg-light); color:var(--text-dark); font-family:'Inter',sans-serif; }
        .sidebar { width:260px; height:100vh; position:fixed; left:0; top:0; background:white; border-right:1px solid var(--border); z-index:1000; overflow-y:auto; }
        .sidebar-brand { padding:1.5rem; font-size:1.5rem; font-weight:800; color:var(--primary); text-decoration:none; display:block; border-bottom:1px solid var(--border); }
        .sidebar-menu { padding:1rem 0; }
        .sidebar-link { display:flex; align-items:center; padding:0.75rem 1.5rem; color:var(--text-dark); text-decoration:none; transition:all 0.2s; cursor:pointer; }
        .sidebar-link:hover, .sidebar-link.active { background:var(--bg-light); color:var(--primary); border-right:3px solid var(--primary); }
        .sidebar-link i { margin-right:0.75rem; width:20px; text-align:center; }
        .sidebar-submenu { padding-left:3.25rem; font-size:0.9rem; }
        .sidebar-submenu .sidebar-link { padding:0.5rem 1.5rem; }
        .main-content { margin-left:260px; padding:2rem; min-height:100vh; }
        .page-header { margin-bottom:2rem; }
        .page-label { font-family:'JetBrains Mono',monospace; font-size:0.75rem; font-weight:600; color:var(--primary); letter-spacing:1px; text-transform:uppercase; }
        .page-title { font-size:1.75rem; font-weight:800; margin-bottom:0.25rem; }
        .page-subtitle { color:var(--text-muted); font-size:0.95rem; }
        .card-custom { background:white; border-radius:12px; border:1px solid var(--border); box-shadow:0 2px 8px rgba(0,0,0,0.04); }
        .table-custom th { font-weight:600; font-size:0.85rem; text-transform:uppercase; color:var(--text-muted); border-bottom:2px solid var(--border); }
        .table-custom td { vertical-align:middle; font-size:0.9rem; }
        .btn-primary-custom { background:var(--primary); color:white; border:none; }
        .btn-primary-custom:hover { background:var(--primary-light); color:white; }
        .btn-outline-custom { background:transparent; color:var(--primary); border:1px solid var(--primary); }
        .btn-outline-custom:hover { background:var(--primary); color:white; }
        .stat-card { background:white; border-radius:12px; padding:1.5rem; border:1px solid var(--border); box-shadow:0 2px 8px rgba(0,0,0,0.04); transition:transform 0.2s; }
        .stat-card:hover { transform:translateY(-5px); }
        .stat-card-label { font-size:0.85rem; color:var(--text-muted); text-transform:uppercase; font-weight:600; }
        .stat-card-value { font-size:2rem; font-weight:800; color:var(--text-dark); }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="<?php echo e(route('admin.dashboard')); ?>" class="sidebar-brand">GuruKuu Admin</a>
        <div class="sidebar-menu">
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.dashboard') ? 'active' : ''); ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            
            <div class="sidebar-link" data-bs-toggle="collapse" data-bs-target="#menuData">
                <i class="bi bi-database"></i> Data
                <i class="bi bi-chevron-down ms-auto" style="font-size:0.8rem;"></i>
            </div>
            <div class="collapse" id="menuData">
                <div class="sidebar-submenu">
                    <a href="<?php echo e(route('admin.guru.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.guru.*') ? 'active' : ''); ?>">Data Guru</a>
                    <a href="<?php echo e(route('admin.siswa.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.siswa.*') ? 'active' : ''); ?>">Data Siswa</a>
                    <a href="<?php echo e(route('admin.jurusan.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.jurusan.*') ? 'active' : ''); ?>">Data Jurusan</a>
                </div>
            </div>

            <div class="sidebar-link" data-bs-toggle="collapse" data-bs-target="#menuLaporan">
                <i class="bi bi-file-earmark-bar-graph"></i> Laporan & Feedback
                <i class="bi bi-chevron-down ms-auto" style="font-size:0.8rem;"></i>
            </div>
            <div class="collapse" id="menuLaporan">
                <div class="sidebar-submenu">
                    <a href="<?php echo e(route('admin.leaderboard.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.leaderboard.*') ? 'active' : ''); ?>">Leaderboard</a>
                    <a href="<?php echo e(route('admin.kritik-saran.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.kritik-saran.*') ? 'active' : ''); ?>">Kritik & Saran</a>
                    <a href="<?php echo e(route('admin.kontak.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.kontak.*') ? 'active' : ''); ?>">Pesan Masuk</a>
                </div>
            </div>

            <a href="<?php echo e(route('admin.pengaturan.index')); ?>" class="sidebar-link <?php echo e(request()->routeIs('admin.pengaturan.*') ? 'active' : ''); ?>">
                <i class="bi bi-gear"></i> Pengaturan
            </a>
            
            <div class="mt-4 px-3">
                <form action="<?php echo e(route('logout')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="main-content">
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show"><?php echo e(session('success')); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
        <?php echo $__env->yieldContent('content'); ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\Users\ADVAN\PROJEK\Laravel\GuruKuu\resources\views/layouts/admin.blade.php ENDPATH**/ ?>