<?php

namespace MiPressCz\Core\Policies;

use Illuminate\Foundation\Auth\User;
use MiPressCz\Core\Models\Menu;

class MenuPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view.menus');
    }

    public function view(User $user, Menu $menu): bool
    {
        return $user->hasPermissionTo('view.menus');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage.menus');
    }

    public function update(User $user, Menu $menu): bool
    {
        return $user->hasPermissionTo('manage.menus');
    }

    public function delete(User $user, Menu $menu): bool
    {
        return $user->hasPermissionTo('manage.menus');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('manage.menus');
    }
}
