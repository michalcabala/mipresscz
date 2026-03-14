<?php

use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;

beforeEach(function () {
    $this->collection = Collection::factory()->create([
        'handle' => 'pages',
        'route_template' => '/{slug}',
    ]);
    $this->blueprint = Blueprint::factory()->create([
        'collection_id' => $this->collection->id,
    ]);
});

it('returns the search page', function () {
    $response = $this->get('/search');

    $response->assertSuccessful();
    $response->assertSee(__('content.search.title'));
});

it('searches published entries by title', function () {
    Entry::factory()->published()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'title' => 'Testovací článek',
        'slug' => 'testovaci-clanek',
        'locale' => 'cs',
    ]);

    $response = $this->get('/search?q=Testovací');

    $response->assertSuccessful();
    $response->assertSee('Testovací článek');
});

it('does not return draft entries in search', function () {
    Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'title' => 'Skrytý koncept',
        'slug' => 'skryty-koncept',
        'status' => EntryStatus::Draft,
        'locale' => 'cs',
    ]);

    $response = $this->get('/search?q=Skrytý');

    $response->assertSuccessful();
    $response->assertDontSee('Skrytý koncept');
});

it('requires at least 2 characters', function () {
    $response = $this->get('/search?q=a');

    $response->assertSuccessful();
    $response->assertSee(__('content.search.min_length'));
});

it('shows no results message when nothing matches', function () {
    $response = $this->get('/search?q=neexistujiciretezec');

    $response->assertSuccessful();
});

it('entry model has searchable array with expected keys', function () {
    $entry = Entry::factory()->published()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'title' => 'Searchable',
        'slug' => 'searchable',
        'locale' => 'cs',
        'meta_title' => 'Meta Title',
        'meta_description' => 'Meta Description',
    ]);

    $searchable = $entry->toSearchableArray();

    expect($searchable)->toHaveKeys(['title', 'slug', 'locale', 'meta_title', 'meta_description']);
    expect($searchable['title'])->toBe('Searchable');
});

it('only indexes published entries', function () {
    $published = Entry::factory()->published()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
    ]);

    $draft = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Draft,
    ]);

    expect($published->shouldBeSearchable())->toBeTrue();
    expect($draft->shouldBeSearchable())->toBeFalse();
});
