<?php

use App\Enums\UserRole;
use App\Models\User;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\Locale;
use MiPressCz\Core\Support\RevisionDiffer;

beforeEach(function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    (new \Database\Seeders\RolesAndPermissionsSeeder)->run();

    Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'is_active' => true, 'order' => 1]);
    locales()->clearCache();

    $this->collection = Collection::factory()->create([
        'handle' => 'posts',
        'is_active' => true,
        'revisions_enabled' => true,
    ]);
    $this->blueprint = Blueprint::factory()->create([
        'collection_id' => $this->collection->id,
        'is_default' => true,
    ]);

    $this->author = User::factory()->create(['role' => UserRole::Admin]);
    $this->author->syncRoles([UserRole::Admin->value]);
});

it('returns empty diff when old revision is null', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'author_id' => $this->author->id,
        'title' => 'Initial',
    ]);

    $revision = $entry->revisions()->latest('created_at')->first();

    expect(RevisionDiffer::compare(null, $revision))->toBe([]);
});

it('detects title change between two revisions', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'author_id' => $this->author->id,
        'title' => 'Old title',
    ]);

    $firstRevision = $entry->revisions()->where('is_current', true)->firstOrFail();

    $entry->update(['title' => 'New title']);

    $secondRevision = $entry->fresh()->revisions()->where('is_current', true)->firstOrFail();

    $diff = RevisionDiffer::compare($firstRevision, $secondRevision);

    expect($diff)->toHaveKey('title');
    expect($diff['title']['old'])->toBe('Old title');
    expect($diff['title']['new'])->toBe('New title');
    expect($diff['title'])->toHaveKey('diff_html');
});

it('returns empty diff when the same revision is compared to itself', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'author_id' => $this->author->id,
        'title' => 'Same',
    ]);

    $revision = $entry->revisions()->latest('created_at')->first();

    $diff = RevisionDiffer::compare($revision, $revision);

    expect($diff)->not->toHaveKey('title');
    expect($diff)->not->toHaveKey('status');
});
