<?php
// database/migrations/2026_01_01_000007_create_penghargaan_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penghargaan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('guru')->cascadeOnDelete();
            $table->foreignId('badge_id')->constrained('badge')->cascadeOnDelete();
            $table->foreignId('periode_id')->constrained('periode')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['guru_id', 'badge_id', 'periode_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penghargaan');
    }
};