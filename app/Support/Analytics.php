<?php

namespace App\Support;

/**
 * Cấu hình đo lường (GTM / GA4) đọc từ SiteSetting. ID chỉ hợp lệ khi đúng định dạng;
 * sai định dạng = coi như trống (không bao giờ chèn chuỗi tuỳ ý vào trang).
 */
class Analytics
{
    public const GTM_PATTERN = '/^GTM-[A-Z0-9]{4,10}$/';

    public const GA4_PATTERN = '/^G-[A-Z0-9]{6,12}$/';

    public static function clean(mixed $value, string $pattern): ?string
    {
        if (! is_string($value)) {
            return null;
        }
        $value = strtoupper(trim($value));

        return preg_match($pattern, $value) === 1 ? $value : null;
    }

    /**
     * @return array{gtm:?string,ga4:?string,scroll:bool}|null null = không nạp gì.
     */
    public static function config(): ?array
    {
        $gtm = self::clean(setting('analytics_gtm_id'), self::GTM_PATTERN);
        // Có GTM thì GTM quản lý GA4 bên trong container; không nạp gtag.js trùng.
        $ga4 = $gtm ? null : self::clean(setting('analytics_ga4_id'), self::GA4_PATTERN);

        if (! $gtm && ! $ga4) {
            return null;
        }

        return ['gtm' => $gtm, 'ga4' => $ga4, 'scroll' => (bool) setting('analytics_scroll_depth', true)];
    }
}
