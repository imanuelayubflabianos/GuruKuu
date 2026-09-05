<?php

namespace App\Console\Commands;

use App\Services\SiPintuService;
use Illuminate\Console\Command;

class SiPintuFetchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sipintu:fetch 
                            {type=all : Tipe data yang ingin diambil (guru|siswa|all)}
                            {--nip= : Filter berdasarkan NIP Guru}
                            {--nis= : Filter berdasarkan NIS Siswa}
                            {--search= : Pencarian berdasarkan nama/email}
                            {--sync : Langsung sinkronkan data ke database GuruKuu}
                            {--all-students : Ambil seluruh siswa termasuk yang tidak aktif}
                            {--refresh : Hapus cache dan ambil langsung dari server SiPintu}
                            {--ping : Hanya lakukan uji koneksi/ping ke Gateway SiPintu}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ambil dan tampilkan data Guru & Siswa dari SiPintu Gateway via CLI';

    protected SiPintuService $service;

    public function __construct(SiPintuService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=== SiPintu Identity & API Gateway Client ===");
        $this->line("Base URL   : " . $this->service->getBaseUrl());
        $this->line("Client ID  : " . $this->service->getClientId());
        $this->newLine();

        // 1. Ping Check
        $ping = $this->service->ping();
        if ($ping['success']) {
            $this->info("✔ Status Gateway : ONLINE (" . $ping['latency_ms'] . " ms)");
        } else {
            $this->error("✖ Status Gateway : OFFLINE / ERROR");
            $this->warn("  Pesan: " . $ping['message']);
            if ($this->option('ping')) {
                return Command::FAILURE;
            }
        }

        if ($this->option('ping')) {
            $val = $this->service->validateClient();
            if ($val['success']) {
                $this->info("✔ Kredensial Client ID & Secret : VALID");
            } else {
                $this->error("✖ Validasi Kredensial : GAGAL (" . ($val['message'] ?? 'Ditolak') . ")");
            }
            return Command::SUCCESS;
        }

        $type = strtolower($this->argument('type'));
        $shouldSync = $this->option('sync');

        // 2. Process Guru
        if ($type === 'all' || $type === 'guru') {
            $this->fetchGuru($shouldSync);
        }

        // 3. Process Siswa
        if ($type === 'all' || $type === 'siswa') {
            $this->fetchSiswa($shouldSync);
        }

        return Command::SUCCESS;
    }

    protected function fetchGuru(bool $shouldSync)
    {
        $this->info("\n--- Mengambil Data Guru dari SiPintu ---");
        $params = [];
        if ($nip = $this->option('nip')) {
            $params['nip'] = $nip;
        }
        if ($search = $this->option('search')) {
            $params['search'] = $search;
        }
        if ($this->option('refresh')) {
            $params['refresh'] = true;
        }

        $res = $this->service->getTeachers($params);

        if (!$res['success']) {
            $this->error("Gagal mengambil data guru: " . $res['message']);
            return;
        }

        $teachers = $res['data'];
        $this->info("Ditemukan " . count($teachers) . " data guru.");

        if (empty($teachers)) {
            return;
        }

        $rows = [];
        foreach ($teachers as $t) {
            $nip = $t['nip'] ?? '-';
            $nama = $t['nama'] ?? '-';
            $email = $t['email'] ?? '-';
            $phone = $t['phone'] ?? $t['hp'] ?? '-';
            $kategori = $t['kategori'] ?? '-';
            $rows[] = [$nip, $nama, $email, $phone, $kategori];

            if ($shouldSync) {
                $syncRes = $this->service->syncTeacherToLocal($t);
                $this->line("  -> Sync: " . ($syncRes['message'] ?? 'OK'));
            }
        }

        $this->table(['NIP', 'Nama Guru', 'Email', 'No. HP', 'Kategori'], $rows);
    }

    protected function fetchSiswa(bool $shouldSync)
    {
        $this->info("\n--- Mengambil Data Siswa dari SiPintu ---");
        $params = [];
        if ($nis = $this->option('nis')) {
            $params['nis'] = $nis;
        }
        if ($search = $this->option('search')) {
            $params['search'] = $search;
        }
        if ($this->option('refresh')) {
            $params['refresh'] = true;
        }
        $params['only_active'] = !$this->option('all-students');

        $res = $this->service->getStudents($params);

        if (!$res['success']) {
            $this->error("Gagal mengambil data siswa: " . $res['message']);
            return;
        }

        $students = $res['data'];
        $this->info("Ditemukan " . count($students) . " data siswa aktif.");

        if (empty($students)) {
            return;
        }

        $rows = [];
        $sample = array_slice($students, 0, 15);
        foreach ($sample as $s) {
            $nis = $s['nis'] ?? '-';
            $nama = $s['nama'] ?? '-';
            $email = $s['email'] ?? '-';
            $kelas = $s['kelas'] ?? '-';
            $jurusan = $s['jurusan'] ?? '-';
            $rows[] = [$nis, $nama, $email, $kelas, $jurusan];

            if ($shouldSync) {
                $syncRes = $this->service->syncStudentToLocal($s);
                $this->line("  -> Sync: " . ($syncRes['message'] ?? 'OK'));
            }
        }

        $this->table(['NIS', 'Nama Siswa', 'Email', 'Kelas', 'Jurusan'], $rows);

        if (count($students) > 15) {
            $this->line("... dan " . (count($students) - 15) . " siswa lainnya.");
        }
    }
}
