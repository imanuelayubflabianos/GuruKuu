<?php

use App\Models\Periode;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('periode:sync', function () {
    $changed = Periode::syncAllStatuses();
    $statusMsg = $changed ? 'Terjadi perubahan status periode.' : 'Semua periode sudah sesuai jadwal.';
    $this->info("Sinkronisasi otomatis status periode berhasil dijalankan. {$statusMsg}");
})->purpose('Sinkronisasi status aktivasi otomatis periode berdasarkan tanggal mulai & selesai');

Schedule::call(function () {
    Periode::syncAllStatuses();
})->everyMinute()->name('periode-auto-sync');
