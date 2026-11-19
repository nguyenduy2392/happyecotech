@include('partials.ui-services')
@php
    // Dải lợi ích dưới hero: lấy từ benefits của TaskFlow (CMS) hoặc playbook; không có thì ẩn.
    $tf = app('ui.services')->firstWhere('slug', 'taskflow');
    $playbook = (require resource_path('views/public/services/playbook.php'))['taskflow'] ?? [];
    $items = collect($data['items'] ?? ($tf && is_array($tf->benefits ?? null) && $tf->benefits ? $tf->benefits : ($playbook['benefits'] ?? [])))
        ->filter(fn ($b) => filled($b['title'] ?? null))->take(3)->values();
@endphp
@if ($items->isNotEmpty())
    <section class="border-b border-ink-100 bg-white" aria-label="Lợi ích chính">
        <ul class="container-page grid divide-ink-100 py-6 md:grid-cols-3 md:divide-x md:py-8">
            @foreach ($items as $b)
                <li class="flex gap-3 py-3 md:px-6 md:first:pl-0 md:last:pr-0">
                    <x-glyph name="check" :size="22" class="mt-0.5 text-brand-600" />
                    <div>
                        <p class="font-semibold">{{ $b['title'] }}</p>
                        @if (! empty($b['text'] ?? $b['description'] ?? null))<p class="mt-1 text-sm text-ink-700">{{ $b['text'] ?? $b['description'] }}</p>@endif
                    </div>
                </li>
            @endforeach
        </ul>
    </section>
@endif
