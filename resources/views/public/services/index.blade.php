@extends('layouts.public')

@section('content')
    @include('partials.ui-services')
    @php
        $all = app('ui.services');
        $flagship = $all->filter(fn ($s) => in_array($s->slug, ['taskflow', 'minicrm'], true))->values();
        $others = $all->reject(fn ($s) => in_array($s->slug, ['taskflow', 'minicrm'], true))->values();
        $playbook = require resource_path('views/public/services/playbook.php');
    @endphp
    @include('partials.page-hero', [
        'title' => 'Sản phẩm và giải pháp của Happy Ecotech',
        'lead' => 'TaskFlow quản lý công việc và vận hành đội nhóm, MiniCRM quản lý khách hàng. Ngoài ra là các giải pháp truy xuất nguồn gốc và hành chính số.',
        'crumbs' => [['label' => 'Sản phẩm']],
    ])

    <section class="section" aria-labelledby="flagship-title">
        <div class="container-page">
            <h2 id="flagship-title" class="sr-only">Sản phẩm chính</h2>
            @if ($all->isEmpty())
                <p class="text-lead text-ink-700">Danh sách sản phẩm đang được cập nhật.</p>
            @endif
            <ul class="grid gap-6 lg:grid-cols-2">
                @foreach ($flagship as $service)
                    @php
                        $feat = $service->features->isNotEmpty() ? $service->features->pluck('title') : collect($playbook[$service->slug]['features'] ?? [])->pluck('title');
                        $isMain = $service->slug === 'taskflow';
                    @endphp
                    <li class="card !p-0 overflow-hidden">
                        @if ($service->cover)
                            <x-media-img :media="$service->cover" :alt="$service->cover->alt ?: $service->name" class="h-auto w-full" sizes="(min-width: 1024px) 560px, 100vw" />
                        @else
                            <x-art :name="'service-'.$service->slug" width="800" height="600" :alt="$service->name" class="w-full !rounded-none !border-0" sizes="(min-width: 1024px) 560px, 100vw" />
                        @endif
                        <div class="flex flex-1 flex-col p-6 md:p-8">
                            <h3 class="text-h2">{{ $service->name }}</h3>
                            @if ($isMain)<p class="mt-1 font-semibold text-brand-700">Sản phẩm chủ lực</p>@else<p class="mt-1 font-semibold text-brand-700">Sản phẩm thứ hai</p>@endif
                            <p class="mt-3 text-ink-700">{{ $service->short_description }}</p>
                            @if ($feat->isNotEmpty())
                                <ul class="mt-4 grid gap-x-6 gap-y-1 sm:grid-cols-2">
                                    @foreach ($feat->take(6) as $t)
                                        <li class="flex gap-2 text-sm"><x-glyph name="check" :size="18" class="mt-0.5 text-brand-600" />{{ $t }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            <div class="mt-auto flex flex-wrap gap-3 pt-6">
                                <a href="{{ route('services.show', $service) }}" data-cta="list-{{ $service->slug }}" class="btn btn-primary">Xem chi tiết {{ $service->name }} <x-glyph name="arrow-right" :size="18" /></a>
                                <a href="{{ route('contact') }}#contact-form" data-cta="list-{{ $service->slug }}-consult" class="btn btn-outline">Nhận tư vấn</a>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    @if ($others->isNotEmpty())
        <section class="section section-tint pt-12" aria-labelledby="others-title">
            <div class="container-page">
                <h2 id="others-title" class="mb-8 text-h2">Giải pháp khác</h2>
                <ul class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($others as $service)
                        <li>
                            <a href="{{ route('services.show', $service) }}" class="card h-full gap-3">
                                <span class="flex items-center gap-3">
                                    <x-art :name="'icon-'.$service->slug" width="40" height="40" :alt="''" class="size-10 !border-0 !bg-transparent !p-0 [&>span]:hidden" sizes="40px" />
                                    <h3>{{ $service->name }}</h3>
                                </span>
                                <p class="line-clamp-3 text-sm text-ink-700">{{ $service->short_description }}</p>
                                <span class="mt-auto inline-flex items-center gap-2 pt-1 font-semibold text-brand-700">Xem chi tiết <x-glyph name="arrow-right" :size="18" /></span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    <section class="section section-ink">
        <div class="container-page flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">
            <div>
                <h2 class="text-h2">Chưa rõ sản phẩm nào phù hợp?</h2>
                <p class="mt-3 max-w-xl text-ink-300">Mô tả ngắn nhu cầu, chúng tôi sẽ đề xuất lộ trình triển khai gọn nhất cho bạn.</p>
            </div>
            <a href="{{ route('contact') }}#contact-form" data-cta="services-index" class="btn btn-light btn-lg">Nhận tư vấn</a>
        </div>
    </section>
@endsection
