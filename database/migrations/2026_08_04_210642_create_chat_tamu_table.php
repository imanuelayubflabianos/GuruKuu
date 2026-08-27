<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_tamu', function (Blueprint $table) {
            $table->id();
            $table->string('device_id'); // Identifier unik per device
            $table->string('nama')->nullable(); // Nama opsional
            $table->text('pesan');
            $table->text('balasan')->nullable();
            $table->boolean('is_read')->default(false);
            $table->boolean('is_replied')->default(false);
            $table->timestamps();
            
            $table->index('device_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_tamu');
    }
};