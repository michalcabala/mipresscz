<?php

use App\Enums\UserRole;
use App\Models\User;
use MiPressCz\Core\Models\Term;

function createUserWithRoleForTermPolicy(UserRole $role): User
{
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    (new \Database\Seeders\RolesAndPermissionsSeeder)->run();
    $user = User::factory()->create(['role' => $role]);
    $user->syncRoles([$role->value]);

    return $user;
}

// -- viewAny --

it('admin can viewAny terms', function () {
    $user = createUserWithRoleForTermPolicy(UserRole::Admin);
    $this->actingAs($user);

    expect($user->can('viewAny', Term::class))->toBeTrue();
});

it('editor can viewAny terms', function () {
    $user = createUserWithRoleForTermPolicy(UserRole::Editor);
    $this->actingAs($user);

    expect($user->can('viewAny', Term::class))->toBeTrue();
});

it('contributor can viewAny terms (has view.taxonomies)', function () {
    $user = createUserWithRoleForTermPolicy(UserRole::Contributor);
    $this->actingAs($user);

    expect($user->can('viewAny', Term::class))->toBeTrue();
});

// -- create --

it('admin can create term', function () {
    $user = createUserWithRoleForTermPolicy(UserRole::Admin);
    $this->actingAs($user);

    expect($user->can('create', Term::class))->toBeTrue();
});

it('editor can create term (has manage.taxonomies)', function () {
    $user = createUserWithRoleForTermPolicy(UserRole::Editor);
    $this->actingAs($user);

    expect($user->can('create', Term::class))->toBeTrue();
});

it('contributor cannot create term', function () {
    $user = createUserWithRoleForTermPolicy(UserRole::Contributor);
    $this->actingAs($user);

    expect($user->can('create', Term::class))->toBeFalse();
});

// -- update --

it('admin can update term', function () {
    $user = createUserWithRoleForTermPolicy(UserRole::Admin);
    $term = Term::factory()->create();
    $this->actingAs($user);

    expect($user->can('update', $term))->toBeTrue();
});

it('editor can update term', function () {
    $user = createUserWithRoleForTermPolicy(UserRole::Editor);
    $term = Term::factory()->create();
    $this->actingAs($user);

    expect($user->can('update', $term))->toBeTrue();
});

it('contributor cannot update term', function () {
    $user = createUserWithRoleForTermPolicy(UserRole::Contributor);
    $term = Term::factory()->create();
    $this->actingAs($user);

    expect($user->can('update', $term))->toBeFalse();
});

// -- delete --

it('admin can delete term', function () {
    $user = createUserWithRoleForTermPolicy(UserRole::Admin);
    $term = Term::factory()->create();
    $this->actingAs($user);

    expect($user->can('delete', $term))->toBeTrue();
});

it('editor can delete term', function () {
    $user = createUserWithRoleForTermPolicy(UserRole::Editor);
    $term = Term::factory()->create();
    $this->actingAs($user);

    expect($user->can('delete', $term))->toBeTrue();
});

it('contributor cannot delete term', function () {
    $user = createUserWithRoleForTermPolicy(UserRole::Contributor);
    $term = Term::factory()->create();
    $this->actingAs($user);

    expect($user->can('delete', $term))->toBeFalse();
});

// -- deleteAny --

it('admin can deleteAny term', function () {
    $user = createUserWithRoleForTermPolicy(UserRole::Admin);
    $this->actingAs($user);

    expect($user->can('deleteAny', Term::class))->toBeTrue();
});

it('contributor cannot deleteAny term', function () {
    $user = createUserWithRoleForTermPolicy(UserRole::Contributor);
    $this->actingAs($user);

    expect($user->can('deleteAny', Term::class))->toBeFalse();
});

// -- restore --

it('admin can restore term', function () {
    $user = createUserWithRoleForTermPolicy(UserRole::Admin);
    $term = Term::factory()->create();
    $this->actingAs($user);

    expect($user->can('restore', $term))->toBeTrue();
});

// -- forceDelete --

it('admin can forceDelete term', function () {
    $user = createUserWithRoleForTermPolicy(UserRole::Admin);
    $term = Term::factory()->create();
    $this->actingAs($user);

    expect($user->can('forceDelete', $term))->toBeTrue();
});
