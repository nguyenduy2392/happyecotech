@php
    $items = collect($data['items'] ?? [])->filter(fn ($i) => filled($i['title'] ?? null));
@endphp
<section class="section section-tint" aria-labelledby="why-title">
    <div class="container-page grid gap-8 lg:grid-cols-[1fr_1.2fr] lg:gap-16">
        <div class="section-head !mb-0">
            <h2 id="why-title">{{ $data['heading'] ?? 'Vì sao chọn Happy Ecotech' }}</h2>
        </div>
        <div>
            @if (! empty($data['description']))
                <p class="text-lead text-ink-700">{{ $data['description'] }}</p>
            @endif
            @if ($items->isNotEmpty())
                <ul class="mt-6 grid gap-4 sm:grid-cols-2">
                    @foreach ($items as $item)
                        <li class="flex gap-3">
                            <x-glyph name="check" class="mt-1 text-brand-600" />
                            <span><strong class="font-semibold">{{ $item['title'] }}</strong>
                                @if (! empty($item['description']))<span class="block text-ink-700">{{ $item['description'] }}</span>@endif
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</section>
