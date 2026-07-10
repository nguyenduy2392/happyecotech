<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Post;
use App\Models\Service;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [];

        $urls[] = ['loc' => route('home'), 'priority' => '1.0'];
        $urls[] = ['loc' => route('about'), 'priority' => '0.7'];
        $urls[] = ['loc' => route('services.index'), 'priority' => '0.8'];
        $urls[] = ['loc' => route('team'), 'priority' => '0.6'];
        $urls[] = ['loc' => route('posts.index'), 'priority' => '0.7'];
        $urls[] = ['loc' => route('contact'), 'priority' => '0.6'];

        foreach (Service::query()->published()->ordered()->get() as $service) {
            $urls[] = ['loc' => route('services.show', $service), 'lastmod' => $service->updated_at, 'priority' => '0.7'];
        }

        foreach (Post::query()->published()->latest('published_at')->get() as $post) {
            $urls[] = ['loc' => route('posts.show', $post), 'lastmod' => $post->updated_at, 'priority' => '0.6'];
        }

        foreach (Page::query()->where('status', 'published')->whereNotIn('slug', ['home', 'gioi-thieu'])->get() as $page) {
            $urls[] = ['loc' => route('page.show', $page), 'lastmod' => $page->updated_at, 'priority' => '0.4'];
        }

        return response()
            ->view('public.sitemap', ['urls' => $urls])
            ->header('Content-Type', 'text/xml');
    }
}
