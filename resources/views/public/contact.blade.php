@extends('layouts.public')

@include('partials.ui-channels')
@php
    $ch = app('ui.channels');
    $safe = fn ($u) => function_exists('safe_url') ? safe_url($u) : null; // R2-01: mọi URL từ CMS đi qua safe_url()
    $phone = setting('contact_phone');
    $tel = $phone ? preg_replace('/[^\d+]/', '', $phone) : null;
    $email = setting('contact_email');
    $address = setting('contact_address');
    $hours = setting('working_hours');
    $mapEmbed = $safe(setting('map_embed_url'));
@endphp

@section('content')
    @include('partials.page-hero', [
        'title' => 'Liên hệ với Happy Ecotech',
        'lead' => 'Mô tả ngắn nhu cầu của bạn, chúng tôi sẽ liên hệ tư vấn giải pháp phù hợp.',
        'crumbs' => [['label' => 'Liên hệ']],
    ])

    <section class="section">
        <div class="container-page grid gap-12 lg:grid-cols-[1.3fr_1fr] lg:gap-16">
            <div id="contact-form" class="scroll-mt-24">
                <h2 class="text-h2">Gửi yêu cầu tư vấn</h2>

                @if (session('contact_success'))
                    <div role="status" class="mt-6 rounded-lg border border-brand-200 bg-brand-50 p-5 text-brand-900">
                        <p class="font-semibold">Cảm ơn bạn. Chúng tôi đã nhận được yêu cầu.</p>
                        <p class="mt-1">Đội ngũ Happy Ecotech sẽ liên hệ lại với bạn sớm nhất có thể.</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div role="alert" class="mt-6 rounded-lg border border-danger-600 bg-danger-50 p-5 text-danger-600">
                        <p class="font-semibold">Vui lòng kiểm tra lại {{ $errors->count() }} trường chưa hợp lệ.</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('contact.submit') }}" class="mt-8 grid gap-5" novalidate data-ajax-form @if (\Illuminate\Support\Facades\Route::has('csrf.token')) data-csrf-endpoint="{{ route('csrf.token', [], false) }}" @endif>
                    <div data-form-status tabindex="-1" class="hidden" aria-live="polite"></div>
                    @csrf
                    <div class="absolute -left-[9999px] h-0 w-0 overflow-hidden" aria-hidden="true">
                        <label for="c-website">Không điền vào ô này</label>
                        <input type="text" id="c-website" name="website" tabindex="-1" autocomplete="off" />
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="field">
                            <label for="c-name">Họ và tên</label>
                            <input class="input" id="c-name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required
                                @error('name') aria-invalid="true" aria-describedby="c-name-err" @enderror />
                            @error('name') <p id="c-name-err" class="field-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="field">
                            <label for="c-phone">Số điện thoại</label>
                            <input class="input" id="c-phone" name="phone" type="tel" inputmode="tel" value="{{ old('phone') }}" autocomplete="tel" required
                                @error('phone') aria-invalid="true" aria-describedby="c-phone-err" @enderror />
                            @error('phone') <p id="c-phone-err" class="field-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="field">
                            <label for="c-email">Email <span class="font-normal text-ink-500">(không bắt buộc)</span></label>
                            <input class="input" id="c-email" name="email" type="email" value="{{ old('email') }}" autocomplete="email"
                                @error('email') aria-invalid="true" aria-describedby="c-email-err" @enderror />
                            @error('email') <p id="c-email-err" class="field-error">{{ $message }}</p> @enderror
                        </div>
                        <div class="field">
                            <label for="c-company">Công ty / Đơn vị <span class="font-normal text-ink-500">(không bắt buộc)</span></label>
                            <input class="input" id="c-company" name="company" type="text" value="{{ old('company') }}" autocomplete="organization" />
                        </div>
                    </div>

                    @php
                        // selectedSolution do controller (slug hợp lệ). Giải pháp chưa có trang/lựa chọn trong select vẫn giữ slug.
                        $solSlug = $selectedSolution ?? null;
                        $solMeta = $solSlug ? ((require resource_path('views/public/services/meta.php'))[$solSlug] ?? null) : null;
                        $solHasService = $solSlug && $services->contains('slug', $solSlug);
                    @endphp
                    @if ($solSlug && ! $solHasService)
                        <input type="hidden" name="solution" value="{{ $solSlug }}" data-solution />
                        <p class="rounded-lg bg-brand-50 p-3 text-sm" data-solution-note>Giải pháp quan tâm: <strong>{{ $solMeta['name'] ?? $solSlug }}</strong></p>
                    @endif
                    <div class="field">
                        <label for="c-service">Giải pháp quan tâm <span class="font-normal text-ink-500">(không bắt buộc)</span></label>
                        <select class="input" id="c-service" name="service_id" data-solution-select>
                            <option value="">Chưa chọn, cần tư vấn thêm</option>
                            @foreach ($services as $svc)
                                <option value="{{ $svc->id }}" data-slug="{{ $svc->slug }}" @selected((string) old('service_id', request('service')) === (string) $svc->id || (! old('service_id') && ($selectedSolution ?? null) === $svc->slug))>{{ $svc->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label for="c-message">Nội dung cần tư vấn</label>
                        <textarea class="input" id="c-message" name="message" rows="5" required
                            @error('message') aria-invalid="true" aria-describedby="c-message-err" @enderror>{{ old('message') }}</textarea>
                        @error('message') <p id="c-message-err" class="field-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label class="flex items-start gap-3 font-normal">
                            <input type="checkbox" name="consent" value="1" class="mt-1 size-5 accent-brand-600" @checked(old('consent'))
                                @error('consent') aria-invalid="true" aria-describedby="c-consent-err" @enderror />
                            <span>Tôi đồng ý để Happy Ecotech lưu và sử dụng thông tin này nhằm liên hệ tư vấn.</span>
                        </label>
                        @error('consent') <p id="c-consent-err" class="field-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary btn-lg">Gửi yêu cầu</button>
                    </div>
                </form>
            </div>

            <aside aria-labelledby="contact-info-title">
                <h2 id="contact-info-title" class="text-h2">Thông tin liên hệ</h2>
                <ul class="mt-6 space-y-5">
                    @if ($phone)
                        <li class="flex gap-4"><x-glyph name="phone" :size="22" class="mt-1 text-brand-600" />
                            <span><span class="block text-sm text-ink-500">Hotline</span><a href="tel:{{ $tel }}" class="font-semibold">{{ $phone }}</a></span></li>
                    @endif
                    @if ($ch['zalo'])
                        <li class="flex gap-4"><x-glyph name="message" :size="22" class="mt-1 text-brand-600" />
                            <span><span class="block text-sm text-ink-500">Zalo</span><a href="{{ $ch['zalo'] }}" class="font-semibold" target="_blank" rel="noopener noreferrer" aria-label="Zalo Happy Ecotech">Nhắn tin Zalo Happy Ecotech</a></span></li>
                    @endif
                    @if ($ch['facebook'])
                        <li class="flex gap-4"><x-glyph name="facebook" :size="22" class="mt-1 text-brand-600" />
                            <span><span class="block text-sm text-ink-500">Facebook</span><a href="{{ $ch['facebook'] }}" class="font-semibold" target="_blank" rel="noopener noreferrer" aria-label="Facebook Happy Ecotech">Trang Facebook Happy Ecotech</a></span></li>
                    @endif
                    @if ($email)
                        <li class="flex gap-4"><x-glyph name="mail" :size="22" class="mt-1 text-brand-600" />
                            <span><span class="block text-sm text-ink-500">Email</span><a href="mailto:{{ $email }}" class="break-all font-semibold">{{ $email }}</a></span></li>
                    @endif
                    @if ($address)
                        <li class="flex gap-4"><x-glyph name="map-pin" :size="22" class="mt-1 text-brand-600" />
                            <span><span class="block text-sm text-ink-500">Địa chỉ</span><span class="font-semibold">{{ $address }}</span></span></li>
                    @endif
                    @if ($hours)
                        <li class="flex gap-4"><x-glyph name="clock" :size="22" class="mt-1 text-brand-600" />
                            <span><span class="block text-sm text-ink-500">Giờ làm việc</span><span class="font-semibold">{{ $hours }}</span></span></li>
                    @endif
                </ul>
                @if ($mapEmbed)
                    <iframe src="{{ $mapEmbed }}" title="Bản đồ văn phòng" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                        class="mt-8 h-72 w-full rounded-lg border border-ink-100"></iframe>
                @endif
            </aside>
        </div>
    </section>
@endsection
