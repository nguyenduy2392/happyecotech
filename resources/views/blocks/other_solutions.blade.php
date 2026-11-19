@include('partials.ui-services')
@php
    // Giải pháp phụ (không phải TaskFlow/MiniCRM). Admin có thể ghi đè nhóm bằng $data['groups'] = [{title, description, icon, slugs: []}].
    $all = app('ui.services')->reject(fn ($s) => in_array($s->slug, ['taskflow', 'minicrm'], true));
    $groups = collect($data['groups'] ?? [
        ['title' => 'Truy xuất nguồn gốc & tem điện tử', 'icon' => 'qr', 'slugs' => ['smartlabel', 'farm2home'],
            'description' => 'SmartLabel gắn mã QR để người mua kiểm tra nguồn gốc, kiểm định và chất lượng sản phẩm; Farm2Home kết nối nông dân với người tiêu dùng.'],
        ['title' => 'Hành chính số cấp xã, phường', 'icon' => 'building', 'slugs' => ['datahub', 'omc'],
            'description' => 'DataHub Văn thư số AI và OMC trong hệ sinh thái hành chính số.'],
    ])->map(function ($g) use ($all) {
        $g['services'] = $all->filter(fn ($s) => in_array($s->slug, $g['slugs'] ?? [], true))->values();

        return $g;
    });
    $used = $groups->flatMap(fn ($g) => $g['services']->pluck('id'))->all();
    $rest = $all->reject(fn ($s) => in_array($s->id, $used, true))->values();
    if ($rest->isNotEmpty()) {
        $groups->push(['title' => 'Giải pháp khác', 'icon' => 'layers', 'description' => 'Các sản phẩm khác trong hệ sinh thái Happy Ecotech.', 'services' => $rest]);
    }
@endphp
<section class="section section-tint" aria-labelledby="other-title" id="giai-phap-khac">
    <div class="container-page">
        <div class="section-head">
            <h2 id="other-title">{{ $data['other_heading'] ?? 'Giải pháp khác của Happy Ecotech' }}</h2>
        </div>
        <div class="grid gap-6 md:grid-cols-2">
            @foreach ($groups as $g)
                <div class="card">
                    <h3 class="flex items-center gap-3"><x-glyph :name="$g['icon'] ?? 'layers'" :size="24" class="text-brand-600" />{{ $g['title'] }}</h3>
                    <p class="mt-2 text-ink-700">{{ $g['description'] ?? '' }}</p>
                    <ul class="mt-4 space-y-1 border-t border-ink-100 pt-3">
                        @forelse ($g['services'] as $service)
                            <li>
                                <a href="{{ route('services.show', $service) }}" data-cta="other-{{ $service->slug }}" class="flex min-h-11 items-center justify-between gap-3 font-semibold text-ink-900 no-underline hover:text-brand-700">
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
                                    Liên hệ để được tư vấn <x-glyph name="arrow-right" :size="18" />
                                </a>
                            </li>
                        @endforelse
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
</section>
