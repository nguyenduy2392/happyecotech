<title>{{ $seo['title'] ?? config('app.name') }}</title>
<meta name="description" content="{{ $seo['description'] ?? '' }}" />
<meta name="robots" content="{{ $seo['robots'] ?? 'index,follow' }}" />
<link rel="canonical" href="{{ $seo['canonical'] ?? url()->current() }}" />

<meta property="og:type" content="{{ $seo['type'] ?? 'website' }}" />
<meta property="og:title" content="{{ $seo['title'] ?? '' }}" />
<meta property="og:description" content="{{ $seo['description'] ?? '' }}" />
<meta property="og:url" content="{{ $seo['canonical'] ?? url()->current() }}" />
@if (! empty($seo['og_image']))
    <meta property="og:image" content="{{ $seo['og_image'] }}" />
@endif
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $seo['title'] ?? '' }}" />
<meta name="twitter:description" content="{{ $seo['description'] ?? '' }}" />
