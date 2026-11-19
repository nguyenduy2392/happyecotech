@php
    $safe = fn ($u) => function_exists('safe_url') ? safe_url($u) : null; // R2-01: mọi URL từ CMS đi qua safe_url()
    $phone = setting('contact_phone');
    $tel = $phone ? preg_replace('/[^\d+]/', '', $phone) : null;
    // Form chỉ hiện khi CMS bật rõ ràng (show_form = true). Nút tuỳ chỉnh hiện khi có cta_text/cta_url (CTA cũ).
    $showForm = filter_var($data['show_form'] ?? false, FILTER_VALIDATE_BOOLEAN);
    $ctaUrl = $safe($data['cta_url'] ?? null);
    $ctaText = $data['cta_text'] ?? null;
    $hasButton = filled($ctaUrl) || filled($ctaText); // cta_url không an toàn -> về form liên hệ
@endphp
<section class="section section-ink" data-cta-pos="cta-block" aria-labelledby="cta-title" id="lien-he-nhanh">
    <div class="container-page grid items-start gap-10 {{ $showForm ? 'lg:grid-cols-2' : '' }} lg:gap-16">
        <div>
            <h2 id="cta-title" class="text-h2">{{ $data['heading'] ?? 'Nhận tư vấn giải pháp phù hợp' }}</h2>
            @if (! empty($data['subheading']))
                <p class="mt-4 max-w-xl text-lead text-ink-300">{{ $data['subheading'] }}</p>
            @endif
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                @if ($hasButton)
                    <a href="{{ $ctaUrl ?: route('contact').'#contact-form' }}" data-cta="cta-block" class="btn btn-light btn-lg">{{ $ctaText ?: 'Liên hệ với chúng tôi' }}</a>
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
