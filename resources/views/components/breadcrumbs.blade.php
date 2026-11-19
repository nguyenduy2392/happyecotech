@props(['items' => []])
{{-- $items: [['label' => 'Dịch vụ', 'url' => '/dich-vu'], ['label' => 'SmartLabel']] (mục cuối không có url) --}}
<nav aria-label="Đường dẫn" class="text-sm text-ink-500">
    <ol class="flex flex-wrap items-center gap-x-2 gap-y-1">
        <li><a href="{{ route('home') }}" class="inline-flex min-h-8 items-center underline-offset-2 hover:text-brand-700 hover:underline">Trang chủ</a></li>
        @foreach ($items as $item)
            <li class="flex items-center gap-2">
                <span aria-hidden="true">/</span>
                @if (! empty($item['url']))
                    <a href="{{ $item['url'] }}" class="inline-flex min-h-8 items-center underline-offset-2 hover:text-brand-700 hover:underline">{{ $item['label'] }}</a>
                @else
                    <span aria-current="page" class="text-ink-900">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
