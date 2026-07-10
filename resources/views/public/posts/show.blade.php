@extends('layouts.public')

@section('content')
    @include('partials.page-hero', [
        'title' => $post->title,
        'breadcrumbs' => [
            ['label' => 'Tin tức', 'url' => route('posts.index')],
            ['label' => \Illuminate\Support\Str::limit($post->title, 40)],
        ],
    ])

    <section class="blog-detail-section pt-80 pb-80">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <article class="post-single">
                        @if ($post->featuredMedia?->url)
                            <img class="post-cover mb-30" src="{{ $post->featuredMedia->url }}" alt="{{ $post->title }}">
                        @endif

                        <div class="post-meta mb-25">
                            @if ($post->published_at)
                                <span><i class="lni lni-calendar"></i> {{ $post->published_at->format('d/m/Y') }}</span>
                            @endif
                            @if ($post->author)
                                <span><i class="lni lni-user"></i> {{ $post->author->name }}</span>
                            @endif
                            @foreach ($post->categories as $category)
                                <a class="post-cat" href="{{ route('posts.category', $category) }}">{{ $category->name }}</a>
                            @endforeach
                        </div>

                        <div class="richtext-content">{!! $post->content !!}</div>

                        @if ($post->tags->isNotEmpty())
                            <div class="post-tags mt-30">
                                @foreach ($post->tags as $tag)
                                    <span class="tag">#{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </article>

                    @if ($related->isNotEmpty())
                        <div class="related-posts mt-50">
                            <h3 class="mb-30">Bài viết liên quan</h3>
                            <div class="row g-4">
                                @foreach ($related as $post)
                                    @include('partials.post-card', ['post' => $post])
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
