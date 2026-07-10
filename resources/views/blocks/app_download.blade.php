@php
    $iosUrl = $data['ios_url'] ?? null;
    $androidUrl = $data['android_url'] ?? null;
@endphp
<section class="app-download-section" id="download-taskflow" aria-labelledby="app-download-heading">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <p class="app-download-kicker wow fadeInUp" data-wow-delay=".1s">{{ $data['kicker'] ?? '' }}</p>
                <h2 id="app-download-heading" class="wow fadeInUp" data-wow-delay=".2s">{{ $data['heading'] ?? '' }}</h2>
                <p class="app-download-lead wow fadeInUp" data-wow-delay=".3s">{{ $data['lead'] ?? '' }}</p>
            </div>
        </div>
        <div class="row justify-content-center g-4 mt-2">
            @if ($iosUrl)
                <div class="col-lg-5 col-md-6">
                    <div class="app-download-card wow fadeInUp" data-wow-delay=".25s">
                        <div class="app-download-qr-wrap">
                            <img src="{{ media_url($data['ios_qr_media_id'] ?? null, asset('assets/qr/ios.png')) }}"
                                width="200" height="200" alt="Mã QR tải ứng dụng trên App Store" loading="lazy" />
                        </div>
                        <div class="app-download-body">
                            <span class="app-download-badge"><i class="lni lni-apple" aria-hidden="true"></i> App Store</span>
                            <p class="app-download-desc">Dành cho iPhone &amp; iPad</p>
                            <a class="app-download-btn app-download-btn--ios" href="{{ $iosUrl }}" target="_blank"
                                rel="noopener noreferrer">
                                <span>Mở trên App Store</span>
                                <i class="lni lni-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
            @if ($androidUrl)
                <div class="col-lg-5 col-md-6">
                    <div class="app-download-card wow fadeInUp" data-wow-delay=".35s">
                        <div class="app-download-qr-wrap">
                            <img src="{{ media_url($data['android_qr_media_id'] ?? null, asset('assets/qr/google-play.png')) }}"
                                width="200" height="200" alt="Mã QR tải ứng dụng trên Google Play" loading="lazy" />
                        </div>
                        <div class="app-download-body">
                            <span class="app-download-badge"><i class="lni lni-play-store" aria-hidden="true"></i> Google Play</span>
                            <p class="app-download-desc">Dành cho Android</p>
                            <a class="app-download-btn app-download-btn--play" href="{{ $androidUrl }}" target="_blank"
                                rel="noopener noreferrer">
                                <span>Mở trên Google Play</span>
                                <i class="lni lni-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
