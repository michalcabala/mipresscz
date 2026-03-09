<?php

use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\Entry;
use App\Models\GlobalSet;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

// -- GlobalSet --

it('globalSet translations HasMany returns locale variants', function () {
    $origin = GlobalSet::factory()->create(['locale' => 'cs', 'origin_id' => null]);
    $translation = GlobalSet::factory()->create(['locale' => 'en', 'origin_id' => $origin->id]);

    expect($origin->translations)->toHaveCount(1);
    expect($origin->translations->first()->id)->toBe($translation->id);
});

it('globalSet origin relationship resolves the parent record', function () {
    $origin = GlobalSet::factory()->create(['locale' => 'cs', 'origin_id' => null]);
    $translation = GlobalSet::factory()->create(['locale' => 'en', 'origin_id' => $origin->id]);

    expect($translation->origin->id)->toBe($origin->id);
});

it('globalSet findByHandle falls back to origin when locale translation missing', function () {
    $origin = GlobalSet::factory()->create(['locale' => 'cs', 'origin_id' => null]);
    $handle = $origin->handle;

    Cache::forget("global_set.{$handle}.de");
    $found = GlobalSet::findByHandle($handle, 'de');

    expect($found)->not->toBeNull();
    expect($found->id)->toBe($origin->id);
});

it('globalSet findByHandle returns null for unknown handle', function () {
    Cache::forget('global_set.__nonexistent__.cs');
    $found = GlobalSet::findByHandle('__nonexistent__', 'cs');

    expect($found)->toBeNull();
});

it('globalSet saved event invalidates the cache entry', function () {
    $set = GlobalSet::factory()->create(['locale' => 'cs', 'data' => ['title' => 'Old']]);
    $handle = $set->handle;
    Cache::put("global_set.{$handle}.cs", $set);

    $set->update(['data' => ['title' => 'New']]);

    expect(Cache::get("global_set.{$handle}.cs"))->toBeNull();
});

it('globalSet getValue returns a nested value from data', function () {
    $set = GlobalSet::factory()->create([
        'locale' => app()->getLocale(),
        'origin_id' => null,
        'data' => ['social' => ['twitter' => '@example']],
    ]);
    Cache::forget("global_set.{$set->handle}.".app()->getLocale());

    $value = GlobalSet::getValue($set->handle, 'social.twitter');

    expect($value)->toBe('@example');
});

// -- Revision --

it('revision entry relationship resolves correctly', function () {
    $collection = Collection::factory()->create(['revisions_enabled' => true]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    $entry = Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id]);
    $revision = $entry->revisions()->first();

    expect($revision->entry->id)->toBe($entry->id);
});

it('revision user relationship resolves to author', function () {
    $user = User::factory()->create();
    $collection = Collection::factory()->create(['revisions_enabled' => true]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    $entry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'author_id' => $user->id,
    ]);
    $revision = $entry->revisions()->first();

    expect($revision->user_id)->toBe($user->id);
});

it('is_current flag is true on the latest revision', function () {
    $collection = Collection::factory()->create(['revisions_enabled' => true]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    $entry = Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id]);

    expect($entry->revisions()->where('is_current', true)->count())->toBe(1);
});

it('updating entry marks old revision as not current and creates new one', function () {
    $collection = Collection::factory()->create(['revisions_enabled' => true]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    $entry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'title' => 'Original',
    ]);

    $entry->update(['title' => 'Updated']);

    expect($entry->revisions()->count())->toBe(2);
    expect($entry->revisions()->where('is_current', true)->count())->toBe(1);
    expect($entry->revisions()->where('is_current', false)->count())->toBe(1);
});

it('no revision created on entry update when revisions_enabled is false', function () {
    $collection = Collection::factory()->create(['revisions_enabled' => false]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    $entry = Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id]);

    $entry->update(['title' => 'Updated title']);

    expect($entry->revisions()->count())->toBe(0);
});
