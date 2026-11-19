@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Phân trang" class="mt-12">
        <ul class="flex flex-wrap items-center justify-center gap-2">
            @if ($paginator->onFirstPage())
                <li><span class="btn btn-outline opacity-50" aria-disabled="true">Trang trước</span></li>
            @else
                <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn btn-outline">Trang trước</a></li>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="px-2 text-ink-500" aria-hidden="true">{{ $element }}</span></li>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span class="btn btn-primary" aria-current="page">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="btn btn-outline" aria-label="Trang {{ $page }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn btn-outline">Trang sau</a></li>
            @else
                <li><span class="btn btn-outline opacity-50" aria-disabled="true">Trang sau</span></li>
            @endif
        </ul>
    </nav>
@endif
