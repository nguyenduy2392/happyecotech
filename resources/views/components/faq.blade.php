@props(['items' => []])
{{-- $items: [['question' => '...', 'answer' => '...']]. Dùng <details> gốc: không cần JS, nội dung luôn nằm trong DOM. --}}
@php
    $items = collect($items)->filter(fn ($i) => filled($i['question'] ?? null) && filled($i['answer'] ?? null))->values();
@endphp
@if ($items->isNotEmpty())
    <div class="faq">
        @foreach ($items as $item)
            <details @if ($loop->first && ($open ?? false)) open @endif>
                <summary>
                    <h3 class="text-lg font-semibold">{{ $item['question'] }}</h3>
                    <x-glyph name="chevron-down" class="chev" />
                </summary>
                <div class="faq-a"><p>{{ $item['answer'] }}</p></div>
            </details>
        @endforeach
    </div>
@endif
