<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use MiPressCz\Core\Models\Entry;
use NoteBrainsLab\FilamentMenuManager\Concerns\HasMenuItems;
use NoteBrainsLab\FilamentMenuManager\Models\Menu;
use NoteBrainsLab\FilamentMenuManager\Models\MenuItem;
use NoteBrainsLab\FilamentMenuManager\Models\MenuLocation;
use NoteBrainsLab\FilamentMenuManager\Pages\MenuManagerPage;

describe('Entry HasMenuItems', function () {
    it('implements HasMenuItems trait', function () {
        $uses = class_uses_recursive(Entry::class);
        expect($uses)->toContain(HasMenuItems::class);
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
        $location = MenuLocation::create([
            'handle' => 'primary',
            'name' => 'Primary navigation',
        ]);

        expect($location->handle)->toBe('primary');
        expect(MenuLocation::count())->toBeGreaterThanOrEqual(1);
    });

    it('can create a menu for a location', function () {
        $location = MenuLocation::firstOrCreate(
            ['handle' => 'primary'],
            ['name' => 'Primary navigation'],
        );

        $menu = Menu::create([
            'menu_location_id' => $location->id,
            'name' => 'Hlavní menu',
            'is_active' => true,
        ]);

        expect($menu->name)->toBe('Hlavní menu');
        expect($menu->menu_location_id)->toBe($location->id);
    });

    it('can create a menu item', function () {
        $location = MenuLocation::firstOrCreate(
            ['handle' => 'primary'],
            ['name' => 'Primary navigation'],
        );

        $menu = Menu::create([
            'menu_location_id' => $location->id,
            'name' => 'Hlavní menu',
        ]);

        $item = MenuItem::create([
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
        $location = MenuLocation::firstOrCreate(
            ['handle' => 'footer'],
            ['name' => 'Footer'],
        );

        $menu = Menu::create([
            'menu_location_id' => $location->id,
            'name' => 'Footer menu',
        ]);

        MenuItem::create([
            'menu_id' => $menu->id,
            'title' => 'Kontakt',
            'url' => '/kontakt',
            'type' => 'custom',
            'order' => 0,
        ]);

        $menuId = $menu->id;
        $menu->delete();

        expect(MenuItem::where('menu_id', $menuId)->count())->toBe(0);
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
