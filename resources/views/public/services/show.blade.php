@extends('layouts.public')

@php
    $playbook = (require resource_path('views/public/services/playbook.php'))[$service->slug] ?? [];
    $cms = fn (string $key) => is_array($service->getAttribute($key) ?? null) ? $service->getAttribute($key) : null;

    $h1 = $service->getAttribute('h1') ?: ($playbook['h1'] ?? $service->name);
    $benefits = collect($cms('benefits') ?? $playbook['benefits'] ?? []);
    $steps = collect($cms('steps') ?? $playbook['steps'] ?? []);
    $faq = collect($cms('faq') ?? [
        ['question' => 'Tôi có thể trải nghiệm demo '.$service->name.' không?', 'answer' => 'Có. Để lại thông tin liên hệ, đội ngũ Happy Ecotech sẽ sắp xếp buổi demo phù hợp với nhu cầu của bạn.'],
        ['question' => 'Chi phí sử dụng '.$service->name.' được tính thế nào?', 'answer' => 'Chi phí phụ thuộc quy mô và phạm vi sử dụng. Chúng tôi tư vấn và báo giá cụ thể sau khi trao đổi nhu cầu với bạn.'],
        ['question' => 'Tôi cần hỗ trợ triển khai thì liên hệ ở đâu?', 'answer' => 'Bạn có thể gọi điện, nhắn Zalo hoặc gửi biểu mẫu ở cuối trang, đội ngũ sẽ phản hồi và hỗ trợ từng bước.'],
    ]);

    $contentText = trim(strip_tags((string) $service->content));
    $hasDetail = $contentText !== '' && $contentText !== trim((string) $service->short_description);

    $relatedPosts = \App\Models\Post::query()->published()->with(['featuredMedia', 'categories'])
        ->where(fn ($q) => $q->where('title', 'like', '%'.$service->name.'%')->orWhere('excerpt', 'like', '%'.$service->name.'%'))
        ->latest('published_at')->limit(3)->get();
    if ($relatedPosts->isEmpty()) {
        $relatedPosts = \App\Models\Post::query()->published()->with(['featuredMedia', 'categories'])->latest('published_at')->limit(3)->get();
    }

    $ctaText = $service->cta_text ?: 'Nhận tư vấn';
    $ctaUrl = $service->cta_url ?: (route('contact').'#contact-form');
@endphp

@section('content')
    <section class="section-brand border-b border-ink-100">
        <div class="container-page grid items-center gap-10 py-10 md:py-14 lg:grid-cols-[minmax(0,1.3fr)_minmax(0,0.7fr)]">
            <div>
                <x-breadcrumbs :items="[['label' => 'Giải pháp', 'url' => route('services.index')], ['label' => $service->name]]" />
                <h1 class="mt-4 max-w-3xl text-display">{{ $h1 }}</h1>
                @if ($service->short_description)
                    <p class="mt-5 max-w-2xl text-lead text-ink-700">{{ $service->short_description }}</p>
                @endif
                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                    <a href="{{ $ctaUrl }}" class="btn btn-primary btn-lg">{{ $ctaText }} <x-glyph name="arrow-right" /></a>
                    @if ($service->external_url)
                        <a href="{{ $service->external_url }}" class="btn btn-outline btn-lg" target="_blank" rel="noopener noreferrer">Truy cập {{ $service->name }}</a>
                    @endif
                    @if ($service->app_store_url)
                        <a href="{{ $service->app_store_url }}" class="btn btn-outline btn-lg" target="_blank" rel="noopener noreferrer"><x-glyph name="smartphone" /> App Store</a>
                    @endif
                    @if ($service->google_play_url)
                        <a href="{{ $service->google_play_url }}" class="btn btn-outline btn-lg" target="_blank" rel="noopener noreferrer"><x-glyph name="smartphone" /> Google Play</a>
                    @endif
                </div>
            </div>
            <x-art :name="'service-'.$service->slug" width="800" height="600" :priority="true" :alt="$service->name"
                class="w-full rounded-xl" sizes="(min-width: 1024px) 400px, 100vw" />
        </div>
    </section>

    @if ($benefits->isNotEmpty())
        <section class="section" aria-labelledby="benefits-title">
            <div class="container-page">
                <div class="section-head"><h2 id="benefits-title">Lợi ích khi dùng {{ $service->name }}</h2></div>
                <ul class="grid gap-6 md:grid-cols-3">
                    @foreach ($benefits as $b)
                        <li class="card">
                            <h3 class="flex items-start gap-3"><x-glyph name="check" :size="22" class="mt-0.5 text-brand-600" />{{ $b['title'] ?? '' }}</h3>
                            <p class="mt-2 text-ink-700">{{ $b['text'] ?? $b['description'] ?? '' }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @if ($steps->isNotEmpty())
        <section class="section section-tint" aria-labelledby="how-title">
            <div class="container-page">
                <div class="section-head"><h2 id="how-title">{{ $service->name }} hoạt động thế nào</h2></div>
                <ol class="grid gap-6 md:grid-cols-3">
                    @foreach ($steps as $i => $step)
                        <li class="card">
                            <span class="flex size-11 items-center justify-center rounded-full bg-brand-600 font-semibold text-white" aria-hidden="true">{{ $i + 1 }}</span>
                            <h3 class="mt-4">{{ $step['title'] ?? '' }}</h3>
                            <p class="mt-2 text-ink-700">{{ $step['text'] ?? $step['description'] ?? '' }}</p>
                        </li>
                    @endforeach
                </ol>
                @if ($service->slug === 'smartlabel')
                    <x-art name="process-traceability" width="1280" height="720" class="mx-auto mt-10 w-full max-w-4xl rounded-xl" sizes="(min-width: 960px) 896px, 100vw" />
                @endif
            </div>
        </section>
    @endif

    @if ($service->features->isNotEmpty())
        <section class="section" aria-labelledby="features-title">
            <div class="container-page">
                <div class="section-head"><h2 id="features-title">Tính năng chính</h2></div>
                <ul class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($service->features as $feature)
                        <li class="card">
                            <h3>{{ $feature->title }}</h3>
                            @if ($feature->description)<p class="mt-2 text-ink-700">{{ $feature->description }}</p>@endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    @if ($hasDetail)
        <section class="section section-tint" aria-labelledby="detail-title">
            <div class="container-page">
                <h2 id="detail-title" class="mb-8 text-h2">Chi tiết về {{ $service->name }}</h2>
                <div class="prose-he">{!! $service->content !!}</div>
            </div>
        </section>
    @endif

    @if ($service->gallery->isNotEmpty())
        <section class="section" aria-labelledby="gallery-title">
            <div class="container-page">
                <h2 id="gallery-title" class="mb-8 text-h2">Hình ảnh</h2>
                <ul class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($service->gallery as $media)
                        <li class="overflow-hidden rounded-lg border border-ink-100">
                            <x-media-img :media="$media" :alt="$media->alt ?: $service->name" class="w-full" sizes="(min-width: 1024px) 380px, 100vw" />
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    <section class="section section-tint" aria-labelledby="faq-title">
        <div class="container-page grid gap-10 lg:grid-cols-[1fr_1.6fr] lg:gap-16">
            <div class="section-head !mb-0"><h2 id="faq-title">Câu hỏi về {{ $service->name }}</h2></div>
            <x-faq :items="$faq" />
        </div>
    </section>

    @if ($relatedPosts->isNotEmpty())
        <section class="section" aria-labelledby="related-posts-title">
            <div class="container-page">
                <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
                    <h2 id="related-posts-title" class="text-h2">Bài viết liên quan</h2>
                    <a href="{{ route('posts.index') }}" class="btn btn-ghost">Tất cả tin tức <x-glyph name="arrow-right" :size="18" /></a>
                </div>
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($relatedPosts as $post)
                        @include('partials.post-card', ['post' => $post])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($related->isNotEmpty())
        <section class="section pt-0" aria-labelledby="related-services-title">
            <div class="container-page">
                <h2 id="related-services-title" class="mb-8 text-h2">Giải pháp khác</h2>
                <ul class="grid gap-4 md:grid-cols-3">
                    @foreach ($related as $item)
                        <li>
                            <a href="{{ route('services.show', $item) }}" class="card h-full">
                                <h3>{{ $item->name }}</h3>
                                <p class="mt-2 line-clamp-2 text-ink-700">{{ $item->short_description }}</p>
                                <span class="mt-auto inline-flex items-center gap-2 pt-3 font-semibold text-brand-700">Xem chi tiết <x-glyph name="arrow-right" :size="18" /></span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    <section class="section section-ink" aria-labelledby="service-cta-title" id="dang-ky-tu-van">
        <div class="container-page grid items-start gap-10 lg:grid-cols-2 lg:gap-16">
            <div>
                <h2 id="service-cta-title" class="text-h2">Bắt đầu với {{ $service->name }}</h2>
                <p class="mt-4 max-w-xl text-lead text-ink-300">Để lại thông tin, đội ngũ Happy Ecotech sẽ liên hệ tư vấn và sắp xếp demo phù hợp.</p>
            </div>
            <div class="rounded-xl bg-white p-6 text-ink-900 md:p-8">
                <x-lead-form id="svc-form" :service-id="$service->id" />
            </div>
        </div>
    </section>
@endsection
