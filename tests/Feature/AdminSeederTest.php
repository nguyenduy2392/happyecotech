<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_is_not_created_without_a_suitable_password(): void
    {
        foreach ([null, '', '   ', 'ChangeMe!2026'] as $password) {
            config(['happyecotech.admin_password' => $password]);
            $this->artisan('db:seed', ['--class' => RolePermissionSeeder::class])
                ->expectsOutputToContain('Bỏ qua tạo SuperAdmin')
                ->assertSuccessful();
            $this->assertDatabaseCount('users', 0);
        }
        $this->assertDatabaseHas('roles', ['name' => 'SuperAdmin']);
    }

    public function test_seeder_never_replaces_an_existing_admin_password(): void
    {
        $password = Str::random(32);
        config(['happyecotech.admin_password' => $password]);
        $this->seed(RolePermissionSeeder::class);
        $admin = User::firstOrFail();
        $this->assertTrue(Hash::check($password, $admin->password));
        $this->assertTrue($admin->hasRole('SuperAdmin'));

        config(['happyecotech.admin_password' => Str::random(32)]);
        $this->seed(RolePermissionSeeder::class);
        $this->assertSame($admin->password, $admin->fresh()->password);
    }
}
