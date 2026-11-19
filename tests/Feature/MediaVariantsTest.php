<?php

namespace Tests\Feature;

use App\Models\MediaAsset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaVariantsTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_and_backfill_generate_webp_and_preserve_fallbacks(): void
    {
        if (! function_exists('imagewebp')) {
            $this->markTestSkipped('PHP GD WebP support is required.');
        }

        $disk = Storage::fake('public');
        $disk->makeDirectory('media');
        $source = imagecreatetruecolor(1700, 100);
        imagepng($source, $disk->path('media/upload.png'));
        imagepng($source, $disk->path('media/old.png'));

        $media = MediaAsset::create(['disk' => 'public', 'path' => 'media/upload.png']);
        $paths = $disk->allFiles('media/variants/'.$media->id);
        $this->assertCount(3, $paths);
        foreach (['sm' => 480, 'md' => 960, 'lg' => 1600] as $variant => $width) {
            $path = collect($paths)->first(fn ($file) => str_ends_with($file, '-'.$variant.'.webp'));
            $this->assertSame($disk->url($path), media_url($media, $variant));
            $info = getimagesize($disk->path($path));
            $this->assertSame($width, $info[0]);
            $this->assertSame(IMAGETYPE_WEBP, $info[2]);
        }
        $old = MediaAsset::createQuietly(['disk' => 'public', 'path' => 'media/old.png', 'file_name' => 'old.png']);
        $this->assertSame($old->url, media_url($old, 'sm'));
        $missing = MediaAsset::createQuietly(['disk' => 'public', 'path' => 'media/missing.png', 'file_name' => 'missing.png']);
        $this->assertNull(media_url($missing, 'sm'));
        $this->assertNull(media_url(null, 'sm'));
        $this->assertSame('/fallback.png', media_url(null, '/fallback.png'));
        $this->artisan('media:backfill-variants')->assertSuccessful();
        $this->assertStringEndsWith('-sm.webp', media_url($old, 'sm'));
        $this->artisan('media:backfill-variants')->assertSuccessful();
    }
}
