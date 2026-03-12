<?php

use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Taxonomy;

// ── Collection: HasLocalizedTitle ──

it('collection translations are cast to array', function () {
    $collection = Collection::factory()->create([
        'title' => 'Articles',
        'translations' => ['cs' => ['title' => 'Clanky'], 'en' => ['title' => 'Articles']],
    ]);

    expect($collection->translations)->toBeArray();
    expect($collection->translations['cs']['title'])->toBe('Clanky');
});

it('getLocalizedTitle returns translation for given locale', function () {
    $collection = Collection::factory()->create([
        'title' => 'Default',
        'translations' => ['cs' => ['title' => 'Clanky'], 'en' => ['title' => 'Articles']],
    ]);

    expect($collection->getLocalizedTitle('cs'))->toBe('Clanky');
    expect($collection->getLocalizedTitle('en'))->toBe('Articles');
});

it('getLocalizedTitle falls back to title when locale translation is missing', function () {
    $collection = Collection::factory()->create(['title' => 'Fallback']);

    expect($collection->getLocalizedTitle('de'))->toBe('Fallback');
});

it('getLocalizedTitle uses app locale when no locale argument provided', function () {
    app()->setLocale('cs');

    $collection = Collection::factory()->create([
        'title' => 'Default',
        'translations' => ['cs' => ['title' => 'Clanky']],
    ]);

    expect($collection->getLocalizedTitle())->toBe('Clanky');
});

// ── Taxonomy: HasLocalizedTitle ──

it('taxonomy translations are cast to array', function () {
    $taxonomy = Taxonomy::factory()->create([
        'title' => 'Tags',
        'translations' => ['cs' => ['title' => 'Stitky'], 'en' => ['title' => 'Tags']],
    ]);

    expect($taxonomy->translations)->toBeArray();
    expect($taxonomy->translations['en']['title'])->toBe('Tags');
});

it('taxonomy getLocalizedTitle falls back to title when translations is null', function () {
    $taxonomy = Taxonomy::factory()->create(['title' => 'Tags', 'translations' => null]);

    expect($taxonomy->getLocalizedTitle('fr'))->toBe('Tags');
});

it('taxonomy getLocalizedDescription returns localized description', function () {
    $taxonomy = Taxonomy::factory()->create([
        'title' => 'Tags',
        'description' => 'English description',
        'translations' => ['cs' => ['title' => 'Stitky', 'description' => 'Cesky popis']],
    ]);

    expect($taxonomy->getLocalizedDescription('cs'))->toBe('Cesky popis');
    expect($taxonomy->getLocalizedDescription('en'))->toBe('English description');
});
