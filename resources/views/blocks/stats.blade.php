@php
    // Số liệu chưa xác minh (unverified) không được hiển thị công khai.
    $items = collect($data['items'] ?? [])->filter(fn ($i) => filled($i['value'] ?? null) && filled($i['label'] ?? null));
@endphp
@if (! ($data['unverified'] ?? false) && $items->isNotEmpty())
    <section class="section section-ink" aria-labelledby="stats-title">
        <div class="container-page">
            <div class="section-head">
                <h2 id="stats-title">{{ $data['heading'] ?? 'Con số nổi bật' }}</h2>
            </div>
            <dl class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($items as $item)
                    <div class="flex flex-col-reverse gap-1 border-t border-ink-700 pt-5">
                        <dt class="text-ink-300">{{ $item['label'] }}</dt>
                        <dd class="text-display text-white">{{ $item['value'] }}{{ $item['suffix'] ?? '' }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>
@endif
