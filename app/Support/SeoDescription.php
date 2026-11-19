<?php

namespace App\Support;

use Illuminate\Support\Str;

class SeoDescription
{
    public static function forPage(?string $candidate, ?string $title, bool $notFound = false, int $page = 1): string
    {
        $description = self::clean($candidate);
        if (mb_strlen($description) < 120) {
            $description = self::fallback(self::clean($title), $notFound);
        }

        $suffix = $page > 1 ? " Trang {$page}." : '';
        $limit = 155 - mb_strlen($suffix);
        if (mb_strlen($description) > $limit) {
            $prefix = rtrim(mb_substr($description, 0, $limit - 1));
            $lastSpace = mb_strrpos($prefix, ' ');
            $description = ($lastSpace !== false && $lastSpace >= $limit - 12 ? mb_substr($prefix, 0, $lastSpace) : $prefix).'…';
        }

        return $description.$suffix;
    }

    private static function clean(?string $text): string
    {
        return Str::squish(html_entity_decode(strip_tags($text ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    private static function fallback(string $title, bool $notFound): string
    {
        if ($notFound) {
            return 'Trang bạn tìm có thể đã được chuyển hoặc không còn tồn tại. Hãy về trang chủ để tìm giải pháp, đọc tin tức hoặc liên hệ với Happy Ecotech.';
        }

        $subject = Str::limit($title ?: 'Happy Ecotech', 42, '…', preserveWords: true);

        return match (request()->route()?->getName()) {
            'home' => 'Happy Ecotech phát triển giải pháp truy xuất nguồn gốc và công cụ số giúp doanh nghiệp quản lý công việc, khách hàng và vận hành hằng ngày.',
            'about' => 'Tìm hiểu tầm nhìn, sứ mệnh và những giá trị định hướng cách Happy Ecotech phát triển sản phẩm, phục vụ khách hàng và đồng hành cùng doanh nghiệp.',
            'contact' => 'Trao đổi với Happy Ecotech về nhu cầu triển khai, phạm vi sử dụng hoặc yêu cầu hỗ trợ. Xem số điện thoại, địa chỉ và gửi thông tin qua biểu mẫu.',
            'team' => 'Gặp gỡ đội ngũ Happy Ecotech và tìm hiểu những người tham gia phát triển giải pháp, tư vấn triển khai và hỗ trợ doanh nghiệp trong quá trình sử dụng.',
            'posts.index' => 'Đọc tin tức từ Happy Ecotech về truy xuất nguồn gốc, ứng dụng công nghệ trong vận hành và những cập nhật mới về sản phẩm, hoạt động của đội ngũ.',
            'posts.category' => "Xem các bài viết thuộc danh mục «{$subject}». Khám phá thông tin và các bài liên quan theo chủ đề để tìm nhanh nội dung bạn đang quan tâm.",
            'posts.show' => "Đọc bài viết «{$subject}» trên Happy Ecotech. Theo dõi nội dung chính và tìm thêm những bài viết liên quan để hiểu rõ hơn về chủ đề.",
            'services.index' => 'Khám phá các giải pháp của Happy Ecotech cho truy xuất nguồn gốc, quản lý công việc và chăm sóc khách hàng. Tìm công cụ phù hợp với nhu cầu của bạn.',
            'services.show' => "Tìm hiểu giải pháp {$subject}, cách sử dụng và những nhu cầu giải pháp có thể hỗ trợ. Liên hệ Happy Ecotech để trao đổi về phạm vi triển khai.",
            default => "Xem thông tin về «{$subject}» trên website Happy Ecotech. Tìm hiểu nội dung của trang và cách liên hệ với đội ngũ khi bạn cần hỗ trợ.",
        };
    }
}
