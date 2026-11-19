@php
    $items = collect($data['items'] ?? $data['values'] ?? [])->filter(fn ($v) => filled($v['title'] ?? null))->values();
@endphp
@if ($items->isNotEmpty())
    <section class="section section-tint" aria-labelledby="values-title">
        <div class="container-page">
            <div class="section-head">
                <h2 id="values-title">{{ $data['heading'] ?? 'Giá trị cốt lõi' }}</h2>
                @if (! empty($data['subheading']))<p>{{ $data['subheading'] }}</p>@endif
            </div>
            <ul class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($items as $i => $value)
                    <li class="card">
                        @php $icon = ['value-transparency', 'value-integrity', 'value-people', 'value-responsibility', 'value-sustainability'][$i] ?? null; @endphp
                        @if ($icon && is_file(public_path("assets/img/v2/{$icon}.svg")))
                            <x-art :name="$icon" width="128" height="128" class="size-14" sizes="56px" />
                        @else
                            <span class="flex size-11 items-center justify-center rounded-md bg-brand-100 font-semibold text-brand-700" aria-hidden="true">{{ $i + 1 }}</span>
                        @endif
                        <h3 class="mt-4">{{ $value['title'] }}</h3>
                        @if (! empty($value['description']))
                            <p class="mt-2 text-ink-700">{{ $value['description'] }}</p>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
@endif
