<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Các mục điều hướng chính, trỏ bằng route nội bộ.
        $items = [
            ['Trang chủ', 'home'],
            ['Giới thiệu', 'about'],
            ['Dịch vụ', 'services.index'],
            ['Đội ngũ', 'team'],
            ['Tin tức', 'posts.index'],
            ['Liên hệ', 'contact'],
        ];

        $header = Menu::firstOrCreate(['location' => 'header'], ['name' => 'Menu Header']);
        $footerQuick = Menu::firstOrCreate(['location' => 'footer_quick'], ['name' => 'Footer - Liên kết nhanh']);

        foreach ([$header, $footerQuick] as $menu) {
            foreach ($items as $i => [$label, $route]) {
                MenuItem::firstOrCreate(
                    ['menu_id' => $menu->id, 'label' => $label],
                    [
                        'link_type' => 'route',
                        'route_name' => $route,
                        'sort_order' => $i,
                        'is_enabled' => true,
                    ],
                );
            }
        }
    }
}
