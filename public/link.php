<?php
/**
 * GuruKuu - Storage Link & Hosting Diagnostic Utility
 * File ini membantu membuat symlink storage dan mengatasi masalah upload di hosting/SSH.
 */

header('Content-Type: text/html; charset=utf-8');

$baseDir = dirname(__DIR__);
$targetFolder = $baseDir . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public';
$linkFolder = __DIR__ . DIRECTORY_SEPARATOR . 'storage';

$uploadsFolder = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';
$uploadsLogo = $uploadsFolder . DIRECTORY_SEPARATOR . 'logo';
$uploadsHero = $uploadsFolder . DIRECTORY_SEPARATOR . 'hero';

// Pastikan folder storage & uploads dibuat jika belum ada
$foldersToEnsure = [
    $targetFolder,
    $baseDir . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'sessions',
    $baseDir . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'views',
    $baseDir . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'cache',
    $uploadsFolder,
    $uploadsLogo,
    $uploadsHero,
];

foreach ($foldersToEnsure as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
}

// Tindakan aksi dari user
$msg = '';
$msgType = 'info';

if (isset($_POST['action'])) {
    if ($_POST['action'] === 'create_symlink') {
        if (file_exists($linkFolder) || is_link($linkFolder)) {
            if (is_link($linkFolder)) {
                @unlink($linkFolder);
            }
        }

        if (!function_exists('symlink')) {
            $msg = 'Fungsi symlink() dinonaktifkan oleh penyedia hosting di php.ini. Silakan gunakan opsi Salin/Sync Folder di bawah ini, atau gunakan route fallback yang sudah otomatis disediakan di GuruKuu.';
            $msgType = 'warning';
        } else {
            try {
                if (@symlink($targetFolder, $linkFolder)) {
                    $msg = 'Symlink berhasil dibuat! Folder public/storage sekarang terhubung ke storage/app/public.';
                    $msgType = 'success';
                } else {
                    $err = error_get_last();
                    $msg = 'Gagal membuat symlink: ' . ($err['message'] ?? 'Periksa permission folder.') . '. Silakan gunakan opsi Salin/Sync Folder di bawah.';
                    $msgType = 'danger';
                }
            } catch (\Throwable $e) {
                $msg = 'Exception saat membuat symlink: ' . $e->getMessage();
                $msgType = 'danger';
            }
        }
    } elseif ($_POST['action'] === 'sync_storage') {
        function copyFolderRecursive($src, $dst) {
            $dir = opendir($src);
            if (!is_dir($dst)) {
                @mkdir($dst, 0775, true);
            }
            while (false !== ($file = readdir($dir))) {
                if (($file != '.') && ($file != '..')) {
                    if (is_dir($src . '/' . $file)) {
                        copyFolderRecursive($src . '/' . $file, $dst . '/' . $file);
                    } else {
                        @copy($src . '/' . $file, $dst . '/' . $file);
                    }
                }
            }
            closedir($dir);
        }

        try {
            if (is_link($linkFolder)) {
                @unlink($linkFolder);
            }
            if (!is_dir($linkFolder)) {
                @mkdir($linkFolder, 0775, true);
            }
            copyFolderRecursive($targetFolder, $linkFolder);
            $msg = 'Folder storage berhasil disinkronkan secara fisik ke public/storage! Semua file dan thumbnail dapat diakses.';
            $msgType = 'success';
        } catch (\Throwable $e) {
            $msg = 'Gagal menyalin folder: ' . $e->getMessage();
            $msgType = 'danger';
        }
    }
}

$symlinkSupported = function_exists('symlink');
$isLinkExists = file_exists($linkFolder) || is_link($linkFolder);
$isLinkValid = is_link($linkFolder) && @readlink($linkFolder) !== false;
$isWritableStorage = is_writable($targetFolder);
$isWritableUploads = is_writable($uploadsFolder);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GuruKuu - Diagnostik Storage & Symlink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f1f5f9; font-family: system-ui, -apple-system, sans-serif; padding: 2rem 1rem; }
        .card { border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); }
        .status-badge { font-size: 0.85rem; padding: 0.35rem 0.75rem; border-radius: 50px; }
        code { background: #e2e8f0; color: #0f172a; padding: 0.2rem 0.4rem; border-radius: 6px; font-size: 0.85em; }
    </style>
</head>
<body>
<div class="container" style="max-width: 720px;">
    <div class="card p-4 p-md-5 bg-white">
        <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 1.5rem;">
                <i class="bi bi-hdd-network"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-0">Diagnostik Storage & Symlink</h4>
                <div class="text-muted small">GuruKuu Hosting & Server Utility</div>
            </div>
        </div>

        <?php if ($msg): ?>
            <div class="alert alert-<?= $msgType ?> d-flex align-items-center gap-2 mb-4">
                <i class="bi bi-info-circle-fill fs-5"></i>
                <div><?= $msg ?></div>
            </div>
        <?php endif; ?>

        <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-shield-check me-1"></i> STATUS SERVER & PERMISSION</h6>
        <ul class="list-group mb-4 shadow-sm">
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>Fungsi PHP <code>symlink()</code></span>
                <?php if ($symlinkSupported): ?>
                    <span class="badge bg-success status-badge"><i class="bi bi-check-circle"></i> Aktif</span>
                <?php else: ?>
                    <span class="badge bg-danger status-badge"><i class="bi bi-x-circle"></i> Dinonaktifkan oleh Hosting</span>
                <?php endif; ?>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>Status Folder <code>public/storage</code></span>
                <?php if ($isLinkValid): ?>
                    <span class="badge bg-success status-badge"><i class="bi bi-link-45deg"></i> Symlink Valid</span>
                <?php elseif ($isLinkExists): ?>
                    <span class="badge bg-info status-badge"><i class="bi bi-folder-check"></i> Folder Fisik Ada</span>
                <?php else: ?>
                    <span class="badge bg-warning text-dark status-badge"><i class="bi bi-exclamation-triangle"></i> Belum Dibuat</span>
                <?php endif; ?>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>Izin Tulis <code>storage/app/public</code></span>
                <?php if ($isWritableStorage): ?>
                    <span class="badge bg-success status-badge"><i class="bi bi-check-circle"></i> Writable</span>
                <?php else: ?>
                    <span class="badge bg-danger status-badge"><i class="bi bi-lock-fill"></i> Read-Only (Perlu chmod 775)</span>
                <?php endif; ?>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>Izin Tulis <code>public/uploads</code></span>
                <?php if ($isWritableUploads): ?>
                    <span class="badge bg-success status-badge"><i class="bi bi-check-circle"></i> Writable</span>
                <?php else: ?>
                    <span class="badge bg-warning text-dark status-badge"><i class="bi bi-exclamation-triangle"></i> Read-Only (Perlu chmod 775)</span>
                <?php endif; ?>
            </li>
        </ul>

        <h6 class="fw-bold text-secondary mb-3"><i class="bi bi-tools me-1"></i> TINDAKAN PERBAIKAN CEPAT</h6>
        <div class="d-flex flex-wrap gap-2 mb-4">
            <form method="POST" class="d-inline">
                <input type="hidden" name="action" value="create_symlink">
                <button type="submit" class="btn btn-primary px-3 py-2 fw-semibold">
                    <i class="bi bi-link-45deg me-1"></i> Buat / Perbarui Symlink
                </button>
            </form>

            <form method="POST" class="d-inline">
                <input type="hidden" name="action" value="sync_storage">
                <button type="submit" class="btn btn-outline-secondary px-3 py-2 fw-semibold" title="Gunakan ini jika hosting melarang symlink">
                    <i class="bi bi-copy me-1"></i> Sinkronisasi Folder (Jika Symlink Dilarang)
                </button>
            </form>
        </div>

        <div class="card bg-light p-3 border-0 rounded-3">
            <h6 class="fw-bold text-dark small mb-2"><i class="bi bi-terminal me-1"></i> Perintah Terminal SSH (Jika memiliki akses SSH):</h6>
            <pre class="bg-dark text-white p-3 rounded-2 small mb-2 font-monospace" style="overflow-x: auto;">
# 1. Masuk ke direktori proyek GuruKuu
cd /path/ke/projek/GuruKuu

# 2. Berikan izin tulis ke folder storage, cache, dan uploads
chmod -R 775 storage bootstrap/cache public/uploads

# 3. Buat symlink storage resmi Laravel
php artisan storage:link</pre>
            <div class="small text-muted">
                <strong>Catatan:</strong> GuruKuu kini juga dilengkapi Route Fallback otomatis di Laravel sehingga gambar tetap dapat dibuka meskipun symlink tidak diizinkan oleh server hosting.
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="/" class="btn btn-link text-decoration-none text-muted small">
                <i class="bi bi-arrow-left"></i> Kembali ke Website
            </a>
        </div>
    </div>
</div>
</body>
</html>