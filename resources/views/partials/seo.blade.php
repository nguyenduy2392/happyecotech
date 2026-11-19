<title>{{ $seo['title'] ?? config('app.name') }}</title>
<meta name="description" content="{{ $seo['description'] ?? '' }}" />
<meta name="robots" content="{{ $seo['robots'] ?? 'index,follow' }}" />
<link rel="canonical" href="{{ $seo['canonical'] ?? url()->current() }}" />

<meta property="og:type" content="{{ $seo['type'] ?? 'website' }}" />
<meta property="og:title" content="{{ $seo['title'] ?? '' }}" />
<meta property="og:description" content="{{ $seo['description'] ?? '' }}" />
<meta property="og:url" content="{{ $seo['canonical'] ?? url()->current() }}" />
<meta property="og:site_name" content="{{ $seo['site_name'] ?? config('app.name') }}" />
<meta property="og:locale" content="{{ $seo['locale'] ?? 'vi_VN' }}" />
@if (! empty($seo['og_image']))
    <meta property="og:image" content="{{ $seo['og_image'] }}" />
    <meta property="og:image:alt" content="{{ $seo['image_alt'] ?? $seo['site_name'] ?? config('app.name') }}" />
    <meta name="twitter:image" content="{{ $seo['og_image'] }}" />
@endif
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $seo['title'] ?? '' }}" />
<meta name="twitter:description" content="{{ $seo['description'] ?? '' }}" />
@if (! empty($seo['schema']))
    <script type="application/ld+json">{!! json_encode($seo['schema'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endif
