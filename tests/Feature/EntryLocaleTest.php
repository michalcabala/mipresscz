<?php

use App\Enums\EntryStatus;
use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\Entry;
use App\Models\Locale;

beforeEach(function () {
    locales()->clearCache();
    Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'is_active' => true, 'url_prefix' => null, 'order' => 1]);
    Locale::factory()->create(['code' => 'en', 'is_default' => false, 'is_active' => true, 'url_prefix' => 'en', 'order' => 2]);
    locales()->clearCache();
});

it('getMissingLocales uses active locales from database', function () {
    $collection = Collection::factory()->create(['route_template' => '/{slug}']);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    $origin = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Published,
    ]);

    expect($origin->getMissingLocales())->toContain('en');
    expect($origin->getMissingLocales())->not->toContain('cs');
});

it('getMissingLocales returns empty when all translations exist', function () {
    $collection = Collection::factory()->create(['route_template' => '/{slug}']);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    $origin = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Published,
    ]);

    Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'locale' => 'en',
        'origin_id' => $origin->id,
        'status' => EntryStatus::Published,
    ]);

    expect($origin->getMissingLocales())->toBeEmpty();
});

it('getFullUrl returns correct url for default locale', function () {
    $collection = Collection::factory()->create(['route_template' => '/{slug}', 'handle' => 'pages']);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    $entry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'locale' => 'cs',
        'slug' => 'uvod',
    ]);

    $url = $entry->getFullUrl();

    expect($url)->toContain('/uvod');
    expect($url)->not->toContain('/cs/');
});

it('getFullUrl returns url with prefix for non-default locale', function () {
    $collection = Collection::factory()->create(['route_template' => '/{slug}', 'handle' => 'pages']);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    $entry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'locale' => 'en',
        'slug' => 'about',
    ]);

    $url = $entry->getFullUrl();

    expect($url)->toContain('/en/');
    expect($url)->toContain('about');
});

it('getHreflangTags returns tags for all translations', function () {
    $collection = Collection::factory()->create(['route_template' => '/{slug}', 'handle' => 'pages']);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    $origin = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'locale' => 'cs',
        'slug' => 'uvod',
        'status' => EntryStatus::Published,
    ]);

    $translation = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'locale' => 'en',
        'slug' => 'intro',
        'origin_id' => $origin->id,
        'status' => EntryStatus::Published,
    ]);

    $origin->load(['translations']);
    $tags = $origin->getHreflangTags();

    expect($tags)->toHaveKey('cs');
    expect($tags)->toHaveKey('en');
    expect($tags)->toHaveKey('x-default');
    expect($tags['x-default'])->toBe($tags['cs']);
});

it('frontend routes locale-prefixed entries correctly', function () {
    $collection = Collection::factory()->create([
        'handle' => 'pages',
        'route_template' => '/{slug}',
    ]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'locale' => 'en',
        'slug' => 'home',
        'status' => EntryStatus::Published,
        'published_at' => now()->subDay(),
    ]);

    $this->get('/en/home')->assertSuccessful();
});

// ── Single-locale behavior ──

it('getFullUrl omits prefix when single frontend locale', function () {
    // Replace the two-locale beforeEach setup with single locale
    Locale::query()->delete();
    locales()->clearCache();
    Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'is_active' => true, 'is_frontend_available' => true, 'url_prefix' => 'cs', 'order' => 1]);
    locales()->clearCache();

    $collection = Collection::factory()->create(['route_template' => '/{slug}', 'handle' => 'pages']);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    $entry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'locale' => 'cs',
        'slug' => 'uvod',
    ]);

    $url = $entry->getFullUrl();

    expect($url)->toContain('/uvod');
    expect($url)->not->toContain('/cs/');
});

it('getHreflangTags returns empty when single frontend locale', function () {
    Locale::query()->delete();
    locales()->clearCache();
    Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'is_active' => true, 'is_frontend_available' => true, 'url_prefix' => null, 'order' => 1]);
    locales()->clearCache();

    $collection = Collection::factory()->create(['route_template' => '/{slug}', 'handle' => 'pages']);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    $origin = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'locale' => 'cs',
        'slug' => 'uvod',
        'status' => EntryStatus::Published,
    ]);

    expect($origin->getHreflangTags())->toBeEmpty();
});

it('redirects prefixed URL to unprefixed when single frontend locale', function () {
    Locale::query()->delete();
    locales()->clearCache();
    Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'is_active' => true, 'is_frontend_available' => true, 'url_prefix' => 'cs', 'order' => 1]);
    locales()->clearCache();

    $this->get('/cs/uvod')->assertRedirect('/uvod');
});
