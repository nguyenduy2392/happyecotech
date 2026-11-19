@extends('layouts.public')

@section('content')
    <article>
        <header class="section-brand border-b border-ink-100">
            <div class="container-page py-10 md:py-14">
                <x-breadcrumbs :items="[['label' => 'Tin tức', 'url' => route('posts.index')], ['label' => $post->title]]" />
                <h1 class="mt-4 max-w-3xl text-display">{{ $post->title }}</h1>
                <div class="mt-5 flex flex-wrap items-center gap-x-4 gap-y-2 text-ink-700">
                    @if ($post->published_at)
                        <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->format('d/m/Y') }}</time>
                    @endif
                    @if ($post->author)
                        <span>Tác giả: {{ $post->author->name }}</span>
                    @endif
                    @foreach ($post->categories as $category)
                        <a href="{{ route('posts.category', $category) }}" class="badge no-underline">{{ $category->name }}</a>
                    @endforeach
                </div>
            </div>
        </header>

        <div class="section">
            <div class="container-page">
                @if ($post->featuredMedia)
                    <x-media-img :media="$post->featuredMedia" :alt="$post->title" :priority="true"
                        sizes="(min-width: 1200px) 1100px, 100vw" class="mb-10 w-full max-w-4xl rounded-xl" />
                @endif
                <div class="prose-he">{!! $post->content !!}</div>

                @if ($post->tags->isNotEmpty())
                    <ul class="mt-10 flex flex-wrap gap-2" aria-label="Thẻ">
                        @foreach ($post->tags as $tag)
                            <li><span class="badge badge-ink">{{ $tag->name }}</span></li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </article>

    @if ($related->isNotEmpty())
        <section class="section section-tint" aria-labelledby="related-title">
            <div class="container-page">
                <h2 id="related-title" class="mb-8 text-h2">Bài viết liên quan</h2>
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $item)
                        @php $item->loadMissing(['featuredMedia', 'categories']); @endphp
                        @include('partials.post-card', ['post' => $item])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="section section-ink" aria-labelledby="post-cta-title">
        <div class="container-page flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">
            <div>
                <h2 id="post-cta-title" class="text-h2">Cần tư vấn giải pháp phù hợp?</h2>
                <p class="mt-3 max-w-xl text-ink-300">Để lại thông tin, đội ngũ Happy Ecotech sẽ liên hệ lại với bạn.</p>
            </div>
            <a href="{{ route('contact') }}#contact-form" data-cta="post-end" class="btn btn-light btn-lg">Nhận tư vấn</a>
        </div>
    </section>
@endsection
