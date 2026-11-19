<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\SiteSetting;
use App\Support\Analytics;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    private function set(string $key, string $value): void
    {
        SiteSetting::put($key, $value, 'analytics');
        Cache::flush();
    }

    private function html(string $url = '/'): string
    {
        return $this->get($url)->assertOk()->getContent();
    }

    public function test_nothing_loaded_when_ids_are_empty(): void
    {
        $html = $this->html();
        $this->assertStringNotContainsString('he-analytics', $html);
        $this->assertStringNotContainsString('googletagmanager', $html);
        $this->assertStringNotContainsString('data-consent-banner', $html);
        $this->assertStringNotContainsString('dataLayer', $html);
    }

    public function test_valid_gtm_renders_config_and_default_denied_consent(): void
    {
        $this->set('analytics_gtm_id', 'GTM-ABC1234');
        $html = $this->html();

        $this->assertStringContainsString('id="he-analytics"', $html);
        $this->assertStringContainsString('"gtm":"GTM-ABC1234"', $html);
        $this->assertStringContainsString("analytics_storage: 'denied'", $html);
        $this->assertStringContainsString("ad_storage: 'denied'", $html);
        $this->assertStringContainsString('data-consent-banner', $html);
        // Script ngoài KHÔNG được nhúng trực tiếp trong HTML (nạp sau tương tác/idle bằng JS).
        $this->assertStringNotContainsString('googletagmanager.com', $html);
    }

    public function test_ga4_only_and_gtm_takes_precedence(): void
    {
        $this->set('analytics_ga4_id', 'G-AB12CD34EF');
        $this->assertStringContainsString('"ga4":"G-AB12CD34EF"', $this->html());

        $this->set('analytics_gtm_id', 'GTM-ABC1234');
        $html = $this->html();
        $this->assertStringContainsString('"gtm":"GTM-ABC1234"', $html);
        $this->assertStringContainsString('"ga4":null', $html);
    }

    #[DataProvider('invalidIds')]
    public function test_invalid_ids_are_ignored(string $key, string $value): void
    {
        $this->set($key, $value);
        $html = $this->html();
        $this->assertStringNotContainsString('he-analytics', $html);
        $this->assertStringNotContainsString($value, $html);
        $this->assertNull(Analytics::config());
    }

    /** @return array<string,array{string,string}> */
    public static function invalidIds(): array
    {
        return [
            'gtm script injection' => ['analytics_gtm_id', 'GTM-AB"><script>alert(1)</script>'],
            'gtm wrong prefix' => ['analytics_gtm_id', 'UA-12345'],
            'gtm too short' => ['analytics_gtm_id', 'GTM-A'],
            'ga4 injection' => ['analytics_ga4_id', "G-ABCDEF');alert(1);//"],
            'ga4 wrong prefix' => ['analytics_ga4_id', 'AW-123456789'],
        ];
    }

    public function test_scroll_depth_flag_follows_setting(): void
    {
        $this->set('analytics_gtm_id', 'GTM-ABC1234');
        $this->assertStringContainsString('"scroll":true', $this->html());
        $this->set('analytics_scroll_depth', '0');
        $this->assertStringContainsString('"scroll":false', $this->html());
    }

    public function test_view_has_no_pii_and_service_page_has_tracking_hooks(): void
    {
        $this->set('analytics_gtm_id', 'GTM-ABC1234');
        $service = Service::query()->published()->first();
        $html = $this->html('/dich-vu/'.$service->slug);

        $this->assertStringContainsString('data-view-service="'.$service->slug.'"', $html);
        preg_match('#<script type="application/json" id="he-analytics">(.*?)</script>#s', $html, $m);
        $this->assertEqualsCanonicalizing(['gtm', 'ga4', 'scroll', 'solutions', 'flagship'], array_keys(json_decode($m[1], true)));
        // Cấu hình đo lường không chứa SĐT/email công ty hay dữ liệu người dùng.
        $this->assertStringNotContainsString('0936685886', $m[1]);
        $this->assertStringNotContainsString('@', $m[1]);

        // Module JS không đọc giá trị ô nhập (name/phone/email) vào sự kiện.
        $js = file_get_contents(resource_path('js/analytics.js'));
        $this->assertDoesNotMatchRegularExpression('/FormData|\.value\b|\[name=["\'](name|phone|email)/', $js);
    }

    public function test_admin_settings_validates_id_format(): void
    {
        $this->assertSame('GTM-ABC1234', Analytics::clean(' gtm-abc1234 ', Analytics::GTM_PATTERN));
        $this->assertNull(Analytics::clean('GTM-<x>', Analytics::GTM_PATTERN));
        $this->assertNull(Analytics::clean(null, Analytics::GA4_PATTERN));
    }

    public function test_solution_values_are_stable_slugs_and_flagship_tier(): void
    {
        $this->assertSame(['taskflow', 'minicrm'], Analytics::FLAGSHIP);
        $this->assertSame([], array_diff(Analytics::FLAGSHIP, Analytics::SOLUTIONS));

        $this->set('analytics_gtm_id', 'GTM-ABC1234');
        $html = $this->html('/lien-he');
        foreach (Service::query()->published()->get() as $svc) {
            $this->assertStringContainsString('value="'.$svc->id.'" data-slug="'.$svc->slug.'"', $html);
        }
        preg_match('#id="he-analytics">(.*?)</script>#s', $html, $m);
        $cfg = json_decode($m[1], true);
        $this->assertSame(Analytics::SOLUTIONS, $cfg['solutions']);
        $this->assertSame(Analytics::FLAGSHIP, $cfg['flagship']);

        // Mọi sự kiện đi qua product(): giá trị lạ -> other, trống -> none, tier suy ra từ slug.
        $js = file_get_contents(resource_path('js/analytics.js'));
        $this->assertStringContainsString("'other'", $js);
        $this->assertSame(3, substr_count($js, 'product('), 'view_service, cta_click, lead_submit đều dùng product()');
        $app = file_get_contents(resource_path('js/app.js'));
        $this->assertStringContainsString('dataset.slug', $app);
        $this->assertStringNotContainsString('selectedOptions[0]?.textContent', $app, 'lead_submit không dùng tên hiển thị');
    }
}
