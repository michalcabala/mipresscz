<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Enums\RevisionType;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\Revision;
use MiPressCz\Core\Services\RevisionService;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->service = app(RevisionService::class);
    $this->collection = Collection::factory()->create([
        'handle' => 'articles',
        'route_template' => '/articles/{slug}',
    ]);
    $this->blueprint = Blueprint::factory()->create([
        'collection_id' => $this->collection->id,
        'is_default' => true,
    ]);
});

it('creates a revision through the service', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
    ]);

    $revision = $this->service->createRevision($entry, RevisionType::Autosave, 'Autosave from service');

    expect($revision)->toBeInstanceOf(Revision::class)
        ->and($revision->type)->toBe(RevisionType::Autosave)
        ->and($revision->note)->toBe('Autosave from service')
        ->and($revision->user_id)->toBe($user->id)
        ->and($revision->revision_number)->toBe(2);
});

it('restores a revision and creates a rollback revision', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'title' => 'Original title',
        'status' => EntryStatus::Draft,
    ]);

    $entry->update([
        'title' => 'Updated title',
        'data' => ['headline' => 'Updated headline'],
    ]);

    $originalRevision = $entry->revisions()->where('revision_number', 1)->firstOrFail();

    $restored = $this->service->restoreRevision($originalRevision);

    expect($restored->title)->toBe('Original title')
        ->and($restored->data)->toBe([])
        ->and($restored->revisions()->count())->toBe(3)
        ->and($restored->latestRevision->type)->toBe(RevisionType::Rollback)
        ->and($restored->latestRevision->note)->toBe('Restored from revision #1')
        ->and($restored->latestRevision->revision_number)->toBe(3);
});

it('produces a structured diff between revisions', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'title' => 'Original title',
        'data' => [
            'headline' => 'Original headline',
            'obsolete' => 'To be removed',
        ],
    ]);

    $entry->update([
        'title' => 'Updated title',
        'data' => [
            'headline' => 'Updated headline',
            'new_field' => 'Added value',
        ],
    ]);

    $oldRevision = $entry->revisions()->where('revision_number', 1)->firstOrFail();
    $newRevision = $entry->revisions()->where('revision_number', 2)->firstOrFail();
    $diff = $this->service->diffRevisions($oldRevision, $newRevision);

    expect($diff['added'])->toContain([
        'field' => 'data.new_field',
        'old' => null,
        'new' => 'Added value',
    ])->and($diff['removed'])->toContain([
        'field' => 'data.obsolete',
        'old' => 'To be removed',
        'new' => null,
    ])->and($diff['changed'])->toContain([
        'field' => 'title',
        'old' => 'Original title',
        'new' => 'Updated title',
    ])->and($diff['changed'])->toContain([
        'field' => 'data.headline',
        'old' => 'Original headline',
        'new' => 'Updated headline',
    ]);
});

it('prunes old non-published revisions and keeps published revisions', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'status' => EntryStatus::Draft,
    ]);

    $entry->update(['title' => 'Draft 2']);
    $entry->update(['title' => 'Draft 3']);
    $publishedRevision = $this->service->createRevision($entry, RevisionType::Published, 'Published snapshot');

    $deletedCount = $this->service->pruneRevisions($entry, 1);

    expect($deletedCount)->toBe(2)
        ->and($entry->revisions()->count())->toBe(2)
        ->and($entry->revisions()->pluck('revision_number')->all())->toBe([4, 3])
        ->and($entry->revisions()->whereKey($publishedRevision->id)->exists())->toBeTrue()
        ->and(Revision::withTrashed()->whereNotNull('deleted_at')->count())->toBe(2);
});
