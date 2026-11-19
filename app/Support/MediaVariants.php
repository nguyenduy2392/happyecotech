<?php

namespace App\Support;

use App\Models\MediaAsset;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class MediaVariants
{
    public const WIDTHS = ['sm' => 480, 'md' => 960, 'lg' => 1600];

    public static function url(MediaAsset $asset, string $variant): ?string
    {
        if (! $asset->path || ! $asset->exists || ! isset(self::WIDTHS[$variant])) {
            return null;
        }
        if ($asset->disk === 'assets' && str_starts_with($asset->path, 'img/v2/')) {
            $assets = Storage::disk('assets');
            $stem = preg_replace('/-lg$/', '', pathinfo($asset->path, PATHINFO_FILENAME));
            $variantPath = 'img/v2/'.$stem.'-'.$variant.'.webp';
            if ($assets->exists($variantPath)) {
                return $assets->url($variantPath);
            }
        }
        $disk = Storage::disk('public');
        $path = self::path($asset, $variant);

        return $disk->exists($path) ? $disk->url($path) : null;
    }

    public static function generate(MediaAsset $asset): void
    {
        if (! $asset->path || ! in_array($asset->disk, ['public', 'assets'], true)) {
            return;
        }
        $source = Storage::disk($asset->disk)->path($asset->path);
        if (! is_file($source)) {
            return;
        }
        $size = @getimagesize($source);
        if (! $size || ! in_array($size[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true)
            || $size[0] * $size[1] > 24_000_000) {
            return;
        }
        $disk = Storage::disk('public');
        $pending = array_filter(self::WIDTHS, fn ($width, $variant) => ! $disk->exists(self::path($asset, $variant)), ARRAY_FILTER_USE_BOTH);
        if ($pending === []) {
            return;
        }
        if (! function_exists('imagewebp')) {
            throw new RuntimeException('PHP GD with WebP support is required.');
        }
        $original = match ($size[2]) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($source),
            IMAGETYPE_PNG => @imagecreatefrompng($source),
            IMAGETYPE_WEBP => @imagecreatefromwebp($source),
        };
        if (! $original) {
            throw new RuntimeException('Could not decode media '.$asset->id);
        }
        foreach ($pending as $variant => $width) {
            $scaled = imagescale($original, min($width, $size[0]), -1, IMG_BILINEAR_FIXED);
            if (! $scaled) {
                throw new RuntimeException('Could not resize media '.$asset->id);
            }
            imagepalettetotruecolor($scaled);
            imagealphablending($scaled, false);
            imagesavealpha($scaled, true);

            ob_start();
            try {
                $encoded = imagewebp($scaled, null, 82);
                $bytes = ob_get_contents();
            } finally {
                ob_end_clean();
            }
            if (! $encoded || ! $bytes || ! $disk->put(self::path($asset, $variant), $bytes)) {
                throw new RuntimeException('Could not write WebP for media '.$asset->id);
            }
        }
    }

    private static function path(MediaAsset $asset, string $variant): string
    {
        $source = hash('sha256', $asset->disk.':'.$asset->path);

        return "media/variants/{$asset->id}/{$source}-{$variant}.webp";
    }
}
