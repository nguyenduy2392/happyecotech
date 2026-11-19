<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use Illuminate\Http\Response;
use XMLWriter;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $xml = new XMLWriter;
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElementNs(null, 'urlset', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $robots = strtolower(setting('seo_robots', 'index,follow'));
        if (! str_contains($robots, 'noindex') && ! preg_match('/\bnone\b/', $robots)) {
            foreach ($this->entries() as [$path, $modified]) {
                $xml->startElement('url');
                $xml->writeElement('loc', seo_url($path));
                if ($modified) {
                    $xml->writeElement('lastmod', $modified->toAtomString());
                }
                $xml->endElement();
            }
        }
        $xml->endElement();

        return response($xml->outputMemory(), 200, ['Content-Type' => 'application/xml']);
    }

    public function robots(): Response
    {
        return response("User-agent: *\nDisallow: /admin\nSitemap: ".seo_url('/sitemap.xml')."\n",
            200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    private function entries(): iterable
    {
        $services = Service::published()->get();
        $posts = Post::published()->get();
        yield ['/dich-vu', $services->max('updated_at')];
        yield ['/tin-tuc', $posts->max('updated_at')];
        yield ['/doi-ngu', TeamMember::published()->latest('updated_at')->first()?->updated_at];
        yield ['/lien-he', max($services->max('updated_at'), SiteSetting::where('group', 'contact')->latest('updated_at')->first()?->updated_at)];

        foreach ($services as $service) {
            yield [route('services.show', $service, false), $service->updated_at];
        }
        foreach ($posts as $post) {
            yield [route('posts.show', $post, false), $post->updated_at];
        }
        foreach (PostCategory::withMax(['posts as posts_updated_at' => fn ($query) => $query->published()], 'posts.updated_at')
            ->withCasts(['posts_updated_at' => 'datetime'])->get() as $category) {
            yield [route('posts.category', $category, false), max($category->updated_at, $category->posts_updated_at)];
        }
        foreach (Page::where('status', 'published')->withMax('enabledBlocks', 'updated_at')
            ->withCasts(['enabled_blocks_max_updated_at' => 'datetime'])->get() as $page) {
            $path = match ($page->slug) {
                'home' => '/',
                'gioi-thieu' => '/gioi-thieu',
                default => ! in_array($page->slug, Page::FIXED_ROUTES, true) && preg_match('/^'.Page::PUBLIC_SLUG_PATTERN.'$/D', $page->slug)
                    ? '/'.$page->slug : null,
            };
            if ($path) {
                yield [$path, max($page->updated_at, $page->enabled_blocks_max_updated_at)];
            }
        }
    }
}
