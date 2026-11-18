<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Penilaian;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPlusAndWakilRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_plus_can_login_via_guru_tab_and_access_full_admin_features(): void
    {
        $response = $this->post(route('login'), [
            'login_role' => 'guru',
            'nis' => 'admin-plus',
            'password' => 'eskasaba',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
        
        $user = auth()->user();
        $this->assertEquals('admin', $user->role);

        // Can access destructive / sensitive settings & SiPintu
        $settingsResponse = $this->get(route('admin.pengaturan.index'));
        $settingsResponse->assertStatus(200);

        $sipintuResponse = $this->get(route('admin.sipintu.index'));
        $sipintuResponse->assertStatus(200);
    }

    public function test_wakil_can_login_and_access_only_leaderboard_and_student_is_anonymous(): void
    {
        $response = $this->post(route('login'), [
            'login_role' => 'guru',
            'nis' => 'wakil',
            'password' => 'eskasaba123',
        ]);

        $response->assertSessionHasNoErrors();
        // Redirected directly to leaderboard
        $response->assertRedirect(route('admin.leaderboard.index'));
        $this->assertAuthenticated();

        $user = auth()->user();
        $this->assertEquals('wakil', $user->role);

        // Visiting dashboard redirects to leaderboard
        $dashboardResponse = $this->get(route('admin.dashboard'));
        $dashboardResponse->assertRedirect(route('admin.leaderboard.index'));

        // Can access Leaderboard page and exports
        $leaderboardResponse = $this->get(route('admin.leaderboard.index'));
        $leaderboardResponse->assertStatus(200);
        $leaderboardResponse->assertSee('Bagikan Peringkat');

        // FORBIDDEN / REDIRECTED from admin-only pages (Data Guru list, Kritik Saran, Pelanggaran, Pengaturan, SiPintu)
        $guruListResponse = $this->get(route('admin.guru.index'));
        $guruListResponse->assertRedirect(route('admin.leaderboard.index'));

        $feedbackResponse = $this->get(route('admin.kritik-saran.index'));
        $feedbackResponse->assertRedirect(route('admin.leaderboard.index'));

        $pelanggaranResponse = $this->get(route('admin.pelanggaran.index'));
        $pelanggaranResponse->assertRedirect(route('admin.leaderboard.index'));

        $settingsResponse = $this->get(route('admin.pengaturan.index'));
        $settingsResponse->assertRedirect(route('admin.leaderboard.index'));

        $sipintuResponse = $this->get(route('admin.sipintu.index'));
        $sipintuResponse->assertRedirect(route('admin.leaderboard.index'));

        // VERIFY STUDENT ANONYMITY in Teacher Detail
        $periode = Periode::create([
            'nama_periode' => 'Semester Ganjil 2026/2027',
            'tahun_ajaran' => '2026/2027',
            'semester' => 'ganjil',
            'status' => 'aktif',
            'tanggal_mulai' => now()->subMonth(),
            'tanggal_selesai' => now()->addMonth(),
        ]);

        $guru = Guru::create([
            'nama' => 'Budi Santoso, S.Pd.',
            'nip' => '198001012005011001',
            'kategori' => 'normada',
        ]);

        $siswa = User::create([
            'name' => 'Ahmad Siswa Rahasia',
            'nis' => '88889999',
            'email' => 'ahmad@example.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
        ]);

        Penilaian::create([
            'guru_id' => $guru->id,
            'siswa_id' => $siswa->id,
            'periode_id' => $periode->id,
            'kedisiplinan' => 5,
            'komunikasi' => 5,
            'tanggung_jawab' => 5,
            'keramahan' => 5,
            'kreativitas' => 5,
            'total_nilai' => 25,
            'rata_rata_evaluasi' => 5.0,
            'kritik' => 'Kritik membangun dari murid',
            'saran' => 'Tetap semangat mengajar',
            'is_censored' => false,
        ]);

        // Wakil visits teacher detail -> student name & NIS MUST NOT be visible!
        $detailResponseAsWakil = $this->get(route('admin.guru.show', $guru));
        $detailResponseAsWakil->assertStatus(200);
        $detailResponseAsWakil->assertDontSee('Ahmad Siswa Rahasia');
        $detailResponseAsWakil->assertDontSee('NIS: 88889999');
        $detailResponseAsWakil->assertSee('Siswa (Anonim)');

        // Admin visits teacher detail -> real student name & NIS IS visible
        $admin = User::where('role', 'admin')->first();
        $detailResponseAsAdmin = $this->actingAs($admin)->get(route('admin.guru.show', $guru));
        $detailResponseAsAdmin->assertStatus(200);
        $detailResponseAsAdmin->assertSee('Ahmad Siswa Rahasia');
        $detailResponseAsAdmin->assertSee('NIS: 88889999');
    }
}
