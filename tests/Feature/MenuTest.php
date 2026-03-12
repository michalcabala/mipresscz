<?php

use App\Enums\UserRole;
use App\Models\User;
use MiPressCz\Core\Enums\MenuItemTarget;
use MiPressCz\Core\Enums\MenuItemType;
use MiPressCz\Core\Models\Menu;
use MiPressCz\Core\Models\MenuItem;

// -- Menu model --

it('menu can be created via factory', function () {
    $menu = Menu::factory()->create();

    expect($menu->id)->not->toBeEmpty()
        ->and($menu->title)->not->toBeEmpty()
        ->and($menu->handle)->not->toBeEmpty();
});

it('menu primary factory state sets location to primary', function () {
    $menu = Menu::factory()->primary()->create();

    expect($menu->location)->toBe('primary')
        ->and($menu->handle)->toBe('primary');
});

it('menu footer factory state sets location to footer', function () {
    $menu = Menu::factory()->footer()->create();

    expect($menu->location)->toBe('footer');
});

it('menu has many items', function () {
    $menu = Menu::factory()->create();
    MenuItem::factory()->count(3)->create(['menu_id' => $menu->id]);

    expect($menu->items()->count())->toBe(3);
});

// -- MenuItem model --

it('menu item can be created via factory', function () {
    $item = MenuItem::factory()->create();

    expect($item->id)->not->toBeEmpty()
        ->and($item->title)->not->toBeEmpty()
        ->and($item->type)->toBe(MenuItemType::CustomLink)
        ->and($item->target)->toBe(MenuItemTarget::Self)
        ->and($item->is_active)->toBeTrue();
});

it('menu item casts type to MenuItemType enum', function () {
    $item = MenuItem::factory()->create(['type' => 'entry']);

    expect($item->type)->toBe(MenuItemType::Entry);
});

it('menu item casts target to MenuItemTarget enum', function () {
    $item = MenuItem::factory()->create(['target' => '_blank']);

    expect($item->target)->toBe(MenuItemTarget::Blank);
});

it('menu item belongs to menu', function () {
    $menu = Menu::factory()->create();
    $item = MenuItem::factory()->create(['menu_id' => $menu->id]);

    expect($item->menu->id)->toBe($menu->id);
});

it('menu item supports nesting via parent_id', function () {
    $menu = Menu::factory()->create();
    $parent = MenuItem::factory()->create(['menu_id' => $menu->id, 'parent_id' => null]);
    $child = MenuItem::factory()->create(['menu_id' => $menu->id, 'parent_id' => $parent->id]);

    expect($child->parent->id)->toBe($parent->id);
});

it('menu item inactive factory state sets is_active to false', function () {
    $item = MenuItem::factory()->inactive()->create();

    expect($item->is_active)->toBeFalse();
});

it('deleting parent menu item cascades to children', function () {
    $menu = Menu::factory()->create();
    $parent = MenuItem::factory()->create(['menu_id' => $menu->id]);
    $child = MenuItem::factory()->create(['menu_id' => $menu->id, 'parent_id' => $parent->id]);

    $parent->delete();

    expect(MenuItem::find($child->id))->toBeNull();
});

it('deleting menu cascades to all items', function () {
    $menu = Menu::factory()->create();
    MenuItem::factory()->count(3)->create(['menu_id' => $menu->id]);

    $menu->delete();

    expect(MenuItem::where('menu_id', $menu->id)->count())->toBe(0);
});

// -- MenuItemType enum --

it('MenuItemType CustomLink has correct value', function () {
    expect(MenuItemType::CustomLink->value)->toBe('custom_link');
});

it('MenuItemType Entry has correct value', function () {
    expect(MenuItemType::Entry->value)->toBe('entry');
});

// -- MenuItemTarget enum --

it('MenuItemTarget Self has correct value', function () {
    expect(MenuItemTarget::Self->value)->toBe('_self');
});

it('MenuItemTarget Blank has correct value', function () {
    expect(MenuItemTarget::Blank->value)->toBe('_blank');
});
