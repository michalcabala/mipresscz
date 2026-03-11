<?php

use App\Enums\UserRole;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    // BreezyCore::$scopeTwoFactorAuthenticationToPanel is a typed property that must be
    // initialized before BreezySession::booted() is called (triggered on User deletion).
    filament('filament-breezy')->enableTwoFactorAuthentication(condition: false, scopeToPanel: false);

    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    (new \Database\Seeders\RolesAndPermissionsSeeder)->run();

    $this->superAdmin = User::factory()->create(['role' => UserRole::SuperAdmin]);
    $this->superAdmin->syncRoles([UserRole::SuperAdmin->value]);
    $this->actingAs($this->superAdmin);
});

// ── Soft delete ─────────────────────────────────────────────────────────────

it('can soft-delete a user', function () {
    $user = User::factory()->create(['role' => UserRole::Editor]);

    Livewire::test(ListUsers::class)
        ->call('loadTable')
        ->callTableAction('delete', $user)
        ->assertNotified();

    $this->assertSoftDeleted(User::class, ['id' => $user->id]);
});

it('cannot soft-delete own account', function () {
    Livewire::test(ListUsers::class)
        ->call('loadTable')
        ->callTableAction('delete', $this->superAdmin)
        ->assertNotified();

    $this->assertDatabaseHas(User::class, ['id' => $this->superAdmin->id, 'deleted_at' => null]);
});

it('can restore a soft-deleted user', function () {
    $user = User::factory()->create(['role' => UserRole::Editor]);
    $user->delete();

    Livewire::test(ListUsers::class)
        ->call('loadTable')
        ->filterTable('trashed', 'only')
        ->callTableAction('restore', $user)
        ->assertNotified();

    expect($user->fresh()->trashed())->toBeFalse();
});

it('can force-delete a soft-deleted user', function () {
    $user = User::factory()->create(['role' => UserRole::Editor]);
    $user->delete();

    Livewire::test(ListUsers::class)
        ->call('loadTable')
        ->filterTable('trashed', 'only')
        ->callTableAction('forceDelete', $user)
        ->assertNotified();

    $this->assertDatabaseMissing(User::class, ['id' => $user->id]);
});

it('cannot force-delete own account', function () {
    $this->superAdmin->delete();

    Livewire::test(ListUsers::class)
        ->call('loadTable')
        ->filterTable('trashed', 'only')
        ->callTableAction('forceDelete', $this->superAdmin)
        ->assertNotified();

    $this->assertSoftDeleted(User::class, ['id' => $this->superAdmin->id]);
});

it('hides edit and delete actions for trashed users', function () {
    $user = User::factory()->create(['role' => UserRole::Editor]);
    $user->delete();

    Livewire::test(ListUsers::class)
        ->call('loadTable')
        ->filterTable('trashed', 'only')
        ->assertTableActionHidden('edit', $user)
        ->assertTableActionHidden('delete', $user);
});

it('hides restore and force-delete actions for active users', function () {
    $user = User::factory()->create(['role' => UserRole::Editor]);

    Livewire::test(ListUsers::class)
        ->call('loadTable')
        ->assertTableActionHidden('restore', $user)
        ->assertTableActionHidden('forceDelete', $user);
});
