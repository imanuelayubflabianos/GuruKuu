<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('tanggal_lahir')->nullable()->after('nis');
            $table->boolean('is_active')->default(false)->after('password');
            $table->boolean('force_change_password')->default(true)->after('is_active');
            $table->timestamp('activated_at')->nullable()->after('force_change_password');
        });

        // Buat kolom password menjadi nullable (untuk siswa yang belum diaktifkan)
        DB::statement('ALTER TABLE users MODIFY password VARCHAR(255) NULL');
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['tanggal_lahir', 'is_active', 'force_change_password', 'activated_at']);
        });
        DB::statement('ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL');
    }
};