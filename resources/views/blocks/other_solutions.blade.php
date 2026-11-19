@include('partials.ui-ecosystem')
@php
    // Các giải pháp còn lại (ngoài TaskFlow/MiniCRM), mỗi giải pháp 1 hàng lớn xen kẽ trái/phải.
    $meta = require resource_path('views/public/services/meta.php');
    $rows = app('ui.ecosystem')->reject(fn ($it) => in_array($it['slug'], ['taskflow', 'minicrm'], true))->values();
    $groupTitles = collect($meta['_groups']);
    $heading = $data['other_heading'] ?? 'Truy xuất nguồn gốc và số hoá cho chuỗi cung ứng và chính quyền cơ sở';
@endphp
<section class="section section-tint" aria-labelledby="other-title" id="giai-phap">
    <div class="container-page">
        <div class="section-head">
            <h2 id="other-title">{{ $heading }}</h2>
        </div>
        <div class="space-y-6 md:space-y-8">
            @foreach ($rows as $i => $it)
                @php
                    $svc = $it['service'];
                    $m = $meta[$it['slug']] ?? [];
                    $points = collect($svc && is_array($svc->getAttribute('highlights') ?? null) ? $svc->getAttribute('highlights') : ($svc && is_array($svc->benefits ?? null) && $svc->benefits ? collect($svc->benefits)->pluck('title')->all() : ($m['points'] ?? [])))->take(3);
                    $blurb = $svc ? $svc->short_description : ($m['blurb'] ?? $it['summary']);
                    $consultUrl = route('contact', ['solution' => $it['slug']]).'#contact-form';
                    $flip = $i % 2 === 1;
                @endphp
                <article id="giai-phap-{{ $it['slug'] }}" class="grid items-center gap-6 overflow-hidden rounded-xl border border-ink-100 bg-white md:grid-cols-2 md:gap-0">
                    <div class="{{ $flip ? 'md:order-2' : '' }}">
                        @if ($svc?->cover)
                            <x-media-img :media="$svc->cover" :alt="$svc->cover->alt ?: $svc->name" class="h-auto w-full" sizes="(min-width: 768px) 560px, 100vw" />
                        @else
                            <x-art :name="'service-'.$it['slug']" width="800" height="600" :alt="$it['name']" class="w-full !rounded-none !border-0" sizes="(min-width: 768px) 560px, 100vw" />
                        @endif
                    </div>
                    <div class="p-6 md:p-10 {{ $flip ? 'md:order-1' : '' }}">
                        @if ($it['category'])<p class="text-sm font-semibold text-brand-700">{{ $it['category'] }}</p>@endif
                        <h3 class="mt-1 text-h2">{{ $it['headline'] }}</h3>
                        @if ($blurb)<p class="mt-3 text-ink-700">{{ $blurb }}</p>@endif
                        @if ($points->isNotEmpty())
                            <ul class="mt-4 space-y-2">
                                @foreach ($points as $pt)
                                    <li class="flex gap-2 text-sm"><x-glyph name="check" :size="18" class="mt-0.5 text-brand-600" />{{ $pt }}</li>
                                @endforeach
                            </ul>
                        @endif
                        <div class="mt-6 flex flex-wrap gap-3">
                            @if ($it['url'])
                                <a href="{{ $it['url'] }}" data-cta="other-{{ $it['slug'] }}" class="btn btn-primary">{{ $it['cta'] }} <x-glyph name="arrow-right" :size="18" /></a>
                                <a href="{{ $consultUrl }}" data-cta="other-{{ $it['slug'] }}-consult" class="btn btn-ghost">Nhận tư vấn</a>
                            @else
                                <a href="{{ $consultUrl }}" data-cta="other-{{ $it['slug'] }}-consult" class="btn btn-primary">{{ $it['cta'] }} <x-glyph name="arrow-right" :size="18" /></a>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
