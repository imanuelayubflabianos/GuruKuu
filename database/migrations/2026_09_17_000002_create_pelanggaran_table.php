<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pelanggaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tipe', 50)->default('penilaian_toxic'); // penilaian_toxic, kontak_toxic, komentar_disensor, dll.
            $table->foreignId('guru_id')->nullable()->constrained('guru')->nullOnDelete();
            $table->foreignId('penilaian_id')->nullable()->constrained('penilaian')->nullOnDelete();
            $table->text('kata_terdeteksi')->nullable(); // JSON atau teks kata terlarang
            $table->text('isi_teks')->nullable(); // Isi teks yang dimasukkan pengguna
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->string('tindakan', 50)->default('diblokir_otomatis'); // diblokir_otomatis, diberi_peringatan, disensor, dll.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggaran');
    }
};
