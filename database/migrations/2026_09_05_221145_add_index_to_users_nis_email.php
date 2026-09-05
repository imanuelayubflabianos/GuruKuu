<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan index pada kolom nis jika kolomnya ada
            if (Schema::hasColumn('users', 'nis')) {
                $table->index('nis', 'idx_users_nis');
            }

            // Tambahkan index pada kolom email jika kolomnya ada
            if (Schema::hasColumn('users', 'email')) {
                $table->index('email', 'idx_users_email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop index hanya jika index-nya ada (menghindari error saat rollback)
            $sm = Schema::getConnection()->getSchemaBuilder();
            
            if ($sm->hasIndex('users', 'idx_users_nis')) {
                $table->dropIndex('idx_users_nis');
            }

            if ($sm->hasIndex('users', 'idx_users_email')) {
                $table->dropIndex('idx_users_email');
            }
        });
    }
};