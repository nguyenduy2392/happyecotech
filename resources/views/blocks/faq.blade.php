@php
    // Dữ liệu: heading, subheading, items[{question, answer}]. Luồng A dùng cùng dữ liệu cho JSON-LD FAQPage.
    $items = collect($data['items'] ?? [])->filter(fn ($i) => filled($i['question'] ?? null) && filled($i['answer'] ?? null))->values();
@endphp
@if ($items->isNotEmpty())
    <section class="section section-tint" aria-labelledby="faq-title" id="hoi-dap">
        <div class="container-page grid gap-10 lg:grid-cols-[1fr_1.6fr] lg:gap-16">
            <div class="section-head !mb-0">
                <h2 id="faq-title">{{ $data['heading'] ?? 'Câu hỏi thường gặp' }}</h2>
                @if (! empty($data['subheading']))<p>{{ $data['subheading'] }}</p>@endif
            </div>
            <x-faq :items="$items" />
        </div>
    </section>
@endif
