<?php

namespace MiPressCz\Core\Policies;

use App\Enums\UserRole;
use Illuminate\Foundation\Auth\User;
use MiPressCz\Core\Models\Entry;

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

    public function publish(User $user, Entry $entry): bool
    {
        return $user->hasPermissionTo('publish.entries');
    }

    public function viewRevisions(User $user, Entry $entry): bool
    {
        if (! $user->hasPermissionTo('view.revisions')) {
            return false;
        }

        if ($user->role === UserRole::Contributor) {
            return $entry->author_id === $user->id;
        }

        return true;
    }

    public function compareRevisions(User $user, Entry $entry): bool
    {
        return $this->viewRevisions($user, $entry)
            && $user->hasPermissionTo('compare.revisions');
    }

    public function restoreRevision(User $user, Entry $entry): bool
    {
        return $this->viewRevisions($user, $entry)
            && $user->hasPermissionTo('restore.revisions')
            && $this->update($user, $entry);
    }

    public function deleteRevision(User $user, Entry $entry): bool
    {
        return $this->viewRevisions($user, $entry)
            && $user->hasPermissionTo('delete.revisions');
    }
}
