<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan kolom class_id ada
        if (!Schema::hasColumn('penilaian', 'class_id')) {
            Schema::table('penilaian', function (Blueprint $table) {
                $table->unsignedBigInteger('class_id')->nullable()->after('periode_id');
                $table->foreign('class_id')->references('id')->on('kelas')->onDelete('cascade');
            });
        }

        // Isi class_id berdasarkan relasi siswa -> kelas
        DB::statement("
            UPDATE penilaian p
            INNER JOIN siswa_kelas sk ON p.siswa_id = sk.user_id
            SET p.class_id = sk.kelas_id
            WHERE p.class_id IS NULL
        ");

        // Tambah unique constraint (jika belum ada)
        try {
            Schema::table('penilaian', function (Blueprint $table) {
                $table->unique(['siswa_id', 'guru_id', 'periode_id'], 'penilaian_unique_periode');
            });
        } catch (\Exception $e) {
            // Unique constraint sudah ada, skip
        }
    }

    public function down(): void
    {
        Schema::table('penilaian', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropColumn('class_id');
            try {
                $table->dropUnique('penilaian_unique_periode');
            } catch (\Exception $e) {}
        });
    }
};