<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom warning_count di tabel users
        if (!Schema::hasColumn('users', 'warning_count')) {
            Schema::table('users', function (Blueprint $table) {
                $table->integer('warning_count')->default(0)->after('is_active');
            });
        }

        // Tambah kolom logo di tabel jurusan
        if (!Schema::hasColumn('jurusan', 'logo')) {
            Schema::table('jurusan', function (Blueprint $table) {
                $table->string('logo')->nullable()->after('nama_jurusan');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('warning_count');
        });

        Schema::table('jurusan', function (Blueprint $table) {
            $table->dropColumn('logo');
        });
    }
};