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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'deactivation_type')) {
                $table->string('deactivation_type', 30)->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('users', 'deactivated_until')) {
                $table->dateTime('deactivated_until')->nullable()->after('deactivation_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['deactivation_type', 'deactivated_until']);
        });
    }
};
