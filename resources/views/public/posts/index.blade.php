@extends('layouts.public')

@section('content')
    @include('partials.page-hero', [
        'title' => $activeCategory->name ?? 'Tin tức',
        'subtitle' => $activeCategory->description ?? 'Tin tức và kiến thức từ Happy Ecotech.',
        'breadcrumbs' => isset($activeCategory)
            ? [['label' => 'Tin tức', 'url' => route('posts.index')], ['label' => $activeCategory->name]]
            : [['label' => 'Tin tức']],
    ])

    <section class="blog-section pt-100 pb-70">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="row g-4">
                        @forelse ($posts as $post)
                            @include('partials.post-card', ['post' => $post])
                        @empty
                            <div class="col-12"><p>Chưa có bài viết nào.</p></div>
                        @endforelse
                    </div>

                    <div class="pt-40 d-flex justify-content-center">
                        {{ $posts->onEachSide(1)->links() }}
                    </div>
                </div>

                <div class="col-lg-4">
                    <aside class="blog-sidebar">
                        <h4>Danh mục</h4>
                        <ul class="category-list">
                            @foreach ($categories as $category)
                                <li>
                                    <a href="{{ route('posts.category', $category) }}"
                                        class="{{ isset($activeCategory) && $activeCategory->id === $category->id ? 'active' : '' }}">
                                        {{ $category->name }} <span>({{ $category->posts_count }})</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </aside>
                </div>
            </div>
        </div>
    </section>
@endsection
