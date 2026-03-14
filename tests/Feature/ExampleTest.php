<?php

use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;

it('returns homepage entry for root url', function () {
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
        'is_homepage' => true,
    ]);

    $this->get('/')->assertSuccessful();
});
