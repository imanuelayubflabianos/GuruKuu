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

    public function test_teacher_detail_reviews_are_paginated_ten_per_page(): void
    {
        $periode = Periode::create([
            'nama_periode' => 'Semester Ganjil 2026/2027',
            'tahun_ajaran' => '2026/2027',
            'semester' => 'ganjil',
            'status' => 'aktif',
            'tanggal_mulai' => now()->subMonth(),
            'tanggal_selesai' => now()->addMonth(),
        ]);

        $guru = Guru::create([
            'nama' => 'Guru Populer, S.Pd.',
            'nip' => '198501012010011002',
            'kategori' => 'produktif',
        ]);

        // Create 15 reviews
        for ($i = 1; $i <= 15; $i++) {
            $siswa = User::create([
                'name' => "Siswa {$i}",
                'nis' => "100{$i}",
                'email' => "siswa{$i}@test.com",
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
                'kritik' => "Kritik ke-{$i}",
                'saran' => "Saran ke-{$i}",
                'is_censored' => false,
                'created_at' => now()->subMinutes(20 - $i),
                'updated_at' => now()->subMinutes(20 - $i),
            ]);
        }

        // Check landing guru detail page 1
        $responsePage1 = $this->get(route('landing.guru.detail', $guru));
        $responsePage1->assertStatus(200);
        $this->assertEquals(15, $responsePage1->viewData('semuaFeedback')->total());
        $this->assertCount(10, $responsePage1->viewData('semuaFeedback')->items());

        // Check landing guru detail page 2
        $responsePage2 = $this->get(route('landing.guru.detail', ['guru' => $guru->id, 'page' => 2]));
        $responsePage2->assertStatus(200);
        $this->assertEquals(15, $responsePage2->viewData('semuaFeedback')->total());
        $this->assertCount(5, $responsePage2->viewData('semuaFeedback')->items());
    }

    public function test_admin_teacher_detail_shows_student_identity_class_jurusan_and_can_filter_by_class(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'nis' => 'admin-test',
            'email' => 'admin-test@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $periode = Periode::create([
            'nama_periode' => 'Semester Ganjil 2026/2027',
            'tahun_ajaran' => '2026/2027',
            'semester' => 'ganjil',
            'status' => 'aktif',
            'tanggal_mulai' => now()->subMonth(),
            'tanggal_selesai' => now()->addMonth(),
        ]);

        $jurusan = Jurusan::create(['nama_jurusan' => 'Akuntansi Keuangan Lembaga', 'kode_jurusan' => 'AKL']);
        $kelasA = Kelas::create(['nama_kelas' => 'X AKL 1', 'tingkat' => 10, 'jurusan_id' => $jurusan->id]);
        $kelasB = Kelas::create(['nama_kelas' => 'XI AKL 2', 'tingkat' => 11, 'jurusan_id' => $jurusan->id]);

        $guru = Guru::create([
            'nama' => 'Ibu Rima Guru AKL, S.Pd.',
            'nip' => '198901012015012003',
            'kategori' => 'produktif',
            'jurusan_id' => $jurusan->id,
        ]);
        $guru->kelas()->attach([$kelasA->id, $kelasB->id]);

        $siswaA = User::create([
            'name' => 'Putri Salsa Christina',
            'nis' => '4679',
            'email' => 'putri@example.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'jurusan_id' => $jurusan->id,
            'is_active' => true,
        ]);
        $siswaA->kelas()->attach($kelasA->id, ['tahun_ajaran' => '2026/2027']);

        $siswaB = User::create([
            'name' => 'Bambang Siswa B',
            'nis' => '4680',
            'email' => 'bambang@example.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'jurusan_id' => $jurusan->id,
            'is_active' => true,
        ]);
        $siswaB->kelas()->attach($kelasB->id, ['tahun_ajaran' => '2026/2027']);

        Penilaian::create([
            'guru_id' => $guru->id,
            'siswa_id' => $siswaA->id,
            'class_id' => $kelasA->id,
            'periode_id' => $periode->id,
            'kedisiplinan' => 5,
            'komunikasi' => 5,
            'tanggung_jawab' => 5,
            'keramahan' => 5,
            'kreativitas' => 5,
            'total_nilai' => 25,
            'rata_rata_evaluasi' => 5.0,
            'kritik' => 'Kritik Putri Kelas A',
            'saran' => 'Saran Putri Kelas A',
            'is_censored' => false,
        ]);

        Penilaian::create([
            'guru_id' => $guru->id,
            'siswa_id' => $siswaB->id,
            'class_id' => $kelasB->id,
            'periode_id' => $periode->id,
            'kedisiplinan' => 4,
            'komunikasi' => 4,
            'tanggung_jawab' => 4,
            'keramahan' => 4,
            'kreativitas' => 4,
            'total_nilai' => 20,
            'rata_rata_evaluasi' => 4.0,
            'kritik' => 'Kritik Bambang Kelas B',
            'saran' => 'Saran Bambang Kelas B',
            'is_censored' => false,
        ]);

        // 1. Admin visits without class filter: sees both reviews and student identity badges
        $resDefault = $this->actingAs($admin)->get(route('admin.guru.show', $guru));
        $resDefault->assertStatus(200);
        $resDefault->assertSee('Putri Salsa Christina');
        $resDefault->assertSee('NIS: 4679');
        $resDefault->assertSee('X AKL 1');
        $resDefault->assertSee('Kritik Putri Kelas A');
        $resDefault->assertSee('Bambang Siswa B');
        $resDefault->assertSee('XI AKL 2');
        $resDefault->assertSee('Kritik Bambang Kelas B');

        // 2. Admin visits with kelas_id filter for Kelas A: only Kelas A review is visible
        $resFilteredA = $this->actingAs($admin)->get(route('admin.guru.show', ['guru' => $guru->id, 'kelas_id' => $kelasA->id]));
        $resFilteredA->assertStatus(200);
        $resFilteredA->assertSee('Putri Salsa Christina');
        $resFilteredA->assertSee('Kritik Putri Kelas A');
        $resFilteredA->assertDontSee('Kritik Bambang Kelas B');
    }

    public function test_admin_data_guru_displays_only_filtered_class_badge_when_filtered(): void
    {
        $admin = User::create([
            'name' => 'Admin Data Guru',
            'nis' => 'admin-dg',
            'email' => 'admin-dg@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $jurusan = Jurusan::create(['nama_jurusan' => 'Akuntansi Keuangan Lembaga', 'kode_jurusan' => 'AKL']);
        $kelasA = Kelas::create(['nama_kelas' => '10 AKL 1', 'tingkat' => 10, 'jurusan_id' => $jurusan->id]);
        $kelasB = Kelas::create(['nama_kelas' => '11 AKL 2', 'tingkat' => 11, 'jurusan_id' => $jurusan->id]);

        $guru = Guru::create([
            'nama' => 'Guru Banyak Kelas, S.Pd.',
            'nip' => '198701012012011004',
            'kategori' => 'produktif',
            'jurusan_id' => $jurusan->id,
        ]);
        $guru->kelas()->attach([$kelasA->id, $kelasB->id]);

        // Default: displays both classes
        $resDefault = $this->actingAs($admin)->get(route('admin.guru.index'));
        $resDefault->assertStatus(200);
        $resDefault->assertSee($kelasA->label_singkat);
        $resDefault->assertSee($kelasB->label_singkat);

        // Filtered by Kelas A: displays ONLY Kelas A, does NOT display Kelas B badge
        $resFiltered = $this->actingAs($admin)->get(route('admin.guru.index', ['kelas_id' => $kelasA->id]));
        $resFiltered->assertStatus(200);
        $resFiltered->assertSee($kelasA->label_singkat);
        $resFiltered->assertDontSee($kelasB->label_singkat);
        // Soft styled badge
        $resFiltered->assertSee('badge bg-light text-dark border');
    }

    public function test_wakil_can_access_pengaturan_wakil_and_admin_parities(): void
    {
        $wakil = User::firstOrCreate(
            ['nis' => 'wakil'],
            [
                'name' => 'Wakil',
                'email' => 'wakil@test.sch.id',
                'password' => bcrypt('eskasaba123'),
                'role' => 'wakil',
                'is_active' => true,
            ]
        );

        $response = $this->actingAs($wakil)->get(route('admin.pengaturan.wakil'));
        $response->assertStatus(200);
        $response->assertSee('Pengaturan Akun Wakil');
        $response->assertSee('Riwayat Perangkat');
        $response->assertSee('FAQ');
        $response->assertSee('Chat Admin');
        $response->assertSee('Kebijakan Privasi');

        // Leaderboard check: badge WAKIL and action "Detail" without arrow icon
        $periode = Periode::create([
            'nama_periode' => 'Semester Ganjil 2026/2027',
            'tahun_ajaran' => '2026/2027',
            'semester' => 'ganjil',
            'status' => 'aktif',
            'tanggal_mulai' => now()->subMonth(),
            'tanggal_selesai' => now()->addMonth(),
        ]);
        $guru = Guru::create([
            'nama' => 'Guru Leaderboard, S.Pd.',
            'nip' => '199901012022011009',
            'kategori' => 'normada',
        ]);
        for ($i = 1; $i <= 5; $i++) {
            $siswa = User::create([
                'name' => "Siswa Penguji {$i}",
                'nis' => "9999111{$i}",
                'email' => "siswa-test{$i}@test.sch.id",
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
                'is_censored' => false,
            ]);
        }

        $leaderboardRes = $this->actingAs($wakil)->get(route('admin.leaderboard.index'));
        $leaderboardRes->assertStatus(200);
        $leaderboardRes->assertSee('WAKIL');
        $leaderboardRes->assertDontSee('WAKIL KEPALA SEKOLAH');
        $leaderboardRes->assertSee('Detail');
        $leaderboardRes->assertDontSee('bi-box-arrow-up-right');

        // Admin visiting wakil settings is redirected to admin settings
        $admin = User::firstOrCreate(
            ['nis' => 'admin-utama'],
            [
                'name' => 'Admin Utama',
                'email' => 'admin-utama@test.sch.id',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
        $adminSettingsRedirect = $this->actingAs($admin)->get(route('admin.pengaturan.wakil'));
        $adminSettingsRedirect->assertRedirect(route('admin.pengaturan.index'));
    }
}
