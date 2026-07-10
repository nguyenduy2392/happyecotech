<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSmokeTest extends TestCase
{
    use RefreshDatabase;

    private function superAdmin(): User
    {
        $this->seed(RolePermissionSeeder::class);

        return User::where('email', env('HE_ADMIN_EMAIL', 'admin@happyecotech.com'))->firstOrFail();
    }

    public function test_login_page_renders(): void
    {
        $this->get('/admin/login')->assertOk()->assertSee('Happy Ecotech CMS');
    }

    public function test_guest_is_redirected_from_admin(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_service_list_renders_for_superadmin(): void
    {
        $this->actingAs($this->superAdmin())
            ->get('/admin/services')
            ->assertOk();
    }

    public function test_dashboard_renders(): void
    {
        $this->actingAs($this->superAdmin())->get('/admin')->assertOk();
    }

    public function test_settings_page_renders(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', env('HE_ADMIN_EMAIL', 'admin@happyecotech.com'))->firstOrFail();

        $this->actingAs($admin)->get('/admin/manage-settings')->assertOk();
    }
}
