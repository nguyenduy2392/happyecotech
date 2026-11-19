<?php

use App\Models\MediaAsset;
use App\Models\SiteSetting;
use App\Support\MediaVariants;
use App\Support\RequestMedia;
use App\Support\SeoDescription;
use App\Support\SeoSchema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (! function_exists('media_url')) {
    /**
     * URL của media theo id, có fallback.
     */
    function media_url(MediaAsset|int|string|null $media, ?string $variant = null, ?string $fallback = null): ?string
    {
        // Positional fallback URLs remain supported for existing Blade calls.
        if ($variant !== null && ! isset(MediaVariants::WIDTHS[$variant])) {
            $fallback = $variant;
            $variant = null;
        }

        $asset = app(RequestMedia::class)->find($media);
        if (! $asset || ! $asset->path) {
            return $fallback;
        }
        if ($variant) {
            $webp = MediaVariants::url($asset, $variant);
            if ($webp) {
                return $webp;
            }
            $disk = $asset->disk ?: 'public';
            if (in_array($disk, ['public', 'assets'], true) && ! Storage::disk($disk)->exists($asset->path)) {
                return $fallback;
            }
        }

        return $asset->url ?? $fallback;
    }
}

if (! function_exists('setting')) {
    /**
     * Đọc một site setting (đã cast + cache).
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return SiteSetting::getValue($key, $default);
    }
}

if (! function_exists('he_seo')) {
    /**
     * Chuẩn hoá metadata SEO cho một trang, có fallback về site settings.
     *
     * @param  array<string,mixed>  $overrides
     * @return array<string,mixed>
     */
    function he_seo(array $overrides = []): array
    {
        $siteName = setting('site_name') ?: config('app.name');
        $template = setting('seo_title_template', '%s | '.$siteName);
        $title = $overrides['title'] ?? null;

        $fullTitle = $title
            ? (str_contains($template, '%s') ? str_replace('%s', $title, $template) : $title.' | '.$siteName)
            : $siteName;

        $ogImage = $overrides['og_image'] ?? null;
        $imageAlt = $overrides['image_alt'] ?? null;
        if (! $ogImage && ($default = setting('seo_default_og_image_media_id'))) {
            $ogImage = $default->url ?? null;
            $imageAlt = $default->alt ?: $siteName;
        }

        $clean = static fn ($text) => Str::squish(
            html_entity_decode(strip_tags((string) $text), ENT_QUOTES | ENT_HTML5, 'UTF-8')
        );
        $canonical = $overrides['canonical'] ?? seo_url(request()->getPathInfo());
        $image = $ogImage ?: seo_url('/assets/img/v2/og.png');
        $robots = $overrides['robots'] ?? setting('seo_robots', 'index,follow');
        $description = SeoDescription::forPage(
            $overrides['description'] ?? (request()->routeIs('home') ? setting('seo_default_description') : null),
            $title,
            $overrides['not_found'] ?? false,
            $overrides['description_page'] ?? 1,
        );

        return [
            'title' => Str::limit($clean(($overrides['standalone_title'] ?? false) ? $title : $fullTitle) ?: $siteName, 65, '…'),
            'description' => $description,
            'canonical' => $canonical,
            'og_image' => $image,
            'image_alt' => ($ogImage ? $imageAlt : null) ?: $siteName,
            'site_name' => $siteName,
            'locale' => config('app.locale') === 'vi' ? 'vi_VN' : str_replace('-', '_', config('app.locale')),
            'robots' => $robots,
            'type' => $overrides['type'] ?? 'website',
            'schema' => str_contains(strtolower($robots), 'noindex') ? [] : SeoSchema::graph($overrides, $canonical, $siteName, $image),
        ];
    }
}

function seo_url(string $path): string
{
    return rtrim(config('app.url'), '/').'/'.ltrim($path, '/');
}
