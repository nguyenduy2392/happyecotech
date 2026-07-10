<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\Rbac;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (Rbac::allPermissions() as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // SuperAdmin: toàn quyền (đồng thời được Gate::before bao phủ).
        Role::findOrCreate('SuperAdmin', 'web')->syncPermissions(Permission::all());

        foreach (Rbac::roleMatrix() as $role => $permissions) {
            Role::findOrCreate($role, 'web')->syncPermissions($permissions);
        }

        $this->seedSuperAdmin();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Tạo SuperAdmin đầu tiên từ biến môi trường. Không ghi đè mật khẩu nếu
     * tài khoản đã tồn tại (giữ nguyên mật khẩu admin đã đổi).
     */
    private function seedSuperAdmin(): void
    {
        $email = env('HE_ADMIN_EMAIL', 'admin@happyecotech.com');

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => env('HE_ADMIN_NAME', 'Super Admin'),
                'password' => Hash::make(env('HE_ADMIN_PASSWORD', 'ChangeMe!2026')),
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        if (! $user->hasRole('SuperAdmin')) {
            $user->assignRole('SuperAdmin');
        }
    }
}
