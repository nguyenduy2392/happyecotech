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
            'seo' => he_seo(['title' => 'Tin tức', 'description' => 'Tin tức mới nhất từ Happy Ecotech.']),
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
            ]),
        ]);
    }

    public function show(Post $post)
    {
        abort_unless($post->isPublished(), 404);

        $post->load(['featuredMedia', 'author', 'categories', 'tags']);

        $categoryIds = $post->categories->pluck('id');
        $related = Post::query()->published()
            ->where('id', '!=', $post->id)
            ->when($categoryIds->isNotEmpty(), fn ($q) => $q->whereHas('categories', fn ($c) => $c->whereIn('post_categories.id', $categoryIds)))
            ->latest('published_at')->limit(3)->get();

        return view('public.posts.show', [
            'post' => $post,
            'related' => $related,
            'seo' => he_seo([
                'title' => $post->seo_title ?: $post->title,
                'description' => $post->seo_description ?: $post->excerpt,
                'canonical' => $post->seo_canonical ?: route('posts.show', $post),
                'og_image' => $post->ogImage?->url ?? $post->featuredMedia?->url,
                'type' => 'article',
            ]),
        ]);
    }

    private function categories()
    {
        return PostCategory::query()->ordered()->withCount(['posts' => fn ($q) => $q->published()])->get();
    }
}
