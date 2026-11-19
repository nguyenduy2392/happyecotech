@php
    // Dịch vụ đã xuất bản, sắp xếp TaskFlow (chủ lực) → MiniCRM (số 2) → các giải pháp khác theo thứ tự CMS. Chỉ truy vấn 1 lần/request.
    app()->singletonIf('ui.services', function () {
        $priority = ['taskflow' => 0, 'minicrm' => 1];

        return \App\Models\Service::query()->published()->ordered()->with(['icon', 'cover', 'features', 'gallery'])->get()
            ->sortBy(fn ($s) => $priority[$s->slug] ?? 10 + $s->sort_order)->values();
    });
@endphp
