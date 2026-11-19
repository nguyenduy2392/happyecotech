<?php

namespace Tests\Contracts;

use App\Models\Post;
use App\Models\PostCategory;
use Database\Seeders\DatabaseSeeder;
use DOMDocument;
use DOMXPath;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

// Run this contract against the integrated public views and SEO backend.
class PublicHeadingsTest extends TestCase
{
    use RefreshDatabase;

    public static function primaryPages(): array
    {
        return [
            'home' => ['/'], 'about' => ['/gioi-thieu'], 'services' => ['/dich-vu'],
            'service detail' => ['/dich-vu/smartlabel'], 'posts' => ['/tin-tuc'],
            'category' => ['category'], 'post detail' => ['post'], 'team' => ['/doi-ngu'],
            'contact' => ['/lien-he'], '404' => ['/nonexistent', 404],
        ];
    }

    #[DataProvider('primaryPages')]
    public function test_public_page_has_exactly_one_h1(string $path, int $status = 200): void
    {
        $this->seed(DatabaseSeeder::class);
        $path = match ($path) {
            'category' => route('posts.category', PostCategory::firstOrFail()),
            'post' => route('posts.show', Post::published()->firstOrFail()),
            default => $path,
        };
        $html = $this->get($path)->assertStatus($status)->getContent();
        $document = new DOMDocument;
        @$document->loadHTML('<?xml encoding="UTF-8">'.$html);
        $this->assertEquals(1, (new DOMXPath($document))->evaluate('count(//h1)'), $path);
    }
}
