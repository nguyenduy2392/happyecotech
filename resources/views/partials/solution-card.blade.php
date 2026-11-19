{{-- Thẻ giải pháp trên /dich-vu. Biến: $it (từ ui.ecosystem), $large (bool). --}}
@php
    $svc = $it['service'];
    $playbook = (require resource_path('views/public/services/playbook.php'))[$it['slug']] ?? [];
    $feat = $svc && $svc->features->isNotEmpty() ? $svc->features->pluck('title') : collect($playbook['features'] ?? [])->pluck('title');
    $consultUrl = route('contact', ['solution' => $it['slug']]).'#contact-form';
    $blurb = $svc?->short_description ?: ($it['summary'] ?? null);
@endphp
<li class="card !p-0 overflow-hidden" id="{{ $it['slug'] }}">
    @if ($svc?->cover)
        <x-media-img :media="$svc->cover" :alt="$svc->cover->alt ?: $svc->name" class="h-auto w-full" sizes="(min-width: 1024px) 560px, 100vw" />
    @else
        <x-art :name="'service-'.$it['slug']" width="800" height="600" :alt="$it['name']" class="w-full !rounded-none !border-0" sizes="(min-width: 1024px) 560px, 100vw" />
    @endif
    <div class="flex flex-1 flex-col p-6 {{ $large ? 'md:p-8' : '' }}">
        @if ($it['category'])<p class="text-sm font-semibold text-brand-700">{{ $it['category'] }}</p>@endif
        <h3 class="{{ $large ? 'text-h2' : 'text-h3' }} mt-1">{{ $it['name'] }}</h3>
        @if ($blurb)<p class="mt-3 text-ink-700">{{ $blurb }}</p>@endif
        @if ($large && $feat->isNotEmpty())
            <ul class="mt-4 grid gap-x-6 gap-y-1 sm:grid-cols-2">
                @foreach ($feat->take(6) as $t)
                    <li class="flex gap-2 text-sm"><x-glyph name="check" :size="18" class="mt-0.5 text-brand-600" />{{ $t }}</li>
                @endforeach
            </ul>
        @endif
        <div class="mt-auto flex flex-wrap gap-3 pt-6">
            @if ($it['url'])
                <a href="{{ $it['url'] }}" data-cta="list-{{ $it['slug'] }}" class="btn {{ $large ? 'btn-primary' : 'btn-outline' }}">{{ $it['cta'] }} <x-glyph name="arrow-right" :size="18" /></a>
                <a href="{{ $consultUrl }}" data-cta="list-{{ $it['slug'] }}-consult" class="btn btn-ghost">Nhận tư vấn</a>
            @else
                <a href="{{ $consultUrl }}" data-cta="list-{{ $it['slug'] }}-consult" class="btn btn-primary">{{ $it['cta'] }} <x-glyph name="arrow-right" :size="18" /></a>
            @endif
        </div>
    </div>
</li>
