<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Sinh slug duy nhất từ một cột nguồn khi slug để trống.
 * Model override slugSource() nếu nguồn không phải 'name'.
 */
trait GeneratesSlug
{
    public static function bootGeneratesSlug(): void
    {
        static::saving(function ($model) {
            $source = $model->slugSource();

            if (blank($model->slug) && filled($model->{$source})) {
                $model->slug = $model->generateUniqueSlug(Str::slug($model->{$source}));
            }
        });
    }

    protected function slugSource(): string
    {
        return 'name';
    }

    public function generateUniqueSlug(string $base): string
    {
        $base = $base !== '' ? $base : 'item';
        $slug = $base;
        $i = 1;

        while ($this->slugExists($slug)) {
            $slug = $base.'-'.(++$i);
        }

        return $slug;
    }

    protected function slugExists(string $slug): bool
    {
        $query = static::query()->where('slug', $slug);

        if ($this->getKey()) {
            $query->where($this->getKeyName(), '!=', $this->getKey());
        }

        if (in_array(SoftDeletes::class, class_uses_recursive(static::class), true)) {
            $query->withTrashed();
        }

        return $query->exists();
    }
}
