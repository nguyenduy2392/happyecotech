<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageBlock extends Model
{
    /**
     * Các loại block hỗ trợ cho trang chủ và trang tĩnh.
     */
    public const TYPES = [
        'hero' => 'Hero',
        'partners' => 'Đối tác / Logo',
        'about' => 'Giới thiệu',
        'vision_mission' => 'Tầm nhìn / Sứ mệnh',
        'core_values' => 'Giá trị cốt lõi',
        'featured_services' => 'Dịch vụ nổi bật',
        'stats' => 'Số liệu nổi bật',
        'why_choose' => 'Lý do lựa chọn',
        'cta' => 'CTA liên hệ',
        'app_download' => 'Tải ứng dụng TaskFlow',
        'latest_news' => 'Tin tức mới nhất',
        'richtext' => 'Nội dung rich text',
    ];

    protected $fillable = [
        'page_id', 'block_type', 'title', 'data', 'sort_order', 'is_enabled',
    ];

    protected $casts = [
        'data' => 'array',
        'sort_order' => 'integer',
        'is_enabled' => 'boolean',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->block_type] ?? $this->block_type;
    }
}
