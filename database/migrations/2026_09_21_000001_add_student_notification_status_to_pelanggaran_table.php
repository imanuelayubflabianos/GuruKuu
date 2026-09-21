<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelanggaran', function (Blueprint $table) {
            $table->boolean('siswa_is_read')->default(false)->after('is_read');
            $table->text('notifikasi_siswa')->nullable()->after('tindakan');
        });
    }

    public function down(): void
    {
        Schema::table('pelanggaran', function (Blueprint $table) {
            $table->dropColumn(['siswa_is_read', 'notifikasi_siswa']);
        });
    }
};
