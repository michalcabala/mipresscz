<?php

namespace MiPressCz\Core\Policies;

use Illuminate\Foundation\Auth\User;
use MiPressCz\Core\Models\Term;

class TermPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view.taxonomies');
    }

    public function view(User $user, Term $term): bool
    {
        return $user->hasPermissionTo('view.taxonomies');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage.taxonomies');
    }

    public function update(User $user, Term $term): bool
    {
        return $user->hasPermissionTo('manage.taxonomies');
    }

    public function delete(User $user, Term $term): bool
    {
        return $user->hasPermissionTo('manage.taxonomies');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('manage.taxonomies');
    }

    public function restore(User $user, Term $term): bool
    {
        return $user->hasPermissionTo('manage.taxonomies');
    }

    public function forceDelete(User $user, Term $term): bool
    {
        return $user->hasPermissionTo('manage.taxonomies');
    }
}
