<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('penilaian_balasans')) {
            Schema::create('penilaian_balasans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('penilaian_id')->constrained('penilaian')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->foreignId('parent_id')->nullable()->constrained('penilaian_balasans')->onDelete('cascade');
                $table->text('pesan');
                $table->string('role', 20)->default('siswa'); // 'siswa', 'guru', 'admin'
                $table->boolean('is_anonim')->default(false);
                $table->timestamps();

                $table->index(['penilaian_id', 'created_at']);
            });
        }

        // Migrasi data balasan_guru lama jika ada
        if (Schema::hasColumn('penilaian', 'balasan_guru')) {
            $oldReplies = DB::table('penilaian')
                ->whereNotNull('balasan_guru')
                ->where('balasan_guru', '!=', '')
                ->get(['id', 'guru_id', 'balasan_guru', 'balasan_guru_at', 'created_at']);

            foreach ($oldReplies as $old) {
                $guru = DB::table('guru')->where('id', $old->guru_id)->first();
                $userId = null;
                if ($guru) {
                    $u = DB::table('users')
                        ->where('email', $guru->email)
                        ->orWhere('nis', $guru->nip)
                        ->first(['id']);
                    $userId = $u ? $u->id : null;
                }

                DB::table('penilaian_balasans')->insert([
                    'penilaian_id' => $old->id,
                    'user_id' => $userId,
                    'parent_id' => null,
                    'pesan' => $old->balasan_guru,
                    'role' => 'guru',
                    'is_anonim' => false,
                    'created_at' => $old->balasan_guru_at ?? $old->created_at ?? now(),
                    'updated_at' => $old->balasan_guru_at ?? $old->created_at ?? now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_balasans');
    }
};
