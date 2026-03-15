<?php

use App\Enums\UserRole;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use Filament\Tables\Enums\ColumnManagerLayout;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Livewire\Livewire;
use MiPressCz\Core\Filament\Resources\Terms\Pages\ListTerms;
use MiPressCz\Core\Models\Locale;
use MiPressCz\Core\Models\Taxonomy;

beforeEach(function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    (new \Database\Seeders\RolesAndPermissionsSeeder)->run();

    Locale::factory()->create([
        'code' => 'cs',
        'is_default' => true,
        'is_active' => true,
        'order' => 1,
    ]);
    locales()->clearCache();

    $this->superAdmin = User::factory()->create(['role' => UserRole::SuperAdmin]);
    $this->superAdmin->syncRoles([UserRole::SuperAdmin->value]);
    $this->actingAs($this->superAdmin);
});

it('renders filament table filters and column manager in slide-over modals with reorderable columns', function () {
    $component = Livewire::test(ListUsers::class)
        ->call('loadTable')
        ->assertOk();

    $table = $component->instance()->getTable();

    expect($table->getFiltersLayout())
        ->toBe(FiltersLayout::Modal)
        ->and($table->getFiltersTriggerAction()->isModalSlideOver())
        ->toBeTrue()
        ->and($table->getColumnManagerLayout())
        ->toBe(ColumnManagerLayout::Modal)
        ->and($table->getColumnManagerTriggerAction()->isModalSlideOver())
        ->toBeTrue()
        ->and($table->hasReorderableColumns())
        ->toBeTrue();
});

it('renders select filters as searchable non-native selects', function () {
    Taxonomy::factory()->create([
        'title' => 'Témata',
        'handle' => 'topics',
    ]);
    Locale::factory()->create([
        'code' => 'en',
        'is_active' => true,
        'order' => 2,
    ]);
    locales()->clearCache();

    Livewire::test(ListTerms::class)
        ->call('loadTable')
        ->assertOk()
        ->assertTableFilterExists('taxonomy_id', fn (SelectFilter $filter): bool => $filter->getSearchable() === true && ! $filter->isNative())
        ->assertTableFilterExists('locale', fn (SelectFilter $filter): bool => $filter->getSearchable() === true && ! $filter->isNative());
});
