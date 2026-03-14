<?php

use App\Enums\UserRole;
use App\Models\User;
use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\Revision;

beforeEach(function () {
    (new \Database\Seeders\RolesAndPermissionsSeeder)->run();

    $this->collection = Collection::factory()->create(['revisions_enabled' => true]);
    $this->blueprint = Blueprint::factory()->create(['collection_id' => $this->collection->id]);
    $this->user = User::factory()->create(['role' => UserRole::Admin]);
    $this->user->syncRoles([UserRole::Admin->value]);
    $this->actingAs($this->user);
});

function wcPublishedEntry(array $overrides = []): Entry
{
    return Entry::factory()->create(array_merge([
        'collection_id' => test()->collection->id,
        'blueprint_id' => test()->blueprint->id,
        'author_id' => test()->user->id,
        'status' => EntryStatus::Published,
        'published_at' => now(),
        'title' => 'Original Title',
        'data' => ['summary' => 'original'],
        'content' => [['type' => 'text', 'data' => ['text' => 'original content']]],
    ], $overrides));
}

function wcDraftEntry(array $overrides = []): Entry
{
    return Entry::factory()->create(array_merge([
        'collection_id' => test()->collection->id,
        'blueprint_id' => test()->blueprint->id,
        'author_id' => test()->user->id,
        'status' => EntryStatus::Draft,
        'title' => 'Draft Title',
    ], $overrides));
}

// ── HasWorkingCopy trait ──

it('hasWorkingCopy returns false when no working copy exists', function () {
    $entry = wcPublishedEntry();

    expect($entry->hasWorkingCopy())->toBeFalse();
});

it('makeWorkingCopy creates a working copy revision', function () {
    $entry = wcPublishedEntry();

    $wc = $entry->makeWorkingCopy();

    expect($wc)
        ->action->toBe('working')
        ->title->toBe('Original Title')
        ->is_current->toBeFalse()
        ->entry_id->toBe($entry->id);

    expect($entry->hasWorkingCopy())->toBeTrue();
});

it('makeWorkingCopy updates existing working copy instead of creating a new one', function () {
    $entry = wcPublishedEntry();

    $wc1 = $entry->makeWorkingCopy();
    $entry->title = 'Updated Title';
    $wc2 = $entry->makeWorkingCopy();

    expect($wc2->id)->toBe($wc1->id)
        ->and($wc2->title)->toBe('Updated Title')
        ->and(Revision::where('entry_id', $entry->id)->where('action', 'working')->count())->toBe(1);
});

it('saveToWorkingCopy creates or updates a working copy with custom attributes', function () {
    $entry = wcPublishedEntry();

    $wc = $entry->saveToWorkingCopy([
        'title' => 'WC Title',
        'data' => ['summary' => 'wc summary'],
        'content' => [['type' => 'text', 'data' => ['text' => 'wc content']]],
    ]);

    expect($wc)
        ->action->toBe('working')
        ->title->toBe('WC Title');

    expect($wc->data)->toBe(['summary' => 'wc summary']);
    expect($wc->content)->toBe([['type' => 'text', 'data' => ['text' => 'wc content']]]);
});

it('workingCopy returns the working copy revision', function () {
    $entry = wcPublishedEntry();

    $entry->makeWorkingCopy();
    $wc = $entry->workingCopy();

    expect($wc)
        ->not->toBeNull()
        ->action->toBe('working')
        ->entry_id->toBe($entry->id);
});

it('publishWorkingCopy applies working copy data to entry', function () {
    $entry = wcPublishedEntry();

    $entry->saveToWorkingCopy([
        'title' => 'WC Published Title',
        'data' => ['summary' => 'wc published'],
        'content' => [['type' => 'text', 'data' => ['text' => 'published content']]],
    ]);

    $entry->publishWorkingCopy($this->user, 'Publishing changes');

    $entry->refresh();

    expect($entry->title)->toBe('WC Published Title')
        ->and($entry->data)->toBe(['summary' => 'wc published'])
        ->and($entry->content)->toBe([['type' => 'text', 'data' => ['text' => 'published content']]])
        ->and($entry->status)->toBe(EntryStatus::Published)
        ->and($entry->hasWorkingCopy())->toBeFalse();
});

it('publishWorkingCopy creates a publish revision snapshot', function () {
    $entry = wcPublishedEntry();
    $entry->saveToWorkingCopy(['title' => 'WC Title']);

    $entry->publishWorkingCopy($this->user, 'Publish message');

    $publishRevision = Revision::where('entry_id', $entry->id)
        ->where('action', 'publish')
        ->first();

    expect($publishRevision)
        ->not->toBeNull()
        ->title->toBe('WC Title')
        ->message->toBe('Publish message')
        ->is_current->toBeTrue();
});

it('publishWorkingCopy does nothing when no working copy exists', function () {
    $entry = wcPublishedEntry();

    $result = $entry->publishWorkingCopy();

    expect($result->id)->toBe($entry->id)
        ->and($result->title)->toBe('Original Title');
});

it('deleteWorkingCopy removes the working copy', function () {
    $entry = wcPublishedEntry();
    $entry->makeWorkingCopy();

    expect($entry->hasWorkingCopy())->toBeTrue();

    $entry->deleteWorkingCopy();

    expect($entry->hasWorkingCopy())->toBeFalse();
});

it('deleteWorkingCopy returns false when no working copy exists', function () {
    $entry = wcPublishedEntry();

    expect($entry->deleteWorkingCopy())->toBeFalse();
});

it('fromWorkingCopy returns a clone with working copy data applied', function () {
    $entry = wcPublishedEntry();

    $entry->saveToWorkingCopy([
        'title' => 'WC Clone Title',
        'data' => ['summary' => 'wc clone'],
    ]);

    $clone = $entry->fromWorkingCopy();

    expect($clone->title)->toBe('WC Clone Title')
        ->and($clone->data)->toBe(['summary' => 'wc clone'])
        ->and($clone->id)->toBe($entry->id);

    // Original entry is unchanged
    expect($entry->title)->toBe('Original Title');
});

it('fromWorkingCopy returns self when no working copy exists', function () {
    $entry = wcPublishedEntry();

    $clone = $entry->fromWorkingCopy();

    expect($clone->title)->toBe($entry->title);
});

// ── Revision model scopes ──

it('workingCopy scope filters only working copy revisions', function () {
    $entry = wcPublishedEntry();

    // Observer creates a revision on entry creation
    $existingRevisions = $entry->revisions()->count();

    $entry->saveToWorkingCopy(['title' => 'WC']);

    expect($entry->revisions()->workingCopy()->count())->toBe(1)
        ->and($entry->revisions()->history()->count())->toBe($existingRevisions);
});

it('history scope excludes working copies', function () {
    $entry = wcPublishedEntry();
    $historyBefore = $entry->revisions()->history()->count();

    $entry->makeWorkingCopy();

    expect($entry->revisions()->history()->count())->toBe($historyBefore);
});

// ── Observer creates revisions with content ──

it('observer stores content in revision on entry creation', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'author_id' => $this->user->id,
        'content' => [['type' => 'heading', 'data' => ['text' => 'Hello']]],
    ]);

    $revision = $entry->revisions()->latest('created_at')->first();

    expect($revision->content)->toBe([['type' => 'heading', 'data' => ['text' => 'Hello']]]);
});

it('observer stores content in revision on entry update', function () {
    $entry = wcPublishedEntry();

    $entry->content = [['type' => 'updated', 'data' => ['text' => 'new']]];
    $entry->save();

    $revision = $entry->revisions()->where('is_current', true)->first();

    expect($revision->content)->toBe([['type' => 'updated', 'data' => ['text' => 'new']]]);
});

// ── EntryPolicy::publish ──

it('admin can publish entries', function () {
    $entry = wcPublishedEntry();

    expect($this->user->can('publish', $entry))->toBeTrue();
});

it('editor can publish entries', function () {
    $editor = User::factory()->create(['role' => UserRole::Editor]);
    $editor->syncRoles([UserRole::Editor->value]);
    $entry = wcPublishedEntry();

    expect($editor->can('publish', $entry))->toBeTrue();
});

it('contributor cannot publish entries', function () {
    $contributor = User::factory()->create(['role' => UserRole::Contributor]);
    $contributor->syncRoles([UserRole::Contributor->value]);
    $entry = wcPublishedEntry(['author_id' => $contributor->id]);

    expect($contributor->can('publish', $entry))->toBeFalse();
});

it('superadmin can publish entries via Gate::before bypass', function () {
    $superadmin = User::factory()->create(['role' => UserRole::SuperAdmin]);
    $superadmin->syncRoles([UserRole::SuperAdmin->value]);
    $entry = wcPublishedEntry();

    expect($superadmin->can('publish', $entry))->toBeTrue();
});

// ── Draft entries bypass working copy ──

it('draft entry does not use working copy workflow', function () {
    $draft = wcDraftEntry();

    expect($draft->status)->toBe(EntryStatus::Draft);

    // Direct save should work (observer creates normal revision)
    $draft->title = 'Updated draft';
    $draft->save();

    $draft->refresh();
    expect($draft->title)->toBe('Updated draft')
        ->and($draft->hasWorkingCopy())->toBeFalse();
});

// ── Collection without revisions bypasses working copy ──

it('entry in collection without revisions does not use working copy', function () {
    $noRevCollection = Collection::factory()->create(['revisions_enabled' => false]);
    $bp = Blueprint::factory()->create(['collection_id' => $noRevCollection->id]);

    $entry = Entry::factory()->create([
        'collection_id' => $noRevCollection->id,
        'blueprint_id' => $bp->id,
        'author_id' => $this->user->id,
        'status' => EntryStatus::Published,
        'published_at' => now(),
    ]);

    // Direct save should work, no revisions created
    $entry->title = 'Updated';
    $entry->save();

    expect($entry->revisions()->count())->toBe(0)
        ->and($entry->hasWorkingCopy())->toBeFalse();
});

// ── Revision action attribute ──

it('revision created by observer has action set to revision', function () {
    $entry = wcPublishedEntry();

    $revision = $entry->revisions()->latest('created_at')->first();

    expect($revision->action)->toBe('revision');
});

it('isWorkingCopy returns true for working copy revision', function () {
    $entry = wcPublishedEntry();

    $wc = $entry->makeWorkingCopy();

    expect($wc->isWorkingCopy())->toBeTrue();
});

it('isWorkingCopy returns false for regular revision', function () {
    $entry = wcPublishedEntry();

    $revision = $entry->revisions()->history()->first();

    expect($revision->isWorkingCopy())->toBeFalse();
});
