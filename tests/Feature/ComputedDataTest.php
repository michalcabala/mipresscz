<?php

use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Services\ComputedFieldRegistry;

// ── Helper ──

function createEntryWithContent(array $content = [], array $overrides = []): Entry
{
    $collection = Collection::factory()->create([
        'handle' => $overrides['collection_handle'] ?? 'articles',
        'route_template' => '/{slug}',
    ]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    return Entry::factory()->create(array_merge([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'title' => 'Test Entry',
        'slug' => 'test-entry',
        'status' => EntryStatus::Published,
        'published_at' => now(),
        'locale' => 'cs',
        'content' => $content,
    ], $overrides));
}

function masonBlock(string $brickId, array $config): array
{
    return [
        'type' => 'masonBrick',
        'attrs' => [
            'id' => $brickId,
            'config' => $config,
        ],
    ];
}

// ══════════════════════════════════════════════════════
// ComputedFieldRegistry
// ══════════════════════════════════════════════════════

describe('ComputedFieldRegistry', function () {
    it('registers and retrieves callbacks for a scope', function () {
        $registry = new ComputedFieldRegistry;

        $registry->register('articles', 'word_count', fn () => 42);

        $callbacks = $registry->getCallbacks('articles');

        expect($callbacks)->toHaveCount(1);
        expect($callbacks->has('word_count'))->toBeTrue();
        expect($callbacks->get('word_count')())->toBe(42);
    });

    it('registers multiple fields at once via array syntax', function () {
        $registry = new ComputedFieldRegistry;

        $registry->register('articles', [
            'word_count' => fn () => 100,
            'reading_time' => fn () => 1,
        ]);

        $callbacks = $registry->getCallbacks('articles');

        expect($callbacks)->toHaveCount(2);
        expect($callbacks->has('word_count'))->toBeTrue();
        expect($callbacks->has('reading_time'))->toBeTrue();
    });

    it('registers the same fields for multiple scopes', function () {
        $registry = new ComputedFieldRegistry;

        $registry->register(['articles', 'blog'], 'word_count', fn () => 50);

        expect($registry->getCallbacks('articles'))->toHaveCount(1);
        expect($registry->getCallbacks('blog'))->toHaveCount(1);
    });

    it('returns empty collection for unknown scope', function () {
        $registry = new ComputedFieldRegistry;

        expect($registry->getCallbacks('nonexistent'))->toBeEmpty();
    });

    it('isolates callbacks between scopes', function () {
        $registry = new ComputedFieldRegistry;

        $registry->register('articles', 'word_count', fn () => 100);
        $registry->register('pages', 'page_depth', fn () => 2);

        expect($registry->getCallbacks('articles'))->toHaveCount(1);
        expect($registry->getCallbacks('articles')->has('word_count'))->toBeTrue();
        expect($registry->getCallbacks('articles')->has('page_depth'))->toBeFalse();

        expect($registry->getCallbacks('pages'))->toHaveCount(1);
        expect($registry->getCallbacks('pages')->has('page_depth'))->toBeTrue();
    });

    it('checks if a specific field is registered', function () {
        $registry = new ComputedFieldRegistry;

        $registry->register('articles', 'word_count', fn () => 42);

        expect($registry->has('articles', 'word_count'))->toBeTrue();
        expect($registry->has('articles', 'missing'))->toBeFalse();
        expect($registry->has('pages', 'word_count'))->toBeFalse();
    });

    it('merges wildcard callbacks with scoped callbacks', function () {
        $registry = new ComputedFieldRegistry;

        $registry->register(ComputedFieldRegistry::WILDCARD, 'global_field', fn () => 'global');
        $registry->register('articles', 'scoped_field', fn () => 'scoped');

        $callbacks = $registry->getCallbacks('articles');

        expect($callbacks)->toHaveCount(2);
        expect($callbacks->has('global_field'))->toBeTrue();
        expect($callbacks->has('scoped_field'))->toBeTrue();
    });

    it('scoped callbacks override wildcard callbacks', function () {
        $registry = new ComputedFieldRegistry;

        $registry->register(ComputedFieldRegistry::WILDCARD, 'word_count', fn () => 'default');
        $registry->register('articles', 'word_count', fn () => 'custom');

        $callbacks = $registry->getCallbacks('articles');

        expect($callbacks)->toHaveCount(1);
        expect($callbacks->get('word_count')())->toBe('custom');
    });

    it('detects wildcard fields via has()', function () {
        $registry = new ComputedFieldRegistry;

        $registry->register(ComputedFieldRegistry::WILDCARD, 'global_field', fn () => 1);

        expect($registry->has('any_scope', 'global_field'))->toBeTrue();
        expect($registry->has('any_scope', 'missing'))->toBeFalse();
    });
});

// ══════════════════════════════════════════════════════
// ContainsComputedData trait (on Entry)
// ══════════════════════════════════════════════════════

describe('ContainsComputedData', function () {
    it('returns computed value from registered callback', function () {
        $registry = app(ComputedFieldRegistry::class);
        $registry->register('articles', 'word_count', fn (Entry $entry) => 42);

        $entry = createEntryWithContent();

        expect($entry->getComputed('word_count'))->toBe(42);
    });

    it('returns null for unregistered computed field', function () {
        $entry = createEntryWithContent();

        expect($entry->getComputed('nonexistent'))->toBeNull();
    });

    it('returns all computed data as a collection', function () {
        $registry = app(ComputedFieldRegistry::class);
        $registry->register('articles', [
            'field_a' => fn () => 'alpha',
            'field_b' => fn () => 'beta',
        ]);

        $entry = createEntryWithContent();

        $data = $entry->computedData();

        // Includes wildcard defaults (word_count, reading_time) + custom fields
        expect($data->get('field_a'))->toBe('alpha');
        expect($data->get('field_b'))->toBe('beta');
        expect($data->has('word_count'))->toBeTrue();
        expect($data->has('reading_time'))->toBeTrue();
    });

    it('returns computed keys', function () {
        $registry = app(ComputedFieldRegistry::class);
        $registry->register('articles', [
            'x' => fn () => 1,
            'y' => fn () => 2,
        ]);

        $entry = createEntryWithContent();

        $keys = $entry->computedKeys()->values()->all();

        // Wildcard defaults + scoped fields
        expect($keys)->toContain('word_count');
        expect($keys)->toContain('reading_time');
        expect($keys)->toContain('x');
        expect($keys)->toContain('y');
    });

    it('returns empty collection when entry has no collection', function () {
        $entry = new Entry;

        expect($entry->computedData())->toBeEmpty();
        expect($entry->computedKeys())->toBeEmpty();
    });

    it('passes the entry instance to the callback', function () {
        $registry = app(ComputedFieldRegistry::class);
        $registry->register('articles', 'title_upper', fn (Entry $entry) => strtoupper($entry->title));

        $entry = createEntryWithContent(overrides: ['title' => 'Hello World']);

        expect($entry->getComputed('title_upper'))->toBe('HELLO WORLD');
    });

    it('prevents infinite recursion via instanceWithoutComputed', function () {
        $registry = app(ComputedFieldRegistry::class);

        // A callback that tries to call getComputed on the passed instance
        // should get null back (computed data is disabled on the clone)
        $registry->register('articles', 'safe_field', function (Entry $entry) {
            // This should return null (no recursion)
            return $entry->getComputed('safe_field') ?? 'safe_value';
        });

        $entry = createEntryWithContent();

        expect($entry->getComputed('safe_field'))->toBe('safe_value');
    });
});

// ══════════════════════════════════════════════════════
// Entry::getPlainTextContent()
// ══════════════════════════════════════════════════════

describe('Entry::getPlainTextContent', function () {
    it('extracts text from a single text brick', function () {
        $entry = createEntryWithContent([
            masonBlock('text', ['content' => '<p>Hello world, this is a test.</p>']),
        ]);

        $text = $entry->getPlainTextContent();

        expect($text)->toBe('Hello world, this is a test.');
    });

    it('extracts text from multiple bricks', function () {
        $entry = createEntryWithContent([
            masonBlock('heading', ['heading' => 'Main Title']),
            masonBlock('text', ['content' => '<p>Paragraph one.</p>']),
            masonBlock('text', ['content' => '<p>Paragraph two.</p>']),
        ]);

        $text = $entry->getPlainTextContent();

        expect($text)->toContain('Main Title');
        expect($text)->toContain('Paragraph one.');
        expect($text)->toContain('Paragraph two.');
    });

    it('extracts text from hero brick with multiple text fields', function () {
        $entry = createEntryWithContent([
            masonBlock('hero', [
                'eyebrow' => 'Welcome',
                'heading' => 'Hero Title',
                'subheading' => 'A subtitle here.',
            ]),
        ]);

        $text = $entry->getPlainTextContent();

        expect($text)->toContain('Welcome');
        expect($text)->toContain('Hero Title');
        expect($text)->toContain('A subtitle here.');
    });

    it('extracts text from nested items arrays', function () {
        $entry = createEntryWithContent([
            masonBlock('features', [
                'heading' => 'Our Features',
                'items' => [
                    ['title' => 'Feature One', 'description' => 'Desc one.'],
                    ['title' => 'Feature Two', 'description' => 'Desc two.'],
                ],
            ]),
        ]);

        $text = $entry->getPlainTextContent();

        expect($text)->toContain('Our Features');
        expect($text)->toContain('Feature One');
        expect($text)->toContain('Desc two.');
    });

    it('strips HTML tags from content', function () {
        $entry = createEntryWithContent([
            masonBlock('text', ['content' => '<p>Hello <strong>bold</strong> <a href="#">link</a></p>']),
        ]);

        $text = $entry->getPlainTextContent();

        expect($text)->not->toContain('<');
        expect($text)->toContain('Hello bold link');
    });

    it('returns empty string for null content', function () {
        $entry = createEntryWithContent();

        expect($entry->getPlainTextContent())->toBe('');
    });

    it('returns empty string for empty array content', function () {
        $entry = createEntryWithContent([]);

        expect($entry->getPlainTextContent())->toBe('');
    });
});

// ══════════════════════════════════════════════════════
// Default computed fields (word_count, reading_time)
// ══════════════════════════════════════════════════════

describe('Default computed fields', function () {
    it('computes word_count from Mason content', function () {
        // Default fields are registered for all active collections in ServiceProvider.
        // We create a collection with handle matching an active one, then verify.
        $entry = createEntryWithContent([
            masonBlock('text', ['content' => '<p>One two three four five six seven eight nine ten.</p>']),
        ]);

        $wordCount = $entry->getComputed('word_count');

        expect($wordCount)->toBe(10);
    });

    it('computes reading_time from word count', function () {
        // 200 words = 1 min, 400 = 2 min, etc.
        // Generate approximately 250 words
        $words = implode(' ', array_fill(0, 250, 'word'));
        $entry = createEntryWithContent([
            masonBlock('text', ['content' => "<p>{$words}</p>"]),
        ]);

        $readingTime = $entry->getComputed('reading_time');

        // 250 / 200 = 1.25 → ceil = 2
        expect($readingTime)->toBe(2);
    });

    it('returns minimum 1 minute reading time for short content', function () {
        $entry = createEntryWithContent([
            masonBlock('text', ['content' => '<p>Short.</p>']),
        ]);

        $readingTime = $entry->getComputed('reading_time');

        expect($readingTime)->toBe(1);
    });

    it('computes word_count across multiple bricks', function () {
        $entry = createEntryWithContent([
            masonBlock('heading', ['heading' => 'Title Here']), // 2 words
            masonBlock('text', ['content' => '<p>Three more words.</p>']), // 3 words
        ]);

        $wordCount = $entry->getComputed('word_count');

        expect($wordCount)->toBe(5);
    });

    it('returns zero word_count for entry without content', function () {
        $entry = createEntryWithContent();

        $wordCount = $entry->getComputed('word_count');

        expect($wordCount)->toBe(0);
    });
});
