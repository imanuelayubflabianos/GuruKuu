<?php
// database/migrations/2026_01_01_000003_create_guru_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guru', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique();
            $table->string('nama');
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('photo')->nullable();
            $table->enum('kategori', ['normada', 'produktif']);
            $table->foreignId('jurusan_id')->nullable()
                  ->constrained('jurusan')->nullOnDelete();
            $table->text('bio')->nullable();
            $table->decimal('rata_rata_nilai', 4, 2)->default(0);
            $table->integer('total_penilaian')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru');
    }
};