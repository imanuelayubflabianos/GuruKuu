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
        if (!Schema::hasTable('penilaian_helpfuls')) {
            Schema::create('penilaian_helpfuls', function (Blueprint $table) {
                $table->id();
                $table->foreignId('penilaian_id')->constrained('penilaian')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();

                $table->unique(['penilaian_id', 'user_id']);
                $table->index(['penilaian_id', 'ip_address']);
            });
        }

        if (!Schema::hasColumn('penilaian', 'helpful_count')) {
            Schema::table('penilaian', function (Blueprint $table) {
                $table->unsignedInteger('helpful_count')->default(0)->after('saran');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_helpfuls');

        if (Schema::hasColumn('penilaian', 'helpful_count')) {
            Schema::table('penilaian', function (Blueprint $table) {
                $table->dropColumn('helpful_count');
            });
        }
    }
};
