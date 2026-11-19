@php
    // items (CMS): [{title, description, image?}]. Chưa có -> lợi thế dựng từ nội dung thật của sản phẩm (SmartLabel, hợp tác xã, DataHub/OMC).
    $items = collect($data['items'] ?? [])->filter(fn ($i) => filled($i['title'] ?? null))->values();
    // Chỉ hiển thị khi CMS có items (nội dung lợi thế cũ thiên về truy xuất nguồn gốc đã bỏ khỏi trang chủ).
@endphp
@if ($items->isNotEmpty())
<section class="section section-tint" aria-labelledby="why-title">
    <div class="container-page">
        <div class="section-head">
            <h2 id="why-title">{{ $data['heading'] ?? 'Vì sao chọn Happy Ecotech' }}</h2>
        </div>
        <ul class="grid gap-6 md:grid-cols-3">
            @foreach ($items as $item)
                <li class="card !p-0 overflow-hidden">
                    @if (! empty($item['image']))
                        <x-art :name="$item['image']" width="800" height="600" class="w-full !rounded-none !border-0" sizes="(min-width: 768px) 380px, 100vw" />
                    @endif
                    <div class="p-6">
                        <h3>{{ $item['title'] }}</h3>
                        @if (! empty($item['description']))<p class="mt-2 text-ink-700">{{ $item['description'] }}</p>@endif
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</section>
@endif
