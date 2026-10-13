<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_dashboard_uses_its_own_url(): void
    {
        $user = User::factory()->create(['user_role' => 'admin']);
        $this->actingAs($user);

        $this->get(route('dashboard'))
            ->assertRedirect(route('admin_dashboard'));

        $this->get(route('admin_dashboard'))
            ->assertOk()
            ->assertSee('หน้าหลักผู้ดูแลระบบ')
            ->assertSee('บริการยอดนิยม')
            ->assertSee('สรุปการจอง');
    }

    public function test_superadmin_dashboard_uses_its_own_url(): void
    {
        $user = User::factory()->create(['user_role' => 'superadmin']);
        $this->actingAs($user);

        $this->get(route('dashboard'))
            ->assertRedirect(route('superadmin_dashboard'));

        $this->get(route('superadmin_dashboard'))
            ->assertOk()
            ->assertSee('รายงานและสรุปข้อมูล')
            ->assertSee('สรุปรายได้ประจำปี');
    }

    public function test_dashboard_urls_reject_the_other_role(): void
    {
        $admin = User::factory()->create(['user_role' => 'admin']);
        $this->actingAs($admin)
            ->get(route('superadmin_dashboard'))
            ->assertForbidden();

        $superadmin = User::factory()->create(['user_role' => 'superadmin']);
        $this->actingAs($superadmin)
            ->get(route('admin_dashboard'))
            ->assertForbidden();
    }
}
