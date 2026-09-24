<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OAuthSSOTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'services.sipintu.base_url' => 'https://sipintu.test',
            'services.sipintu.client_id' => 'test_client_id',
            'services.sipintu.client_secret' => 'test_client_secret',
            'services.sipintu.redirect_uri' => 'http://localhost:8000/oauth/callback',
        ]);
    }

    public function test_callback_fails_when_error_parameter_present()
    {
        $response = $this->get('/oauth/callback?error=access_denied&error_description=User+denied+access');

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['nis']);
        $this->assertFalse(Auth::check());
    }

    public function test_callback_fails_when_code_missing()
    {
        $response = $this->get('/oauth/callback');

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['nis']);
        $this->assertFalse(Auth::check());
    }

    public function test_callback_fails_when_code_is_reused()
    {
        $code = 'reuse_code_123';
        $codeHash = 'sso_code_' . hash('sha256', $code);
        Cache::put($codeHash, true, 300);

        $response = $this->get('/oauth/callback?code=' . $code);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['nis']);
        $this->assertFalse(Auth::check());
    }

    public function test_callback_fails_when_token_exchange_fails()
    {
        Http::fake([
            'https://sipintu.test/oauth/token' => Http::response([
                'error' => 'invalid_grant',
                'error_description' => 'The authorization code is invalid',
            ], 400),
        ]);

        $response = $this->get('/oauth/callback?code=bad_code');

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['nis']);
        $this->assertFalse(Auth::check());
    }

    public function test_callback_fails_when_user_not_found_locally()
    {
        Http::fake([
            'https://sipintu.test/oauth/token' => Http::response([
                'access_token' => 'mock_token_abc',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ], 200),
            'https://sipintu.test/api/v1/user' => Http::response([
                'data' => [
                    'id' => 999,
                    'name' => 'Unknown Student',
                    'nis' => '99999',
                    'email' => 'unknown@example.com',
                ],
            ], 200),
        ]);

        $response = $this->get('/oauth/callback?code=valid_code_new');

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['nis']);
        $this->assertFalse(Auth::check());
        $this->assertDatabaseMissing('users', ['nis' => '99999']);
    }

    public function test_callback_fails_when_user_is_deactivated()
    {
        $user = User::factory()->create([
            'name' => 'Suspended Student',
            'nis' => '12345',
            'email' => 'suspended@example.com',
            'role' => 'siswa',
            'is_active' => false,
            'deactivation_type' => 'permanen',
            'deactivated_reason' => 'Melanggar aturan sekolah',
        ]);

        Http::fake([
            'https://sipintu.test/oauth/token' => Http::response([
                'access_token' => 'mock_token_abc',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ], 200),
            'https://sipintu.test/api/v1/user' => Http::response([
                'data' => [
                    'id' => 123,
                    'name' => 'Suspended Student',
                    'nis' => '12345',
                    'email' => 'suspended@example.com',
                ],
            ], 200),
        ]);

        $response = $this->get('/oauth/callback?code=code_suspended');

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['nis']);
        $this->assertFalse(Auth::check());
    }

    public function test_callback_succeeds_and_redirects_siswa()
    {
        $user = User::factory()->create([
            'name' => 'Siswa Aktif',
            'nis' => '11111',
            'email' => 'siswa@example.com',
            'role' => 'siswa',
            'is_active' => true,
        ]);

        Http::fake([
            'https://sipintu.test/oauth/token' => Http::response([
                'access_token' => 'mock_token_siswa',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ], 200),
            'https://sipintu.test/api/v1/user' => Http::response([
                'data' => [
                    'id' => 1,
                    'name' => 'Siswa Aktif',
                    'nis' => '11111',
                    'email' => 'siswa@example.com',
                ],
            ], 200),
        ]);

        $response = $this->get('/oauth/callback?code=code_siswa');

        $response->assertRedirect(route('siswa.dashboard'));
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
    }

    public function test_callback_succeeds_and_redirects_guru()
    {
        $guruUser = User::factory()->create([
            'name' => 'Bapak Guru',
            'nis' => '198001012005011001',
            'email' => 'guru@example.com',
            'role' => 'guru',
            'is_active' => true,
        ]);

        Http::fake([
            'https://sipintu.test/oauth/token' => Http::response([
                'access_token' => 'mock_token_guru',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ], 200),
            'https://sipintu.test/api/v1/user' => Http::response([
                'data' => [
                    'id' => 2,
                    'name' => 'Bapak Guru',
                    'nip' => '198001012005011001',
                    'email' => 'guru@example.com',
                ],
            ], 200),
        ]);

        $response = $this->get('/oauth/callback?code=code_guru');

        $response->assertRedirect(route('guru.dashboard'));
        $this->assertTrue(Auth::check());
        $this->assertEquals($guruUser->id, Auth::id());
    }

    public function test_callback_succeeds_and_redirects_admin()
    {
        $adminUser = User::factory()->create([
            'name' => 'Administrator',
            'nis' => 'admin',
            'email' => 'admin@gurukuu.com',
            'role' => 'admin',
            'is_active' => true,
        ]);

        Http::fake([
            'https://sipintu.test/oauth/token' => Http::response([
                'access_token' => 'mock_token_admin',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ], 200),
            'https://sipintu.test/api/v1/user' => Http::response([
                'data' => [
                    'id' => 3,
                    'name' => 'Administrator',
                    'username' => 'admin',
                    'email' => 'admin@gurukuu.com',
                ],
            ], 200),
        ]);

        $response = $this->get('/oauth/callback?code=code_admin');

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertTrue(Auth::check());
        $this->assertEquals($adminUser->id, Auth::id());
    }

    public function test_manual_login_routes_still_work()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);

        $user = User::factory()->create([
            'nis' => '22222',
            'password' => bcrypt('secret123'),
            'role' => 'siswa',
            'is_active' => true,
        ]);

        $postResponse = $this->post('/login', [
            'nis' => '22222',
            'login_role' => 'siswa',
            'password' => 'secret123',
        ]);

        $postResponse->assertRedirect(route('siswa.dashboard'));
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::id());
    }
}
