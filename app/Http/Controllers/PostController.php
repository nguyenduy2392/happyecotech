<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostCategory;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::query()->published()->with(['featuredMedia', 'categories'])
            ->latest('published_at')->paginate(9);

        return view('public.posts.index', [
            'posts' => $posts,
            'categories' => $this->categories(),
            'seo' => he_seo([
                'title' => 'Tin tức',
                'description' => 'Tin tức mới nhất từ '.setting('site_name', config('app.name')).'.',
                'og_image' => seo_url('/assets/img/v2/og-news.webp'),
                'canonical' => $this->paginatedCanonical('posts.index', $posts->currentPage()),
            ]),
        ]);
    }

    public function category(PostCategory $category)
    {
        $posts = $category->posts()->published()->with(['featuredMedia', 'categories'])
            ->latest('published_at')->paginate(9);

        return view('public.posts.index', [
            'posts' => $posts,
            'categories' => $this->categories(),
            'activeCategory' => $category,
            'seo' => he_seo([
                'title' => $category->name,
                'description' => $category->description ?: ('Bài viết thuộc danh mục '.$category->name),
                'og_image' => seo_url('/assets/img/v2/og-news.webp'),
                'canonical' => $this->paginatedCanonical('posts.category', $posts->currentPage(), ['category' => $category]),
                'breadcrumbs' => [['Tin tức', seo_url('/tin-tuc')]],
            ]),
        ]);
    }

    public function show(Post $post)
    {
        abort_unless($post->isPublished(), 404);

        $post->load(['featuredMedia', 'ogImage', 'author', 'categories', 'tags']);

        $categoryIds = $post->categories->pluck('id');
        $related = Post::query()->published()->with('featuredMedia')
            ->where('id', '!=', $post->id)
            ->when($categoryIds->isNotEmpty(), fn ($q) => $q->whereHas('categories', fn ($c) => $c->whereIn('post_categories.id', $categoryIds)))
            ->latest('published_at')->limit(3)->get();

        return view('public.posts.show', [
            'post' => $post,
            'related' => $related,
            'seo' => he_seo([
                'title' => $post->seo_title ?: $post->title,
                'description' => $post->seo_description ?: $post->excerpt,
                'canonical' => seo_url(route('posts.show', $post, false)),
                'og_image' => ($image = $post->ogImage ?? $post->featuredMedia)?->url,
                'image_alt' => $image?->alt ?: $post->title,
                'type' => 'article',
                'breadcrumbs' => [['Tin tức', seo_url('/tin-tuc')]],
                'entity' => [
                    '@type' => 'BlogPosting',
                    'headline' => $post->title,
                    'datePublished' => $post->published_at?->toAtomString(),
                    'dateModified' => $post->updated_at?->toAtomString(),
                    'author' => $post->author ? ['@type' => 'Person', 'name' => $post->author->name] : null,
                    'publisher' => ['@id' => seo_url('/').'#organization'],
                ],
            ]),
        ]);
    }

    private function categories()
    {
        return PostCategory::query()->ordered()->withCount(['posts' => fn ($q) => $q->published()])->get();
    }

    private function paginatedCanonical(string $name, int $page, array $parameters = []): string
    {
        $requestedPage = request()->query('page');
        $hasPage = is_scalar($requestedPage) && (string) $requestedPage === (string) $page;

        return seo_url(route($name, $parameters + ($page > 1 || $hasPage ? ['page' => $page] : []), false));
    }
}
