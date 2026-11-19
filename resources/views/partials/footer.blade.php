@include('partials.ui-services')
@include('partials.ui-channels')
@php
    $safe = fn ($u) => function_exists('safe_url') ? safe_url($u) : null; // R2-01: mọi URL từ CMS đi qua safe_url()
    $footerLogo = setting('footer_logo_media_id') ?? setting('logo_media_id');
    $siteName = setting('site_name', 'Happy Ecotech');
    $isDefaultLogo = ! $footerLogo || ($footerLogo->disk === 'assets' && $footerLogo->path === 'img/logo/logo-full.png');
    $phone = setting('contact_phone');
    $email = setting('contact_email');
    $address = setting('contact_address');
    $mapEmbed = $safe(setting('map_embed_url'));
    $showSocial = setting('footer_show_social', true);
    $showServices = setting('footer_show_services', true);
    $policyPages = \App\Models\Page::query()->where('status', 'published')
        ->whereIn('slug', ['chinh-sach-bao-mat', 'dieu-khoan-su-dung'])
        ->get();
    $socialIcons = ['facebook' => 'facebook', 'youtube' => 'youtube', 'linkedin' => 'linkedin'];
@endphp
<footer class="section-ink">
    <div class="container-page py-14 md:py-16">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-[1.4fr_1fr_1fr_1.3fr]">
            <div>
                {{-- Logo xanh trên nền ink không đủ tương phản: đặt trong khối trắng. --}}
                <a href="{{ route('home') }}" class="inline-block rounded-lg bg-white px-3 py-2" aria-label="{{ $siteName }} - Trang chủ">
                    @if ($isDefaultLogo)
                        <img src="{{ asset('assets/img/logo/logo-full.webp') }}" alt="{{ $siteName }}" width="380" height="141"
                            class="h-9 w-auto" loading="lazy" decoding="async" />
                    @else
                        <x-media-img :media="$footerLogo" :alt="$siteName" class="h-9 w-auto" sizes="140px" />
                    @endif
                </a>
                @if (setting('footer_description'))
                    <p class="mt-5 max-w-sm text-ink-300">{{ setting('footer_description') }}</p>
                @endif
                @php $ch = app('ui.channels'); @endphp
                @if ($showSocial && ($ch['zalo'] || $ch['facebook'] || $ch['others']->isNotEmpty()))
                    <ul class="mt-5 flex gap-2">
                        @foreach ([['Zalo Happy Ecotech', $ch['zalo'], 'message'], ['Facebook Happy Ecotech', $ch['facebook'], 'facebook']] as [$label, $url, $icon])
                            @if ($url)
                                <li>
                                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $label }}"
                                        class="flex size-11 items-center justify-center rounded-lg border border-ink-700 text-white hover:bg-ink-700"><x-glyph :name="$icon" /></a>
                                </li>
                            @endif
                        @endforeach
                        @foreach ($ch['others'] as $social)
                            <li>
                                <a href="{{ $safe($social->url) }}" target="_blank" rel="noopener noreferrer"
                                    class="flex size-11 items-center justify-center rounded-lg border border-ink-700 text-white hover:bg-ink-700"
                                    aria-label="{{ $social->label ?: $social->platform }} Happy Ecotech">
                                    <x-glyph :name="$socialIcons[strtolower((string) $social->platform)] ?? 'link'" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <nav aria-label="Liên kết nhanh">
                <h2 class="text-lg text-white">Liên kết</h2>
                <ul class="mt-4 space-y-1">
                    @forelse ($footerQuickMenu as $item)
                        <li><a href="{{ $item->resolveUrl() }}" class="inline-flex min-h-11 items-center text-ink-300 hover:text-white">{{ $item->label }}</a></li>
                    @empty
                        <li><a href="{{ route('home') }}" class="inline-flex min-h-11 items-center text-ink-300 hover:text-white">Trang chủ</a></li>
                    @endforelse
                </ul>
            </nav>

            @if ($showServices && app('ui.services')->isNotEmpty())
                <nav aria-label="Sản phẩm">
                    <h2 class="text-lg text-white">Sản phẩm</h2>
                    <ul class="mt-4 space-y-1">
                        @foreach (app('ui.services') as $service)
                            <li><a href="{{ route('services.show', $service) }}" class="inline-flex min-h-11 items-center text-ink-300 hover:text-white">{{ $service->name }}</a></li>
                        @endforeach
                    </ul>
                </nav>
            @endif

            <div>
                <h2 class="text-lg text-white">Liên hệ</h2>
                <ul class="mt-4 space-y-3 text-ink-300">
                    @if ($phone)
                        <li class="flex gap-3"><x-glyph name="phone" class="mt-1" /><a href="tel:{{ preg_replace('/[^\d+]/', '', $phone) }}" class="hover:text-white">{{ $phone }}</a></li>
                    @endif
                    @if ($email)
                        <li class="flex gap-3"><x-glyph name="mail" class="mt-1" /><a href="mailto:{{ $email }}" class="break-all hover:text-white">{{ $email }}</a></li>
                    @endif
                    @if ($address)
                        <li class="flex gap-3"><x-glyph name="map-pin" class="mt-1" /><span>{{ $address }}</span></li>
                    @endif
                </ul>
                @if ($mapEmbed)
                    <iframe src="{{ $mapEmbed }}" title="Bản đồ văn phòng {{ $siteName }}" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade" class="mt-5 h-36 w-full rounded-lg border border-ink-700"></iframe>
                @endif
            </div>
        </div>

        <div class="mt-12 flex flex-col gap-3 border-t border-ink-700 pt-6 text-sm text-ink-300 md:flex-row md:items-center md:justify-between">
            <p>© {{ date('Y') }} {{ setting('footer_copyright', $siteName) }}. Bản quyền đã được bảo hộ.</p>
            @if ($policyPages->isNotEmpty())
                <ul class="flex flex-wrap gap-x-5">
                    @foreach ($policyPages as $page)
                        <li><a href="{{ route('page.show', $page) }}" class="inline-flex min-h-11 items-center underline underline-offset-2 hover:text-white">{{ $page->title }}</a></li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</footer>
