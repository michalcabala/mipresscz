<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\GlobalSet;
use MiPressCz\Core\Models\Taxonomy;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

// ── Helper ──

function createUserWithRole(UserRole $role): User
{
    $user = User::factory()->create(['role' => $role]);
    $user->syncRoles([$role->value]);

    return $user;
}

// ── SuperAdmin bypasses all ──

it('superadmin bypasses all authorization', function () {
    $superadmin = createUserWithRole(UserRole::SuperAdmin);
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    $entry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'author_id' => $superadmin->id,
    ]);
    $taxonomy = Taxonomy::factory()->create();
    $globalSet = GlobalSet::factory()->create();

    expect($superadmin->can('viewAny', User::class))->toBeTrue()
        ->and($superadmin->can('create', User::class))->toBeTrue()
        ->and($superadmin->can('update', $entry))->toBeTrue()
        ->and($superadmin->can('delete', $entry))->toBeTrue()
        ->and($superadmin->can('viewAny', Collection::class))->toBeTrue()
        ->and($superadmin->can('update', $collection))->toBeTrue()
        ->and($superadmin->can('viewAny', Taxonomy::class))->toBeTrue()
        ->and($superadmin->can('update', $taxonomy))->toBeTrue()
        ->and($superadmin->can('viewAny', GlobalSet::class))->toBeTrue()
        ->and($superadmin->can('update', $globalSet))->toBeTrue();
});

// ── Admin ──

it('admin can manage users', function () {
    $admin = createUserWithRole(UserRole::Admin);
    $otherUser = User::factory()->create();

    expect($admin->can('viewAny', User::class))->toBeTrue()
        ->and($admin->can('view', $otherUser))->toBeTrue()
        ->and($admin->can('create', User::class))->toBeTrue()
        ->and($admin->can('update', $otherUser))->toBeTrue()
        ->and($admin->can('delete', $otherUser))->toBeTrue();
});

it('admin can manage collections', function () {
    $admin = createUserWithRole(UserRole::Admin);
    $collection = Collection::factory()->create();

    expect($admin->can('viewAny', Collection::class))->toBeTrue()
        ->and($admin->can('view', $collection))->toBeTrue()
        ->and($admin->can('create', Collection::class))->toBeTrue()
        ->and($admin->can('update', $collection))->toBeTrue()
        ->and($admin->can('delete', $collection))->toBeTrue();
});

it('admin can manage entries', function () {
    $admin = createUserWithRole(UserRole::Admin);
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    $entry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
    ]);

    expect($admin->can('viewAny', Entry::class))->toBeTrue()
        ->and($admin->can('view', $entry))->toBeTrue()
        ->and($admin->can('create', Entry::class))->toBeTrue()
        ->and($admin->can('update', $entry))->toBeTrue()
        ->and($admin->can('delete', $entry))->toBeTrue();
});

it('admin can manage taxonomies', function () {
    $admin = createUserWithRole(UserRole::Admin);
    $taxonomy = Taxonomy::factory()->create();

    expect($admin->can('viewAny', Taxonomy::class))->toBeTrue()
        ->and($admin->can('create', Taxonomy::class))->toBeTrue()
        ->and($admin->can('update', $taxonomy))->toBeTrue()
        ->and($admin->can('delete', $taxonomy))->toBeTrue();
});

it('admin can manage global sets', function () {
    $admin = createUserWithRole(UserRole::Admin);
    $globalSet = GlobalSet::factory()->create();

    expect($admin->can('viewAny', GlobalSet::class))->toBeTrue()
        ->and($admin->can('create', GlobalSet::class))->toBeTrue()
        ->and($admin->can('update', $globalSet))->toBeTrue()
        ->and($admin->can('delete', $globalSet))->toBeTrue();
});

// ── Editor ──

it('editor can manage entries and taxonomies', function () {
    $editor = createUserWithRole(UserRole::Editor);
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    $entry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
    ]);
    $taxonomy = Taxonomy::factory()->create();

    expect($editor->can('viewAny', Entry::class))->toBeTrue()
        ->and($editor->can('create', Entry::class))->toBeTrue()
        ->and($editor->can('update', $entry))->toBeTrue()
        ->and($editor->can('delete', $entry))->toBeTrue()
        ->and($editor->can('viewAny', Taxonomy::class))->toBeTrue()
        ->and($editor->can('create', Taxonomy::class))->toBeTrue()
        ->and($editor->can('update', $taxonomy))->toBeTrue()
        ->and($editor->can('delete', $taxonomy))->toBeTrue();
});

it('editor can view but not manage collections', function () {
    $editor = createUserWithRole(UserRole::Editor);
    $collection = Collection::factory()->create();

    expect($editor->can('viewAny', Collection::class))->toBeTrue()
        ->and($editor->can('view', $collection))->toBeTrue()
        ->and($editor->can('create', Collection::class))->toBeFalse()
        ->and($editor->can('update', $collection))->toBeFalse()
        ->and($editor->can('delete', $collection))->toBeFalse();
});

it('editor can view but not manage global sets', function () {
    $editor = createUserWithRole(UserRole::Editor);
    $globalSet = GlobalSet::factory()->create();

    expect($editor->can('viewAny', GlobalSet::class))->toBeTrue()
        ->and($editor->can('view', $globalSet))->toBeTrue()
        ->and($editor->can('create', GlobalSet::class))->toBeFalse()
        ->and($editor->can('update', $globalSet))->toBeFalse()
        ->and($editor->can('delete', $globalSet))->toBeFalse();
});

it('editor can view but not manage users', function () {
    $editor = createUserWithRole(UserRole::Editor);
    $otherUser = User::factory()->create();

    expect($editor->can('viewAny', User::class))->toBeTrue()
        ->and($editor->can('view', $otherUser))->toBeTrue()
        ->and($editor->can('create', User::class))->toBeFalse()
        ->and($editor->can('update', $otherUser))->toBeFalse()
        ->and($editor->can('delete', $otherUser))->toBeFalse();
});

// ── Contributor ──

it('contributor can view and create entries', function () {
    $contributor = createUserWithRole(UserRole::Contributor);

    expect($contributor->can('viewAny', Entry::class))->toBeTrue()
        ->and($contributor->can('create', Entry::class))->toBeTrue();
});

it('contributor can update own entries only', function () {
    $contributor = createUserWithRole(UserRole::Contributor);
    $otherUser = User::factory()->create();
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    $ownEntry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'author_id' => $contributor->id,
    ]);

    $otherEntry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'author_id' => $otherUser->id,
    ]);

    expect($contributor->can('update', $ownEntry))->toBeTrue()
        ->and($contributor->can('update', $otherEntry))->toBeFalse();
});

it('contributor cannot delete entries', function () {
    $contributor = createUserWithRole(UserRole::Contributor);
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    $entry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'author_id' => $contributor->id,
    ]);

    expect($contributor->can('delete', $entry))->toBeFalse()
        ->and($contributor->can('deleteAny', Entry::class))->toBeFalse();
});

it('contributor can view taxonomies but not manage them', function () {
    $contributor = createUserWithRole(UserRole::Contributor);
    $taxonomy = Taxonomy::factory()->create();

    expect($contributor->can('viewAny', Taxonomy::class))->toBeTrue()
        ->and($contributor->can('view', $taxonomy))->toBeTrue()
        ->and($contributor->can('create', Taxonomy::class))->toBeFalse()
        ->and($contributor->can('update', $taxonomy))->toBeFalse()
        ->and($contributor->can('delete', $taxonomy))->toBeFalse();
});

it('contributor cannot access users', function () {
    $contributor = createUserWithRole(UserRole::Contributor);

    expect($contributor->can('viewAny', User::class))->toBeFalse()
        ->and($contributor->can('create', User::class))->toBeFalse();
});

it('contributor cannot access collections', function () {
    $contributor = createUserWithRole(UserRole::Contributor);

    expect($contributor->can('viewAny', Collection::class))->toBeFalse()
        ->and($contributor->can('create', Collection::class))->toBeFalse();
});

it('applies revision authorization rules per role', function () {
    $admin = createUserWithRole(UserRole::Admin);
    $editor = createUserWithRole(UserRole::Editor);
    $contributor = createUserWithRole(UserRole::Contributor);
    $otherUser = User::factory()->create();
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    $ownEntry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'author_id' => $contributor->id,
    ]);

    $otherEntry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'author_id' => $otherUser->id,
    ]);

    expect($contributor->can('viewRevisions', $ownEntry))->toBeTrue()
        ->and($contributor->can('viewRevisions', $otherEntry))->toBeFalse()
        ->and($contributor->can('compareRevisions', $ownEntry))->toBeFalse()
        ->and($contributor->can('restoreRevision', $ownEntry))->toBeFalse()
        ->and($editor->can('viewRevisions', $otherEntry))->toBeTrue()
        ->and($editor->can('compareRevisions', $otherEntry))->toBeTrue()
        ->and($editor->can('restoreRevision', $otherEntry))->toBeFalse()
        ->and($admin->can('viewRevisions', $otherEntry))->toBeTrue()
        ->and($admin->can('compareRevisions', $otherEntry))->toBeTrue()
        ->and($admin->can('restoreRevision', $otherEntry))->toBeTrue()
        ->and($admin->can('deleteRevision', $otherEntry))->toBeFalse();
});

it('contributor cannot access global sets', function () {
    $contributor = createUserWithRole(UserRole::Contributor);

    expect($contributor->can('viewAny', GlobalSet::class))->toBeFalse()
        ->and($contributor->can('create', GlobalSet::class))->toBeFalse();
});

// ── User model role sync ──

it('syncs spatie role on user save when role changes', function () {
    $user = User::factory()->create(['role' => UserRole::Editor]);
    $user->syncRoles([UserRole::Editor->value]);

    expect($user->hasRole('editor'))->toBeTrue();

    $user->update(['role' => UserRole::Admin]);

    expect($user->fresh()->hasRole('admin'))->toBeTrue()
        ->and($user->fresh()->hasRole('editor'))->toBeFalse();
});

// ── Permission matrix ──

it('defines correct permissions per role', function (UserRole $role, string $permission, bool $expected) {
    $user = createUserWithRole($role);

    expect($user->hasPermissionTo($permission))->toBe($expected);
})->with([
    // Admin has all permissions
    'admin can view.users' => [UserRole::Admin, 'view.users', true],
    'admin can manage.users' => [UserRole::Admin, 'manage.users', true],
    'admin can manage.collections' => [UserRole::Admin, 'manage.collections', true],
    'admin can delete.entries' => [UserRole::Admin, 'delete.entries', true],
    'admin can manage.global_sets' => [UserRole::Admin, 'manage.global_sets', true],
    'admin can view.revisions' => [UserRole::Admin, 'view.revisions', true],
    'admin can compare.revisions' => [UserRole::Admin, 'compare.revisions', true],
    'admin can restore.revisions' => [UserRole::Admin, 'restore.revisions', true],
    'admin cannot delete.revisions' => [UserRole::Admin, 'delete.revisions', false],

    // Editor partial access
    'editor can view.users' => [UserRole::Editor, 'view.users', true],
    'editor cannot manage.users' => [UserRole::Editor, 'manage.users', false],
    'editor can view.collections' => [UserRole::Editor, 'view.collections', true],
    'editor cannot manage.collections' => [UserRole::Editor, 'manage.collections', false],
    'editor can delete.entries' => [UserRole::Editor, 'delete.entries', true],
    'editor can manage.taxonomies' => [UserRole::Editor, 'manage.taxonomies', true],
    'editor cannot manage.global_sets' => [UserRole::Editor, 'manage.global_sets', false],
    'editor can view.revisions' => [UserRole::Editor, 'view.revisions', true],
    'editor can compare.revisions' => [UserRole::Editor, 'compare.revisions', true],
    'editor cannot restore.revisions' => [UserRole::Editor, 'restore.revisions', false],
    'editor cannot delete.revisions' => [UserRole::Editor, 'delete.revisions', false],

    // Contributor minimal access
    'contributor can view.entries' => [UserRole::Contributor, 'view.entries', true],
    'contributor can create.entries' => [UserRole::Contributor, 'create.entries', true],
    'contributor can update.entries' => [UserRole::Contributor, 'update.entries', true],
    'contributor cannot delete.entries' => [UserRole::Contributor, 'delete.entries', false],
    'contributor can view.revisions' => [UserRole::Contributor, 'view.revisions', true],
    'contributor cannot compare.revisions' => [UserRole::Contributor, 'compare.revisions', false],
    'contributor cannot restore.revisions' => [UserRole::Contributor, 'restore.revisions', false],
    'contributor cannot delete.revisions' => [UserRole::Contributor, 'delete.revisions', false],
    'contributor can view.taxonomies' => [UserRole::Contributor, 'view.taxonomies', true],
    'contributor cannot manage.taxonomies' => [UserRole::Contributor, 'manage.taxonomies', false],
    'contributor cannot view.users' => [UserRole::Contributor, 'view.users', false],
    'contributor cannot view.collections' => [UserRole::Contributor, 'view.collections', false],
    'contributor cannot view.global_sets' => [UserRole::Contributor, 'view.global_sets', false],

    // New media/locale/settings permissions
    'admin can manage.media' => [UserRole::Admin, 'manage.media', true],
    'admin can manage.locales' => [UserRole::Admin, 'manage.locales', true],
    'admin can manage.settings' => [UserRole::Admin, 'manage.settings', true],
    'editor can manage.media' => [UserRole::Editor, 'manage.media', true],
    'editor cannot manage.locales' => [UserRole::Editor, 'manage.locales', false],
    'editor cannot manage.settings' => [UserRole::Editor, 'manage.settings', false],
    'contributor can view.media' => [UserRole::Contributor, 'view.media', true],
    'contributor cannot manage.media' => [UserRole::Contributor, 'manage.media', false],
    'contributor cannot manage.locales' => [UserRole::Contributor, 'manage.locales', false],
    'contributor cannot manage.settings' => [UserRole::Contributor, 'manage.settings', false],
    'contributor cannot manage.menus' => [UserRole::Contributor, 'manage.menus', false],
]);
