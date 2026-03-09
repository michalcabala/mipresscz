<?php

use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\Entry;
use App\Models\Taxonomy;
use App\Models\Term;
use App\Models\User;
use Illuminate\Support\Str;
use MiPressCz\Core\Enums\EntryStatus;

// ── Scopes ──

it('scopeDraft returns only draft entries', function () {
    $collection = Collection::factory()->create(['revisions_enabled' => false]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id, 'status' => EntryStatus::Draft]);
    Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id, 'status' => EntryStatus::Published, 'published_at' => now()]);

    expect(Entry::draft()->count())->toBe(1);
});

it('scopeRoot returns only entries without a parent', function () {
    $collection = Collection::factory()->create(['revisions_enabled' => false]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    $parent = Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id]);
    Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id, 'parent_id' => $parent->id]);

    expect(Entry::root()->count())->toBe(1);
    expect(Entry::root()->first()->id)->toBe($parent->id);
});

it('scopeOrdered orders entries by order field ascending by default', function () {
    $collection = Collection::factory()->create(['revisions_enabled' => false, 'sort_field' => 'order', 'sort_direction' => 'asc']);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id, 'order' => 3]);
    Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id, 'order' => 1]);
    Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id, 'order' => 2]);

    $ids = Entry::inCollection($collection->handle)->ordered()->pluck('order')->all();

    expect($ids)->toBe([1, 2, 3]);
});

it('published scope excludes entries with expired_at in the past', function () {
    $collection = Collection::factory()->create(['revisions_enabled' => false]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'status' => EntryStatus::Published,
        'published_at' => now()->subDay(),
        'expired_at' => now()->subHour(),
    ]);
    Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'status' => EntryStatus::Published,
        'published_at' => now()->subDay(),
        'expired_at' => now()->addDay(),
    ]);

    expect(Entry::published()->count())->toBe(1);
});

it('published scope excludes entries with future published_at', function () {
    $collection = Collection::factory()->create(['revisions_enabled' => false]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'status' => EntryStatus::Published,
        'published_at' => now()->addDay(),
    ]);

    expect(Entry::published()->count())->toBe(0);
});

// ── Origin / Translation helpers ──

it('isOrigin returns true when entry has no origin_id', function () {
    $collection = Collection::factory()->create(['revisions_enabled' => false]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    $entry = Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id]);

    expect($entry->isOrigin())->toBeTrue();
    expect($entry->isTranslation())->toBeFalse();
});

it('isTranslation returns true when entry has origin_id', function () {
    $collection = Collection::factory()->create(['revisions_enabled' => false]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    $origin = Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id]);
    $translation = Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id, 'origin_id' => $origin->id, 'locale' => 'en']);

    expect($translation->isTranslation())->toBeTrue();
    expect($translation->isOrigin())->toBeFalse();
});

it('getOrigin returns self for origin entry', function () {
    $collection = Collection::factory()->create(['revisions_enabled' => false]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    $entry = Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id]);

    expect($entry->getOrigin()->id)->toBe($entry->id);
});

it('getOrigin returns the origin entry for a translation', function () {
    $collection = Collection::factory()->create(['revisions_enabled' => false]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    $origin = Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id]);
    $translation = Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id, 'origin_id' => $origin->id, 'locale' => 'en']);

    expect($translation->getOrigin()->id)->toBe($origin->id);
});

it('getAvailableLocales includes the origin and all translations', function () {
    $collection = Collection::factory()->create(['revisions_enabled' => false]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    $origin = Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id, 'locale' => 'cs']);
    Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id, 'origin_id' => $origin->id, 'locale' => 'en']);

    $locales = $origin->getAvailableLocales();

    expect($locales)->toContain('cs')->toContain('en');
});

// ── Field data helpers ──

it('getTranslatableData returns only translatable fields from blueprint', function () {
    $collection = Collection::factory()->create(['revisions_enabled' => false]);
    $blueprint = Blueprint::factory()->create([
        'collection_id' => $collection->id,
        'fields' => [
            ['handle' => 'headline', 'type' => 'text', 'translatable' => true, 'section' => 'main'],
            ['handle' => 'sku', 'type' => 'text', 'translatable' => false, 'section' => 'main'],
        ],
    ]);
    $entry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'data' => ['headline' => 'Hello', 'sku' => 'ABC-123'],
    ]);

    expect($entry->load('blueprint')->getTranslatableData())
        ->toHaveKey('headline')
        ->not->toHaveKey('sku');
});

it('getNonTranslatableData returns only non-translatable fields', function () {
    $collection = Collection::factory()->create(['revisions_enabled' => false]);
    $blueprint = Blueprint::factory()->create([
        'collection_id' => $collection->id,
        'fields' => [
            ['handle' => 'headline', 'type' => 'text', 'translatable' => true, 'section' => 'main'],
            ['handle' => 'sku', 'type' => 'text', 'translatable' => false, 'section' => 'main'],
        ],
    ]);
    $entry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'data' => ['headline' => 'Hello', 'sku' => 'ABC-123'],
    ]);

    expect($entry->load('blueprint')->getNonTranslatableData())
        ->toHaveKey('sku')
        ->not->toHaveKey('headline');
});

// ── Relationships ──

it('revisions relationship is accessible directly on the entry', function () {
    $collection = Collection::factory()->create(['revisions_enabled' => true]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    $entry = Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id]);

    expect($entry->revisions)->toHaveCount(1);
    expect($entry->revisions->first())->toBeInstanceOf(\MiPressCz\Core\Models\Revision::class);
});

it('entry terms relationship works via morphToMany', function () {
    $collection = Collection::factory()->create(['revisions_enabled' => false]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    $entry = Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id]);
    $taxonomy = Taxonomy::factory()->create();
    $term = Term::factory()->create(['taxonomy_id' => $taxonomy->id]);
    $entry->terms()->attach($term, ['order' => 1]);

    expect($entry->terms)->toHaveCount(1);
    expect($entry->terms->first()->id)->toBe($term->id);
});

it('relatedEntries relationship resolves correctly', function () {
    $collection = Collection::factory()->create(['revisions_enabled' => false]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    $parent = Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id]);
    $related = Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id]);

    \Illuminate\Support\Facades\DB::table('entry_relationships')->insert([
        'id' => (string) Str::ulid(),
        'parent_entry_id' => $parent->id,
        'related_entry_id' => $related->id,
        'field_handle' => 'links',
        'order' => 0,
    ]);

    expect($parent->relatedEntries('links')->get())->toHaveCount(1);
    expect($parent->relatedEntries('links')->get()->first()->id)->toBe($related->id);
});

it('referencedBy relationship resolves the reverse direction', function () {
    $collection = Collection::factory()->create(['revisions_enabled' => false]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    $parent = Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id]);
    $related = Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id]);

    \Illuminate\Support\Facades\DB::table('entry_relationships')->insert([
        'id' => (string) Str::ulid(),
        'parent_entry_id' => $parent->id,
        'related_entry_id' => $related->id,
        'field_handle' => 'links',
        'order' => 0,
    ]);

    expect($related->referencedBy()->get())->toHaveCount(1);
    expect($related->referencedBy()->get()->first()->id)->toBe($parent->id);
});

it('author relationship resolves to a user', function () {
    $user = User::factory()->create();
    $collection = Collection::factory()->create(['revisions_enabled' => false]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    $entry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'author_id' => $user->id,
    ]);

    expect($entry->author->id)->toBe($user->id);
});

// ── URI generation ──

it('generateUri replaces year month day tokens using published_at', function () {
    $publishedAt = \Illuminate\Support\Carbon::create(2025, 6, 15);
    $collection = Collection::factory()->create([
        'revisions_enabled' => false,
        'route_template' => '/{year}/{month}/{day}/{slug}',
    ]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    $entry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'slug' => 'my-post',
        'status' => EntryStatus::Published,
        'published_at' => $publishedAt,
    ]);

    expect($entry->uri)->toBe('/2025/06/15/my-post');
});

// ── Boot hooks ──

it('updating title regenerates slug and uri', function () {
    $collection = Collection::factory()->create([
        'revisions_enabled' => false,
        'route_template' => '/blog/{slug}',
    ]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    $entry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'title' => 'Old Title',
    ]);

    $entry->update(['title' => 'New Title']);
    $entry->refresh();

    expect($entry->slug)->toBe('new-title');
    expect($entry->uri)->toBe('/blog/new-title');
});

it('updating slug only regenerates uri without changing slug', function () {
    $collection = Collection::factory()->create([
        'revisions_enabled' => false,
        'route_template' => '/blog/{slug}',
    ]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    $entry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'title' => 'Old Title',
        'slug' => 'custom-slug',
    ]);

    $entry->update(['slug' => 'updated-slug']);
    $entry->refresh();

    expect($entry->slug)->toBe('updated-slug');
    expect($entry->uri)->toBe('/blog/updated-slug');
});

// ── Casts ──

it('is_pinned defaults to false and can be toggled', function () {
    $collection = Collection::factory()->create(['revisions_enabled' => false]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    $entry = Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id]);

    expect($entry->is_pinned)->toBeFalse();

    $entry->update(['is_pinned' => true]);
    expect($entry->fresh()->is_pinned)->toBeTrue();
});
