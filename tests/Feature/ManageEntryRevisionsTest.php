<?php

use App\Enums\UserRole;
use App\Models\User;
use Livewire\Livewire;
use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Filament\Resources\Entries\Pages\ManageEntryRevisions;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\Locale;
use MiPressCz\Core\Models\Revision;

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
        'handle' => 'articles',
        'title' => 'Články',
        'is_active' => true,
    ]);
    $this->blueprint = Blueprint::factory()->create([
        'collection_id' => $this->collection->id,
        'title' => 'Článek',
        'handle' => 'article',
        'is_default' => true,
    ]);

    $this->admin = User::factory()->create(['role' => UserRole::Admin]);
    $this->admin->syncRoles([UserRole::Admin->value]);
    $this->actingAs($this->admin);
});

it('can render the revisions page for an entry', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Draft,
        'author_id' => $this->admin->id,
        'title' => 'Původní název',
    ]);

    $entry->update(['title' => 'Aktualizovaný název']);

    Livewire::test(ManageEntryRevisions::class, ['record' => $entry->getKey()])
        ->assertOk()
        ->assertSee('Revize #2')
        ->assertSee('Revize #1')
        ->assertSee('Aktualizovaný název');
});

it('can restore a revision from the revisions page', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Draft,
        'author_id' => $this->admin->id,
        'title' => 'Původní název',
        'data' => ['headline' => 'Původní headline'],
    ]);

    $entry->update([
        'title' => 'Nový název',
        'data' => ['headline' => 'Nový headline'],
    ]);

    $originalRevision = $entry->revisions()->where('revision_number', 1)->firstOrFail();

    Livewire::test(ManageEntryRevisions::class, ['record' => $entry->getKey()])
        ->mountAction('restoreRevision', ['revision' => $originalRevision->getKey()])
        ->callMountedAction();

    expect($entry->fresh()->title)->toBe('Původní název')
        ->and($entry->fresh()->data)->toBe(['headline' => 'Původní headline'])
        ->and($entry->fresh()->latestRevision->type->value)->toBe('rollback')
        ->and(Revision::query()->whereMorphedTo('revisionable', $entry)->count())->toBe(3);
});
