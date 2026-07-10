<?php

namespace App\Policies;

use App\Models\User;

class ContactSubmissionPolicy extends BasePolicy
{
    protected string $resource = 'contact';

    /** Lead không được tạo thủ công trong admin (chỉ đến từ form public). */
    public function create(User $user): bool
    {
        return false;
    }

    public function export(User $user): bool
    {
        return $user->can('export_contact');
    }
}
