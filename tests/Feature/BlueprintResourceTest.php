<?php

use App\Enums\UserRole;
use App\Models\User;
use Livewire\Livewire;
use MiPressCz\Core\Filament\Resources\Blueprints\Pages\CreateBlueprint;
use MiPressCz\Core\Filament\Resources\Blueprints\Pages\EditBlueprint;
use MiPressCz\Core\Filament\Resources\Blueprints\Pages\ListBlueprints;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
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

it('can render the blueprints list page', function () {
    Livewire::test(ListBlueprints::class)
        ->assertOk();
});

it('can see blueprint records in the table', function () {
    $blueprints = Blueprint::factory()->count(3)->create();

    Livewire::test(ListBlueprints::class)
        ->call('loadTable')
        ->assertCanSeeTableRecords($blueprints);
});

// -- Create page --

it('can render the blueprint create page', function () {
    Livewire::test(CreateBlueprint::class)
        ->assertOk();
});

it('can create a blueprint', function () {
    $collection = Collection::factory()->create();

    Livewire::test(CreateBlueprint::class)
        ->fillForm([
            'title' => 'Article',
            'handle' => 'article',
            'collection_id' => $collection->id,
            'is_default' => true,
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Blueprint::where('handle', 'article')->exists())->toBeTrue();
});

it('validates required fields on blueprint create', function () {
    Livewire::test(CreateBlueprint::class)
        ->fillForm([
            'title' => null,
            'handle' => null,
            'collection_id' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['title' => 'required', 'handle' => 'required', 'collection_id' => 'required']);
});

// -- Edit page --

it('can render the blueprint edit page', function () {
    $blueprint = Blueprint::factory()->create();

    Livewire::test(EditBlueprint::class, [
        'record' => $blueprint->getRouteKey(),
    ])
        ->assertOk();
});

it('can update a blueprint', function () {
    $blueprint = Blueprint::factory()->create(['title' => 'Old Title']);

    Livewire::test(EditBlueprint::class, [
        'record' => $blueprint->getRouteKey(),
    ])
        ->fillForm([
            'title' => 'New Title',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($blueprint->fresh()->title)->toBe('New Title');
});

// -- Authorization --

it('prevents contributor from accessing blueprint list', function () {
    $contributor = User::factory()->create(['role' => UserRole::Contributor]);
    $contributor->syncRoles([UserRole::Contributor->value]);
    $this->actingAs($contributor);

    Livewire::test(ListBlueprints::class)
        ->assertForbidden();
});
