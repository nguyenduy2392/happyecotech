<?php

namespace App\Models;

use App\Models\Concerns\GeneratesSlug;
use App\Models\Concerns\HasBlameable;
use App\Models\Concerns\Orderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostCategory extends Model
{
    use GeneratesSlug, HasBlameable, Orderable, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'sort_order', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'category_post', 'post_category_id', 'post_id');
    }
}
