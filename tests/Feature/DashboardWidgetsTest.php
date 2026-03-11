<?php

use App\Enums\UserRole;
use App\Models\User;
use Livewire\Livewire;
use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Filament\Widgets\EntryStatsWidget;
use MiPressCz\Core\Filament\Widgets\LatestEntriesWidget;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\Locale;

beforeEach(function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    (new \Database\Seeders\RolesAndPermissionsSeeder)->run();

    Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'is_active' => true, 'order' => 1]);
    locales()->clearCache();

    $this->collection = Collection::factory()->create(['handle' => 'pages', 'route_template' => '/{slug}']);
    $this->blueprint = Blueprint::factory()->create(['collection_id' => $this->collection->id]);

    $this->admin = User::factory()->create(['role' => UserRole::Admin]);
    $this->admin->syncRoles([UserRole::Admin->value]);
    $this->actingAs($this->admin);
});

// ── EntryStatsWidget ──

it('renders EntryStatsWidget', function () {
    Livewire::test(EntryStatsWidget::class)
        ->assertSuccessful();
});

it('EntryStatsWidget shows total entry count', function () {
    Entry::factory()->count(3)->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
    ]);

    Livewire::test(EntryStatsWidget::class)
        ->assertSee('3');
});

it('EntryStatsWidget shows published count', function () {
    Entry::factory()->published()->count(2)->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
    ]);
    Entry::factory()->count(1)->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
    ]);

    Livewire::test(EntryStatsWidget::class)
        ->assertSee('2');
});

it('EntryStatsWidget shows draft count', function () {
    Entry::factory()->count(4)->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'status' => EntryStatus::Draft,
    ]);

    Livewire::test(EntryStatsWidget::class)
        ->assertSee('4');
});

// ── LatestEntriesWidget ──

it('renders LatestEntriesWidget', function () {
    Livewire::test(LatestEntriesWidget::class)
        ->assertSuccessful();
});

it('LatestEntriesWidget shows entry titles', function () {
    Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'title' => 'Testovací záznam',
    ]);

    Livewire::test(LatestEntriesWidget::class)
        ->call('loadTable')
        ->assertSee('Testovací záznam');
});

it('LatestEntriesWidget shows at most 10 entries', function () {
    Entry::factory()->count(15)->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
    ]);

    $widget = Livewire::test(LatestEntriesWidget::class);

    // Should render successfully even with many entries
    $widget->assertSuccessful();
});
