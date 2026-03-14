<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Events\EntryDeleted;
use MiPressCz\Core\Events\EntrySaved;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\Locale;
use MiPressCz\Core\Services\CacheService;

// ── Helper ──

function createPublishedEntry(array $overrides = []): Entry
{
    $collection = Collection::factory()->create([
        'handle' => $overrides['collection_handle'] ?? 'pages',
        'route_template' => '/{slug}',
    ]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    return Entry::factory()->create(array_merge([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'title' => 'Test Page',
        'slug' => 'test-page',
        'status' => EntryStatus::Published,
        'published_at' => now()->subDay(),
        'locale' => 'cs',
    ], $overrides));
}

// ══════════════════════════════════════════════════════
// CacheService
// ══════════════════════════════════════════════════════

describe('CacheService', function () {
    it('generates deterministic page cache keys', function () {
        $cache = app(CacheService::class);

        $key1 = $cache->getPageKey('/about', 'cs');
        $key2 = $cache->getPageKey('/about', 'cs');
        $key3 = $cache->getPageKey('/about', 'en');

        expect($key1)->toBe($key2);
        expect($key1)->not->toBe($key3);
        expect($key1)->toStartWith(CacheService::PREFIX);
    });

    it('stores and retrieves page cache', function () {
        $cache = app(CacheService::class);

        expect($cache->getPage('/test', 'cs'))->toBeNull();

        $cache->putPage('/test', 'cs', '<html>cached</html>');

        expect($cache->getPage('/test', 'cs'))->toBe('<html>cached</html>');
    });

    it('flushes specific page by uri and locale', function () {
        $cache = app(CacheService::class);

        $cache->putPage('/page-a', 'cs', 'A-cs');
        $cache->putPage('/page-a', 'en', 'A-en');
        $cache->putPage('/page-b', 'cs', 'B-cs');

        $cache->flushPage('/page-a', 'cs');

        expect($cache->getPage('/page-a', 'cs'))->toBeNull();
        expect($cache->getPage('/page-a', 'en'))->toBe('A-en');
        expect($cache->getPage('/page-b', 'cs'))->toBe('B-cs');
    });

    it('flushes entry for all locales', function () {
        Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'is_active' => true, 'order' => 1]);
        Locale::factory()->create(['code' => 'en', 'is_default' => false, 'is_active' => true, 'order' => 2]);
        locales()->clearCache();

        $cache = app(CacheService::class);
        $cache->putPage('/about', 'cs', 'cs-about');
        $cache->putPage('/about', 'en', 'en-about');

        $cache->flushEntry('/about');

        expect($cache->getPage('/about', 'cs'))->toBeNull();
        expect($cache->getPage('/about', 'en'))->toBeNull();
    });

    it('flushes nav cache', function () {
        Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'is_active' => true, 'order' => 1]);
        locales()->clearCache();

        $cache = app(CacheService::class);

        // Populate nav cache
        $cache->getNav('header', 'cs', fn () => collect(['Home']));
        expect(Cache::has($cache->getNavKey('header', 'cs')))->toBeTrue();

        $cache->flushNav();
        expect(Cache::has($cache->getNavKey('header', 'cs')))->toBeFalse();
    });

    it('caches nav query result', function () {
        $cache = app(CacheService::class);
        $callCount = 0;

        $result1 = $cache->getNav('header', 'cs', function () use (&$callCount) {
            $callCount++;

            return collect(['Home', 'About']);
        });

        $result2 = $cache->getNav('header', 'cs', function () use (&$callCount) {
            $callCount++;

            return collect(['Should not be called']);
        });

        expect($callCount)->toBe(1);
        expect($result1)->toEqual($result2);
    });

    it('flushes all pages', function () {
        $cache = app(CacheService::class);

        $cache->putPage('/a', 'cs', 'A');
        $cache->putPage('/b', 'cs', 'B');

        $cache->flushAllPages();

        expect($cache->getPage('/a', 'cs'))->toBeNull();
        expect($cache->getPage('/b', 'cs'))->toBeNull();
    });
});

// ══════════════════════════════════════════════════════
// PageCache Middleware
// ══════════════════════════════════════════════════════

describe('PageCache Middleware', function () {
    it('caches GET entry response', function () {
        Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'is_active' => true, 'order' => 1]);
        locales()->clearCache();

        $entry = createPublishedEntry();
        $cache = app(CacheService::class);

        // First request — MISS
        $response = $this->get($entry->uri);
        $response->assertOk();
        $response->assertHeader('X-Page-Cache', 'MISS');

        // Second request — HIT
        $response2 = $this->get($entry->uri);
        $response2->assertOk();
        $response2->assertHeader('X-Page-Cache', 'HIT');

        // Verify cached content exists
        expect($cache->getPage($entry->uri, 'cs'))->not->toBeNull();
    });

    it('does not cache POST requests', function () {
        Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'is_active' => true, 'order' => 1]);
        locales()->clearCache();

        $response = $this->post('/test-page');
        $response->assertHeaderMissing('X-Page-Cache');
    });

    it('does not cache requests with query strings', function () {
        Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'is_active' => true, 'order' => 1]);
        locales()->clearCache();

        $entry = createPublishedEntry();

        $response = $this->get($entry->uri.'?page=2');
        $response->assertHeaderMissing('X-Page-Cache');
    });

    it('does not cache authenticated requests', function () {
        Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'is_active' => true, 'order' => 1]);
        locales()->clearCache();

        $entry = createPublishedEntry();
        $user = \App\Models\User::factory()->create();

        $response = $this->actingAs($user)->get($entry->uri);
        $response->assertOk();
        $response->assertHeaderMissing('X-Page-Cache');
    });

    it('does not cache 404 responses', function () {
        Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'is_active' => true, 'order' => 1]);
        locales()->clearCache();

        $response = $this->get('/nonexistent-page');
        $response->assertNotFound();

        $cache = app(CacheService::class);
        expect($cache->getPage('/nonexistent-page', 'cs'))->toBeNull();
    });
});

// ══════════════════════════════════════════════════════
// Cache Invalidation
// ══════════════════════════════════════════════════════

describe('Cache Invalidation', function () {
    it('flushes page cache when entry is saved', function () {
        Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'is_active' => true, 'order' => 1]);
        locales()->clearCache();

        $entry = createPublishedEntry();
        $cache = app(CacheService::class);

        // Simulate cached page
        $cache->putPage($entry->uri, 'cs', '<html>old content</html>');
        expect($cache->getPage($entry->uri, 'cs'))->not->toBeNull();

        // Update entry — triggers EntrySaved
        $entry->update(['title' => 'Updated Title']);

        // Cache should be flushed
        expect($cache->getPage($entry->uri, 'cs'))->toBeNull();
    });

    it('flushes page cache when entry is deleted', function () {
        Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'is_active' => true, 'order' => 1]);
        locales()->clearCache();

        $entry = createPublishedEntry();
        $cache = app(CacheService::class);
        $uri = $entry->uri;

        $cache->putPage($uri, 'cs', '<html>cached</html>');

        $entry->delete();

        expect($cache->getPage($uri, 'cs'))->toBeNull();
    });

    it('dispatches EntryDeleted event on delete', function () {
        Event::fake([EntryDeleted::class]);

        $entry = createPublishedEntry();
        $entry->delete();

        Event::assertDispatched(EntryDeleted::class, function (EntryDeleted $e) use ($entry) {
            return $e->entry->id === $entry->id;
        });
    });

    it('flushes homepage cache when homepage entry changes', function () {
        Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'is_active' => true, 'order' => 1]);
        locales()->clearCache();

        $entry = createPublishedEntry(['is_homepage' => true]);
        $cache = app(CacheService::class);

        $cache->putPage('/', 'cs', '<html>homepage</html>');

        $entry->update(['title' => 'New Home']);

        expect($cache->getPage('/', 'cs'))->toBeNull();
    });

    it('flushes nav cache when entry is saved', function () {
        Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'is_active' => true, 'order' => 1]);
        locales()->clearCache();

        $cache = app(CacheService::class);
        $cache->getNav('header', 'cs', fn () => collect(['nav data']));

        expect(Cache::has($cache->getNavKey('header', 'cs')))->toBeTrue();

        $entry = createPublishedEntry();
        $entry->update(['title' => 'Changed']);

        expect(Cache::has($cache->getNavKey('header', 'cs')))->toBeFalse();
    });

    it('flushes all cache when locale changes', function () {
        $locale = Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'is_active' => true, 'order' => 1]);
        locales()->clearCache();

        $cache = app(CacheService::class);
        $cache->putPage('/test', 'cs', '<html>cached</html>');

        $locale->update(['native_name' => 'Čeština (updated)']);

        expect($cache->getPage('/test', 'cs'))->toBeNull();
    });
});

// ══════════════════════════════════════════════════════
// NavComposer
// ══════════════════════════════════════════════════════

describe('NavComposer', function () {
    it('provides cached nav entries to header', function () {
        Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'is_active' => true, 'order' => 1]);
        locales()->clearCache();

        $entry = createPublishedEntry(['title' => 'About Us', 'slug' => 'about-us', 'is_homepage' => false]);

        $cache = app(CacheService::class);

        // Verify cache key is populated after first request
        $this->get('/');
        expect(Cache::has($cache->getNavKey('header', 'cs')))->toBeTrue();

        // Second request should use cached nav
        $this->get('/');
        expect(Cache::has($cache->getNavKey('header', 'cs')))->toBeTrue();
    });
});
