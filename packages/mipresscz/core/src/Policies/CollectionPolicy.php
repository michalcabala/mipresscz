<?php

namespace MiPressCz\Core\Policies;

use Illuminate\Foundation\Auth\User;
use MiPressCz\Core\Models\Collection;

class CollectionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view.collections');
    }

    public function view(User $user, Collection $collection): bool
    {
        return $user->hasPermissionTo('view.collections');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage.collections');
    }

    public function update(User $user, Collection $collection): bool
    {
        return $user->hasPermissionTo('manage.collections');
    }

    public function delete(User $user, Collection $collection): bool
    {
        return $user->hasPermissionTo('manage.collections');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('manage.collections');
    }
}
