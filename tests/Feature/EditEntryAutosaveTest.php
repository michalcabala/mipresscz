<?php

use App\Enums\UserRole;
use App\Models\User;
use Livewire\Livewire;
use MiPressCz\Core\Enums\EntryStatus;
use MiPressCz\Core\Enums\RevisionType;
use MiPressCz\Core\Filament\Resources\Entries\Pages\EditEntry;
use MiPressCz\Core\Models\Blueprint;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\Locale;
use MiPressCz\Core\Models\Revision;

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

it('creates autosave revisions only when the form state changes', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Draft,
        'author_id' => $this->admin->id,
        'title' => 'Původní název',
    ]);

    $component = Livewire::test(EditEntry::class, ['record' => $entry->getKey()]);

    $component
        ->call('autosave')
        ->assertSet('autosaveStatus', 'saved');

    expect($entry->fresh()->revisions()->count())->toBe(1);

    $component
        ->fillForm(['title' => 'Autosave změna'])
        ->call('autosave')
        ->assertSet('autosaveStatus', 'saved');

    expect($entry->fresh()->revisions()->count())->toBe(2)
        ->and($entry->fresh()->latestRevision->type)->toBe(RevisionType::Autosave);

    $component
        ->call('autosave')
        ->assertSet('autosaveStatus', 'saved');

    expect($entry->fresh()->revisions()->count())->toBe(2);
});

it('prunes old autosave revisions above the configured limit', function () {
    config()->set('mipress-revisions.autosave_max', 2);

    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'locale' => 'cs',
        'status' => EntryStatus::Draft,
        'author_id' => $this->admin->id,
        'title' => 'Autosave limit',
    ]);

    $component = Livewire::test(EditEntry::class, ['record' => $entry->getKey()]);

    $component->fillForm(['title' => 'Autosave 1'])->call('autosave');
    $component->fillForm(['title' => 'Autosave 2'])->call('autosave');
    $component->fillForm(['title' => 'Autosave 3'])->call('autosave');

    expect($entry->fresh()->revisions()->count())->toBe(3)
        ->and($entry->fresh()->revisions()->where('type', RevisionType::Autosave)->count())->toBe(2)
        ->and(Revision::withTrashed()->where('type', RevisionType::Autosave)->whereNotNull('deleted_at')->count())->toBe(1);
});
