<?php

namespace Database\Seeders;

use App\Models\MediaAsset;
use App\Models\Page;
use App\Models\PageBlock;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedHome();
        $this->seedAbout();
        $this->seedLegalPages();
    }

    private function seedHome(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Trang chủ',
                'status' => 'published',
                'is_system' => true,
                'seo_title' => 'HappyEcotech | Minh bạch từ gốc – Hạnh phúc từ công nghệ',
                'seo_description' => 'Hệ sinh thái công nghệ minh bạch dữ liệu, tem điện tử, SmartLabel, truy xuất nguồn gốc, TaskFlow, MiniCRM và Farm2Home.',
            ],
        );

        $heroId = MediaAsset::where('disk', 'assets')->where('path', 'img/hero/hero-1.png')->value('id');
        $qrIos = MediaAsset::where('disk', 'assets')->where('path', 'qr/ios.png')->value('id');
        $qrAndroid = MediaAsset::where('disk', 'assets')->where('path', 'qr/google-play.png')->value('id');

        $blocks = [
            ['hero', 'Hero', true, [
                'heading' => 'Minh bạch từ gốc – Hạnh phúc từ công nghệ',
                'subheading' => 'Hệ sinh thái công nghệ minh bạch dữ liệu, tem điện tử, SmartLabel, truy xuất nguồn gốc, TaskFlow, MiniCRM và Farm2Home.',
                'cta_text' => 'Liên hệ hợp tác',
                'cta_url' => '/lien-he#contact-form',
                'image_media_id' => $heroId,
            ]],
            ['partners', 'Đối tác', false, [
                // Logo đối tác cũ là placeholder của template → tắt sẵn, chờ logo thật.
                'heading' => 'Đối tác',
                'logos' => [],
                '_note' => 'Cần logo đối tác thật trước khi bật.',
            ]],
            ['vision_mission', 'Tầm nhìn – Sứ mệnh', true, [
                'heading' => 'Tầm nhìn – Sứ mệnh',
                'vision' => 'Trở thành hệ sinh thái công nghệ minh bạch hàng đầu Đông Nam Á.',
                'mission' => 'Minh bạch dữ liệu – Số hóa kiểm định – Kết nối doanh nghiệp và người tiêu dùng với công nghệ an toàn.',
                'values' => [
                    ['title' => 'Minh bạch', 'description' => 'Minh bạch là nguyên tắc nền tảng trong mọi hoạt động của công ty. Chúng tôi cam kết cung cấp thông tin rõ ràng, chính xác và kịp thời cho khách hàng, đối tác và nhân sự nội bộ.'],
                    ['title' => 'Chính trực', 'description' => 'Chính trực là tiêu chuẩn đạo đức bất biến trong văn hóa doanh nghiệp. Công ty luôn tuân thủ pháp luật, hành xử trung thực, tôn trọng cam kết và chịu trách nhiệm với lời nói cũng như hành động của mình.'],
                    ['title' => 'Công nghệ vì con người', 'description' => 'Chúng tôi tin rằng công nghệ chỉ thật sự có ý nghĩa khi phục vụ con người. Tất cả sản phẩm và giải pháp đều hướng đến việc nâng cao chất lượng cuộc sống và tạo ra giá trị thiết thực cho xã hội.'],
                    ['title' => 'Trách nhiệm xã hội', 'description' => 'Công ty luôn ý thức vai trò và ảnh hưởng của mình đối với cộng đồng, chủ động tham gia các hoạt động đóng góp xã hội, bảo vệ môi trường và thúc đẩy các giá trị nhân văn.'],
                    ['title' => 'Bền vững', 'description' => 'Phát triển bền vững là định hướng chiến lược lâu dài của công ty, hướng đến tạo ra giá trị dài hạn cho khách hàng, nhân viên và xã hội.'],
                ],
            ]],
            ['featured_services', 'Dịch vụ nổi bật', true, [
                'heading' => 'Dịch vụ',
                'subheading' => 'Hệ sinh thái công nghệ minh bạch dữ liệu, tem điện tử, SmartLabel, truy xuất nguồn gốc, TaskFlow, MiniCRM và Farm2Home.',
                'limit' => 4,
            ]],
            ['stats', 'Số liệu nổi bật', false, [
                // GIÁ TRỊ PLACEHOLDER CỦA TEMPLATE — CẦN CHỦ WEBSITE XÁC MINH TRƯỚC KHI BẬT.
                'heading' => 'Tại sao lại lựa chọn chúng tôi?',
                'unverified' => true,
                'items' => [
                    ['value' => '3642', 'label' => 'Khách hàng hài lòng', 'icon' => 'lni lni-emoji-smile'],
                    ['value' => '5436', 'label' => 'Dự án hoàn thành', 'icon' => 'lni lni-checkmark'],
                    ['value' => '642', 'suffix' => 'K', 'label' => 'Thiết kế sống động', 'icon' => 'lni lni-world'],
                    ['value' => '42', 'label' => 'Thiết kế sáng tạo', 'icon' => 'lni lni-users'],
                ],
                '_note' => 'Số liệu lấy từ template gốc, chưa xác minh. Không hiển thị công khai cho tới khi chủ website xác nhận.',
            ]],
            ['why_choose', 'Lý do lựa chọn', true, [
                'heading' => 'Tại sao lại lựa chọn chúng tôi?',
                'description' => 'Chúng tôi là đơn vị phát triển phần mềm chuyên nghiệp, có đội ngũ nhân sự giỏi, kinh nghiệm, tận tâm và chuyên nghiệp.',
            ]],
            ['cta', 'CTA liên hệ', true, [
                'heading' => 'Bạn có ý tưởng gì? Bạn có thể liên hệ chúng tôi để được hỗ trợ.',
                'subheading' => 'Chúng tôi sẽ giúp bạn tạo ra ý tưởng đó thành hiện thực.',
                'cta_text' => 'LIÊN HỆ VỚI CHÚNG TÔI',
                'cta_url' => '/lien-he#contact-form',
            ]],
            ['app_download', 'Tải ứng dụng TaskFlow', true, [
                'kicker' => 'Ứng dụng TaskFlow',
                'heading' => 'Tải ứng dụng trên điện thoại',
                'lead' => 'Quét mã QR hoặc chọn cửa hàng ứng dụng để cài đặt nhanh.',
                'ios_url' => 'https://apps.apple.com/us/app/taskflow/id6758305963',
                'android_url' => 'https://play.google.com/store/apps/details?id=com.happyecotech.taskflow',
                'ios_qr_media_id' => $qrIos,
                'android_qr_media_id' => $qrAndroid,
            ]],
            ['latest_news', 'Tin tức mới nhất', true, [
                'heading' => 'Tin tức mới nhất',
                'limit' => 3,
            ]],
        ];

        foreach ($blocks as $i => [$type, $title, $enabled, $data]) {
            PageBlock::firstOrCreate(
                ['page_id' => $page->id, 'block_type' => $type],
                ['title' => $title, 'is_enabled' => $enabled, 'sort_order' => $i, 'data' => $data],
            );
        }
    }

    private function seedAbout(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'gioi-thieu'],
            [
                'title' => 'Giới thiệu',
                'status' => 'published',
                'is_system' => true,
                'seo_title' => 'Giới thiệu',
                'seo_description' => 'Tầm nhìn, sứ mệnh và giá trị cốt lõi của Happy Ecotech.',
            ],
        );

        $blocks = [
            ['vision_mission', 'Tầm nhìn – Sứ mệnh', true, [
                'heading' => 'Tầm nhìn – Sứ mệnh',
                'vision' => 'Trở thành hệ sinh thái công nghệ minh bạch hàng đầu Đông Nam Á.',
                'mission' => 'Minh bạch dữ liệu – Số hóa kiểm định – Kết nối doanh nghiệp và người tiêu dùng với công nghệ an toàn.',
                'values' => [
                    ['title' => 'Minh bạch', 'description' => 'Cung cấp thông tin rõ ràng, chính xác và kịp thời cho khách hàng, đối tác và nhân sự.'],
                    ['title' => 'Chính trực', 'description' => 'Tuân thủ pháp luật, hành xử trung thực và chịu trách nhiệm với cam kết của mình.'],
                    ['title' => 'Công nghệ vì con người', 'description' => 'Công nghệ phục vụ con người, nâng cao chất lượng cuộc sống và tạo giá trị cho xã hội.'],
                    ['title' => 'Trách nhiệm xã hội', 'description' => 'Đóng góp cho cộng đồng, bảo vệ môi trường và thúc đẩy giá trị nhân văn.'],
                    ['title' => 'Bền vững', 'description' => 'Tạo giá trị dài hạn cho khách hàng, nhân viên và xã hội.'],
                ],
            ]],
            ['cta', 'CTA liên hệ', true, [
                'heading' => 'Cùng Happy Ecotech kiến tạo giải pháp minh bạch',
                'subheading' => 'Liên hệ để được tư vấn về hệ sinh thái sản phẩm của chúng tôi.',
                'cta_text' => 'Liên hệ với chúng tôi',
                'cta_url' => '/lien-he#contact-form',
            ]],
        ];

        foreach ($blocks as $i => [$type, $title, $enabled, $data]) {
            PageBlock::firstOrCreate(
                ['page_id' => $page->id, 'block_type' => $type],
                ['title' => $title, 'is_enabled' => $enabled, 'sort_order' => $i, 'data' => $data],
            );
        }
    }

    private function seedLegalPages(): void
    {
        $pages = [
            ['chinh-sach-bao-mat', 'Chính sách bảo mật'],
            ['dieu-khoan-su-dung', 'Điều khoản sử dụng'],
        ];

        foreach ($pages as [$slug, $title]) {
            $page = Page::firstOrCreate(
                ['slug' => $slug],
                ['title' => $title, 'status' => 'draft', 'is_system' => true, 'seo_title' => $title],
            );

            PageBlock::firstOrCreate(
                ['page_id' => $page->id, 'block_type' => 'richtext'],
                [
                    'title' => $title,
                    'is_enabled' => true,
                    'sort_order' => 0,
                    'data' => [
                        'html' => '<p><em>Nội dung '.$title.' đang được cập nhật. Vui lòng rà soát và bổ sung nội dung pháp lý chính thức trước khi công bố.</em></p>',
                        '_note' => 'Cần chủ website / bộ phận pháp chế cung cấp nội dung chính thức.',
                    ],
                ],
            );
        }
    }
}
