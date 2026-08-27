<?php
// database/migrations/2026_01_01_000006_create_badge_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badge', function (Blueprint $table) {
            $table->id();
            $table->string('nama_badge');
            $table->text('deskripsi')->nullable();
            $table->string('icon')->nullable(); // emoji atau icon class
            $table->string('warna')->nullable(); // hex color
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('badge');
    }
};