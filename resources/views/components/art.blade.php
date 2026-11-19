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
    foreach (['webp', 'svg', 'png', 'jpg'] as $ext) {
        if (is_file("{$dir}/{$name}.{$ext}")) {
            $found = "{$name}.{$ext}";
            break;
        }
    }
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
    <img src="{{ asset('assets/img/v2/'.$found) }}" alt="{{ $alt }}" width="{{ $width }}" height="{{ $height }}"
        sizes="{{ $sizes }}"
        @if ($priority) fetchpriority="high" decoding="async" @else loading="lazy" decoding="async" @endif
        {{ $attributes }} />
@else
    <div {{ $attributes->class(['flex items-center justify-center rounded-lg border border-dashed border-brand-200 bg-brand-50 p-4 text-center text-sm text-ink-500']) }}
        style="aspect-ratio: {{ $width }} / {{ $height }}" aria-hidden="true">
        <span>{{ $label }}</span>
    </div>
@endif
