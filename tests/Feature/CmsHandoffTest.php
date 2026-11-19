<?php

namespace Tests\Feature;

use App\Filament\Resources\Services\Pages\EditService;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CmsHandoffTest extends TestCase
{
    use RefreshDatabase;

    public function test_cms_urls_are_filtered_on_read_and_rejected_in_filament(): void
    {
        foreach ([' JaVaScRiPt:alert(1) ', 'DaTa:text/html,content', 'vBsCrIpT:content', 'javascript&#58;alert(1)',
            'java&Tab;script:alert(1)', '&#47;&#47;example.test', "\t/duong-dan"] as $url) {
            $this->assertNull(safe_url($url));
        }
        $this->assertSame('HTTPS://example.test/app?a=1&b=2', safe_url(' HTTPS://example.test/app?a=1&amp;b=2 '));
        foreach (['javascript:alert(1)', 'data:text/html,content', '//example.test', '/\\example.test', "https://example.test/\npath"] as $url) {
            $this->assertNull(safe_url($url));
            $this->assertSame('#', (new MenuItem(['url' => $url, 'link_type' => 'url']))->resolveUrl());
        }
        foreach (['/lien-he#contact-form', '#form', 'https://example.test/app?source=site'] as $url) {
            $this->assertSame($url, safe_url($url));
        }
        $this->seed(DatabaseSeeder::class);
        $this->actingAs(User::whereEmail(config('happyecotech.admin_email'))->firstOrFail());
        $service = Service::whereSlug('smartlabel')->firstOrFail();
        Livewire::test(EditService::class, ['record' => $service->getRouteKey()])
            ->fillForm(['cta_url' => 'javascript:alert(1)', 'external_url' => '/noi-bo'])
            ->call('save')
            ->assertHasFormErrors(['cta_url', 'external_url']);
    }

    public function test_cta_upgrade_sets_missing_values_without_overwriting_choices_or_other_pages(): void
    {
        $this->seed(DatabaseSeeder::class);
        $home = Page::whereSlug('home')->firstOrFail()->blocks()->where('block_type', 'cta')->firstOrFail();
        $about = Page::whereSlug('gioi-thieu')->firstOrFail()->blocks()->where('block_type', 'cta')->firstOrFail();
        $other = Page::create(['slug' => 'trang-thu', 'title' => 'Trang thử', 'status' => 'draft'])
            ->blocks()->create(['block_type' => 'cta', 'data' => []]);
        $this->assertTrue($home->data['show_form']);
        $this->assertTrue($about->data['show_form']);
        $home->update(['data' => ['cta_text' => 'Nội dung đã có']]);
        $about->update(['data' => ['show_form' => false]]);

        $migration = require database_path('migrations/2026_11_19_000004_enable_existing_landing_cta_forms.php');
        $migration->up();
        $migration->up();
        $this->assertSameJsonValue(['cta_text' => 'Nội dung đã có', 'show_form' => true], $home->refresh()->data);
        $this->assertFalse($about->refresh()->data['show_form']);
        $this->assertArrayNotHasKey('show_form', $other->refresh()->data);
        $farm = Service::whereSlug('farm2home')->firstOrFail();
        $this->assertNull($farm->benefits);
        $this->assertNull($farm->steps);
    }
}
