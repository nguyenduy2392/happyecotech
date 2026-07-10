<section class="richtext-section pt-100 pb-50">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                @if (! empty($data['title']))
                    <div class="section-title mb-30">
                        <h1>{{ $data['title'] }}</h1>
                    </div>
                @endif
                <div class="richtext-content">
                    {!! \Mews\Purifier\Facades\Purifier::clean($data['html'] ?? '') !!}
                </div>
            </div>
        </div>
    </div>
</section>
