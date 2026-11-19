<?php

namespace App\Http\Controllers;

use App\Http\Middleware\NormalizePublicUrl;
use App\Models\Page;
use App\Models\Post;
use App\Models\Service;
use App\Models\TeamMember;
use App\Support\CompanyProfile;
use App\Support\RequestMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function home(): View
    {
        return $this->page(Page::whereSlug('home')->whereStatus('published')->firstOrFail());
    }

    public function about(Request $request): View|RedirectResponse
    {
        $page = Page::whereSlug('gioi-thieu')->first();
        if ($page) {
            return $this->page($page);
        }
        $path = Page::redirectPath('/gioi-thieu');
        abort_unless($path, 404);

        return NormalizePublicUrl::redirect($request, $path);
    }

    public function page(Page $page): View
    {
        abort_unless($page->status === 'published', 404);
        $page->load(['blocks' => fn ($query) => $query->where('is_enabled', true), 'ogImage']);
        $services = $page->blocks->firstWhere('block_type', 'featured_services');
        $news = $page->blocks->firstWhere('block_type', 'latest_news');
        $hero = $page->blocks->firstWhere('block_type', 'hero');
        $isHome = $page->slug === 'home';

        $ids = $page->blocks->flatMap(function ($block) {
            $data = $block->data ?? [];

            return [
                ...array_values(array_filter($data, fn ($key) => str_ends_with($key, '_media_id'), ARRAY_FILTER_USE_KEY)),
                ...((array) ($data['logos'] ?? [])),
            ];
        });
        $media = app(RequestMedia::class);
        $media->preload($ids);
        $image = $page->ogImage;
        if ($hero && filled($hero->data['h1'] ?? null)) {
            // Existing views continue to consume the effective heading.
            $hero->data = array_replace($hero->data, ['heading' => $hero->data['h1']]);
        }
        $this->resolveSolutionLinks($page);
        $leader = $page->slug === 'gioi-thieu'
            ? TeamMember::published()->whereSlug(CompanyProfile::CEO_SLUG)->with('photo')->first()
            : null;

        return view('public.page', [
            'page' => $page,
            'featuredServices' => $services ? Service::published()->with(['icon', 'cover'])->where('is_featured', true)
                ->ordered()->limit($this->limit($services->data['limit'] ?? null, 4))->get() : collect(),
            'latestPosts' => $news ? Post::published()->with(['featuredMedia', 'categories'])->latest('published_at')
                ->limit($this->limit($news->data['limit'] ?? null, 3))->get() : collect(),
            'seo' => he_seo([
                'title' => $page->seo_title ?: $page->title,
                'standalone_title' => $isHome && filled($page->seo_title),
                'description' => $page->seo_description ?: ($hero?->data['subheading'] ?? null),
                'canonical' => seo_url($isHome ? '/' : '/'.$page->slug),
                'og_image' => $image?->url ?? ($page->slug === 'gioi-thieu' ? seo_url('/assets/img/v2/og-about-contact.webp') : null),
                'image_alt' => $image?->alt ?: $page->title,
                'people' => array_values(array_filter([CompanyProfile::person($leader)])),
                'faq' => $page->blocks->where('block_type', 'faq')->flatMap(fn ($block) => $block->data['items'] ?? [])->all(),
            ]),
        ]);
    }

    private function resolveSolutionLinks(Page $page): void
    {
        $blocks = $page->blocks->where('block_type', 'other_solutions');
        $keys = $blocks->flatMap(fn ($block) => array_column($block->data['items'] ?? [], 'key'))
            ->filter(fn ($key) => is_string($key) && preg_match('/\A[a-z0-9]+(?:-[a-z0-9]+)*\z/', $key))->unique();
        if ($keys->isEmpty()) {
            return;
        }

        $published = Service::published()->whereIn('slug', $keys)->pluck('slug')->flip();
        foreach ($blocks as $block) {
            $data = $block->data;
            $data['items'] = array_map(function ($item) use ($keys, $published) {
                $slug = $item['key'] ?? null;
                if (is_string($slug) && $keys->containsStrict($slug)) {
                    $public = '/dich-vu/'.$slug;
                    $contact = '/lien-he?solution='.$slug;
                    $url = $item['url'] ?? null;
                    if (blank($url) || in_array($url, [$public, $contact], true)) {
                        $item['url'] = $published->has($slug) ? $public : $contact;
                    }
                }

                return $item;
            }, $data['items'] ?? []);
            $block->data = $data;
        }
    }

    private function limit(mixed $value, int $default): int
    {
        return max(1, min(24, is_numeric($value) ? (int) $value : $default));
    }
}
