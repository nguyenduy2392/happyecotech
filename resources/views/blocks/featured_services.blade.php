@php
    $all = \App\Models\Service::query()->published()->ordered()->with('icon')->get();

    // Nhóm giải pháp. Admin có thể ghi đè bằng $data['groups'] = [{title, description, icon, slugs: []}].
    $groups = collect($data['groups'] ?? [
        ['title' => 'Truy xuất & Tem điện tử', 'icon' => 'qr', 'art' => 'group-traceability', 'slugs' => ['smartlabel', 'farm2home'],
            'description' => 'Gắn mã QR để người mua tự kiểm tra nguồn gốc, kiểm định và chất lượng sản phẩm.'],
        ['title' => 'Quản trị vận hành', 'icon' => 'workflow', 'art' => 'group-operations', 'slugs' => ['taskflow', 'minicrm'],
            'description' => 'Quản lý công việc, dự án và khách hàng trên nền tảng gọn nhẹ, dùng được cả trên điện thoại.'],
        ['title' => 'Hành chính số', 'icon' => 'building', 'art' => 'group-egov', 'slugs' => ['datahub', 'omc'],
            'description' => 'Hệ sinh thái cho cấp xã, phường: DataHub Văn thư số AI và OMC.'],
    ]);

    $used = [];
    $groups = $groups->map(function ($g) use ($all, &$used) {
        $g['services'] = $all->filter(fn ($s) => in_array($s->slug, $g['slugs'] ?? [], true))->values();
        $used = array_merge($used, $g['services']->pluck('id')->all());
        return $g;
    });
    $rest = $all->reject(fn ($s) => in_array($s->id, $used, true))->values();
    if ($rest->isNotEmpty()) {
        $groups->push(['title' => 'Giải pháp khác', 'icon' => 'layers', 'description' => 'Các sản phẩm khác trong hệ sinh thái Happy Ecotech.', 'services' => $rest]);
    }
@endphp
<section class="section" aria-labelledby="solutions-title" id="giai-phap">
    <div class="container-page">
        <div class="section-head">
            <h2 id="solutions-title">{{ $data['heading'] ?? 'Giải pháp theo nhu cầu của bạn' }}</h2>
            @if (! empty($data['subheading']))
                <p>{{ $data['subheading'] }}</p>
            @endif
        </div>
        <div class="grid gap-6 lg:grid-cols-3">
            @foreach ($groups as $g)
                <div class="card">
                    @if (! empty($g['art']))
                        <x-art :name="$g['art']" width="800" height="600" class="mb-5 w-full" sizes="(min-width: 1024px) 360px, 100vw" />
                    @endif
                    <h3 class="flex items-center gap-3">
                        <x-glyph :name="$g['icon'] ?? 'layers'" :size="26" class="text-brand-600" />
                        {{ $g['title'] }}
                    </h3>
                    <p class="mt-2 text-ink-700">{{ $g['description'] ?? '' }}</p>
                    <ul class="mt-5 space-y-1 border-t border-ink-100 pt-4">
                        @forelse ($g['services'] as $service)
                            <li>
                                <a href="{{ route('services.show', $service) }}" class="flex min-h-11 items-center justify-between gap-3 font-semibold text-ink-900 no-underline hover:text-brand-700">
                                    <span class="flex items-center gap-3">
                                        <x-art :name="'icon-'.$service->slug.'-48'" width="28" height="28" :alt="''" class="size-7 !border-0 !bg-transparent !p-0 [&>span]:hidden" sizes="28px" />
                                        {{ $service->name }}
                                    </span>
                                    <x-glyph name="arrow-right" :size="18" class="text-brand-600" />
                                </a>
                            </li>
                        @empty
                            <li>
                                <a href="{{ route('contact') }}#contact-form" class="flex min-h-11 items-center justify-between gap-3 font-semibold text-brand-700 no-underline hover:underline">
                                    Liên hệ để được tư vấn
                                    <x-glyph name="arrow-right" :size="18" />
                                </a>
                            </li>
                        @endforelse
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
</section>
