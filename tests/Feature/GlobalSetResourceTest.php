<?php

use App\Enums\UserRole;
use App\Models\User;
use Livewire\Livewire;
use MiPressCz\Core\Filament\Resources\Globals\Pages\CreateGlobalSet;
use MiPressCz\Core\Filament\Resources\Globals\Pages\EditGlobalSet;
use MiPressCz\Core\Filament\Resources\Globals\Pages\ListGlobalSets;
use MiPressCz\Core\Models\GlobalSet;
use MiPressCz\Core\Models\Locale;

beforeEach(function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    (new \Database\Seeders\RolesAndPermissionsSeeder)->run();
    Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'is_active' => true, 'order' => 1]);
    locales()->clearCache();

    $this->admin = User::factory()->create(['role' => UserRole::Admin]);
    $this->admin->syncRoles([UserRole::Admin->value]);
    $this->actingAs($this->admin);
});

// -- List page --

it('can render the global sets list page', function () {
    Livewire::test(ListGlobalSets::class)
        ->assertOk();
});

it('can see global set records in the table', function () {
    $globalSets = GlobalSet::factory()->count(3)->create();

    Livewire::test(ListGlobalSets::class)
        ->call('loadTable')
        ->assertCanSeeTableRecords($globalSets);
});

// -- Create page --

it('can render the global set create page', function () {
    Livewire::test(CreateGlobalSet::class)
        ->assertOk();
});

it('can create a global set', function () {
    Livewire::test(CreateGlobalSet::class)
        ->fillForm([
            'title' => 'Site Settings',
            'handle' => 'site_settings',
            'locale' => 'cs',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(GlobalSet::where('handle', 'site_settings')->exists())->toBeTrue();
});

it('validates required fields on global set create', function () {
    Livewire::test(CreateGlobalSet::class)
        ->fillForm([
            'title' => null,
            'handle' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['title' => 'required', 'handle' => 'required']);
});

it('validates handle uniqueness on global set create', function () {
    GlobalSet::factory()->create(['handle' => 'existing']);

    Livewire::test(CreateGlobalSet::class)
        ->fillForm([
            'title' => 'Existing',
            'handle' => 'existing',
        ])
        ->call('create')
        ->assertHasFormErrors(['handle' => 'unique']);
});

// -- Edit page --

it('can render the global set edit page', function () {
    $globalSet = GlobalSet::factory()->create();

    Livewire::test(EditGlobalSet::class, [
        'record' => $globalSet->getRouteKey(),
    ])
        ->assertOk();
});

it('can update a global set', function () {
    $globalSet = GlobalSet::factory()->create(['title' => 'Old Title']);

    Livewire::test(EditGlobalSet::class, [
        'record' => $globalSet->getRouteKey(),
    ])
        ->fillForm([
            'title' => 'New Title',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($globalSet->fresh()->title)->toBe('New Title');
});

// -- Authorization --

it('prevents contributor from accessing global set list', function () {
    $contributor = User::factory()->create(['role' => UserRole::Contributor]);
    $contributor->syncRoles([UserRole::Contributor->value]);
    $this->actingAs($contributor);

    Livewire::test(ListGlobalSets::class)
        ->assertForbidden();
});
