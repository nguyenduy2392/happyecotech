{{-- Khối sản phẩm trên trang chủ. Biến: $service, $level = 'flagship' (TaskFlow) | 'second' (MiniCRM), $heading? --}}
@php
    $safe = fn ($u) => function_exists('safe_url') ? safe_url($u) : null;
    $flagship = ($level ?? 'flagship') === 'flagship';
    $playbook = (require resource_path('views/public/services/playbook.php'))[$service->slug] ?? [];
    $features = $service->features->isNotEmpty()
        ? $service->features->map(fn ($f) => ['icon' => $f->icon, 'title' => $f->title, 'text' => $f->description])
        : collect($playbook['features'] ?? []);
    $features = $features->take($flagship ? 6 : 4)->values();
    $steps = collect(is_array($service->steps ?? null) ? $service->steps : ($playbook['steps'] ?? []));
    $knownIcons = ['check-square', 'folder', 'map-pin', 'clock', 'message', 'clipboard', 'users', 'filter', 'file-text', 'bar-chart', 'lock', 'calendar', 'camera', 'bell', 'layers', 'target', 'workflow'];
    $cycle = $flagship ? ['check-square', 'folder', 'map-pin', 'clock', 'message', 'clipboard'] : ['users', 'filter', 'message', 'file-text'];
    $appStore = $safe($service->app_store_url);
    $playStore = $safe($service->google_play_url);
    $metaAll = require resource_path('views/public/services/meta.php');
    $m = $metaAll[$service->slug] ?? [];
    $eyebrow = $service->getAttribute('category_label') ?: ($m['category'] ?? null);
    $h = $heading ?? ($service->getAttribute('tagline') ?: ($m['headline'] ?? $service->name));
    $titleId = 'product-'.$service->slug.'-title';

@endphp
<section class="section {{ $flagship ? 'section-brand' : '' }}" aria-labelledby="{{ $titleId }}" id="san-pham-{{ $service->slug }}">
    <div class="container-page">
        <div class="grid items-start gap-10 lg:gap-16 {{ $flagship ? 'lg:grid-cols-[minmax(0,1.1fr)_minmax(0,0.9fr)]' : 'lg:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)]' }}">
            <div class="{{ $flagship ? '' : 'lg:order-2' }}">
                @if ($eyebrow)<p class="mb-2 font-semibold text-brand-700">{{ $eyebrow }}</p>@endif
                <h2 id="{{ $titleId }}" class="{{ $flagship ? 'text-h2' : 'text-h3 md:text-2xl' }}">{{ $h }}</h2>
                @if ($service->short_description)
                    <p class="mt-4 max-w-xl {{ $flagship ? 'text-lead' : '' }} text-ink-700">{{ $service->short_description }}</p>
                @endif

                @if ($features->isNotEmpty())
                    <ul class="mt-8 grid gap-x-8 gap-y-5 {{ $flagship ? 'sm:grid-cols-2' : 'sm:grid-cols-2' }}">
                        @foreach ($features as $i => $f)
                            @php $icon = in_array($f['icon'] ?? null, $knownIcons, true) ? $f['icon'] : $cycle[$i % count($cycle)]; @endphp
                            <li class="flex gap-3">
                                <x-glyph :name="$icon" :size="22" class="mt-0.5 text-brand-600" />
                                <div>
                                    <h3 class="text-base">{{ $f['title'] ?? '' }}</h3>
                                    @if (! empty($f['text']))<p class="mt-1 text-sm text-ink-700">{{ $f['text'] }}</p>@endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                    <a href="{{ route('services.show', $service) }}" data-cta="product-{{ $service->slug }}" class="btn {{ $flagship ? 'btn-primary btn-lg' : 'btn-outline' }}">Xem chi tiết {{ $service->name }} <x-glyph name="arrow-right" :size="18" /></a>
                    <a href="{{ route('contact') }}#contact-form" data-cta="product-{{ $service->slug }}-consult" class="btn {{ $flagship ? 'btn-outline btn-lg' : 'btn-ghost' }}">Nhận tư vấn</a>
                </div>
                @if ($flagship && ($appStore || $playStore))
                    <p class="mt-5 flex flex-wrap items-center gap-x-5 gap-y-1 text-sm text-ink-700">
                        <span>Ứng dụng {{ $service->name }} trên điện thoại:</span>
                        @if ($appStore)<a href="{{ $appStore }}" class="font-semibold text-brand-700 underline underline-offset-2" target="_blank" rel="noopener noreferrer">App Store</a>@endif
                        @if ($playStore)<a href="{{ $playStore }}" class="font-semibold text-brand-700 underline underline-offset-2" target="_blank" rel="noopener noreferrer">Google Play</a>@endif
                    </p>
                @endif
            </div>

            <div class="{{ $flagship ? '' : 'lg:order-1' }}">
                @if ($service->cover)
                    <x-media-img :media="$service->cover" :alt="$service->cover->alt ?: $service->name" class="h-auto w-full rounded-2xl" sizes="(min-width: 1024px) 520px, (min-width: 640px) 80vw, 260px" />
                @else
                    <x-art :name="'service-'.$service->slug" width="800" height="600" :alt="$service->name" class="w-full rounded-2xl" sizes="(min-width: 1024px) 520px, (min-width: 640px) 80vw, 260px" />
                @endif
                <x-service-shots :service="$service" :limit="$flagship ? 4 : 3" class="mt-4" />
            </div>
        </div>

        @if ($flagship && $steps->isNotEmpty())
            <h3 class="mt-14 text-h3">{{ $service->name }} bắt đầu như thế nào</h3>
            <ol class="steps mt-8" style="--steps: {{ min($steps->count(), 4) }}">
                @foreach ($steps as $i => $step)
                    <li class="step">
                        <span class="step-num" aria-hidden="true">{{ $i + 1 }}</span>
                        <h4 class="text-h3">{{ $step['title'] ?? '' }}</h4>
                        <p>{{ $step['text'] ?? $step['description'] ?? '' }}</p>
                        @unless ($loop->last)<x-glyph name="arrow-right" :size="20" class="step-arrow" />@endunless
                    </li>
                @endforeach
            </ol>
        @endif
    </div>
</section>
