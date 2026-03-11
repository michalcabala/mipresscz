<?php

use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Taxonomy;
use MiPressCz\Core\Models\Term;

// ── Taxonomy ──

it('taxonomy has terms relationship', function () {
    $taxonomy = Taxonomy::factory()->create();
    Term::factory()->create(['taxonomy_id' => $taxonomy->id]);

    expect($taxonomy->terms)->toHaveCount(1);
});

it('taxonomy collections returns attached collections', function () {
    $taxonomy = Taxonomy::factory()->create();
    $collection = Collection::factory()->create();
    $taxonomy->collections()->attach($collection);

    expect($taxonomy->collections)->toHaveCount(1);
    expect($taxonomy->collections->first()->id)->toBe($collection->id);
});

it('taxonomy soft-deletes correctly', function () {
    $taxonomy = Taxonomy::factory()->create();
    $id = $taxonomy->id;
    $taxonomy->delete();

    expect(Taxonomy::query()->find($id))->toBeNull();
    expect(Taxonomy::withTrashed()->find($id))->not->toBeNull();
});

it('taxonomy is_active is cast to boolean', function () {
    $taxonomy = Taxonomy::factory()->create(['is_active' => false]);

    expect($taxonomy->is_active)->toBeFalse();
});

it('taxonomy settings are cast to array', function () {
    $taxonomy = Taxonomy::factory()->create(['settings' => ['color' => 'blue']]);

    expect($taxonomy->settings)->toBeArray()->toHaveKey('color', 'blue');
});

// ── Term ──

it('scopeRoot returns only top-level terms', function () {
    $taxonomy = Taxonomy::factory()->create();
    $parent = Term::factory()->create(['taxonomy_id' => $taxonomy->id]);
    Term::factory()->create(['taxonomy_id' => $taxonomy->id, 'parent_id' => $parent->id]);

    expect(Term::root()->count())->toBe(1);
    expect(Term::root()->first()->id)->toBe($parent->id);
});

it('scopeOrdered sorts terms by order column', function () {
    $taxonomy = Taxonomy::factory()->create();
    Term::factory()->create(['taxonomy_id' => $taxonomy->id, 'order' => 3]);
    Term::factory()->create(['taxonomy_id' => $taxonomy->id, 'order' => 1]);
    Term::factory()->create(['taxonomy_id' => $taxonomy->id, 'order' => 2]);

    $orders = Term::ordered()->pluck('order')->all();

    expect($orders)->toBe([1, 2, 3]);
});

it('term data is cast to array', function () {
    $taxonomy = Taxonomy::factory()->create();
    $term = Term::factory()->create(['taxonomy_id' => $taxonomy->id, 'data' => ['meta_title' => 'Test']]);

    expect($term->data)->toBeArray()->toHaveKey('meta_title', 'Test');
});

it('term supports grandchild nesting', function () {
    $taxonomy = Taxonomy::factory()->create();
    $grandparent = Term::factory()->create(['taxonomy_id' => $taxonomy->id]);
    $parent = Term::factory()->create(['taxonomy_id' => $taxonomy->id, 'parent_id' => $grandparent->id]);
    $child = Term::factory()->create(['taxonomy_id' => $taxonomy->id, 'parent_id' => $parent->id]);

    expect($grandparent->children)->toHaveCount(1);
    expect($parent->children->first()->id)->toBe($child->id);
    expect($child->parent->id)->toBe($parent->id);
});

it('term soft-deletes correctly', function () {
    $taxonomy = Taxonomy::factory()->create();
    $term = Term::factory()->create(['taxonomy_id' => $taxonomy->id]);
    $id = $term->id;
    $term->delete();

    expect(Term::query()->find($id))->toBeNull();
    expect(Term::withTrashed()->find($id))->not->toBeNull();
});
