<?php

declare(strict_types=1);

use App\Models\User;
use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Enums\RevisionType;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\Revision;

beforeEach(function () {
    $this->collection = Collection::factory()->create([
        'handle' => 'articles',
        'route_template' => '/articles/{slug}',
    ]);
    $this->blueprint = Blueprint::factory()->create([
        'collection_id' => $this->collection->id,
        'is_default' => true,
        'use_mason' => true,
    ]);
});

it('creates a draft revision automatically when an entry is created', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'title' => 'First draft',
        'data' => ['headline' => 'Draft headline'],
        'content' => [['type' => 'hero', 'attrs' => ['config' => ['heading' => 'Draft heading']]]],
    ]);

    $revision = $entry->revisions()->first();

    expect($entry->revisions)->toHaveCount(1)
        ->and($revision)->toBeInstanceOf(Revision::class)
        ->and($revision->type)->toBe(RevisionType::Draft)
        ->and($revision->revision_number)->toBe(1)
        ->and($revision->user_id)->toBe($user->id)
        ->and($revision->content['title'])->toBe('First draft')
        ->and($revision->content['data'])->toBe(['headline' => 'Draft headline']);
});

it('creates a published revision automatically for published entries', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'status' => EntryStatus::Published,
        'published_at' => now(),
    ]);

    expect($entry->latestRevision->type)->toBe(RevisionType::Published)
        ->and($entry->latestRevision->revision_number)->toBe(1);
});

it('creates a new revision automatically when an entry is updated', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'title' => 'Original title',
    ]);

    $entry->update([
        'title' => 'Updated title',
        'data' => ['headline' => 'Updated headline'],
    ]);

    $revisions = $entry->fresh()->revisions;

    expect($revisions)->toHaveCount(2)
        ->and($revisions->pluck('revision_number')->all())->toBe([2, 1])
        ->and($revisions->first()->content['title'])->toBe('Updated title')
        ->and($revisions->first()->type)->toBe(RevisionType::Draft);
});

it('increments revision numbers per revisionable model', function () {
    $firstEntry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
    ]);
    $secondEntry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
    ]);

    $firstEntry->update(['title' => 'First entry updated']);

    expect($firstEntry->fresh()->revisions->pluck('revision_number')->all())->toBe([2, 1])
        ->and($secondEntry->fresh()->revisions->pluck('revision_number')->all())->toBe([1]);
});

it('can create a manual revision with a note', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
    ]);

    $revision = $entry->createRevision(RevisionType::Autosave, 'Background save');

    expect($entry->fresh()->revisions)->toHaveCount(2)
        ->and($revision->type)->toBe(RevisionType::Autosave)
        ->and($revision->note)->toBe('Background save')
        ->and($revision->revision_number)->toBe(2);
});

it('latestRevision resolves the newest revision', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
    ]);

    $entry->update(['title' => 'Newer title']);

    expect($entry->fresh()->latestRevision)->not->toBeNull()
        ->and($entry->fresh()->latestRevision->revision_number)->toBe(2)
        ->and($entry->fresh()->latestRevision->content['title'])->toBe('Newer title');
});

it('prunes old draft revisions automatically when max_revisions is configured', function () {
    config()->set('mipress-revisions.max_revisions', 2);

    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'title' => 'Revision 1',
    ]);

    $entry->update(['title' => 'Revision 2']);
    $entry->update(['title' => 'Revision 3']);

    expect($entry->fresh()->revisions)->toHaveCount(2)
        ->and($entry->fresh()->revisions->pluck('revision_number')->all())->toBe([3, 2]);
});
