<?php

namespace App\Models;

use App\Models\Concerns\GeneratesSlug;
use App\Models\Concerns\HasBlameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use GeneratesSlug, HasBlameable, SoftDeletes;

    public const STATUSES = [
        'draft' => 'Nháp',
        'published' => 'Đã đăng',
    ];

    protected $fillable = [
        'title', 'slug', 'template', 'status', 'is_system',
        'seo_title', 'seo_description', 'seo_canonical', 'og_image_media_id',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    protected function slugSource(): string
    {
        return 'title';
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function ogImage(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'og_image_media_id');
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(PageBlock::class)->orderBy('sort_order');
    }

    public function enabledBlocks(): HasMany
    {
        return $this->blocks()->where('is_enabled', true);
    }
}
