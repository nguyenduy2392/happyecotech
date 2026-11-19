@props(['service', 'limit' => 4])
{{-- Ảnh màn hình sản phẩm: ưu tiên gallery CMS, sau đó public/assets/img/v2/screen-{slug}-N.webp. Không có ảnh -> không render gì.
     Mỗi ảnh là link tới ảnh lớn (dùng được khi tắt JS); JS mở <dialog> phóng to. --}}
@php
    $items = collect();
    foreach ($service->gallery ?? [] as $media) {
        $items->push(['src' => $media->url, 'full' => $media->url, 'w' => $media->width ?: 1200, 'h' => $media->height ?: 800, 'alt' => $media->alt ?: ($service->name.' - màn hình ứng dụng')]);
    }
    if ($items->isEmpty()) {
        $dir = public_path('assets/img/v2');
        $alts = [];
        if (is_file("{$dir}/manifest.json")) {
            foreach ((array) json_decode((string) file_get_contents("{$dir}/manifest.json"), true) as $row) {
                if (is_array($row) && isset($row['file'])) { $alts[$row['file']] = $row['alt'] ?? null; }
            }
        }
        $files = glob("{$dir}/screen-{$service->slug}-*.webp") ?: [];
        natsort($files);
        foreach ($files as $f) {
            if (preg_match('/-(sm|md)\.webp$/', $f)) { continue; }
            $name = basename($f);
            [$w, $h] = @getimagesize($f) ?: [1200, 800];
            $items->push(['src' => asset("assets/img/v2/{$name}"), 'full' => asset("assets/img/v2/{$name}"), 'w' => $w, 'h' => $h, 'alt' => $alts[$name] ?? ($service->name.' - màn hình ứng dụng')]);
        }
    }
    $items = $items->take($limit)->values();
    $portrait = $items->isNotEmpty() && $items[0]['h'] > $items[0]['w'];
@endphp
@if ($items->isNotEmpty())
    <ul {{ $attributes->class(['grid gap-4', $portrait ? 'grid-cols-2 sm:grid-cols-4' : 'grid-cols-1 sm:grid-cols-2']) }}>
        @foreach ($items as $item)
            <li class="overflow-hidden rounded-lg border border-ink-100 bg-white">
                <a href="{{ $item['full'] }}" data-lightbox class="group relative block" aria-label="Phóng to: {{ $item['alt'] }}">
                    <img src="{{ $item['src'] }}" alt="{{ $item['alt'] }}" width="{{ $item['w'] }}" height="{{ $item['h'] }}" loading="lazy" decoding="async" class="h-auto w-full" />
                </a>
            </li>
        @endforeach
    </ul>
    @once
        @push('scripts')
            <dialog data-lightbox-dialog aria-label="Xem ảnh lớn" class="lightbox">
                <form method="dialog"><button class="lightbox-close btn btn-light" aria-label="Đóng ảnh lớn"><x-glyph name="x" /></button></form>
                <img src="" alt="" class="lightbox-img" />
            </dialog>
        @endpush
    @endonce
@endif
