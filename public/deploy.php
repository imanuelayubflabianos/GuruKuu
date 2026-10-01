<?php
declare(strict_types=1);

/**
 * =========================================================================
 * GURUKUU DEPLOYER — sinkronisasi GitHub -> server dari dalam sistem
 * =========================================================================
 * Alur kerja:  [komputer lokal] --git push--> [GitHub] --> [server]
 *
 * Ada 2 cara update server:
 *   1. OTOMATIS : GitHub Webhook memanggil  /deploy.php?hook=1  setiap push.
 *   2. MANUAL   : buka /deploy.php, login, klik "Deploy Sekarang".
 *
 * File ini SENGAJA berdiri sendiri (tidak lewat Laravel) supaya tetap bisa
 * dibuka meski aplikasi error setelah sebuah update.
 *
 * LOKASI FILE: taruh di folder public/ (public/deploy.php). Bila folder publik
 * server dipisah dari folder Laravel (mis. public_html di hosting), isi
 * DEPLOY_ROOT_OVERRIDE di bawah dengan path absolut folder Laravel (yang berisi
 * file `artisan`), atau set env GURUKUU_ROOT.
 *
 * KONFIGURASI (tambahkan ke file .env di SERVER, bukan di git):
 *
 *   DEPLOY_KEY=isi-kunci-acak-minimal-12-karakter   # WAJIB (atau DEPLOY_KEY_HASH)
 *   DEPLOY_KEY_HASH=                                # opsional: hasil password_hash() bcrypt
 *   DEPLOY_WEBHOOK_SECRET=isi-secret-webhook-acak   # opsional: aktifkan auto-deploy
 *   DEPLOY_BRANCH=main                              # default: main
 *   DEPLOY_REMOTE=origin                            # default: origin
 *   DEPLOY_ALLOWED_IPS=                             # opsional: "1.2.3.4,5.6.7.8" (khusus halaman web)
 *   DEPLOY_AUTO_MIGRATE=true                        # jalankan migrate saat deploy (default true)
 *   DEPLOY_MAINTENANCE=false                        # mode maintenance selama deploy (default false)
 *   DEPLOY_WARMUP=false                             # jalankan `artisan optimize` setelah deploy
 *   DEPLOY_BUILD_ASSETS=false                       # jalankan `npm ci/build` jika JS/CSS berubah
 *   DEPLOY_PHP_BINARY=                              # opsional: path php CLI (mis. /usr/bin/php8.3)
 *   DEPLOY_HOME=                                    # opsional: HOME untuk git/composer
 *   DEPLOY_NPM_BINARY=                              # opsional: path npm (default: cari otomatis, termasuk storage/framework/node)
 *   DEPLOY_NODE_VERSION=22                          # versi mayor Node untuk tombol "Pasang Node Lokal"
 *   DEPLOY_ALLOW_TERMINAL=false                     # aktifkan kotak perintah bebas (shell) di halaman web
 *   DEPLOY_ALLOW_FRESH=false                        # izinkan migrate:fresh (menghapus SEMUA tabel). Default MATI demi keamanan data
 *   DEPLOY_SSH_KEY=                                 # path private key SSH (Deploy Key GitHub) bila remote berbentuk git@github.com:...
 *
 * Tidak ada kunci bawaan: bila DEPLOY_KEY/DEPLOY_KEY_HASH kosong, halaman
 * web menolak semua akses.
 * =========================================================================
 */

const DEPLOY_SESSION_IDLE = 1800; // detik
const DEPLOY_ROOT_OVERRIDE = ''; // opsional: path absolut folder Laravel, mis. '/home/user/gurukuu'

// -------------------------------------------------------------------------
// Helper umum
// -------------------------------------------------------------------------
function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function detect_root(): ?string
{
    $extra = DEPLOY_ROOT_OVERRIDE !== '' ? DEPLOY_ROOT_OVERRIDE : (string) getenv('GURUKUU_ROOT');
    $candidates = [__DIR__ . '/..', __DIR__, __DIR__ . '/../..'];
    if ($extra !== '') {
        array_unshift($candidates, $extra);
    }
    foreach ($candidates as $candidate) {
        if (is_file($candidate . '/artisan')) {
            return realpath($candidate) ?: null;
        }
    }
    return null;
}

final class Env
{
    private static array $vars = [];

    public static function load(?string $root): void
    {
        if (! $root || ! is_file($root . '/.env')) {
            return;
        }
        foreach (file($root . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') {
                continue;
            }
            if (str_starts_with($line, 'export ')) {
                $line = substr($line, 7);
            }
            if (! str_contains($line, '=')) {
                continue;
            }
            [$k, $v] = explode('=', $line, 2);
            $v = trim($v);
            if ($v !== '' && ($v[0] === '"' || $v[0] === "'")) {
                $q   = $v[0];
                $end = strpos($v, $q, 1);
                $v   = $end === false ? substr($v, 1) : substr($v, 1, $end - 1);
            } else {
                $v = trim(preg_replace('/\s+#.*$/', '', $v) ?? $v);
            }
            self::$vars[trim($k)] = $v;
        }
    }

    public static function get(string $key, string $default = ''): string
    {
        return self::$vars[$key] ?? (getenv($key) !== false ? (string) getenv($key) : $default);
    }

    public static function bool(string $key, bool $default): bool
    {
        $v = strtolower(self::get($key, $default ? 'true' : 'false'));
        return in_array($v, ['1', 'true', 'yes', 'on'], true);
    }
}

// -------------------------------------------------------------------------
// Inti: eksekusi perintah (tanpa shell -> aman dari injeksi) + pipeline deploy
// -------------------------------------------------------------------------
final class Deployer
{
    /** @var string[] */
    public array $log = [];
    private $lockFp = null;

    public function __construct(private string $root, private array $cfg)
    {
    }

    public function root(): string
    {
        return $this->root;
    }

    // ---- binary ----------------------------------------------------------
    public function phpBinary(): string
    {
        $o = Env::get('DEPLOY_PHP_BINARY');
        if ($o !== '' && is_executable($o)) {
            return $o;
        }
        // Di bawah php-fpm/cgi PHP_BINARY menunjuk ke fpm, bukan CLI -> jangan dipakai.
        if (PHP_BINARY && is_executable(PHP_BINARY) && preg_match('/^php[\d.]*(\.exe)?$/i', basename(PHP_BINARY))) {
            return PHP_BINARY;
        }
        foreach ([PHP_BINDIR . '/php', '/usr/bin/php', '/usr/local/bin/php'] as $c) {
            if (is_executable($c)) {
                return $c;
            }
        }
        return 'php';
    }

    private function findBinary(string $name): ?string
    {
        $dirs = array_filter(explode(PATH_SEPARATOR, (string) getenv('PATH')));
        $dirs = array_merge($dirs, ['/usr/local/bin', '/usr/bin', '/opt/homebrew/bin']);
        foreach (array_unique($dirs) as $d) {
            $p = rtrim($d, '/\\') . DIRECTORY_SEPARATOR . $name;
            if (is_file($p) && is_executable($p)) {
                return $p;
            }
        }
        return null;
    }

    /** @return string[]|null prefix perintah composer */
    public function composerCmd(): ?array
    {
        if (is_file($this->root . '/composer.phar')) {
            return [$this->phpBinary(), $this->root . '/composer.phar'];
        }
        $c = $this->findBinary('composer');
        if (! $c) {
            return null;
        }
        // Composer (phar/skrip PHP) dijalankan lewat PHP CLI pilihan kita, supaya
        // syarat php ^8.3 dan hook `@php artisan package:discover` memakai PHP yang sama.
        $head = (string) @file_get_contents($c, false, null, 0, 120);
        $first = strtok($head, "\n") ?: '';
        if (str_starts_with($head, '<?php') || (str_starts_with($first, '#!') && stripos($first, 'php') !== false)) {
            return [$this->phpBinary(), $c];
        }
        return [$c];
    }

    /** Folder Node lokal hasil "Pasang Node Lokal". */
    public function localNodeDir(): string
    {
        return $this->root . '/storage/framework/node';
    }

    /** Path node: lokal bila ada, selain itu `node` dari PATH. */
    public function nodeBinary(): string
    {
        foreach ($this->nodeBinDirs() as $d) {
            if (is_file($d . '/node')) {
                return $d . '/node';
            }
        }
        return 'node';
    }

    public function npmBinary(): ?string
    {
        $o = Env::get('DEPLOY_NPM_BINARY');
        if ($o !== '' && is_executable($o)) {
            return $o;
        }
        $local = $this->localNodeDir() . '/bin/npm';
        if (is_file($local)) {
            return $local;
        }
        return $this->findBinary('npm');
    }

    /** Folder bin yang harus didahulukan di PATH agar `node`/`npm` yang benar dipakai. */
    private function nodeBinDirs(): array
    {
        $dirs = [];
        $o = Env::get('DEPLOY_NPM_BINARY');
        if ($o !== '' && is_file($o)) {
            $dirs[] = dirname($o);
        }
        $local = $this->localNodeDir() . '/bin';
        if (is_file($local . '/node')) {
            $dirs[] = $local;
        }
        return array_values(array_unique($dirs));
    }

    // ---- eksekusi --------------------------------------------------------
    /** @return array{0:string,1:int} [output, exitCode] */
    public function exec(array $cmd, int $timeout = 300): array
    {
        $env = getenv() ?: [];
        $env['GIT_TERMINAL_PROMPT'] = '0'; // jangan menggantung menunggu password

        $sshKey = Env::get('DEPLOY_SSH_KEY');

        // known_hosts khusus deployer (user web sering tidak punya ~/.ssh yang bisa ditulis).
        // accept-new: host baru (github.com) diterima sekali, lalu dikunci -> tanpa prompt.
        $knownHosts = $this->root . '/storage/framework/deployer_known_hosts';
        $sshOpts    = ' -o BatchMode=yes -o StrictHostKeyChecking=accept-new'
            . ' -o UserKnownHostsFile=' . escapeshellarg($knownHosts);

        if ($sshKey !== '' && is_file($sshKey)) {
            $env['GIT_SSH_COMMAND'] =
                'ssh -i ' . escapeshellarg($sshKey) .
                ' -o IdentitiesOnly=yes' . $sshOpts;
        } elseif (empty($env['GIT_SSH_COMMAND'])) {
            $env['GIT_SSH_COMMAND'] = 'ssh' . $sshOpts;
        }
        if (empty($env['HOME'])) {
            $h = Env::get('DEPLOY_HOME') ?: $this->root . '/storage/framework/deployer_home';
            @mkdir($h, 0700, true);
            $env['HOME'] = $h;
        }
        if (empty($env['COMPOSER_HOME'])) {
            $env['COMPOSER_HOME'] = $env['HOME'] . '/.composer';
        }
        if ($dirs = $this->nodeBinDirs()) {
            $env['PATH'] = implode(PATH_SEPARATOR, $dirs) . PATH_SEPARATOR . ($env['PATH'] ?? '/usr/local/bin:/usr/bin:/bin');
        }
        $env['CI'] = '1'; // npm/vite: tanpa prompt interaktif

        $proc = @proc_open(
            $cmd,
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['redirect', 1]],
            $pipes,
            $this->root,
            $env
        );
        if (! is_resource($proc)) {
            return ['[gagal menjalankan proses: ' . ($cmd[0] ?? '?') . ']', 127];
        }

        fclose($pipes[0]);
        stream_set_blocking($pipes[1], false);
        $out   = '';
        $start = time();
        $exit  = null;

        while (true) {
            $chunk = stream_get_contents($pipes[1]);
            if ($chunk !== false && $chunk !== '') {
                $out .= $chunk;
            }
            $st = proc_get_status($proc);
            if (! $st['running']) {
                if ($st['exitcode'] >= 0) {
                    $exit = $st['exitcode'];
                }
                break;
            }
            if (time() - $start > $timeout) {
                proc_terminate($proc, 9);
                $out .= "\n[TIMEOUT: dihentikan setelah {$timeout} detik]";
                $exit = 124;
                break;
            }
            usleep(100000);
        }

        $rest = stream_get_contents($pipes[1]);
        if ($rest !== false) {
            $out .= $rest;
        }
        fclose($pipes[1]);
        $closed = proc_close($proc);

        return [$out, $exit ?? ($closed >= 0 ? $closed : 1)];
    }

    private function cmdLine(array $cmd): string
    {
        return implode(' ', array_map(
            fn ($a) => preg_match('/^[\w@%+=:,.\/~^-]+$/', (string) $a) ? (string) $a : escapeshellarg((string) $a),
            $cmd
        ));
    }

    /** Sembunyikan token/password pada URL remote di output. */
    public function mask(string $s): string
    {
        return preg_replace('#(https?://)[^/\s@]+@#i', '$1***@', $s) ?? $s;
    }

    /** Jalankan + catat ke log tampilan. */
    public function step(array $cmd, int $timeout = 300, ?string $display = null): array
    {
        $this->log[] = '$ ' . ($display ?? $this->cmdLine($cmd));
        [$out, $code] = $this->exec($cmd, $timeout);
        $out = trim($this->mask($out));
        if ($out !== '') {
            $this->log[] = $out;
        }
        if ($code !== 0) {
            $this->log[] = "[gagal: exit code {$code}]";
            if (stripos($out, 'Permission denied (publickey)') !== false) {
                $this->log[] = '[PETUNJUK] Server belum punya kunci SSH untuk GitHub. Pasang Deploy Key lalu isi DEPLOY_SSH_KEY di .env '
                    . '(atau ganti remote ke HTTPS + token). Pastikan file kunci dimiliki & bisa dibaca user web server (chmod 600).';
            }
        }
        return [$out, $code];
    }

    private function gitBase(): array
    {
        return ['git', '-c', 'safe.directory=' . $this->root];
    }

    public function git(array $args, int $timeout = 180): array
    {
        return $this->step(array_merge($this->gitBase(), $args), $timeout, 'git ' . $this->cmdLine($args));
    }

    /** Git tanpa dicatat ke log. @return array{0:string,1:int} */
    public function gitOut(array $args): array
    {
        [$o, $c] = $this->exec(array_merge($this->gitBase(), $args), 60);
        return [trim($this->mask($o)), $c];
    }

    public function artisan(array $args, int $timeout = 300): array
    {
        return $this->step(
            array_merge([$this->phpBinary(), 'artisan'], $args),
            $timeout,
            'php artisan ' . $this->cmdLine($args)
        );
    }

    // ---- state, lock, log ------------------------------------------------
    public function isDown(): bool
    {
        return is_file($this->root . '/storage/framework/down');
    }

    private function statePath(): string
    {
        return $this->root . '/storage/framework/deployer_state.json';
    }

    public function state(): array
    {
        $d = @json_decode((string) @file_get_contents($this->statePath()), true);
        return is_array($d) ? $d : [];
    }

    private function saveState(string $prev, string $now): void
    {
        @file_put_contents($this->statePath(), json_encode([
            'previous' => $prev,
            'current'  => $now,
            'at'       => date('c'),
        ]), LOCK_EX);
    }

    private function lock(): bool
    {
        $path = $this->root . '/storage/framework/deployer.lock';
        $fp   = @fopen($path, 'c') ?: @fopen(sys_get_temp_dir() . '/gurukuu_deployer.lock', 'c');
        if (! $fp || ! flock($fp, LOCK_EX | LOCK_NB)) {
            $this->log[] = '[DITOLAK] Proses deploy lain sedang berjalan. Coba lagi beberapa saat.';
            return false;
        }
        $this->lockFp = $fp;
        return true;
    }

    private function unlock(): void
    {
        if ($this->lockFp) {
            flock($this->lockFp, LOCK_UN);
            fclose($this->lockFp);
            $this->lockFp = null;
        }
    }

    private function auditPath(): string
    {
        $dir = $this->root . '/storage/logs';
        return (is_dir($dir) && is_writable($dir) ? $dir : sys_get_temp_dir()) . '/deployer.log';
    }

    public function audit(string $who, string $action, bool $ok, string $note = ''): void
    {
        $p = $this->auditPath();
        if (is_file($p) && filesize($p) > 512 * 1024) {
            $keep = array_slice(file($p) ?: [], -200);
            @file_put_contents($p, implode('', $keep), LOCK_EX);
        }
        $line = sprintf(
            "[%s] %s | %s | %s%s\n",
            date('Y-m-d H:i:s'),
            $who,
            $action,
            $ok ? 'OK' : 'GAGAL',
            $note !== '' ? ' | ' . preg_replace('/\s+/', ' ', $note) : ''
        );
        @file_put_contents($p, $line, FILE_APPEND | LOCK_EX);
    }

    /** @return string[] */
    public function auditTail(int $n = 15): array
    {
        $p = $this->auditPath();
        return is_file($p) ? array_slice(file($p, FILE_IGNORE_NEW_LINES) ?: [], -$n) : [];
    }

    // ---- pipeline --------------------------------------------------------
    /**
     * @param array{migrate?:bool,maintenance?:bool,warmup?:bool,force?:bool} $o
     */
    public function deploy(array $o): bool
    {
        if (! $this->lock()) {
            return false;
        }

        $down = false;
        try {
            if (! empty($o['maintenance']) && ! $this->isDown()) {
                [, $c] = $this->artisan(['down', '--retry=60']);
                $down  = $c === 0;
            }
            return $this->pipeline($o);
        } catch (\Throwable $t) {
            $this->log[] = '[ERROR] ' . $t->getMessage();
            return false;
        } finally {
            if ($down) {
                $this->bringUp();
            }
            $this->unlock();
        }
    }

    private function bringUp(): void
    {
        [, $c] = $this->artisan(['up']);
        if ($c !== 0 && @unlink($this->root . '/storage/framework/down')) {
            $this->log[] = '[INFO] `artisan up` gagal; file maintenance dihapus manual.';
        }
    }

    private function pipeline(array $o): bool
    {
        $branch = $this->cfg['branch'];
        $remote = $this->cfg['remote'];
        $ref    = "{$remote}/{$branch}";
        $force  = ! empty($o['force']);

        [$prev, $c] = $this->gitOut(['rev-parse', 'HEAD']);
        if ($c !== 0) {
            $this->log[] = '[GAGAL] Folder ini bukan repositori git atau git tidak bisa dijalankan.';
            $this->log[] = $prev;
            return false;
        }

        if (! $force) {
            [$cur] = $this->gitOut(['branch', '--show-current']);
            if ($cur !== $branch) {
                $this->log[] = "[DITOLAK] Server sedang di branch '{$cur}', bukan '{$branch}'. Gunakan Force Sync bila memang ingin pindah.";
                return false;
            }
            [$dirty] = $this->gitOut(['status', '--porcelain', '--untracked-files=no']);
            if ($dirty !== '') {
                $this->log[] = "[DITOLAK] Ada file di server yang diubah manual (akan tertimpa):";
                $this->log[] = implode("\n", array_slice(explode("\n", $dirty), 0, 10));
                $this->log[] = 'Gunakan Force Sync bila perubahan itu memang boleh dibuang.';
                return false;
            }
        }

        [, $c] = $this->git(['fetch', $remote, $branch]);
        if ($c !== 0) {
            $this->log[] = 'Cek akses server ke GitHub (deploy key / token, koneksi internet).';
            return false;
        }

        [, $c] = $this->gitOut(['rev-parse', '--verify', $ref . '^{commit}']);
        if ($c !== 0) {
            $this->log[] = "[GAGAL] Referensi {$ref} tidak ditemukan. Periksa DEPLOY_REMOTE / DEPLOY_BRANCH.";
            return false;
        }

        if ($force) {
            [, $c] = $this->git(['checkout', '-f', '-B', $branch, $ref]);
            if ($c !== 0) {
                return false;
            }
            // Yang dilindungi dari `git clean`: .env, aset, upload, storage, dan file deployer ini sendiri
            // (bila belum di-commit ke git, tanpa ini Force Sync akan menghapus deployer).
            $keep = ['.env', 'public/build', 'public/storage', 'public/uploads', 'storage'];
            $me   = str_replace('\\', '/', __FILE__);
            $base = str_replace('\\', '/', $this->root) . '/';
            if (str_starts_with($me, $base)) {
                $keep[] = substr($me, strlen($base));
            }
            $clean = ['clean', '-fd'];
            foreach ($keep as $k) {
                array_push($clean, '-e', $k);
            }
            $this->git($clean);
        } else {
            [$cnt] = $this->gitOut(['rev-list', '--count', "HEAD..{$ref}"]);
            if ((int) $cnt === 0) {
                $this->log[] = 'Kode sudah versi terbaru (tidak ada commit baru). Langkah pasca-update tetap dijalankan.';
            } else {
                $this->log[] = "Commit baru yang akan diterapkan ({$cnt}):";
                [$list] = $this->gitOut(['log', '--oneline', '--no-decorate', '--max-count=20', "HEAD..{$ref}"]);
                $this->log[] = $list;
                [, $c] = $this->git(['merge', '--ff-only', $ref]);
                if ($c !== 0) {
                    $this->log[] = 'Riwayat server menyimpang dari GitHub. Gunakan Force Sync bila aman.';
                    return false;
                }
            }
        }

        [$now] = $this->gitOut(['rev-parse', 'HEAD']);
        $ok    = $this->applyChanges($prev, $now, $o);

        if ($prev !== $now) {
            $this->saveState($prev, $now);
        }
        $this->log[] = $ok
            ? '✔ Deploy selesai. Versi aktif: ' . substr($now, 0, 7)
            : '✖ Deploy selesai DENGAN ERROR (lihat log di atas). Versi kode: ' . substr($now, 0, 7);

        return $ok;
    }

    /** Langkah pasca perubahan kode: composer, build aset, migrate, clear cache. */
    private function applyChanges(string $prev, string $now, array $o): bool
    {
        $changed = [];
        if ($prev !== $now) {
            [$d]     = $this->gitOut(['diff', '--name-only', $prev, $now]);
            $changed = $d === '' ? [] : explode("\n", $d);
        }
        $has = fn (string $re): bool => (bool) preg_grep($re, $changed);
        $ok  = true;

        if ($has('#^composer\.(json|lock)$#')) {
            $composer = $this->composerCmd();
            if ($composer) {
                [, $c] = $this->step(
                    array_merge($composer, ['install', '--no-dev', '--optimize-autoloader', '--no-interaction', '--no-progress']),
                    900
                );
                $ok = $ok && $c === 0;
            } else {
                $this->log[] = '[PERINGATAN] composer.json/lock berubah tetapi composer tidak ditemukan. Jalankan "composer install --no-dev" manual.';
            }
        }

        $frontendRe = '#^(package(-lock)?\.json|vite\.config\.[jt]s|tailwind\.config\.[jt]s|postcss\.config\.[jt]s|resources/(js|css)/)#';
        if ($has($frontendRe)) {
            if ($this->cfg['build_assets']) {
                $ok = $this->buildAssets($has('#^package(-lock)?\.json$#')) && $ok;
            } else {
                $this->log[] = '[INFO] Ada perubahan JS/CSS. Pastikan folder public/build ikut di-commit, atau set DEPLOY_BUILD_ASSETS=true di .env.';
            }
        }

        if (! empty($o['migrate'])) {
            [, $c] = $this->artisan(['migrate', '--force']);
            $ok    = $ok && $c === 0;
        }

        $this->artisan(['optimize:clear']);
        if (! empty($o['warmup'])) {
            $this->artisan(['optimize']);
        }

        return $ok;
    }

    /** Versi mayor Node yang aktif (0 bila tidak ada). */
    public function nodeMajor(): int
    {
        [$v, $c] = $this->exec([$this->nodeBinary(), '-v'], 20);
        return ($c === 0 && preg_match('/^v(\d+)\./', trim($v), $m)) ? (int) $m[1] : 0;
    }

    /** npm ci (opsional) + npm run build. */
    public function buildAssets(bool $install = true): bool
    {
        $npm = $this->npmBinary();
        if (! $npm) {
            $this->log[] = '[GAGAL] npm tidak ditemukan. Gunakan tombol "Pasang Node Lokal", atau set DEPLOY_NPM_BINARY di .env.';
            return false;
        }
        $major = $this->nodeMajor();
        if ($major === 0) {
            $this->log[] = '[GAGAL] node tidak bisa dijalankan.';
            return false;
        }
        if ($major < 18) {
            $this->log[] = "[GAGAL] Node v{$major} terlalu lama untuk Vite modern (butuh 18+). Gunakan tombol \"Pasang Node Lokal\".";
            return false;
        }
        $this->step([$this->nodeBinary(), '-v']);
        $this->step([$npm, '-v']);

        if ($install) {
            $hasLock = is_file($this->root . '/package-lock.json');
            [, $c] = $this->step([$npm, $hasLock ? 'ci' : 'install', '--no-audit', '--no-fund'], 900);
            if ($c !== 0) {
                return false;
            }
        } elseif (! is_dir($this->root . '/node_modules')) {
            $this->log[] = '[GAGAL] folder node_modules belum ada. Centang "npm ci dulu" pada build.';
            return false;
        }
        [, $c] = $this->step([$npm, 'run', 'build'], 900);
        if ($c === 0) {
            $this->log[] = '✔ Build aset selesai (public/build diperbarui).';
        } elseif ($c === 137 || $c === 9) {
            $this->log[] = '[PETUNJUK] Proses dimatikan sistem (kemungkinan kehabisan RAM). Tambah swap/RAM, atau build di lokal lalu commit public/build.';
        }
        return $c === 0;
    }

    /** Unduh Node.js LTS (biner resmi) ke storage/framework/node tanpa akses root. */
    public function installNode(): bool
    {
        if (PHP_OS_FAMILY !== 'Linux') {
            $this->log[] = '[GAGAL] Pemasangan otomatis hanya untuk Linux.';
            return false;
        }
        $m    = php_uname('m');
        $arch = ['x86_64' => 'x64', 'amd64' => 'x64', 'aarch64' => 'arm64', 'arm64' => 'arm64'][$m] ?? null;
        if (! $arch) {
            $this->log[] = "[GAGAL] Arsitektur '{$m}' tidak didukung.";
            return false;
        }
        $ver  = $this->cfg['node_version'];
        $base = "https://nodejs.org/dist/latest-v{$ver}.x";
        $dir  = $this->localNodeDir();
        $sh   = 'set -e; '
            . 'command -v curl >/dev/null || { echo "curl tidak ada"; exit 3; }; '
            . 'command -v xz >/dev/null || { echo "xz tidak ada (paket xz-utils)"; exit 3; }; '
            . 'F=$(curl -fsSL ' . escapeshellarg($base . '/SHASUMS256.txt')
            . ' | grep -o ' . escapeshellarg("node-v[0-9.]*-linux-{$arch}\.tar\.xz") . ' | head -1); '
            . '[ -n "$F" ] || { echo "berkas Node tidak ditemukan"; exit 4; }; '
            . 'echo "Mengunduh $F"; '
            . 'rm -rf ' . escapeshellarg($dir . '.tmp') . '; mkdir -p ' . escapeshellarg($dir . '.tmp') . '; '
            . 'curl -fsSL ' . escapeshellarg($base . '/') . '"$F" | tar -xJ -C ' . escapeshellarg($dir . '.tmp') . ' --strip-components=1; '
            . 'rm -rf ' . escapeshellarg($dir) . '; mv ' . escapeshellarg($dir . '.tmp') . ' ' . escapeshellarg($dir);
        [, $c] = $this->step(['/bin/sh', '-c', $sh], 600, "Pasang Node {$ver} LTS ke storage/framework/node");
        if ($c !== 0) {
            return false;
        }
        $this->step([$dir . '/bin/node', '-v']);
        $this->step([$dir . '/bin/node', $dir . '/lib/node_modules/npm/bin/npm-cli.js', '-v']);
        $this->log[] = '✔ Node lokal terpasang. Deployer otomatis memakainya untuk npm (tidak perlu ubah .env).';
        return true;
    }

    /** Mundurkan kode ke revisi tertentu (git reset --hard). */
    public function rollbackTo(string $rev): bool
    {
        if (! $this->lock()) {
            return false;
        }
        try {
            [$prev]          = $this->gitOut(['rev-parse', 'HEAD']);
            [$target, $code] = $this->gitOut(['rev-parse', '--verify', $rev . '^{commit}']);
            if ($code !== 0) {
                $this->log[] = "[GAGAL] Revisi '{$rev}' tidak ditemukan.";
                return false;
            }
            $this->log[] = 'Sebelum: ' . substr($prev, 0, 7);
            [, $c] = $this->git(['reset', '--hard', $target]);
            if ($c !== 0) {
                return false;
            }
            [$now] = $this->gitOut(['rev-parse', 'HEAD']);
            $ok    = $this->applyChanges($prev, $now, ['migrate' => false]);
            $this->saveState($prev, $now);
            $this->log[] = 'Sesudah: ' . substr($now, 0, 7);
            $this->log[] = '[CATATAN] Migrasi database TIDAK ikut dimundurkan (gunakan "Rollback Migrasi" bila perlu). '
                . 'Deploy berikutnya akan kembali maju ke commit terbaru GitHub.';
            return $ok;
        } finally {
            $this->unlock();
        }
    }

    /** Cek commit baru di GitHub tanpa mengubah apa pun. */
    public function check(): bool
    {
        $ref = $this->cfg['remote'] . '/' . $this->cfg['branch'];
        [, $c] = $this->git(['fetch', $this->cfg['remote'], $this->cfg['branch']]);
        if ($c !== 0) {
            return false;
        }
        [$cnt] = $this->gitOut(['rev-list', '--count', "HEAD..{$ref}"]);
        if ((int) $cnt === 0) {
            $this->log[] = '✔ Server sudah menjalankan versi terbaru.';
            return true;
        }
        $this->log[] = "Ada {$cnt} commit baru menunggu di GitHub:";
        [$list] = $this->gitOut(['log', '--oneline', '--no-decorate', '--max-count=20', "HEAD..{$ref}"]);
        $this->log[] = $list;
        [$files] = $this->gitOut(['diff', '--name-status', 'HEAD', $ref]);
        $lines   = $files === '' ? [] : explode("\n", $files);
        $this->log[] = "\nFile berubah (" . count($lines) . '):';
        $this->log[] = implode("\n", array_slice($lines, 0, 40)) . (count($lines) > 40 ? "\n… dan " . (count($lines) - 40) . ' lainnya' : '');
        return true;
    }

    /** @return array<string,mixed> */
    public function telemetry(): array
    {
        [$branch] = $this->gitOut(['branch', '--show-current']);
        [$head]   = $this->gitOut(['log', '-1', '--pretty=format:%h|%an|%ar|%s']);
        [$dirty]  = $this->gitOut(['status', '--porcelain', '--untracked-files=no']);
        [$recent] = $this->gitOut(['log', '-6', '--pretty=format:%h|%an|%ar|%s']);
        [$remote] = $this->gitOut(['remote', 'get-url', $this->cfg['remote']]);

        $p = explode('|', $head, 4) + [null, null, null, null];
        $commits = [];
        foreach ($recent === '' ? [] : explode("\n", $recent) as $l) {
            $x = explode('|', $l, 4);
            if (count($x) === 4) {
                $commits[] = $x;
            }
        }
        $free = @disk_free_space($this->root);

        return [
            'branch'  => $branch ?: '(detached)',
            'head'    => $p,
            'dirty'   => $dirty === '' ? [] : explode("\n", $dirty),
            'commits' => $commits,
            'remote'  => $remote,
            'down'    => $this->isDown(),
            'linked'  => is_link($this->root . '/public/storage') || is_dir($this->root . '/public/storage'),
            'disk'    => $free !== false ? round($free / 1073741824, 2) : null,
            'composer' => $this->composerCmd() !== null,
            'npm'      => $this->npmBinary() !== null,
        ];
    }
}

// -------------------------------------------------------------------------
// Throttle login (file JSON, per IP): 5 gagal -> kunci 15 menit
// -------------------------------------------------------------------------
function throttle_path(): string
{
    $dir = DEPLOY_ROOT !== '' ? DEPLOY_ROOT . '/storage/framework' : '';
    return ($dir !== '' && is_dir($dir) && is_writable($dir) ? $dir : sys_get_temp_dir()) . '/deployer_throttle.json';
}

function throttle_load(): array
{
    $d = @json_decode((string) @file_get_contents(throttle_path()), true);
    return is_array($d) ? $d : [];
}

function throttle_locked_for(string $ip): int
{
    $r = throttle_load()[$ip] ?? null;
    return ($r && ($r['until'] ?? 0) > time()) ? (int) $r['until'] - time() : 0;
}

function throttle_fail(string $ip): void
{
    $d = throttle_load();
    $r = $d[$ip] ?? ['n' => 0, 'first' => time()];
    if (time() - ($r['first'] ?? 0) > 900) {
        $r = ['n' => 0, 'first' => time()];
    }
    $r['n']++;
    if ($r['n'] >= 5) {
        $r = ['n' => 0, 'first' => time(), 'until' => time() + 900];
    }
    $d[$ip] = $r;
    foreach ($d as $k => $v) { // buang catatan basi
        if (($v['until'] ?? 0) < time() && time() - ($v['first'] ?? 0) > 900) {
            unset($d[$k]);
        }
    }
    @file_put_contents(throttle_path(), json_encode($d), LOCK_EX);
}

function throttle_clear(string $ip): void
{
    $d = throttle_load();
    unset($d[$ip]);
    @file_put_contents(throttle_path(), json_encode($d), LOCK_EX);
}

// -------------------------------------------------------------------------
// Bootstrap
// -------------------------------------------------------------------------
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');

$ROOT = detect_root();
define('DEPLOY_ROOT', $ROOT ?? '');
Env::load($ROOT);

$branch = Env::get('DEPLOY_BRANCH', 'main');
$remote = Env::get('DEPLOY_REMOTE', 'origin');
$cfg = [
    'key'            => Env::get('DEPLOY_KEY'),
    'key_hash'       => Env::get('DEPLOY_KEY_HASH'),
    'branch'         => preg_match('#^[A-Za-z0-9._/-]+$#', $branch) ? $branch : 'main',
    'remote'         => preg_match('#^[A-Za-z0-9._-]+$#', $remote) ? $remote : 'origin',
    'webhook_secret' => Env::get('DEPLOY_WEBHOOK_SECRET'),
    'allowed_ips'    => array_filter(array_map('trim', explode(',', Env::get('DEPLOY_ALLOWED_IPS')))),
    'auto_migrate'   => Env::bool('DEPLOY_AUTO_MIGRATE', true),
    'maintenance'    => Env::bool('DEPLOY_MAINTENANCE', false),
    'warmup'         => Env::bool('DEPLOY_WARMUP', false),
    'build_assets'   => Env::bool('DEPLOY_BUILD_ASSETS', false),
    'node_version'   => preg_match('/^\d{2}$/', Env::get('DEPLOY_NODE_VERSION', '22')) ? Env::get('DEPLOY_NODE_VERSION', '22') : '22',
    'allow_terminal' => Env::bool('DEPLOY_ALLOW_TERMINAL', false),
    'allow_fresh'    => Env::bool('DEPLOY_ALLOW_FRESH', false),
];

$execOk    = function_exists('proc_open');
$D         = ($ROOT && $execOk) ? new Deployer($ROOT, $cfg) : null;
$keyReady  = ($cfg['key_hash'] !== '') || (strlen($cfg['key']) >= 12);
$clientIp  = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

// -------------------------------------------------------------------------
// Webhook GitHub  (POST deploy.php?hook=1) — tanpa sesi, dilindungi HMAC
// -------------------------------------------------------------------------
if (isset($_GET['hook'])) {
    header('Content-Type: text/plain; charset=utf-8');

    if ($cfg['webhook_secret'] === '' || ! $D) {
        http_response_code(404);
        exit('Webhook tidak aktif.');
    }
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        http_response_code(405);
        exit('Method tidak diizinkan.');
    }

    $payload = (string) file_get_contents('php://input');
    $sig     = (string) ($_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '');
    $calc    = 'sha256=' . hash_hmac('sha256', $payload, $cfg['webhook_secret']);
    if (! hash_equals($calc, $sig)) {
        http_response_code(401);
        $D->audit($clientIp, 'webhook', false, 'signature tidak valid');
        exit('Signature tidak valid.');
    }

    $event = (string) ($_SERVER['HTTP_X_GITHUB_EVENT'] ?? '');
    if ($event === 'ping') {
        exit('pong');
    }
    $data = json_decode($payload, true);
    if ($event !== 'push' || ($data['ref'] ?? '') !== 'refs/heads/' . $cfg['branch']) {
        http_response_code(202);
        exit('Diabaikan (bukan push ke branch ' . $cfg['branch'] . ').');
    }

    // Balas GitHub secepatnya (batas 10 detik), lanjutkan deploy di latar belakang.
    ignore_user_abort(true);
    set_time_limit(0);
    $msg = 'Deploy dimulai.';
    http_response_code(202);
    header('Connection: close');
    header('Content-Length: ' . strlen($msg));
    echo $msg;
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    } else {
        while (ob_get_level() > 0) {
            @ob_end_flush();
        }
        flush();
    }

    $ok = $D->deploy([
        'migrate'     => $cfg['auto_migrate'],
        'maintenance' => $cfg['maintenance'],
        'warmup'      => $cfg['warmup'],
    ]);
    $D->audit('webhook', 'deploy ' . substr((string) ($data['after'] ?? ''), 0, 7), $ok, (string) ($data['head_commit']['message'] ?? ''));
    exit;
}

// -------------------------------------------------------------------------
// Halaman web: pembatasan IP, sesi, CSRF
// -------------------------------------------------------------------------
if ($cfg['allowed_ips'] && ! in_array($clientIp, $cfg['allowed_ips'], true)) {
    http_response_code(403);
    exit('Akses ditolak.');
}

$https = (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? '') === '443');
session_name('gurukuu_deploy');
session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'httponly' => true, 'samesite' => 'Strict', 'secure' => $https]);
session_start();

if (! empty($_SESSION['auth']) && time() - (int) ($_SESSION['last'] ?? 0) > DEPLOY_SESSION_IDLE) {
    $_SESSION = [];
    session_regenerate_id(true);
}
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$self   = strtok($_SERVER['REQUEST_URI'] ?? 'deploy.php', '?') ?: 'deploy.php';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = $method === 'POST' ? (string) ($_POST['action'] ?? '') : '';
$loginError = null;

if ($method === 'POST') {
    $tokenOk = hash_equals((string) $_SESSION['csrf'], (string) ($_POST['_csrf'] ?? ''));
    if (! $tokenOk) {
        http_response_code(419);
        exit('Sesi/CSRF token tidak valid. Muat ulang halaman.');
    }
}

// ---- Login -----------------------------------------------------------------
if ($action === 'login' && $keyReady) {
    $wait = throttle_locked_for($clientIp);
    if ($wait > 0) {
        $loginError = 'Terlalu banyak percobaan gagal. Coba lagi dalam ' . ceil($wait / 60) . ' menit.';
    } else {
        $input = (string) ($_POST['key'] ?? '');
        $valid = $cfg['key_hash'] !== ''
            ? password_verify($input, $cfg['key_hash'])
            : hash_equals($cfg['key'], $input);

        if ($valid) {
            throttle_clear($clientIp);
            session_regenerate_id(true);
            $_SESSION['auth']  = true;
            $_SESSION['last']  = time();
            $_SESSION['csrf']  = bin2hex(random_bytes(32));
            $D?->audit($clientIp, 'login', true);
            header('Location: ' . $self);
            exit;
        }
        throttle_fail($clientIp);
        $D?->audit($clientIp, 'login', false);
        usleep(600000);
        $loginError = 'Kunci tidak valid.';
    }
}

// ---- Logout ----------------------------------------------------------------
if ($action === 'logout') {
    $_SESSION = [];
    session_regenerate_id(true);
    header('Location: ' . $self);
    exit;
}

$auth = ! empty($_SESSION['auth']);
if ($auth) {
    $_SESSION['last'] = time();
}

// -------------------------------------------------------------------------
// Aksi (hanya terautentikasi, POST + CSRF) -> hasil disimpan ke sesi (PRG)
// -------------------------------------------------------------------------
if ($auth && $D && $method === 'POST' && ! in_array($action, ['', 'login', 'logout'], true)) {
    $t0        = microtime(true);
    $title     = 'Aksi';
    $ok        = true;
    $auditNote = '';

    // Daftar seeder dari folder database/seeders (whitelist)
    $seeders = [];
    foreach (glob($ROOT . '/database/seeders/*.php') ?: [] as $f) {
        $seeders[] = basename($f, '.php');
    }

    switch ($action) {
        case 'check':
            $title = 'Cek Pembaruan dari GitHub';
            $ok    = $D->check();
            break;

        case 'deploy':
            $title = 'Deploy: Pull + Update Server';
            $ok    = $D->deploy([
                'migrate'     => isset($_POST['migrate']),
                'maintenance' => isset($_POST['maintenance']),
                'warmup'      => isset($_POST['warmup']),
            ]);
            break;

        case 'force_sync':
            $title = 'Force Sync (samakan persis dengan GitHub)';
            $ok    = $D->deploy(['force' => true, 'migrate' => isset($_POST['migrate']), 'maintenance' => false]);
            break;

        case 'rollback_prev':
            $title = 'Kembali ke Versi Sebelum Deploy Terakhir';
            $prev  = (string) ($D->state()['previous'] ?? '');
            if (! preg_match('/^[0-9a-f]{40}$/', $prev)) {
                $D->log[] = 'Belum ada catatan deploy sebelumnya.';
                $ok       = false;
            } else {
                $ok = $D->rollbackTo($prev);
            }
            break;

        case 'undo_steps':
            $n     = max(1, min(5, (int) ($_POST['undo_steps'] ?? 1)));
            $title = "Undo {$n} Commit (HEAD~{$n})";
            $ok    = $D->rollbackTo("HEAD~{$n}");
            break;

        case 'composer':
            $title = 'Composer Install (--no-dev)';
            $cmd   = $D->composerCmd();
            if (! $cmd) {
                $D->log[] = 'Composer tidak ditemukan di server.';
                $ok       = false;
            } else {
                [, $c] = $D->step(array_merge($cmd, ['install', '--no-dev', '--optimize-autoloader', '--no-interaction', '--no-progress']), 900);
                $ok    = $c === 0;
            }
            break;

        case 'npm_build':
            $title = 'Build Aset (npm run build)';
            $ok    = $D->buildAssets(isset($_POST['npm_install']));
            break;

        case 'node_install':
            $title = 'Pasang Node Lokal';
            $ok    = $D->installNode();
            break;

        case 'node_status':
            $title = 'Status Node & npm';
            $npm   = $D->npmBinary();
            $D->step([$D->nodeBinary(), '-v']);
            $D->log[] = 'npm: ' . ($npm ?: '(tidak ditemukan)');
            if ($npm) {
                $D->step([$npm, '-v']);
            }
            $ok = $npm !== null && $D->nodeMajor() >= 18;
            break;

        case 'migrate':
            $title = 'Database Migrate';
            [, $c] = $D->artisan(['migrate', '--force']);
            $ok    = $c === 0;
            break;

        case 'migrate_status':
            $title = 'Status Migrasi';
            [, $c] = $D->artisan(['migrate:status']);
            $ok    = $c === 0;
            break;

        case 'migrate_rollback':
            $step  = max(1, min(5, (int) ($_POST['rollback_step'] ?? 1)));
            $title = "Rollback Migrasi ({$step} batch)";
            [, $c] = $D->artisan(['migrate:rollback', '--step=' . $step, '--force']);
            $ok    = $c === 0;
            break;

        case 'migrate_fresh':
            $title = 'Migrate Fresh' . (isset($_POST['with_seed']) ? ' + Seed' : '');
            if (! $cfg['allow_fresh']) {
                $D->log[] = '[DITOLAK] migrate:fresh dinonaktifkan (DEPLOY_ALLOW_FRESH=false).';
                $ok       = false;
                break;
            }
            if (trim((string) ($_POST['confirm_text'] ?? '')) !== 'FRESH') {
                $D->log[] = '[DITOLAK] Ketik FRESH pada kolom konfirmasi untuk melanjutkan.';
                $ok       = false;
                break;
            }
            $args = ['migrate:fresh', '--force'];
            if (isset($_POST['with_seed'])) {
                $class = (string) ($_POST['fresh_seeder'] ?? '');
                if ($class === '' || $class === 'DatabaseSeeder') {
                    $args[] = '--seed';
                } elseif (in_array($class, $seeders, true)) {
                    $args[] = '--seeder=' . $class;
                } else {
                    $D->log[] = 'Seeder tidak dikenal.';
                    $ok       = false;
                    break;
                }
            }
            [, $c]     = $D->artisan($args, 600);
            $ok        = $c === 0;
            $auditNote = implode(' ', $args);
            break;

        case 'seed':
            $class = (string) ($_POST['seeder_class'] ?? '');
            $title = "Seeder ({$class})";
            if (! in_array($class, $seeders, true)) {
                $D->log[] = 'Seeder tidak dikenal.';
                $ok       = false;
            } else {
                [, $c] = $D->artisan(['db:seed', '--class=' . $class, '--force']);
                $ok    = $c === 0;
            }
            break;

        case 'terminal':
            $title = 'Terminal';
            if (! $cfg['allow_terminal']) {
                $D->log[] = '[DITOLAK] Terminal nonaktif. Set DEPLOY_ALLOW_TERMINAL=true di .env server.';
                $ok       = false;
                break;
            }
            $raw = trim((string) ($_POST['cmd'] ?? ''));
            if ($raw === '' || strlen($raw) > 2000) {
                $D->log[] = 'Perintah kosong atau terlalu panjang (maks 2000 karakter).';
                $ok       = false;
                break;
            }
            $timeout = max(10, min(900, (int) ($_POST['cmd_timeout'] ?? 120)));

            // Shortcut: "artisan ..." dan "php ..." memakai PHP CLI yang benar
            $run = $raw;
            if (preg_match('/^artisan(\s|$)/', $run)) {
                $run = escapeshellarg($D->phpBinary()) . ' ' . $run;
            } elseif (preg_match('/^php(\s|$)/', $run)) {
                $run = escapeshellarg($D->phpBinary()) . substr($run, 3);
            }

            $shell = PHP_OS_FAMILY === 'Windows' ? ['cmd', '/c', $run] : ['/bin/sh', '-c', $run];
            [, $c]     = $D->step($shell, $timeout, $raw);
            $ok        = $c === 0;
            $auditNote = mb_strimwidth($raw, 0, 200, '…');
            break;

        case 'sipintu_sync':
            $title = 'Sinkron SiPintu (Jurusan, Kelas, Guru, Siswa)';
            $args  = ['sipintu:sync-all'];
            if (isset($_POST['all_students'])) {
                $args[] = '--all-students';
            }
            [, $c]     = $D->artisan($args, 900);
            $ok        = $c === 0;
            $auditNote = implode(' ', $args);
            break;

        case 'cache_clear':
            $title = 'Bersihkan Semua Cache';
            [, $c] = $D->artisan(['optimize:clear']);
            $ok    = $c === 0;
            break;

        case 'cache_optimize':
            $title = 'Optimasi Cache (warmup)';
            [, $c] = $D->artisan(['optimize']);
            $ok    = $c === 0;
            break;

        case 'storage_link':
            $title = 'Storage Link';
            [, $c] = $D->artisan(['storage:link']);
            $ok    = $c === 0;
            break;

        case 'maintenance_toggle':
            if ($D->isDown()) {
                $title = 'Aktifkan Website (artisan up)';
                [, $c] = $D->artisan(['up']);
            } else {
                $title  = 'Matikan Website Sementara (artisan down)';
                $secret = bin2hex(random_bytes(6));
                [, $c]  = $D->artisan(['down', '--secret=' . $secret]);
                $D->log[] = 'URL bypass untuk Anda: /' . $secret;
            }
            $ok = $c === 0;
            break;

        default:
            $title    = 'Perintah tidak dikenal';
            $D->log[] = 'Aksi tidak valid.';
            $ok       = false;
    }

    $log = implode("\n\n", $D->log);
    if (strlen($log) > 30000) {
        $log = '… (dipotong) …' . substr($log, -30000);
    }
    $D->audit($clientIp, $action, $ok, $auditNote);

    $_SESSION['flash'] = ['title' => $title, 'log' => $log, 'ok' => $ok, 'time' => round(microtime(true) - $t0, 2)];
    header('Location: ' . $self);
    exit;
}

$flash = null;
if ($auth && isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

// -------------------------------------------------------------------------
// Tampilan
// -------------------------------------------------------------------------
$csrf = (string) $_SESSION['csrf'];

function post_form(string $action, string $label, string $cls = '', string $extra = '', string $confirm = '', string $style = ''): string
{
    global $csrf;
    $on = $confirm !== '' ? ' onsubmit="return confirm(' . e(json_encode($confirm, JSON_UNESCAPED_UNICODE)) . ')"' : '';
    return '<form method="POST" action=""' . $on . ($style ? ' style="' . e($style) . '"' : '') . '>'
        . '<input type="hidden" name="_csrf" value="' . e($csrf) . '">'
        . '<input type="hidden" name="action" value="' . e($action) . '">'
        . $extra
        . '<button type="submit" class="btn ' . e($cls) . '">' . e($label) . '</button></form>';
}

$T       = ($auth && $D) ? $D->telemetry() : null;
$state   = ($auth && $D) ? $D->state() : [];
$hookUrl = ($https ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'domain-anda') . $self . '?hook=1';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>GuruKuu Deployer</title>
<style>
:root{--bg:#0b0f17;--card:#121826;--inner:#182033;--line:#263149;--tx:#e8edf7;--mut:#94a3b8;--dim:#64748b;--acc:#2563eb;--acc2:#1d4ed8;--ok:#10b981;--warn:#f59e0b;--bad:#ef4444}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;background:var(--bg);color:var(--tx);min-height:100vh;line-height:1.5;padding:1.25rem 1rem 3rem}
.wrap{max-width:1040px;margin:0 auto}
.mono,code,pre{font-family:ui-monospace,"JetBrains Mono",Menlo,Consolas,monospace}
code{background:#0b1220;padding:.05rem .35rem;border-radius:4px;font-size:.78rem}
.top{display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;background:var(--card);border:1px solid var(--line);border-radius:8px;padding:.8rem 1.1rem;margin-bottom:1.1rem}
.brand{font-weight:800;font-size:1.05rem}.brand span{color:var(--acc)}
.sub{font-size:.72rem;color:var(--mut);text-transform:uppercase;letter-spacing:.05em}
.row{display:flex;gap:.5rem;align-items:center;flex-wrap:wrap}
.badge{display:inline-block;font-size:.68rem;font-weight:700;padding:.15rem .55rem;border-radius:99px;border:1px solid;text-transform:uppercase;letter-spacing:.04em}
.b-ok{color:#34d399;border-color:rgba(16,185,129,.35);background:rgba(16,185,129,.1)}
.b-warn{color:#fbbf24;border-color:rgba(245,158,11,.35);background:rgba(245,158,11,.1)}
.b-bad{color:#f87171;border-color:rgba(239,68,68,.4);background:rgba(239,68,68,.12)}
.b-info{color:#7dd3fc;border-color:rgba(14,165,233,.35);background:rgba(14,165,233,.1)}
.btn{display:inline-flex;align-items:center;justify-content:center;font-size:.82rem;font-weight:600;padding:.5rem .95rem;border-radius:5px;border:1px solid var(--line);background:transparent;color:var(--mut);cursor:pointer;text-decoration:none;font-family:inherit}
.btn:hover{background:var(--inner);color:#fff}
.btn.pri{background:var(--acc);border-color:var(--acc2);color:#fff}.btn.pri:hover{background:var(--acc2)}
.btn.warn{color:#fbbf24;border-color:rgba(245,158,11,.4)}.btn.warn:hover{background:rgba(245,158,11,.12)}
.btn.bad{color:#f87171;border-color:rgba(239,68,68,.4)}.btn.bad:hover{background:rgba(239,68,68,.12)}
.btn.grn{color:#34d399;border-color:rgba(16,185,129,.4)}.btn.grn:hover{background:rgba(16,185,129,.12)}
.btn.full,form.full .btn{width:100%}
.grid3{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:.9rem;margin-bottom:1.1rem}
.tel{background:var(--card);border:1px solid var(--line);border-radius:8px;padding:.9rem 1rem;border-top:2px solid var(--acc)}
.tel.ok{border-top-color:var(--ok)}.tel.warn{border-top-color:var(--warn)}
.tl{font-size:.68rem;color:var(--mut);text-transform:uppercase;letter-spacing:.05em;font-weight:700;display:flex;justify-content:space-between;gap:.5rem;margin-bottom:.3rem}
.tv{font-weight:700;font-size:.92rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.ts{font-size:.72rem;color:var(--dim);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.panel{background:var(--card);border:1px solid var(--line);border-radius:8px;padding:1.25rem;margin-bottom:1.1rem}
.ph{margin-bottom:1rem;padding-bottom:.75rem;border-bottom:1px solid rgba(255,255,255,.06)}
.pt{font-weight:700;font-size:.95rem}.pd{font-size:.8rem;color:var(--mut);margin-top:.15rem}
.acts{display:grid;grid-template-columns:repeat(auto-fit,minmax(290px,1fr));gap:1rem}
.card{background:var(--inner);border:1px solid rgba(255,255,255,.06);border-radius:6px;padding:1rem;display:flex;flex-direction:column;gap:.7rem}
.ct{font-weight:700;font-size:.88rem}.cd{font-size:.77rem;color:var(--mut)}
.sel,.inp{width:100%;background:#0b0f17;border:1px solid var(--line);border-radius:5px;padding:.5rem .7rem;color:#fff;font-size:.82rem;font-family:inherit;margin-bottom:.6rem}
.inp:focus,.sel:focus{outline:none;border-color:var(--acc)}
.chk{display:flex;gap:.5rem;align-items:center;font-size:.8rem;color:var(--mut);margin:.15rem 0}
.term{background:#05070c;border:1px solid var(--line);border-radius:8px;margin-bottom:1.1rem;overflow:hidden}
.termh{background:#0c111b;border-bottom:1px solid var(--line);padding:.55rem 1rem;font-size:.75rem;font-weight:600;color:var(--mut);display:flex;justify-content:space-between;gap:1rem;flex-wrap:wrap}
.termc{padding:1rem 1.1rem;font-size:.8rem;line-height:1.6;white-space:pre-wrap;word-break:break-all;max-height:420px;overflow:auto}
.termc.ok{color:#34d399}.termc.bad{color:#fca5a5}
table{width:100%;border-collapse:collapse;font-size:.8rem}
th{text-align:left;padding:.5rem .8rem;font-size:.68rem;color:var(--dim);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--line)}
td{padding:.55rem .8rem;border-bottom:1px solid rgba(255,255,255,.04)}
.hash{color:#7dd3fc;font-weight:600}
.alert{padding:.7rem 1rem;border-radius:5px;font-size:.82rem;margin-bottom:1rem;border:1px solid}
.alert.bad{background:rgba(239,68,68,.12);border-color:rgba(239,68,68,.4);color:#fca5a5}
.alert.info{background:rgba(14,165,233,.1);border-color:rgba(14,165,233,.3);color:#bae6fd}
.auth{min-height:80vh;display:flex;align-items:center;justify-content:center}
.authc{width:100%;max-width:420px;background:var(--card);border:1px solid var(--line);border-radius:8px;padding:2rem;border-top:3px solid var(--acc)}
</style>
</head>
<body>

<?php if (! $auth): ?>
<div class="auth"><div class="authc">
    <div class="brand" style="margin-bottom:.2rem">GURUKUU <span>DEPLOYER</span></div>
    <div class="sub" style="margin-bottom:1.25rem">Sinkronisasi GitHub &amp; Server</div>

    <?php if (! $ROOT): ?>
        <div class="alert bad">Folder Laravel (file <code>artisan</code>) tidak ditemukan dari lokasi file ini.</div>
    <?php elseif (! $execOk): ?>
        <div class="alert bad">Fungsi <code>proc_open</code> dinonaktifkan di PHP server ini, sehingga deployer tidak bisa menjalankan git/artisan. Aktifkan lewat php.ini atau hubungi penyedia hosting.</div>
    <?php elseif (! $keyReady): ?>
        <div class="alert bad"><b>Deployer dinonaktifkan.</b> Tambahkan <code>DEPLOY_KEY=…</code> (min. 12 karakter, acak) ke file <code>.env</code> di server, lalu muat ulang halaman ini.</div>
    <?php else: ?>
        <?php if ($loginError): ?><div class="alert bad"><?= e($loginError) ?></div><?php endif; ?>
        <form method="POST" action="" autocomplete="off">
            <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
            <input type="hidden" name="action" value="login">
            <label class="sub" style="display:block;margin-bottom:.4rem">Kunci Deploy</label>
            <input type="password" name="key" class="inp" placeholder="Masukkan DEPLOY_KEY" required autofocus autocomplete="off">
            <button type="submit" class="btn pri full" style="padding:.7rem">Masuk</button>
        </form>
    <?php endif; ?>
</div></div>

<?php else: ?>
<div class="wrap">

    <div class="top">
        <div>
            <div class="brand">GURUKUU <span>DEPLOYER</span></div>
            <div class="sub">Push dari lokal → GitHub → server</div>
        </div>
        <div class="row">
            <?php if ($T['down']): ?><span class="badge b-bad">Website DOWN</span><?php endif; ?>
            <span class="badge <?= $cfg['webhook_secret'] !== '' ? 'b-ok' : 'b-warn' ?>">Auto-deploy: <?= $cfg['webhook_secret'] !== '' ? 'aktif' : 'nonaktif' ?></span>
            <a class="btn" href="/" target="_blank" rel="noopener">Lihat Web ↗</a>
            <?= post_form('logout', 'Keluar', 'bad') ?>
        </div>
    </div>

    <div class="grid3">
        <div class="tel">
            <div class="tl"><span>Versi di server</span><span class="badge b-info"><?= e($cfg['remote'] . '/' . $T['branch']) ?></span></div>
            <div class="tv mono">#<?= e($T['head'][0] ?? 'N/A') ?> <span style="font-weight:400;color:var(--mut);font-size:.78rem"><?= e(mb_strimwidth((string) ($T['head'][3] ?? ''), 0, 34, '')) ?></span></div>
            <div class="ts"><?= e(($T['head'][1] ?? '-') . ' • ' . ($T['head'][2] ?? '-')) ?></div>
        </div>
        <div class="tel <?= $T['dirty'] ? 'warn' : 'ok' ?>">
            <div class="tl"><span>Working tree</span>
                <span class="badge <?= $T['dirty'] ? 'b-warn' : 'b-ok' ?>"><?= $T['dirty'] ? count($T['dirty']) . ' file diubah' : 'bersih' ?></span></div>
            <div class="tv" style="font-size:.85rem"><?= $T['dirty'] ? 'Ada file server termodifikasi' : 'Sinkron dengan repository' ?></div>
            <div class="ts">Remote: <span class="mono"><?= e($T['remote'] ?: '-') ?></span></div>
        </div>
        <div class="tel">
            <div class="tl"><span>Server</span><span class="badge <?= $T['linked'] ? 'b-ok' : 'b-info' ?>"><?= $T['linked'] ? 'storage link ok' : 'link belum (fallback aktif)' ?></span></div>
            <div class="tv" style="font-size:.85rem"><?= $T['disk'] !== null ? e((string) $T['disk']) . ' GB ruang kosong' : 'Penyimpanan aktif' ?></div>
            <div class="ts">PHP CLI: <span class="mono"><?= e(basename($D->phpBinary())) ?></span> • composer: <?= $T['composer'] ? 'ada' : 'tidak ada' ?> • npm: <?= $T['npm'] ? 'ada' : 'tidak ada' ?></div>
        </div>
    </div>

    <?php if ($flash): ?>
    <div class="term">
        <div class="termh">
            <span><?= $flash['ok'] ? '✔' : '✖' ?> <?= e($flash['title']) ?></span>
            <span><?= e((string) $flash['time']) ?> detik</span>
        </div>
        <div class="termc <?= $flash['ok'] ? 'ok' : 'bad' ?> mono"><?= e($flash['log']) ?></div>
    </div>
    <?php endif; ?>

    <div class="panel">
        <div class="ph">
            <div class="pt"> Update Server dari GitHub</div>
            <div class="pd">Setelah <code>git push</code> dari komputer lokal, klik Deploy (atau biarkan webhook melakukannya otomatis).</div>
        </div>
        <div class="row" style="align-items:flex-start;gap:1.5rem">
            <div style="flex:1;min-width:260px">
                <form method="POST" action="">
                    <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                    <input type="hidden" name="action" value="deploy">
                    <label class="chk"><input type="checkbox" name="migrate" <?= $cfg['auto_migrate'] ? 'checked' : '' ?>> Jalankan <code>migrate --force</code></label>
                    <label class="chk"><input type="checkbox" name="maintenance" <?= $cfg['maintenance'] ? 'checked' : '' ?>> Mode maintenance selama proses</label>
                    <label class="chk"><input type="checkbox" name="warmup" <?= $cfg['warmup'] ? 'checked' : '' ?>> Cache warmup (<code>artisan optimize</code>)</label>
                    <div class="row" style="margin-top:.8rem">
                        <button type="submit" class="btn pri" style="flex:1">⚡ Deploy Sekarang</button>
                    </div>
                </form>
            </div>
            <div style="flex:1;min-width:260px">
                <?= post_form('check', '🔍 Cek Pembaruan (tanpa mengubah apa pun)', 'full', '', '', 'margin-bottom:.6rem') ?>
                <div class="cd">
                    Webhook GitHub: <code><?= e($hookUrl) ?></code><br>
                    <?php if ($cfg['webhook_secret'] === ''): ?>Isi <code>DEPLOY_WEBHOOK_SECRET</code> di .env untuk mengaktifkan.<?php else: ?>Content type <code>application/json</code>, event <code>push</code>.<?php endif; ?>
                </div>
                <?php if (! empty($state['at'])): ?>
                    <div class="cd" style="margin-top:.4rem">Deploy terakhir: <?= e(date('d M Y H:i', strtotime((string) $state['at']))) ?> (<?= e(substr((string) $state['previous'], 0, 7)) ?> → <?= e(substr((string) $state['current'], 0, 7)) ?>)</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="ph"><div class="pt">🛠️ Aksi Lanjutan</div><div class="pd">Pemulihan, basis data, cache, dan pemeliharaan.</div></div>
        <div class="acts">

            <div class="card">
                <div><div class="ct">↩️ Rollback Kode</div>
                <div class="cd">Kembalikan kode server. Migrasi database tidak ikut dimundurkan.</div></div>
                <?= post_form('rollback_prev', 'Kembali ke versi sebelum deploy terakhir', 'warn full', '', 'Kembalikan kode server ke versi sebelum deploy terakhir?') ?>
                <form method="POST" action="" onsubmit="return confirm('Mundurkan kode server beberapa commit?')">
                    <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                    <input type="hidden" name="action" value="undo_steps">
                    <select name="undo_steps" class="sel"><option value="1">Undo 1 commit (HEAD~1)</option><option value="2">Undo 2 commit</option><option value="3">Undo 3 commit</option></select>
                    <button type="submit" class="btn warn" style="width:100%">Undo Commit</button>
                </form>
            </div>

            <div class="card">
                <div><div class="ct">💥 Force Sync</div>
                <div class="cd">Samakan server persis dengan <code><?= e($cfg['remote'] . '/' . $cfg['branch']) ?></code>. Perubahan manual di server dibuang; <code>.env</code>, <code>storage</code>, <code>public/uploads</code> &amp; file deployer ini aman.</div></div>
                <form method="POST" action="" onsubmit="return confirm('Timpa SEMUA file server agar persis sama dengan GitHub (kecuali .env dan storage)?')">
                    <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                    <input type="hidden" name="action" value="force_sync">
                    <label class="chk"><input type="checkbox" name="migrate" checked> Jalankan migrate</label>
                    <button type="submit" class="btn warn" style="width:100%;margin-top:.5rem">Force Reset &amp; Sync</button>
                </form>
            </div>

            <div class="card">
                <div><div class="ct">️ Database</div><div class="cd">Migrasi &amp; status.</div></div>
                <div class="row">
                    <?= post_form('migrate', 'Migrate', 'pri', '', '', 'flex:1') ?>
                    <?= post_form('migrate_status', 'Status', '', '', '', 'flex:1') ?>
                </div>
                <?= post_form('migrate_rollback', '⚠️ Rollback 1 batch migrasi', 'bad full', '<input type="hidden" name="rollback_step" value="1">', 'PERINGATAN: rollback dapat MENGHAPUS tabel/kolom beserta datanya. Lanjutkan?') ?>
            </div>

            <div class="card">
                <div><div class="ct">🌱 Seeder</div><div class="cd">Isi data awal. Hati-hati di data produksi.</div></div>
                <?php
                $sl = [];
                foreach (glob($ROOT . '/database/seeders/*.php') ?: [] as $f) { $sl[] = basename($f, '.php'); }
                sort($sl);
                ?>
                <?php if ($sl): ?>
                <form method="POST" action="" onsubmit="return confirm('Seeder menambah/mengubah data di database produksi. Lanjutkan?')">
                    <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                    <input type="hidden" name="action" value="seed">
                    <select name="seeder_class" class="sel"><?php foreach ($sl as $s): ?><option value="<?= e($s) ?>"><?= e($s) ?></option><?php endforeach; ?></select>
                    <button type="submit" class="btn grn" style="width:100%">Jalankan Seeder</button>
                </form>
                <?php else: ?><div class="cd">Tidak ada seeder.</div><?php endif; ?>
            </div>

            <div class="card" style="border-color:rgba(239,68,68,.35)">
                <div><div class="ct">☢️ Migrate Fresh</div>
                <div class="cd">Menghapus <b>SEMUA tabel dan data</b>, lalu migrasi ulang dari awal. Jangan dipakai di produksi yang berisi data asli.</div></div>
                <?php if (! $cfg['allow_fresh']): ?>
                    <div class="cd">Dinonaktifkan secara bawaan. Set <code>DEPLOY_ALLOW_FRESH=true</code> di <code>.env</code> server bila benar-benar perlu.</div>
                <?php else: ?>
                <form method="POST" action="" onsubmit="return confirm('SEMUA DATA DATABASE AKAN DIHAPUS. Yakin melanjutkan?')">
                    <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                    <input type="hidden" name="action" value="migrate_fresh">
                    <label class="chk"><input type="checkbox" name="with_seed" checked> Jalankan seeder (<code>--seed</code>)</label>
                    <select name="fresh_seeder" class="sel" style="margin-top:.4rem">
                        <option value="DatabaseSeeder">DatabaseSeeder (default)</option>
                        <?php foreach ($sl as $s): if ($s === 'DatabaseSeeder') { continue; } ?>
                            <option value="<?= e($s) ?>"><?= e($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" name="confirm_text" class="inp" placeholder="Ketik FRESH untuk konfirmasi" autocomplete="off" required>
                    <button type="submit" class="btn bad" style="width:100%">Migrate Fresh</button>
                </form>
                <?php endif; ?>
            </div>

            <div class="card">
                <div><div class="ct">🔄 Sinkron SiPintu</div>
                <div class="cd">Jalankan <code>sipintu:sync-all</code>: tarik jurusan, kelas, guru &amp; siswa aktif dari SiPintu Gateway. Opsi <code>--clean</code> sengaja tidak disediakan di sini.</div></div>
                <form method="POST" action="" onsubmit="return confirm('Jalankan sinkronisasi dari SiPintu Gateway sekarang? (bisa memakan waktu beberapa menit)')">
                    <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                    <input type="hidden" name="action" value="sipintu_sync">
                    <label class="chk"><input type="checkbox" name="all_students"> Sertakan siswa tidak aktif (<code>--all-students</code>)</label>
                    <button type="submit" class="btn grn" style="width:100%;margin-top:.5rem">Sinkron Sekarang</button>
                </form>
            </div>

            <div class="card">
                <div><div class="ct">🧹 Cache &amp; Dependensi</div><div class="cd">Bersihkan cache Laravel atau pasang paket composer.</div></div>
                <div class="row">
                    <?= post_form('cache_clear', 'Clear Cache', '', '', '', 'flex:1') ?>
                    <?= post_form('cache_optimize', 'Warmup', '', '', '', 'flex:1') ?>
                </div>
                <?= post_form('composer', 'Composer install --no-dev', 'full', '', 'Jalankan composer install di server?') ?>
            </div>

            <div class="card">
                <div><div class="ct">📦 Node &amp; Build Aset</div>
                <div class="cd">Jalankan <code>npm run build</code> di server. Node &lt; 18 tidak cukup: pasang Node lokal (tanpa root) ke <code>storage/framework/node</code>.</div></div>
                <form method="POST" action="" onsubmit="return confirm('Jalankan build aset di server? (bisa memakan waktu beberapa menit)')">
                    <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                    <input type="hidden" name="action" value="npm_build">
                    <label class="chk"><input type="checkbox" name="npm_install" checked> <code>npm ci</code> dulu (pasang dependensi)</label>
                    <button type="submit" class="btn pri" style="width:100%;margin-top:.5rem">▶ npm run build</button>
                </form>
                <div class="row">
                    <?= post_form('node_status', 'Cek Node/npm', '', '', '', 'flex:1') ?>
                    <?= post_form('node_install', 'Pasang Node Lokal', 'warn', '', 'Unduh Node.js ' . $cfg['node_version'] . ' LTS dari nodejs.org ke storage/framework/node?', 'flex:1') ?>
                </div>
            </div>

            <div class="card">
                <div><div class="ct">🔗 Storage &amp; Maintenance</div><div class="cd">Symlink storage publik &amp; mode perbaikan.</div></div>
                <div class="row">
                    <?= post_form('storage_link', 'Fix Storage Link', '', '', '', 'flex:1') ?>
                    <?= post_form('maintenance_toggle', $T['down'] ? '🟢 Buka Web (UP)' : '🚧 Matikan (DOWN)', $T['down'] ? 'pri' : 'warn', '', $T['down'] ? '' : 'Website akan menampilkan halaman 503 untuk pengunjung. Lanjutkan?', 'flex:1') ?>
                </div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="ph">
            <div class="pt">⌨️ Terminal</div>
            <div class="pd">Jalankan perintah di folder root proyek. Contoh: <code>artisan route:list</code>, <code>php -v</code>, <code>git log -3</code>, <code>ls -la storage</code>.</div>
        </div>
        <?php if (! $cfg['allow_terminal']): ?>
            <div class="alert info">Terminal nonaktif. Tambahkan <code>DEPLOY_ALLOW_TERMINAL=true</code> ke <code>.env</code> di server untuk mengaktifkan.</div>
        <?php else: ?>
        <form method="POST" action="" onsubmit="return confirm('Jalankan perintah ini di server?')">
            <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
            <input type="hidden" name="action" value="terminal">
            <div class="row" style="align-items:stretch">
                <input type="text" name="cmd" class="inp mono" style="flex:1;min-width:260px;margin-bottom:0"
                       placeholder="artisan about" maxlength="2000" required autocomplete="off" spellcheck="false">
                <select name="cmd_timeout" class="sel" style="width:120px;margin-bottom:0">
                    <option value="60">60 detik</option>
                    <option value="120" selected>120 detik</option>
                    <option value="300">300 detik</option>
                    <option value="900">900 detik</option>
                </select>
                <button type="submit" class="btn pri">▶ Jalankan</button>
            </div>
            <div class="cd" style="margin-top:.5rem">Tanpa TTY: perintah interaktif (yang menunggu input) akan berhenti karena timeout. Tambahkan <code>--no-interaction</code> atau <code>--force</code> bila perlu.</div>
        </form>
        <?php endif; ?>
    </div>

    <div class="panel">
        <div class="ph"><div class="pt">📜 Riwayat Commit di Server</div><div class="pd">6 commit terakhir yang aktif.</div></div>
        <div style="overflow-x:auto"><table>
            <thead><tr><th style="width:90px">Hash</th><th style="width:140px">Author</th><th style="width:120px">Waktu</th><th>Pesan</th></tr></thead>
            <tbody>
            <?php if ($T['commits']): foreach ($T['commits'] as $i => $c): ?>
                <tr><td class="hash mono">#<?= e($c[0]) ?><?= $i === 0 ? ' <span class="badge b-ok">HEAD</span>' : '' ?></td>
                    <td style="color:var(--mut)"><?= e($c[1]) ?></td><td style="color:var(--dim)"><?= e($c[2]) ?></td><td><?= e($c[3]) ?></td></tr>
            <?php endforeach; else: ?>
                <tr><td colspan="4" style="text-align:center;color:var(--mut);padding:1.2rem">Tidak dapat membaca riwayat git.</td></tr>
            <?php endif; ?>
            </tbody></table></div>
    </div>

    <div class="panel">
        <div class="ph"><div class="pt">🧾 Log Aktivitas Deployer</div></div>
        <div class="termc mono" style="background:#05070c;border:1px solid var(--line);border-radius:6px;max-height:220px;color:var(--mut)"><?= e($D->auditTail() ? implode("\n", $D->auditTail()) : 'Belum ada aktivitas.') ?></div>
    </div>

</div>
<?php endif; ?>

</body>
</html>