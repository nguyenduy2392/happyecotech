@extends('layouts.public')

@section('content')
    @include('partials.page-hero', [
        'title' => 'Dịch vụ',
        'subtitle' => 'Hệ sinh thái công nghệ minh bạch dữ liệu của Happy Ecotech.',
        'breadcrumbs' => [['label' => 'Dịch vụ']],
    ])

    <section class="service-list-section pt-100 pb-100">
        <div class="container">
            <div class="row g-4">
                @forelse ($services as $service)
                    <div class="col-lg-3 col-md-6 d-flex">
                        <a href="{{ route('services.show', $service) }}"
                            class="single-service h-100 d-block text-decoration-none service-card-link">
                            <div class="icon">
                                <img src="{{ $service->icon?->url ?? asset('assets/img/project-icon/task-flow.png') }}"
                                    alt="{{ $service->name }}"
                                    onerror="this.src='{{ asset('assets/img/project-icon/task-flow.png') }}'">
                            </div>
                            <div class="content">
                                <h3>{{ $service->name }}</h3>
                                <p>{{ \Illuminate\Support\Str::limit($service->short_description, 130) }}</p>
                                <span class="service-more">Xem chi tiết <i class="lni lni-arrow-right"></i></span>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12 text-center"><p>Dịch vụ đang được cập nhật.</p></div>
                @endforelse
            </div>

            <div class="text-center pt-50">
                <a href="{{ route('contact') }}#contact-form" class="main-btn btn-hover">Liên hệ tư vấn</a>
            </div>
        </div>
    </section>
@endsection
