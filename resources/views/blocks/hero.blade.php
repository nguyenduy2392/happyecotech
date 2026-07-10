<section id="home" class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1 class="wow fadeInUp" data-wow-delay=".4s">{{ $data['heading'] ?? '' }}</h1>
                    <p class="wow fadeInUp" data-wow-delay=".6s">{{ $data['subheading'] ?? '' }}</p>
                    @if (! empty($data['cta_text']))
                        <a href="{{ $data['cta_url'] ?? route('contact') }}" class="main-btn btn-hover wow fadeInUp"
                            data-wow-delay=".6s">{{ $data['cta_text'] }}</a>
                    @endif
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-img wow fadeInUp" data-wow-delay=".5s">
                    <img src="{{ media_url($data['image_media_id'] ?? null, asset('assets/img/hero/hero-1.png')) }}"
                        alt="{{ $data['heading'] ?? 'Happy Ecotech' }}" style="width: 100%;">
                </div>
            </div>
        </div>
    </div>
</section>
