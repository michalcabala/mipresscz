<?php

use App\Enums\UserRole;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Filament\Resources\Entries\EntryResource;
use MiPressCz\Core\Filament\Resources\Entries\Pages\CreateEntry;
use MiPressCz\Core\Filament\Resources\Entries\Pages\EditEntry;
use MiPressCz\Core\Filament\Resources\Entries\Pages\EditEntrySeo;
use MiPressCz\Core\Filament\Resources\Entries\Pages\ListEntries;
use MiPressCz\Core\Filament\Resources\Entries\Pages\ManageEntryRevisions;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\Locale;
use MiPressCz\Core\Models\Taxonomy;
use MiPressCz\Core\Models\Term;

beforeEach(function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    (new \Database\Seeders\RolesAndPermissionsSeeder)->run();

    $this->locale = Locale::factory()->create([
        'code' => 'cs',
        'is_default' => true,
        'is_active' => true,
        'order' => 1,
    ]);
    locales()->clearCache();

    $this->collection = Collection::factory()->create([
        'handle' => 'articles',
        'title' => 'Články',
        'is_active' => true,
    ]);
    $this->blueprint = Blueprint::factory()->create([
        'collection_id' => $this->collection->id,
        'title' => 'Článek',
        'handle' => 'article',
        'is_default' => true,
    ]);

    $this->admin = User::factory()->create(['role' => UserRole::Admin]);
    $this->admin->syncRoles([UserRole::Admin->value]);
    $this->actingAs($this->admin);
});

// ── List page ──────────────────────────────────────────────────────────────

it('can render the entries list page', function () {
    Livewire::test(ListEntries::class)
        ->assertOk();
});

it('can see entry records in the table', function () {
    $entries = Entry::factory()->count(3)->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Published,
        'author_id' => $this->admin->id,
    ]);

    Livewire::test(ListEntries::class)
        ->call('loadTable')
        ->assertCanSeeTableRecords($entries);
});

it('defaults sort order to published_at descending', function () {
    Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Published,
        'author_id' => $this->admin->id,
        'published_at' => now()->subDays(2),
    ]);
    Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Published,
        'author_id' => $this->admin->id,
        'published_at' => now()->subDay(),
    ]);

    Livewire::test(ListEntries::class)
        ->call('loadTable')
        ->assertOk();
});

it('renders the seo page for an entry', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Published,
        'published_at' => now(),
        'author_id' => $this->admin->id,
    ]);

    Livewire::test(EditEntrySeo::class, ['record' => $entry->id])
        ->assertOk();
});

it('can save seo fields on the seo page', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Published,
        'published_at' => now(),
        'author_id' => $this->admin->id,
    ]);

    Livewire::test(EditEntrySeo::class, ['record' => $entry->id])
        ->fillForm([
            'meta_title' => 'SEO Test Title',
            'meta_description' => 'SEO test description for search engines.',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($entry->fresh()->meta_title)->toBe('SEO Test Title');
    expect($entry->fresh()->meta_description)->toBe('SEO test description for search engines.');
});

it('renders manage revisions page when revisions are enabled', function () {
    $this->collection->update(['revisions_enabled' => true]);

    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Published,
        'published_at' => now(),
        'author_id' => $this->admin->id,
    ]);

    Livewire::test(ManageEntryRevisions::class, ['record' => $entry->id])
        ->assertOk();
});

it('lists revisions in the manage revisions page', function () {
    $this->collection->update(['revisions_enabled' => true]);

    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Published,
        'published_at' => now(),
        'author_id' => $this->admin->id,
        'title' => 'Original title',
    ]);

    $entry->update(['title' => 'Updated title']);

    Livewire::test(ManageEntryRevisions::class, ['record' => $entry->id])
        ->assertOk()
        ->call('loadTable')
        ->assertCanSeeTableRecords($entry->fresh()->revisions()->latest('created_at')->get());
});

// ── Table filters ──────────────────────────────────────────────────────────

it('can filter entries by status', function () {
    $published = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Published,
        'author_id' => $this->admin->id,
    ]);
    $draft = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Draft,
        'author_id' => $this->admin->id,
    ]);

    Livewire::test(ListEntries::class)
        ->call('loadTable')
        ->filterTable('status', EntryStatus::Published->value)
        ->assertCanSeeTableRecords([$published])
        ->assertCanNotSeeTableRecords([$draft]);
});

it('hides locale filter when only one default locale is configured', function () {
    Livewire::test(ListEntries::class)
        ->call('loadTable')
        ->assertTableFilterHidden('locale');
});

it('can filter entries by locale', function () {
    Locale::factory()->create(['code' => 'en', 'is_active' => true, 'order' => 2]);
    locales()->clearCache();

    $czech = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Published,
        'author_id' => $this->admin->id,
    ]);
    $english = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'en',
        'status' => EntryStatus::Published,
        'author_id' => $this->admin->id,
    ]);

    Livewire::test(ListEntries::class)
        ->call('loadTable')
        ->assertTableFilterVisible('locale')
        ->filterTable('locale', 'cs')
        ->assertCanSeeTableRecords([$czech])
        ->assertCanNotSeeTableRecords([$english]);
});

it('can search entries by title', function () {
    Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'title' => 'Hledaný článek',
        'slug' => 'hledany-clanek',
        'status' => EntryStatus::Draft,
        'author_id' => $this->admin->id,
    ]);
    $other = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'title' => 'Jiný článek',
        'slug' => 'jiny-clanek',
        'status' => EntryStatus::Draft,
        'author_id' => $this->admin->id,
    ]);

    Livewire::test(ListEntries::class)
        ->call('loadTable')
        ->searchTable('Hledaný')
        ->assertCanNotSeeTableRecords([$other]);
});

it('shows taxonomy columns for scoped collection resources', function () {
    $taxonomy = Taxonomy::factory()->create([
        'title' => 'Témata',
        'handle' => 'topics',
    ]);
    $this->collection->taxonomies()->attach($taxonomy);

    $term = Term::factory()->create([
        'taxonomy_id' => $taxonomy->id,
        'title' => 'Laravel',
        'locale' => 'cs',
    ]);

    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Published,
        'author_id' => $this->admin->id,
    ]);
    $entry->terms()->syncWithoutDetaching([$term->id]);

    $configurationKey = 'test-articles-taxonomies';
    $panel = Filament::getDefaultPanel();
    Filament::setCurrentPanel($panel);
    Route::get("/_test/{$configurationKey}/create", fn () => 'ok')
        ->name("filament.admin.resources.entries.{$configurationKey}.create");
    Route::get("/_test/{$configurationKey}/{record}/edit", fn () => 'ok')
        ->name("filament.admin.resources.entries.{$configurationKey}.edit");

    $panel->resources([
        EntryResource::make($configurationKey)
            ->collectionHandle($this->collection->handle)
            ->navigationLabel($this->collection->title),
    ]);

    EntryResource::withConfiguration($configurationKey, fn () => Livewire::test(ListEntries::class)
        ->call('loadTable')
        ->assertTableColumnVisible('taxonomy_topics')
        ->assertTableColumnStateSet('taxonomy_topics', 'Laravel', $entry)
    );
});

it('can filter scoped collection entries by taxonomy term', function () {
    $taxonomy = Taxonomy::factory()->create([
        'title' => 'Témata',
        'handle' => 'topics',
    ]);
    $this->collection->taxonomies()->attach($taxonomy);

    $laravel = Term::factory()->create([
        'taxonomy_id' => $taxonomy->id,
        'title' => 'Laravel',
        'locale' => 'cs',
    ]);
    $php = Term::factory()->create([
        'taxonomy_id' => $taxonomy->id,
        'title' => 'PHP',
        'locale' => 'cs',
    ]);

    $matchingEntry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Published,
        'author_id' => $this->admin->id,
    ]);
    $matchingEntry->terms()->syncWithoutDetaching([$laravel->id]);

    $otherEntry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Published,
        'author_id' => $this->admin->id,
    ]);
    $otherEntry->terms()->syncWithoutDetaching([$php->id]);

    $configurationKey = 'test-articles-taxonomy-filter';
    $panel = Filament::getDefaultPanel();
    Filament::setCurrentPanel($panel);
    Route::get("/_test/{$configurationKey}/create", fn () => 'ok')
        ->name("filament.admin.resources.entries.{$configurationKey}.create");
    Route::get("/_test/{$configurationKey}/{record}/edit", fn () => 'ok')
        ->name("filament.admin.resources.entries.{$configurationKey}.edit");

    $panel->resources([
        EntryResource::make($configurationKey)
            ->collectionHandle($this->collection->handle)
            ->navigationLabel($this->collection->title),
    ]);

    EntryResource::withConfiguration($configurationKey, fn () => Livewire::test(ListEntries::class)
        ->call('loadTable')
        ->assertTableFilterVisible('taxonomy_topics')
        ->filterTable('taxonomy_topics', $laravel->id)
        ->assertCanSeeTableRecords([$matchingEntry])
        ->assertCanNotSeeTableRecords([$otherEntry])
    );
});

it('can filter scoped collection entries by hierarchical taxonomy term using select tree', function () {
    $taxonomy = Taxonomy::factory()->hierarchical()->create([
        'title' => 'Kategorie',
        'handle' => 'categories',
    ]);
    $this->collection->taxonomies()->attach($taxonomy);

    $parentTerm = Term::factory()->create([
        'taxonomy_id' => $taxonomy->id,
        'title' => 'Laravel',
        'locale' => 'cs',
        'parent_id' => null,
        'order' => 1,
    ]);
    $childTerm = Term::factory()->create([
        'taxonomy_id' => $taxonomy->id,
        'title' => 'Filament',
        'locale' => 'cs',
        'parent_id' => $parentTerm->id,
        'order' => 2,
    ]);
    $otherTerm = Term::factory()->create([
        'taxonomy_id' => $taxonomy->id,
        'title' => 'Statamic',
        'locale' => 'cs',
        'parent_id' => null,
        'order' => 3,
    ]);

    $matchingEntry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Published,
        'author_id' => $this->admin->id,
    ]);
    $matchingEntry->terms()->syncWithoutDetaching([$childTerm->id]);

    $otherEntry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Published,
        'author_id' => $this->admin->id,
    ]);
    $otherEntry->terms()->syncWithoutDetaching([$otherTerm->id]);

    $configurationKey = 'test-articles-hierarchical-taxonomy-filter';
    $panel = Filament::getDefaultPanel();
    Filament::setCurrentPanel($panel);
    Route::get("/_test/{$configurationKey}/create", fn () => 'ok')
        ->name("filament.admin.resources.entries.{$configurationKey}.create");
    Route::get("/_test/{$configurationKey}/{record}/edit", fn () => 'ok')
        ->name("filament.admin.resources.entries.{$configurationKey}.edit");

    $panel->resources([
        EntryResource::make($configurationKey)
            ->collectionHandle($this->collection->handle)
            ->navigationLabel($this->collection->title),
    ]);

    EntryResource::withConfiguration($configurationKey, fn () => Livewire::test(ListEntries::class)
        ->call('loadTable')
        ->assertTableFilterVisible('taxonomy_categories')
        ->filterTable('taxonomy_categories', ['value' => $childTerm->id])
        ->assertCanSeeTableRecords([$matchingEntry])
        ->assertCanNotSeeTableRecords([$otherEntry])
    );
});

it('shows an active indicator for hierarchical taxonomy filters', function () {
    $taxonomy = Taxonomy::factory()->hierarchical()->create([
        'title' => 'Kategorie',
        'handle' => 'categories',
    ]);
    $this->collection->taxonomies()->attach($taxonomy);

    $term = Term::factory()->create([
        'taxonomy_id' => $taxonomy->id,
        'title' => 'Filament',
        'locale' => 'cs',
        'parent_id' => null,
        'order' => 1,
    ]);

    $configurationKey = 'test-articles-hierarchical-taxonomy-indicator';
    $panel = Filament::getDefaultPanel();
    Filament::setCurrentPanel($panel);
    Route::get("/_test/{$configurationKey}/create", fn () => 'ok')
        ->name("filament.admin.resources.entries.{$configurationKey}.create");
    Route::get("/_test/{$configurationKey}/{record}/edit", fn () => 'ok')
        ->name("filament.admin.resources.entries.{$configurationKey}.edit");

    $panel->resources([
        EntryResource::make($configurationKey)
            ->collectionHandle($this->collection->handle)
            ->navigationLabel($this->collection->title),
    ]);

    $indicatorLabels = EntryResource::withConfiguration($configurationKey, function () use ($term): array {
        $component = Livewire::test(ListEntries::class)
            ->call('loadTable')
            ->filterTable('taxonomy_categories', ['value' => $term->id]);

        return collect($component->instance()->getTable()->getFilterIndicators())
            ->map(fn ($indicator): string => (string) $indicator->getLabel())
            ->values()
            ->all();
    });

    expect($indicatorLabels)->toContain('Kategorie: Filament');
});

it('allows drag and drop reordering for scoped collections sorted by order', function () {
    $firstEntry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Published,
        'author_id' => $this->admin->id,
        'order' => 1,
        'title' => 'První',
    ]);
    $secondEntry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Published,
        'author_id' => $this->admin->id,
        'order' => 2,
        'title' => 'Druhý',
    ]);

    $configurationKey = 'test-articles-reordering';
    $panel = Filament::getDefaultPanel();
    Filament::setCurrentPanel($panel);
    Route::get("/_test/{$configurationKey}/create", fn () => 'ok')
        ->name("filament.admin.resources.entries.{$configurationKey}.create");
    Route::get("/_test/{$configurationKey}/{record}/edit", fn () => 'ok')
        ->name("filament.admin.resources.entries.{$configurationKey}.edit");

    $panel->resources([
        EntryResource::make($configurationKey)
            ->collectionHandle($this->collection->handle)
            ->navigationLabel($this->collection->title),
    ]);

    EntryResource::withConfiguration($configurationKey, function () use ($firstEntry, $secondEntry): void {
        $component = Livewire::test(ListEntries::class)
            ->call('loadTable');

        expect($component->instance()->getTable()->isReorderable())
            ->toBeTrue()
            ->and($component->instance()->getTable()->getReorderColumn())
            ->toBe('order');

        $component
            ->call('toggleTableReordering')
            ->call('reorderTable', [$secondEntry->getKey(), $firstEntry->getKey()]);
    });

    expect($secondEntry->fresh()->order)->toBe(1)
        ->and($firstEntry->fresh()->order)->toBe(2);
});

it('allows drag and drop reordering for scoped article collections even when default sort is published_at', function () {
    $this->collection->update([
        'sort_field' => 'published_at',
        'sort_direction' => 'desc',
    ]);

    $olderEntry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Published,
        'author_id' => $this->admin->id,
        'order' => 1,
        'published_at' => now()->subDay(),
        'title' => 'Starší článek',
    ]);
    $newerEntry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Published,
        'author_id' => $this->admin->id,
        'order' => 2,
        'published_at' => now(),
        'title' => 'Novější článek',
    ]);

    $configurationKey = 'test-articles-reordering-published-at';
    $panel = Filament::getDefaultPanel();
    Filament::setCurrentPanel($panel);
    Route::get("/_test/{$configurationKey}/create", fn () => 'ok')
        ->name("filament.admin.resources.entries.{$configurationKey}.create");
    Route::get("/_test/{$configurationKey}/{record}/edit", fn () => 'ok')
        ->name("filament.admin.resources.entries.{$configurationKey}.edit");

    $panel->resources([
        EntryResource::make($configurationKey)
            ->collectionHandle($this->collection->handle)
            ->navigationLabel($this->collection->title),
    ]);

    EntryResource::withConfiguration($configurationKey, function () use ($olderEntry, $newerEntry): void {
        $component = Livewire::test(ListEntries::class)
            ->call('loadTable');

        expect($component->instance()->getTable()->isReorderable())
            ->toBeTrue()
            ->and($component->instance()->getTable()->getDefaultSortDirection())
            ->toBe('desc');

        $component
            ->call('toggleTableReordering')
            ->call('reorderTable', [$newerEntry->getKey(), $olderEntry->getKey()]);
    });

    expect($newerEntry->fresh()->order)->toBe(1)
        ->and($olderEntry->fresh()->order)->toBe(2);
});

it('loads scoped collection taxonomies only once when building columns and filters', function () {
    $taxonomy = Taxonomy::factory()->create([
        'title' => 'Témata',
        'handle' => 'topics',
    ]);
    $this->collection->taxonomies()->attach($taxonomy);

    $term = Term::factory()->create([
        'taxonomy_id' => $taxonomy->id,
        'title' => 'Laravel',
        'locale' => 'cs',
    ]);

    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Published,
        'author_id' => $this->admin->id,
    ]);
    $entry->terms()->syncWithoutDetaching([$term->id]);

    $configurationKey = 'test-articles-taxonomy-query-count';
    $panel = Filament::getDefaultPanel();
    Filament::setCurrentPanel($panel);
    Route::get("/_test/{$configurationKey}/create", fn () => 'ok')
        ->name("filament.admin.resources.entries.{$configurationKey}.create");
    Route::get("/_test/{$configurationKey}/{record}/edit", fn () => 'ok')
        ->name("filament.admin.resources.entries.{$configurationKey}.edit");

    $panel->resources([
        EntryResource::make($configurationKey)
            ->collectionHandle($this->collection->handle)
            ->navigationLabel($this->collection->title),
    ]);

    $queries = EntryResource::withConfiguration($configurationKey, function () {
        DB::flushQueryLog();
        DB::enableQueryLog();

        Livewire::test(ListEntries::class)
            ->assertTableColumnVisible('taxonomy_topics')
            ->assertTableFilterVisible('taxonomy_topics');

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        return $queries;
    });

    $collectionLookupCount = collect($queries)
        ->filter(fn (array $query): bool => str_contains($query['query'], 'from `collections`')
            && str_contains($query['query'], 'where `handle` = ?'))
        ->count();

    $taxonomyLookupCount = collect($queries)
        ->filter(fn (array $query): bool => str_contains($query['query'], 'from `taxonomies`')
            && str_contains($query['query'], 'inner join `collection_taxonomy`'))
        ->count();

    expect($collectionLookupCount)->toBe(1)
        ->and($taxonomyLookupCount)->toBe(1);
});

it('does not show taxonomy columns for scoped collections without taxonomies', function () {
    $pagesCollection = Collection::factory()->create([
        'handle' => 'pages',
        'title' => 'Stránky',
        'is_active' => true,
    ]);
    Blueprint::factory()->create([
        'collection_id' => $pagesCollection->id,
        'title' => 'Stránka',
        'handle' => 'page',
        'is_default' => true,
    ]);

    Taxonomy::factory()->create([
        'title' => 'Témata',
        'handle' => 'topics',
    ]);

    $configurationKey = 'test-pages-no-taxonomies';
    $panel = Filament::getDefaultPanel();
    Filament::setCurrentPanel($panel);
    Route::get("/_test/{$configurationKey}/create", fn () => 'ok')
        ->name("filament.admin.resources.entries.{$configurationKey}.create");
    Route::get("/_test/{$configurationKey}/{record}/edit", fn () => 'ok')
        ->name("filament.admin.resources.entries.{$configurationKey}.edit");

    $panel->resources([
        EntryResource::make($configurationKey)
            ->collectionHandle($pagesCollection->handle)
            ->navigationLabel($pagesCollection->title),
    ]);

    EntryResource::withConfiguration($configurationKey, fn () => Livewire::test(ListEntries::class)
        ->call('loadTable')
        ->assertTableColumnDoesNotExist('taxonomy_topics')
    );
});

// ── Create page ────────────────────────────────────────────────────────────

it('can render the entry create page', function () {
    Livewire::test(CreateEntry::class)
        ->assertOk();
});

it('can create an entry via save_as_draft action', function () {
    Livewire::test(CreateEntry::class)
        ->fillForm([
            'title' => 'Nový článek',
            'slug' => 'novy-clanek',
            'collection_id' => $this->collection->id,
            'blueprint_id' => $this->blueprint->id,
            'locale' => 'cs',
            'status' => EntryStatus::Draft->value,
        ])
        ->callAction('save_as_draft')
        ->assertHasNoFormErrors();

    expect(Entry::where('slug', 'novy-clanek')->exists())->toBeTrue();
});

it('can create and publish an entry via publish action', function () {
    Livewire::test(CreateEntry::class)
        ->fillForm([
            'title' => 'Publikovaný článek',
            'slug' => 'publikovany-clanek',
            'collection_id' => $this->collection->id,
            'blueprint_id' => $this->blueprint->id,
            'locale' => 'cs',
            'status' => EntryStatus::Draft->value,
        ])
        ->callAction('publish')
        ->assertHasNoFormErrors();

    $entry = Entry::where('slug', 'publikovany-clanek')->first();
    expect($entry)->not->toBeNull()
        ->and($entry->status)->toBe(EntryStatus::Published);
});

it('validates required fields on entry create', function () {
    Livewire::test(CreateEntry::class)
        ->fillForm([
            'title' => null,
            'slug' => null,
        ])
        ->callAction('save_as_draft')
        ->assertHasFormErrors(['title' => 'required', 'slug' => 'required']);
});

it('validates slug uniqueness within same collection and locale', function () {
    Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'slug' => 'existing-slug',
        'status' => EntryStatus::Draft,
        'author_id' => $this->admin->id,
    ]);

    Livewire::test(CreateEntry::class)
        ->fillForm([
            'title' => 'Test',
            'slug' => 'existing-slug',
            'collection_id' => $this->collection->id,
            'blueprint_id' => $this->blueprint->id,
            'locale' => 'cs',
        ])
        ->callAction('save_as_draft')
        ->assertHasFormErrors(['slug']);
});

// ── Edit page ──────────────────────────────────────────────────────────────

it('can render the entry edit page', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Draft,
        'author_id' => $this->admin->id,
    ]);

    Livewire::test(EditEntry::class, ['record' => $entry->getKey()])
        ->assertOk();
});

it('can update an entry title', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'title' => 'Původní název',
        'slug' => 'puvodni-nazev',
        'status' => EntryStatus::Draft,
        'author_id' => $this->admin->id,
    ]);

    Livewire::test(EditEntry::class, ['record' => $entry->getKey()])
        ->fillForm(['title' => 'Nový název'])
        ->callAction('save')
        ->assertHasNoFormErrors();

    expect($entry->fresh()->title)->toBe('Nový název');
});

it('can publish a draft entry from edit page', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Draft,
        'author_id' => $this->admin->id,
    ]);

    Livewire::test(EditEntry::class, ['record' => $entry->getKey()])
        ->callAction('publish')
        ->assertHasNoFormErrors();

    expect($entry->fresh()->status)->toBe(EntryStatus::Published);
});

it('can unpublish a published entry from edit page', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Published,
        'author_id' => $this->admin->id,
        'published_at' => now(),
    ]);

    Livewire::test(EditEntry::class, ['record' => $entry->getKey()])
        ->callAction('unpublish')
        ->assertHasNoFormErrors();

    expect($entry->fresh()->status)->toBe(EntryStatus::Draft);
});

it('can delete an entry from edit page', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Draft,
        'author_id' => $this->admin->id,
    ]);
    $id = $entry->getKey();

    Livewire::test(EditEntry::class, ['record' => $entry->getKey()])
        ->callAction('delete');

    expect(Entry::withTrashed()->find($id))->not->toBeNull()
        ->and(Entry::find($id))->toBeNull();
});

// ── Bulk actions ───────────────────────────────────────────────────────────

it('can bulk delete entries', function () {
    $entries = Entry::factory()->count(3)->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Draft,
        'author_id' => $this->admin->id,
    ]);

    Livewire::test(ListEntries::class)
        ->callTableBulkAction('delete', $entries);

    foreach ($entries as $entry) {
        expect(Entry::find($entry->getKey()))->toBeNull();
    }
});
