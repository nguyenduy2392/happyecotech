@php
    // items (CMS): [{title, description, image?}]. Chưa có -> lợi thế dựng từ nội dung thật của sản phẩm (SmartLabel, hợp tác xã, DataHub/OMC).
    $items = collect($data['items'] ?? [])->filter(fn ($i) => filled($i['title'] ?? null))->values();
    if ($items->isEmpty()) {
        $items = collect([
            ['image' => 'industry-produce', 'title' => 'Mỗi sản phẩm một mã QR duy nhất', 'description' => 'SmartLabel gắn mã QR riêng cho từng sản phẩm hoặc lô hàng, người mua quét là xem được hồ sơ nguồn gốc.'],
            ['image' => 'industry-cooperative', 'title' => 'Triển khai gọn, mở rộng dần', 'description' => 'Bắt đầu từ vài sản phẩm hoặc một tổ sản xuất rồi mở rộng theo nhu cầu; phù hợp doanh nghiệp nhỏ và hợp tác xã.'],
            ['image' => 'industry-commune', 'title' => 'Sẵn sàng cho hành chính số cấp xã, phường', 'description' => 'DataHub Văn thư số AI và OMC nằm trong cùng hệ sinh thái với tem điện tử và quản trị vận hành.'],
        ]);
    }
@endphp
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
