<?php

namespace Tests\Feature;

use App\Models\ContactSubmission;
use App\Models\Service;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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
            ->assertSee('TaskFlow giúp đội nhóm giao việc rõ ràng', false)
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

    public function test_lead_can_omit_message_and_stores_company_solution_and_source(): void
    {
        $service = Service::whereSlug('smartlabel')->firstOrFail();
        $this->post('/lien-he', [
            'name' => 'Người gửi thử', 'phone' => '0900000000', 'company' => 'Công ty Demo',
            'service_id' => $service->id, 'source' => 'lead-form', 'consent' => '1',
        ])->assertRedirect()->assertSessionHas('contact_success');
        $this->assertDatabaseHas('contact_submissions', [
            'company' => 'Công ty Demo', 'service_interest' => 'SmartLabel', 'source' => 'lead-form', 'message' => null,
        ]);
    }

    public function test_contact_validation_errors(): void
    {
        $this->from('/lien-he')
            ->post('/lien-he', ['name' => '', 'phone' => '', 'message' => ''])
            ->assertSessionHasErrors(['name', 'phone', 'message']);
    }

    public function test_contact_returns_json_success_and_field_errors(): void
    {
        $this->postJson('/lien-he', [
            'name' => 'Người gửi thử', 'phone' => '0900000000', 'source' => 'lead-form', 'consent' => '1',
        ])->assertOk()->assertExactJson([
            'ok' => true, 'message' => 'Cảm ơn bạn. Happy Ecotech đã nhận được thông tin và sẽ sớm liên hệ.',
        ]);

        $this->post('/lien-he', ['consent' => '1'], [
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'text/html',
        ])->assertUnprocessable()->assertExactJson([
            'ok' => false,
            'errors' => [
                'name' => ['Vui lòng nhập họ tên.'],
                'phone' => ['Vui lòng nhập số điện thoại.'],
                'message' => ['Vui lòng nhập nội dung.'],
            ],
        ]);
        $this->postJson('/lien-he', ['website' => 'https://example.test'])->assertOk()->assertJsonPath('ok', true);
        $this->assertDatabaseCount('contact_submissions', 1);
    }

    public function test_lead_requires_consent_and_vietnamese_phone_format(): void
    {
        $lead = ['name' => 'Người gửi thử', 'phone' => '12345', 'source' => 'lead-form'];
        $this->postJson('/lien-he', $lead)->assertUnprocessable()->assertJsonValidationErrors(['phone', 'consent']);
        $this->postJson('/lien-he', array_replace($lead, ['phone' => '+84 936.685.886', 'consent' => '1']))
            ->assertOk()->assertJsonPath('ok', true);
        $this->post('/lien-he', ['name' => 'Người gửi thử', 'phone' => '024 1234.5678', 'message' => 'Tư vấn'])
            ->assertRedirect()->assertSessionHas('contact_success');
        $this->assertDatabaseHas('contact_submissions', ['phone' => '024 1234.5678', 'consent' => false]);
    }

    public function test_retry_keeps_one_submission_and_one_notification(): void
    {
        Mail::shouldReceive('raw')->once();
        $lead = [
            'name' => 'Người gửi thử', 'phone' => '0936685886', 'source' => 'lead-form',
            'consent' => '1', 'idempotency_key' => (string) Str::uuid(),
        ];
        $first = $this->postJson('/lien-he', $lead)->assertOk()->assertJsonPath('ok', true)->json();
        $this->postJson('/lien-he', array_replace($lead, [
            'name' => 'Không ghi đè', 'idempotency_key' => strtoupper($lead['idempotency_key']),
        ]))->assertOk()->assertExactJson($first);
        $this->assertDatabaseCount('contact_submissions', 1);
        $this->assertDatabaseHas('contact_submissions', [
            'idempotency_key' => $lead['idempotency_key'], 'name' => $lead['name'],
        ]);
    }

    public function test_only_new_submissions_consume_throttle_and_invalid_keys_are_ignored(): void
    {
        $lead = ['name' => 'Người gửi thử', 'phone' => '0936685886', 'source' => 'lead-form', 'consent' => '1'];
        $key = (string) Str::uuid();
        $expected = $this->postJson('/lien-he', $lead + ['idempotency_key' => $key])->assertOk()->json();
        for ($i = 0; $i < 8; $i++) {
            $this->postJson('/lien-he', ['idempotency_key' => $key])->assertOk()->assertExactJson($expected);
        }
        foreach (['invalid', 'a1b2c3d4-5678-1234-8234-123456789abc', ['invalid'], null] as $invalid) {
            $this->postJson('/lien-he', $lead + ['idempotency_key' => $invalid])->assertOk();
        }
        $this->postJson('/lien-he', $lead)->assertTooManyRequests();
        $this->postJson('/lien-he', ['idempotency_key' => $key])->assertOk()->assertExactJson($expected);
        $this->assertDatabaseCount('contact_submissions', 5);
        $this->assertSame(4, ContactSubmission::whereNull('idempotency_key')->count());
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
