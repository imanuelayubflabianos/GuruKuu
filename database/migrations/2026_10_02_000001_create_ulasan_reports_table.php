<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ulasan_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penilaian_id')->constrained('penilaian')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->string('alasan');
            $table->text('catatan')->nullable();
            $table->boolean('is_reviewed')->default(false);
            $table->timestamps();

            // Index pencegahan spam
            $table->index(['penilaian_id', 'user_id']);
            $table->index(['penilaian_id', 'ip_address']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ulasan_reports');
    }
};
