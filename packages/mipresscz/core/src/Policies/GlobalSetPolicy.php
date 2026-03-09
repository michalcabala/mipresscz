<?php

namespace MiPressCz\Core\Policies;

use Illuminate\Foundation\Auth\User;
use MiPressCz\Core\Models\GlobalSet;

class GlobalSetPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view.global_sets');
    }

    public function view(User $user, GlobalSet $globalSet): bool
    {
        return $user->hasPermissionTo('view.global_sets');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage.global_sets');
    }

    public function update(User $user, GlobalSet $globalSet): bool
    {
        return $user->hasPermissionTo('manage.global_sets');
    }

    public function delete(User $user, GlobalSet $globalSet): bool
    {
        return $user->hasPermissionTo('manage.global_sets');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('manage.global_sets');
    }
}
