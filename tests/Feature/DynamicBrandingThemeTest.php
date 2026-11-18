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
        $response->assertSee('gk-dynamic-brand-colors');

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
}
