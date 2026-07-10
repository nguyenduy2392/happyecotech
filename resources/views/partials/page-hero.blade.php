<section class="page-hero">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h1>{{ $title }}</h1>
                @if (! empty($subtitle))
                    <p>{{ $subtitle }}</p>
                @endif
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                        @foreach ($breadcrumbs ?? [] as $crumb)
                            @if (! empty($crumb['url']))
                                <li class="breadcrumb-item"><a href="{{ $crumb['url'] }}">{{ $crumb['label'] }}</a></li>
                            @else
                                <li class="breadcrumb-item active" aria-current="page">{{ $crumb['label'] }}</li>
                            @endif
                        @endforeach
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>
