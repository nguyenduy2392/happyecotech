@include('partials.ui-services')
@php
    // Danh sách 6 giải pháp của hệ sinh thái: dịch vụ đã xuất bản + ô "chưa có trang" (DataHub/OMC) từ meta. Memo 1 lần/request.
    app()->singletonIf('ui.ecosystem', function () {
        $meta = require resource_path('views/public/services/meta.php');
        $order = ['taskflow', 'minicrm', 'smartlabel', 'farm2home', 'datahub', 'omc'];
        $published = app('ui.services')->keyBy('slug');
        $items = collect();
        foreach ($order as $slug) {
            $m = $meta[$slug] ?? [];
            $svc = $published->get($slug);
            $items->push([
                'slug' => $slug,
                'service' => $svc,
                'name' => $svc?->name ?? ($m['name'] ?? ucfirst($slug)),
                'category' => $svc?->getAttribute('category_label') ?: ($m['category'] ?? null),
                'headline' => $svc?->getAttribute('tagline') ?: ($m['headline'] ?? ($svc?->name)),
                'summary' => $svc?->short_description ?: ($m['summary'] ?? null),
                'group' => $m['group'] ?? 'trace',
                'url' => $svc ? route('services.show', $svc) : null,
                'cta' => $m['cta'] ?? ('Tìm hiểu '.($svc?->name ?? ($m['name'] ?? ''))),
            ]);
        }
        // Dịch vụ xuất bản khác (admin thêm mới) nằm sau 6 ô chuẩn.
        foreach ($published as $slug => $svc) {
            if (! in_array($slug, $order, true)) {
                $items->push(['slug' => $slug, 'service' => $svc, 'name' => $svc->name, 'category' => $svc->getAttribute('category_label'),
                    'headline' => $svc->getAttribute('tagline') ?: $svc->name, 'summary' => $svc->short_description, 'group' => 'trace',
                    'url' => route('services.show', $svc), 'cta' => 'Tìm hiểu '.$svc->name]);
            }
        }

        return $items;
    });
@endphp
