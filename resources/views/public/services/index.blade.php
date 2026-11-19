@extends('layouts.public')

@section('content')
    @include('partials.page-hero', [
        'title' => 'Giải pháp của Happy Ecotech',
        'lead' => 'Tem điện tử và truy xuất nguồn gốc, quản trị vận hành, hành chính số: chọn giải pháp phù hợp với quy mô của bạn.',
        'crumbs' => [['label' => 'Giải pháp']],
    ])

    <section class="section">
        <div class="container-page">
            @if ($services->isEmpty())
                <p class="text-lead text-ink-700">Danh sách giải pháp đang được cập nhật.</p>
            @else
                <ul class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($services as $service)
                        <li>
                            <a href="{{ route('services.show', $service) }}" class="card h-full gap-4">
                                <x-art :name="'icon-'.$service->slug" width="56" height="56" :alt="''" class="size-14 !border-0 !bg-transparent !p-0 [&>span]:hidden" sizes="56px" />
                                <h2 class="text-h3">{{ $service->name }}</h2>
                                <p class="text-ink-700">{{ $service->short_description }}</p>
                                <span class="mt-auto inline-flex items-center gap-2 pt-2 font-semibold text-brand-700">
                                    Xem chi tiết <x-glyph name="arrow-right" :size="18" />
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>

    <section class="section section-ink">
        <div class="container-page flex flex-col items-start justify-between gap-6 md:flex-row md:items-center">
            <div>
                <h2 class="text-h2">Chưa rõ giải pháp nào phù hợp?</h2>
                <p class="mt-3 max-w-xl text-ink-300">Mô tả ngắn nhu cầu, chúng tôi sẽ đề xuất lộ trình triển khai gọn nhất cho bạn.</p>
            </div>
            <a href="{{ route('contact') }}#contact-form" class="btn btn-light btn-lg">Nhận tư vấn</a>
        </div>
    </section>
@endsection
