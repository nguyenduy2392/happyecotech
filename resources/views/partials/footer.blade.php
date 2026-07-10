@php
    $footerLogo = setting('footer_logo_media_id') ?? setting('logo_media_id');
    $footerLogoUrl = $footerLogo?->url ?? asset('assets/img/logo/logo-full.png');
    $phone = setting('contact_phone');
    $email = setting('contact_email');
    $address = setting('contact_address');
    $mapEmbed = setting('map_embed_url');
    $showSocial = setting('footer_show_social', true);
    $showServices = setting('footer_show_services', true);
    $policyPages = \App\Models\Page::query()->where('status', 'published')
        ->whereIn('slug', ['chinh-sach-bao-mat', 'dieu-khoan-su-dung'])
        ->get();
@endphp
<footer class="footer">
    <div class="container">
        <div class="widget-wrapper">
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="footer-widget">
                        <div class="logo mb-35">
                            <a href="{{ route('home') }}"><img src="{{ $footerLogoUrl }}" alt="{{ setting('site_name', 'Happy Ecotech') }}"></a>
                        </div>
                        <p class="desc mb-35">{{ setting('footer_description') }}</p>
                        @if ($showSocial && $socialLinks->isNotEmpty())
                            <ul class="socials">
                                @foreach ($socialLinks as $social)
                                    @continue(blank($social->url))
                                    <li>
                                        <a href="{{ $social->url }}" target="_blank" rel="noopener noreferrer"
                                            aria-label="{{ $social->label ?: $social->platform }}">
                                            <i class="{{ $social->icon }}"></i>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                <div class="col-xl-2 offset-xl-1 col-md-5 offset-md-1 col-sm-6">
                    <div class="footer-widget">
                        <h3>Liên kết</h3>
                        <ul class="links">
                            @forelse ($footerQuickMenu as $item)
                                <li><a href="{{ $item->resolveUrl() }}">{{ $item->label }}</a></li>
                            @empty
                                <li><a href="{{ route('home') }}">Trang chủ</a></li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                @if ($showServices && $footerServices->isNotEmpty())
                    <div class="col-xl-3 col-md-6 col-sm-6">
                        <div class="footer-widget">
                            <h3>Dịch vụ</h3>
                            <ul class="links">
                                @foreach ($footerServices as $service)
                                    <li><a href="{{ route('services.show', $service) }}">{{ $service->name }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <div class="col-xl-3 col-md-6">
                    <div class="footer-widget">
                        <h3>Liên hệ</h3>
                        <ul>
                            @if ($phone)
                                <li><a href="tel:{{ preg_replace('/\s+/', '', $phone) }}">{{ $phone }}</a></li>
                            @endif
                            @if ($email)
                                <li><a href="mailto:{{ $email }}">{{ $email }}</a></li>
                            @endif
                            @if ($address)
                                <li>{{ $address }}</li>
                            @endif
                        </ul>
                        @if ($mapEmbed)
                            <div class="contact_map" style="width: 100%; height: 150px; margin-top: 25px;">
                                <div class="gmap_canvas">
                                    <iframe src="{{ $mapEmbed }}" style="width: 100%;" title="Bản đồ" loading="lazy"></iframe>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="copyright">© {{ date('Y') }} {{ setting('footer_copyright', setting('site_name', 'Happy Ecotech')) }}. Bản quyền đã được bảo hộ.</p>
                </div>
                @if ($policyPages->isNotEmpty())
                    <div class="col-md-6">
                        <ul class="footer-bottom-links">
                            @foreach ($policyPages as $page)
                                <li><a href="{{ route('page.show', $page) }}">{{ $page->title }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</footer>
