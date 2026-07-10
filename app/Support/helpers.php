<?php

use App\Models\MediaAsset;
use App\Models\SiteSetting;

if (! function_exists('media_url')) {
    /**
     * URL của media theo id, có fallback.
     */
    function media_url(int|string|null $id, ?string $fallback = null): ?string
    {
        if (blank($id)) {
            return $fallback;
        }

        return MediaAsset::find($id)?->url ?? $fallback;
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
        $siteName = setting('site_name', 'Happy Ecotech');
        $template = setting('seo_title_template', '%s | '.$siteName);
        $title = $overrides['title'] ?? null;

        $fullTitle = $title
            ? (str_contains($template, '%s') ? str_replace('%s', $title, $template) : $title.' | '.$siteName)
            : $siteName;

        $ogImage = $overrides['og_image'] ?? null;
        if (! $ogImage && ($default = setting('seo_default_og_image_media_id'))) {
            $ogImage = $default->url ?? null;
        }

        return [
            'title' => $fullTitle,
            'description' => $overrides['description'] ?? setting('seo_default_description', ''),
            'canonical' => $overrides['canonical'] ?? url()->current(),
            'og_image' => $ogImage,
            'robots' => $overrides['robots'] ?? setting('seo_robots', 'index,follow'),
            'type' => $overrides['type'] ?? 'website',
        ];
    }
}
