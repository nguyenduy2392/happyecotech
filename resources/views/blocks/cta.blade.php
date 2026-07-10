<section class="cta-section img-bg pt-110 pb-60">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xl-6 col-lg-7">
                <div class="section-title mb-50">
                    <h1 class="mb-20 wow fadeInUp" data-wow-delay=".2s">{{ $data['heading'] ?? '' }}</h1>
                    <p class="wow fadeInUp" data-wow-delay=".4s">{{ $data['subheading'] ?? '' }}</p>
                </div>
            </div>
            <div class="col-xl-6 col-lg-5">
                <div class="cta-btn text-lg-end mb-50">
                    <a href="{{ $data['cta_url'] ?? route('contact') }}"
                        class="main-btn btn-hover text-uppercase">{{ $data['cta_text'] ?? 'Liên hệ với chúng tôi' }}</a>
                </div>
            </div>
        </div>
    </div>
</section>
