<?php

use App\Enums\UserRole;
use App\Models\User;
use Awcodes\Curator\Models\Media;
use Livewire\Livewire;
use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Filament\Resources\Entries\Pages\EditEntry;
use MiPressCz\Core\Filament\Resources\Entries\Pages\ListEntries;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\Locale;

beforeEach(function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    (new \Database\Seeders\RolesAndPermissionsSeeder)->run();

    Locale::factory()->create([
        'code' => 'cs',
        'is_default' => true,
        'is_active' => true,
        'order' => 1,
    ]);
    locales()->clearCache();

    $this->collection = Collection::factory()->create([
        'handle' => 'articles',
        'is_active' => true,
    ]);
    $this->blueprint = Blueprint::factory()->create([
        'collection_id' => $this->collection->id,
        'is_default' => true,
    ]);

    $this->admin = User::factory()->create(['role' => UserRole::Admin]);
    $this->admin->syncRoles([UserRole::Admin->value]);
    $this->actingAs($this->admin);
});

/**
 * Creates a minimal Curator Media record for test purposes.
 */
function createTestMedia(array $attributes = []): Media
{
    return Media::create(array_merge([
        'disk' => 'public',
        'directory' => 'tests',
        'visibility' => 'public',
        'name' => 'test-image',
        'path' => 'tests/test-image.jpg',
        'width' => 800,
        'height' => 600,
        'size' => 12345,
        'type' => 'image/jpeg',
        'ext' => 'jpg',
        'alt' => 'Test image',
    ], $attributes));
}

// ── Entry → featuredImage relationship ────────────────────────────────────

it('Entry has a featuredImage BelongsTo relationship', function () {
    $media = createTestMedia();

    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'author_id' => $this->admin->id,
        'featured_image_id' => $media->id,
    ]);

    expect($entry->featuredImage)->toBeInstanceOf(Media::class)
        ->and($entry->featuredImage->id)->toBe($media->id);
});

it('featuredImage is null when featured_image_id is not set', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'author_id' => $this->admin->id,
        'featured_image_id' => null,
    ]);

    expect($entry->featuredImage)->toBeNull();
});

it('featured_image_id can be updated on an existing entry', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'author_id' => $this->admin->id,
        'featured_image_id' => null,
    ]);

    $media = createTestMedia();
    $entry->update(['featured_image_id' => $media->id]);

    expect($entry->fresh()->featured_image_id)->toBe($media->id);
});

// ── Curator form integration ───────────────────────────────────────────────

it('edit form renders correctly for entry with featured image', function () {
    $media = createTestMedia();

    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'author_id' => $this->admin->id,
        'featured_image_id' => $media->id,
    ]);

    Livewire::test(EditEntry::class, ['record' => $entry->getKey()])
        ->assertOk()
        ->assertFormFieldExists('featured_image_id');
});

it('can clear featured image and save via model update', function () {
    $media = createTestMedia();

    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'author_id' => $this->admin->id,
        'featured_image_id' => $media->id,
    ]);

    $entry->update(['featured_image_id' => null]);

    expect($entry->fresh()->featured_image_id)->toBeNull()
        ->and($entry->fresh()->featuredImage)->toBeNull();
});

// ── Table column presence ─────────────────────────────────────────────────

it('entries with featured image are visible in the table', function () {
    $media = createTestMedia();

    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'author_id' => $this->admin->id,
        'featured_image_id' => $media->id,
        'status' => EntryStatus::Published,
    ]);

    Livewire::test(ListEntries::class)
        ->call('loadTable')
        ->assertCanSeeTableRecords([$entry]);
});
