<?php
// database/migrations/2026_01_01_000005_create_penilaian_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('guru')->cascadeOnDelete();
            $table->foreignId('periode_id')->constrained('periode')->cascadeOnDelete();
            $table->tinyInteger('kedisiplinan')->comment('1-5');
            $table->tinyInteger('cara_mengajar')->comment('1-5');
            $table->tinyInteger('komunikasi')->comment('1-5');
            $table->tinyInteger('tanggung_jawab')->comment('1-5');
            $table->tinyInteger('kreativitas')->comment('1-5');
            $table->tinyInteger('keramahan')->comment('1-5');
            $table->decimal('total_nilai', 4, 2);
            $table->text('kritik')->nullable();
            $table->text('saran')->nullable();
            $table->timestamps();

            // Unique: 1 siswa hanya bisa menilai 1 guru per kategori per periode
            $table->unique(['siswa_id', 'guru_id', 'periode_id'], 'penilaian_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian');
    }
};