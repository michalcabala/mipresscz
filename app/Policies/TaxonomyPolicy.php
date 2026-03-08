<?php

namespace App\Policies;

use App\Models\Taxonomy;
use App\Models\User;

class TaxonomyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view.taxonomies');
    }

    public function view(User $user, Taxonomy $taxonomy): bool
    {
        return $user->hasPermissionTo('view.taxonomies');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage.taxonomies');
    }

    public function update(User $user, Taxonomy $taxonomy): bool
    {
        return $user->hasPermissionTo('manage.taxonomies');
    }

    public function delete(User $user, Taxonomy $taxonomy): bool
    {
        return $user->hasPermissionTo('manage.taxonomies');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('manage.taxonomies');
    }
}
