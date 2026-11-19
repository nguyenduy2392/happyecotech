@php
    $text = $data['html'] ?? $data['content'] ?? $data['description'] ?? null;
@endphp
@if ($text || ! empty($data['heading']))
    <section class="section" aria-labelledby="about-title">
        <div class="container-page">
            <h2 id="about-title" class="text-h2">{{ $data['heading'] ?? 'Giới thiệu' }}</h2>
            @if ($text)
                <div class="prose-he mt-6">{!! \Mews\Purifier\Facades\Purifier::clean($text) !!}</div>
            @endif
        </div>
    </section>
@endif
