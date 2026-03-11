<?php

use App\Enums\UserRole;
use App\Models\User;
use App\Providers\Filament\AdminPanelProvider;
use MiPressCz\Core\Filament\Resources\Entries\EntryResource;
use MiPressCz\Core\Filament\Resources\Entries\EntryResourceConfiguration;
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

// -- EntryResourceConfiguration --

it('can create entry resource configuration with collection handle', function () {
    $config = EntryResource::make('pages')
        ->slug('pages')
        ->collectionHandle('pages')
        ->navigationLabel('Stránky')
        ->navigationIcon('fal-file-lines')
        ->navigationSort(1);

    expect($config)
        ->toBeInstanceOf(EntryResourceConfiguration::class)
        ->and($config->getCollectionHandle())->toBe('pages')
        ->and($config->getNavigationLabel())->toBe('Stránky')
        ->and($config->getNavigationIcon())->toBe('fal-file-lines')
        ->and($config->getNavigationSort())->toBe(1);
});

// -- Dynamic resource generation --

it('generates entry resources for active collections', function () {
    Collection::factory()->create(['name' => 'pages', 'handle' => 'pages', 'title' => 'Stránky', 'is_active' => true]);
    Collection::factory()->create(['name' => 'articles', 'handle' => 'articles', 'title' => 'Články', 'is_active' => true]);
    Collection::factory()->create(['name' => 'inactive', 'handle' => 'inactive', 'title' => 'Neaktivní', 'is_active' => false]);

    $provider = new AdminPanelProvider(app());
    $resources = (new \ReflectionMethod($provider, 'getCollectionResources'))->invoke($provider);

    expect($resources)->toHaveCount(2);

    $handles = collect($resources)->map(fn ($r) => $r->getCollectionHandle())->sort()->values()->all();
    expect($handles)->toBe(['articles', 'pages']);
});

it('returns empty array when no active collections exist', function () {
    $provider = new AdminPanelProvider(app());
    $resources = (new \ReflectionMethod($provider, 'getCollectionResources'))->invoke($provider);

    expect($resources)->toBeArray()->toBeEmpty();
});

it('generates correct navigation metadata for collection resources', function () {
    Collection::factory()->create([
        'name' => 'news',
        'handle' => 'news',
        'title' => 'Novinky',
        'icon' => 'fal-newspaper',
        'is_active' => true,
    ]);

    $provider = new AdminPanelProvider(app());
    $resources = (new \ReflectionMethod($provider, 'getCollectionResources'))->invoke($provider);

    expect($resources)->toHaveCount(1);

    $config = $resources[0];
    expect($config->getCollectionHandle())->toBe('news')
        ->and($config->getNavigationLabel())->toBe('Novinky')
        ->and($config->getNavigationIcon())->toBe('fal-newspaper')
        ->and($config->getNavigationSort())->toBe(1);
});

it('uses default icon when collection has no icon', function () {
    Collection::factory()->create([
        'name' => 'blog',
        'handle' => 'blog',
        'title' => 'Blog',
        'icon' => null,
        'is_active' => true,
    ]);

    $provider = new AdminPanelProvider(app());
    $resources = (new \ReflectionMethod($provider, 'getCollectionResources'))->invoke($provider);

    expect($resources[0]->getNavigationIcon())->toBe('fal-file-lines');
});
