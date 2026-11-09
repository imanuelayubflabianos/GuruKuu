<?php

namespace Tests\Feature;

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

    public function test_wakil_can_login_via_guru_tab_and_access_monitoring_only(): void
    {
        $response = $this->post(route('login'), [
            'login_role' => 'guru',
            'nis' => 'wakil',
            'password' => 'eskasaba123',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();

        $user = auth()->user();
        $this->assertEquals('wakil', $user->role);

        // Can access evaluation and monitoring pages
        $dashboardResponse = $this->get(route('admin.dashboard'));
        $dashboardResponse->assertStatus(200);

        $leaderboardResponse = $this->get(route('admin.leaderboard.index'));
        $leaderboardResponse->assertStatus(200);

        $guruListResponse = $this->get(route('admin.guru.index'));
        $guruListResponse->assertStatus(200);

        $feedbackResponse = $this->get(route('admin.kritik-saran.index'));
        $feedbackResponse->assertStatus(200);

        $pelanggaranResponse = $this->get(route('admin.pelanggaran.index'));
        $pelanggaranResponse->assertStatus(200);

        // FORBIDDEN / REDIRECTED from destructive pages & actions
        $settingsResponse = $this->get(route('admin.pengaturan.index'));
        $settingsResponse->assertRedirect(route('admin.dashboard'));

        $sipintuResponse = $this->get(route('admin.sipintu.index'));
        $sipintuResponse->assertRedirect(route('admin.dashboard'));

        // Cannot perform destructive admin actions
        $cleanLogsResponse = $this->post(route('admin.pelanggaran.clean-logs'), ['mode' => 'read']);
        $cleanLogsResponse->assertRedirect(route('admin.dashboard'));
    }
}
