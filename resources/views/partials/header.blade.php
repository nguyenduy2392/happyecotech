@include('partials.ui-services')
@include('partials.ui-channels')
@php
    $logo = setting('logo_media_id');
    $siteName = setting('site_name', 'Happy Ecotech');
    // Logo mặc định đã có bản WebP tối ưu; logo tuỳ chỉnh từ admin dùng nguyên media.
    $isDefaultLogo = ! $logo || ($logo->disk === 'assets' && $logo->path === 'img/logo/logo-full.png');
    $ch = app('ui.channels');
    $nav = $headerMenu->map(function ($item) {
        // Mục trỏ tới danh sách dịch vụ tự có menu con theo thứ tự sản phẩm.
        $subs = $item->children->isNotEmpty()
            ? $item->children->map(fn ($c) => ['label' => $c->label, 'url' => $c->resolveUrl(), 'blank' => $c->target_blank])
            : (($item->link_type === 'route' && $item->route_name === 'services.index')
                ? app('ui.services')->map(fn ($sv) => ['label' => $sv->name, 'url' => route('services.show', $sv), 'blank' => false])
                : collect());

        return [
            'item' => $item,
            'subs' => $subs,
            'active' => $item->link_type === 'route' && $item->route_name && request()->routeIs($item->route_name),
        ];
    });
@endphp
<header data-site-header class="sticky top-3 z-40 px-3 sm:px-4">
    <div class="nav-bar">
        <div class="flex h-14 items-center justify-between gap-4 px-4 md:h-16 md:px-6">
            <a href="{{ route('home') }}" class="flex shrink-0 items-center" aria-label="{{ $siteName }} - Trang chủ">
                @if ($isDefaultLogo)
                    <img src="{{ asset('assets/img/logo/logo-full.webp') }}" alt="{{ $siteName }}" width="380" height="141"
                        class="h-9 w-auto md:h-10" decoding="async" />
                @else
                    <x-media-img :media="$logo" :alt="$siteName" class="h-9 w-auto md:h-10" sizes="160px" />
                @endif
            </a>

            <nav aria-label="Điều hướng chính" class="hidden items-center gap-1 lg:flex">
                @foreach ($nav as $n)
                    @php $item = $n['item']; @endphp
                    <div class="group relative">
                        <a href="{{ $item->resolveUrl() }}" class="nav-link" @if ($n['active']) aria-current="page" @endif
                            @if ($item->target_blank) target="_blank" rel="noopener noreferrer" @endif>{{ $item->label }}</a>
                        @if ($n['subs']->isNotEmpty())
                            <ul class="invisible absolute left-0 top-full min-w-56 translate-y-2 rounded-2xl border border-ink-100 bg-white p-2 opacity-0 shadow-card transition duration-300 ease-[cubic-bezier(0.22,1,0.36,1)] group-focus-within:visible group-focus-within:translate-y-0 group-focus-within:opacity-100 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100">
                                @foreach ($n['subs'] as $child)
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
                @foreach ([['Zalo Happy Ecotech', $ch['zalo'], 'message'], ['Facebook Happy Ecotech', $ch['facebook'], 'facebook']] as [$label, $url, $icon])
                    @if ($url)
                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $label }}"
                            class="hidden size-11 items-center justify-center rounded-lg text-ink-700 hover:bg-brand-50 hover:text-brand-700 xl:flex"><x-glyph :name="$icon" :size="22" /></a>
                    @endif
                @endforeach
                <a href="{{ route('contact') }}#contact-form" data-cta="header" class="btn btn-primary hidden sm:inline-flex">Nhận tư vấn</a>
                <button type="button" class="btn btn-outline px-3 lg:hidden" data-nav-toggle aria-expanded="false"
                    aria-controls="mobile-nav" aria-label="Mở menu">
                    <span class="burger" aria-hidden="true"><i></i><i></i></span>
                </button>
            </div>
        </div>
    </div>

    <div id="mobile-nav" data-nav-panel class="nav-panel">
        <nav aria-label="Điều hướng chính (di động)" class="flex flex-col gap-1">
            @foreach ($nav as $n)
                @php $item = $n['item']; @endphp
                <a href="{{ $item->resolveUrl() }}" class="nav-link text-lg" @if ($n['active']) aria-current="page" @endif
                    @if ($item->target_blank) target="_blank" rel="noopener noreferrer" @endif>{{ $item->label }}</a>
                @foreach ($n['subs'] as $child)
                    <a href="{{ $child['url'] }}" class="nav-link pl-8"
                        @if ($child['blank']) target="_blank" rel="noopener noreferrer" @endif>{{ $child['label'] }}</a>
                @endforeach
            @endforeach
            <a href="{{ route('contact') }}#contact-form" data-cta="header-mobile" class="btn btn-primary btn-lg nav-cta mt-3">Nhận tư vấn</a>
        </nav>
    </div>
</header>
