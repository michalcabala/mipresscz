<?php

use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;

it('use_mason defaults to false for new blueprints', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    expect($blueprint->use_mason)->toBeFalse();
});

it('use_mason can be set to true', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->create([
        'collection_id' => $collection->id,
        'use_mason' => true,
    ]);

    expect($blueprint->use_mason)->toBeTrue();
});

it('use_mason is cast to boolean', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->create([
        'collection_id' => $collection->id,
        'use_mason' => 1,
    ]);

    expect($blueprint->use_mason)->toBeBool()->toBeTrue();
});

it('use_mason is included in fillable', function () {
    $blueprint = new Blueprint;

    expect($blueprint->getFillable())->toContain('use_mason');
});

it('blueprint with use_mason true is persisted and retrievable', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->create([
        'collection_id' => $collection->id,
        'use_mason' => true,
    ]);

    $fresh = Blueprint::find($blueprint->id);

    expect($fresh->use_mason)->toBeTrue();
});
