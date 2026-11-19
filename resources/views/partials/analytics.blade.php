@php
    $analytics = \App\Support\Analytics::config();
    $solutions = \App\Support\Analytics::SOLUTIONS;
    $flagship = \App\Support\Analytics::FLAGSHIP;
@endphp
@if ($analytics)
    {{-- Đo lường: chỉ render khi có GTM/GA4 ID hợp lệ trong Cài đặt. Consent Mode v2 mặc định "denied". Không chứa PII. --}}
    <script type="application/json" id="he-analytics">{!! json_encode($analytics + ['solutions' => $solutions, 'flagship' => $flagship], JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES) !!}</script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('consent', 'default', { ad_storage: 'denied', analytics_storage: 'denied', ad_user_data: 'denied', ad_personalization: 'denied', wait_for_update: 500 });
    </script>
    {{-- Banner cố định (không đẩy nội dung -> không CLS). Ẩn mặc định; JS chỉ hiện khi chưa có lựa chọn. --}}
    <div class="consent-banner fixed inset-x-2 bottom-20 z-50 rounded-2xl border border-ink-200 bg-white p-4 shadow-lg md:inset-x-auto md:bottom-4 md:left-4 md:max-w-md"
        data-consent-banner role="region" aria-labelledby="consent-title" hidden>
        <p id="consent-title" class="font-semibold">Cookie và đo lường</p>
        <p class="mt-1 text-sm text-ink-700">Chúng tôi dùng cookie phân tích để hiểu cách website được sử dụng và cải thiện nội dung. Bạn có thể từ chối; website vẫn hoạt động bình thường.</p>
        <div class="mt-3 flex flex-wrap gap-2">
            <button type="button" class="btn btn-primary" data-consent="granted">Đồng ý</button>
            <button type="button" class="btn btn-outline" data-consent="denied">Từ chối</button>
        </div>
    </div>
@endif
