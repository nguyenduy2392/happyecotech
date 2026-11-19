@php
    $values = collect($data['values'] ?? [])->filter(fn ($v) => filled($v['title'] ?? null))->values();
@endphp
<section class="section" aria-labelledby="vision-title">
    <div class="container-page">
        <div class="section-head">
            <h2 id="vision-title">{{ $data['heading'] ?? 'Tầm nhìn và sứ mệnh' }}</h2>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            @foreach ([['Tầm nhìn', $data['vision'] ?? null], ['Sứ mệnh', $data['mission'] ?? null]] as [$label, $text])
                @if ($text)
                    <div class="rounded-2xl bg-brand-50 p-8 md:p-10">
                        <h3 class="text-brand-700">{{ $label }}</h3>
                        <p class="mt-4 text-h3 font-semibold leading-snug text-ink-900 md:text-2xl">{{ $text }}</p>
                    </div>
                @endif
            @endforeach
        </div>

        @if ($values->isNotEmpty())
            <h3 class="mt-16 text-h2">Giá trị cốt lõi</h3>
            <ul class="mt-8 grid gap-x-8 gap-y-10 sm:grid-cols-2 lg:grid-cols-5">
                @foreach ($values as $i => $value)
                    <li class="card !p-5">
                        @php $icon = ['value-transparency', 'value-integrity', 'value-people', 'value-responsibility', 'value-sustainability'][$i] ?? null; @endphp
                        @if ($icon && is_file(public_path("assets/img/v2/{$icon}.svg")))
                            <x-art :name="$icon" width="128" height="128" class="size-12" sizes="48px" />
                        @endif
                        <h4 class="mt-3 text-h3">{{ $value['title'] }}</h4>
                        @if (! empty($value['description']))
                            <p class="mt-2 text-sm leading-relaxed text-ink-700">{{ $value['description'] }}</p>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</section>
