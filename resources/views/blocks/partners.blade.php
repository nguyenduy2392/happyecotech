@if (! empty($data['logos']))
    <section class="client-logo-section pt-150">
        <div class="container">
            <div class="row">
                @foreach ($data['logos'] as $logoId)
                    <div class="col-lg-3 col-sm-6">
                        <div class="single-logo text-center">
                            <img src="{{ media_url($logoId) }}" alt="">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
