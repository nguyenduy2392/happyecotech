@extends('layouts.public')

@php
    // Trang chủ: giữ cấu trúc chuyển đổi cố định (hero → proof → giải pháp → số liệu → giá trị → ứng dụng → tin → FAQ → CTA).
    // Các trang khác: giữ đúng thứ tự admin sắp xếp.
    $blocks = $page->blocks;
    $isHome = $page->slug === 'home';

    if ($isHome) {
        // Định vị: TaskFlow (chủ lực) → MiniCRM → giá trị → giải pháp khác. featured_services tách thành các block ảo.
        $virtual = fn (string $type, array $data = []) => new \App\Models\PageBlock(['block_type' => $type, 'data' => $data, 'is_enabled' => true]);
        $fs = $blocks->firstWhere('block_type', 'featured_services');
        if ($fs) {
            $blocks = $blocks->reject(fn ($b) => $b->block_type === 'featured_services')
                ->push($virtual('benefit_strip'), $virtual('product_taskflow'), $virtual('product_minicrm'), $virtual('other_solutions', $fs->data ?? []));
        }
        $order = array_flip(['hero', 'benefit_strip', 'partners', 'product_taskflow', 'product_minicrm', 'app_download', 'stats', 'why_choose', 'vision_mission', 'core_values', 'other_solutions', 'latest_news', 'richtext', 'faq', 'cta']);
        $blocks = $blocks->sortBy(fn ($b) => $order[$b->block_type] ?? 50)->values();
    }

    $heroBlock = $blocks->firstWhere('block_type', 'hero');
    $isFirstHeroH1 = true;
@endphp

@section('content')
    @unless ($heroBlock)
        @include('partials.page-hero', [
            'title' => $page->title,
            'crumbs' => [['label' => $page->title]],
        ])
    @endunless

    @foreach ($blocks as $block)
        @includeIf('blocks.'.$block->block_type, [
            'block' => $block,
            'data' => $block->data ?? [],
            'headingTag' => ($block->block_type === 'hero' && $isFirstHeroH1) ? 'h1' : 'h2',
        ])
        @php if ($block->block_type === 'hero') { $isFirstHeroH1 = false; } @endphp
    @endforeach
@endsection
