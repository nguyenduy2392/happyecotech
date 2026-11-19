<?php

namespace App\Models;

use App\Models\Concerns\GeneratesSlug;
use App\Models\Concerns\HasBlameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Page extends Model
{
    use GeneratesSlug, HasBlameable, SoftDeletes;

    public const STATUSES = [
        'draft' => 'Nháp',
        'published' => 'Đã đăng',
    ];

    public const FIXED_ROUTES = ['home', 'gioi-thieu', 'dich-vu', 'tin-tuc', 'doi-ngu', 'lien-he', 'up'];

    public const PUBLIC_SLUG_PATTERN = '(?!(?:admin|livewire|storage|assets|api|sitemap)(?:-|$))[a-z0-9]+(?:-[a-z0-9]+)*';

    protected static function booted(): void
    {
        static::saving(function (Page $page) {
            if ($page->getOriginal('slug') === 'home' || $page->slug === 'home') {
                $page->slug = 'home';
                $page->status = 'published';
                $page->is_system = true;
                $page->deleted_at = null;
            }
        });
        static::deleting(fn (Page $page) => $page->slug === 'home' ? false : null);
    }

    public function save(array $options = [])
    {
        $previous = $this->getOriginal('slug');
        if (! $this->exists || ! $this->isDirty('slug') || $previous === 'home') {
            return parent::save($options);
        }

        return $this->getConnection()->transaction(function () use ($options, $previous) {
            $previous = static::whereKey($this->id)->lockForUpdate()->value('slug') ?? $previous;
            $saved = parent::save($options);
            if ($saved && $previous !== $this->slug) {
                $this->getConnection()->table('page_redirects')->upsert(
                    [['source_slug' => $previous, 'page_id' => $this->id]], ['source_slug'], ['page_id'],
                );
            }

            return $saved;
        });
    }

    public static function redirectPath(string $path): ?string
    {
        $slug = ltrim($path, '/');
        if (! preg_match('/^'.self::PUBLIC_SLUG_PATTERN.'$/D', $slug) || in_array($slug, array_diff(self::FIXED_ROUTES, ['gioi-thieu']), true)
            || static::whereSlug($slug)->exists()) {
            return null;
        }
        $target = DB::table('page_redirects')->join('pages', 'pages.id', '=', 'page_redirects.page_id')
            ->where('source_slug', $slug)->where('pages.status', 'published')
            ->whereNull('pages.deleted_at')->value('pages.slug');

        return $target ? '/'.$target : null;
    }

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
