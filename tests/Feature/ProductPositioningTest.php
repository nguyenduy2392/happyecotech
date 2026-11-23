<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageSettings;
use App\Models\MediaAsset;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use App\Models\User;
use App\Support\CompanyProfile;
use App\Support\EcosystemCopy;
use App\Support\ProductPositioning;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\LandingCopy;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductPositioningTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_uses_new_hero_for_the_legacy_image_reference(): void
    {
        $this->seed(DatabaseSeeder::class);
        $hero = Page::whereSlug('home')->firstOrFail()->blocks()->where('block_type', 'hero')->firstOrFail();
        $legacy = MediaAsset::firstOrCreate(['disk' => 'assets', 'path' => 'img/hero/hero-1.png'], ['alt' => 'Ảnh cũ']);
        $hero->update(['data' => array_merge($hero->data, ['image_media_id' => $legacy->id])]);

        $response = $this->get('/')->assertOk();
        $response->assertSee('hero-taskflow', false);
        $response->assertDontSee('assets/img/hero/hero-1.png', false);
    }

    public function test_fresh_site_leads_with_taskflow_then_minicrm_and_emits_software_schema(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->assertSame(['taskflow', 'minicrm', 'smartlabel', 'farm2home'], Service::published()->ordered()->pluck('slug')->all());
        $this->assertDatabaseHas('menu_items', ['route_name' => 'services.index', 'label' => 'Sản phẩm']);
        $home = $this->get('/')->assertOk()->getContent();
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

    public function test_ecosystem_has_six_entries_and_solution_links_follow_publish_state(): void
    {
        $this->seed(DatabaseSeeder::class);
        $home = Page::whereSlug('home')->firstOrFail();
        $overview = $home->blocks()->where('block_type', 'ecosystem_overview')->firstOrFail();
        $this->assertSame(array_keys(EcosystemCopy::LABELS), array_column($overview->data['items'], 'key'));
        foreach (EcosystemCopy::LABELS as $slug => $copy) {
            $service = Service::whereSlug($slug)->firstOrFail();
            $this->assertSame($copy['category_label'], $service->category_label);
            $this->assertLessThanOrEqual(70, mb_strlen($service->tagline));
        }
        $this->get('/')->assertOk()->assertViewHas('page', function (Page $page) {
            $items = $page->blocks->firstWhere('block_type', 'other_solutions')->data['items'];

            return array_column($items, 'url') === ['/dich-vu/smartlabel', '/dich-vu/farm2home', '/lien-he?solution=datahub', '/lien-he?solution=omc'];
        });
        Service::whereSlug('farm2home')->firstOrFail()->update(['status' => 'draft']);
        $this->get('/')->assertOk()->assertViewHas('page', function (Page $page) {
            return $page->blocks->firstWhere('block_type', 'other_solutions')->data['items'][1]['url'] === '/lien-he?solution=farm2home';
        });
        $overview->update(['data' => ['heading' => 'Nội dung đã biên tập', 'items' => []], 'is_enabled' => false]);
        $home->blocks()->where('block_type', 'other_solutions')->delete();
        $crm = Service::whereSlug('minicrm')->firstOrFail();
        $crm->update(['category_label' => null, 'tagline' => 'Giữ câu đã biên tập']);
        $this->artisan('ecotech:apply-positioning')->assertSuccessful();
        $this->assertFalse($home->blocks()->where('block_type', 'other_solutions')->exists());
        $this->assertNull($crm->refresh()->category_label);
        $this->artisan('ecotech:apply-positioning', ['--force' => true])->assertSuccessful();
        $this->assertTrue($home->blocks()->where('block_type', 'other_solutions')->exists());
        $this->assertSame('Nội dung đã biên tập', $overview->refresh()->data['heading']);
        $this->assertFalse($overview->is_enabled);
        $this->assertSame(EcosystemCopy::LABELS['minicrm']['category_label'], $crm->refresh()->category_label);
        $this->assertSame('Giữ câu đã biên tập', $crm->tagline);
        $this->assertDatabaseHas('services', ['slug' => 'farm2home', 'status' => 'draft']);
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
        $this->assertSameJsonValue(ProductPositioning::PRODUCTS['minicrm']['faq'], $crm->refresh()->faq);
        $this->assertDatabaseHas('services', ['slug' => 'datahub', 'status' => 'draft']);
        $this->assertDatabaseHas('services', ['slug' => 'omc', 'status' => 'draft']);
        $this->assertDatabaseHas('pages', ['slug' => 'chinh-sach-bao-mat', 'status' => 'draft']);
    }

    public function test_official_profiles_and_portrait_are_published_without_a_placeholder_person_in_schema(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->assertSame(CompanyProfile::ZALO, setting('contact_zalo'));
        $this->assertSame(CompanyProfile::FACEBOOK, setting('social_facebook'));
        $home = $this->html($this->get('/')->assertOk()->getContent());
        $this->assertSame([CompanyProfile::FACEBOOK, CompanyProfile::ZALO], $this->schema($home)[0]['sameAs']);

        $leader = TeamMember::whereSlug(CompanyProfile::CEO_SLUG)->firstOrFail();
        $this->assertSame(CompanyProfile::CEO_NAME, $leader->name);
        $this->assertSame(CompanyProfile::CEO_ROLE, $leader->position);
        $this->assertNull($leader->bio);
        $this->assertNull($leader->short_bio);
        $this->assertSame(CompanyProfile::CEO_PHOTO, $leader->photo->path);
        $this->assertSame(CompanyProfile::CEO_ALT, $leader->photo->alt);
        foreach (['sm' => [160, 200], 'md' => [240, 300], 'lg' => [360, 450], 'original' => [361, 481]] as $version => $dimensions) {
            $path = public_path("assets/img/v2/team/ceo-portrait-{$version}.webp");
            $this->assertSame($dimensions, array_slice(getimagesize($path), 0, 2));
            $this->assertLessThanOrEqual(40000, filesize($path));
        }
        $this->assertStringEndsWith('team/ceo-portrait-sm.webp', media_url($leader->photo, 'sm'));

        foreach (['/gioi-thieu', '/doi-ngu'] as $path) {
            $document = $this->html($this->get($path)->assertOk()->getContent());
            $schema = collect($this->schema($document));
            $person = $schema->firstWhere('@type', 'Person');
            $this->assertSameJsonValue([
                '@type' => 'Person',
                'name' => CompanyProfile::CEO_NAME,
                'jobTitle' => CompanyProfile::CEO_ROLE,
                'image' => seo_url('/assets/'.CompanyProfile::CEO_PHOTO),
                'worksFor' => ['@id' => $schema->firstWhere('@type', 'Organization')['@id']],
            ], $person);
        }
        $leader->update(['name' => CompanyProfile::CEO_ROLE]);
        $team = $this->html($this->get('/doi-ngu')->assertOk()->getContent());
        $this->assertNotContains('Person', array_column($this->schema($team), '@type'));

        $this->artisan('ecotech:apply-positioning', ['--force' => true])->assertSuccessful();
        $this->assertSame(CompanyProfile::CEO_ROLE, $leader->refresh()->name);
        $leader->update(['name' => '', 'position' => null]);
        $this->artisan('ecotech:apply-positioning')->assertSuccessful();
        $this->assertSame('', $leader->refresh()->name);
        $this->artisan('ecotech:apply-positioning', ['--force' => true])->assertSuccessful();
        $this->assertSame(CompanyProfile::CEO_NAME, $leader->refresh()->name);
        $this->assertSame(CompanyProfile::CEO_ROLE, $leader->position);
    }

    public function test_company_update_keeps_edited_facebook_and_attaches_existing_leader(): void
    {
        $this->seed(DatabaseSeeder::class);
        SiteSetting::put('social_facebook', 'https://www.facebook.com/edited', 'social');
        SiteSetting::put('contact_zalo', 'http://zalo.me/1747723032009789626?src=qr', 'contact');
        $leader = TeamMember::whereSlug(CompanyProfile::CEO_SLUG)->firstOrFail();
        $leader->update(['slug' => 'lanh-dao-hien-co', 'name' => 'Hồ sơ đã có', 'photo_media_id' => null]);
        $this->artisan('ecotech:apply-positioning')->assertSuccessful();
        $this->assertNull($leader->refresh()->photo_media_id);
        $this->artisan('ecotech:apply-positioning', ['--force' => true])->assertSuccessful();
        $this->assertSame(CompanyProfile::ZALO, setting('contact_zalo'));
        $this->assertSame('https://www.facebook.com/edited', setting('social_facebook'));
        $this->assertSame('Hồ sơ đã có', $leader->refresh()->name);
        $this->assertSame(CompanyProfile::CEO_PHOTO, $leader->photo->path);
        $this->assertDatabaseCount('team_members', 1);

        $this->actingAs(User::whereEmail(config('happyecotech.admin_email'))->firstOrFail());
        Livewire::test(ManageSettings::class)->fillForm([
            'contact_zalo' => 'http://zalo.me/1747723032009789626',
            'social_facebook' => 'javascript:alert(1)',
        ])->call('save')->assertHasFormErrors(['contact_zalo', 'social_facebook']);
    }

    public function test_home_faq_follows_product_order_and_only_replaces_stock_items(): void
    {
        $this->seed(DatabaseSeeder::class);
        $block = Page::whereSlug('home')->firstOrFail()->blocks()->where('block_type', 'faq')->firstOrFail();
        $items = $block->data['items'];
        $questions = array_column($items, 'question');
        $this->assertSame(['TaskFlow', 'TaskFlow', 'TaskFlow', 'MiniCRM', 'SmartLabel'], array_map(
            fn (string $question) => preg_match('/TaskFlow|MiniCRM|SmartLabel/', $question, $match) ? $match[0] : '',
            $questions,
        ));
        $document = $this->html($this->get('/')->assertOk()->getContent());
        $visible = $document->evaluate('string(//main)');
        $positions = array_map(fn (string $question) => mb_strpos($visible, $question), $questions);
        $this->assertNotContains(false, $positions);
        $sorted = $positions;
        sort($sorted);
        $this->assertSame($positions, $sorted);
        $faq = collect($this->schema($document))->firstWhere('@type', 'FAQPage');
        $this->assertSame($questions, array_column($faq['mainEntity'], 'name'));
        $this->assertSame(array_column($items, 'answer'), array_column(array_column($faq['mainEntity'], 'acceptedAnswer'), 'text'));

        $legacy = LandingCopy::LEGACY_HOME['faq'];
        $legacy['items'] = array_map(fn (array $item) => array_reverse($item, true), $legacy['items']);
        $block->update(['data' => $legacy + ['editor_note' => 'Giữ nguyên']]);
        $this->artisan('ecotech:apply-positioning')->assertSuccessful();
        $this->assertSameJsonValue($legacy['items'], $block->refresh()->data['items']);
        $this->artisan('ecotech:apply-positioning', ['--force' => true])->assertSuccessful();
        $this->assertSameJsonValue($items, $block->refresh()->data['items']);
        $this->assertSame('Giữ nguyên', $block->data['editor_note']);

        $custom = [['question' => 'Câu hỏi đã biên tập?', 'answer' => 'Câu trả lời đã biên tập.']];
        $block->update(['data' => array_replace($legacy, ['items' => $custom])]);
        $this->artisan('ecotech:apply-positioning', ['--force' => true])->assertSuccessful();
        $this->assertSameJsonValue($custom, $block->refresh()->data['items']);
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
