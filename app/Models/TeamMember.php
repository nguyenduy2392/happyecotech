<?php

namespace App\Models;

use App\Models\Concerns\GeneratesSlug;
use App\Models\Concerns\HasBlameable;
use App\Models\Concerns\Orderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamMember extends Model
{
    use GeneratesSlug, HasBlameable, HasFactory, Orderable, SoftDeletes;

    public const STATUSES = [
        'draft' => 'Nháp',
        'published' => 'Đã đăng',
        'archived' => 'Lưu trữ',
    ];

    protected $fillable = [
        'name', 'slug', 'position', 'group', 'short_bio', 'bio',
        'photo_media_id', 'email', 'phone', 'socials',
        'status', 'is_featured', 'sort_order',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'socials' => 'array',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function photo(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'photo_media_id');
    }
}
