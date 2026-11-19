{{-- Đầu trang con: breadcrumb + H1 duy nhất của trang + đoạn dẫn. Biến: $title, $lead?, $crumbs? --}}
<section class="section-brand border-b border-ink-100">
    <div class="container-page py-10 md:py-14">
        @isset($crumbs)
            <x-breadcrumbs :items="$crumbs" />
        @endisset
        <h1 class="hero-title mt-4">{{ $title }}</h1>
        @if (! empty($lead))
            <p class="hero-lead mt-4">{{ $lead }}</p>
        @endif
        {{ $slot ?? '' }}
    </div>
</section>
