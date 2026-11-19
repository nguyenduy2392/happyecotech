@include('partials.ui-ecosystem')
@php
    $items = app('ui.ecosystem');
    $anchorFor = fn ($it) => in_array($it['slug'], ['taskflow', 'minicrm'], true) && $it['service']
        ? '#san-pham-'.$it['slug']
        : '#giai-phap-'.$it['slug'];
@endphp
<section class="section" aria-labelledby="eco-title" id="he-sinh-thai">
    <div class="container-page">
        <div class="section-head">
            <h2 id="eco-title">{{ $data['heading'] ?? 'Hệ sinh thái giải pháp Happy Ecotech' }}</h2>
            <p>{{ $data['intro'] ?? 'Happy Ecotech phát triển phần mềm cho doanh nghiệp, hợp tác xã và chính quyền cơ sở: quản lý công việc và khách hàng, truy xuất nguồn gốc, hành chính số.' }}</p>
        </div>
        <ul class="grid gap-4 md:grid-cols-2 lg:grid-cols-12">
            @foreach ($items->take(6) as $it)
                @php $big = $loop->index < 2; @endphp
                <li class="{{ $big ? 'lg:col-span-6' : 'lg:col-span-3' }}">
                    <a href="{{ $anchorFor($it) }}" data-cta="eco-{{ $it['slug'] }}"
                        class="card h-full gap-3 {{ $big ? 'border-brand-200 bg-brand-50 md:flex-row md:items-start md:gap-5 md:p-7' : '' }}">
                        <x-art :name="'icon-'.$it['slug']" width="56" height="56" :alt="''"
                            class="{{ $big ? 'size-14' : 'size-11' }} shrink-0 !border-0 !bg-transparent !p-0 [&>span]:hidden" sizes="56px" />
                        <span class="block min-w-0">
                            @if ($it['category'])<span class="block text-sm font-semibold text-brand-700">{{ $it['category'] }}</span>@endif
                            <h3 class="{{ $big ? 'text-h2' : '' }} mt-0.5">{{ $it['name'] }}</h3>
                            @if ($it['summary'])<span class="mt-2 line-clamp-2 block text-sm text-ink-700">{{ $it['summary'] }}</span>@endif
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</section>
