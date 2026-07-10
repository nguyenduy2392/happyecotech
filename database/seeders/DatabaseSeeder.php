<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Toàn bộ seed đều idempotent (firstOrCreate) — chạy lại an toàn, không tạo bản ghi trùng.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            MediaAssetSeeder::class,
            SiteSettingSeeder::class,
            ServiceSeeder::class,
            MenuSeeder::class,
            PageSeeder::class,
            BlogSeeder::class,
        ]);
    }
}
