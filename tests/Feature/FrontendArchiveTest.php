<?php

use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\Locale;

beforeEach(function () {
    Locale::query()->delete();
    locales()->clearCache();

    Locale::factory()->create([
        'code' => 'cs',
        'is_default' => true,
        'is_active' => true,
        'is_frontend_available' => true,
        'url_prefix' => null,
        'order' => 1,
    ]);

    locales()->clearCache();
});

it('renders a collection archive page from the route template root', function () {
    $collection = Collection::factory()->create([
        'title' => 'Články',
        'handle' => 'articles',
        'route_template' => '/blog/{slug}',
        'is_active' => true,
    ]);

    $blueprint = Blueprint::factory()->create([
        'collection_id' => $collection->id,
        'handle' => 'article',
    ]);

    Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'title' => 'První článek',
        'slug' => 'prvni-clanek',
        'uri' => '/blog/prvni-clanek',
        'locale' => 'cs',
        'status' => EntryStatus::Published,
        'published_at' => now()->subDay(),
    ]);

    Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'title' => 'Druhý článek',
        'slug' => 'druhy-clanek',
        'uri' => '/blog/druhy-clanek',
        'locale' => 'cs',
        'status' => EntryStatus::Published,
        'published_at' => now()->subDays(2),
    ]);

    $this->get('/blog?view=list')
        ->assertOk()
        ->assertSee('Články')
        ->assertSee('První článek')
        ->assertSee('Druhý článek')
        ->assertSee('view=grid', false)
        ->assertSee('view=list', false)
        ->assertSee('Archiv kolekce');
});

it('includes class-based theme bootstrap in the frontend layout', function () {
    $collection = Collection::factory()->create([
        'title' => 'Články',
        'handle' => 'articles',
        'route_template' => '/blog/{slug}',
        'is_active' => true,
    ]);

    $blueprint = Blueprint::factory()->create([
        'collection_id' => $collection->id,
        'handle' => 'article',
    ]);

    Entry::factory()->create([
        'collection_id' => $collection->id,
        'blueprint_id' => $blueprint->id,
        'title' => 'První článek',
        'slug' => 'prvni-clanek',
        'uri' => '/blog/prvni-clanek',
        'locale' => 'cs',
        'status' => EntryStatus::Published,
        'published_at' => now()->subDay(),
    ]);

    $this->get('/blog')
        ->assertOk()
        ->assertDontSee('cdn.tailwindcss.com', false)
        ->assertSee("localStorage.getItem('theme')", false)
        ->assertSee("root.classList.toggle('dark', resolvedTheme === 'dark');", false)
        ->assertSee('function miPressApplyTheme(theme)', false)
        ->assertSee('function miPressToggleTheme()', false);
});
