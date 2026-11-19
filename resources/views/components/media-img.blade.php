@props([
    'media' => null,
    'src' => null,
    'alt' => null,
    'width' => null,
    'height' => null,
    'priority' => false,
    'sizes' => '100vw',
])
@php
    /** @var \App\Models\MediaAsset|null $asset */
    $asset = $media instanceof \App\Models\MediaAsset ? $media : null;
    $original = $asset?->url ?? $src;
    $alt = $alt ?? ($asset?->alt ?? '');
    $width = $width ?? $asset?->width;
    $height = $height ?? $asset?->height;

    $srcset = null;
    $fallbackSrc = $original;

    if ($asset) {
        // 1) Biến thể WebP của luồng A: media_url($media, 'sm'|'md'|'lg'). Chưa có -> TypeError/giá trị gốc -> bỏ qua.
        $variants = [];
        // Chỉ gọi khi helper nhận MediaAsset (bản hiện tại nhận id: truyền model sẽ sinh SQL lỗi).
        static $variantAware = null;
        $variantAware ??= str_contains((string) (new \ReflectionFunction('media_url'))->getParameters()[0]->getType(), 'MediaAsset');
        foreach ($variantAware ? ['sm' => 480, 'md' => 960, 'lg' => 1600] : [] as $variant => $w) {
            try {
                $u = media_url($asset, $variant);
            } catch (\Throwable) {
                $u = null;
            }
            // Helper hiện tại có thể trả về chính tên biến thể làm fallback: chỉ nhận URL thật.
            if (is_string($u) && preg_match('~^(https?:)?/~', $u) && $u !== $original) {
                $variants[$u] = $w;
            }
        }
        // 2) Bản WebP tối ưu sẵn cạnh file gốc trong public/assets (vd hero-1-480.webp / hero-1-960.webp).
        if (! $variants && $asset->disk === 'assets' && filled($asset->path)) {
            $stem = \Illuminate\Support\Str::beforeLast($asset->path, '.');
            foreach ([480, 960] as $w) {
                if (is_file(public_path("assets/{$stem}-{$w}.webp"))) {
                    $variants[asset("assets/{$stem}-{$w}.webp")] = $w;
                }
            }
        }
        if (count($variants) > 0) {
            $parts = [];
            foreach ($variants as $u => $w) {
                $parts[] = $u.' '.$w.'w';
            }
            $srcset = implode(', ', $parts);
            $fallbackSrc = array_key_last($variants);
            $fallbackSrc = array_search(max($variants), $variants, true) ?: $fallbackSrc;
        }
    }
@endphp
@if ($fallbackSrc)
    @if ($priority && $srcset)
        @push('head')
            <link rel="preload" as="image" imagesrcset="{{ $srcset }}" imagesizes="{{ $sizes }}" fetchpriority="high" />
        @endpush
    @endif
    <img src="{{ $fallbackSrc }}"
        @if ($srcset) srcset="{{ $srcset }}" sizes="{{ $sizes }}" @endif
        alt="{{ $alt }}"
        @if ($width) width="{{ $width }}" @endif
        @if ($height) height="{{ $height }}" @endif
        @if ($priority) fetchpriority="high" decoding="async" @else loading="lazy" decoding="async" @endif
        {{ $attributes }} />
@endif
