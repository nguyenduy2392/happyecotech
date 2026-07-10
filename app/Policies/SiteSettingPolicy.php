<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class SiteSettingPolicy extends BasePolicy
{
    protected string $resource = 'site_setting';

    public function viewAny(User $user): bool
    {
        return $user->can('manage_settings');
    }

    public function view(User $user, Model $model): bool
    {
        return $user->can('manage_settings');
    }

    public function update(User $user, Model $model): bool
    {
        return $user->can('manage_settings');
    }
}
