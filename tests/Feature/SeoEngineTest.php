<?php

namespace Tests\Feature;

use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\RelationManagers\BlocksRelationManager;
use App\Models\MediaAsset;
use App\Models\Page;
use App\Models\Post;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Tests\TestCase;

class SeoEngineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_sitemap_urls_are_live_and_self_canonical(): void
    {
        $response = $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml');
        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml);
        $this->assertGreaterThan(10, count($xml->url));

        $descriptions = [];
        foreach ($xml->url as $entry) {
            $url = (string) $entry->loc;
            $this->assertFalse(isset($entry->priority));
            $document = $this->html($this->get($url)->assertOk()->getContent());
            $this->assertSame($url, $document->evaluate('string(//link[@rel="canonical"]/@href)'));
            $description = $document->evaluate('string(//meta[@name="description"]/@content)');
            $this->assertGreaterThanOrEqual(120, mb_strlen($description), $url);
            $this->assertLessThanOrEqual(155, mb_strlen($description), $url);
            $this->assertNotContains($description, $descriptions, $url);
            $descriptions[] = $description;
        }
        $response->assertSee('/tin-tuc/danh-muc/', false)->assertSee('<lastmod>', false);
    }

    public function test_legacy_canonical_and_pagination(): void
    {
        $post = Post::published()->firstOrFail();
        $post->update(['seo_canonical' => url($post->slug)]);
        $url = route('posts.show', $post);
        $this->get($url)->assertOk()->assertSee('href="'.$url.'"', false);
        $url = route('posts.index', ['page' => 2]);
        $document = $this->html($this->get($url)->assertOk()->getContent());
        $this->assertSame($url, $document->evaluate('string(//link[@rel="canonical"]/@href)'));
        $this->assertStringEndsWith('Trang 2.', $document->evaluate('string(//meta[@name="description"]/@content)'));
    }

    public function test_robots_and_redirects(): void
    {
        $this->get('/robots.txt')->assertOk()
            ->assertSee('Disallow: /admin', false)->assertSee('Sitemap: '.route('sitemap'), false);
        foreach (['/HOME/', '/index.php', '/home'] as $path) {
            $this->get($path)->assertStatus(301)->assertRedirect(seo_url('/'));
        }
        $this->get('/TIN-TUC/')->assertStatus(301)->assertRedirect(route('posts.index'));

        $page = Page::whereSlug('home')->firstOrFail();
        $page->update(['slug' => 'bad-home', 'status' => 'draft']);
        $this->assertSame('home', $page->refresh()->slug);
        $this->assertSame('published', $page->status);

        $page = Page::create(['title' => 'Test', 'slug' => 'old', 'status' => 'published']);
        $page->update(['slug' => 'new']);
        $page->update(['slug' => 'latest']);
        $this->get('/OLD/')->assertStatus(301)->assertRedirect(url('/latest'));
        Page::whereSlug('gioi-thieu')->firstOrFail()->update(['slug' => 've-chung-toi']);
        $this->get('/gioi-thieu')->assertStatus(301)->assertRedirect(seo_url('/ve-chung-toi'));
    }

    public function test_filament_protects_home_and_edits_seeded_block_data(): void
    {
        $home = Page::whereSlug('home')->firstOrFail();
        $this->actingAs(User::where('email', env('HE_ADMIN_EMAIL', 'admin@happyecotech.com'))->firstOrFail());
        Livewire::test(EditPage::class, ['record' => $home->getRouteKey()])
            ->assertFormFieldIsDisabled('slug')
            ->assertFormFieldIsDisabled('status');

        $block = $home->blocks()->where('block_type', 'vision_mission')->firstOrFail();
        $values = $block->data['values'];
        $block->update(['data' => $block->data + ['_note' => 'Nội dung cũ']]);
        Livewire::test(BlocksRelationManager::class, ['ownerRecord' => $home, 'pageClass' => EditPage::class])
            ->callTableAction('edit', $block, data: ['data' => ['heading' => 'Tầm nhìn mới']])
            ->assertHasNoTableActionErrors();
        $this->assertSame('Tầm nhìn mới', $block->refresh()->data['heading']);
        $this->assertSame($values, $block->data['values']);
        $this->assertSame('Nội dung cũ', $block->data['_note']);
    }

    public function test_home_skips_optional_content_queries_when_blocks_are_disabled(): void
    {
        $home = Page::whereSlug('home')->firstOrFail();
        $home->blocks()->whereIn('block_type', ['featured_services', 'latest_news'])->update(['is_enabled' => false]);
        DB::enableQueryLog();
        $this->get('/')->assertOk();
        $queries = collect(DB::getQueryLog())->pluck('query');
        DB::disableQueryLog();
        $this->assertCount(0, $queries->filter(fn ($sql) => str_contains($sql, '"posts"')));
        $this->assertCount(0, $queries->filter(fn ($sql) => str_contains($sql, '"is_featured"')));
    }

    public function test_metadata_graph_and_safe_json_encoding(): void
    {
        $home = Page::whereSlug('home')->firstOrFail();
        $home->update(['seo_title' => 'Tiêu đề SEO trang chủ']);
        $home->blocks()->where('block_type', 'faq')->firstOrFail()->update(['data' => [
            'items' => [['question' => 'Câu hỏi?', 'answer' => 'Đáp án </script>']],
        ]]);
        SiteSetting::put('company_name', 'Tên công ty thử nghiệm');
        $document = $this->html($this->get('/')->assertOk()->getContent());
        $this->assertSame('Tiêu đề SEO trang chủ', $document->evaluate('string(//title)'));
        $this->assertNotEmpty($document->evaluate('string(//meta[@property="og:image"]/@content)'));
        $graph = $this->graph($document);
        $this->assertSame(['Organization', 'LocalBusiness'], $graph[0]['@type']);
        $this->assertSame('Tên công ty thử nghiệm', $graph[0]['name']);
        $this->assertSame('WebSite', $graph[1]['@type']);
        $this->assertSame('FAQPage', $graph[2]['@type']);
        $this->assertSame('Đáp án </script>', $graph[2]['mainEntity'][0]['acceptedAnswer']['text']);

        foreach (['/dich-vu/smartlabel' => 'Service', route('posts.show', Post::published()->firstOrFail()) => 'BlogPosting'] as $url => $type) {
            $graph = $this->graph($this->html($this->get($url)->assertOk()->getContent()));
            $this->assertSame($type === 'Service' ? ['BreadcrumbList', 'Service', 'FAQPage'] : ['BreadcrumbList', $type], array_column($graph, '@type'));
        }
        $this->get('/nonexistent')->assertNotFound()->assertSee('noindex,follow');
    }

    public function test_guest_html_cache_is_invalidated_on_save_and_contact_keeps_session(): void
    {
        $page = Page::create(['title' => 'Trang kiểm tra cache', 'slug' => 'kiem-tra-cache', 'status' => 'published']);
        $this->get('/kiem-tra-cache')->assertOk();
        DB::enableQueryLog();
        $this->get('/kiem-tra-cache')->assertOk()->assertSee('Trang kiểm tra cache');
        $queries = collect(DB::getQueryLog())->pluck('query');
        DB::disableQueryLog();
        $this->assertCount(0, $queries->filter(fn ($sql) => str_contains($sql, '"page_blocks"')));

        $page->update(['seo_title' => 'Tiêu đề đã xuất bản']);
        $this->get('/kiem-tra-cache')->assertOk()->assertSee('Tiêu đề đã xuất bản');
        $this->withSession(['contact_success' => true])->get('/lien-he')
            ->assertOk()->assertHeader('Cache-Control', 'no-store, private')->assertSee('name="_token"', false);
    }

    public function test_seeded_assets_exist_and_hero_media_accepts_model_or_id(): void
    {
        foreach (MediaAsset::whereDisk('assets')->cursor() as $media) {
            $this->assertFileExists(public_path('assets/'.$media->path));
        }
        foreach (json_decode(file_get_contents(public_path('assets/img/v2/manifest.json')), true, flags: JSON_THROW_ON_ERROR) as $asset) {
            $this->assertFileExists(public_path('assets/img/v2/'.$asset['file']));
        }
        $hero = Page::whereSlug('home')->firstOrFail()->blocks()->where('block_type', 'hero')->firstOrFail();
        $media = MediaAsset::findOrFail($hero->data['image_media_id']);
        $this->assertStringEndsWith('/hero-md.webp', media_url($media, 'md'));
        $this->assertSame(media_url($media, 'sm'), media_url($media->id, 'sm'));
        $this->assertDatabaseHas('services', ['slug' => 'datahub', 'status' => 'draft']);
        $this->assertDatabaseHas('services', ['slug' => 'omc', 'status' => 'draft']);
    }

    public function test_only_token_free_lead_forms_are_cached_and_token_endpoint_uses_current_session(): void
    {
        $renders = 0;
        $markup = '<form data-lead-form><input type="hidden" name="_token" value="" data-csrf-from="/csrf-token"></form>';
        Route::middleware('web')->get('/cache-fixture/lead', function () use (&$renders, $markup) {
            $renders++;

            return response($markup);
        })->name('services.cached-lead');
        Route::middleware('web')->get('/cache-fixture/plain', fn () => response('<form><input name="query"></form>'))
            ->name('services.uncached-form');
        Route::middleware('web')->get('/cache-fixture/embedded-token', fn () => response(str_replace('value=""', 'value="already-filled"', $markup)))
            ->name('services.embedded-token');

        $this->withSession(['_token' => 'session-token-one'])->get('/cache-fixture/lead')->assertOk()->assertDontSee('session-token-one');
        $this->withSession(['_token' => 'session-token-two'])->get('/cache-fixture/lead')->assertOk()->assertDontSee('session-token-two');
        $this->assertSame(1, $renders);
        $this->get('/cache-fixture/plain')->assertHeader('Cache-Control', 'no-store, private');
        $this->get('/cache-fixture/embedded-token')->assertHeader('Cache-Control', 'no-store, private');
        $this->getJson('/csrf-token')->assertOk()->assertHeader('Cache-Control', 'no-store, private')
            ->assertExactJson(['token' => 'session-token-two']);
    }

    private function graph(DOMXPath $document): array
    {
        $json = $document->evaluate('string(//script[@type="application/ld+json"])');

        return json_decode($json, true, flags: JSON_THROW_ON_ERROR)['@graph'];
    }

    private function html(string $html): DOMXPath
    {
        $document = new DOMDocument;
        @$document->loadHTML('<?xml encoding="UTF-8">'.$html);

        return new DOMXPath($document);
    }
}
