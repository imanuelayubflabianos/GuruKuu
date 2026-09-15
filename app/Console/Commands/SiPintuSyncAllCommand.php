<?php

namespace App\Console\Commands;

use App\Services\SiPintuService;
use Illuminate\Console\Command;

class SiPintuSyncAllCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sipintu:sync-all
                            {--clean : Bersihkan data lokal lama (guru, siswa non-admin, jurusan, kelas) sebelum sinkron}
                            {--all-students : Ambil seluruh siswa termasuk yang tidak aktif}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi menyeluruh dari SiPintu Gateway: Jurusan, Kelas, Guru, dan Siswa Aktif';

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
        $this->info("=== SiPintu Gateway Full Synchronization ===");
        $clean = (bool) $this->option('clean');
        $onlyActive = !$this->option('all-students');

        if ($clean) {
            $this->warn("⚠ Opsi --clean aktif: Data lokal penilaian, guru, siswa non-admin, jurusan, dan kelas akan dibersihkan.");
        }
        $this->line("Mode Siswa : " . ($onlyActive ? "HANYA SISWA AKTIF" : "SEMUA SISWA"));

        $this->info("\nMemulai proses sinkronisasi dari SiPintu Gateway...");
        $startTime = microtime(true);

        try {
            $result = $this->service->syncAllFromSiPintu($clean, $onlyActive);

            $duration = round(microtime(true) - $startTime, 2);

            if ($result['success']) {
                $this->newLine();
                $this->info("✔ " . $result['message']);
                $this->table(
                    ['Entitas', 'Jumlah Tersinkron'],
                    [
                        ['Guru', $result['guru_count']],
                        ['Siswa Aktif', $result['siswa_count']],
                        ['Jurusan', $result['jurusan_count']],
                        ['Kelas', $result['kelas_count']],
                    ]
                );
                $this->line("Waktu proses: {$duration} detik.");
                return Command::SUCCESS;
            } else {
                $this->error("✖ Gagal: " . ($result['message'] ?? 'Terjadi kesalahan.'));
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error("✖ Terjadi Exception: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
