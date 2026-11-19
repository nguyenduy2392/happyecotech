@extends('layouts.public')

@section('content')
    @include('partials.page-hero', [
        'title' => isset($activeCategory) ? $activeCategory->name : 'Tin tức',
        'lead' => isset($activeCategory) ? ($activeCategory->description ?: null) : 'Cập nhật mới về tem điện tử, truy xuất nguồn gốc, chuyển đổi số và hoạt động của Happy Ecotech.',
        'crumbs' => isset($activeCategory)
            ? [['label' => 'Tin tức', 'url' => route('posts.index')], ['label' => $activeCategory->name]]
            : [['label' => 'Tin tức']],
    ])

    <section class="section">
        <div class="container-page">
            @if (($categories ?? collect())->isNotEmpty())
                <nav aria-label="Danh mục tin tức" class="mb-10">
                    <ul class="flex flex-wrap gap-2">
                        <li>
                            <a href="{{ route('posts.index') }}" class="btn {{ isset($activeCategory) ? 'btn-outline' : 'btn-primary' }}"
                                @unless (isset($activeCategory)) aria-current="page" @endunless>Tất cả</a>
                        </li>
                        @foreach ($categories as $cat)
                            @php $isActive = isset($activeCategory) && $activeCategory->id === $cat->id; @endphp
                            <li>
                                <a href="{{ route('posts.category', $cat) }}" class="btn {{ $isActive ? 'btn-primary' : 'btn-outline' }}"
                                    @if ($isActive) aria-current="page" @endif>{{ $cat->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            @endif

            @if ($posts->isEmpty())
                <p class="text-lead text-ink-700">Chưa có bài viết nào trong mục này.</p>
            @else
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($posts as $post)
                        @include('partials.post-card', ['post' => $post, 'headingTag' => 'h2'])
                    @endforeach
                </div>
                {{ $posts->links('partials.pagination') }}
            @endif
        </div>
    </section>
@endsection
