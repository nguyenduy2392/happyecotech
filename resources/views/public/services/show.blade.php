@extends('layouts.public')

@section('content')
    @include('partials.page-hero', [
        'title' => $service->name,
        'subtitle' => $service->short_description,
        'breadcrumbs' => [
            ['label' => 'Dịch vụ', 'url' => route('services.index')],
            ['label' => $service->name],
        ],
    ])

    <section class="service-detail-section pt-80 pb-80">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    @if ($service->cover?->url)
                        <img class="service-cover mb-40" src="{{ $service->cover->url }}" alt="{{ $service->name }}">
                    @endif

                    <div class="richtext-content">{!! $service->content !!}</div>

                    @if ($service->features->isNotEmpty())
                        <h3 class="mt-40 mb-20">Tính năng &amp; lợi ích</h3>
                        <ul class="feature-list">
                            @foreach ($service->features as $feature)
                                <li>
                                    <i class="{{ $feature->icon ?: 'lni lni-checkmark-circle' }}"></i>
                                    <div>
                                        <strong>{{ $feature->title }}</strong>
                                        @if ($feature->description)
                                            <p>{{ $feature->description }}</p>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if ($service->gallery->isNotEmpty())
                        <div class="row g-3 mt-30">
                            @foreach ($service->gallery as $image)
                                <div class="col-6 col-md-4">
                                    <img src="{{ $image->url }}" class="img-fluid rounded" alt="{{ $image->alt }}" loading="lazy">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="col-lg-4">
                    <div class="service-sidebar">
                        @if ($service->icon?->url)
                            <img class="service-icon mb-20" src="{{ $service->icon->url }}" alt="{{ $service->name }}">
                        @endif

                        @if ($service->external_url || $service->app_store_url || $service->google_play_url)
                            <h4>Liên kết</h4>
                            @if ($service->external_url)
                                <a class="sidebar-link" href="{{ $service->external_url }}" target="_blank" rel="noopener noreferrer">
                                    <i class="lni lni-world"></i> Website sản phẩm</a>
                            @endif
                            @if ($service->app_store_url)
                                <a class="sidebar-link" href="{{ $service->app_store_url }}" target="_blank" rel="noopener noreferrer">
                                    <i class="lni lni-apple"></i> App Store</a>
                            @endif
                            @if ($service->google_play_url)
                                <a class="sidebar-link" href="{{ $service->google_play_url }}" target="_blank" rel="noopener noreferrer">
                                    <i class="lni lni-play-store"></i> Google Play</a>
                            @endif
                        @endif

                        <a href="{{ $service->cta_url ?: route('contact').'#contact-form' }}"
                            class="main-btn btn-hover w-100 mt-20 text-center">{{ $service->cta_text ?: 'Liên hệ tư vấn' }}</a>
                    </div>

                    @if ($related->isNotEmpty())
                        <div class="service-sidebar mt-30">
                            <h4>Dịch vụ khác</h4>
                            <ul class="related-list">
                                @foreach ($related as $item)
                                    <li><a href="{{ route('services.show', $item) }}">{{ $item->name }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
