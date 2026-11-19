@php
    // Tagline: câu lợi ích cốt lõi (≥2 dòng) lấy từ CMS (data.text → sứ mệnh trong block vision_mission → tagline cài đặt). Không bịa nội dung.
    $text = trim((string) ($data['text'] ?? setting('tagline', '')));
    $words = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
@endphp
@if (count($words) >= 6)
    <section class="section" aria-label="Điều chúng tôi theo đuổi">
        <div class="container-page">
            <p class="tagline-text">@foreach ($words as $w)<span class="tw">{{ $w }}</span>{{ $loop->last ? '' : ' ' }}@endforeach</p>
        </div>
    </section>
@endif
