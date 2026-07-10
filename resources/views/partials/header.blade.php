@php
    $logo = setting('logo_media_id');
    $logoUrl = $logo?->url ?? asset('assets/img/logo/logo-full.png');
    $siteName = setting('site_name', 'Happy Ecotech');
@endphp
<header class="header">
    <div class="navbar-area">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <nav class="navbar navbar-expand-lg">
                        <a class="navbar-brand" href="{{ route('home') }}">
                            <img src="{{ $logoUrl }}" alt="{{ $siteName }}" />
                        </a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Mở/đóng menu">
                            <span class="toggler-icon"></span>
                            <span class="toggler-icon"></span>
                            <span class="toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse sub-menu-bar" id="navbarSupportedContent">
                            <ul id="nav" class="navbar-nav ms-auto">
                                @forelse ($headerMenu as $item)
                                    @php
                                        $active = $item->link_type === 'route' && $item->route_name
                                            && request()->routeIs($item->route_name);
                                    @endphp
                                    <li class="nav-item">
                                        <a href="{{ $item->resolveUrl() }}"
                                            class="{{ $active ? 'active' : '' }}"
                                            @if ($active) aria-current="page" @endif
                                            @if ($item->target_blank) target="_blank" rel="noopener noreferrer" @endif>
                                            {{ $item->label }}
                                        </a>
                                        @if ($item->children->isNotEmpty())
                                            <ul class="sub-menu">
                                                @foreach ($item->children as $child)
                                                    <li class="nav-item">
                                                        <a href="{{ $child->resolveUrl() }}"
                                                            @if ($child->target_blank) target="_blank" rel="noopener noreferrer" @endif>
                                                            {{ $child->label }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                                @empty
                                    <li class="nav-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                                @endforelse
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>
