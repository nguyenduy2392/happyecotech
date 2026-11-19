@php
    $phone = setting('contact_phone');
    $tel = $phone ? preg_replace('/[^\d+]/', '', $phone) : null;
@endphp
@if ($tel)
    {{-- Thanh Gọi/Zalo cố định, chỉ trên mobile. Số lấy từ SiteSetting contact_phone. --}}
    <div class="contact-bar fixed inset-x-0 bottom-0 z-40 grid grid-cols-2 gap-2 border-t border-ink-100 bg-white p-2 md:hidden"
        role="region" aria-label="Liên hệ nhanh" data-cta-pos="contact-bar">
        <a href="tel:{{ $tel }}" class="btn btn-primary">
            <x-glyph name="phone" /> Gọi ngay
        </a>
        <a href="https://zalo.me/{{ $tel }}" class="btn btn-zalo" target="_blank" rel="noopener noreferrer">
            <x-glyph name="message" /> Chat Zalo
        </a>
    </div>
@endif
