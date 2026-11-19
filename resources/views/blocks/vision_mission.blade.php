@php
    $values = collect($data['values'] ?? [])->filter(fn ($v) => filled($v['title'] ?? null))->values();
@endphp
<section class="section" aria-labelledby="vision-title">
    <div class="container-page">
        <div class="section-head">
            <h2 id="vision-title">{{ $data['heading'] ?? 'Tầm nhìn và sứ mệnh' }}</h2>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            @if (! empty($data['vision']))
                <div class="card section-brand border-brand-200">
                    <h3>Tầm nhìn</h3>
                    <p class="mt-3 text-ink-700">{{ $data['vision'] }}</p>
                </div>
            @endif
            @if (! empty($data['mission']))
                <div class="card section-brand border-brand-200">
                    <h3>Sứ mệnh</h3>
                    <p class="mt-3 text-ink-700">{{ $data['mission'] }}</p>
                </div>
            @endif
        </div>

        @if ($values->isNotEmpty())
            <h3 class="mt-14 text-h2">Giá trị cốt lõi</h3>
            <ul class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($values as $i => $value)
                    <li class="card">
                        @php $icon = ['value-transparency', 'value-integrity', 'value-people', 'value-responsibility', 'value-sustainability'][$i] ?? null; @endphp
                        @if ($icon && is_file(public_path("assets/img/v2/{$icon}.svg")))
                            <x-art :name="$icon" width="128" height="128" class="size-14" sizes="56px" />
                        @else
                            <span class="flex size-11 items-center justify-center rounded-md bg-brand-100 font-semibold text-brand-700" aria-hidden="true">{{ $i + 1 }}</span>
                        @endif
                        <h4 class="mt-4 text-h3">{{ $value['title'] }}</h4>
                        @if (! empty($value['description']))
                            <p class="mt-2 text-ink-700">{{ $value['description'] }}</p>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</section>
