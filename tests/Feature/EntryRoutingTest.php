<?php

use App\Enums\EntryStatus;
use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\Entry;

it('returns entry for matching uri', function () {
    $collection = Collection::factory()->create([
        'handle' => 'pages',
        'route_template' => '/{slug}',
    ]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'title' => 'Úvod',
        'slug' => 'uvod',
        'status' => EntryStatus::Published,
        'published_at' => now()->subDay(),
        'locale' => 'cs',
    ]);

    $response = $this->get('/uvod');

    $response->assertSuccessful();
    $response->assertSee('Úvod');
});

it('returns 404 for non-existent entry', function () {
    $this->get('/neexistuje')->assertNotFound();
});

it('returns 404 for draft entry', function () {
    $collection = Collection::factory()->create([
        'handle' => 'pages',
        'route_template' => '/{slug}',
    ]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'slug' => 'draft-page',
        'status' => EntryStatus::Draft,
        'locale' => 'cs',
    ]);

    $this->get('/draft-page')->assertNotFound();
});

it('returns entry at nested uri', function () {
    $collection = Collection::factory()->create([
        'handle' => 'articles',
        'route_template' => '/blog/{slug}',
    ]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'title' => 'Článek',
        'slug' => 'test-clanek',
        'status' => EntryStatus::Published,
        'published_at' => now()->subDay(),
        'locale' => 'cs',
    ]);

    $response = $this->get('/blog/test-clanek');

    $response->assertSuccessful();
    $response->assertSee('Článek');
});
