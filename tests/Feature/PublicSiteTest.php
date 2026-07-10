<?php

namespace Tests\Feature;

use App\Models\ContactSubmission;
use App\Models\Service;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_home_page_renders_with_seeded_content(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Minh bạch từ gốc', false)
            ->assertSee('SmartLabel');
    }

    public function test_published_service_detail_ok_but_draft_returns_404(): void
    {
        $this->get('/dich-vu/smartlabel')->assertOk()->assertSee('SmartLabel');

        $draft = Service::create([
            'name' => 'Dịch vụ nháp', 'slug' => 'dich-vu-nhap', 'status' => 'draft',
        ]);

        $this->get('/dich-vu/'.$draft->slug)->assertNotFound();
    }

    public function test_service_list_shows_only_published(): void
    {
        Service::create(['name' => 'Ẩn', 'slug' => 'an', 'status' => 'draft']);

        $response = $this->get('/dich-vu')->assertOk();
        $response->assertSee('SmartLabel');
        $response->assertDontSee('Ẩn');
    }

    public function test_contact_form_stores_lead_and_redirects(): void
    {
        $response = $this->post('/lien-he', [
            'name' => 'Nguyễn Văn A',
            'phone' => '0900000000',
            'email' => 'a@example.com',
            'message' => 'Tôi quan tâm dịch vụ',
            'consent' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('contact_submissions', [
            'name' => 'Nguyễn Văn A',
            'status' => 'new',
        ]);
    }

    public function test_contact_honeypot_silently_dropped(): void
    {
        $this->post('/lien-he', [
            'name' => 'Bot', 'phone' => '0900000000', 'message' => 'spam', 'website' => 'http://spam',
        ])->assertRedirect();

        $this->assertSame(0, ContactSubmission::count());
    }

    public function test_contact_validation_errors(): void
    {
        $this->from('/lien-he')
            ->post('/lien-he', ['name' => '', 'phone' => '', 'message' => ''])
            ->assertSessionHasErrors(['name', 'phone', 'message']);
    }

    public function test_sitemap_lists_published_service(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('/dich-vu/smartlabel', false);
    }

    public function test_posts_index_and_detail_render(): void
    {
        $this->get('/tin-tuc')->assertOk();
        $this->get('/tin-tuc/chao-mung-den-voi-happy-ecotech')->assertOk()->assertSee('Happy Ecotech');
    }
}
