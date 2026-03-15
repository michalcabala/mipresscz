<?php

use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\Locale;

// ── Helpers ──

function seoTestEntry(array $attributes = []): Entry
{
    $collection = Collection::factory()->create([
        'handle' => 'pages',
        'route_template' => '/{slug}',
    ]);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    return Entry::factory()->published()->create(array_merge([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'locale' => 'cs',
    ], $attributes));
}

// ── Entry SEO model fields ──

it('stores meta_title on entry', function () {
    $entry = seoTestEntry(['meta_title' => 'Vlastní SEO titulek']);

    expect($entry->fresh()->meta_title)->toBe('Vlastní SEO titulek');
});

it('stores meta_description on entry', function () {
    $entry = seoTestEntry(['meta_description' => 'Popis pro vyhledávače.']);

    expect($entry->fresh()->meta_description)->toBe('Popis pro vyhledávače.');
});

it('meta_title defaults to null when not set', function () {
    $entry = seoTestEntry(['title' => 'Základní stránka']);

    expect($entry->fresh()->meta_title)->toBeNull();
});

// ── Meta tags in HTML ──

it('shows entry title in <title> when meta_title is not set', function () {
    Locale::factory()->default()->create(['code' => 'cs']);

    $entry = seoTestEntry(['title' => 'Hlavní stránka', 'meta_title' => null]);

    $this->get($entry->uri)->assertSee('<title>Hlavní stránka</title>', false);
});

it('shows meta_title in <title> when set', function () {
    Locale::factory()->default()->create(['code' => 'cs']);

    $entry = seoTestEntry(['title' => 'Hlavní stránka', 'meta_title' => 'Vlastní titulek pro SEO']);

    $this->get($entry->uri)->assertSee('<title>Vlastní titulek pro SEO</title>', false);
});

it('shows meta description tag when set', function () {
    Locale::factory()->default()->create(['code' => 'cs']);

    $entry = seoTestEntry(['meta_description' => 'Krátký popis stránky pro Google.']);

    $this->get($entry->uri)->assertSee('Krátký popis stránky pro Google.');
});

it('does not show meta description tag when not set', function () {
    Locale::factory()->default()->create(['code' => 'cs']);

    $entry = seoTestEntry(['meta_description' => null]);

    $this->get($entry->uri)->assertDontSee('<meta name="description"', false);
});

// ── Canonical URL ──

it('outputs canonical link tag', function () {
    Locale::factory()->default()->create(['code' => 'cs']);

    $entry = seoTestEntry(['slug' => 'test-stranka']);

    $this->get($entry->uri)
        ->assertSee('<link rel="canonical"', false)
        ->assertSee($entry->uri);
});

// ── Hreflang ──

it('does not output hreflang when no translations exist', function () {
    Locale::factory()->default()->create(['code' => 'cs']);

    $entry = seoTestEntry(['slug' => 'solo-stranka']);

    $this->get($entry->uri)->assertDontSee('hreflang');
});

it('outputs hreflang links when translations exist', function () {
    Locale::factory()->default()->create(['code' => 'cs']);
    Locale::factory()->create(['code' => 'en', 'is_default' => false]);

    $collection = Collection::factory()->create(['handle' => 'pages', 'route_template' => '/{slug}']);
    $blueprint = Blueprint::factory()->create(['collection_id' => $collection->id]);

    $origin = Entry::factory()->published()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'locale' => 'cs',
        'slug' => 'o-nas',
    ]);

    Entry::factory()->published()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'locale' => 'en',
        'slug' => 'about-us',
        'origin_id' => $origin->id,
    ]);

    $response = $this->get($origin->uri);

    $response->assertSee('hreflang');
    $response->assertSee('rel="alternate"', false);
    $response->assertSee('x-default');
});

// ── Sitemap ──

it('sitemap returns 200 with xml content type', function () {
    $sitemapPath = public_path('sitemap.xml');
    $originalSitemap = file_exists($sitemapPath) ? file_get_contents($sitemapPath) : null;

    file_put_contents($sitemapPath, <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>https://mipresscz.test</loc>
    </url>
</urlset>
XML);

    try {
        $response = $this->get('/sitemap.xml')
            ->assertSuccessful()
            ->assertHeader('Content-Type', 'application/xml; charset=utf-8');

        expect($response->baseResponse)->toBeInstanceOf(\Symfony\Component\HttpFoundation\BinaryFileResponse::class);
        expect($response->baseResponse->getFile()->getPathname())->toBe($sitemapPath);
    } finally {
        if ($originalSitemap === null) {
            @unlink($sitemapPath);
        } else {
            file_put_contents($sitemapPath, $originalSitemap);
        }
    }
});

it('sitemap returns not found when static file is missing', function () {
    $sitemapPath = public_path('sitemap.xml');
    $backupPath = public_path('sitemap.xml.bak.testing');

    if (file_exists($backupPath)) {
        @unlink($backupPath);
    }

    if (file_exists($sitemapPath)) {
        rename($sitemapPath, $backupPath);
    }

    try {
        $this->get('/sitemap.xml')->assertNotFound();
    } finally {
        if (file_exists($backupPath)) {
            rename($backupPath, $sitemapPath);
        }
    }
});

// ── RSS Feed ──

it('rss feed returns 200 with xml content type', function () {
    Locale::factory()->default()->create(['code' => 'cs']);

    $response = $this->get('/feed.xml')->assertStatus(200);

    expect((string) $response->headers->get('Content-Type'))
        ->toStartWith('application/xml');
});

it('rss feed contains published entry titles', function () {
    Locale::factory()->default()->create(['code' => 'cs']);

    $entry = seoTestEntry(['title' => 'Článek do feedu']);

    $this->get('/feed.xml')->assertSee('Článek do feedu');
});

it('rss feed contains rss root element', function () {
    Locale::factory()->default()->create(['code' => 'cs']);

    $this->get('/feed.xml')->assertSee('<rss version="2.0"', false);
});

// ── Robots.txt ──

it('robots txt route returns 200', function () {
    $this->get('/robots.txt')->assertSuccessful();
});
