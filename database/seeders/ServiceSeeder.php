<?php

namespace Database\Seeders;

use App\Models\MediaAsset;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'SmartLabel',
                'slug' => 'smartlabel',
                'icon' => 'img/project-icon/smart-tray.png',
                'short_description' => 'SmartLabel là giải pháp điện tử hóa kiểm định và quản lý chất lượng sản phẩm, giúp doanh nghiệp đánh dấu và xác thực nguồn gốc, chất lượng và an toàn.',
                'external_url' => null,
                'app_store_url' => null,
                'google_play_url' => null,
            ],
            [
                'name' => 'TaskFlow',
                'slug' => 'taskflow',
                'icon' => 'img/project-icon/task-flow.png',
                'short_description' => 'TaskFlow là giải pháp quản lý dự án và công việc, giúp doanh nghiệp tối ưu hóa quy trình làm việc, giảm thiểu sai sót và nâng cao hiệu suất.',
                'external_url' => null,
                'app_store_url' => 'https://apps.apple.com/us/app/taskflow/id6758305963',
                'google_play_url' => 'https://play.google.com/store/apps/details?id=com.happyecotech.taskflow',
            ],
            [
                'name' => 'MiniCRM',
                'slug' => 'minicrm',
                'icon' => 'img/project-icon/mini-crm.png',
                'short_description' => 'MiniCRM là giải pháp quản lý khách hàng và mối quan hệ khách hàng.',
                'external_url' => null,
                'app_store_url' => null,
                'google_play_url' => null,
            ],
            [
                'name' => 'Farm2Home',
                'slug' => 'farm2home',
                'icon' => 'img/project-icon/farm2home.png',
                'short_description' => 'Farm2Home là giải pháp kết nối trực tiếp giữa nông dân và người tiêu dùng.',
                'external_url' => null,
                'app_store_url' => null,
                'google_play_url' => null,
            ],
        ];

        foreach ($services as $i => $data) {
            $iconId = MediaAsset::where('disk', 'assets')->where('path', $data['icon'])->value('id');

            Service::firstOrCreate(
                ['slug' => $data['slug']],
                [
                    'name' => $data['name'],
                    'short_description' => $data['short_description'],
                    'content' => '<p>'.e($data['short_description']).'</p>',
                    'icon_media_id' => $iconId,
                    'external_url' => $data['external_url'],
                    'app_store_url' => $data['app_store_url'],
                    'google_play_url' => $data['google_play_url'],
                    'cta_text' => 'Liên hệ tư vấn',
                    'cta_url' => '/lien-he#contact-form',
                    'status' => 'published',
                    'is_featured' => true,
                    'sort_order' => $i + 1,
                    'published_at' => now(),
                ],
            );
        }
    }
}
