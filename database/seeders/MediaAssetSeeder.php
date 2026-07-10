<?php

namespace Database\Seeders;

use App\Models\MediaAsset;
use Illuminate\Database\Seeder;

/**
 * Đăng ký các asset tĩnh hiện có (logo, icon dịch vụ, QR, favicon) vào Media Library
 * dưới disk 'assets' — KHÔNG di chuyển file, giữ nguyên URL /assets/... đã index.
 */
class MediaAssetSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['img/logo/logo-full.png', 'logo', 'Happy Ecotech'],
            ['img/favicon.svg', 'favicon', 'Favicon Happy Ecotech'],
            ['img/project-icon/smart-tray.png', 'service', 'SmartLabel'],
            ['img/project-icon/task-flow.png', 'service', 'TaskFlow'],
            ['img/project-icon/mini-crm.png', 'service', 'MiniCRM'],
            ['img/project-icon/farm2home.png', 'service', 'Farm2Home'],
            ['qr/ios.png', 'qr', 'QR tải TaskFlow trên App Store'],
            ['qr/google-play.png', 'qr', 'QR tải TaskFlow trên Google Play'],
            ['img/hero/hero-1.png', 'hero', 'Ảnh hero trang chủ'],
        ];

        foreach ($items as [$path, $folder, $alt]) {
            $this->register($path, $folder, $alt);
        }
    }

    private function register(string $path, string $folder, ?string $alt = null): void
    {
        $full = public_path('assets/'.$path);
        $size = is_file($full) ? filesize($full) : null;
        $width = $height = null;

        if ($size && str_starts_with($this->mime($path), 'image/') && ! str_ends_with($path, '.svg')) {
            $info = @getimagesize($full);
            if ($info) {
                [$width, $height] = $info;
            }
        }

        MediaAsset::firstOrCreate(
            ['disk' => 'assets', 'path' => $path],
            [
                'file_name' => basename($path),
                'original_name' => basename($path),
                'mime_type' => $this->mime($path),
                'extension' => pathinfo($path, PATHINFO_EXTENSION),
                'size' => $size,
                'width' => $width,
                'height' => $height,
                'alt' => $alt,
                'folder' => $folder,
            ],
        );
    }

    private function mime(string $path): string
    {
        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            default => 'application/octet-stream',
        };
    }
}
