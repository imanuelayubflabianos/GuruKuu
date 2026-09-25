<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('session_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_name')->nullable();
            $table->string('device_type')->default('Desktop'); // Desktop, Ponsel, Tablet
            $table->string('platform')->nullable(); // Windows, Android, iOS, macOS, Linux
            $table->string('browser')->nullable(); // Chrome, Edge, Safari, Firefox
            $table->string('status')->default('active'); // active, logged_out
            $table->boolean('is_active')->default(true);
            $table->timestamp('login_at')->useCurrent();
            $table->timestamp('last_activity')->useCurrent();
            $table->timestamp('logout_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_histories');
    }
};
