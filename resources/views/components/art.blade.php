@props([
    'name',
    'width',
    'height',
    'alt' => null,
    'priority' => false,
    'sizes' => '100vw',
    'label' => 'Ảnh minh hoạ sẽ được bổ sung',
])
{{-- Slot ảnh v2: tự nhận public/assets/img/v2/{name}.{webp,svg,png,jpg}. Alt ưu tiên từ manifest.json.
     Chưa có file => khung giữ đúng tỉ lệ (không lệch layout, không CLS), không hiện chữ giả. --}}
@php
    $dir = public_path('assets/img/v2');
    $found = null;
    foreach ([$name, $name.'-lg'] as $stem) {
        foreach (['webp', 'svg', 'png', 'jpg'] as $ext) {
            if (! $found && is_file("{$dir}/{$stem}.{$ext}")) {
                $found = "{$stem}.{$ext}";
            }
        }
    }
    // Biến thể responsive {name}-sm/md/lg.webp (480/960/1600) nếu có.
    // Chỉ nhận biến thể cùng tỉ lệ với slot (tránh nhảy bố cục khi trình duyệt đổi nguồn).
    $sameRatio = fn ($f) => ($d = @getimagesize($f)) && abs($d[0] / max($d[1], 1) - $width / $height) < 0.02;
    $srcset = collect(['sm', 'md', 'lg'])
        ->filter(fn ($k) => is_file("{$dir}/{$name}-{$k}.webp") && $sameRatio("{$dir}/{$name}-{$k}.webp"))
        ->map(fn ($k) => asset("assets/img/v2/{$name}-{$k}.webp").' '.(@getimagesize("{$dir}/{$name}-{$k}.webp")[0] ?? 0).'w')
        ->when($found && str_ends_with($found, '.webp') && ! str_contains($found, '-lg.') && $sameRatio("{$dir}/{$found}"), fn ($c) => $c->push(asset("assets/img/v2/{$found}").' '.(@getimagesize("{$dir}/{$found}")[0] ?? 0).'w'))
        ->unique()->implode(', ');
    $manifestAlt = null;
    if ($found && is_file("{$dir}/manifest.json")) {
        $m = json_decode((string) file_get_contents("{$dir}/manifest.json"), true);
        $rows = is_array($m) ? ($m['images'] ?? $m['files'] ?? $m) : [];
        foreach ($rows as $key => $row) {
            if (! is_array($row)) { continue; }
            $file = basename((string) ($row['file'] ?? (is_string($key) ? $key : '')));
            if ($file === $found) { $manifestAlt = $row['alt'] ?? null; break; }
        }
    }
    $alt = $alt ?? $manifestAlt ?? '';
@endphp
@if ($found)
    @if ($priority && $srcset)
        @push('head')
            <link rel="preload" as="image" imagesrcset="{{ $srcset }}" imagesizes="{{ $sizes }}" fetchpriority="high" />
        @endpush
    @endif
    <img src="{{ asset('assets/img/v2/'.$found) }}" alt="{{ $alt }}" width="{{ $width }}" height="{{ $height }}"
        @if ($srcset) srcset="{{ $srcset }}" @endif sizes="{{ $sizes }}"
        @if ($priority) fetchpriority="high" decoding="async" @else loading="lazy" decoding="async" @endif
        {{ $attributes }} />
@else
    <div {{ $attributes->class(['flex items-center justify-center rounded-2xl border border-dashed border-brand-200 bg-brand-50 p-4 text-center text-sm text-ink-500']) }}
        style="aspect-ratio: {{ $width }} / {{ $height }}" aria-hidden="true">
        <span>{{ $label }}</span>
    </div>
@endif
