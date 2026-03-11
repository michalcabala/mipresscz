<?php

use App\Enums\UserRole;
use App\Models\User;
use Livewire\Livewire;
use MiPressCz\Core\Filament\Resources\Entries\Pages\EditEntry;
use MiPressCz\Core\Filament\Resources\Entries\Pages\ListEntries;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\Locale;

beforeEach(function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    (new \Database\Seeders\RolesAndPermissionsSeeder)->run();

    $this->locale = Locale::factory()->create([
        'code' => 'cs',
        'is_default' => true,
        'is_active' => true,
        'order' => 1,
    ]);
    locales()->clearCache();

    $this->collection = Collection::factory()->create([
        'handle' => 'pages',
        'title' => 'Stránky',
        'is_active' => true,
    ]);
    $this->blueprint = Blueprint::factory()->create([
        'collection_id' => $this->collection->id,
        'title' => 'Stránka',
        'handle' => 'page',
        'is_default' => true,
    ]);

    $this->admin = User::factory()->create(['role' => UserRole::Admin]);
    $this->admin->syncRoles([UserRole::Admin->value]);
    $this->actingAs($this->admin);
});

// ── Homepage flag ──────────────────────────────────────────────────────────

it('defaults is_homepage to false on new entries', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
    ]);

    expect($entry->fresh()->is_homepage)->toBeFalse();
});

it('can set an entry as homepage via table action', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'is_homepage' => false,
    ]);

    Livewire::test(ListEntries::class)
        ->call('loadTable')
        ->callTableAction('set_homepage', $entry)
        ->assertNotified();

    expect($entry->fresh()->is_homepage)->toBeTrue();
});

it('resets previous homepage when setting a new one', function () {
    $first = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'is_homepage' => true,
    ]);
    $second = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'is_homepage' => false,
    ]);

    Livewire::test(ListEntries::class)
        ->call('loadTable')
        ->callTableAction('set_homepage', $second)
        ->assertNotified();

    expect($first->fresh()->is_homepage)->toBeFalse();
    expect($second->fresh()->is_homepage)->toBeTrue();
});

it('hides set_homepage table action for the current homepage entry', function () {
    $homepage = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'is_homepage' => true,
    ]);
    $other = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'is_homepage' => false,
    ]);

    Livewire::test(ListEntries::class)
        ->call('loadTable')
        ->assertTableActionHidden('set_homepage', $homepage)
        ->assertTableActionVisible('set_homepage', $other);
});

it('cannot delete the homepage entry from the list table', function () {
    $homepage = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'is_homepage' => true,
    ]);

    Livewire::test(ListEntries::class)
        ->call('loadTable')
        ->callTableAction('delete', $homepage)
        ->assertNotified();

    $this->assertDatabaseHas(Entry::class, ['id' => $homepage->id, 'is_homepage' => true]);
});

it('can delete a non-homepage entry from the list table', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'is_homepage' => false,
    ]);

    Livewire::test(ListEntries::class)
        ->call('loadTable')
        ->callTableAction('delete', $entry)
        ->assertNotified();

    $this->assertSoftDeleted(Entry::class, ['id' => $entry->id]);
});

it('cannot delete the homepage entry from the edit page', function () {
    $homepage = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'is_homepage' => true,
    ]);

    Livewire::test(EditEntry::class, ['record' => $homepage->id])
        ->callAction('delete')
        ->assertNotified();

    $this->assertDatabaseHas(Entry::class, ['id' => $homepage->id]);
});

it('can set entry as homepage from the edit page', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'is_homepage' => false,
    ]);

    Livewire::test(EditEntry::class, ['record' => $entry->id])
        ->callAction('set_homepage')
        ->assertNotified();

    expect($entry->fresh()->is_homepage)->toBeTrue();
});
