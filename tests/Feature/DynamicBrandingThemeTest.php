<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DynamicBrandingThemeTest extends TestCase
{
    use RefreshDatabase;

    public function test_custom_branding_and_colors_reflect_across_website_and_loader(): void
    {
        // Set custom site title and colors
        Setting::set('site_title', 'garakaa');
        Setting::set('site_title_part1', 'gara');
        Setting::set('site_title_part2', 'kaa');
        Setting::set('site_title_color1', '#123456');
        Setting::set('site_title_color2', '#abcdef');

        // Test Landing page
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('gara');
        $response->assertSee('kaa');
        $response->assertSee('#123456');
        $response->assertSee('#abcdef');
        $response->assertSee('@smkn1bangsri.official');
        $response->assertSee('instagram.com/smkn1bangsri.official');

        // Test Auth/Login page
        $loginResponse = $this->get('/login');
        $loginResponse->assertStatus(200);
        $loginResponse->assertSee('gara');
        $loginResponse->assertSee('kaa');
        $loginResponse->assertSee('#123456');
        $loginResponse->assertSee('#abcdef');

        // Test page loader component directly or via dashboard
        $siswa = User::create([
            'name' => 'Siswa Test',
            'nis' => '123456',
            'email' => 'siswa@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
        ]);

        $dashResponse = $this->actingAs($siswa)->get('/siswa/dashboard');
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('gk-page-veil');
        $dashResponse->assertSee('gara');
        $dashResponse->assertSee('kaa');
        $dashResponse->assertSee('#123456');
        $dashResponse->assertSee('#abcdef');
    }

    public function test_admin_settings_updates_and_syncs_site_title_and_parts(): void
    {
        $admin = User::create([
            'name' => 'Admin Test',
            'nis' => 'admin-test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Submit setting with site_title = 'garakaa'
        $response = $this->actingAs($admin)->post(route('admin.pengaturan.landing'), [
            'site_title' => 'garakaa',
            'site_title_part1' => 'gara',
            'site_title_part2' => 'kaa',
            'site_title_color1' => '#ff0055',
            'site_title_color2' => '#00ffaa',
        ]);

        $response->assertRedirect();
        $this->assertEquals('garakaa', Setting::get('site_title'));
        $this->assertEquals('gara', Setting::get('site_title_part1'));
        $this->assertEquals('kaa', Setting::get('site_title_part2'));
        $this->assertEquals('#ff0055', Setting::get('site_title_color1'));
        $this->assertEquals('#00ffaa', Setting::get('site_title_color2'));
    }

    public function test_siswa_riwayat_is_filtered_by_active_periode_by_default_and_can_filter_past_periods(): void
    {
        $oldPeriode = \App\Models\Periode::create([
            'nama_periode' => 'Semester Ganjil 2025/2026',
            'tahun_ajaran' => '2025/2026',
            'semester' => 'ganjil',
            'status' => 'nonaktif',
            'tanggal_mulai' => now()->subYears(1),
            'tanggal_selesai' => now()->subMonths(6),
        ]);

        $activePeriode = \App\Models\Periode::create([
            'nama_periode' => 'Semester Ganjil 2026/2027',
            'tahun_ajaran' => '2026/2027',
            'semester' => 'ganjil',
            'status' => 'aktif',
            'tanggal_mulai' => now()->subMonth(),
            'tanggal_selesai' => now()->addMonth(),
        ]);

        $siswa = User::create([
            'name' => 'Siswa Riwayat',
            'nis' => '778899',
            'email' => 'siswa_riwayat@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'is_active' => true,
        ]);

        $guru1 = \App\Models\Guru::create([
            'nama' => 'Guru Periode Lalu, S.Pd.',
            'nip' => '198101012005011001',
            'kategori' => 'produktif',
        ]);

        $guru2 = \App\Models\Guru::create([
            'nama' => 'Guru Periode Aktif, M.Kom.',
            'nip' => '198202022006022002',
            'kategori' => 'normada',
        ]);

        // Review in old period
        \App\Models\Penilaian::create([
            'siswa_id' => $siswa->id,
            'guru_id' => $guru1->id,
            'periode_id' => $oldPeriode->id,
            'kedisiplinan' => 5,
            'komunikasi' => 5,
            'tanggung_jawab' => 5,
            'kreativitas' => 5,
            'keramahan' => 5,
            'total_nilai' => 25,
            'rata_rata_evaluasi' => 5.0,
            'kritik' => 'Kritik periode lalu',
            'saran' => 'Saran periode lalu',
        ]);

        // Review in active period
        \App\Models\Penilaian::create([
            'siswa_id' => $siswa->id,
            'guru_id' => $guru2->id,
            'periode_id' => $activePeriode->id,
            'kedisiplinan' => 4,
            'komunikasi' => 4,
            'tanggung_jawab' => 4,
            'kreativitas' => 4,
            'keramahan' => 4,
            'total_nilai' => 20,
            'rata_rata_evaluasi' => 4.0,
            'kritik' => 'Kritik periode aktif',
            'saran' => 'Saran periode aktif',
        ]);

        // 1. Default request to riwayat (should only show active period)
        $defaultResponse = $this->actingAs($siswa)->get(route('siswa.riwayat'));
        $defaultResponse->assertStatus(200);
        $defaultResponse->assertSee('Guru Periode Aktif');
        $defaultResponse->assertSee('Kritik periode aktif');
        $defaultResponse->assertDontSee('Guru Periode Lalu');
        $defaultResponse->assertDontSee('Kritik periode lalu');

        // 2. Request with periode_id = all (should show both)
        $allResponse = $this->actingAs($siswa)->get(route('siswa.riwayat', ['periode_id' => 'all']));
        $allResponse->assertStatus(200);
        $allResponse->assertSee('Guru Periode Aktif');
        $allResponse->assertSee('Guru Periode Lalu');

        // 3. Request with oldPeriode id (should only show old period review)
        $oldResponse = $this->actingAs($siswa)->get(route('siswa.riwayat', ['periode_id' => $oldPeriode->id]));
        $oldResponse->assertStatus(200);
        $oldResponse->assertSee('Guru Periode Lalu');
        $oldResponse->assertDontSee('Guru Periode Aktif');
    }

    public function test_pagination_renders_numbered_pages_for_mobile_and_desktop(): void
    {
        $periode = \App\Models\Periode::create([
            'nama_periode' => 'Semester Ganjil 2026/2027',
            'tahun_ajaran' => '2026/2027',
            'semester' => 'ganjil',
            'status' => 'aktif',
            'tanggal_mulai' => now()->subMonth(),
            'tanggal_selesai' => now()->addMonth(),
        ]);

        $guru = \App\Models\Guru::create([
            'nama' => 'Guru Pagination Test, S.Pd.',
            'nip' => '199001012015011005',
            'kategori' => 'produktif',
        ]);

        $hashedPass = bcrypt('secret');
        // Create 25 reviews (10 per page -> 3 pages)
        for ($i = 1; $i <= 25; $i++) {
            $siswa = User::create([
                'name' => "Siswa Pag {$i}",
                'nis' => "9900{$i}",
                'email' => "siswapag{$i}@test.com",
                'password' => $hashedPass,
                'role' => 'siswa',
                'is_active' => true,
            ]);

            \App\Models\Penilaian::create([
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

        $res = $this->get(route('landing.guru.detail', $guru));
        $res->assertStatus(200);
        // Ensure page numbers 1, 2, 3 are present in the DOM (not hidden by d-sm-none)
        $res->assertSee('Navigasi Halaman');
        $res->assertSee('>1<', false);
        $res->assertSee('>2<', false);
        $res->assertSee('>3<', false);
    }
}
