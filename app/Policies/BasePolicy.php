<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Policy cơ sở: map các ability của Filament sang permission "{ability}_{resource}".
 * Backend luôn kiểm tra quyền — không chỉ ẩn menu ở frontend.
 */
abstract class BasePolicy
{
    /** Khoá resource, ví dụ 'service'. */
    protected string $resource;

    public function viewAny(User $user): bool
    {
        return $user->can("view_any_{$this->resource}");
    }

    public function view(User $user, Model $model): bool
    {
        return $user->can("view_{$this->resource}");
    }

    public function create(User $user): bool
    {
        return $user->can("create_{$this->resource}");
    }

    public function update(User $user, Model $model): bool
    {
        return $user->can("update_{$this->resource}");
    }

    public function delete(User $user, Model $model): bool
    {
        return $user->can("delete_{$this->resource}");
    }

    public function deleteAny(User $user): bool
    {
        return $user->can("delete_{$this->resource}");
    }

    public function restore(User $user, Model $model): bool
    {
        return $user->can("restore_{$this->resource}");
    }

    public function restoreAny(User $user): bool
    {
        return $user->can("restore_{$this->resource}");
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return $user->can("force_delete_{$this->resource}");
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can("force_delete_{$this->resource}");
    }

    public function reorder(User $user): bool
    {
        return $user->can("update_{$this->resource}");
    }
}
