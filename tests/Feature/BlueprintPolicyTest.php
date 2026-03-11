<?php

use App\Enums\UserRole;
use App\Models\User;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\GlobalSet;
use MiPressCz\Core\Models\Taxonomy;

function createUserWithRoleForPolicy(UserRole $role): User
{
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    (new \Database\Seeders\RolesAndPermissionsSeeder)->run();
    $user = User::factory()->create(['role' => $role]);
    $user->syncRoles([$role->value]);

    return $user;
}

// -- BlueprintPolicy --

it('admin can viewAny blueprints', function () {
    $user = createUserWithRoleForPolicy(UserRole::Admin);
    $this->actingAs($user);

    expect($user->can('viewAny', Blueprint::class))->toBeTrue();
});

it('editor can viewAny blueprints (has view.collections permission)', function () {
    $user = createUserWithRoleForPolicy(UserRole::Editor);
    $this->actingAs($user);

    expect($user->can('viewAny', Blueprint::class))->toBeTrue();
});

it('contributor cannot viewAny blueprints', function () {
    $user = createUserWithRoleForPolicy(UserRole::Contributor);
    $this->actingAs($user);

    expect($user->can('viewAny', Blueprint::class))->toBeFalse();
});

it('admin can create blueprint', function () {
    $user = createUserWithRoleForPolicy(UserRole::Admin);
    $this->actingAs($user);

    expect($user->can('create', Blueprint::class))->toBeTrue();
});

it('editor cannot create blueprint', function () {
    $user = createUserWithRoleForPolicy(UserRole::Editor);
    $this->actingAs($user);

    expect($user->can('create', Blueprint::class))->toBeFalse();
});

it('admin can update blueprint', function () {
    $user = createUserWithRoleForPolicy(UserRole::Admin);
    $blueprint = Blueprint::factory()->create();
    $this->actingAs($user);

    expect($user->can('update', $blueprint))->toBeTrue();
});

it('admin can delete blueprint', function () {
    $user = createUserWithRoleForPolicy(UserRole::Admin);
    $blueprint = Blueprint::factory()->create();
    $this->actingAs($user);

    expect($user->can('delete', $blueprint))->toBeTrue();
});

it('admin can deleteAny blueprint', function () {
    $user = createUserWithRoleForPolicy(UserRole::Admin);
    $this->actingAs($user);

    expect($user->can('deleteAny', Blueprint::class))->toBeTrue();
});

it('admin can restore blueprint', function () {
    $user = createUserWithRoleForPolicy(UserRole::Admin);
    $blueprint = Blueprint::factory()->create();
    $this->actingAs($user);

    expect($user->can('restore', $blueprint))->toBeTrue();
});

it('admin can forceDelete blueprint', function () {
    $user = createUserWithRoleForPolicy(UserRole::Admin);
    $blueprint = Blueprint::factory()->create();
    $this->actingAs($user);

    expect($user->can('forceDelete', $blueprint))->toBeTrue();
});

// -- deleteAny gaps in existing policies --

it('admin can deleteAny collection', function () {
    $user = createUserWithRoleForPolicy(UserRole::Admin);
    $this->actingAs($user);

    expect($user->can('deleteAny', Collection::class))->toBeTrue();
});

it('editor cannot deleteAny collection', function () {
    $user = createUserWithRoleForPolicy(UserRole::Editor);
    $this->actingAs($user);

    expect($user->can('deleteAny', Collection::class))->toBeFalse();
});

it('admin can deleteAny taxonomy', function () {
    $user = createUserWithRoleForPolicy(UserRole::Admin);
    $this->actingAs($user);

    expect($user->can('deleteAny', Taxonomy::class))->toBeTrue();
});

it('admin can deleteAny global set', function () {
    $user = createUserWithRoleForPolicy(UserRole::Admin);
    $this->actingAs($user);

    expect($user->can('deleteAny', GlobalSet::class))->toBeTrue();
});
