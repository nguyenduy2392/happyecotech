@extends('layouts.public')

@php
    // Trang chủ: giữ cấu trúc chuyển đổi cố định (hero → proof → giải pháp → số liệu → giá trị → ứng dụng → tin → FAQ → CTA).
    // Các trang khác: giữ đúng thứ tự admin sắp xếp.
    $blocks = $page->blocks;
    $isHome = $page->slug === 'home';

    if ($isHome) {
        // Thứ tự trang chủ: tổng quan hệ sinh thái → TaskFlow → MiniCRM → các giải pháp còn lại → giá trị. featured_services tách thành các block ảo.
        $virtual = fn (string $type, array $data = []) => new \App\Models\PageBlock(['block_type' => $type, 'data' => $data, 'is_enabled' => true]);
        $fs = $blocks->firstWhere('block_type', 'featured_services');
        $has = fn (string $t) => $blocks->contains('block_type', $t);
        $blocks = $blocks->reject(fn ($b) => $b->block_type === 'featured_services')->values();
        // Block ảo chỉ được thêm khi CMS chưa có block cùng loại (tránh render đôi).
        $vm = $blocks->firstWhere('block_type', 'vision_mission');
        if (! $has('tagline_reveal')) { $blocks->push($virtual('tagline_reveal', ['text' => is_array($vm?->data) ? ($vm->data['mission'] ?? null) : null])); }
        foreach (['benefit_strip', 'ecosystem_overview', 'product_taskflow', 'product_minicrm'] as $t) {
            if (! $has($t) && ($fs || $has('hero'))) { $blocks->push($virtual($t)); }
        }
        if (! $has('other_solutions') && ($fs || $has('hero'))) { $blocks->push($virtual('other_solutions', $fs?->data ?? [])); }
        $order = array_flip(['hero', 'benefit_strip', 'ecosystem_overview', 'partners', 'product_taskflow', 'app_download', 'product_minicrm', 'tagline_reveal', 'other_solutions', 'stats', 'why_choose', 'vision_mission', 'core_values', 'latest_news', 'richtext', 'faq', 'cta']);
        $blocks = $blocks->sortBy(fn ($b) => $order[$b->block_type] ?? 50)->values();
    }

    if ($page->slug === 'gioi-thieu' && ! $blocks->contains('block_type', 'leadership')) {
        $cta = $blocks->firstWhere('block_type', 'cta');
        $leadershipBlock = new \App\Models\PageBlock(['block_type' => 'leadership', 'data' => [], 'is_enabled' => true]);
        $blocks = $cta ? $blocks->reject(fn ($b) => $b === $cta)->push($leadershipBlock, $cta)->values() : $blocks->push($leadershipBlock);
    }

    $heroBlock = $blocks->firstWhere('block_type', 'hero');
    $isFirstHeroH1 = true;
@endphp

@section('content')
    @unless ($heroBlock)
        @include('partials.page-hero', [
            'title' => $page->title,
            'lead' => null,
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
