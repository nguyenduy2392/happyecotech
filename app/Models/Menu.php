<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $fillable = ['name', 'location', 'description'];

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort_order');
    }

    /**
     * Trả về cây menu (tối đa 2 cấp) gồm các item đã bật.
     *
     * @return Collection<int,MenuItem>
     */
    public function tree(): Collection
    {
        return $this->items()
            ->where('is_enabled', true)
            ->whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->where('is_enabled', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();
    }
}
