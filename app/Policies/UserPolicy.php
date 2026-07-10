<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserPolicy extends BasePolicy
{
    protected string $resource = 'user';

    /** Không cho tự xoá tài khoản của chính mình. */
    public function delete(User $user, Model $model): bool
    {
        if ($model->getKey() === $user->getKey()) {
            return false;
        }

        return parent::delete($user, $model);
    }
}
