@extends('layouts.public')

@php
    // Trang chủ: giữ cấu trúc chuyển đổi cố định (hero → proof → giải pháp → số liệu → giá trị → ứng dụng → tin → FAQ → CTA).
    // Các trang khác: giữ đúng thứ tự admin sắp xếp.
    $blocks = $page->blocks;
    $isHome = $page->slug === 'home';
    $defaults = $isHome ? require resource_path('views/blocks/defaults/home.php') : [];

    if ($isHome) {
        if (! $blocks->contains('block_type', 'faq') && ! empty($defaults['faq'])) {
            $blocks = $blocks->push(new \App\Models\PageBlock(['block_type' => 'faq', 'data' => $defaults['faq'], 'is_enabled' => true]));
        }
        $order = array_flip(['hero', 'partners', 'featured_services', 'stats', 'why_choose', 'vision_mission', 'core_values', 'app_download', 'latest_news', 'richtext', 'faq', 'cta']);
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
