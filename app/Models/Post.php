<?php

namespace App\Models;

use App\Models\Concerns\GeneratesSlug;
use App\Models\Concerns\HasBlameable;
use App\Models\Concerns\Publishable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mews\Purifier\Facades\Purifier;

class Post extends Model
{
    use GeneratesSlug, HasBlameable, HasFactory, Publishable, SoftDeletes;

    public const STATUSES = [
        'draft' => 'Nháp',
        'published' => 'Đã đăng',
        'archived' => 'Lưu trữ',
    ];

    protected $fillable = [
        'title', 'slug', 'excerpt', 'content',
        'featured_media_id', 'og_image_media_id', 'author_id',
        'status', 'is_featured', 'published_at',
        'seo_title', 'seo_description', 'seo_canonical',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected function slugSource(): string
    {
        return 'title';
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Sanitize HTML rich-text chống XSS khi lưu. */
    protected function content(): Attribute
    {
        return Attribute::make(set: fn (?string $value) => filled($value) ? Purifier::clean($value) : $value);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function featuredMedia(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'featured_media_id');
    }

    public function ogImage(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'og_image_media_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(PostCategory::class, 'category_post', 'post_id', 'post_category_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tag', 'post_id', 'tag_id');
    }
}
