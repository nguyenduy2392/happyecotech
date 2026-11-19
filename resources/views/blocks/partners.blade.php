@php
    // Chỉ hiển thị khi có logo thật (không bịa logo khách hàng).
    $logos = collect($data['logos'] ?? [])->map(function ($l) {
        $id = is_array($l) ? ($l['media_id'] ?? $l['logo_media_id'] ?? null) : null;
        return [
            'name' => is_array($l) ? ($l['name'] ?? '') : '',
            'url' => is_array($l) ? ($l['url'] ?? null) : null,
            'media' => filled($id) ? \App\Models\MediaAsset::find($id) : null,
        ];
    })->filter(fn ($l) => $l['media'])->values();
@endphp
@if ($logos->isNotEmpty())
    <section class="border-b border-ink-100 py-10 md:py-12" aria-labelledby="partners-title">
        <div class="container-page">
            <h2 id="partners-title" class="text-center text-lg text-ink-700">{{ $data['heading'] ?? 'Đối tác và khách hàng' }}</h2>
            <ul class="mt-8 flex flex-wrap items-center justify-center gap-x-12 gap-y-6">
                @foreach ($logos as $logo)
                    <li>
                        @if ($logo['url'])
                            <a href="{{ $logo['url'] }}" target="_blank" rel="noopener noreferrer" class="block">
                                <x-media-img :media="$logo['media']" :alt="$logo['name']" class="h-10 w-auto opacity-80 hover:opacity-100" sizes="160px" />
                            </a>
                        @else
                            <x-media-img :media="$logo['media']" :alt="$logo['name']" class="h-10 w-auto opacity-80" sizes="160px" />
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
@endif
