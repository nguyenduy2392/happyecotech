@include('partials.ui-services')
@php
    $logo = setting('logo_media_id');
    $siteName = setting('site_name', 'Happy Ecotech');
    // Logo mặc định đã có bản WebP tối ưu; logo tuỳ chỉnh từ admin dùng nguyên media.
    $isDefaultLogo = ! $logo || ($logo->disk === 'assets' && $logo->path === 'img/logo/logo-full.png');
@endphp
<header data-site-header class="sticky top-0 z-40 border-b border-ink-100 bg-white/95 backdrop-blur">
    <div class="container-page flex h-16 items-center justify-between gap-4 md:h-[4.5rem]">
        <a href="{{ route('home') }}" class="flex shrink-0 items-center" aria-label="{{ $siteName }} - Trang chủ">
            @if ($isDefaultLogo)
                <img src="{{ asset('assets/img/logo/logo-full.webp') }}" alt="{{ $siteName }}" width="380" height="141"
                    class="h-10 w-auto md:h-11" fetchpriority="high" decoding="async" />
            @else
                <x-media-img :media="$logo" :alt="$siteName" :priority="true" class="h-10 w-auto md:h-11" sizes="160px" />
            @endif
        </a>

        <nav aria-label="Điều hướng chính" class="hidden items-center gap-1 lg:flex">
            @foreach ($headerMenu as $item)
                @php
                    $active = $item->link_type === 'route' && $item->route_name && request()->routeIs($item->route_name);
                    // Mục trỏ tới danh sách dịch vụ tự có menu con: TaskFlow → MiniCRM → giải pháp khác (không cần cấu hình tay).
                    $subs = $item->children->isNotEmpty()
                        ? $item->children->map(fn ($c) => ['label' => $c->label, 'url' => $c->resolveUrl(), 'blank' => $c->target_blank])
                        : (($item->link_type === 'route' && $item->route_name === 'services.index')
                            ? app('ui.services')->map(fn ($sv) => ['label' => $sv->name, 'url' => route('services.show', $sv), 'blank' => false])
                            : collect());
                @endphp
                <div class="group relative">
                    <a href="{{ $item->resolveUrl() }}" class="nav-link" @if ($active) aria-current="page" @endif
                        @if ($item->target_blank) target="_blank" rel="noopener noreferrer" @endif>{{ $item->label }}</a>
                    @if ($subs->isNotEmpty())
                        <ul class="invisible absolute left-0 top-full min-w-56 rounded-lg border border-ink-100 bg-white p-2 opacity-0 shadow-card group-focus-within:visible group-focus-within:opacity-100 group-hover:visible group-hover:opacity-100">
                            @foreach ($subs as $child)
                                <li>
                                    <a href="{{ $child['url'] }}" class="nav-link w-full"
                                        @if ($child['blank']) target="_blank" rel="noopener noreferrer" @endif>{{ $child['label'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            <a href="{{ route('contact') }}#contact-form" data-cta="header" class="btn btn-primary hidden sm:inline-flex">Nhận tư vấn</a>
            <button type="button" class="btn btn-outline px-3 lg:hidden" data-nav-toggle aria-expanded="false"
                aria-controls="mobile-nav" aria-label="Mở menu">
                <x-glyph name="menu" :size="22" />
            </button>
        </div>
    </div>

    <div id="mobile-nav" data-nav-panel class="border-t border-ink-100 bg-white lg:hidden">
        <nav aria-label="Điều hướng chính (di động)" class="container-page flex flex-col gap-1 py-4">
            @foreach ($headerMenu as $item)
                @php
                    $active = $item->link_type === 'route' && $item->route_name && request()->routeIs($item->route_name);
                    // Mục trỏ tới danh sách dịch vụ tự có menu con: TaskFlow → MiniCRM → giải pháp khác (không cần cấu hình tay).
                    $subs = $item->children->isNotEmpty()
                        ? $item->children->map(fn ($c) => ['label' => $c->label, 'url' => $c->resolveUrl(), 'blank' => $c->target_blank])
                        : (($item->link_type === 'route' && $item->route_name === 'services.index')
                            ? app('ui.services')->map(fn ($sv) => ['label' => $sv->name, 'url' => route('services.show', $sv), 'blank' => false])
                            : collect());
                @endphp
                <a href="{{ $item->resolveUrl() }}" class="nav-link text-lg" @if ($active) aria-current="page" @endif
                    @if ($item->target_blank) target="_blank" rel="noopener noreferrer" @endif>{{ $item->label }}</a>
                @foreach ($subs as $child)
                    <a href="{{ $child['url'] }}" class="nav-link pl-8"
                        @if ($child['blank']) target="_blank" rel="noopener noreferrer" @endif>{{ $child['label'] }}</a>
                @endforeach
            @endforeach
            <a href="{{ route('contact') }}#contact-form" data-cta="header-mobile" class="btn btn-primary btn-lg mt-3">Nhận tư vấn</a>
        </nav>
    </div>
</header>
