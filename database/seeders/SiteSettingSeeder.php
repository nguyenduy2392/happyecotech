<?php

namespace Database\Seeders;

use App\Models\MediaAsset;
use App\Models\SiteSetting;
use App\Models\SocialLink;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $logo = $this->mediaId('img/logo/logo-full.png');
        $favicon = $this->mediaId('img/favicon.svg');
        $qrIos = $this->mediaId('qr/ios.png');
        $qrAndroid = $this->mediaId('qr/google-play.png');

        $settings = [
            // General
            ['general', 'site_name', 'Happy Ecotech', 'string'],
            ['general', 'tagline', 'Minh bạch từ gốc – Hạnh phúc từ công nghệ', 'string'],
            ['general', 'logo_media_id', $logo, 'media'],
            ['general', 'footer_logo_media_id', $logo, 'media'],
            ['general', 'favicon_media_id', $favicon, 'media'],

            // Contact
            ['contact', 'company_name', 'Happy Ecotech', 'string'],
            ['contact', 'contact_phone', '0936685886', 'string'],
            ['contact', 'contact_email', 'hung.duy@happyecotech.com', 'string'],
            ['contact', 'contact_address', '198 Trường Chinh, Hải Phòng', 'string'],
            ['contact', 'working_hours', '', 'string'],
            ['contact', 'map_embed_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4520.861944537773!2d106.66066972918166!3d20.835788870260203!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x314a709c3b095cb9%3A0x6fca0a200afd143!2zMTk4IMSQLiBUcsaw4budbmcgQ2hpbmgsIFF1w6FuIFRy4buvLCBLaeG6v24gQW4sIEjhuqNpIFBow7JuZywgVmnhu4d0IE5hbQ!5e1!3m2!1svi!2sus!4v1765598428061!5m2!1svi!2sus', 'text'],

            // SEO
            ['seo', 'seo_title_template', '%s | Happy Ecotech', 'string'],
            ['seo', 'seo_default_description', 'Hệ sinh thái công nghệ minh bạch dữ liệu, tem điện tử, SmartLabel, truy xuất nguồn gốc, TaskFlow, MiniCRM và Farm2Home.', 'text'],
            ['seo', 'seo_default_og_image_media_id', null, 'media'],
            ['seo', 'seo_robots', 'index,follow', 'string'],
            ['seo', 'zalo_site_verification', 'Fz-kBBpPAIrbyBfscFKTR1hmd6RsXaanDJ0o', 'string'],

            // Footer
            ['footer', 'footer_description', 'Chúng tôi là đơn vị phát triển phần mềm chuyên nghiệp, có đội ngũ nhân sự giỏi, kinh nghiệm, tận tâm và chuyên nghiệp.', 'text'],
            ['footer', 'footer_copyright', 'Happy Ecotech', 'string'],
            ['footer', 'footer_services_count', '4', 'integer'],
            ['footer', 'footer_show_services', '1', 'boolean'],
            ['footer', 'footer_show_social', '1', 'boolean'],

            // App download
            ['app', 'app_store_url', 'https://apps.apple.com/us/app/taskflow/id6758305963', 'string'],
            ['app', 'google_play_url', 'https://play.google.com/store/apps/details?id=com.happyecotech.taskflow', 'string'],
            ['app', 'app_qr_ios_media_id', $qrIos, 'media'],
            ['app', 'app_qr_android_media_id', $qrAndroid, 'media'],

            // SMTP: chỉ hiển thị trạng thái, secret đọc từ env — không lưu password ở đây.
            ['smtp', 'smtp_notify_enabled', '0', 'boolean'],
        ];

        foreach ($settings as [$group, $key, $value, $type]) {
            SiteSetting::firstOrCreate(
                ['key' => $key],
                ['group' => $group, 'type' => $type, 'value' => $this->encode($value, $type)],
            );
        }

        $this->seedSocialLinks();
    }

    private function seedSocialLinks(): void
    {
        // Social thật chưa được cung cấp → seed disabled, admin điền URL và bật sau.
        $links = [
            ['facebook', 'Facebook', ''],
            ['linkedin', 'LinkedIn', ''],
            ['youtube', 'YouTube', ''],
            ['zalo', 'Zalo', ''],
        ];

        foreach ($links as $i => [$platform, $label, $url]) {
            SocialLink::firstOrCreate(
                ['platform' => $platform],
                ['label' => $label, 'url' => $url, 'sort_order' => $i, 'is_enabled' => false],
            );
        }
    }

    private function mediaId(string $path): ?string
    {
        $id = MediaAsset::where('disk', 'assets')->where('path', $path)->value('id');

        return $id ? (string) $id : null;
    }

    private function encode(mixed $value, string $type): ?string
    {
        return match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => json_encode($value, JSON_UNESCAPED_UNICODE),
            default => is_null($value) ? null : (string) $value,
        };
    }
}
