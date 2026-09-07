<?php

namespace Database\Seeders;

use App\Models\Periode;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🔄 Memulai seeding data dasar...');

        // 1. Periode Aktif (WAJIB ADA agar sistem penilaian tidak error)
        Periode::firstOrCreate(
            ['tahun_ajaran' => '2026/2027', 'semester' => 'ganjil'],
            [
                'nama_periode' => 'Semester Ganjil 2026/2027',
                'status' => 'aktif',
                'tanggal_mulai' => '2026-07-01',
                'tanggal_selesai' => '2026-12-31'
            ]
        );

        // 2. Akun ADMIN (Login via tab "Guru / Admin" dengan NIP: admin)
        User::firstOrCreate(
            ['nis' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'admin@gurukuu.com',
                'password' => Hash::make('eskasaba'), // Password sesuai request
                'role' => 'admin',
                'is_active' => true,
            ]
        );

    

        $this->command->info('✅ Seeding data dasar selesai!');
        $this->command->info('💡 Langkah selanjutnya: Login sebagai Admin, lalu sinkronisasi data Guru, Siswa, dan Kelas melalui menu "Gateway SiPintu".');
    }
}