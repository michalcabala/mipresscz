<?php

use MiPressCz\Core\Models\Taxonomy;
use MiPressCz\Core\Models\Term;

// ── Locale field ──

it('term has locale field defaulting to cs', function () {
    $taxonomy = Taxonomy::factory()->create();
    $term = Term::factory()->create(['taxonomy_id' => $taxonomy->id]);

    expect($term->locale)->toBe('cs');
});

it('term can be created with a specific locale', function () {
    $taxonomy = Taxonomy::factory()->create();
    $term = Term::factory()->create(['taxonomy_id' => $taxonomy->id, 'locale' => 'en']);

    expect($term->locale)->toBe('en');
});

// ── origin / translations relationships ──

it('term can have an origin term for translation linking', function () {
    $taxonomy = Taxonomy::factory()->create();
    $origin = Term::factory()->create(['taxonomy_id' => $taxonomy->id, 'locale' => 'cs']);
    $translated = Term::factory()->create([
        'taxonomy_id' => $taxonomy->id,
        'locale' => 'en',
        'origin_id' => $origin->id,
    ]);

    expect($translated->origin->id)->toBe($origin->id);
});

it('origin term has translations relationship returning locale variants', function () {
    $taxonomy = Taxonomy::factory()->create();
    $origin = Term::factory()->create(['taxonomy_id' => $taxonomy->id, 'locale' => 'cs']);
    Term::factory()->create(['taxonomy_id' => $taxonomy->id, 'locale' => 'en', 'origin_id' => $origin->id]);
    Term::factory()->create(['taxonomy_id' => $taxonomy->id, 'locale' => 'de', 'origin_id' => $origin->id]);

    expect($origin->translations)->toHaveCount(2);
});

it('term origin is nullable', function () {
    $taxonomy = Taxonomy::factory()->create();
    $term = Term::factory()->create(['taxonomy_id' => $taxonomy->id]);

    expect($term->origin)->toBeNull();
});

it('deleting origin sets origin_id to null on translations', function () {
    $taxonomy = Taxonomy::factory()->create();
    $origin = Term::factory()->create(['taxonomy_id' => $taxonomy->id, 'locale' => 'cs']);
    $translated = Term::factory()->create([
        'taxonomy_id' => $taxonomy->id,
        'locale' => 'en',
        'origin_id' => $origin->id,
    ]);

    $origin->forceDelete();
    $translated->refresh();

    expect($translated->origin_id)->toBeNull();
});

// ── scopeForLocale ──

it('scopeForLocale filters terms by locale', function () {
    $taxonomy = Taxonomy::factory()->create();
    Term::factory()->create(['taxonomy_id' => $taxonomy->id, 'locale' => 'cs']);
    Term::factory()->create(['taxonomy_id' => $taxonomy->id, 'locale' => 'cs']);
    Term::factory()->create(['taxonomy_id' => $taxonomy->id, 'locale' => 'en']);

    expect(Term::forLocale('cs')->count())->toBe(2);
    expect(Term::forLocale('en')->count())->toBe(1);
});

// ── Unique constraint ──

it('two terms with same slug but different locale can coexist', function () {
    $taxonomy = Taxonomy::factory()->create();
    Term::factory()->create(['taxonomy_id' => $taxonomy->id, 'locale' => 'cs', 'slug' => 'technika']);
    $en = Term::factory()->create(['taxonomy_id' => $taxonomy->id, 'locale' => 'en', 'slug' => 'technika']);

    expect($en->exists)->toBeTrue();
});

it('two terms with same slug and same locale in same taxonomy are rejected', function () {
    $this->expectException(\Illuminate\Database\UniqueConstraintViolationException::class);

    $taxonomy = Taxonomy::factory()->create();
    Term::factory()->create(['taxonomy_id' => $taxonomy->id, 'locale' => 'cs', 'slug' => 'duplicate']);
    Term::factory()->create(['taxonomy_id' => $taxonomy->id, 'locale' => 'cs', 'slug' => 'duplicate']);
});
