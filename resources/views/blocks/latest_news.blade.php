@php
    $posts = ($latestPosts ?? \App\Models\Post::query()->published()->latest('published_at')->limit(3)->get())
        ->take((int) ($data['limit'] ?? 3))
        ->loadMissing(['featuredMedia', 'categories']);
@endphp
@if ($posts->isNotEmpty())
    <section class="section" aria-labelledby="news-title">
        <div class="container-page">
            <div class="mb-10 flex flex-wrap items-end justify-between gap-4 md:mb-14">
                <h2 id="news-title" class="text-h2">{{ $data['heading'] ?? 'Tin tức mới' }}</h2>
                <a href="{{ route('posts.index') }}" class="btn btn-ghost">Xem tất cả tin tức <x-glyph name="arrow-right" :size="18" /></a>
            </div>
            <div class="grid gap-6 {{ $posts->count() >= 3 ? 'md:grid-cols-2 lg:grid-cols-3' : ($posts->count() === 2 ? 'md:grid-cols-2' : '') }}">
                @foreach ($posts as $post)
                    @include('partials.post-card', ['post' => $post, 'horizontal' => $posts->count() === 1])
                @endforeach
            </div>
        </div>
    </section>
@endif
