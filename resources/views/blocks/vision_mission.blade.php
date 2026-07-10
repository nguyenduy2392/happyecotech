@php $accId = 'valuesAcc'.$block->id; @endphp
<section id="about" class="about-section pt-150">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="about-img mb-50">
                    <img src="{{ asset('assets/img/about/about-img.svg') }}" alt="Giới thiệu" class="img-fluid">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-content mb-50">
                    <div class="section-title">
                        <h1 class="mb-25">{{ $data['heading'] ?? 'Tầm nhìn – Sứ mệnh' }}</h1>
                        <p>
                            @if (! empty($data['vision']))
                                Tầm nhìn: {{ $data['vision'] }}<br />
                            @endif
                            @if (! empty($data['mission']))
                                Sứ mệnh: {{ $data['mission'] }}
                            @endif
                        </p>
                    </div>

                    @if (! empty($data['values']))
                        <div class="accordion pb-15" id="{{ $accId }}">
                            @foreach ($data['values'] as $i => $item)
                                <div class="single-faq">
                                    <button class="w-100 text-start {{ $i ? 'collapsed' : '' }}" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#{{ $accId }}-{{ $i }}"
                                        aria-expanded="{{ $i ? 'false' : 'true' }}">
                                        {{ $item['title'] ?? '' }}
                                    </button>
                                    <div id="{{ $accId }}-{{ $i }}" class="collapse {{ $i ? '' : 'show' }}"
                                        data-bs-parent="#{{ $accId }}">
                                        <div class="faq-content">{{ $item['description'] ?? '' }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
