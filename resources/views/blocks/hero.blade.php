@php
    $safe = fn ($u) => function_exists('safe_url') ? safe_url($u) : null; // R2-01: mọi URL từ CMS đi qua safe_url()
    $tag = $headingTag ?? 'h2';
    $heading = $data['h1'] ?? $data['heading'] ?? setting('site_name', 'Happy Ecotech');
    $sub = $data['subheading'] ?? $data['description'] ?? null;
    $ctaText = $data['cta_text'] ?? 'Nhận tư vấn';
    $ctaUrl = $safe($data['cta_url'] ?? null) ?? (route('contact').'#contact-form');
    // Định vị TaskFlow-first: nút phụ mặc định dẫn tới trang TaskFlow (nếu đã xuất bản), không hứa hẹn dùng thử/giá.
    $tf = \App\Models\Service::query()->published()->where('slug', 'taskflow')->first();
    $cta2Text = $data['secondary_cta_text'] ?? ($tf ? 'Xem sản phẩm TaskFlow' : 'Xem các giải pháp');
    $cta2Url = $safe($data['secondary_cta_url'] ?? null) ?? ($tf ? route('services.show', $tf) : route('services.index'));
    $fallbackArt = is_file(public_path('assets/img/v2/hero-taskflow-lg.webp')) || is_file(public_path('assets/img/v2/hero-taskflow.webp')) ? 'hero-taskflow' : 'service-taskflow';
    $image = filled($data['image_media_id'] ?? null) ? \App\Models\MediaAsset::find($data['image_media_id']) : null;
@endphp
<section class="section-brand overflow-hidden border-b border-ink-100">
    <div class="container-page grid items-center gap-10 py-12 md:py-16 lg:grid-cols-[minmax(0,1.05fr)_minmax(0,0.95fr)] lg:gap-14 lg:py-20">
        <div>
            <{{ $tag }} class="text-display text-ink-900">{{ $heading }}</{{ $tag }}>
            @if ($sub)
                <p class="mt-5 max-w-xl text-lead text-ink-700">{{ $sub }}</p>
            @endif
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="{{ $ctaUrl }}" data-cta="hero" class="btn btn-primary btn-lg">{{ $ctaText }} <x-glyph name="arrow-right" /></a>
                <a href="{{ $cta2Url }}" data-cta="hero-secondary" class="btn btn-outline btn-lg">{{ $cta2Text }}</a>
            </div>
        </div>
        @if ($image)
            <x-media-img :media="$image" :alt="$data['image_alt'] ?? null" :priority="true"
                sizes="(min-width: 1024px) 560px, 100vw" class="h-auto w-full rounded-xl" />
        @else
            @if ($fallbackArt === 'hero-taskflow')
                <x-art name="hero-taskflow" width="1600" height="1000" :priority="true" :alt="$data['image_alt'] ?? null"
                    sizes="(min-width: 1024px) 560px, 100vw" class="w-full rounded-xl" />
            @else
                <x-art name="service-taskflow" width="800" height="600" :priority="true" :alt="$data['image_alt'] ?? 'Ứng dụng TaskFlow'"
                    sizes="(min-width: 1024px) 560px, 100vw" class="w-full rounded-xl" />
            @endif
        @endif
    </div>
</section>
