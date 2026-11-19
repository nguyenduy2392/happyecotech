@php
    // Kênh liên hệ chính thức từ CMS (SiteSetting hoặc SocialLink). Mọi URL đi qua safe_url(); chỉ truy vấn 1 lần/request.
    app()->singletonIf('ui.channels', function () {
        $safe = fn ($u) => function_exists('safe_url') ? safe_url($u) : null;
        $social = \App\Models\SocialLink::query()->enabled()->get()->keyBy(fn ($s) => strtolower((string) $s->platform));
        $phone = setting('contact_phone');
        $tel = $phone ? preg_replace('/[^\d+]/', '', $phone) : null;

        return [
            'phone' => $phone,
            'tel' => $tel,
            'email' => setting('contact_email'),
            'address' => setting('contact_address'),
            // Hợp đồng dữ liệu công ty: CompanyProfile::url('contact_zalo' | 'social_facebook'). Null -> ẩn nút (không ghép SĐT, không dùng dòng social cũ).
            'zalo' => class_exists(\App\Support\CompanyProfile::class) ? $safe(\App\Support\CompanyProfile::url('contact_zalo')) : $safe(setting('contact_zalo')),
            'facebook' => class_exists(\App\Support\CompanyProfile::class) ? $safe(\App\Support\CompanyProfile::url('social_facebook')) : $safe(setting('social_facebook')),
            'others' => $social->reject(fn ($s, $k) => in_array($k, ['zalo', 'facebook'], true))->filter(fn ($s) => filled($safe($s->url)))->values(),
        ];
    });
@endphp
