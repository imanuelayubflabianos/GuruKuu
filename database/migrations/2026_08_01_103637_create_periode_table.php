<?php
// database/migrations/2026_01_01_000004_create_periode_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periode', function (Blueprint $table) {
            $table->id();
            $table->string('nama_periode');
            $table->string('tahun_ajaran'); // contoh: 2025/2026
            $table->enum('semester', ['ganjil', 'genap']);
            $table->enum('status', ['aktif', 'nonaktif'])->default('nonaktif');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periode');
    }
};