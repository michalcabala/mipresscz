<?php

use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\GlobalSet;
use MiPressCz\Core\Models\Locale;
use MiPressCz\Core\Models\Taxonomy;
use MiPressCz\Core\Models\Term;
use MiPressCz\Core\Support\Blink;

beforeEach(function () {
    Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'is_active' => true]);
    Locale::factory()->create(['code' => 'en', 'is_default' => false, 'is_active' => true]);

    $this->collection = Collection::factory()->create(['revisions_enabled' => false]);
    $this->blueprint = Blueprint::factory()->create(['collection_id' => $this->collection->id]);
});

// ── Entry: HasOrigin helpers ──

it('entry isOrigin returns true for origin entries', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
    ]);

    expect($entry->isOrigin())->toBeTrue()
        ->and($entry->isTranslation())->toBeFalse();
});

it('entry isTranslation returns true for translated entries', function () {
    $origin = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
    ]);
    $translation = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'en',
        'origin_id' => $origin->id,
    ]);

    expect($translation->isTranslation())->toBeTrue()
        ->and($translation->isOrigin())->toBeFalse();
});

it('entry getOrigin returns self for origin entry', function () {
    $origin = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
    ]);

    expect($origin->getOrigin()->id)->toBe($origin->id);
});

it('entry getOrigin returns parent for translation', function () {
    $origin = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
    ]);
    $translation = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'en',
        'origin_id' => $origin->id,
    ]);

    expect($translation->getOrigin()->id)->toBe($origin->id);
});

it('entry getOrigin uses Blink cache', function () {
    $origin = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
    ]);
    $translation = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'en',
        'origin_id' => $origin->id,
    ]);

    $blink = app(Blink::class);
    $blink->flush();

    // first call populates cache
    $translation->getOrigin();
    expect($blink->has('origin-Entry-'.$translation->id))->toBeTrue();

    // second call is cached — no extra queries
    $translation->getOrigin();
});

it('entry root returns the root origin ancestor', function () {
    $origin = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
    ]);
    $translation = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'en',
        'origin_id' => $origin->id,
    ]);

    expect($translation->originRoot()->id)->toBe($origin->id)
        ->and($origin->originRoot()->id)->toBe($origin->id);
});

it('entry getTranslation returns correct locale variant', function () {
    $origin = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'title' => 'Český článek',
    ]);
    $en = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'en',
        'origin_id' => $origin->id,
        'title' => 'English article',
    ]);

    expect($origin->getTranslation('en')->title)->toBe('English article')
        ->and($origin->getTranslation('cs')->title)->toBe('Český článek')
        ->and($en->getTranslation('cs')->title)->toBe('Český článek');
});

it('entry getTranslation returns null for missing locale', function () {
    $origin = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
    ]);

    expect($origin->getTranslation('de'))->toBeNull();
});

it('entry getTranslations returns all locale variants keyed by locale', function () {
    $origin = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
    ]);
    Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'en',
        'origin_id' => $origin->id,
    ]);

    $translations = $origin->getTranslations();

    expect($translations)->toHaveCount(2)
        ->and($translations->keys()->all())->toContain('cs', 'en');
});

it('entry getAvailableLocales returns locale codes', function () {
    $origin = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
    ]);
    Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'en',
        'origin_id' => $origin->id,
    ]);

    expect($origin->getAvailableLocales())->toContain('cs', 'en');
});

it('entry getMissingLocales returns locales without translations', function () {
    $origin = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
    ]);

    expect($origin->getMissingLocales())->toContain('en');
});

// ── Term: HasOrigin helpers ──

it('term uses HasOrigin trait for origin/translations', function () {
    $taxonomy = Taxonomy::factory()->create();
    $origin = Term::factory()->create(['taxonomy_id' => $taxonomy->id, 'locale' => 'cs']);
    $translation = Term::factory()->create([
        'taxonomy_id' => $taxonomy->id,
        'locale' => 'en',
        'origin_id' => $origin->id,
    ]);

    expect($origin->isOrigin())->toBeTrue()
        ->and($translation->isTranslation())->toBeTrue()
        ->and($translation->getOrigin()->id)->toBe($origin->id)
        ->and($origin->getTranslations())->toHaveCount(2);
});

// ── GlobalSet: HasOrigin helpers ──

it('global set uses HasOrigin trait for origin/translations', function () {
    $origin = GlobalSet::factory()->create(['locale' => 'cs', 'handle' => 'site', 'name' => 'site']);
    $translation = GlobalSet::factory()->create([
        'locale' => 'en',
        'handle' => 'site',
        'name' => 'site',
        'origin_id' => $origin->id,
    ]);

    expect($origin->isOrigin())->toBeTrue()
        ->and($translation->isTranslation())->toBeTrue()
        ->and($translation->getOrigin()->id)->toBe($origin->id)
        ->and($origin->getTranslation('en')->locale)->toBe('en');
});
