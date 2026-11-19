@php
    $tag = $headingTag ?? 'h2';
    $heading = $data['heading'] ?? setting('site_name', 'Happy Ecotech');
    $sub = $data['subheading'] ?? $data['description'] ?? null;
    $ctaText = $data['cta_text'] ?? 'Nhận tư vấn';
    $ctaUrl = $data['cta_url'] ?? (route('contact').'#contact-form');
    $cta2Text = $data['secondary_cta_text'] ?? 'Xem các giải pháp';
    $cta2Url = $data['secondary_cta_url'] ?? route('services.index');
@endphp
<section class="section-brand overflow-hidden border-b border-ink-100">
    <div class="container-page grid items-center gap-10 py-12 md:py-16 lg:grid-cols-[minmax(0,1.05fr)_minmax(0,0.95fr)] lg:gap-14 lg:py-20">
        <div>
            <{{ $tag }} class="text-display text-ink-900">{{ $heading }}</{{ $tag }}>
            @if ($sub)
                <p class="mt-5 max-w-xl text-lead text-ink-700">{{ $sub }}</p>
            @endif
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="{{ $ctaUrl }}" class="btn btn-primary btn-lg">{{ $ctaText }} <x-glyph name="arrow-right" /></a>
                <a href="{{ $cta2Url }}" class="btn btn-outline btn-lg">{{ $cta2Text }}</a>
            </div>
        </div>
        <x-art name="hero" width="1200" height="900" :priority="true" :alt="$data['image_alt'] ?? null"
            sizes="(min-width: 1024px) 560px, 100vw" class="w-full rounded-xl" />
    </div>
</section>
