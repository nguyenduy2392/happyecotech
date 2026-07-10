@if (($latestPosts ?? collect())->isNotEmpty())
    <section class="latest-news-section pt-100 pb-50">
        <div class="container">
            <div class="section-title text-center mb-50">
                <h1>{{ $data['heading'] ?? 'Tin tức mới nhất' }}</h1>
            </div>
            <div class="row g-4">
                @foreach ($latestPosts as $post)
                    @include('partials.post-card', ['post' => $post])
                @endforeach
            </div>
            <div class="view-all-btn text-center pt-40">
                <a href="{{ route('posts.index') }}" class="main-btn btn-hover">Xem tất cả tin tức</a>
            </div>
        </div>
    </section>
@endif
