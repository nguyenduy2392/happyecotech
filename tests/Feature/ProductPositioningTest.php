<?php

namespace Tests\Feature;

use App\Models\MediaAsset;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Service;
use App\Support\ProductPositioning;
use Database\Seeders\DatabaseSeeder;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPositioningTest extends TestCase
{
    use RefreshDatabase;

    public function test_fresh_site_leads_with_taskflow_then_minicrm_and_emits_software_schema(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->assertSame(['taskflow', 'minicrm', 'smartlabel', 'farm2home'], Service::published()->ordered()->pluck('slug')->all());
        $this->assertDatabaseHas('menu_items', ['route_name' => 'services.index', 'label' => 'Sản phẩm']);
        $home = $this->get('/')->assertOk()->assertSeeInOrder(['Sản phẩm chủ lực', 'Giải pháp khác'])->getContent();
        $document = $this->html($home);
        $this->assertSame(ProductPositioning::HERO['h1'], trim($document->evaluate('string(//h1)')));
        $this->assertSame(ProductPositioning::HOME_DESCRIPTION, $document->evaluate('string(//meta[@name="description"]/@content)'));
        $this->assertSame(ProductPositioning::ORGANIZATION_DESCRIPTION, $this->schema($document)[0]['description']);

        foreach (ProductPositioning::PRODUCTS as $slug => $copy) {
            $document = $this->html($this->get('/dich-vu/'.$slug)->assertOk()->getContent());
            $graph = array_column($this->schema($document), null, '@type');
            $app = $graph['SoftwareApplication'];
            $this->assertSame($copy['h1'], trim($document->evaluate('string(//h1)')));
            $this->assertSame($copy['seo_description'], $document->evaluate('string(//meta[@name="description"]/@content)'));
            $this->assertSame('BusinessApplication', $app['applicationCategory']);
            $this->assertSame(seo_url('/dich-vu/'.$slug), $app['url']);
            $this->assertStringEndsWith("og-service-{$slug}.webp", $app['image']);
            $this->assertSame($graph['Organization']['@id'], $app['provider']['@id']);
            $this->assertArrayNotHasKey('offers', $app);
            $this->assertArrayNotHasKey('aggregateRating', $app);
            $this->assertSame(count($copy['faq']), count($graph['FAQPage']['mainEntity']));
            if ($slug === 'taskflow') {
                $this->assertSame(['iOS', 'Android'], $app['operatingSystem']);
            } else {
                $this->assertArrayNotHasKey('operatingSystem', $app);
            }
            $service = Service::whereSlug($slug)->firstOrFail();
            $this->assertCount(3, $service->features);
            $this->assertCount(4, $service->gallery);
        }
    }

    public function test_positioning_is_opt_in_and_preserves_editor_fields(): void
    {
        $this->seed(DatabaseSeeder::class);
        $home = Page::whereSlug('home')->firstOrFail();
        $home->update(['seo_title' => 'Tiêu đề biên tập', 'seo_description' => null]);
        $hero = $home->blocks()->where('block_type', 'hero')->firstOrFail();
        $legacyImage = MediaAsset::wherePath('img/v2/hero.webp')->firstOrFail();
        $hero->update(['data' => ['heading' => 'Minh bạch từ gốc – Hạnh phúc từ công nghệ', 'image_media_id' => $legacyImage->id, 'editor_note' => 'Giữ nguyên']]);
        MenuItem::where('route_name', 'services.index')->firstOrFail()->update(['label' => 'Dịch vụ']);
        $task = Service::whereSlug('taskflow')->firstOrFail();
        $task->update(['sort_order' => 2, 'faq' => [], 'h1' => 'H1 của biên tập viên']);
        $crm = Service::whereSlug('minicrm')->firstOrFail();
        $crm->update(['sort_order' => 3, 'faq' => [[
            'question' => 'Tìm hiểu MiniCRM ở đâu?',
            'answer' => 'Bạn có thể liên hệ Happy Ecotech để trao đổi nhu cầu và phạm vi triển khai phù hợp.',
        ]]]);
        Service::whereSlug('smartlabel')->firstOrFail()->update(['sort_order' => 1]);

        $this->artisan('ecotech:apply-positioning')->expectsOutputToContain('Dry-run')->assertSuccessful();
        $this->assertNull($home->refresh()->seo_description);
        $this->assertSame(2, $task->refresh()->sort_order);
        $this->assertSame($legacyImage->id, $hero->refresh()->data['image_media_id']);
        $this->artisan('ecotech:apply-positioning', ['--force' => true, '--dry-run' => true])->assertSuccessful();
        $this->assertSame(2, $task->refresh()->sort_order);

        $this->artisan('ecotech:apply-positioning', ['--force' => true])->assertSuccessful();
        $this->artisan('ecotech:apply-positioning', ['--force' => true])->assertSuccessful();
        $this->assertSame('Tiêu đề biên tập', $home->refresh()->seo_title);
        $this->assertSame(ProductPositioning::HOME_DESCRIPTION, $home->seo_description);
        $this->assertSame('Giữ nguyên', $hero->refresh()->data['editor_note']);
        $this->assertSame('img/v2/hero-taskflow.webp', MediaAsset::findOrFail($hero->data['image_media_id'])->path);
        $this->assertSame(1, $task->refresh()->sort_order);
        $this->assertSame('H1 của biên tập viên', $task->h1);
        $this->assertSame([], $task->faq);
        $this->assertSame(ProductPositioning::PRODUCTS['minicrm']['faq'], $crm->refresh()->faq);
        $this->assertDatabaseHas('services', ['slug' => 'datahub', 'status' => 'draft']);
        $this->assertDatabaseHas('services', ['slug' => 'omc', 'status' => 'draft']);
        $this->assertDatabaseHas('pages', ['slug' => 'chinh-sach-bao-mat', 'status' => 'draft']);
    }

    private function html(string $html): DOMXPath
    {
        $document = new DOMDocument;
        @$document->loadHTML('<?xml encoding="UTF-8">'.$html);

        return new DOMXPath($document);
    }

    private function schema(DOMXPath $document): array
    {
        return json_decode($document->evaluate('string(//script[@type="application/ld+json"])'), true, flags: JSON_THROW_ON_ERROR)['@graph'];
    }
}
