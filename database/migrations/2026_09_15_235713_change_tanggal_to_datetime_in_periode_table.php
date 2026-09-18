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
        Schema::table('periode', function (Blueprint $table) {
            $table->dateTime('tanggal_mulai')->change();
            $table->dateTime('tanggal_selesai')->change();
        });
    }

    public function down(): void
    {
        Schema::table('periode', function (Blueprint $table) {
            $table->date('tanggal_mulai')->change();
            $table->date('tanggal_selesai')->change();
        });
    }
};
