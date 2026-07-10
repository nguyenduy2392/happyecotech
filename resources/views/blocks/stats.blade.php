<section class="counter-up-section pt-150">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="counter-up-content mb-50">
                    <div class="section-title mb-40">
                        <h1 class="mb-20 wow fadeInUp" data-wow-delay=".2s">{{ $data['heading'] ?? '' }}</h1>
                    </div>
                    <div class="counter-up-wrapper">
                        <div class="row">
                            @foreach ($data['items'] ?? [] as $i => $item)
                                <div class="col-lg-6 col-sm-6">
                                    <div class="single-counter">
                                        <div class="icon color-{{ ($i % 4) + 1 }}">
                                            <i class="{{ $item['icon'] ?? 'lni lni-checkmark' }}"></i>
                                        </div>
                                        <div class="content">
                                            <h1 class="countup" cup-end="{{ $item['value'] ?? 0 }}"
                                                cup-append="{{ $item['suffix'] ?? ' ' }}">{{ $item['value'] ?? 0 }}</h1>
                                            <span>{{ $item['label'] ?? '' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-6">
                <div class="counter-up-img mb-50">
                    <img src="{{ asset('assets/img/counter-up/counter-up-img.svg') }}" alt="">
                </div>
            </div>
        </div>
    </div>
</section>
