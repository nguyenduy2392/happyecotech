<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Post;
use App\Models\Service;

class HomeController extends Controller
{
    public function home()
    {
        $page = Page::where('slug', 'home')->where('status', 'published')
            ->with(['blocks' => fn ($q) => $q->where('is_enabled', true)->orderBy('sort_order')])
            ->firstOrFail();

        return view('public.page', [
            'page' => $page,
            'featuredServices' => $this->featuredServices(),
            'latestPosts' => $this->latestPosts(),
            'seo' => he_seo([
                'title' => null,
                'description' => $page->seo_description,
                'canonical' => route('home'),
            ]),
        ]);
    }

    public function about()
    {
        $page = Page::where('slug', 'gioi-thieu')->where('status', 'published')
            ->with(['blocks' => fn ($q) => $q->where('is_enabled', true)->orderBy('sort_order')])
            ->firstOrFail();

        return view('public.page', [
            'page' => $page,
            'featuredServices' => $this->featuredServices(),
            'latestPosts' => $this->latestPosts(),
            'seo' => he_seo([
                'title' => $page->seo_title ?: $page->title,
                'description' => $page->seo_description,
            ]),
        ]);
    }

    public function page(Page $page)
    {
        abort_unless($page->status === 'published', 404);

        $page->load(['blocks' => fn ($q) => $q->where('is_enabled', true)->orderBy('sort_order')]);

        return view('public.page', [
            'page' => $page,
            'featuredServices' => $this->featuredServices(),
            'latestPosts' => $this->latestPosts(),
            'seo' => he_seo([
                'title' => $page->seo_title ?: $page->title,
                'description' => $page->seo_description,
            ]),
        ]);
    }

    private function featuredServices()
    {
        return Service::query()->published()->where('is_featured', true)->ordered()->limit(4)->get();
    }

    private function latestPosts()
    {
        return Post::query()->published()->latest('published_at')->limit(3)->get();
    }
}
