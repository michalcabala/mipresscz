<?php

use App\Enums\UserRole;
use App\Models\User;
use MiPressCz\Core\Models\Menu;

function createUserWithRoleForMenuPolicy(UserRole $role): User
{
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    (new \Database\Seeders\RolesAndPermissionsSeeder)->run();
    $user = User::factory()->create(['role' => $role]);
    $user->syncRoles([$role->value]);

    return $user;
}

// -- viewAny --

it('admin can viewAny menus', function () {
    $user = createUserWithRoleForMenuPolicy(UserRole::Admin);

    expect($user->can('viewAny', Menu::class))->toBeTrue();
});

it('editor can viewAny menus', function () {
    $user = createUserWithRoleForMenuPolicy(UserRole::Editor);

    expect($user->can('viewAny', Menu::class))->toBeTrue();
});

it('contributor cannot viewAny menus', function () {
    $user = createUserWithRoleForMenuPolicy(UserRole::Contributor);

    expect($user->can('viewAny', Menu::class))->toBeFalse();
});

// -- create --

it('admin can create menu', function () {
    $user = createUserWithRoleForMenuPolicy(UserRole::Admin);

    expect($user->can('create', Menu::class))->toBeTrue();
});

it('editor can create menu', function () {
    $user = createUserWithRoleForMenuPolicy(UserRole::Editor);

    expect($user->can('create', Menu::class))->toBeTrue();
});

it('contributor cannot create menu', function () {
    $user = createUserWithRoleForMenuPolicy(UserRole::Contributor);

    expect($user->can('create', Menu::class))->toBeFalse();
});

// -- update --

it('admin can update menu', function () {
    $user = createUserWithRoleForMenuPolicy(UserRole::Admin);
    $menu = Menu::factory()->create();

    expect($user->can('update', $menu))->toBeTrue();
});

it('editor can update menu', function () {
    $user = createUserWithRoleForMenuPolicy(UserRole::Editor);
    $menu = Menu::factory()->create();

    expect($user->can('update', $menu))->toBeTrue();
});

it('contributor cannot update menu', function () {
    $user = createUserWithRoleForMenuPolicy(UserRole::Contributor);
    $menu = Menu::factory()->create();

    expect($user->can('update', $menu))->toBeFalse();
});

// -- delete --

it('admin can delete menu', function () {
    $user = createUserWithRoleForMenuPolicy(UserRole::Admin);
    $menu = Menu::factory()->create();

    expect($user->can('delete', $menu))->toBeTrue();
});

it('editor can delete menu', function () {
    $user = createUserWithRoleForMenuPolicy(UserRole::Editor);
    $menu = Menu::factory()->create();

    expect($user->can('delete', $menu))->toBeTrue();
});

it('contributor cannot delete menu', function () {
    $user = createUserWithRoleForMenuPolicy(UserRole::Contributor);
    $menu = Menu::factory()->create();

    expect($user->can('delete', $menu))->toBeFalse();
});
