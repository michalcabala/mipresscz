<?php

namespace App\Policies;

use App\Models\Block;
use App\Models\User;

class BlockPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view.blocks');
    }

    public function view(User $user, Block $block): bool
    {
        return $user->hasPermissionTo('view.blocks');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage.blocks');
    }

    public function update(User $user, Block $block): bool
    {
        return $user->hasPermissionTo('manage.blocks');
    }

    public function delete(User $user, Block $block): bool
    {
        return $user->hasPermissionTo('manage.blocks');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('manage.blocks');
    }
}
