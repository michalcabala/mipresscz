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
        'title' => 'Articles',
        'is_active' => true,
    ]);
    $this->blueprint = Blueprint::factory()->create([
        'collection_id' => $this->collection->id,
        'title' => 'Article',
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
        'title' => 'Original title',
    ]);

    $entry->update(['title' => 'Updated title']);

    Livewire::test(ManageEntryRevisions::class, ['record' => $entry->getKey()])
        ->assertOk()
        ->assertSee('Revize #2')
        ->assertSee('Revize #1')
        ->assertSee('Updated title');
});

it('can restore a revision from the revisions page', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Draft,
        'author_id' => $this->admin->id,
        'title' => 'Original title',
        'data' => ['headline' => 'Original headline'],
    ]);

    $entry->update([
        'title' => 'New title',
        'data' => ['headline' => 'New headline'],
    ]);

    $originalRevision = $entry->revisions()->where('revision_number', 1)->firstOrFail();

    Livewire::test(ManageEntryRevisions::class, ['record' => $entry->getKey()])
        ->mountAction('restoreRevision', ['revision' => $originalRevision->getKey()])
        ->callMountedAction();

    expect($entry->fresh()->title)->toBe('Original title')
        ->and($entry->fresh()->data)->toBe(['headline' => 'Original headline'])
        ->and($entry->fresh()->latestRevision->type->value)->toBe('rollback')
        ->and(Revision::query()->whereMorphedTo('revisionable', $entry)->count())->toBe(3);
});

it('allows contributors to access revisions only for their own entries', function () {
    $contributor = User::factory()->create(['role' => UserRole::Contributor]);
    $contributor->syncRoles([UserRole::Contributor->value]);
    $this->actingAs($contributor);

    $ownEntry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Draft,
        'author_id' => $contributor->id,
    ]);

    $otherEntry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Draft,
        'author_id' => $this->admin->id,
    ]);

    expect(ManageEntryRevisions::canAccess(['record' => $ownEntry->getKey()]))->toBeTrue()
        ->and(ManageEntryRevisions::canAccess(['record' => $otherEntry->getKey()]))->toBeFalse();
});

it('hides compare and restore controls for contributors', function () {
    $contributor = User::factory()->create(['role' => UserRole::Contributor]);
    $contributor->syncRoles([UserRole::Contributor->value]);
    $this->actingAs($contributor);

    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Draft,
        'author_id' => $contributor->id,
        'title' => 'Original title',
    ]);

    $entry->update(['title' => 'Updated title']);

    Livewire::test(ManageEntryRevisions::class, ['record' => $entry->getKey()])
        ->assertOk()
        ->assertDontSee(__('revisions.actions.compare'))
        ->assertDontSee(__('revisions.actions.restore'))
        ->assertDontSee(__('revisions.compare_heading'));
});

it('shows compare controls but hides restore controls for editors', function () {
    $editor = User::factory()->create(['role' => UserRole::Editor]);
    $editor->syncRoles([UserRole::Editor->value]);
    $this->actingAs($editor);

    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Draft,
        'author_id' => $editor->id,
        'title' => 'Original title',
    ]);

    Livewire::test(ManageEntryRevisions::class, ['record' => $entry->getKey()])
        ->assertOk()
        ->assertSee(__('revisions.actions.compare'))
        ->assertSee(__('revisions.compare_heading'))
        ->assertDontSee(__('revisions.actions.restore'));
});
