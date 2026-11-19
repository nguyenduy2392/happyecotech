<section class="section">
    <div class="container-page">
        @if (! empty($data['title']))
            <h2 class="mb-8 max-w-3xl text-h2">{{ $data['title'] }}</h2>
        @endif
        <div class="prose-he">
            {!! \Mews\Purifier\Facades\Purifier::clean($data['html'] ?? '') !!}
        </div>
    </div>
</section>
