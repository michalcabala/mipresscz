<?php

use App\Enums\UserRole;
use App\Models\User;
use Livewire\Livewire;
use MiPressCz\Core\Filament\Resources\Collections\Pages\CreateCollection;
use MiPressCz\Core\Filament\Resources\Collections\Pages\EditCollection;
use MiPressCz\Core\Filament\Resources\Collections\Pages\ListCollections;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Locale;
use MiPressCz\Core\Models\Taxonomy;

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

it('can render the collections list page', function () {
    Livewire::test(ListCollections::class)
        ->assertOk();
});

it('can see collection records in the table', function () {
    $collections = Collection::factory()->count(3)->create();

    Livewire::test(ListCollections::class)
        ->call('loadTable')
        ->assertCanSeeTableRecords($collections);
});

// -- Create page --

it('can render the collection create page', function () {
    Livewire::test(CreateCollection::class)
        ->assertOk();
});

it('can create a collection', function () {
    Livewire::test(CreateCollection::class)
        ->fillForm([
            'title' => 'Novinky',
            'handle' => 'novinky',
            'route_template' => '/{slug}',
            'sort_field' => 'order',
            'sort_direction' => 'asc',
            'date_behavior' => \MiPressCz\Core\Enums\DateBehavior::None,
            'default_status' => \MiPressCz\Core\Enums\DefaultStatus::Draft,
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Collection::where('handle', 'novinky')->exists())->toBeTrue();
});

it('validates required fields on collection create', function () {
    Livewire::test(CreateCollection::class)
        ->fillForm([
            'title' => null,
            'handle' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['title' => 'required', 'handle' => 'required']);
});

it('validates handle uniqueness on collection create', function () {
    Collection::factory()->create(['handle' => 'existing']);

    Livewire::test(CreateCollection::class)
        ->fillForm([
            'title' => 'Existing',
            'handle' => 'existing',
        ])
        ->call('create')
        ->assertHasFormErrors(['handle' => 'unique']);
});

// -- Edit page --

it('can render the collection edit page', function () {
    $collection = Collection::factory()->create();

    Livewire::test(EditCollection::class, [
        'record' => $collection->getRouteKey(),
    ])
        ->assertOk();
});

it('can update a collection', function () {
    $collection = Collection::factory()->create(['title' => 'Old Title']);

    Livewire::test(EditCollection::class, [
        'record' => $collection->getRouteKey(),
    ])
        ->fillForm([
            'title' => 'New Title',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($collection->fresh()->title)->toBe('New Title');
});

it('prevents attaching a taxonomy that is already assigned to another collection', function () {
    $primaryCollection = Collection::factory()->create(['title' => 'Články']);
    $secondaryCollection = Collection::factory()->create(['title' => 'Stránky']);
    $taxonomy = Taxonomy::factory()->create(['title' => 'Kategorie', 'handle' => 'categories']);
    $primaryCollection->taxonomies()->attach($taxonomy);

    Livewire::test(EditCollection::class, [
        'record' => $secondaryCollection->getRouteKey(),
    ])
        ->fillForm([
            'taxonomies' => [$taxonomy->getKey()],
        ])
        ->call('save')
        ->assertHasFormErrors(['taxonomies']);

    expect($secondaryCollection->fresh()->taxonomies)->toHaveCount(0)
        ->and($primaryCollection->fresh()->taxonomies->pluck('id')->all())->toBe([$taxonomy->getKey()]);
});

// -- Authorization --

it('prevents contributor from accessing collection list', function () {
    $contributor = User::factory()->create(['role' => UserRole::Contributor]);
    $contributor->syncRoles([UserRole::Contributor->value]);
    $this->actingAs($contributor);

    Livewire::test(ListCollections::class)
        ->assertForbidden();
});
