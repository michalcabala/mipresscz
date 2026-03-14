<?php

use App\Enums\UserRole;
use App\Models\User;
use MiPressCz\Core\Filament\Resources\MediaTags\Pages\CreateMediaTag;
use MiPressCz\Core\Filament\Resources\MediaTags\Pages\EditMediaTag;
use MiPressCz\Core\Filament\Resources\MediaTags\Pages\ListMediaTags;
use MiPressCz\Core\Models\Media;
use MiPressCz\Core\Models\MediaFolder;
use MiPressCz\Core\Models\MediaTag;

beforeEach(function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    (new \Database\Seeders\RolesAndPermissionsSeeder)->run();

    $this->admin = User::factory()->create(['role' => UserRole::Admin]);
    $this->admin->syncRoles([UserRole::Admin->value]);
    $this->actingAs($this->admin);
});

function createTestMediaRecord(array $attributes = []): Media
{
    return Media::create(array_merge([
        'disk' => 'public',
        'directory' => 'tests',
        'visibility' => 'public',
        'name' => 'test-image',
        'path' => 'tests/test-image-'.uniqid().'.jpg',
        'width' => 800,
        'height' => 600,
        'size' => 12345,
        'type' => 'image/jpeg',
        'ext' => 'jpg',
    ], $attributes));
}

// ── MediaFolder model ─────────────────────────────────────────────────────

describe('MediaFolder model', function () {
    it('can be created via factory', function () {
        $folder = MediaFolder::factory()->create(['name' => 'Photos', 'slug' => 'photos']);

        expect($folder)->toBeInstanceOf(MediaFolder::class)
            ->and($folder->name)->toBe('Photos')
            ->and($folder->slug)->toBe('photos');
    });

    it('supports parent-child tree structure', function () {
        $parent = MediaFolder::factory()->create(['name' => 'Root']);
        $child = MediaFolder::factory()->create([
            'name' => 'Child',
            'parent_id' => $parent->id,
        ]);

        expect($child->parent->id)->toBe($parent->id)
            ->and($parent->children)->toHaveCount(1)
            ->and($parent->children->first()->id)->toBe($child->id);
    });

    it('cascades delete to child folders', function () {
        $parent = MediaFolder::factory()->create();
        $child = MediaFolder::factory()->create(['parent_id' => $parent->id]);
        $childId = $child->id;

        $parent->delete();

        expect(MediaFolder::find($childId))->toBeNull();
    });

    it('has media relationship', function () {
        $folder = MediaFolder::factory()->create();
        $media = createTestMediaRecord(['media_folder_id' => $folder->id]);

        expect($folder->media)->toHaveCount(1)
            ->and($folder->media->first()->id)->toBe($media->id);
    });
});

// ── MediaTag model ────────────────────────────────────────────────────────

describe('MediaTag model', function () {
    it('can be created via factory', function () {
        $tag = MediaTag::factory()->create(['name' => 'Nature']);

        expect($tag)->toBeInstanceOf(MediaTag::class)
            ->and($tag->name)->toBe('Nature');
    });

    it('has unique slug', function () {
        MediaTag::factory()->create(['slug' => 'unique-tag']);

        expect(fn () => MediaTag::factory()->create(['slug' => 'unique-tag']))
            ->toThrow(\Illuminate\Database\QueryException::class);
    });

    it('has many-to-many media relationship', function () {
        $tag = MediaTag::factory()->create();
        $media = createTestMediaRecord();

        $tag->media()->attach($media->id);

        expect($tag->media)->toHaveCount(1)
            ->and($media->tags)->toHaveCount(1);
    });
});

// ── Custom Media model ────────────────────────────────────────────────────

describe('Custom Media model', function () {
    it('uses custom MiPressCz Media model', function () {
        expect(config('curator.model'))->toBe(Media::class);
    });

    it('extends Curator Media', function () {
        expect(is_subclass_of(Media::class, \Awcodes\Curator\Models\Media::class))->toBeTrue();
    });

    it('has folder relationship', function () {
        $folder = MediaFolder::factory()->create();
        $media = createTestMediaRecord(['media_folder_id' => $folder->id]);

        expect($media->folder)->toBeInstanceOf(MediaFolder::class)
            ->and($media->folder->id)->toBe($folder->id);
    });

    it('has tags relationship', function () {
        $media = createTestMediaRecord();
        $tag1 = MediaTag::factory()->create(['name' => 'Tag 1']);
        $tag2 = MediaTag::factory()->create(['name' => 'Tag 2']);

        $media->tags()->attach([$tag1->id, $tag2->id]);
        $media->refresh();

        expect($media->tags)->toHaveCount(2);
    });

    it('cascades delete on media to pivot', function () {
        $media = createTestMediaRecord();
        $tag = MediaTag::factory()->create();
        $media->tags()->attach($tag->id);

        expect(\Illuminate\Support\Facades\DB::table('media_media_tag')->count())->toBe(1);

        $media->delete();

        expect(\Illuminate\Support\Facades\DB::table('media_media_tag')->count())->toBe(0);
    });

    it('cascades delete on tag to pivot', function () {
        $media = createTestMediaRecord();
        $tag = MediaTag::factory()->create();
        $media->tags()->attach($tag->id);

        $tag->delete();

        expect(\Illuminate\Support\Facades\DB::table('media_media_tag')->count())->toBe(0);
    });
});

// ── MediaTag Filament Resource ────────────────────────────────────────────

describe('MediaTag Filament Resource', function () {
    it('can render list page', function () {
        \Livewire\Livewire::test(ListMediaTags::class)
            ->assertSuccessful();
    });

    it('can render create page', function () {
        \Livewire\Livewire::test(CreateMediaTag::class)
            ->assertSuccessful();
    });

    it('can create a tag', function () {
        \Livewire\Livewire::test(CreateMediaTag::class)
            ->fillForm(['name' => 'Landscapes'])
            ->call('create')
            ->assertHasNoFormErrors();

        expect(MediaTag::where('name', 'Landscapes')->exists())->toBeTrue();
        expect(MediaTag::where('slug', 'landscapes')->exists())->toBeTrue();
    });

    it('can render edit page', function () {
        $tag = MediaTag::factory()->create();

        \Livewire\Livewire::test(EditMediaTag::class, ['record' => $tag->id])
            ->assertSuccessful();
    });

    it('can update a tag', function () {
        $tag = MediaTag::factory()->create(['name' => 'Old Name', 'slug' => 'old-name']);

        \Livewire\Livewire::test(EditMediaTag::class, ['record' => $tag->id])
            ->fillForm(['name' => 'New Name'])
            ->call('save')
            ->assertHasNoFormErrors();

        $tag->refresh();
        expect($tag->name)->toBe('New Name')
            ->and($tag->slug)->toBe('new-name');
    });

    it('can delete a tag', function () {
        $tag = MediaTag::factory()->create();

        \Livewire\Livewire::test(EditMediaTag::class, ['record' => $tag->id])
            ->callAction(\Filament\Actions\DeleteAction::class);

        expect(MediaTag::find($tag->id))->toBeNull();
    });

    it('shows tags in table', function () {
        $tag = MediaTag::factory()->create();
        $media = createTestMediaRecord();
        $tag->media()->attach($media->id);

        \Livewire\Livewire::test(ListMediaTags::class)
            ->loadTable()
            ->assertSuccessful()
            ->assertSee($tag->name);
    });
});

// ── MediaFolder Filament Resource ─────────────────────────────────────────

describe('MediaFolder Filament Resource', function () {
    it('can create a folder', function () {
        $folder = MediaFolder::factory()->create(['name' => 'TestFolder']);

        expect(MediaFolder::where('name', 'TestFolder')->exists())->toBeTrue();
    });

    it('can update a folder', function () {
        $folder = MediaFolder::factory()->create(['name' => 'Old']);
        $folder->update(['name' => 'New', 'slug' => 'new']);

        expect($folder->fresh()->name)->toBe('New');
    });

    it('can delete a folder', function () {
        $folder = MediaFolder::factory()->create();
        $folderId = $folder->id;
        $folder->delete();

        expect(MediaFolder::find($folderId))->toBeNull();
    });

    it('nullifies folder reference on media when folder deleted', function () {
        $folder = MediaFolder::factory()->create();
        $media = createTestMediaRecord(['media_folder_id' => $folder->id]);

        $folder->delete();
        $media->refresh();

        expect($media->media_folder_id)->toBeNull();
    });
});
