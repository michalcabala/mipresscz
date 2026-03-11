<?php

namespace MiPressCz\Core\Policies;

use Illuminate\Foundation\Auth\User;
use MiPressCz\Core\Models\Blueprint;

class BlueprintPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view.collections');
    }

    public function view(User $user, Blueprint $blueprint): bool
    {
        return $user->hasPermissionTo('view.collections');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage.collections');
    }

    public function update(User $user, Blueprint $blueprint): bool
    {
        return $user->hasPermissionTo('manage.collections');
    }

    public function delete(User $user, Blueprint $blueprint): bool
    {
        return $user->hasPermissionTo('manage.collections');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('manage.collections');
    }

    public function restore(User $user, Blueprint $blueprint): bool
    {
        return $user->hasPermissionTo('manage.collections');
    }

    public function forceDelete(User $user, Blueprint $blueprint): bool
    {
        return $user->hasPermissionTo('manage.collections');
    }
}
