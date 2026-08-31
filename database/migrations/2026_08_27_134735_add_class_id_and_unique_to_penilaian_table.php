<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penilaian', function (Blueprint $table) {
            // Tambah kolom class_id (nullable agar backward compatible dengan data lama)
            if (!Schema::hasColumn('penilaian', 'class_id')) {
                $table->unsignedBigInteger('class_id')->nullable()->after('periode_id');
                $table->foreign('class_id')->references('id')->on('kelas')->onDelete('cascade');
            }
            
            // Tambah unique constraint: 1 siswa = 1 penilaian per guru per periode
            $table->unique(['siswa_id', 'guru_id', 'periode_id'], 'penilaian_unique_periode');
        });
    }

    public function down(): void
    {
        Schema::table('penilaian', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropColumn('class_id');
            $table->dropUnique('penilaian_unique_periode');
        });
    }
};