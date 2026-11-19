<?php
/**
 * =============================================================================
 * GURUKUU DEVOPS TERMINAL — WEB DEPLOYMENT & MAINTENANCE CONSOLE
 * =============================================================================
 * SMK NEGERI 1 BANGSRI • Sistem Evaluasi & Akuntabilitas Kinerja Pendidik
 * 
 * Skrip utilitas mandiri berbasis web untuk otomasi deployment Laravel di VPS / cPanel Shared Hosting.
 * Mendukung Git Pull, Clean Sync, Rollback Commit, Database Migrate/Rollback/Fresh/Seed,
 * Cache Warmup/Clear, Storage Symlink & Permission Fix, serta Maintenance Mode.
 *
 * INSTRUKSI PENGGUNAAN:
 * 1. Buka berkas `.env` di root server GuruKuu.
 * 2. Tambahkan variabel: DEPLOY_KEY=kata-sandi-rahasia-anda
 * 3. Buka browser: https://gurukuu.smkn1bangsri.sch.id/deploy.php
 * 4. Masukkan sandi DEPLOY_KEY yang telah Anda tetapkan.
 * =============================================================================
 */

// 1. Header Keamanan Ketat & Anti-Indexing
header('X-Robots-Tag: noindex, nofollow, noarchive, nosnippet');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-XSS-Protection: 1; mode=block');

// 2. Konfigurasi Sistem
$CONFIG = [
    'app_name'           => 'GuruKuu',
    'app_title'          => 'DevOps Terminal',
    'app_subtitle'       => 'SMKN 1 BANGSRI • Automated Web Deployment & Server Maintenance',
    'footer_note'        => 'GuruKuu DevOps Terminal — Terisolasi secara mandiri untuk kelancaran deployment tanpa SSH.',
    
    // Tema Warna Profesional GuruKuu (Dark Mode Slate & Navy Amber)
    'theme_primary'      => '#0284C7', // Sky 600
    'theme_primary_dark' => '#0369A1', // Sky 700
    'theme_amber'        => '#F59E0B', // Amber 500
    'theme_dark_bg'      => '#0B1120', // Deep Slate 950
    'theme_card_bg'      => '#0F172A', // Slate 900
    'theme_card_inner'   => '#1E293B', // Slate 800
    'theme_border'       => '#334155', // Slate 700

    // Git Configuration
    'git_remote'         => 'origin',
    'git_default_branch' => 'main',
    
    // Keamanan & Akses
    'env_key_name'       => 'DEPLOY_KEY',
    'max_login_attempts' => 5,          // Batas percobaan login gagal sebelum IP dikunci
    'lockout_duration'   => 900,        // Durasi penguncian IP dalam detik (15 menit)
    'session_lifetime'   => 1800,       // Masa aktif sesi dalam detik (30 menit)
    'maintenance_secret' => 'gurukuu-bypass-' . date('Y'), // Token bypass maintenance

    // Folder Storage yang Otomatis Dibuat & Diberi Izin (0775)
    'storage_dirs'       => [
        'storage/app/public',
        'storage/framework/cache/data',
        'storage/framework/sessions',
        'storage/framework/views',
        'storage/logs',
        'storage/framework/cache/devops_locks',
        'public/storage',
        'public/uploads',
    ],
];

// 3. Sesi PHP untuk Otentikasi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 4. Deteksi Root Path Laravel
$laravelRoot = null;
if (file_exists(__DIR__ . '/../artisan')) {
    $laravelRoot = realpath(__DIR__ . '/..');
} elseif (file_exists(__DIR__ . '/artisan')) {
    $laravelRoot = __DIR__;
} elseif (file_exists(__DIR__ . '/../../artisan')) {
    $laravelRoot = realpath(__DIR__ . '/../..');
}

if ($laravelRoot) {
    chdir($laravelRoot);
}

// 5. Deteksi Binary PHP CLI (Mendukung cPanel EA-PHP 8.2 & 8.3)
$phpBinary = 'php';
$possiblePhp = [
    defined('PHP_BINARY') && PHP_BINARY ? PHP_BINARY : null,
    '/usr/local/bin/ea-php83',
    '/usr/local/bin/ea-php82',
    '/opt/cpanel/ea-php83/root/usr/bin/php',
    '/opt/cpanel/ea-php82/root/usr/bin/php',
    '/usr/bin/php8.3',
    '/usr/bin/php8.2',
    '/usr/bin/php',
];
foreach ($possiblePhp as $p) {
    if ($p && @file_exists($p) && @is_executable($p)) {
        $phpBinary = $p;
        break;
    }
}

// 6. Deteksi Binary Git CLI
$gitBinary = 'git';
$possibleGit = [
    '/usr/bin/git',
    '/usr/local/bin/git',
    '/usr/bin/local/git',
];
foreach ($possibleGit as $g) {
    if (@file_exists($g) && @is_executable($g)) {
        $gitBinary = $g;
        break;
    }
}

// 7. Pengaturan Batas Waktu Eksekusi (Mencegah timeout hosting saat git pull/migrasi)
@set_time_limit(300);
@ini_set('max_execution_time', '300');

// 8. Cek Ketersediaan Fungsi Eksekusi Shell
$disabledFunctions = array_map('trim', explode(',', (string)ini_get('disable_functions')));
$canExec = function_exists('shell_exec') && !in_array('shell_exec', $disabledFunctions, true);

// 9. Konfigurasi Git Safe Directory & Filemode di Latar Belakang (Mencegah "dubious ownership" & chmod issues di cPanel)
if ($canExec && $laravelRoot) {
    @shell_exec("{$gitBinary} config --global --add safe.directory " . escapeshellarg($laravelRoot) . " 2>&1");
    @shell_exec("{$gitBinary} config core.fileMode false 2>&1");
}

// 10. Pembacaan Kunci Rahasia dari .env (Strict: TIDAK ADA FALLBACK LEMAH)
$SECRET_KEY = null;
$envFile = ($laravelRoot ?: __DIR__ . '/..') . '/.env';
if (file_exists($envFile)) {
    $envLines = @file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    $targetVar = $CONFIG['env_key_name'] . '=';
    foreach ($envLines as $line) {
        $line = trim($line);
        if (str_starts_with($line, $targetVar)) {
            $parts = explode('=', $line, 2);
            if (!empty($parts[1])) {
                $SECRET_KEY = trim($parts[1], " \t\n\r\0\x0B\"'");
                break;
            }
        }
    }
}
$isKeyConfigured = !empty($SECRET_KEY);

// 11. Penanganan Logout
if (isset($_GET['logout'])) {
    unset($_SESSION['gurukuu_devops_auth'], $_SESSION['gurukuu_devops_csrf'], $_SESSION['gurukuu_devops_login_time']);
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
    header('Location: ' . strtok($_SERVER['REQUEST_URI'] ?? '/deploy.php', '?'));
    exit;
}

// 12. Deteksi IP Klien & File-based IP Lockout (Mencegah brute force)
$clientIp = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
if (strpos($clientIp, ',') !== false) {
    $clientIp = trim(explode(',', $clientIp)[0]);
}

$lockDir = ($laravelRoot ?: __DIR__ . '/..') . '/storage/framework/cache/devops_locks';
if (!is_dir($lockDir)) {
    @mkdir($lockDir, 0755, true);
}
$ipHash = md5($clientIp);
$ipLockFile = $lockDir . '/lock_' . $ipHash . '.json';
$ipData = ['attempts' => 0, 'locked_until' => 0];
if (file_exists($ipLockFile)) {
    $rawLock = @file_get_contents($ipLockFile);
    if ($rawLock) {
        $ipData = json_decode($rawLock, true) ?: $ipData;
    }
}

$attempts = max((int)($_SESSION['gurukuu_devops_attempts'] ?? 0), (int)($ipData['attempts'] ?? 0));
$lockedUntil = max((int)($_SESSION['gurukuu_devops_lockout_until'] ?? 0), (int)($ipData['locked_until'] ?? 0));

if ($lockedUntil > time()) {
    $remainingMinutes = ceil(($lockedUntil - time()) / 60);
    $isLockedOut = true;
    $loginError = "⛔ Akses IP ({$clientIp}) terkunci sementara akibat batas salah sandi. Silakan tunggu {$remainingMinutes} menit.";
} else {
    $isLockedOut = false;
    if ($lockedUntil > 0 && $lockedUntil <= time()) {
        unset($_SESSION['gurukuu_devops_attempts'], $_SESSION['gurukuu_devops_lockout_until']);
        $attempts = 0;
        if (file_exists($ipLockFile)) {
            @unlink($ipLockFile);
        }
    }
}

// 13. Pemrosesan Login via Form POST
if (!$isLockedOut && $isKeyConfigured && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['login_submit'])) {
    $submittedKey = (string)($_POST['key'] ?? '');
    if (!empty($submittedKey) && hash_equals($SECRET_KEY, $submittedKey)) {
        session_regenerate_id(true);
        $_SESSION['gurukuu_devops_auth'] = true;
        $_SESSION['gurukuu_devops_csrf'] = bin2hex(random_bytes(32));
        $_SESSION['gurukuu_devops_login_time'] = time();
        unset($_SESSION['gurukuu_devops_attempts'], $_SESSION['gurukuu_devops_lockout_until']);
        if (file_exists($ipLockFile)) {
            @unlink($ipLockFile);
        }
        header('Location: ' . strtok($_SERVER['REQUEST_URI'] ?? '/deploy.php', '?'));
        exit;
    } else {
        $attempts++;
        $_SESSION['gurukuu_devops_attempts'] = $attempts;
        $ipData['attempts'] = $attempts;
        $remaining = $CONFIG['max_login_attempts'] - $attempts;
        if ($remaining <= 0) {
            $lockExpiry = time() + $CONFIG['lockout_duration'];
            $_SESSION['gurukuu_devops_lockout_until'] = $lockExpiry;
            $ipData['locked_until'] = $lockExpiry;
            $loginError = '⛔ Terlalu banyak percobaan sandi salah. IP Anda terkunci selama 15 menit.';
            $isLockedOut = true;
        } else {
            $loginError = "⛔ Kata sandi tidak cocok. Sisa kesempatan untuk IP Anda: {$remaining} kali.";
        }
        @file_put_contents($ipLockFile, json_encode($ipData));
    }
}

// 14. Cek Status Otentikasi Sesi (Timeout 30 menit)
$isAuthenticated = false;
if (!empty($_SESSION['gurukuu_devops_auth'])) {
    if (isset($_SESSION['gurukuu_devops_login_time']) && (time() - $_SESSION['gurukuu_devops_login_time'] > $CONFIG['session_lifetime'])) {
        unset($_SESSION['gurukuu_devops_auth'], $_SESSION['gurukuu_devops_csrf'], $_SESSION['gurukuu_devops_login_time']);
        $isAuthenticated = false;
    } else {
        $_SESSION['gurukuu_devops_login_time'] = time();
        $isAuthenticated = true;
    }
}

// 15. Token CSRF Sesi
if ($isAuthenticated && empty($_SESSION['gurukuu_devops_csrf'])) {
    $_SESSION['gurukuu_devops_csrf'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['gurukuu_devops_csrf'] ?? '';

// 16. Telemetri Sistem & Repositori Git
$gitBranch       = $CONFIG['git_default_branch'];
$gitCommitHash   = 'N/A';
$gitCommitMsg    = 'N/A';
$gitCommitDate   = 'N/A';
$gitCommitAuthor = 'N/A';
$gitStatusClean  = true;
$gitUncommitted  = [];
$gitRecentLogs   = [];
$isDown          = false;
$storageLinked   = false;
$diskFreeGB      = null;
$laravelVersion  = '11.x';

if ($isAuthenticated && $laravelRoot && $canExec) {
    // Deteksi Branch Aktif
    $detectedBranch = trim((string)@shell_exec("{$gitBinary} branch --show-current 2>&1"));
    if ($detectedBranch && !str_starts_with($detectedBranch, 'fatal:')) {
        $gitBranch = $detectedBranch;
    }

    // Detail Commit Terakhir
    $logRaw = trim((string)@shell_exec("{$gitBinary} log -1 --pretty=format:\"%h|%an|%ar|%s\" 2>&1"));
    if ($logRaw && str_contains($logRaw, '|')) {
        $p = explode('|', $logRaw, 4);
        $gitCommitHash   = $p[0] ?? 'N/A';
        $gitCommitAuthor = $p[1] ?? 'N/A';
        $gitCommitDate   = $p[2] ?? 'N/A';
        $gitCommitMsg    = $p[3] ?? 'N/A';
    }

    // Status Working Tree (Abaikan izin fileMode Linux hosting dan sinkronkan .gitignore framework)
    @shell_exec("{$gitBinary} config core.fileMode false 2>&1");
    @shell_exec("{$gitBinary} checkout -- bootstrap/cache/.gitignore storage/ 2>&1");
    $statusRaw = trim((string)@shell_exec("{$gitBinary} status --porcelain 2>&1"));
    if (!empty($statusRaw) && !str_starts_with($statusRaw, 'fatal:')) {
        $gitStatusClean = false;
        $gitUncommitted = array_slice(explode("\n", $statusRaw), 0, 8);
    }

    // 6 Commit Terakhir
    $recentRaw = trim((string)@shell_exec("{$gitBinary} log -6 --pretty=format:\"%h|%an|%ar|%s\" 2>&1"));
    if (!empty($recentRaw) && !str_starts_with($recentRaw, 'fatal:')) {
        foreach (explode("\n", $recentRaw) as $item) {
            $p = explode('|', $item, 4);
            if (count($p) === 4) {
                $gitRecentLogs[] = [
                    'hash'   => $p[0],
                    'author' => $p[1],
                    'date'   => $p[2],
                    'msg'    => $p[3],
                ];
            }
        }
    }

    // Status Maintenance
    $isDown = file_exists($laravelRoot . '/storage/framework/down');

    // Status Storage Link
    $publicStorage = $laravelRoot . '/public/storage';
    $storageLinked = is_link($publicStorage) || (file_exists($publicStorage) && is_dir($publicStorage));

    // Sisa Ruang Disk
    $freeBytes = @disk_free_space($laravelRoot);
    if ($freeBytes !== false) {
        $diskFreeGB = round($freeBytes / 1024 / 1024 / 1024, 2);
    }

    // Versi Laravel
    $vRaw = trim((string)@shell_exec("{$phpBinary} artisan --version 2>&1"));
    if ($vRaw && str_starts_with($vRaw, 'Laravel Framework')) {
        $laravelVersion = trim(str_replace('Laravel Framework', '', $vRaw));
    }
}

// 17. Pemindaian Seeder Otomatis dari folder database/seeders/
$availableSeeders = [];
if ($laravelRoot && is_dir($laravelRoot . '/database/seeders')) {
    $seederFiles = glob($laravelRoot . '/database/seeders/*Seeder.php');
    if (!empty($seederFiles)) {
        foreach ($seederFiles as $sFile) {
            $cName = basename($sFile, '.php');
            $availableSeeders[$cName] = $cName;
        }
    }
}
if (empty($availableSeeders)) {
    $availableSeeders = ['DatabaseSeeder' => 'DatabaseSeeder'];
} else {
    if (isset($availableSeeders['DatabaseSeeder'])) {
        unset($availableSeeders['DatabaseSeeder']);
        $availableSeeders = ['DatabaseSeeder' => 'DatabaseSeeder (Semua Seeder Utama)'] + $availableSeeders;
    }
}

// 18. Pemrosesan Aksi Operasional (100% Whitelist Terproteksi)
$actionResult = null;
$actionTitle  = null;
$actionStatus = 'success';
$actionTime   = 0;

if ($isAuthenticated && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action'])) {
    $submittedCsrf = (string)($_POST['csrf_token'] ?? '');
    if (empty($csrfToken) || !hash_equals($csrfToken, $submittedCsrf)) {
        $actionTitle  = 'Keamanan Ditolak (Sesi Kedaluwarsa)';
        $actionResult = 'Aksi dibatalkan karena token CSRF sesi tidak cocok. Silakan refresh halaman.';
        $actionStatus = 'danger';
    } elseif (!$canExec) {
        $actionTitle  = 'Fungsi shell_exec Tidak Aktif';
        $actionResult = "Fungsi PHP shell_exec() dinonaktifkan di hosting ini.\nAktifkan shell_exec via cPanel (Select PHP Version -> Options -> hapus dari disable_functions).";
        $actionStatus = 'danger';
    } else {
        $startTime = microtime(true);
        $action = (string)$_POST['action'];
        $remote = escapeshellarg($CONFIG['git_remote']);
        $branch = escapeshellarg($gitBranch);

        switch ($action) {
            // A. GIT FAST PULL
            case 'git_pull':
                $actionTitle = "Git Fast Pull ({$CONFIG['git_remote']}/{$gitBranch})";
                $output = [];
                @shell_exec("{$gitBinary} config core.fileMode false 2>&1");
                @shell_exec("{$gitBinary} checkout -- bootstrap/cache/.gitignore storage/ 2>&1");
                $output[] = "$ {$gitBinary} pull {$CONFIG['git_remote']} {$gitBranch}";
                $output[] = (string)shell_exec("{$gitBinary} pull {$remote} {$branch} 2>&1");
                $output[] = "$ {$phpBinary} artisan optimize:clear";
                $output[] = (string)shell_exec("{$phpBinary} artisan optimize:clear 2>&1");
                $actionResult = implode("\n", array_map('trim', $output));
                break;

            // B. GIT FORCE RESET & CLEAN SYNC
            case 'git_force_sync':
                $actionTitle = "Git Force Reset & Clean Sync ({$CONFIG['git_remote']}/{$gitBranch})";
                $output = [];
                $output[] = "$ {$gitBinary} fetch {$CONFIG['git_remote']} {$gitBranch}";
                $output[] = (string)shell_exec("{$gitBinary} fetch {$remote} {$branch} 2>&1");
                $output[] = "$ {$gitBinary} checkout -f -B {$gitBranch} {$CONFIG['git_remote']}/{$gitBranch}";
                $output[] = (string)shell_exec("{$gitBinary} checkout -f -B {$branch} {$remote}/{$branch} 2>&1");
                $output[] = "$ {$gitBinary} reset --hard {$CONFIG['git_remote']}/{$gitBranch}";
                $output[] = (string)shell_exec("{$gitBinary} reset --hard {$remote}/{$branch} 2>&1");
                // Proteksi mutlak file .env, storage, uploads, dan build
                $output[] = "$ {$gitBinary} clean -fd -e .env -e storage -e public/storage -e public/uploads -e public/build";
                $output[] = (string)shell_exec("{$gitBinary} clean -fd -e .env -e storage -e public/storage -e public/uploads -e public/build 2>&1");
                $output[] = "$ {$phpBinary} artisan optimize:clear";
                $output[] = (string)shell_exec("{$phpBinary} artisan optimize:clear 2>&1");
                $actionResult = implode("\n\n", array_map('trim', $output));
                break;

            // C. GIT UNDO / ROLLBACK COMMIT
            case 'git_undo':
                $steps = max(1, min(5, (int)($_POST['undo_steps'] ?? 1)));
                $actionTitle = "Git Undo (Rollback $steps Commit ke Belakang)";
                $output = [];
                $prevCommit = trim((string)shell_exec("{$gitBinary} log -1 --oneline 2>&1"));
                $output[] = "[SEBELUM UNDO]: " . $prevCommit;
                $output[] = "$ {$gitBinary} reset --hard HEAD~{$steps}";
                $output[] = (string)shell_exec("{$gitBinary} reset --hard HEAD~{$steps} 2>&1");
                $newCommit = trim((string)shell_exec("{$gitBinary} log -1 --oneline 2>&1"));
                $output[] = "[SETELAH UNDO]: " . $newCommit;
                $output[] = "$ {$phpBinary} artisan optimize:clear";
                $output[] = (string)shell_exec("{$phpBinary} artisan optimize:clear 2>&1");
                $actionResult = implode("\n\n", array_map('trim', $output));
                break;

            // D. DATABASE MIGRATE & STATUS
            case 'db_migrate':
                $actionTitle = 'Database Migration (php artisan migrate --force)';
                $output = [];
                $output[] = "$ {$phpBinary} artisan migrate --force";
                $output[] = (string)shell_exec("{$phpBinary} artisan migrate --force 2>&1");
                $actionResult = implode("\n", array_map('trim', $output));
                break;

            case 'db_migrate_status':
                $actionTitle = 'Status Migrasi Basis Data';
                $output = [];
                $output[] = "$ {$phpBinary} artisan migrate:status";
                $output[] = (string)shell_exec("{$phpBinary} artisan migrate:status 2>&1");
                $actionResult = implode("\n", array_map('trim', $output));
                break;

            case 'db_migrate_rollback':
                $step = max(1, min(10, (int)($_POST['rollback_step'] ?? 1)));
                $actionTitle = "Rollback Migrasi Database ($step Batch)";
                $output = [];
                $output[] = "$ {$phpBinary} artisan migrate:rollback --step={$step} --force";
                $output[] = (string)shell_exec("{$phpBinary} artisan migrate:rollback --step={$step} --force 2>&1");
                $actionResult = implode("\n", array_map('trim', $output));
                break;

            // E. DATABASE FRESH (RESET TOTAL DENGAN KONFIRMASI)
            case 'db_fresh':
                $confirmPhrase = trim((string)($_POST['confirm_phrase'] ?? ''));
                if ($confirmPhrase !== 'RESET') {
                    $actionTitle  = 'Konfirmasi Gagal';
                    $actionResult = 'Penghapusan database dibatalkan karena kata konfirmasi tidak sesuai (harus mengetik RESET).';
                    $actionStatus = 'danger';
                    break;
                }
                $withSeed = isset($_POST['with_seed']) && $_POST['with_seed'] === '1';
                $actionTitle = $withSeed 
                    ? 'Reset & Buat Ulang Database + Seeder (migrate:fresh --seed)' 
                    : 'Reset & Buat Ulang Database (migrate:fresh)';
                $output = [];
                $cmd = "{$phpBinary} artisan migrate:fresh" . ($withSeed ? ' --seed' : '') . ' --force';
                $output[] = "$ " . $cmd;
                $output[] = (string)shell_exec($cmd . ' 2>&1');
                $output[] = "$ {$phpBinary} artisan optimize:clear";
                $output[] = (string)shell_exec("{$phpBinary} artisan optimize:clear 2>&1");
                $actionResult = implode("\n\n", array_map('trim', $output));
                break;

            // F. DATABASE SEEDER
            case 'db_seed':
                $seederClass = trim((string)($_POST['seeder_class'] ?? 'DatabaseSeeder'));
                if (!preg_match('/^[A-Za-z0-9_\\\\]+$/', $seederClass)) {
                    $seederClass = 'DatabaseSeeder';
                }
                $actionTitle = "Database Seeder ({$seederClass})";
                $output = [];
                $cmd = "{$phpBinary} artisan db:seed --class=" . escapeshellarg($seederClass) . ' --force';
                $output[] = "$ " . $cmd;
                $output[] = (string)shell_exec($cmd . ' 2>&1');
                $actionResult = implode("\n", array_map('trim', $output));
                break;

            // G. CACHE CLEAR & WARMUP
            case 'cache_clear':
                $actionTitle = 'Pembersihan Cache Aplikasi (optimize:clear)';
                $output = [];
                $output[] = "$ {$phpBinary} artisan optimize:clear";
                $output[] = (string)shell_exec("{$phpBinary} artisan optimize:clear 2>&1");
                $actionResult = implode("\n", array_map('trim', $output));
                break;

            case 'cache_optimize':
                $actionTitle = 'Optimasi & Cache Warmup Produksi (optimize)';
                $output = [];
                $output[] = "$ {$phpBinary} artisan optimize";
                $output[] = (string)shell_exec("{$phpBinary} artisan optimize 2>&1");
                $actionResult = implode("\n", array_map('trim', $output));
                break;

            // H. STORAGE SYMLINK & PERMISSION FIX
            case 'storage_fix_permissions':
                $actionTitle = 'Perbaikan Folder, Izin Upload & Storage Symlink';
                $output = [];

                foreach ($CONFIG['storage_dirs'] as $dir) {
                    $fullPath = $laravelRoot . '/' . $dir;
                    if (!file_exists($fullPath)) {
                        @mkdir($fullPath, 0775, true);
                        $output[] = "[FOLDER CREATED]: {$dir}";
                    }
                    @chmod($fullPath, 0775);
                }

                // Proteksi anti eksekusi script PHP di storage upload
                $securityHtaccess = "# SECURITY: BLOCK PHP SCRIPT EXECUTION IN STORAGE\n<IfModule mod_php.c>\n    php_flag engine off\n</IfModule>\n<IfModule mod_php7.c>\n    php_flag engine off\n</IfModule>\n<IfModule mod_php8.c>\n    php_flag engine off\n</IfModule>\n<FilesMatch \"(?i)\\.(php|phtml|php3|php4|php5|php7|php8|phar|inc|cgi|pl|py|sh|bash|exe|jsp|asp|aspx)$\">\n    Order Deny,Allow\n    Deny from all\n</FilesMatch>\nOptions -ExecCGI -Indexes\n";
                @file_put_contents($laravelRoot . '/storage/app/public/.htaccess', $securityHtaccess);
                if (is_dir($laravelRoot . '/public/storage')) {
                    @file_put_contents($laravelRoot . '/public/storage/.htaccess', $securityHtaccess);
                }
                $output[] = "[SECURITY]: Proteksi anti-eksekusi PHP (.htaccess) disematkan di folder storage.";

                $output[] = "$ {$phpBinary} artisan storage:link";
                $output[] = (string)shell_exec("{$phpBinary} artisan storage:link 2>&1");

                $output[] = "$ {$phpBinary} artisan optimize:clear";
                $output[] = (string)shell_exec("{$phpBinary} artisan optimize:clear 2>&1");

                $pubStorage = $laravelRoot . '/public/storage';
                $isWritable = is_writable($pubStorage);
                $output[] = "[STATUS]: public/storage writable = " . ($isWritable ? '✅ BISA DITULIS (Izin 0775 OK)' : '⚠️ BELUM BISA DITULIS (Cek file manager hosting)');

                $actionResult = implode("\n", array_map('trim', $output));
                break;

            // I. MAINTENANCE MODE TOGGLE
            case 'maintenance_toggle':
                if ($isDown) {
                    $actionTitle = 'Mengaktifkan Website (php artisan up)';
                    $output = [];
                    $output[] = "$ {$phpBinary} artisan up";
                    $output[] = (string)shell_exec("{$phpBinary} artisan up 2>&1");
                    $actionResult = implode("\n", array_map('trim', $output));
                } else {
                    $actionTitle = 'Mengaktifkan Mode Pemeliharaan (php artisan down)';
                    $secret = $CONFIG['maintenance_secret'];
                    $output = [];
                    $cmd = "{$phpBinary} artisan down --secret=" . escapeshellarg($secret);
                    $output[] = "$ " . $cmd;
                    $output[] = (string)shell_exec($cmd . ' 2>&1');
                    $output[] = "Bypass URL Administrator: /" . $secret;
                    $actionResult = implode("\n", array_map('trim', $output));
                }
                break;

            // J. COMPOSER INSTALL (OPSIONAL PRODUKSI)
            case 'composer_install':
                $actionTitle = 'Composer Install (--no-dev, optimize-autoloader)';
                $output = [];
                $composerCmd = 'composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction';
                $output[] = "$ " . $composerCmd;
                $output[] = (string)shell_exec($composerCmd . ' 2>&1');
                $output[] = "$ {$phpBinary} artisan optimize:clear";
                $output[] = (string)shell_exec("{$phpBinary} artisan optimize:clear 2>&1");
                $actionResult = implode("\n", array_map('trim', $output));
                break;

            default:
                $actionTitle  = 'Perintah Tidak Dikenal';
                $actionResult = 'Aksi yang dipilih tidak terdaftar dalam whitelist keamanan.';
                $actionStatus = 'danger';
                break;
        }

        $actionTime = round(microtime(true) - $startTime, 3);

        // Perbarui status telemetri setelah eksekusi
        if ($laravelRoot && $canExec) {
            $logRaw = trim((string)shell_exec("{$gitBinary} log -1 --pretty=format:\"%h|%an|%ar|%s\" 2>&1"));
            if ($logRaw && str_contains($logRaw, '|')) {
                $p = explode('|', $logRaw, 4);
                $gitCommitHash   = $p[0] ?? 'N/A';
                $gitCommitAuthor = $p[1] ?? 'N/A';
                $gitCommitDate   = $p[2] ?? 'N/A';
                $gitCommitMsg    = $p[3] ?? 'N/A';
            }
            $isDown = file_exists($laravelRoot . '/storage/framework/down');
            $storageLinked = is_link($laravelRoot . '/public/storage') || (file_exists($laravelRoot . '/public/storage') && is_dir($laravelRoot . '/public/storage'));
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <title><?php echo htmlspecialchars($CONFIG['app_name']); ?> <?php echo htmlspecialchars($CONFIG['app_title']); ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --primary: <?php echo htmlspecialchars($CONFIG['theme_primary']); ?>;
            --primary-dark: <?php echo htmlspecialchars($CONFIG['theme_primary_dark']); ?>;
            --amber: <?php echo htmlspecialchars($CONFIG['theme_amber']); ?>;
            --dark-bg: <?php echo htmlspecialchars($CONFIG['theme_dark_bg']); ?>;
            --card-bg: <?php echo htmlspecialchars($CONFIG['theme_card_bg']); ?>;
            --card-inner: <?php echo htmlspecialchars($CONFIG['theme_card_inner']); ?>;
            --border: <?php echo htmlspecialchars($CONFIG['theme_border']); ?>;
            --text-main: #F8FAFC;
            --text-muted: #94A3B8;
            --text-dim: #64748B;
            --emerald: #10B981;
            --rose: #F43F5E;
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--dark-bg);
            color: var(--text-main);
            min-height: 100vh;
            line-height: 1.5;
            padding: 1.5rem 1rem 3rem;
            background-image: 
                radial-gradient(circle at 50% 0%, rgba(2, 132, 199, 0.08), transparent 40%),
                linear-gradient(to right, rgba(255,255,255,0.015) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255,255,255,0.015) 1px, transparent 1px);
            background-size: 100% 100%, 32px 32px, 32px 32px;
        }

        .mono { font-family: 'JetBrains Mono', monospace; }
        .container { max-width: 1100px; margin: 0 auto; }

        /* Topbar Header */
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 1rem 1.4rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .brand-logo {
            width: 38px;
            height: 38px;
            border-radius: var(--radius-sm);
            background: linear-gradient(135deg, #0284C7 0%, #003366 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
            box-shadow: 0 2px 10px rgba(2, 132, 199, 0.35);
        }

        .brand-title {
            font-size: 1.15rem;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: -0.01em;
            line-height: 1.2;
        }

        .brand-title .brand-accent { color: var(--amber); }
        .brand-title .brand-terminal { color: var(--primary); font-weight: 700; margin-left: 4px; font-size: 0.95rem; }
        .brand-subtitle { font-size: 0.72rem; color: var(--text-muted); }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            flex-wrap: wrap;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.72rem;
            font-weight: 600;
            padding: 0.3rem 0.75rem;
            border-radius: 9999px;
            border: 1px solid transparent;
            letter-spacing: 0.02em;
        }

        .badge-emerald { background: rgba(16, 185, 129, 0.12); color: #34D399; border-color: rgba(16, 185, 129, 0.3); }
        .badge-amber   { background: rgba(245, 158, 11, 0.12); color: #FBBF24; border-color: rgba(245, 158, 11, 0.3); }
        .badge-rose    { background: rgba(244, 63, 94, 0.15); color: #FB7185; border-color: rgba(244, 63, 94, 0.3); }
        .badge-sky     { background: rgba(2, 132, 199, 0.12); color: #38BDF8; border-color: rgba(2, 132, 199, 0.3); }

        .pulse-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background-color: currentColor;
            box-shadow: 0 0 8px currentColor;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.82rem;
            font-weight: 600;
            padding: 0.55rem 1.05rem;
            border-radius: var(--radius-sm);
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
            white-space: nowrap;
        }

        .btn-primary { background: var(--primary); color: #FFFFFF; border-color: var(--primary-dark); }
        .btn-primary:hover { background: var(--primary-dark); box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35); }
        .btn-amber { background: #D97706; color: #FFFFFF; border-color: #B45309; }
        .btn-amber:hover { background: #B45309; box-shadow: 0 4px 14px rgba(217, 119, 6, 0.35); }
        .btn-danger { background: #E11D48; color: #FFFFFF; border-color: #BE123C; }
        .btn-danger:hover { background: #BE123C; box-shadow: 0 4px 14px rgba(225, 29, 72, 0.35); }
        .btn-outline { background: transparent; color: var(--text-muted); border-color: var(--border); }
        .btn-outline:hover { background: var(--card-inner); color: #FFFFFF; border-color: var(--text-dim); }

        /* Telemetry Grid */
        .telemetry-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .telemetry-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 1rem 1.2rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .telemetry-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 3px; height: 100%;
            background: var(--border);
        }

        .telemetry-card.card-primary::before { background: var(--primary); }
        .telemetry-card.card-amber::before { background: var(--amber); }
        .telemetry-card.card-emerald::before { background: var(--emerald); }
        .telemetry-card.card-rose::before { background: var(--rose); }

        .telemetry-label {
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: 0.35rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .telemetry-val {
            font-size: 1.15rem;
            font-weight: 700;
            color: #FFFFFF;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .telemetry-sub {
            font-size: 0.72rem;
            color: var(--text-muted);
            margin-top: 0.35rem;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
        }

        /* Console Output Box */
        .console-box {
            background: #020617;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0,0,0,0.4);
        }

        .console-header {
            background: #0F172A;
            border-bottom: 1px solid var(--border);
            padding: 0.65rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .console-dots {
            display: flex;
            gap: 0.4rem;
        }

        .console-dot {
            width: 10px; height: 10px; border-radius: 50%;
        }

        .dot-red { background: #EF4444; }
        .dot-yellow { background: #F59E0B; }
        .dot-green { background: #10B981; }

        .console-title {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .console-body {
            padding: 1.25rem;
            font-size: 0.82rem;
            line-height: 1.6;
            color: #E2E8F0;
            max-height: 380px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-all;
        }

        /* Sections Grid */
        .sections-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .panel-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }

        .panel-head {
            margin-bottom: 1.15rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }

        .panel-icon {
            width: 36px; height: 36px; border-radius: var(--radius-sm);
            background: var(--card-inner);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; flex-shrink: 0;
        }

        .icon-blue { color: var(--primary); }
        .icon-amber { color: var(--amber); }
        .icon-emerald { color: var(--emerald); }
        .icon-rose { color: var(--rose); }

        .panel-title { font-size: 0.98rem; font-weight: 700; color: #FFFFFF; }
        .panel-desc { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.15rem; line-height: 1.4; }

        .panel-actions {
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
        }

        /* Form Inputs */
        .form-control, .form-select {
            background: var(--card-inner);
            border: 1px solid var(--border);
            color: var(--text-main);
            padding: 0.55rem 0.85rem;
            border-radius: var(--radius-sm);
            font-size: 0.82rem;
            width: 100%;
            outline: none;
            transition: border-color 0.15s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
        }

        /* Modals */
        .modal-backdrop {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(4px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .modal-box {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            max-width: 500px;
            width: 100%;
            padding: 1.5rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
        }

        .modal-title { font-size: 1.1rem; font-weight: 700; color: #FFFFFF; margin-bottom: 0.5rem; }
        .modal-desc { font-size: 0.82rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem; }

        /* Login Card */
        .login-wrap {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .login-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            max-width: 440px;
            width: 100%;
            padding: 2.25rem 2rem;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }

        .login-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: rgba(2, 132, 199, 0.12);
            color: #38BDF8;
            border: 1px solid rgba(2, 132, 199, 0.25);
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        .login-title { font-size: 1.45rem; font-weight: 800; color: #FFFFFF; line-height: 1.2; }
        .login-title .accent { color: var(--amber); }
        .login-subtitle { font-size: 0.8rem; color: var(--text-muted); margin-top: 0.4rem; margin-bottom: 1.75rem; }

        .input-group { position: relative; margin-bottom: 1.25rem; }
        .input-toggle {
            position: absolute; right: 12px; top: 50%;
            transform: translateY(-50%);
            background: transparent; border: none;
            color: var(--text-muted); cursor: pointer;
            font-size: 1rem;
        }

        .alert {
            padding: 0.85rem 1rem;
            border-radius: var(--radius-sm);
            font-size: 0.82rem;
            margin-bottom: 1.25rem;
            line-height: 1.45;
        }

        .alert-danger {
            background: rgba(244, 63, 94, 0.15);
            color: #FDA4AF;
            border: 1px solid rgba(244, 63, 94, 0.3);
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.15);
            color: #FDE68A;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .footer {
            text-align: center;
            font-size: 0.75rem;
            color: var(--text-dim);
            margin-top: 2rem;
        }

        .footer a { color: var(--text-muted); text-decoration: none; }
        .footer a:hover { color: var(--primary); }
    </style>
</head>
<body>

<div class="container">

<?php if (!$isAuthenticated): ?>
    <!-- TAMPILAN LOGIN DEVOPS TERMINAL -->
    <div class="login-wrap">
        <div class="login-card">
            <div class="login-badge">
                <i class="bi bi-shield-lock-fill"></i> PROTECTED CONSOLE
            </div>
            <h1 class="login-title">Guru<span class="accent">Kuu</span> DevOps Terminal</h1>
            <p class="login-subtitle">SMK NEGERI 1 BANGSRI • Autentikasi deployment web & pemeliharaan server.</p>

            <?php if (!$isKeyConfigured): ?>
                <div class="alert alert-warning">
                    <strong class="d-block mb-1"><i class="bi bi-exclamation-triangle-fill"></i> DEPLOY_KEY Belum Diatur!</strong>
                    Variabel <code>DEPLOY_KEY</code> belum disetel pada berkas <code>.env</code> server.<br>
                    Demi keamanan server, tambahkan baris berikut di <code>.env</code> Anda:<br>
                    <code class="mono mt-2 d-block p-2 bg-black rounded" style="font-size: 0.75rem;">DEPLOY_KEY=kata-sandi-rahasia-anda</code>
                </div>
            <?php else: ?>
                <?php if (!empty($loginError)): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-x-circle-fill me-1"></i> <?php echo htmlspecialchars($loginError); ?>
                    </div>
                <?php endif; ?>

                <?php if (!$isLockedOut): ?>
                    <form method="POST" action="">
                        <input type="hidden" name="login_submit" value="1">
                        <div class="input-group">
                            <input type="password" id="deployKeyInput" name="key" class="form-control mono" placeholder="Masukkan Sandi DEPLOY_KEY..." required autofocus style="padding-right: 40px;">
                            <button type="button" class="input-toggle" onclick="togglePasswordVisibility()" title="Lihat Sandi">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        <button type="submit" class="btn btn-primary w-100" style="padding: 0.75rem; font-size: 0.9rem;">
                            <i class="bi bi-box-arrow-in-right"></i> Buka Terminal DevOps
                        </button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>

            <div class="mt-4 pt-3 border-top text-center" style="border-color: var(--border) !important;">
                <a href="/" class="small text-muted text-decoration-none d-inline-flex align-items-center gap-1">
                    <i class="bi bi-arrow-left"></i> Kembali ke Beranda GuruKuu
                </a>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- TAMPILAN DASHBOARD DEVOPS UTAMA -->

    <!-- Topbar -->
    <header class="topbar">
        <div class="brand-section">
            <div class="brand-logo">
                <i class="bi bi-terminal-fill"></i>
            </div>
            <div>
                <div class="brand-title">
                    Guru<span class="brand-accent">Kuu</span><span class="brand-terminal">DevOps</span>
                </div>
                <div class="brand-subtitle"><?php echo htmlspecialchars($CONFIG['app_subtitle']); ?></div>
            </div>
        </div>

        <div class="topbar-actions">
            <?php if ($isDown): ?>
                <span class="badge badge-rose"><span class="pulse-dot"></span> Maintenance Mode ON</span>
            <?php else: ?>
                <span class="badge badge-emerald"><span class="pulse-dot"></span> Web Live & Aktif</span>
            <?php endif; ?>

            <a href="/" target="_blank" class="btn btn-outline" title="Lihat Website Publik">
                <i class="bi bi-box-arrow-up-right"></i> Beranda Publik
            </a>

            <a href="?logout=1" class="btn btn-outline" title="Keluar dari Terminal">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </header>

    <!-- Telemetry Cards -->
    <div class="telemetry-grid">
        <div class="telemetry-card card-primary">
            <div class="telemetry-label">
                <span>Branch Git</span>
                <i class="bi bi-git"></i>
            </div>
            <div class="telemetry-val mono"><?php echo htmlspecialchars($gitBranch); ?></div>
            <div class="telemetry-sub">Remote: <?php echo htmlspecialchars($CONFIG['git_remote']); ?></div>
        </div>

        <div class="telemetry-card card-amber">
            <div class="telemetry-label">
                <span>Commit Terakhir</span>
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="telemetry-val mono">#<?php echo htmlspecialchars($gitCommitHash); ?></div>
            <div class="telemetry-sub" title="<?php echo htmlspecialchars($gitCommitMsg); ?>"><?php echo htmlspecialchars($gitCommitMsg); ?></div>
        </div>

        <div class="telemetry-card card-emerald">
            <div class="telemetry-label">
                <span>Ruang Disk Tersedia</span>
                <i class="bi bi-hdd-network"></i>
            </div>
            <div class="telemetry-val mono"><?php echo $diskFreeGB !== null ? $diskFreeGB . ' GB' : 'N/A'; ?></div>
            <div class="telemetry-sub">PHP <?php echo PHP_VERSION; ?> CLI</div>
        </div>

        <div class="telemetry-card <?php echo $storageLinked ? 'card-emerald' : 'card-rose'; ?>">
            <div class="telemetry-label">
                <span>Public Storage Link</span>
                <i class="bi bi-folder-symlink"></i>
            </div>
            <div class="telemetry-val"><?php echo $storageLinked ? 'Terkoneksi' : 'Terputus'; ?></div>
            <div class="telemetry-sub">public/storage: <?php echo $storageLinked ? 'OK' : 'Perlu Fix'; ?></div>
        </div>

        <div class="telemetry-card card-sky">
            <div class="telemetry-label">
                <span>Framework Engine</span>
                <i class="bi bi-cpu"></i>
            </div>
            <div class="telemetry-val">Laravel <?php echo htmlspecialchars($laravelVersion); ?></div>
            <div class="telemetry-sub"><?php echo PHP_OS; ?> • <?php echo $canExec ? 'Exec Ready' : 'Exec Disabled'; ?></div>
        </div>
    </div>

    <!-- Console Output Window (Jika ada aksi yang baru dijalankan) -->
    <?php if (!empty($actionResult)): ?>
        <div class="console-box">
            <div class="console-header">
                <div class="console-dots">
                    <div class="console-dot dot-red"></div>
                    <div class="console-dot dot-yellow"></div>
                    <div class="console-dot dot-green"></div>
                </div>
                <div class="console-title mono">
                    <i class="bi bi-terminal"></i>
                    <span>Output Log: <?php echo htmlspecialchars($actionTitle ?: 'Hasil Eksekusi'); ?></span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge <?php echo $actionStatus === 'danger' ? 'badge-rose' : 'badge-emerald'; ?>">
                        <?php echo $actionStatus === 'danger' ? 'Gagal' : 'Selesai (' . $actionTime . 's)'; ?>
                    </span>
                    <button type="button" class="btn btn-outline" style="padding: 0.2rem 0.5rem; font-size: 0.72rem;" onclick="copyConsoleOutput()">
                        <i class="bi bi-clipboard"></i> Salin Log
                    </button>
                </div>
            </div>
            <pre class="console-body mono" id="consoleOutput"><?php echo htmlspecialchars($actionResult); ?></pre>
        </div>
    <?php endif; ?>

    <!-- Working Tree Warning jika ada perubahan uncommitted -->
    <?php if (!$gitStatusClean): ?>
        <div class="alert alert-warning d-flex align-items-start gap-2 mb-4">
            <i class="bi bi-exclamation-diamond-fill fs-5 mt-1"></i>
            <div class="w-100">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <strong>Peringatan Working Tree Tidak Bersih:</strong>
                    <button type="button" class="btn btn-sm btn-amber" onclick="openModal('modalForceSync')">
                        <i class="bi bi-arrow-clockwise"></i> Bersihkan & Sync Sekarang
                    </button>
                </div>
                <p class="mb-1" style="font-size: 0.78rem;">Terdapat berkas yang dimodifikasi langsung di server tanpa di-commit. Disarankan menggunakan "Git Force Reset & Sync" jika Anda ingin menimpa server sesuai commit GitHub terbaru.</p>
                <div class="mono small bg-black bg-opacity-25 p-2 rounded" style="font-size: 0.72rem;">
                    <?php foreach ($gitUncommitted as $line): ?>
                        <div><?php echo htmlspecialchars($line); ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Action Sections Grid -->
    <div class="sections-grid">
        <!-- 1. GIT DEPLOYMENT OPERATIONS -->
        <div class="panel-card">
            <div>
                <div class="panel-head">
                    <div class="panel-icon icon-blue"><i class="bi bi-cloud-arrow-down-fill"></i></div>
                    <div>
                        <h2 class="panel-title">Otomasi Git Deployment</h2>
                        <p class="panel-desc">Sinkronisasi kode terbaru GuruKuu dari repositori GitHub.</p>
                    </div>
                </div>
            </div>
            <div class="panel-actions">
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                    <input type="hidden" name="action" value="git_pull">
                    <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Jalankan Git Fast Pull dari branch <?php echo htmlspecialchars($gitBranch); ?>?')">
                        <i class="bi bi-arrow-repeat"></i> Git Fast Pull (Cepat)
                    </button>
                </form>

                <button type="button" class="btn btn-outline w-100" onclick="openModal('modalForceSync')">
                    <i class="bi bi-arrow-clockwise"></i> Git Force Reset & Clean Sync
                </button>

                <button type="button" class="btn btn-outline w-100" onclick="openModal('modalUndoCommit')">
                    <i class="bi bi-arrow-counterclockwise"></i> Git Undo / Rollback Commit
                </button>
            </div>
        </div>

        <!-- 2. DATABASE OPERATIONS -->
        <div class="panel-card">
            <div>
                <div class="panel-head">
                    <div class="panel-icon icon-amber"><i class="bi bi-database-fill-gear"></i></div>
                    <div>
                        <h2 class="panel-title">Manajemen Database & Migrasi</h2>
                        <p class="panel-desc">Menjalankan migrasi skema tabel, seeder, atau rollback.</p>
                    </div>
                </div>
            </div>
            <div class="panel-actions">
                <div class="d-flex gap-2">
                    <form method="POST" action="" class="flex-grow-1">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                        <input type="hidden" name="action" value="db_migrate">
                        <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Jalankan migrasi database baru?')">
                            <i class="bi bi-database-add"></i> Migrate --force
                        </button>
                    </form>

                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                        <input type="hidden" name="action" value="db_migrate_status">
                        <button type="submit" class="btn btn-outline" title="Cek Status Migrasi">
                            <i class="bi bi-list-check"></i> Status
                        </button>
                    </form>
                </div>

                <!-- Seeder Runner -->
                <form method="POST" action="" class="d-flex gap-2">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                    <input type="hidden" name="action" value="db_seed">
                    <select name="seeder_class" class="form-select font-mono" style="font-size: 0.75rem;">
                        <?php foreach ($availableSeeders as $sVal => $sLabel): ?>
                            <option value="<?php echo htmlspecialchars($sVal); ?>"><?php echo htmlspecialchars($sLabel); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-outline flex-shrink-0" onclick="return confirm('Jalankan seeder yang dipilih?')">
                        <i class="bi bi-play-fill"></i> Seed
                    </button>
                </form>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline w-100" onclick="openModal('modalRollback')">
                        <i class="bi bi-skip-backward-fill"></i> Rollback Migrasi
                    </button>
                    <button type="button" class="btn btn-danger flex-shrink-0" onclick="openModal('modalFresh')">
                        <i class="bi bi-trash3-fill"></i> Reset DB
                    </button>
                </div>
            </div>
        </div>

        <!-- 3. CACHE & STORAGE OPTIMIZATION -->
        <div class="panel-card">
            <div>
                <div class="panel-head">
                    <div class="panel-icon icon-emerald"><i class="bi bi-lightning-charge-fill"></i></div>
                    <div>
                        <h2 class="panel-title">Cache & Storage Optimization</h2>
                        <p class="panel-desc">Optimasi performa routing/config & perbaikan storage symlink.</p>
                    </div>
                </div>
            </div>
            <div class="panel-actions">
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                    <input type="hidden" name="action" value="cache_optimize">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-speedometer2"></i> Optimize Warmup (Produksi)
                    </button>
                </form>

                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                    <input type="hidden" name="action" value="cache_clear">
                    <button type="submit" class="btn btn-outline w-100">
                        <i class="bi bi-stars"></i> Bersihkan Seluruh Cache (optimize:clear)
                    </button>
                </form>

                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                    <input type="hidden" name="action" value="storage_fix_permissions">
                    <button type="submit" class="btn btn-outline w-100">
                        <i class="bi bi-folder-check"></i> Fix Storage Symlink & Permission 0775
                    </button>
                </form>
            </div>
        </div>

        <!-- 4. MAINTENANCE MODE & SERVER HEALTH -->
        <div class="panel-card">
            <div>
                <div class="panel-head">
                    <div class="panel-icon icon-rose"><i class="bi bi-cone-striped"></i></div>
                    <div>
                        <h2 class="panel-title">Mode Pemeliharaan (Maintenance)</h2>
                        <p class="panel-desc">Aktifkan halaman pemeliharaan sementara saat pembaruan besar.</p>
                    </div>
                </div>
            </div>
            <div class="panel-actions">
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                    <input type="hidden" name="action" value="maintenance_toggle">
                    <?php if ($isDown): ?>
                        <button type="submit" class="btn btn-primary w-100" style="background: #10B981; border-color: #059669;">
                            <i class="bi bi-toggle-on"></i> Matikan Mode Pemeliharaan (Live Website)
                        </button>
                    <?php else: ?>
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Aktifkan mode pemeliharaan? Pengunjung umum akan melihat halaman 503 sementara.')">
                            <i class="bi bi-toggle-off"></i> Aktifkan Maintenance Mode
                        </button>
                    <?php endif; ?>
                </form>

                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                    <input type="hidden" name="action" value="composer_install">
                    <button type="submit" class="btn btn-outline w-100" onclick="return confirm('Jalankan composer install --no-dev? (Pastikan koneksi hosting stabil)')">
                        <i class="bi bi-box-seam"></i> Composer Install (--no-dev)
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- 5. HISTORI 6 COMMIT TERAKHIR -->
    <?php if (!empty($gitRecentLogs)): ?>
        <div class="panel-card mb-4" style="padding: 1.25rem;">
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <h3 class="panel-title" style="font-size: 0.92rem;"><i class="bi bi-clock-history me-1 text-primary"></i> 6 Commit Terakhir Repositori GitHub</h3>
                <span class="badge badge-sky mono"><?php echo htmlspecialchars($gitBranch); ?></span>
            </div>
            <div class="d-flex flex-column gap-2">
                <?php foreach ($gitRecentLogs as $idx => $commit): ?>
                    <div class="p-2.5 rounded d-flex align-items-center justify-content-between flex-wrap gap-2" style="background: var(--card-inner); font-size: 0.78rem;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge <?php echo $idx === 0 ? 'badge-amber' : 'badge-sky'; ?> mono">#<?php echo htmlspecialchars($commit['hash']); ?></span>
                            <span class="fw-semibold text-white"><?php echo htmlspecialchars($commit['msg']); ?></span>
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-person me-1"></i><?php echo htmlspecialchars($commit['author']); ?> • <i class="bi bi-calendar3 me-1"></i><?php echo htmlspecialchars($commit['date']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

<?php endif; ?>

    <footer class="footer">
        <div><?php echo htmlspecialchars($CONFIG['footer_note']); ?></div>
        <div class="mt-1">SMK Negeri 1 Bangsri Jepara • Powered by GuruKuu DevOps Engine</div>
    </footer>

</div>

<!-- MODAL KONFIRMASI: GIT FORCE RESET & SYNC -->
<div class="modal-backdrop" id="modalForceSync">
    <div class="modal-box">
        <h3 class="modal-title text-amber"><i class="bi bi-exclamation-triangle-fill text-amber me-1"></i> Konfirmasi Force Clean Sync</h3>
        <p class="modal-desc">
            Aksi ini akan menjalankan <code>git fetch</code>, <code>git reset --hard</code>, dan <code>git clean -fd</code>.<br>
            Seluruh berkas lokal yang tidak di-commit akan ditimpa bersih sesuai repositori GitHub.<br>
            <em class="text-emerald d-block mt-2">Berkas <code>.env</code>, folder <code>storage/</code>, dan berkas upload tetap terlindungi 100%.</em>
        </p>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
            <input type="hidden" name="action" value="git_force_sync">
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalForceSync')">Batal</button>
                <button type="submit" class="btn btn-amber">Ya, Jalankan Clean Sync</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL KONFIRMASI: GIT UNDO COMMIT -->
<div class="modal-backdrop" id="modalUndoCommit">
    <div class="modal-box">
        <h3 class="modal-title"><i class="bi bi-arrow-counterclockwise text-primary me-1"></i> Rollback Commit (Undo)</h3>
        <p class="modal-desc">Kembalikan kode server ke beberapa commit sebelumnya jika commit terakhir bermasalah.</p>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
            <input type="hidden" name="action" value="git_undo">
            <div class="mb-3">
                <label class="small text-muted d-block mb-1">Jumlah commit yang di-rollback (HEAD~N):</label>
                <select name="undo_steps" class="form-select font-mono">
                    <option value="1">1 Commit ke Belakang (HEAD~1)</option>
                    <option value="2">2 Commit ke Belakang (HEAD~2)</option>
                    <option value="3">3 Commit ke Belakang (HEAD~3)</option>
                    <option value="5">5 Commit ke Belakang (HEAD~5)</option>
                </select>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalUndoCommit')">Batal</button>
                <button type="submit" class="btn btn-primary">Rollback Commit</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL KONFIRMASI: ROLLBACK MIGRASI DATABASE -->
<div class="modal-backdrop" id="modalRollback">
    <div class="modal-box">
        <h3 class="modal-title"><i class="bi bi-skip-backward-fill text-amber me-1"></i> Rollback Batch Migrasi</h3>
        <p class="modal-desc">Menjalankan <code>php artisan migrate:rollback</code> untuk membatalkan batch migrasi tabel terakhir.</p>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
            <input type="hidden" name="action" value="db_migrate_rollback">
            <div class="mb-3">
                <label class="small text-muted d-block mb-1">Jumlah langkah (step):</label>
                <select name="rollback_step" class="form-select font-mono">
                    <option value="1">1 Batch Terakhir</option>
                    <option value="2">2 Batch Terakhir</option>
                    <option value="3">3 Batch Terakhir</option>
                </select>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalRollback')">Batal</button>
                <button type="submit" class="btn btn-amber">Rollback Migrasi</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL KONFIRMASI: RESET TOTAL DATABASE (MIGRATE:FRESH) -->
<div class="modal-backdrop" id="modalFresh">
    <div class="modal-box" style="border-color: rgba(244, 63, 94, 0.4);">
        <h3 class="modal-title text-rose"><i class="bi bi-shield-slash-fill text-rose me-1"></i> BAHAYA: Reset Total Database</h3>
        <p class="modal-desc">
            Aksi <code>migrate:fresh</code> akan <strong>MENGHAPUS SELURUH TABEL & DATA</strong> di database, lalu membuat ulang dari awal.<br>
            Data evaluasi, akun, dan penilaian yang ada akan hilang permanen.
        </p>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
            <input type="hidden" name="action" value="db_fresh">
            <div class="mb-3">
                <label class="small text-muted d-block mb-1">Ketik kata <strong>RESET</strong> untuk konfirmasi:</label>
                <input type="text" name="confirm_phrase" class="form-control font-mono" placeholder="Ketik RESET" required autocomplete="off">
            </div>
            <div class="mb-3">
                <label class="small d-flex align-items-center gap-2 text-white">
                    <input type="checkbox" name="with_seed" value="1" checked>
                    <span>Otomatis jalankan seeder setelah reset (<code>--seed</code>)</span>
                </label>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalFresh')">Batal</button>
                <button type="submit" class="btn btn-danger">Hapus & Reset Database</button>
            </div>
        </form>
    </div>
</div>

<script>
    function togglePasswordVisibility() {
        const input = document.getElementById('deployKeyInput');
        const icon = document.getElementById('eyeIcon');
        if (!input) return;
        if (input.type === 'password') {
            input.type = 'text';
            if (icon) icon.className = 'bi bi-eye-slash';
        } else {
            input.type = 'password';
            if (icon) icon.className = 'bi bi-eye';
        }
    }

    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.style.display = 'flex';
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) modal.style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal-backdrop')) {
            event.target.style.display = 'none';
        }
    }

    function copyConsoleOutput() {
        const consoleEl = document.getElementById('consoleOutput');
        if (!consoleEl) return;
        navigator.clipboard.writeText(consoleEl.innerText).then(() => {
            alert('Log konsol berhasil disalin ke clipboard!');
        }).catch(err => {
            console.error('Gagal menyalin log:', err);
        });
    }
</script>

</body>
</html>
