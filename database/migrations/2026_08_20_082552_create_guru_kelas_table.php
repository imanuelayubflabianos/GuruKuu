<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guru_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('guru')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->string('mata_pelajaran')->nullable(); // Mata pelajaran yang diajar
            $table->timestamps();
            
            $table->unique(['guru_id', 'kelas_id', 'mata_pelajaran']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru_kelas');
    }
};