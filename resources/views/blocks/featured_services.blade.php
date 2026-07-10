<section id="service" class="service-section img-bg pt-100 pb-100 mt-150">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xxl-5 col-xl-6 col-lg-7 col-md-10">
                <div class="section-title text-center mb-50">
                    <h1>{{ $data['heading'] ?? 'Dịch vụ' }}</h1>
                    <p>{{ $data['subheading'] ?? '' }}</p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            @forelse (($featuredServices ?? collect()) as $service)
                <div class="col-xl-3 col-md-6 d-flex">
                    <a href="{{ route('services.show', $service) }}"
                        class="single-service h-100 d-block text-decoration-none service-card-link">
                        <div class="icon">
                            <img src="{{ $service->icon?->url ?? asset('assets/img/project-icon/task-flow.png') }}"
                                alt="{{ $service->name }}"
                                onerror="this.src='{{ asset('assets/img/project-icon/task-flow.png') }}'">
                        </div>
                        <div class="content">
                            <h3>{{ $service->name }}</h3>
                            <p>{{ \Illuminate\Support\Str::limit($service->short_description, 120) }}</p>
                            <span class="service-more">Xem chi tiết <i class="lni lni-arrow-right"></i></span>
                        </div>
                    </a>
                </div>
            @empty
                <div class="col-12 text-center"><p>Chưa có dịch vụ nào được hiển thị.</p></div>
            @endforelse
        </div>

        <div class="view-all-btn text-center pt-30">
            <a href="{{ route('services.index') }}" class="main-btn btn-hover">Xem tất cả dịch vụ</a>
        </div>
    </div>
</section>
