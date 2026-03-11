<?php

use App\Enums\UserRole;
use App\Models\User;
use Livewire\Livewire;
use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Filament\Resources\Entries\Pages\CreateEntry;
use MiPressCz\Core\Filament\Resources\Entries\Pages\EditEntry;
use MiPressCz\Core\Filament\Resources\Entries\Pages\ListEntries;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\Locale;

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
