<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Entry;
use App\Models\User;

class EntryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view.entries');
    }

    public function view(User $user, Entry $entry): bool
    {
        return $user->hasPermissionTo('view.entries');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create.entries');
    }

    public function update(User $user, Entry $entry): bool
    {
        if (! $user->hasPermissionTo('update.entries')) {
            return false;
        }

        if ($user->role === UserRole::Contributor) {
            return $entry->author_id === $user->id;
        }

        return true;
    }

    public function delete(User $user, Entry $entry): bool
    {
        return $user->hasPermissionTo('delete.entries');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete.entries');
    }
}
