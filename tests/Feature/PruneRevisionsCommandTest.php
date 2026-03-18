<?php

declare(strict_types=1);

use MiPressCz\Core\Enums\RevisionType;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Services\RevisionService;

beforeEach(function () {
    config()->set('mipress-revisions.enabled_models', [Entry::class]);

    $this->collection = Collection::factory()->create([
        'handle' => 'articles',
        'route_template' => '/articles/{slug}',
    ]);
    $this->blueprint = Blueprint::factory()->create([
        'collection_id' => $this->collection->id,
        'is_default' => true,
    ]);
});

it('supports dry-run pruning without deleting revisions', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'title' => 'Revision 1',
    ]);

    $entry->update(['title' => 'Revision 2']);
    $entry->update(['title' => 'Revision 3']);

    $this->artisan('mipress:prune-revisions', [
        '--keep' => 1,
        '--model' => 'Entry',
        '--dry-run' => true,
    ])->assertSuccessful();

    expect($entry->fresh()->revisions)->toHaveCount(3);
});

it('prunes old revisions and keeps published snapshots', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'title' => 'Revision 1',
    ]);

    $entry->update(['title' => 'Revision 2']);
    app(RevisionService::class)->createRevision($entry, RevisionType::Published, 'Published snapshot');
    $entry->update(['title' => 'Revision 3']);

    $this->artisan('mipress:prune-revisions', [
        '--keep' => 1,
        '--model' => Entry::class,
    ])->assertSuccessful();

    expect($entry->fresh()->revisions)->toHaveCount(2)
        ->and($entry->fresh()->revisions->pluck('revision_number')->all())->toBe([4, 3])
        ->and($entry->fresh()->revisions->pluck('type')->map->value->all())->toBe(['draft', 'published']);
});

it('can prune published revisions when configured to do so', function () {
    config()->set('mipress-revisions.prune_keep_published', false);

    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'title' => 'Revision 1',
    ]);

    app(RevisionService::class)->createRevision($entry, RevisionType::Published, 'Published snapshot');
    $entry->update(['title' => 'Revision 2']);

    $this->artisan('mipress:prune-revisions', [
        '--keep' => 1,
        '--model' => 'Entry',
    ])->assertSuccessful();

    expect($entry->fresh()->revisions)->toHaveCount(1)
        ->and($entry->fresh()->latestRevision->type)->toBe(RevisionType::Draft);
});

it('fails for an unknown revisionable model option', function () {
    $this->artisan('mipress:prune-revisions', [
        '--model' => 'UnknownModel',
    ])->assertFailed();
});
