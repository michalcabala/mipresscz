<?php

use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\Entry;

// ── Collection ──

it('collection defaultBlueprint returns null when no default exists', function () {
    $collection = Collection::factory()->create();
    Blueprint::factory()->create(['collection_id' => $collection->id, 'is_default' => false]);

    expect($collection->defaultBlueprint())->toBeNull();
});

it('collection soft-deletes and is hidden from default queries', function () {
    $collection = Collection::factory()->create();
    $id = $collection->id;

    $collection->delete();

    expect(Collection::query()->find($id))->toBeNull();
    expect(Collection::withTrashed()->find($id))->not->toBeNull();
});

it('collection can be restored after soft delete', function () {
    $collection = Collection::factory()->create();
    $collection->delete();
    $collection->restore();

    expect(Collection::query()->find($collection->id))->not->toBeNull();
});

it('collection settings are cast to array', function () {
    $collection = Collection::factory()->create(['settings' => ['foo' => 'bar']]);

    expect($collection->settings)->toBeArray()->toHaveKey('foo', 'bar');
});

it('collection is_active is cast to boolean', function () {
    $collection = Collection::factory()->create(['is_active' => true]);

    expect($collection->is_active)->toBeTrue();
});

// ── Blueprint ──

it('blueprint belongs to collection', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    expect($blueprint->collection->id)->toBe($collection->id);
});

it('blueprint has entries relationship', function () {
    $collection = Collection::factory()->create(['revisions_enabled' => false]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id]);

    expect($blueprint->entries)->toHaveCount(1);
});

it('blueprint getNonTranslatableFields excludes section_break type', function () {
    $blueprint = Blueprint::factory()->create([
        'fields' => [
            ['handle' => 'divider', 'type' => 'section_break', 'translatable' => false, 'section' => 'main'],
            ['handle' => 'sku', 'type' => 'text', 'translatable' => false, 'section' => 'main'],
        ],
    ]);

    $fields = $blueprint->getNonTranslatableFields();

    expect($fields)->toHaveCount(1);
    expect($fields[0]['handle'])->toBe('sku');
});

it('blueprint getFieldsBySection returns empty for unknown section', function () {
    $blueprint = Blueprint::factory()->create([
        'fields' => [
            ['handle' => 'title', 'type' => 'text', 'translatable' => true, 'section' => 'main', 'order' => 1],
        ],
    ]);

    expect($blueprint->getFieldsBySection('nonexistent'))->toBeEmpty();
});

it('blueprint getFieldsBySection returns ordered fields', function () {
    $blueprint = Blueprint::factory()->create([
        'fields' => [
            ['handle' => 'b_field', 'type' => 'text', 'translatable' => false, 'section' => 'seo', 'order' => 2],
            ['handle' => 'a_field', 'type' => 'text', 'translatable' => false, 'section' => 'seo', 'order' => 1],
        ],
    ]);

    $fields = $blueprint->getFieldsBySection('seo');

    expect($fields)->toHaveCount(2);
    expect($fields[0]['handle'])->toBe('a_field');
});

it('blueprint soft-deletes correctly', function () {
    $blueprint = Blueprint::factory()->create();
    $id = $blueprint->id;
    $blueprint->delete();

    expect(Blueprint::query()->find($id))->toBeNull();
    expect(Blueprint::withTrashed()->find($id))->not->toBeNull();
});

it('blueprint is_active is cast to boolean', function () {
    $blueprint = Blueprint::factory()->create(['is_active' => false]);

    expect($blueprint->is_active)->toBeFalse();
});
