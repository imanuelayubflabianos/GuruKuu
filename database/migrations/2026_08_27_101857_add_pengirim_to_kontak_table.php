<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kontak', function (Blueprint $table) {
            if (!Schema::hasColumn('kontak', 'pengirim')) {
                $table->string('pengirim')->nullable()->after('id');
            }
            if (!Schema::hasColumn('kontak', 'identifier')) {
                $table->string('identifier')->nullable()->after('pengirim');
            }
            if (!Schema::hasColumn('kontak', 'is_siswa')) {
                $table->boolean('is_siswa')->default(false)->after('identifier');
            }
            if (!Schema::hasColumn('kontak', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('is_siswa');
            }
            if (!Schema::hasColumn('kontak', 'is_replied')) {
                $table->boolean('is_replied')->default(false)->after('is_read');
            }
        });
    }

    public function down(): void
    {
        Schema::table('kontak', function (Blueprint $table) {
            $table->dropColumn(['pengirim', 'identifier', 'is_siswa', 'is_read', 'is_replied']);
        });
    }
};