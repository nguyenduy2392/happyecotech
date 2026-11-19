@props([
    'id' => 'lead-form',
    'source' => 'Biểu mẫu tư vấn nhanh',
    'serviceId' => null,
    'tone' => 'light',
])
{{-- Form lead ngắn: Họ tên, SĐT, Công ty, Giải pháp. POST về route contact.submit (backend hiện có).
     "message" là trường bắt buộc của request nên gửi kèm giá trị ẩn mô tả nguồn lead. --}}
@php
    $services = \App\Models\Service::query()->published()->ordered()->get(['id', 'name']);
    $selected = old('service_id', $serviceId);
@endphp
<form method="POST" action="{{ route('contact.submit') }}" class="grid gap-4" novalidate aria-labelledby="{{ $id }}-title">
    @csrf
    <input type="hidden" name="message" value="{{ $source }}{{ $serviceId ? ' (trang giải pháp)' : '' }}" />
    <div class="absolute -left-[9999px] h-0 w-0 overflow-hidden" aria-hidden="true">
        <label for="{{ $id }}-website">Không điền vào ô này</label>
        <input type="text" id="{{ $id }}-website" name="website" tabindex="-1" autocomplete="off" />
    </div>

    <div class="field">
        <label for="{{ $id }}-name">Họ và tên</label>
        <input class="input" type="text" id="{{ $id }}-name" name="name" value="{{ old('name') }}" autocomplete="name" required
            @error('name') aria-invalid="true" aria-describedby="{{ $id }}-name-err" @enderror />
        @error('name') <p id="{{ $id }}-name-err" class="field-error">{{ $message }}</p> @enderror
    </div>

    <div class="field">
        <label for="{{ $id }}-phone">Số điện thoại</label>
        <input class="input" type="tel" inputmode="tel" id="{{ $id }}-phone" name="phone" value="{{ old('phone') }}" autocomplete="tel" required
            @error('phone') aria-invalid="true" aria-describedby="{{ $id }}-phone-err" @enderror />
        @error('phone') <p id="{{ $id }}-phone-err" class="field-error">{{ $message }}</p> @enderror
    </div>

    <div class="field">
        <label for="{{ $id }}-company">Công ty / Đơn vị <span class="font-normal text-ink-500">(không bắt buộc)</span></label>
        <input class="input" type="text" id="{{ $id }}-company" name="company" value="{{ old('company') }}" autocomplete="organization" />
    </div>

    <div class="field">
        <label for="{{ $id }}-service">Giải pháp quan tâm <span class="font-normal text-ink-500">(không bắt buộc)</span></label>
        <select class="input" id="{{ $id }}-service" name="service_id">
            <option value="">Chưa chọn, cần tư vấn thêm</option>
            @foreach ($services as $svc)
                <option value="{{ $svc->id }}" @selected((string) $selected === (string) $svc->id)>{{ $svc->name }}</option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn btn-primary btn-lg w-full">Nhận tư vấn</button>
    <p class="field-hint {{ $tone === 'dark' ? 'text-ink-300' : '' }}">Chúng tôi chỉ dùng thông tin này để liên hệ tư vấn cho bạn.</p>
</form>
