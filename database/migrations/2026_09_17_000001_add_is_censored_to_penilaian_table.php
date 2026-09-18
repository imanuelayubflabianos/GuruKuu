<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penilaian', function (Blueprint $table) {
            if (!Schema::hasColumn('penilaian', 'is_censored')) {
                $table->boolean('is_censored')->default(false)->after('saran');
            }
            if (!Schema::hasColumn('penilaian', 'censored_reason')) {
                $table->string('censored_reason')->nullable()->after('is_censored');
            }
        });
    }

    public function down(): void
    {
        Schema::table('penilaian', function (Blueprint $table) {
            if (Schema::hasColumn('penilaian', 'censored_reason')) {
                $table->dropColumn('censored_reason');
            }
            if (Schema::hasColumn('penilaian', 'is_censored')) {
                $table->dropColumn('is_censored');
            }
        });
    }
};
