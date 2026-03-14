<?php

namespace MiPressCz\Core\Policies;

use Illuminate\Foundation\Auth\User;
use MiPressCz\Core\Models\MediaTag;

class MediaTagPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage.media');
    }

    public function view(User $user, MediaTag $mediaTag): bool
    {
        return $user->hasPermissionTo('manage.media');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage.media');
    }

    public function update(User $user, MediaTag $mediaTag): bool
    {
        return $user->hasPermissionTo('manage.media');
    }

    public function delete(User $user, MediaTag $mediaTag): bool
    {
        return $user->hasPermissionTo('manage.media');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('manage.media');
    }
}
