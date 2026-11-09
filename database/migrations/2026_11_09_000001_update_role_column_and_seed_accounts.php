<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('siswa')->change();
        });

        // 1. Seed akun admin-plus (password: eskasaba)
        User::updateOrCreate(
            ['nis' => 'admin-plus'],
            [
                'name' => 'Administrator Plus',
                'email' => 'admin-plus@gurukuu.com',
                'password' => Hash::make('eskasaba'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // 2. Seed akun wakil (password: eskasaba123)
        User::updateOrCreate(
            ['nis' => 'wakil'],
            [
                'name' => 'Wakil Kepala Sekolah',
                'email' => 'wakil@gurukuu.com',
                'password' => Hash::make('eskasaba123'),
                'role' => 'wakil',
                'is_active' => true,
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        User::whereIn('nis', ['admin-plus', 'wakil'])->delete();

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'siswa', 'guru'])->default('siswa')->change();
        });
    }
};
