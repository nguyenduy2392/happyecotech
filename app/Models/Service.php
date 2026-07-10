<?php

namespace App\Models;

use App\Models\Concerns\GeneratesSlug;
use App\Models\Concerns\HasBlameable;
use App\Models\Concerns\Orderable;
use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mews\Purifier\Facades\Purifier;

class Service extends Model
{
    use GeneratesSlug, HasBlameable, HasFactory, Orderable, Publishable, SoftDeletes;

    public const STATUSES = [
        'draft' => 'Nháp',
        'published' => 'Đã đăng',
        'archived' => 'Lưu trữ',
    ];

    protected $fillable = [
        'name', 'slug', 'short_description', 'content',
        'icon_media_id', 'cover_media_id', 'og_image_media_id',
        'external_url', 'app_store_url', 'google_play_url',
        'cta_text', 'cta_url', 'status', 'is_featured', 'sort_order',
        'published_at', 'seo_title', 'seo_description', 'seo_canonical',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'published_at' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Sanitize HTML rich-text chống XSS khi lưu. */
    protected function content(): Attribute
    {
        return Attribute::make(set: fn (?string $value) => filled($value) ? Purifier::clean($value) : $value);
    }

    public function features(): HasMany
    {
        return $this->hasMany(ServiceFeature::class)->orderBy('sort_order');
    }

    public function gallery(): BelongsToMany
    {
        return $this->belongsToMany(MediaAsset::class, 'service_media', 'service_id', 'media_asset_id')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderBy('service_media.sort_order');
    }

    public function icon(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'icon_media_id');
    }

    public function cover(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'cover_media_id');
    }

    public function ogImage(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'og_image_media_id');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(ContactSubmission::class);
    }
}
