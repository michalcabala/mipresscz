<?php

use Illuminate\Support\Facades\Cache;
use MiPressCz\Core\Models\GlobalSet;

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
