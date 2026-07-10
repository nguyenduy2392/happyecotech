<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Route;

class MenuItem extends Model
{
    public const LINK_TYPES = [
        'url' => 'URL tuỳ ý',
        'route' => 'Route nội bộ',
        'page' => 'Trang tĩnh',
        'service' => 'Dịch vụ',
        'post' => 'Bài viết',
        'category' => 'Danh mục',
    ];

    protected $fillable = [
        'menu_id', 'parent_id', 'label', 'link_type', 'url', 'route_name',
        'reference_id', 'target_blank', 'sort_order', 'is_enabled',
    ];

    protected $casts = [
        'target_blank' => 'boolean',
        'is_enabled' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Giải quyết URL cuối cùng dựa trên loại liên kết.
     */
    public function resolveUrl(): string
    {
        return match ($this->link_type) {
            'route' => $this->route_name && Route::has($this->route_name)
                ? route($this->route_name)
                : ($this->url ?: '#'),
            'service' => optional(Service::find($this->reference_id))->slug
                ? url('/dich-vu/'.Service::find($this->reference_id)->slug)
                : ($this->url ?: '#'),
            'post' => optional(Post::find($this->reference_id))->slug
                ? url('/tin-tuc/'.Post::find($this->reference_id)->slug)
                : ($this->url ?: '#'),
            'category' => optional(PostCategory::find($this->reference_id))->slug
                ? url('/tin-tuc/danh-muc/'.PostCategory::find($this->reference_id)->slug)
                : ($this->url ?: '#'),
            'page' => optional(Page::find($this->reference_id))->slug
                ? url('/'.Page::find($this->reference_id)->slug)
                : ($this->url ?: '#'),
            default => $this->url ?: '#',
        };
    }
}
