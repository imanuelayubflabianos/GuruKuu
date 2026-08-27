<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas'); // Contoh: "PPLG 1", "TO 2"
            $table->foreignId('jurusan_id')->constrained('jurusan')->onDelete('cascade');
            $table->enum('tingkat', ['10', '11', '12']);
            $table->integer('jumlah_siswa')->default(36);
            $table->timestamps();
            
            $table->unique(['nama_kelas', 'tingkat']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};