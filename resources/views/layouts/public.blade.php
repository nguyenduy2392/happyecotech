@php
    $seo = $seo ?? he_seo();
    $favicon = setting('favicon_media_id');
    $faviconUrl = $favicon?->url ?? asset('assets/img/favicon.svg');
    $zalo = setting('zalo_site_verification');
@endphp
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    @include('partials.seo', ['seo' => $seo])

    <link rel="shortcut icon" type="image/x-icon" href="{{ $faviconUrl }}" />
    @if ($zalo)
        <meta name="zalo-platform-site-verification" content="{{ $zalo }}" />
    @endif

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-5.0.0-beta2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/LineIcons.2.0.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/tiny-slider.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/happyecotech.css') }}" />

    @stack('head')
</head>

<body>
    <div class="preloader">
        <div class="loader">
            <div class="spinner">
                <div class="spinner-container">
                    <div class="spinner-rotator">
                        <div class="spinner-left">
                            <div class="spinner-circle"></div>
                        </div>
                        <div class="spinner-right">
                            <div class="spinner-circle"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('partials.header')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    <a href="#" class="scroll-top btn-hover" aria-label="Lên đầu trang">
        <i class="lni lni-chevron-up"></i>
    </a>

    <script src="{{ asset('assets/js/bootstrap-5.0.0-beta2.min.js') }}"></script>
    <script src="{{ asset('assets/js/count-up.min.js') }}"></script>
    <script src="{{ asset('assets/js/tiny-slider.js') }}"></script>
    <script src="{{ asset('assets/js/wow.min.js') }}"></script>
    <script src="{{ asset('assets/js/polifill.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    @stack('scripts')
</body>

</html>
