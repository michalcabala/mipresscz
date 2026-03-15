<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Livewire\Livewire;
use MiPressCz\Core\Concerns\HasNavMenuItems;
use MiPressCz\Core\Filament\Pages\MenuManagerPage;
use MiPressCz\Core\Livewire\NavMenuPanel;
use MiPressCz\Core\Models\Collection;
use MiPressCz\Core\Models\Entry;
use MiPressCz\Core\Models\NavMenu;
use MiPressCz\Core\Models\NavMenuItem;
use MiPressCz\Core\Models\NavMenuLocation;
use MiPressCz\Core\Services\NavMenuService;

describe('Entry HasNavMenuItems', function () {
    it('implements HasNavMenuItems trait', function () {
        $uses = class_uses_recursive(Entry::class);
        expect($uses)->toContain(HasNavMenuItems::class);
    });

    it('returns menu label from title', function () {
        $entry = new Entry(['title' => 'Moje stránka']);
        expect($entry->getMenuLabel())->toBe('Moje stránka');
    });

    it('returns menu icon', function () {
        $entry = new Entry;
        expect($entry->getMenuIcon())->toBe('heroicon-o-document-text');
    });
});

describe('Menu manager DB tables', function () {
    it('can create a menu location', function () {
        $location = NavMenuLocation::create([
            'handle' => 'primary',
            'name' => 'Primary navigation',
        ]);

        expect($location->handle)->toBe('primary');
        expect(NavMenuLocation::count())->toBeGreaterThanOrEqual(1);
    });

    it('can create a menu for a location', function () {
        $location = NavMenuLocation::firstOrCreate(
            ['handle' => 'primary'],
            ['name' => 'Primary navigation'],
        );

        $menu = NavMenu::create([
            'menu_location_id' => $location->id,
            'name' => 'Hlavní menu',
            'is_active' => true,
        ]);

        expect($menu->name)->toBe('Hlavní menu');
        expect($menu->menu_location_id)->toBe($location->id);
    });

    it('can create a menu item', function () {
        $location = NavMenuLocation::firstOrCreate(
            ['handle' => 'primary'],
            ['name' => 'Primary navigation'],
        );

        $menu = NavMenu::create([
            'menu_location_id' => $location->id,
            'name' => 'Hlavní menu',
        ]);

        $item = NavMenuItem::create([
            'menu_id' => $menu->id,
            'title' => 'Úvod',
            'url' => '/',
            'type' => 'custom',
            'target' => '_self',
            'enabled' => true,
            'order' => 0,
        ]);

        expect($item->title)->toBe('Úvod');
        expect($item->menu_id)->toBe($menu->id);
    });

    it('deletes menu items when menu is deleted (cascade)', function () {
        $location = NavMenuLocation::firstOrCreate(
            ['handle' => 'footer'],
            ['name' => 'Footer'],
        );

        $menu = NavMenu::create([
            'menu_location_id' => $location->id,
            'name' => 'Footer menu',
        ]);

        NavMenuItem::create([
            'menu_id' => $menu->id,
            'title' => 'Kontakt',
            'url' => '/kontakt',
            'type' => 'custom',
            'order' => 0,
        ]);

        $menuId = $menu->id;
        $menu->delete();

        expect(NavMenuItem::where('menu_id', $menuId)->count())->toBe(0);
    });

    it('derives archive sources from collection route templates', function () {
        Collection::factory()->create([
            'title' => 'Blog',
            'handle' => 'blog',
            'route_template' => '/blog/{slug}',
            'is_active' => true,
        ]);

        Collection::factory()->create([
            'title' => 'Pages',
            'handle' => 'pages',
            'route_template' => '/{slug}',
            'is_active' => true,
        ]);

        Collection::factory()->create([
            'title' => 'Hidden',
            'handle' => 'hidden',
            'route_template' => '/hidden/{slug}',
            'is_active' => false,
        ]);

        $archives = app(NavMenuService::class)->getArchiveSources();

        expect($archives)->toHaveCount(1)
            ->and($archives[0]['handle'])->toBe('blog')
            ->and($archives[0]['title'])->toBe('Blog')
            ->and($archives[0]['url'])->toBe(url('/blog'));
    });

    it('adds a custom link directly from the panel', function () {
        $location = NavMenuLocation::firstOrCreate(
            ['handle' => 'primary'],
            ['name' => 'Primary navigation'],
        );

        $menu = NavMenu::create([
            'menu_location_id' => $location->id,
            'name' => 'Main menu',
        ]);

        Livewire::test(NavMenuPanel::class, ['menuId' => $menu->id])
            ->set('customTitle', 'Blog')
            ->set('customUrl', '/blog')
            ->call('addCustomLink');

        $item = NavMenuItem::query()->where('menu_id', $menu->id)->first();

        expect($item)->not->toBeNull()
            ->and($item?->title)->toBe('Blog')
            ->and($item?->url)->toBe('/blog')
            ->and($item?->type)->toBe('custom');
    });

    it('adds an archive item directly from the panel', function () {
        $location = NavMenuLocation::firstOrCreate(
            ['handle' => 'primary'],
            ['name' => 'Primary navigation'],
        );

        $menu = NavMenu::create([
            'menu_location_id' => $location->id,
            'name' => 'Main menu',
        ]);

        Collection::factory()->create([
            'title' => 'Blog',
            'handle' => 'blog',
            'route_template' => '/blog/{slug}',
            'is_active' => true,
        ]);

        Livewire::test(NavMenuPanel::class, ['menuId' => $menu->id])
            ->call('addArchiveItem', 'blog');

        $item = NavMenuItem::query()->where('menu_id', $menu->id)->first();

        expect($item)->not->toBeNull()
            ->and($item?->type)->toBe('archive')
            ->and(data_get($item?->data, 'archive_handle'))->toBe('blog')
            ->and($item?->url)->toBe(url('/blog'));
    });

    it('adds a model entry directly from the panel', function () {
        $location = NavMenuLocation::firstOrCreate(
            ['handle' => 'primary'],
            ['name' => 'Primary navigation'],
        );

        $menu = NavMenu::create([
            'menu_location_id' => $location->id,
            'name' => 'Main menu',
        ]);

        $entry = Entry::factory()->create([
            'title' => 'About us',
            'uri' => '/about-us',
        ]);

        Livewire::test(NavMenuPanel::class, ['menuId' => $menu->id])
            ->call('addModelItem', Entry::class, $entry->id);

        $item = NavMenuItem::query()->where('menu_id', $menu->id)->first();

        expect($item)->not->toBeNull()
            ->and($item?->type)->toBe('model')
            ->and(data_get($item?->data, 'linkable_type'))->toBe(Entry::class)
            ->and(data_get($item?->data, 'linkable_id'))->toBe($entry->id)
            ->and($item?->url)->toBe($entry->getMenuUrl());
    });
});

describe('MenuManagerPage access', function () {
    beforeEach(function () {
        $this->seed(RolesAndPermissionsSeeder::class);
    });

    it('is accessible to authenticated admin user', function () {
        $admin = User::factory()->create(['role' => App\Enums\UserRole::Admin]);
        $admin->syncRoles([App\Enums\UserRole::Admin->value]);

        $this->actingAs($admin);

        expect(MenuManagerPage::canAccess())->toBeTrue();
    });
});

describe('Frontend menu tree', function () {
    it('returns nested tree via NavMenuService::getMenuTree()', function () {
        $location = NavMenuLocation::firstOrCreate(
            ['handle' => 'primary'],
            ['name' => 'Primary navigation'],
        );

        $menu = NavMenu::create([
            'menu_location_id' => $location->id,
            'name' => 'Main',
            'is_active' => true,
        ]);

        $parent = NavMenuItem::create([
            'menu_id' => $menu->id,
            'title' => 'O nás',
            'url' => '/o-nas',
            'type' => 'custom',
            'target' => '_self',
            'enabled' => true,
            'order' => 0,
        ]);

        NavMenuItem::create([
            'menu_id' => $menu->id,
            'title' => 'Tým',
            'url' => '/o-nas/tym',
            'type' => 'custom',
            'target' => '_self',
            'enabled' => true,
            'order' => 0,
            'parent_id' => $parent->id,
        ]);

        NavMenuItem::create([
            'menu_id' => $menu->id,
            'title' => 'Skrytá',
            'url' => '/skryta',
            'type' => 'custom',
            'target' => '_self',
            'enabled' => false,
            'order' => 1,
        ]);

        $tree = app(NavMenuService::class)->getMenuTree('primary');

        expect($tree)->toHaveCount(1)
            ->and($tree[0]['title'])->toBe('O nás')
            ->and($tree[0]['url'])->toBe('/o-nas')
            ->and($tree[0]['children'])->toHaveCount(1)
            ->and($tree[0]['children'][0]['title'])->toBe('Tým');
    });

    it('returns empty array for unknown location', function () {
        $tree = app(NavMenuService::class)->getMenuTree('nonexistent');

        expect($tree)->toBeArray()->toBeEmpty();
    });

    it('returns empty array when no active menu exists for location', function () {
        $location = NavMenuLocation::firstOrCreate(
            ['handle' => 'sidebar'],
            ['name' => 'Sidebar'],
        );

        NavMenu::create([
            'menu_location_id' => $location->id,
            'name' => 'Inactive menu',
            'is_active' => false,
        ]);

        $tree = app(NavMenuService::class)->getMenuTree('sidebar');

        expect($tree)->toBeArray()->toBeEmpty();
    });

    it('resolves menu tree via the menu() helper', function () {
        $location = NavMenuLocation::firstOrCreate(
            ['handle' => 'footer'],
            ['name' => 'Footer'],
        );

        $menu = NavMenu::create([
            'menu_location_id' => $location->id,
            'name' => 'Footer menu',
            'is_active' => true,
        ]);

        NavMenuItem::create([
            'menu_id' => $menu->id,
            'title' => 'Kontakt',
            'url' => '/kontakt',
            'type' => 'custom',
            'target' => '_self',
            'enabled' => true,
            'order' => 0,
        ]);

        $tree = menu('footer');

        expect($tree)->toHaveCount(1)
            ->and($tree[0]['title'])->toBe('Kontakt')
            ->and($tree[0]['url'])->toBe('/kontakt');
    });
});
