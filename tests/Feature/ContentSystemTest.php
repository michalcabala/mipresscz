<?php

use App\Models\User;
use MiPressCz\Core\Enums\DateBehavior;
use MiPressCz\Core\Enums\DefaultStatus;
use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\GlobalSet;
use MiPressCz\Core\Models\Revision;
use MiPressCz\Core\Models\Taxonomy;
use MiPressCz\Core\Models\Term;

// ── Collection ──

it('can create a collection', function () {
    $collection = Collection::factory()->create([
        'title' => 'Stránky',
        'handle' => 'pages',
    ]);

    expect($collection)
        ->title->toBe('Stránky')
        ->handle->toBe('pages')
        ->id->not->toBeNull();
});

it('collection has blueprints relationship', function () {
    $collection = Collection::factory()->create();
    Blueprint::factory()->create(['collection_id' => $collection->id]);

    expect($collection->blueprints)->toHaveCount(1);
});

it('collection has entries relationship', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
    ]);

    expect($collection->entries)->toHaveCount(1);
});

it('collection has taxonomies relationship', function () {
    $collection = Collection::factory()->create();
    $taxonomy = Taxonomy::factory()->create();
    $collection->taxonomies()->attach($taxonomy);

    expect($collection->taxonomies)->toHaveCount(1);
});

it('collection returns default blueprint', function () {
    $collection = Collection::factory()->create();
    Blueprint::factory()->create(['collection_id' => $collection->id, 'is_default' => true]);
    Blueprint::factory()->create(['collection_id' => $collection->id, 'is_default' => false]);

    expect($collection->defaultBlueprint())
        ->not->toBeNull()
        ->is_default->toBeTrue();
});

it('collection casts enums correctly', function () {
    $collection = Collection::factory()->create([
        'date_behavior' => DateBehavior::Required,
        'default_status' => DefaultStatus::Published,
    ]);

    expect($collection->date_behavior)->toBe(DateBehavior::Required)
        ->and($collection->default_status)->toBe(DefaultStatus::Published);
});

// ── Blueprint ──

it('can create a blueprint', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->create([
        'collection_id' => $collection->id,
        'title' => 'Článek',
        'handle' => 'article',
        'fields' => [
            ['handle' => 'content', 'type' => 'rich_editor', 'translatable' => true, 'section' => 'main'],
            ['handle' => 'image', 'type' => 'media', 'translatable' => false, 'section' => 'sidebar'],
        ],
    ]);

    expect($blueprint)
        ->title->toBe('Článek')
        ->fields->toHaveCount(2);
});

it('blueprint returns translatable fields', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->create([
        'collection_id' => $collection->id,
        'fields' => [
            ['handle' => 'content', 'type' => 'rich_editor', 'translatable' => true],
            ['handle' => 'image', 'type' => 'media', 'translatable' => false],
        ],
    ]);

    expect($blueprint->getTranslatableFields())->toHaveCount(1)
        ->and($blueprint->getTranslatableFields()[0]['handle'])->toBe('content');
});

it('blueprint returns non-translatable fields', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->create([
        'collection_id' => $collection->id,
        'fields' => [
            ['handle' => 'content', 'type' => 'rich_editor', 'translatable' => true],
            ['handle' => 'image', 'type' => 'media', 'translatable' => false],
        ],
    ]);

    expect($blueprint->getNonTranslatableFields())->toHaveCount(1)
        ->and($blueprint->getNonTranslatableFields()[0]['handle'])->toBe('image');
});

it('blueprint returns fields by section', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->create([
        'collection_id' => $collection->id,
        'fields' => [
            ['handle' => 'content', 'type' => 'rich_editor', 'section' => 'main', 'order' => 1],
            ['handle' => 'image', 'type' => 'media', 'section' => 'sidebar', 'order' => 2],
            ['handle' => 'excerpt', 'type' => 'textarea', 'section' => 'main', 'order' => 3],
        ],
    ]);

    expect($blueprint->getFieldsBySection('main'))->toHaveCount(2)
        ->and($blueprint->getFieldsBySection('sidebar'))->toHaveCount(1);
});

// ── Entry ──

it('can create an entry', function () {
    $collection = Collection::factory()->create(['route_template' => '/{slug}']);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    $entry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'title' => 'Testovací stránka',
        'slug' => 'testovaci-stranka',
    ]);

    expect($entry)
        ->title->toBe('Testovací stránka')
        ->slug->toBe('testovaci-stranka')
        ->uri->toBe('/testovaci-stranka');
});

it('entry auto-generates slug from title', function () {
    $collection = Collection::factory()->create(['route_template' => '/{slug}']);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    $entry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'title' => 'Nový článek',
        'slug' => '',
    ]);

    expect($entry->slug)->toBe('novy-clanek');
});

it('entry generates unique slug', function () {
    $collection = Collection::factory()->create(['route_template' => '/{slug}']);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'title' => 'Test',
        'slug' => 'test',
        'locale' => 'cs',
    ]);

    $entry2 = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'title' => 'Test',
        'slug' => '',
        'locale' => 'cs',
    ]);

    expect($entry2->slug)->toBe('test-2');
});

it('entry generates uri from route template', function () {
    $collection = Collection::factory()->create(['route_template' => '/blog/{slug}']);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    $entry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'slug' => 'my-article',
    ]);

    expect($entry->uri)->toBe('/blog/my-article');
});

it('entry uri is null when collection has no route template', function () {
    $collection = Collection::factory()->create(['route_template' => null]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    $entry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
    ]);

    expect($entry->uri)->toBeNull();
});

it('entry published scope works', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'status' => EntryStatus::Published,
        'published_at' => now()->subDay(),
    ]);
    Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'status' => EntryStatus::Draft,
    ]);

    expect(Entry::published()->count())->toBe(1);
});

it('entry locale scope works', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id, 'locale' => 'cs']);
    Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id, 'locale' => 'en']);

    expect(Entry::locale('cs')->count())->toBe(1)
        ->and(Entry::locale('en')->count())->toBe(1);
});

it('entry inCollection scope works', function () {
    $collection1 = Collection::factory()->create(['handle' => 'pages']);
    $collection2 = Collection::factory()->create(['handle' => 'articles']);
    $bp1 = Blueprint::factory()->create(['collection_id' => $collection1->id]);
    $bp2 = Blueprint::factory()->create(['collection_id' => $collection2->id]);

    Entry::factory()->create(['collection_id' => $collection1->id, 'blueprint_id' => $bp1->id]);
    Entry::factory()->create(['collection_id' => $collection2->id, 'blueprint_id' => $bp2->id]);

    expect(Entry::inCollection('pages')->count())->toBe(1);
});

it('entry can have parent and children', function () {
    $collection = Collection::factory()->create(['is_tree' => true]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    $parent = Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id]);
    $child = Entry::factory()->create(['collection_id' => $collection->id, 'blueprint_id' => $blueprint->id, 'parent_id' => $parent->id]);

    expect($parent->children)->toHaveCount(1)
        ->and($child->parent->id)->toBe($parent->id);
});

// ── Multilingual ──

it('entry identifies origin correctly', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    $origin = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'locale' => 'cs',
    ]);

    $translation = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'locale' => 'en',
        'origin_id' => $origin->id,
    ]);

    expect($origin->isOrigin())->toBeTrue()
        ->and($origin->isTranslation())->toBeFalse()
        ->and($translation->isOrigin())->toBeFalse()
        ->and($translation->isTranslation())->toBeTrue();
});

it('entry returns translations', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    $origin = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'locale' => 'cs',
    ]);

    Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'locale' => 'en',
        'origin_id' => $origin->id,
    ]);

    $translations = $origin->getTranslations();

    expect($translations)->toHaveCount(2)
        ->and($translations->keys()->sort()->values()->all())->toBe(['cs', 'en']);
});

it('entry get translation by locale', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    $origin = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'locale' => 'cs',
        'title' => 'Český název',
    ]);

    $en = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'locale' => 'en',
        'origin_id' => $origin->id,
        'title' => 'English title',
    ]);

    expect($origin->getTranslation('en')->title)->toBe('English title')
        ->and($origin->getTranslation('cs')->title)->toBe('Český název');
});

// ── Taxonomy & Terms ──

it('can create taxonomy with terms', function () {
    $taxonomy = Taxonomy::factory()->create([
        'title' => 'Kategorie',
        'handle' => 'categories',
        'is_hierarchical' => true,
    ]);

    $parent = Term::factory()->create([
        'taxonomy_id' => $taxonomy->id,
        'title' => 'Technologie',
        'slug' => 'technologie',
    ]);

    $child = Term::factory()->create([
        'taxonomy_id' => $taxonomy->id,
        'title' => 'PHP',
        'slug' => 'php',
        'parent_id' => $parent->id,
    ]);

    expect($taxonomy->terms)->toHaveCount(2)
        ->and($parent->children)->toHaveCount(1)
        ->and($child->parent->id)->toBe($parent->id);
});

it('entry can be tagged with terms', function () {
    $collection = Collection::factory()->create();
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    $taxonomy = Taxonomy::factory()->create();
    $term = Term::factory()->create(['taxonomy_id' => $taxonomy->id]);

    $entry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
    ]);

    $entry->terms()->attach($term, ['order' => 1]);

    expect($entry->terms)->toHaveCount(1)
        ->and($entry->terms->first()->id)->toBe($term->id);
});

// ── Revisions ──

it('creates revision when entry is created', function () {
    $collection = Collection::factory()->create(['revisions_enabled' => true]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);
    $user = User::factory()->create();

    $this->actingAs($user);

    $entry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'data' => ['content' => 'Hello World'],
    ]);

    expect(Revision::where('entry_id', $entry->id)->count())->toBe(1);
});

it('does not create revision when revisions are disabled', function () {
    $collection = Collection::factory()->create(['revisions_enabled' => false]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    $entry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
    ]);

    expect(Revision::where('entry_id', $entry->id)->count())->toBe(0);
});

// ── GlobalSet ──

it('can create and retrieve global set', function () {
    $globalSet = GlobalSet::create([
        'name' => 'site_test',
        'title' => 'Nastavení webu',
        'handle' => 'site_test',
        'locale' => 'cs',
        'data' => ['site_name' => 'miPress', 'description' => 'CMS'],
    ]);

    expect($globalSet)->not->toBeNull()
        ->and($globalSet->data['site_name'])->toBe('miPress');
});

it('global set findByHandle works', function () {
    GlobalSet::create([
        'name' => 'test_set',
        'title' => 'Test Set',
        'handle' => 'test_set',
        'locale' => 'cs',
        'data' => ['key' => 'value'],
    ]);

    $found = GlobalSet::findByHandle('test_set', 'cs');

    expect($found)->not->toBeNull()
        ->and($found->handle)->toBe('test_set');
});

it('global set getValue works', function () {
    GlobalSet::create([
        'name' => 'val_test',
        'title' => 'Test Set',
        'handle' => 'val_test',
        'locale' => 'cs',
        'data' => ['site_name' => 'miPress'],
    ]);

    expect(GlobalSet::getValue('val_test', 'site_name'))->toBe('miPress')
        ->and(GlobalSet::getValue('val_test', 'nonexistent', 'default'))->toBe('default');
});

// ── EntryStatus Enum ──

it('entry status has correct icon and color', function () {
    expect(EntryStatus::Published->icon())->not->toBeEmpty()
        ->and(EntryStatus::Published->color())->toBe('success')
        ->and(EntryStatus::Draft->color())->toBe('gray')
        ->and(EntryStatus::Scheduled->color())->toBe('info')
        ->and(EntryStatus::Archived->color())->toBe('warning');
});
