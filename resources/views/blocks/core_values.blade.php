@php $cvId = 'coreValues'.$block->id; @endphp
<section class="about-section pt-70 pb-50">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="section-title text-center mb-40">
                    <h1>{{ $data['heading'] ?? 'Giá trị cốt lõi' }}</h1>
                </div>
                <div class="accordion" id="{{ $cvId }}">
                    @foreach ($data['items'] ?? [] as $i => $item)
                        <div class="single-faq">
                            <button class="w-100 text-start {{ $i ? 'collapsed' : '' }}" type="button"
                                data-bs-toggle="collapse" data-bs-target="#{{ $cvId }}-{{ $i }}"
                                aria-expanded="{{ $i ? 'false' : 'true' }}">
                                {{ $item['title'] ?? '' }}
                            </button>
                            <div id="{{ $cvId }}-{{ $i }}" class="collapse {{ $i ? '' : 'show' }}"
                                data-bs-parent="#{{ $cvId }}">
                                <div class="faq-content">{{ $item['description'] ?? '' }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
