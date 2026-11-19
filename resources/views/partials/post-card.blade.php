{{-- Thẻ bài viết. Biến: $post (cần featuredMedia, categories). --}}
@php $category = $post->categories->first(); @endphp
<article class="card group h-full overflow-hidden !p-0">
    @if ($post->featuredMedia)
        <a href="{{ route('posts.show', $post) }}" class="block aspect-[16/9] overflow-hidden bg-ink-100" tabindex="-1" aria-hidden="true">
            <x-media-img :media="$post->featuredMedia" :alt="''" width="640" height="360" sizes="(min-width: 1024px) 380px, 100vw"
                class="size-full object-cover" />
        </a>
    @elseif (is_file(public_path('assets/img/v2/post-fallback.webp')))
        <x-art name="post-fallback" width="1280" height="720" :alt="''" class="w-full" sizes="(min-width: 1024px) 380px, 100vw" />
    @endif
    <div class="flex flex-1 flex-col gap-3 p-6">
        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-ink-500">
            @if ($category)
                <a href="{{ route('posts.category', $category) }}" class="badge no-underline">{{ $category->name }}</a>
            @endif
            @if ($post->published_at)
                <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('d/m/Y') }}</time>
            @endif
        </div>
        <{{ $tag = $headingTag ?? 'h3' }} class="text-xl">
            <a href="{{ route('posts.show', $post) }}" class="no-underline hover:text-brand-700 hover:underline">{{ $post->title }}</a>
        </{{ $tag }}>
        @if ($post->excerpt)
            <p class="line-clamp-3 text-ink-700">{{ $post->excerpt }}</p>
        @endif
    </div>
</article>
