<?php

namespace App\Models;

use App\Models\Concerns\Orderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    use Orderable;

    /** Nền tảng => class icon LineIcons mặc định. */
    public const PLATFORMS = [
        'facebook' => 'lni lni-facebook-filled',
        'linkedin' => 'lni lni-linkedin-original',
        'youtube' => 'lni lni-youtube',
        'instagram' => 'lni lni-instagram-filled',
        'twitter' => 'lni lni-twitter-filled',
        'tiktok' => 'lni lni-tiktok',
        'zalo' => 'lni lni-comments',
    ];

    protected $fillable = ['platform', 'label', 'url', 'icon', 'sort_order', 'is_enabled'];

    protected $casts = [
        'is_enabled' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (SocialLink $link) {
            if (blank($link->icon)) {
                $link->icon = self::PLATFORMS[$link->platform] ?? 'lni lni-link';
            }
        });
    }

    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('is_enabled', true);
    }
}
