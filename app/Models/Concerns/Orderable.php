<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait Orderable
{
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy($this->getTable().'.sort_order')
            ->orderBy($this->getTable().'.created_at');
    }
}
