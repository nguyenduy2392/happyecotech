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
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    @include('partials.seo', ['seo' => $seo])

    <link rel="icon" href="{{ $faviconUrl }}" />
    @if ($zalo)
        <meta name="zalo-platform-site-verification" content="{{ $zalo }}" />
    @endif

    <link rel="preload" href="{{ asset('assets/fonts/bvp-600-latin.woff2') }}" as="font" type="font/woff2" crossorigin />
    <link rel="preload" href="{{ asset('assets/fonts/bvp-400-latin.woff2') }}" as="font" type="font/woff2" crossorigin />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>

<body>
    <a href="#main" class="skip-link">Bỏ qua, đến nội dung chính</a>

    @include('partials.header')

    <main id="main" tabindex="-1">
        @yield('content')
    </main>

    @include('partials.footer')
    @include('partials.contact-bar')

    @stack('scripts')
</body>

</html>
