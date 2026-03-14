<?php

namespace MiPressCz\Core\Policies;

use Illuminate\Foundation\Auth\User;
use MiPressCz\Core\Models\MediaFolder;

class MediaFolderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage.media');
    }

    public function view(User $user, MediaFolder $mediaFolder): bool
    {
        return $user->hasPermissionTo('manage.media');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage.media');
    }

    public function update(User $user, MediaFolder $mediaFolder): bool
    {
        return $user->hasPermissionTo('manage.media');
    }

    public function delete(User $user, MediaFolder $mediaFolder): bool
    {
        return $user->hasPermissionTo('manage.media');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('manage.media');
    }
}
