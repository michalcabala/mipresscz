<?php

use Illuminate\Support\Facades\Event;
use MiPressCz\Core\Events\EntrySaved;
use MiPressCz\Core\Events\EntrySaving;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;

beforeEach(function () {
    $this->collection = Collection::factory()->create([
        'handle' => 'pages',
        'route_template' => '/{slug}',
    ]);
    $this->blueprint = Blueprint::factory()->create(['collection_id' => $this->collection->id]);
});

// ── EntrySaving ──

it('dispatches EntrySaving before entry is created', function () {
    Event::fake([EntrySaving::class]);

    Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
    ]);

    Event::assertDispatched(EntrySaving::class);
});

it('dispatches EntrySaving before entry is updated', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
    ]);

    Event::fake([EntrySaving::class]);

    $entry->update(['title' => 'Updated title']);

    Event::assertDispatched(EntrySaving::class, fn (EntrySaving $e) => $e->entry->is($entry));
});

it('EntrySaving event carries the entry instance', function () {
    $dispatched = [];
    Event::listen(EntrySaving::class, function (EntrySaving $event) use (&$dispatched) {
        $dispatched[] = $event->entry;
    });

    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
    ]);

    expect($dispatched)->toHaveCount(1)
        ->and($dispatched[0]->id)->toBe($entry->id);
});

it('cancelling EntrySaving prevents the entry from being saved', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'title' => 'Original title',
    ]);

    // Register cancel listener AFTER the initial create
    Event::listen(EntrySaving::class, function (EntrySaving $event) {
        $event->cancel();
    });

    // Try updating — the update should be aborted
    $entry->update(['title' => 'Should not be saved']);

    expect($entry->fresh()->title)->toBe('Original title');
});

it('EntrySaving isCancelled defaults to false', function () {
    $event = new EntrySaving(new Entry);

    expect($event->isCancelled())->toBeFalse();
});

it('EntrySaving isCancelled is true after cancel()', function () {
    $event = new EntrySaving(new Entry);
    $event->cancel();

    expect($event->isCancelled())->toBeTrue();
});

// ── EntrySaved ──

it('dispatches EntrySaved after entry is created with wasCreated=true', function () {
    Event::fake([EntrySaved::class]);

    Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
    ]);

    Event::assertDispatched(EntrySaved::class, fn (EntrySaved $e) => $e->wasCreated === true);
});

it('dispatches EntrySaved after entry is updated with wasCreated=false', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
    ]);

    Event::fake([EntrySaved::class]);

    $entry->update(['title' => 'New title']);

    Event::assertDispatched(EntrySaved::class, fn (EntrySaved $e) => $e->wasCreated === false);
});

it('EntrySaved is not dispatched when save is cancelled', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'title' => 'Original',
    ]);

    Event::listen(EntrySaving::class, fn (EntrySaving $e) => $e->cancel());
    Event::fake([EntrySaved::class]);

    $entry->update(['title' => 'Should not save']);

    Event::assertNotDispatched(EntrySaved::class);
});
