<?php

use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\Locale;

it('generates sitemap with published entries only', function () {
    $sitemapPath = public_path('sitemap.xml');

    if (file_exists($sitemapPath)) {
        unlink($sitemapPath);
    }

    Locale::factory()->default()->create(['code' => 'cs']);

    $collection = Collection::factory()->create([
        'handle' => 'pages',
        'route_template' => '/{slug}',
    ]);

    $blueprint = Blueprint::factory()->create([
        'collection_id' => $collection->id,
    ]);

    $publishedEntry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'locale' => 'cs',
        'slug' => 'sitemap-published',
        'status' => EntryStatus::Published,
        'published_at' => now()->subMinute(),
    ]);

    $draftEntry = Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'locale' => 'cs',
        'slug' => 'sitemap-draft',
        'status' => EntryStatus::Draft,
        'published_at' => null,
    ]);

    $this->artisan('filament-sitemap-generator:generate')->assertExitCode(0);

    expect(file_exists($sitemapPath))->toBeTrue();

    $sitemapContent = file_get_contents($sitemapPath);
    expect($sitemapContent)->toContain($publishedEntry->getFullUrl());
    expect($sitemapContent)->not->toContain($draftEntry->getFullUrl());

    unlink($sitemapPath);
});
