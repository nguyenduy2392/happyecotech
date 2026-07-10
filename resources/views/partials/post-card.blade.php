<div class="col-lg-4 col-md-6">
    <div class="single-news-card h-100">
        <a href="{{ route('posts.show', $post) }}" class="news-thumb">
            <img src="{{ $post->featuredMedia?->url ?? asset('assets/img/portfolio/portfolio-1.jpg') }}"
                alt="{{ $post->title }}" loading="lazy"
                onerror="this.src='{{ asset('assets/img/portfolio/portfolio-1.jpg') }}'">
        </a>
        <div class="news-body">
            @if ($post->published_at)
                <span class="news-date"><i class="lni lni-calendar"></i> {{ $post->published_at->format('d/m/Y') }}</span>
            @endif
            <h3><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></h3>
            <p>{{ \Illuminate\Support\Str::limit($post->excerpt, 100) }}</p>
            <a href="{{ route('posts.show', $post) }}" class="news-more">Đọc tiếp <i class="lni lni-arrow-right"></i></a>
        </div>
    </div>
</div>
