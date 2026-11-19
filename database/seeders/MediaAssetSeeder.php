<?php

namespace Database\Seeders;

use App\Models\MediaAsset;
use App\Support\CompanyProfile;
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
            ['img/v2/favicon.svg', 'favicon', 'Favicon Happy Ecotech'],
            ['qr/ios.png', 'qr', 'QR tải TaskFlow trên App Store'],
            ['qr/google-play.png', 'qr', 'QR tải TaskFlow trên Google Play'],
        ];

        $manifest = json_decode(file_get_contents(public_path('assets/img/v2/manifest.json')), true, flags: JSON_THROW_ON_ERROR);
        foreach ($manifest as $asset) {
            if (in_array($asset['file'], ['hero.webp', 'hero-taskflow.webp', CompanyProfile::CEO_FILE], true)
                || preg_match('/^(?:service-.+-lg\.webp|screen-(?:taskflow|minicrm)-[1-4]\.webp|icon-(?!.*-48).*\.svg|og[^\/]*\.(?:png|webp))$/D', $asset['file'])) {
                $items[] = ['img/v2/'.$asset['file'], str($asset['use'])->before('.')->toString(), $asset['alt']];
            }
        }

        foreach ($items as [$path, $folder, $alt]) {
            $this->register($path, $folder, $alt);
        }
    }

    private function register(string $path, string $folder, ?string $alt = null): void
    {
        $full = public_path('assets/'.$path);
        if (! is_file($full)) {
            throw new \RuntimeException("Thiếu tài sản tĩnh: {$path}");
        }
        $size = filesize($full);
        $width = $height = null;

        if ($size && str_starts_with($this->mime($path), 'image/') && ! str_ends_with($path, '.svg')) {
            $info = @getimagesize($full);
            if ($info) {
                [$width, $height] = $info;
            }
        }

        $media = MediaAsset::firstOrCreate(
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
        if ($path === CompanyProfile::CEO_PHOTO && $media->alt === CompanyProfile::PREVIOUS_CEO_ALT) {
            $media->update(['alt' => CompanyProfile::CEO_ALT]);
        }
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
