@php
    $phone = setting('contact_phone');
    $tel = $phone ? preg_replace('/[^\d+]/', '', $phone) : null;
    $showForm = $data['show_form'] ?? true;
@endphp
<section class="section section-ink" aria-labelledby="cta-title" id="lien-he-nhanh">
    <div class="container-page grid items-start gap-10 lg:grid-cols-2 lg:gap-16">
        <div>
            <h2 id="cta-title" class="text-h2">{{ $data['heading'] ?? 'Nhận tư vấn giải pháp phù hợp' }}</h2>
            @if (! empty($data['subheading']))
                <p class="mt-4 max-w-xl text-lead text-ink-300">{{ $data['subheading'] }}</p>
            @endif
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                @if (! $showForm)
                    <a href="{{ $data['cta_url'] ?? route('contact').'#contact-form' }}" class="btn btn-light btn-lg">{{ $data['cta_text'] ?? 'Liên hệ với chúng tôi' }}</a>
                @endif
                @if ($tel)
                    <a href="tel:{{ $tel }}" class="btn btn-light btn-lg"><x-glyph name="phone" /> {{ $phone }}</a>
                    <a href="https://zalo.me/{{ $tel }}" class="btn btn-zalo btn-lg" target="_blank" rel="noopener noreferrer"><x-glyph name="message" /> Chat Zalo</a>
                @endif
            </div>
        </div>
        @if ($showForm)
            <div class="rounded-xl bg-white p-6 text-ink-900 md:p-8">
                <h3 id="cta-form-title" class="mb-5 text-h3">Để lại thông tin, chúng tôi gọi lại</h3>
                <x-lead-form id="cta-form" />
            </div>
        @endif
    </div>
</section>
