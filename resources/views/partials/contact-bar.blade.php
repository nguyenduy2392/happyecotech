@include('partials.ui-channels')
@php $ch = app('ui.channels'); @endphp
@if ($ch['tel'] || $ch['zalo'])
    {{-- Thanh Gọi/Zalo cố định, chỉ trên mobile. Số và link Zalo lấy từ CMS (ui.channels). --}}
    <div class="contact-bar fixed inset-x-0 bottom-0 z-40 grid gap-2 border-t border-ink-100 bg-white p-2 md:hidden {{ $ch['tel'] && $ch['zalo'] ? 'grid-cols-2' : 'grid-cols-1' }}"
        role="region" aria-label="Liên hệ nhanh" data-cta-pos="contact-bar">
        @if ($ch['tel'])
            <a href="tel:{{ $ch['tel'] }}" class="btn btn-primary">
                <x-glyph name="phone" /> Gọi ngay
            </a>
        @endif
        @if ($ch['zalo'])
            <a href="{{ $ch['zalo'] }}" class="btn btn-zalo" target="_blank" rel="noopener noreferrer" aria-label="Chat Zalo Happy Ecotech">
                <x-glyph name="message" /> Chat Zalo
            </a>
        @endif
    </div>
@endif
