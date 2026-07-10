<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PostPolicy extends BasePolicy
{
    protected string $resource = 'post';

    public function update(User $user, Model $model): bool
    {
        if (! $user->can('update_post')) {
            return false;
        }

        return $this->limitedToOwn($user) ? $model->author_id === $user->id : true;
    }

    public function delete(User $user, Model $model): bool
    {
        if (! $user->can('delete_post')) {
            return false;
        }

        return $this->limitedToOwn($user) ? $model->author_id === $user->id : true;
    }

    /** Author (không kèm role cao hơn) chỉ thao tác được bài của chính mình. */
    private function limitedToOwn(User $user): bool
    {
        return $user->hasRole('Author') && ! $user->hasAnyRole(['SuperAdmin', 'Admin', 'Editor']);
    }
}
