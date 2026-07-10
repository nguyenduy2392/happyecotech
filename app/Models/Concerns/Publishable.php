<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait Publishable
{
    /**
     * Chỉ lấy bản ghi đã publish và đến thời điểm hiển thị.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where($this->getTable().'.status', 'published')
            ->where(function (Builder $q) {
                $q->whereNull($this->getTable().'.published_at')
                    ->orWhere($this->getTable().'.published_at', '<=', now());
            });
    }

    public function isPublished(): bool
    {
        return $this->status === 'published'
            && (is_null($this->published_at) || $this->published_at <= now());
    }
}
