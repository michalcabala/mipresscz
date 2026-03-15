<?php

use App\Enums\UserRole;
use App\Models\User;
use Livewire\Livewire;
use MiPressCz\Core\Filament\Resources\Taxonomies\Pages\CreateTaxonomy;
use MiPressCz\Core\Filament\Resources\Taxonomies\Pages\EditTaxonomy;
use MiPressCz\Core\Filament\Resources\Taxonomies\Pages\ListTaxonomies;
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

it('can render the taxonomies list page', function () {
    Livewire::test(ListTaxonomies::class)
        ->assertOk();
});

it('can see taxonomy records in the table', function () {
    $taxonomies = Taxonomy::factory()->count(3)->create();

    Livewire::test(ListTaxonomies::class)
        ->call('loadTable')
        ->assertCanSeeTableRecords($taxonomies);
});

// -- Create page --

it('can render the taxonomy create page', function () {
    Livewire::test(CreateTaxonomy::class)
        ->assertOk();
});

it('can create a taxonomy assigned to one collection', function () {
    $collection = Collection::factory()->create();

    Livewire::test(CreateTaxonomy::class)
        ->fillForm([
            'title' => 'Kategorie',
            'handle' => 'kategorie',
            'collection_id' => $collection->getKey(),
            'is_hierarchical' => true,
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $taxonomy = Taxonomy::where('handle', 'kategorie')->first();

    expect($taxonomy)->not->toBeNull()
        ->and($taxonomy->collections()->pluck('collections.id')->all())->toBe([$collection->getKey()]);
});

it('validates required fields on taxonomy create', function () {
    Livewire::test(CreateTaxonomy::class)
        ->fillForm([
            'title' => null,
            'handle' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['title' => 'required', 'handle' => 'required']);
});

it('validates handle uniqueness on taxonomy create', function () {
    Taxonomy::factory()->create(['handle' => 'existing']);

    Livewire::test(CreateTaxonomy::class)
        ->fillForm([
            'title' => 'Existing',
            'handle' => 'existing',
        ])
        ->call('create')
        ->assertHasFormErrors(['handle' => 'unique']);
});

// -- Edit page --

it('can render the taxonomy edit page', function () {
    $taxonomy = Taxonomy::factory()->create();

    Livewire::test(EditTaxonomy::class, [
        'record' => $taxonomy->getRouteKey(),
    ])
        ->assertOk();
});

it('can update a taxonomy', function () {
    $firstCollection = Collection::factory()->create(['title' => 'Články']);
    $secondCollection = Collection::factory()->create(['title' => 'Stránky']);
    $taxonomy = Taxonomy::factory()->create(['title' => 'Old Title']);
    $taxonomy->collections()->attach($firstCollection);

    Livewire::test(EditTaxonomy::class, [
        'record' => $taxonomy->getRouteKey(),
    ])
        ->fillForm([
            'title' => 'New Title',
            'collection_id' => $secondCollection->getKey(),
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($taxonomy->fresh()->title)->toBe('New Title')
        ->and($taxonomy->fresh()->collections()->pluck('collections.id')->all())->toBe([$secondCollection->getKey()]);
});

// -- Authorization --

it('allows contributor to view taxonomy list', function () {
    $contributor = User::factory()->create(['role' => UserRole::Contributor]);
    $contributor->syncRoles([UserRole::Contributor->value]);
    $this->actingAs($contributor);

    Livewire::test(ListTaxonomies::class)
        ->assertOk();
});

it('prevents contributor from creating a taxonomy', function () {
    $contributor = User::factory()->create(['role' => UserRole::Contributor]);
    $contributor->syncRoles([UserRole::Contributor->value]);
    $this->actingAs($contributor);

    Livewire::test(CreateTaxonomy::class)
        ->assertForbidden();
});
