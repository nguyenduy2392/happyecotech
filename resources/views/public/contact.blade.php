@extends('layouts.public')

@section('content')
    @include('partials.page-hero', [
        'title' => 'Liên hệ',
        'subtitle' => 'Chúng tôi luôn sẵn sàng hỗ trợ bạn.',
        'breadcrumbs' => [['label' => 'Liên hệ']],
    ])

    <section class="contact-section pt-80 pb-80">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-5">
                    <div class="contact-info">
                        <h3 class="mb-25">Thông tin liên hệ</h3>
                        @if (setting('company_name'))
                            <p class="company"><strong>{{ setting('company_name') }}</strong></p>
                        @endif
                        @if (setting('contact_address'))
                            <p><i class="lni lni-map-marker"></i> {{ setting('contact_address') }}</p>
                        @endif
                        @if (setting('contact_phone'))
                            <p><i class="lni lni-phone"></i>
                                <a href="tel:{{ preg_replace('/\s+/', '', setting('contact_phone')) }}">{{ setting('contact_phone') }}</a></p>
                        @endif
                        @if (setting('contact_email'))
                            <p><i class="lni lni-envelope"></i>
                                <a href="mailto:{{ setting('contact_email') }}">{{ setting('contact_email') }}</a></p>
                        @endif
                        @if (setting('working_hours'))
                            <p><i class="lni lni-alarm-clock"></i> {{ setting('working_hours') }}</p>
                        @endif

                        @if (setting('map_embed_url'))
                            <div class="contact-map mt-25">
                                <iframe src="{{ setting('map_embed_url') }}" title="Bản đồ" loading="lazy"></iframe>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="contact-form-wrap" id="contact-form">
                        <h3 class="mb-25">Gửi yêu cầu tư vấn</h3>

                        @if (session('contact_success'))
                            <div class="alert alert-success" role="alert">
                                Cảm ơn bạn! Chúng tôi đã nhận được thông tin và sẽ liên hệ trong thời gian sớm nhất.
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                Vui lòng kiểm tra lại các trường được đánh dấu bên dưới.
                            </div>
                        @endif

                        <form method="POST" action="{{ route('contact.submit') }}" novalidate>
                            @csrf

                            {{-- Honeypot chống spam --}}
                            <div class="hp-field" aria-hidden="true">
                                <label>Để trống ô này
                                    <input type="text" name="website" tabindex="-1" autocomplete="off" value="">
                                </label>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="name">Họ tên <span class="req">*</span></label>
                                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                                        class="form-control @error('name') is-invalid @enderror" required>
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="phone">Số điện thoại <span class="req">*</span></label>
                                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                                        class="form-control @error('phone') is-invalid @enderror" required>
                                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="email">Email</label>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                                        class="form-control @error('email') is-invalid @enderror">
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="company">Công ty</label>
                                    <input type="text" id="company" name="company" value="{{ old('company') }}"
                                        class="form-control">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="service_id">Dịch vụ quan tâm</label>
                                    <select id="service_id" name="service_id" class="form-select">
                                        <option value="">— Chọn dịch vụ —</option>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->id }}" @selected(old('service_id') == $service->id)>
                                                {{ $service->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="message">Nội dung <span class="req">*</span></label>
                                    <textarea id="message" name="message" rows="5"
                                        class="form-control @error('message') is-invalid @enderror" required>{{ old('message') }}</textarea>
                                    @error('message') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input @error('consent') is-invalid @enderror" type="checkbox"
                                            id="consent" name="consent" value="1" @checked(old('consent'))>
                                        <label class="form-check-label" for="consent">
                                            Tôi đồng ý để Happy Ecotech liên hệ và xử lý thông tin của tôi.
                                        </label>
                                        @error('consent') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="main-btn btn-hover">Gửi yêu cầu</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
