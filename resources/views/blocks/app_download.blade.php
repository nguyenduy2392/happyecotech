@php
    $ios = $data['ios_url'] ?? setting('app_store_url');
    $android = $data['android_url'] ?? setting('google_play_url');
    $iosQr = filled($data['ios_qr_media_id'] ?? null) ? \App\Models\MediaAsset::find($data['ios_qr_media_id']) : null;
    $androidQr = filled($data['android_qr_media_id'] ?? null) ? \App\Models\MediaAsset::find($data['android_qr_media_id']) : null;
@endphp
@if ($ios || $android)
    <section class="section section-tint" aria-labelledby="app-title" id="ung-dung">
        <div class="container-page grid items-center gap-10 lg:grid-cols-2 lg:gap-16">
            <div>
                <h2 id="app-title" class="text-h2">{{ $data['heading'] ?? 'Ứng dụng TaskFlow trên điện thoại' }}</h2>
                @if (! empty($data['lead']))
                    <p class="mt-4 max-w-xl text-lead text-ink-700">{{ $data['lead'] }}</p>
                @endif
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    @if ($ios)
                        <a href="{{ $ios }}" class="btn btn-primary btn-lg" target="_blank" rel="noopener noreferrer">
                            <x-glyph name="smartphone" /> Tải trên App Store
                        </a>
                    @endif
                    @if ($android)
                        <a href="{{ $android }}" class="btn btn-outline btn-lg" target="_blank" rel="noopener noreferrer">
                            <x-glyph name="smartphone" /> Tải trên Google Play
                        </a>
                    @endif
                </div>
            @if ($iosQr || $androidQr)
                <ul class="mt-8 flex flex-wrap gap-6">
                    @foreach ([['App Store', $iosQr], ['Google Play', $androidQr]] as [$label, $qr])
                        @if ($qr)
                            <li class="card items-center text-center">
                                <x-media-img :media="$qr" :alt="'Mã QR tải ứng dụng trên '.$label" width="160" height="160" class="size-40" sizes="160px" />
                                <span class="mt-3 font-semibold">{{ $label }}</span>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @endif
            </div>
            <x-art name="app-taskflow" width="720" height="960" class="mx-auto w-full max-w-60 rounded-xl lg:order-last" sizes="240px" />
        </div>
    </section>
@endif
