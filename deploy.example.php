<?php
/**
 * =============================================================================
 * LARAVEL DEVOPS & WEB DEPLOYMENT TERMINAL
 * =============================================================================
 * Skrip utilitas mandiri berbasis web untuk otomasi deployment Laravel di VPS / Shared Hosting (cPanel).
 * Mendukung Git Pull, Force Reset, Rollback Commit, Database Migrate/Rollback/Fresh/Seed,
 * Cache Warmup/Clear, Storage Symlink & Permission Fix, serta Maintenance Mode.
 *
 * INSTRUKSI PENGGUNAAN:
 * 1. Letakkan berkas ini di dalam folder `public/` proyek Laravel Anda (misal: public/deploy.php).
 * 2. Tambahkan `DEPLOY_KEY=kata-sandi-rahasia-anda` pada berkas `.env` di root Laravel.
 * 3. Buka browser: https://domain-anda.com/deploy.php
 * 4. Masukkan sandi yang telah Anda atur.
 * =============================================================================
 */

// Kirim header keamanan ketat
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-XSS-Protection: 1; mode=block');

// =============================================================================
// KONFIGURASI SISTEM (SESUAIKAN DENGAN KEBUTUHAN ANDA)
// =============================================================================
$CONFIG = [
    // Identitas Aplikasi & Tampilan
    'app_name'           => 'DEVOPS TERMINAL',
    'app_subtitle'       => 'Laravel Automated Deployment & Server Maintenance',
    'footer_note'        => 'Laravel Web DevOps Terminal — Dikonfigurasi untuk kemudahan deployment tanpa SSH.',
    
    // Tema Warna (Hex Code)
    'theme_primary'      => '#2563EB', // Warna utama (Biru Modern). Ganti misal: #DC2626 (Merah), #059669 (Hijau), #7C3AED (Ungu)
    'theme_hover'        => '#1D4ED8', // Warna hover tombol utama
    'theme_dark_bg'      => '#09090C', // Warna latar belakang gelap
    'theme_card_bg'      => '#121217', // Warna latar kartu / panel
    'theme_card_inner'   => '#18181F', // Warna elemen dalam kartu
    'theme_border'       => '#27272A', // Warna garis tepi

    // Git Repository Configuration
    'git_remote'         => 'origin',  // Remote git bawaan
    'git_default_branch' => 'main',    // Branch utama default jika deteksi otomatis kosong ('main' atau 'master')
    
    // Keamanan & Akses
    'env_key_name'       => 'DEPLOY_KEY', // Variabel di .env yang digunakan untuk sandi
    'fallback_secret'    => 'ganti_dengan_sandi_rahasia_anda_123', // Digunakan HANYA jika DEPLOY_KEY belum diisi di .env
    'max_login_attempts' => 5,          // Batas percobaan login gagal sebelum IP dikunci
    'lockout_duration'   => 900,        // Durasi penguncian IP dalam detik (900 detik = 15 menit)
    'session_lifetime'   => 1800,       // Masa aktif sesi login dalam detik (1800 detik = 30 menit)
    'maintenance_secret' => 'deploy-bypass-' . date('Y'), // Token bypass saat web dalam mode maintenance

    // Folder Storage yang Otomatis Dibuat & Diberi Izin (0775)
    'storage_dirs'       => [
        'storage/app/public',
        'storage/app/private/livewire-tmp',
        'storage/framework/cache/data',
        'storage/framework/sessions',
        'storage/framework/views',
        'storage/logs',
        'public/storage',
        // Tambahkan folder khusus proyek Anda di bawah ini jika diperlukan:
        // 'public/storage/uploads',
        // 'public/storage/images',
    ],
];

// =============================================================================
// LOGIKA SISTEM (UMUMNYA TIDAK PERLU DIUBAH KECUALI INGIN KUSTOMISASI LANJUT)
// =============================================================================

// 1. Sesi PHP untuk Otentikasi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Deteksi Root Path Laravel
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

// 3. Deteksi Binary PHP
$phpBinary = 'php';
if (defined('PHP_BINARY') && PHP_BINARY && is_executable(PHP_BINARY)) {
    $phpBinary = PHP_BINARY;
}

// 4. Pembacaan Kunci Rahasia dari .env
$SECRET_KEY = null;
$envFile = ($laravelRoot ? $laravelRoot : __DIR__ . '/..') . '/.env';
if (file_exists($envFile)) {
    $envLines = @file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    $targetVar = $CONFIG['env_key_name'] . '=';
    foreach ($envLines as $line) {
        $line = trim($line);
        if (str_starts_with($line, $targetVar) || str_starts_with($line, 'UPDATE_KEY=')) {
            $parts = explode('=', $line, 2);
            if (!empty($parts[1])) {
                $SECRET_KEY = trim($parts[1], " \t\n\r\0\x0B\"'");
                break;
            }
        }
    }
}

// Fallback jika belum diatur di .env
if (empty($SECRET_KEY)) {
    $isKeyConfigured = false;
    $SECRET_KEY = $CONFIG['fallback_secret'];
} else {
    $isKeyConfigured = true;
}

// 5. Penanganan Logout
if (isset($_GET['logout'])) {
    unset($_SESSION['deployer_auth'], $_SESSION['deployer_csrf'], $_SESSION['deployer_login_time']);
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
    header('Location: ' . strtok($_SERVER['REQUEST_URI'] ?? '/deploy.php', '?'));
    exit;
}

// 6. Deteksi IP Klien & File-based IP Lockout (Mencegah brute force)
$clientIp = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
if (strpos($clientIp, ',') !== false) {
    $clientIp = trim(explode(',', $clientIp)[0]);
}

$lockDir = ($laravelRoot ? $laravelRoot : __DIR__ . '/..') . '/storage/framework/cache/deploy_locks';
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

$attempts = max((int)($_SESSION['deployer_attempts'] ?? 0), (int)($ipData['attempts'] ?? 0));
$lockedUntil = max((int)($_SESSION['deployer_lockout_until'] ?? 0), (int)($ipData['locked_until'] ?? 0));

if ($lockedUntil > time()) {
    $remainingMinutes = ceil(($lockedUntil - time()) / 60);
    $isLockedOut = true;
    $loginError = "⛔ Akses IP ({$clientIp}) terkunci sementara akibat terlalu banyak percobaan sandi salah. Coba lagi dalam {$remainingMinutes} menit.";
} else {
    $isLockedOut = false;
    if ($lockedUntil > 0 && $lockedUntil <= time()) {
        unset($_SESSION['deployer_attempts'], $_SESSION['deployer_lockout_until']);
        $attempts = 0;
        if (file_exists($ipLockFile)) {
            @unlink($ipLockFile);
        }
    }
}

// Deteksi upaya passing kunci via URL query parameter (dicegah demi privasi)
$urlParamAttempt = isset($_GET['key']) || isset($_GET['password']) || isset($_GET['token']) || isset($_GET['auth']);

// 7. Pemrosesan Login via Form POST
if (!$isLockedOut && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['login_submit'])) {
    $submittedKey = (string)($_POST['key'] ?? '');
    if (!empty($submittedKey) && !empty($SECRET_KEY) && hash_equals($SECRET_KEY, $submittedKey)) {
        session_regenerate_id(true);
        $_SESSION['deployer_auth'] = true;
        $_SESSION['deployer_csrf'] = bin2hex(random_bytes(32));
        $_SESSION['deployer_login_time'] = time();
        unset($_SESSION['deployer_attempts'], $_SESSION['deployer_lockout_until']);
        if (file_exists($ipLockFile)) {
            @unlink($ipLockFile);
        }
        header('Location: ' . strtok($_SERVER['REQUEST_URI'] ?? '/deploy.php', '?'));
        exit;
    } else {
        $attempts++;
        $_SESSION['deployer_attempts'] = $attempts;
        $ipData['attempts'] = $attempts;
        $remaining = $CONFIG['max_login_attempts'] - $attempts;
        if ($remaining <= 0) {
            $lockExpiry = time() + $CONFIG['lockout_duration'];
            $_SESSION['deployer_lockout_until'] = $lockExpiry;
            $ipData['locked_until'] = $lockExpiry;
            $loginError = '⛔ Terlalu banyak percobaan sandi salah. Akses IP Anda terkunci sementara.';
            $isLockedOut = true;
        } else {
            $loginError = "⛔ Kata sandi tidak cocok. Sisa kesempatan untuk IP Anda: {$remaining} kali.";
        }
        @file_put_contents($ipLockFile, json_encode($ipData));
    }
}

// 8. Cek Status Otentikasi Sesi (Timeout sesuai config)
$isAuthenticated = false;
if (!empty($_SESSION['deployer_auth'])) {
    if (isset($_SESSION['deployer_login_time']) && (time() - $_SESSION['deployer_login_time'] > $CONFIG['session_lifetime'])) {
        unset($_SESSION['deployer_auth'], $_SESSION['deployer_csrf'], $_SESSION['deployer_login_time']);
        $isAuthenticated = false;
    } else {
        $_SESSION['deployer_login_time'] = time();
        $isAuthenticated = true;
    }
}

// Token CSRF untuk aksi POST
if ($isAuthenticated && empty($_SESSION['deployer_csrf'])) {
    $_SESSION['deployer_csrf'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['deployer_csrf'] ?? '';

// Cek ketersediaan fungsi shell_exec
$canExec = function_exists('shell_exec') && !in_array('shell_exec', array_map('trim', explode(',', ini_get('disable_functions'))));

// 9. Telemetri Sistem & Repositori Git
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

if ($isAuthenticated && $laravelRoot && $canExec) {
    // Deteksi Branch Aktif
    $detectedBranch = trim((string)@shell_exec('git branch --show-current 2>&1'));
    if ($detectedBranch && !str_starts_with($detectedBranch, 'fatal:')) {
        $gitBranch = $detectedBranch;
    }

    // Detail Commit Terakhir
    $logRaw = trim((string)@shell_exec('git log -1 --pretty=format:"%h|%an|%ar|%s" 2>&1'));
    if ($logRaw && str_contains($logRaw, '|')) {
        $p = explode('|', $logRaw, 4);
        $gitCommitHash   = $p[0] ?? 'N/A';
        $gitCommitAuthor = $p[1] ?? 'N/A';
        $gitCommitDate   = $p[2] ?? 'N/A';
        $gitCommitMsg    = $p[3] ?? 'N/A';
    }

    // Status Working Tree
    $statusRaw = trim((string)@shell_exec('git status --porcelain 2>&1'));
    if (!empty($statusRaw) && !str_starts_with($statusRaw, 'fatal:')) {
        $gitStatusClean = false;
        $gitUncommitted = array_slice(explode("\n", $statusRaw), 0, 8);
    }

    // 6 Commit Terakhir
    $recentRaw = trim((string)@shell_exec('git log -6 --pretty=format:"%h|%an|%ar|%s" 2>&1'));
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
}

// 10. Pemindaian Seeder Secara Otomatis dari folder database/seeders/
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
    // Pastikan DatabaseSeeder ada di posisi paling atas
    if (isset($availableSeeders['DatabaseSeeder'])) {
        unset($availableSeeders['DatabaseSeeder']);
        $availableSeeders = ['DatabaseSeeder' => 'DatabaseSeeder (Semua Seeder Utama)'] + $availableSeeders;
    }
}

// 11. Pemrosesan Aksi Operasional
$actionResult = null;
$actionTitle  = null;
$actionStatus = 'success';
$actionTime   = 0;

if ($isAuthenticated && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action'])) {
    $submittedCsrf = (string)($_POST['csrf_token'] ?? '');
    if (empty($csrfToken) || !hash_equals($csrfToken, $submittedCsrf)) {
        $actionTitle  = 'Keamanan Ditolak (Sesi Kedaluwarsa)';
        $actionResult = 'Aksi dibatalkan demi keamanan karena token CSRF sesi tidak valid atau telah kedaluwarsa. Silakan refresh halaman.';
        $actionStatus = 'danger';
    } elseif (!$canExec) {
        $actionTitle  = 'Fungsi shell_exec Tidak Aktif';
        $actionResult = 'Fungsi PHP shell_exec() dinonaktifkan pada hosting/server ini. Aktifkan shell_exec melalui cPanel (PHP Selector -> disable_functions) untuk menjalankan perintah deployment.';
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
                $output[] = "$ git pull {$CONFIG['git_remote']} {$gitBranch}";
                $output[] = (string)shell_exec("git pull {$remote} {$branch} 2>&1");
                $output[] = "$ php artisan optimize:clear";
                $output[] = (string)shell_exec($phpBinary . ' artisan optimize:clear 2>&1');
                $actionResult = implode("\n", array_map('trim', $output));
                break;

            // B. GIT FORCE RESET & SYNC
            case 'git_force_sync':
                $actionTitle = "Git Force Reset & Clean Sync ({$CONFIG['git_remote']}/{$gitBranch})";
                $output = [];
                $output[] = "$ git fetch {$CONFIG['git_remote']} {$gitBranch}";
                $output[] = (string)shell_exec("git fetch {$remote} {$branch} 2>&1");
                $output[] = "$ git checkout -f -B {$gitBranch} {$CONFIG['git_remote']}/{$gitBranch}";
                $output[] = (string)shell_exec("git checkout -f -B {$branch} {$remote}/{$branch} 2>&1");
                $output[] = "$ git reset --hard {$CONFIG['git_remote']}/{$gitBranch}";
                $output[] = (string)shell_exec("git reset --hard {$remote}/{$branch} 2>&1");
                $output[] = "$ git clean -fd -e .env -e public/build -e public/storage -e storage";
                $output[] = (string)shell_exec("git clean -fd -e .env -e public/build -e public/storage -e storage 2>&1");
                $output[] = "$ php artisan optimize:clear";
                $output[] = (string)shell_exec($phpBinary . ' artisan optimize:clear 2>&1');
                $actionResult = implode("\n\n", array_map('trim', $output));
                break;

            // C. GIT UNDO / ROLLBACK COMMIT
            case 'git_undo':
                $steps = max(1, min(5, (int)($_POST['undo_steps'] ?? 1)));
                $actionTitle = "Git Undo (Rollback $steps Commit ke Belakang)";
                $output = [];
                $prevCommit = trim((string)shell_exec('git log -1 --oneline 2>&1'));
                $output[] = "[SEBELUM UNDO]: " . $prevCommit;
                $output[] = "$ git reset --hard HEAD~{$steps}";
                $output[] = (string)shell_exec("git reset --hard HEAD~{$steps} 2>&1");
                $newCommit = trim((string)shell_exec('git log -1 --oneline 2>&1'));
                $output[] = "[SETELAH UNDO]: " . $newCommit;
                $output[] = "$ php artisan optimize:clear";
                $output[] = (string)shell_exec($phpBinary . ' artisan optimize:clear 2>&1');
                $actionResult = implode("\n\n", array_map('trim', $output));
                break;

            // D. DATABASE MIGRATE & STATUS
            case 'db_migrate':
                $actionTitle = 'Database Migration (php artisan migrate --force)';
                $output = [];
                $output[] = "$ php artisan migrate --force";
                $output[] = (string)shell_exec($phpBinary . ' artisan migrate --force 2>&1');
                $actionResult = implode("\n", array_map('trim', $output));
                break;

            case 'db_migrate_status':
                $actionTitle = 'Status Migrasi Basis Data';
                $output = [];
                $output[] = "$ php artisan migrate:status";
                $output[] = (string)shell_exec($phpBinary . ' artisan migrate:status 2>&1');
                $actionResult = implode("\n", array_map('trim', $output));
                break;

            case 'db_migrate_rollback':
                $step = max(1, min(10, (int)($_POST['rollback_step'] ?? 1)));
                $actionTitle = "Rollback Migrasi Database ($step Batch)";
                $output = [];
                $output[] = "$ php artisan migrate:rollback --step={$step} --force";
                $output[] = (string)shell_exec($phpBinary . " artisan migrate:rollback --step={$step} --force 2>&1");
                $actionResult = implode("\n", array_map('trim', $output));
                break;

            // E. DATABASE FRESH (RESET TOTAL)
            case 'db_fresh':
                $withSeed = isset($_POST['with_seed']) && $_POST['with_seed'] === '1';
                $actionTitle = $withSeed 
                    ? 'Reset & Buat Ulang Database + Seeder (migrate:fresh --seed)' 
                    : 'Reset & Buat Ulang Database (migrate:fresh)';
                $output = [];
                $cmd = $phpBinary . ' artisan migrate:fresh' . ($withSeed ? ' --seed' : '') . ' --force';
                $output[] = "$ " . $cmd;
                $output[] = (string)shell_exec($cmd . ' 2>&1');
                $output[] = "$ php artisan optimize:clear";
                $output[] = (string)shell_exec($phpBinary . ' artisan optimize:clear 2>&1');
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
                $cmd = $phpBinary . ' artisan db:seed --class=' . escapeshellarg($seederClass) . ' --force';
                $output[] = "$ " . $cmd;
                $output[] = (string)shell_exec($cmd . ' 2>&1');
                $actionResult = implode("\n", array_map('trim', $output));
                break;

            // G. CACHE CLEAR & WARMUP
            case 'cache_clear':
                $actionTitle = 'Pembersihan Cache Aplikasi (optimize:clear)';
                $output = [];
                $output[] = "$ php artisan optimize:clear";
                $output[] = (string)shell_exec($phpBinary . ' artisan optimize:clear 2>&1');
                $actionResult = implode("\n", array_map('trim', $output));
                break;

            case 'cache_optimize':
                $actionTitle = 'Optimasi & Cache Warmup Produksi (optimize)';
                $output = [];
                $output[] = "$ php artisan optimize";
                $output[] = (string)shell_exec($phpBinary . ' artisan optimize 2>&1');
                $actionResult = implode("\n", array_map('trim', $output));
                break;

            // H. STORAGE SYMLINK, FOLDER & PERMISSION FIX
            case 'storage_fix_permissions':
                $actionTitle = 'Perbaikan Folder, Izin Upload & Storage Symlink';
                $output = [];

                // 1. Buat direktori-direktori penting
                foreach ($CONFIG['storage_dirs'] as $dir) {
                    $fullPath = $laravelRoot . '/' . $dir;
                    if (!file_exists($fullPath)) {
                        @mkdir($fullPath, 0775, true);
                        $output[] = "[FOLDER CREATED]: {$dir}";
                    }
                    @chmod($fullPath, 0775);
                }

                // 2. Pasang proteksi anti eksekusi script PHP di dalam folder public/storage
                $securityHtaccess = "# SECURITY: BLOCK PHP SCRIPT EXECUTION IN STORAGE\n<IfModule mod_php.c>\n    php_flag engine off\n</IfModule>\n<IfModule mod_php7.c>\n    php_flag engine off\n</IfModule>\n<IfModule mod_php8.c>\n    php_flag engine off\n</IfModule>\n<FilesMatch \"(?i)\\.(php|phtml|php3|php4|php5|php7|php8|phar|inc|cgi|pl|py|sh|bash|exe|jsp|asp|aspx)$\">\n    Order Deny,Allow\n    Deny from all\n</FilesMatch>\nOptions -ExecCGI -Indexes\n";
                @file_put_contents($laravelRoot . '/storage/app/public/.htaccess', $securityHtaccess);
                if (is_dir($laravelRoot . '/public/storage')) {
                    @file_put_contents($laravelRoot . '/public/storage/.htaccess', $securityHtaccess);
                }
                $output[] = "[SECURITY]: Proteksi anti-eksekusi PHP (.htaccess) dipasang di storage.";

                // 3. Buat symlink storage
                $output[] = "$ php artisan storage:link";
                $output[] = (string)shell_exec($phpBinary . ' artisan storage:link 2>&1');

                // 4. Bersihkan cache
                $output[] = "$ php artisan optimize:clear";
                $output[] = (string)shell_exec($phpBinary . ' artisan optimize:clear 2>&1');

                // 5. Verifikasi izin tulis
                $pubStorage = $laravelRoot . '/public/storage';
                $isWritable = is_writable($pubStorage);
                $output[] = "[STATUS]: public/storage writable = " . ($isWritable ? '✅ BISA DITULIS (Izin OK)' : '⚠️ BELUM BISA DITULIS (Cek file manager / ubah ke permission 775)');

                $actionResult = implode("\n", array_map('trim', $output));
                break;

            // I. MAINTENANCE MODE TOGGLE
            case 'maintenance_toggle':
                if ($isDown) {
                    $actionTitle = 'Mengaktifkan Website (php artisan up)';
                    $output = [];
                    $output[] = "$ php artisan up";
                    $output[] = (string)shell_exec($phpBinary . ' artisan up 2>&1');
                    $actionResult = implode("\n", array_map('trim', $output));
                } else {
                    $actionTitle = 'Mematikan Website Sementara (php artisan down)';
                    $secret = $CONFIG['maintenance_secret'];
                    $output = [];
                    $cmd = $phpBinary . ' artisan down --secret=' . escapeshellarg($secret) . ' --render="errors::503"';
                    $output[] = "$ " . $cmd;
                    $output[] = (string)shell_exec($cmd . ' 2>&1');
                    $output[] = "Bypass URL: /" . $secret;
                    $actionResult = implode("\n", array_map('trim', $output));
                }
                break;

            // J. COMPOSER INSTALL (JIKA DIDUKUNG)
            case 'composer_install':
                $actionTitle = 'Composer Install (no-dev, optimize-autoloader)';
                $output = [];
                $composerCmd = 'composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction';
                $output[] = "$ " . $composerCmd;
                $output[] = (string)shell_exec($composerCmd . ' 2>&1');
                $output[] = "$ php artisan optimize:clear";
                $output[] = (string)shell_exec($phpBinary . ' artisan optimize:clear 2>&1');
                $actionResult = implode("\n", array_map('trim', $output));
                break;

            default:
                $actionTitle  = 'Perintah Tidak Dikenal';
                $actionResult = 'Aksi yang dipilih tidak valid.';
                $actionStatus = 'danger';
                break;
        }

        $actionTime = round(microtime(true) - $startTime, 3);

        // Perbarui status telemetri setelah eksekusi
        if ($laravelRoot && $canExec) {
            $logRaw = trim((string)shell_exec('git log -1 --pretty=format:"%h|%an|%ar|%s" 2>&1'));
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
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo htmlspecialchars($CONFIG['app_name']); ?> — Deploy Terminal</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: <?php echo htmlspecialchars($CONFIG['theme_primary']); ?>;
            --primary-hover: <?php echo htmlspecialchars($CONFIG['theme_hover']); ?>;
            --dark-bg: <?php echo htmlspecialchars($CONFIG['theme_dark_bg']); ?>;
            --card-bg: <?php echo htmlspecialchars($CONFIG['theme_card_bg']); ?>;
            --card-inner: <?php echo htmlspecialchars($CONFIG['theme_card_inner']); ?>;
            --border: <?php echo htmlspecialchars($CONFIG['theme_border']); ?>;
            --text-main: #F4F4F5;
            --text-muted: #A1A1AA;
            --text-dark: #71717A;
            --color-emerald: #10B981;
            --color-amber: #F59E0B;
            --color-sky: #0EA5E9;
            --color-rose: #F43F5E;
            --radius-sm: 6px;
            --radius-md: 8px;
            --radius-lg: 12px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--dark-bg);
            color: var(--text-main);
            min-height: 100vh;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            background-image: 
                radial-gradient(circle at 50% 0%, rgba(37, 99, 235, 0.08), transparent 45%),
                linear-gradient(to right, rgba(255,255,255,0.015) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255,255,255,0.015) 1px, transparent 1px);
            background-size: 100% 100%, 32px 32px, 32px 32px;
            padding: 1.5rem 1rem 3rem;
        }

        .mono {
            font-family: 'JetBrains Mono', monospace;
        }

        .container {
            max-width: 1080px;
            margin: 0 auto;
        }

        /* Top Bar */
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 0.95rem 1.25rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .brand-badge {
            width: 8px;
            height: 30px;
            background: var(--primary);
            border-radius: 3px;
        }

        .brand-title {
            font-size: 1.05rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #FFFFFF;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .brand-title span {
            color: var(--primary);
        }

        .brand-subtitle {
            font-size: 0.72rem;
            color: var(--text-muted);
            letter-spacing: 0.04em;
        }

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
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            border: 1px solid transparent;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .badge-emerald {
            background: rgba(16, 185, 129, 0.12);
            color: #34D399;
            border-color: rgba(16, 185, 129, 0.25);
        }

        .badge-amber {
            background: rgba(245, 158, 11, 0.12);
            color: #FBBF24;
            border-color: rgba(245, 158, 11, 0.25);
        }

        .badge-rose {
            background: rgba(244, 63, 94, 0.15);
            color: #FB7185;
            border-color: rgba(244, 63, 94, 0.3);
        }

        .badge-sky {
            background: rgba(14, 165, 233, 0.12);
            color: #38BDF8;
            border-color: rgba(14, 165, 233, 0.25);
        }

        .pulse-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: currentColor;
            box-shadow: 0 0 8px currentColor;
        }

        /* Tombol & Link */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.82rem;
            font-weight: 600;
            padding: 0.55rem 1rem;
            border-radius: var(--radius-sm);
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
            white-space: nowrap;
        }

        .btn-primary {
            background: var(--primary);
            color: #FFFFFF;
            border-color: var(--primary-hover);
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
        }

        .btn-outline {
            background: transparent;
            color: var(--text-muted);
            border-color: var(--border);
        }

        .btn-outline:hover {
            background: var(--card-inner);
            color: #FFFFFF;
            border-color: rgba(255, 255, 255, 0.2);
        }

        .btn-danger-outline {
            background: transparent;
            color: #FB7185;
            border-color: rgba(244, 63, 94, 0.3);
        }

        .btn-danger-outline:hover {
            background: rgba(244, 63, 94, 0.12);
            color: #FFFFFF;
            border-color: var(--color-rose);
        }

        .btn-amber-outline {
            background: transparent;
            color: #FBBF24;
            border-color: rgba(245, 158, 11, 0.3);
        }

        .btn-amber-outline:hover {
            background: rgba(245, 158, 11, 0.12);
            color: #FFFFFF;
            border-color: var(--color-amber);
        }

        .btn-sky-outline {
            background: transparent;
            color: #38BDF8;
            border-color: rgba(14, 165, 233, 0.3);
        }

        .btn-sky-outline:hover {
            background: rgba(14, 165, 233, 0.12);
            color: #FFFFFF;
            border-color: var(--color-sky);
        }

        /* Telemetri Grid */
        .telemetry-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .telemetry-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 1rem 1.15rem;
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
            position: relative;
            overflow: hidden;
        }

        .telemetry-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: transparent;
        }

        .telemetry-card.primary-top::before { background: var(--primary); }
        .telemetry-card.emerald-top::before { background: var(--color-emerald); }
        .telemetry-card.sky-top::before { background: var(--color-sky); }
        .telemetry-card.amber-top::before { background: var(--color-amber); }

        .telemetry-label {
            font-size: 0.7rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .telemetry-value {
            font-size: 0.98rem;
            font-weight: 700;
            color: #FFFFFF;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .telemetry-sub {
            font-size: 0.72rem;
            color: var(--text-dark);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Main Workspace Panels */
        .workspace-panel {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            padding-bottom: 0.85rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .panel-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #FFFFFF;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            letter-spacing: -0.01em;
        }

        .panel-desc {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 0.2rem;
        }

        /* Action Grid */
        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.25rem;
        }

        .action-card {
            background: var(--card-inner);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: var(--radius-sm);
            padding: 1.25rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.2s ease;
        }

        .action-card:hover {
            border-color: rgba(37, 99, 235, 0.4);
            transform: translateY(-1px);
        }

        .action-card-header {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .action-icon {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            background: rgba(37, 99, 235, 0.15);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.15rem;
        }

        .action-icon.sky {
            background: rgba(14, 165, 233, 0.15);
            color: var(--color-sky);
        }

        .action-icon.amber {
            background: rgba(245, 158, 11, 0.15);
            color: var(--color-amber);
        }

        .action-icon.emerald {
            background: rgba(16, 185, 129, 0.15);
            color: var(--color-emerald);
        }

        .action-icon.rose {
            background: rgba(244, 63, 94, 0.15);
            color: var(--color-rose);
        }

        .action-card-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: #FFFFFF;
        }

        .action-card-desc {
            font-size: 0.78rem;
            color: var(--text-muted);
            line-height: 1.45;
            margin-top: 0.2rem;
        }

        .action-card-form {
            margin-top: 1rem;
            padding-top: 0.85rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Form Controls */
        .form-select, .form-input {
            width: 100%;
            background: #09090C;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 0.55rem 0.75rem;
            color: #FFFFFF;
            font-size: 0.82rem;
            font-family: inherit;
            margin-bottom: 0.75rem;
            outline: none;
            transition: border-color 0.15s ease;
        }

        .form-select:focus, .form-input:focus {
            border-color: var(--primary);
        }

        /* Console Output Box */
        .terminal-box {
            background: #050507;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .terminal-header {
            background: #0D0D12;
            padding: 0.6rem 1rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        .terminal-dots {
            display: flex;
            gap: 6px;
        }

        .terminal-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #27272A;
        }

        .terminal-dot.r { background: #EF4444; }
        .terminal-dot.y { background: #F59E0B; }
        .terminal-dot.g { background: #10B981; }

        .terminal-content {
            padding: 1.25rem;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.82rem;
            line-height: 1.6;
            color: #34D399;
            white-space: pre-wrap;
            word-break: break-all;
            max-height: 400px;
            overflow-y: auto;
        }

        /* Commit History Table */
        .commit-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
        }

        .commit-table th {
            text-align: left;
            padding: 0.6rem 0.85rem;
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--text-dark);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border);
        }

        .commit-table td {
            padding: 0.65rem 0.85rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            color: var(--text-main);
        }

        .commit-table tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        .commit-hash {
            font-family: 'JetBrains Mono', monospace;
            color: #38BDF8;
            font-weight: 600;
        }

        /* Auth Gate Screen */
        .auth-wrapper {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-card {
            width: 100%;
            max-width: 440px;
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 2.25rem 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            position: relative;
        }

        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), #60A5FA);
            border-radius: var(--radius-md) var(--radius-md) 0 0;
        }

        .alert-box {
            padding: 0.75rem 1rem;
            border-radius: var(--radius-sm);
            font-size: 0.8rem;
            margin-bottom: 1.25rem;
            border: 1px solid transparent;
            line-height: 1.45;
        }

        .alert-box.danger {
            background: rgba(244, 63, 94, 0.15);
            border-color: rgba(244, 63, 94, 0.4);
            color: #FDA4AF;
        }

        .alert-box.info {
            background: rgba(14, 165, 233, 0.12);
            border-color: rgba(14, 165, 233, 0.3);
            color: #BAE6FD;
        }

        .alert-box.warning {
            background: rgba(245, 158, 11, 0.12);
            border-color: rgba(245, 158, 11, 0.3);
            color: #FCD34D;
        }

        /* Modal Dialog */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 1rem;
        }

        .modal-box {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            max-width: 480px;
            width: 100%;
            padding: 1.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.7);
        }

        .modal-title {
            font-size: 1rem;
            font-weight: 700;
            color: #FFFFFF;
            margin-bottom: 0.5rem;
        }

        .modal-body {
            font-size: 0.84rem;
            color: var(--text-muted);
            margin-bottom: 1.25rem;
            line-height: 1.5;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        /* Footer */
        .footer-note {
            text-align: center;
            font-size: 0.75rem;
            color: var(--text-dark);
            margin-top: 2rem;
            line-height: 1.6;
        }
    </style>
</head>
<body>

<?php if (!$isAuthenticated): ?>
<!-- ======================================================================= -->
<!-- TAMPILAN 1: AUTHENTICATION GATE (LOGIN TERPROTEKSI)                     -->
<!-- ======================================================================= -->
<div class="auth-wrapper">
    <div class="auth-card">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
            <div class="brand-badge" style="height: 32px;"></div>
            <div>
                <div class="brand-title"><?php echo htmlspecialchars($CONFIG['app_name']); ?></div>
                <div class="brand-subtitle"><?php echo htmlspecialchars($CONFIG['app_subtitle']); ?></div>
            </div>
        </div>

        <?php if (!empty($loginError)): ?>
            <div class="alert-box danger">
                <?php echo htmlspecialchars($loginError); ?>
            </div>
        <?php elseif ($urlParamAttempt): ?>
            <div class="alert-box danger">
                ⚠️ <strong>Akses URL Ditolak:</strong> Kata sandi tidak boleh dimasukkan melalui URL query parameter (GET). Masukkan kata sandi pada form di bawah ini demi keamanan.
            </div>
        <?php else: ?>
            <div class="alert-box info">
                🔒 Halaman ini dilindungi kunci otentikasi server. Masukkan kata sandi deploy untuk melanjutkan.
            </div>
        <?php endif; ?>

        <?php if (!$isKeyConfigured): ?>
            <div class="alert-box warning">
                🛡️ <strong>Rekomendasi Keamanan:</strong> Atur variabel <code><?php echo htmlspecialchars($CONFIG['env_key_name']); ?></code> pada file <code>.env</code> Anda untuk menentukan sandi kustom yang aman.
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars(strtok($_SERVER['REQUEST_URI'] ?? '', '?')); ?>" autocomplete="off">
            <input type="hidden" name="login_submit" value="1">
            <label style="font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); display: block; margin-bottom: 0.45rem;">
                Kata Sandi Server:
            </label>
            <div style="position: relative; margin-bottom: 1.25rem;">
                <input type="password" id="authKeyInput" name="key" class="form-input" placeholder="<?php echo $isLockedOut ? 'Akses terkunci sementara...' : 'Masukkan kata sandi deploy...'; ?>" value="" required autofocus autocomplete="current-password" style="margin-bottom: 0; padding-right: 2.5rem;" <?php echo $isLockedOut ? 'disabled' : ''; ?>>
                <button type="button" onclick="togglePassword()" style="position: absolute; right: 0.65rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; font-size: 0.85rem;" title="Lihat/Sembunyikan" <?php echo $isLockedOut ? 'disabled' : ''; ?>>👁️</button>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-size: 0.88rem;" <?php echo $isLockedOut ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ''; ?>>
                <?php echo $isLockedOut ? 'Terkunci Sementara ⏳' : 'Buka Terminal Deploy ⚡'; ?>
            </button>
        </form>

        <div style="margin-top: 1.5rem; text-align: center;">
            <a href="/" class="btn btn-outline" style="font-size: 0.78rem; padding: 0.45rem 0.85rem;">← Kembali ke Beranda Publik</a>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    var input = document.getElementById('authKeyInput');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>

<?php else: ?>
<!-- ======================================================================= -->
<!-- TAMPILAN 2: DEVOPS WORKSPACE UTAMA (TERAUTENTIKASI)                     -->
<!-- ======================================================================= -->
<div class="container">

    <!-- Top Navigation Bar -->
    <div class="topbar">
        <div class="brand-section">
            <div class="brand-badge"></div>
            <div>
                <div class="brand-title"><?php echo htmlspecialchars($CONFIG['app_name']); ?></div>
                <div class="brand-subtitle"><?php echo htmlspecialchars($CONFIG['app_subtitle']); ?></div>
            </div>
        </div>

        <div class="topbar-actions">
            <span class="badge badge-emerald">
                <span class="pulse-dot"></span>
                Terminal Online
            </span>

            <?php if ($isDown): ?>
                <span class="badge badge-rose">Mode Pemeliharaan (DOWN)</span>
            <?php endif; ?>

            <a href="/" target="_blank" class="btn btn-outline">
                Lihat Web ↗
            </a>

            <a href="?logout=1" class="btn btn-danger-outline" style="padding: 0.45rem 0.75rem;">
                Kunci Keluar 🔒
            </a>
        </div>
    </div>

    <!-- Telemetri Sistem -->
    <div class="telemetry-grid">
        <!-- Card 1: Git Branch & Commit -->
        <div class="telemetry-card primary-top">
            <div class="telemetry-label">
                <span>Git Active Branch</span>
                <span class="badge badge-sky" style="font-size: 0.65rem; padding: 0.1rem 0.45rem;"><?php echo htmlspecialchars($gitBranch); ?></span>
            </div>
            <div class="telemetry-value mono" style="font-size: 0.85rem;">
                #<?php echo htmlspecialchars($gitCommitHash); ?> <span style="font-size: 0.75rem; font-weight: normal; color: var(--text-muted);"><?php echo htmlspecialchars(mb_strimwidth($gitCommitMsg, 0, 32, '...')); ?></span>
            </div>
            <div class="telemetry-sub">
                <?php echo htmlspecialchars($gitCommitAuthor); ?> • <?php echo htmlspecialchars($gitCommitDate); ?>
            </div>
        </div>

        <!-- Card 2: Status File Lokal -->
        <div class="telemetry-card <?php echo $gitStatusClean ? 'emerald-top' : 'amber-top'; ?>">
            <div class="telemetry-label">
                <span>Working Tree Status</span>
                <?php if ($gitStatusClean): ?>
                    <span class="badge badge-emerald" style="font-size: 0.65rem; padding: 0.1rem 0.45rem;">Bersih (Clean)</span>
                <?php else: ?>
                    <span class="badge badge-amber" style="font-size: 0.65rem; padding: 0.1rem 0.45rem;"><?php echo count($gitUncommitted); ?> Perubahan</span>
                <?php endif; ?>
            </div>
            <div class="telemetry-value" style="font-size: 0.88rem;">
                <?php echo $gitStatusClean ? 'Sinkron dengan Repository' : 'Ada file lokal termodifikasi'; ?>
            </div>
            <div class="telemetry-sub">
                Path: <span class="mono"><?php echo htmlspecialchars($laravelRoot ?? 'Tidak terdeteksi'); ?></span>
            </div>
        </div>

        <!-- Card 3: Storage Symlink & Disk -->
        <div class="telemetry-card sky-top">
            <div class="telemetry-label">
                <span>Storage & Ruang Server</span>
                <span class="badge <?php echo $storageLinked ? 'badge-emerald' : 'badge-amber'; ?>" style="font-size: 0.65rem; padding: 0.1rem 0.45rem;">
                    <?php echo $storageLinked ? 'Symlink OK' : 'Symlink Belum'; ?>
                </span>
            </div>
            <div class="telemetry-value" style="font-size: 0.88rem;">
                <?php echo $diskFreeGB !== null ? $diskFreeGB . ' GB Sisa Ruang' : 'Penyimpanan Aktif'; ?>
            </div>
            <div class="telemetry-sub">
                PHP Binary: <span class="mono"><?php echo htmlspecialchars(basename($phpBinary)); ?></span>
            </div>
        </div>
    </div>

    <!-- Alert / Hasil Eksekusi Terakhir -->
    <?php if ($actionResult !== null): ?>
        <div class="terminal-box">
            <div class="terminal-header">
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <div class="terminal-dots">
                        <span class="terminal-dot r"></span>
                        <span class="terminal-dot y"></span>
                        <span class="terminal-dot g"></span>
                    </div>
                    <span>HASIL EKSEKUSI: <?php echo htmlspecialchars($actionTitle ?? 'Console Output'); ?></span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <span class="badge badge-emerald" style="font-size: 0.65rem;">Selesai dalam <?php echo $actionTime; ?>s</span>
                    <button type="button" onclick="copyConsoleOutput()" class="btn btn-outline" style="padding: 0.2rem 0.5rem; font-size: 0.7rem;">Salin Log</button>
                </div>
            </div>
            <div class="terminal-content" id="consoleOutputArea"><?php echo htmlspecialchars($actionResult); ?></div>
        </div>
    <?php endif; ?>

    <!-- PANEL AKSI UTAMA -->
    <div class="workspace-panel">
        <div class="panel-header">
            <div>
                <div class="panel-title">
                    <span>⚡</span> Pusat Kendali Deployment & Basis Data
                </div>
                <div class="panel-desc">
                    Pilih aksi DevOps yang ingin dijalankan pada instalasi server ini.
                </div>
            </div>
        </div>

        <div class="action-grid">
            <!-- 1. GIT PULL BIASA -->
            <div class="action-card">
                <div>
                    <div class="action-card-header">
                        <div class="action-icon">🚀</div>
                        <div>
                            <div class="action-card-title">Git Fast Pull</div>
                            <div class="action-card-desc">Tarik pembaruan commit terbaru dari branch <code><?php echo htmlspecialchars($gitBranch); ?></code> dan segarkan cache.</div>
                        </div>
                    </div>
                </div>
                <div class="action-card-form">
                    <form method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                        <input type="hidden" name="action" value="git_pull">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            ⚡ Pull & Clear Cache
                        </button>
                    </form>
                </div>
            </div>

            <!-- 2. GIT FORCE RESET & SYNC -->
            <div class="action-card">
                <div>
                    <div class="action-card-header">
                        <div class="action-icon amber">💥</div>
                        <div>
                            <div class="action-card-title">Force Sync (Hard Reset)</div>
                            <div class="action-card-desc">Paksa sinkronisasi persis dengan branch <code><?php echo htmlspecialchars($gitBranch); ?></code> GitHub. File <code>.env</code> & <code>storage</code> tetap aman.</div>
                        </div>
                    </div>
                </div>
                <div class="action-card-form">
                    <button type="button" class="btn btn-amber-outline" style="width: 100%;" onclick="confirmAction('formForceSync', 'Konfirmasi Force Sync', 'Tindakan ini akan menimpa seluruh file kode lokal agar persis dengan branch GitHub (kecuali berkas .env dan folder storage). Lanjutkan?')">
                        ⚡ Force Reset & Sync
                    </button>
                    <form id="formForceSync" method="POST" action="" style="display: none;">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                        <input type="hidden" name="action" value="git_force_sync">
                    </form>
                </div>
            </div>

            <!-- 3. GIT UNDO / ROLLBACK COMMIT -->
            <div class="action-card">
                <div>
                    <div class="action-card-header">
                        <div class="action-icon amber">↩️</div>
                        <div>
                            <div class="action-card-title">Undo Git Commit (Rollback)</div>
                            <div class="action-card-desc">Kembalikan kode ke 1 atau beberapa commit sebelumnya jika commit terbaru mengalami kendala.</div>
                        </div>
                    </div>
                </div>
                <div class="action-card-form">
                    <form id="formGitUndo" method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                        <input type="hidden" name="action" value="git_undo">
                        <div style="display: flex; gap: 0.5rem; margin-bottom: 0.65rem;">
                            <select name="undo_steps" class="form-select" style="margin-bottom: 0; flex: 1;">
                                <option value="1">Undo 1 Commit Terakhir (HEAD~1)</option>
                                <option value="2">Undo 2 Commit Terakhir (HEAD~2)</option>
                                <option value="3">Undo 3 Commit Terakhir (HEAD~3)</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn-amber-outline" style="width: 100%;" onclick="confirmAction('formGitUndo', 'Konfirmasi Undo Commit', 'Apakah Anda yakin ingin melakukan rollback commit ke belakang? Pastikan Anda memahami perubahan yang dibatalkan.')">
                            ↩️ Eksekusi Undo Commit
                        </button>
                    </form>
                </div>
            </div>

            <!-- 4. DATABASE MIGRATE -->
            <div class="action-card">
                <div>
                    <div class="action-card-header">
                        <div class="action-icon sky">🗄️</div>
                        <div>
                            <div class="action-card-title">Database Migrate</div>
                            <div class="action-card-desc">Jalankan file migrasi tabel baru ke database (<code>php artisan migrate --force</code>).</div>
                        </div>
                    </div>
                </div>
                <div class="action-card-form">
                    <div style="display: flex; gap: 0.5rem;">
                        <form method="POST" action="" style="flex: 1;">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                            <input type="hidden" name="action" value="db_migrate">
                            <button type="submit" class="btn btn-sky-outline" style="width: 100%;">
                                ⚡ Run Migrate
                            </button>
                        </form>
                        <form method="POST" action="" style="flex: 1;">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                            <input type="hidden" name="action" value="db_migrate_status">
                            <button type="submit" class="btn btn-outline" style="width: 100%;">
                                📋 Status
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- 5. DATABASE SEEDER -->
            <div class="action-card">
                <div>
                    <div class="action-card-header">
                        <div class="action-icon emerald">🌱</div>
                        <div>
                            <div class="action-card-title">Database Seeder</div>
                            <div class="action-card-desc">Isi data awal aplikasi. Daftar seeder terdeteksi otomatis dari direktori <code>database/seeders/</code>.</div>
                        </div>
                    </div>
                </div>
                <div class="action-card-form">
                    <form id="formDbSeed" method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                        <input type="hidden" name="action" value="db_seed">
                        <select name="seeder_class" class="form-select">
                            <?php foreach ($availableSeeders as $class => $desc): ?>
                                <option value="<?php echo htmlspecialchars($class); ?>"><?php echo htmlspecialchars($desc); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn btn-outline" style="width: 100%; border-color: rgba(16, 185, 129, 0.4); color: #34D399;" onclick="confirmAction('formDbSeed', 'Konfirmasi Seeder', 'Menjalankan seeder akan menginput record data ke database. Lanjutkan?')">
                            🌱 Eksekusi Seeder
                        </button>
                    </form>
                </div>
            </div>

            <!-- 6. REFRESH / BUAT ULANG DATABASE (migrate:fresh) -->
            <div class="action-card" style="border-color: rgba(244, 63, 94, 0.25);">
                <div>
                    <div class="action-card-header">
                        <div class="action-icon rose">♻️</div>
                        <div>
                            <div class="action-card-title" style="color: #FDA4AF;">Refresh / Buat Ulang Database</div>
                            <div class="action-card-desc">Hapus seluruh tabel dan susun ulang struktur basis data dari awal (<code>artisan migrate:fresh --force</code>).</div>
                        </div>
                    </div>
                </div>
                <div class="action-card-form">
                    <form id="formDbFresh" method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                        <input type="hidden" name="action" value="db_fresh">
                        <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; color: #E2E8F0; cursor: pointer; margin-bottom: 0.65rem; user-select: none;">
                            <input type="checkbox" name="with_seed" value="1" checked style="accent-color: var(--primary); width: 16px; height: 16px;">
                            <span>Isi ulang seluruh data seeder otomatis (<code>--seed</code>)</span>
                        </label>
                        <button type="button" class="btn btn-danger-outline" style="width: 100%; border-color: rgba(244, 63, 94, 0.4); color: #FB7185;" onclick="confirmAction('formDbFresh', 'Konfirmasi Reset Total Database', 'PERINGATAN KERAS: Aksi ini akan MENGHAPUS SEMUA TABEL di database dan menyusunnya kembali dari nol (migrate:fresh). Seluruh data yang ada akan hilang. Apakah Anda yakin?')">
                            🔥 Reset &amp; Buat Ulang Database
                        </button>
                    </form>
                </div>
            </div>

            <!-- 7. ROLLBACK MIGRASI -->
            <div class="action-card">
                <div>
                    <div class="action-card-header">
                        <div class="action-icon amber">🔄</div>
                        <div>
                            <div class="action-card-title">Rollback Migrasi Database</div>
                            <div class="action-card-desc">Batalkan migrasi batch sebelumnya jika ada perubahan skema yang keliru (<code>migrate:rollback</code>).</div>
                        </div>
                    </div>
                </div>
                <div class="action-card-form">
                    <form id="formMigrateRollback" method="POST" action="">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                        <input type="hidden" name="action" value="db_migrate_rollback">
                        <input type="hidden" name="rollback_step" value="1">
                        <button type="button" class="btn btn-danger-outline" style="width: 100%;" onclick="confirmAction('formMigrateRollback', 'Konfirmasi Rollback Migrasi', 'PERINGATAN: Rollback migrasi akan menghapus tabel atau kolom database pada batch terakhir. Lanjutkan?')">
                            ⚠️ Rollback 1 Batch Migrasi
                        </button>
                    </form>
                </div>
            </div>

            <!-- 8. BERSIHKAN & OPTIMASI CACHE -->
            <div class="action-card">
                <div>
                    <div class="action-card-header">
                        <div class="action-icon">🧹</div>
                        <div>
                            <div class="action-card-title">Manajemen Cache Laravel</div>
                            <div class="action-card-desc">Bersihkan cache tampilan, rute, config, dan lakukan cache warmup untuk kecepatan produksi.</div>
                        </div>
                    </div>
                </div>
                <div class="action-card-form">
                    <div style="display: flex; gap: 0.5rem;">
                        <form method="POST" action="" style="flex: 1;">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                            <input type="hidden" name="action" value="cache_clear">
                            <button type="submit" class="btn btn-outline" style="width: 100%;">
                                🧹 Clear Cache
                            </button>
                        </form>
                        <form method="POST" action="" style="flex: 1;">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                            <input type="hidden" name="action" value="cache_optimize">
                            <button type="submit" class="btn btn-outline" style="width: 100%; color: var(--primary); border-color: rgba(37, 99, 235, 0.4);">
                                🚀 Warmup Cache
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- 9. STORAGE LINK & MAINTENANCE -->
            <div class="action-card">
                <div>
                    <div class="action-card-header">
                        <div class="action-icon sky">🔗</div>
                        <div>
                            <div class="action-card-title">Storage Izin & Mode Maintenance</div>
                            <div class="action-card-desc">Buat symlink storage, perbaiki izin tulis (chmod 775), atau aktifkan mode pemeliharaan.</div>
                        </div>
                    </div>
                </div>
                <div class="action-card-form">
                    <div style="display: flex; gap: 0.5rem;">
                        <form method="POST" action="" style="flex: 1;">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                            <input type="hidden" name="action" value="storage_fix_permissions">
                            <button type="submit" class="btn btn-outline" style="width: 100%;">
                                📁 Fix Storage & Izin
                            </button>
                        </form>
                        <form method="POST" action="" style="flex: 1;">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                            <input type="hidden" name="action" value="maintenance_toggle">
                            <button type="submit" class="btn <?php echo $isDown ? 'btn-primary' : 'btn-amber-outline'; ?>" style="width: 100%;">
                                <?php echo $isDown ? '🟢 Buka Web (UP)' : '🚧 Matikan (DOWN)'; ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PANEL RIWAYAT COMMIT TERAKHIR -->
    <div class="workspace-panel">
        <div class="panel-header">
            <div>
                <div class="panel-title">
                    <span>📜</span> Riwayat Commit Git Terbaru di Server
                </div>
                <div class="panel-desc">
                    Catatan 6 commit terakhir yang tersinkronisasi pada instalasi hosting/server ini.
                </div>
            </div>
        </div>

        <div style="overflow-x: auto;">
            <table class="commit-table">
                <thead>
                    <tr>
                        <th style="width: 90px;">Hash</th>
                        <th style="width: 140px;">Author</th>
                        <th style="width: 120px;">Waktu</th>
                        <th>Pesan Commit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($gitRecentLogs)): ?>
                        <?php foreach ($gitRecentLogs as $idx => $c): ?>
                            <tr>
                                <td class="commit-hash">
                                    #<?php echo htmlspecialchars($c['hash']); ?>
                                    <?php if ($idx === 0): ?>
                                        <span class="badge badge-emerald" style="font-size: 0.55rem; padding: 0.05rem 0.35rem; margin-left: 0.2rem;">HEAD</span>
                                    <?php endif; ?>
                                </td>
                                <td style="color: var(--text-muted);"><?php echo htmlspecialchars($c['author']); ?></td>
                                <td style="color: var(--text-dark);"><?php echo htmlspecialchars($c['date']); ?></td>
                                <td style="font-weight: 500; color: #FFFFFF;"><?php echo htmlspecialchars($c['msg']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 1.5rem;">Tidak dapat membaca riwayat commit git.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="footer-note">
        <?php echo htmlspecialchars($CONFIG['footer_note']); ?><br>
        Keamanan terproteksi file lock & rate limiting. Simpan sandi dengan aman.
    </div>

</div>

<!-- Modal Konfirmasi Dialog -->
<div id="confirmModal" class="modal-overlay">
    <div class="modal-box">
        <div id="modalTitle" class="modal-title">Konfirmasi Aksi</div>
        <div id="modalBody" class="modal-body">Apakah Anda yakin ingin menjalankan aksi ini?</div>
        <div class="modal-actions">
            <button type="button" onclick="closeConfirmModal()" class="btn btn-outline">Batal</button>
            <button type="button" id="modalConfirmBtn" class="btn btn-primary">Lanjutkan Aksi</button>
        </div>
    </div>
</div>

<script>
var targetFormId = null;

function confirmAction(formId, title, message) {
    targetFormId = formId;
    document.getElementById('modalTitle').innerText = title;
    document.getElementById('modalBody').innerText = message;
    document.getElementById('confirmModal').style.display = 'flex';
}

function closeConfirmModal() {
    document.getElementById('confirmModal').style.display = 'none';
    targetFormId = null;
}

document.getElementById('modalConfirmBtn').addEventListener('click', function() {
    if (targetFormId) {
        var form = document.getElementById(targetFormId);
        if (form) {
            closeConfirmModal();
            form.submit();
        }
    }
});

function copyConsoleOutput() {
    var area = document.getElementById('consoleOutputArea');
    if (!area) return;
    navigator.clipboard.writeText(area.innerText).then(function() {
        alert('Log output berhasil disalin ke clipboard!');
    });
}
</script>

<?php endif; ?>

</body>
</html>
