@extends('layouts.public')

@section('content')
    @include('partials.ui-ecosystem')
    @php
        $meta = require resource_path('views/public/services/meta.php');
        $byGroup = app('ui.ecosystem')->groupBy('group');
    @endphp
    @include('partials.page-hero', [
        'title' => 'Sản phẩm và giải pháp của Happy Ecotech',
        'lead' => 'Từ quản lý công việc và khách hàng đến truy xuất nguồn gốc và hành chính số cấp xã, phường: chọn giải pháp phù hợp với quy mô của bạn.',
        'crumbs' => [['label' => 'Sản phẩm']],
    ])

    @foreach (['ops', 'trace', 'egov'] as $gi => $gkey)
        @continue(! $byGroup->has($gkey))
        <section class="section {{ $gi % 2 === 1 ? 'section-tint' : '' }} {{ $gi > 0 ? 'pt-12 md:pt-16' : '' }}" aria-labelledby="group-{{ $gkey }}-title" id="nhom-{{ $gkey }}">
            <div class="container-page">
                <h2 id="group-{{ $gkey }}-title" class="mb-8 flex items-center gap-3 text-h2">
                    <x-glyph :name="$meta['_groups'][$gkey]['icon']" :size="28" class="text-brand-600" />{{ $meta['_groups'][$gkey]['title'] }}
                </h2>
                <ul class="grid gap-6 {{ $gkey === 'ops' ? 'lg:grid-cols-2' : 'md:grid-cols-2' }}">
                    @foreach ($byGroup[$gkey] as $it)
                        @include('partials.solution-card', ['it' => $it, 'large' => $gkey === 'ops'])
                    @endforeach
                </ul>
            </div>
        </section>
    @endforeach

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
