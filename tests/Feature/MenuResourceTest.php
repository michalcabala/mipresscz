<?php

use App\Enums\UserRole;
use App\Models\User;
use Livewire\Livewire;
use MiPressCz\Core\Filament\Resources\Menus\Pages\CreateMenu;
use MiPressCz\Core\Filament\Resources\Menus\Pages\EditMenu;
use MiPressCz\Core\Filament\Resources\Menus\Pages\ListMenus;
use MiPressCz\Core\Filament\Resources\Menus\Pages\ManageMenuItems;
use MiPressCz\Core\Models\Locale;
use MiPressCz\Core\Models\Menu;
use MiPressCz\Core\Models\MenuItem;

beforeEach(function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    (new \Database\Seeders\RolesAndPermissionsSeeder)->run();
    Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'is_active' => true, 'order' => 1]);
    locales()->clearCache();

    $this->admin = User::factory()->create(['role' => UserRole::Admin]);
    $this->admin->syncRoles([UserRole::Admin->value]);
    $this->actingAs($this->admin);
});

// -- List page --

it('can render the menus list page', function () {
    Livewire::test(ListMenus::class)
        ->assertOk();
});

it('can see menus in the table', function () {
    $menus = Menu::factory()->count(3)->create();

    Livewire::test(ListMenus::class)
        ->call('loadTable')
        ->assertCanSeeTableRecords($menus);
});

// -- Create page --

it('can render the menu create page', function () {
    Livewire::test(CreateMenu::class)
        ->assertOk();
});

it('can create a menu', function () {
    Livewire::test(CreateMenu::class)
        ->fillForm([
            'title'    => 'Hlavní navigace',
            'handle'   => 'primary',
            'location' => 'primary',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Menu::where('handle', 'primary')->exists())->toBeTrue();
});

it('validates required fields on menu create', function () {
    Livewire::test(CreateMenu::class)
        ->fillForm([
            'title'  => null,
            'handle' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['title' => 'required', 'handle' => 'required']);
});

it('validates handle uniqueness on menu create', function () {
    Menu::factory()->create(['handle' => 'primary']);

    Livewire::test(CreateMenu::class)
        ->fillForm([
            'title'  => 'Primary',
            'handle' => 'primary',
        ])
        ->call('create')
        ->assertHasFormErrors(['handle' => 'unique']);
});

// -- Edit page --

it('can render the menu edit page', function () {
    $menu = Menu::factory()->create();

    Livewire::test(EditMenu::class, ['record' => $menu->id])
        ->assertOk();
});

it('can update a menu', function () {
    $menu = Menu::factory()->create(['title' => 'Old Title']);

    Livewire::test(EditMenu::class, ['record' => $menu->id])
        ->fillForm(['title' => 'New Title'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($menu->fresh()->title)->toBe('New Title');
});

it('can delete a menu from edit page', function () {
    $menu = Menu::factory()->create();

    Livewire::test(EditMenu::class, ['record' => $menu->id])
        ->callAction('delete')
        ->assertRedirect();

    expect(Menu::find($menu->id))->toBeNull();
});

// -- Manage Items page (TreeRelationPage) --

it('can render the manage menu items page', function () {
    $menu = Menu::factory()->create();

    Livewire::test(ManageMenuItems::class, ['record' => $menu->id])
        ->assertOk();
});

it('can create a menu item via manage items page', function () {
    $menu = Menu::factory()->create();

    Livewire::test(ManageMenuItems::class, ['record' => $menu->id])
        ->callAction('create', [
            'type'      => 'custom_link',
            'title'     => 'Homepage',
            'url'       => 'https://example.com',
            'target'    => '_self',
            'is_active' => true,
        ])
        ->assertHasNoActionErrors();

    expect(MenuItem::where('menu_id', $menu->id)->where('title', 'Homepage')->exists())->toBeTrue();
});

it('manage items page shows existing items count', function () {
    $menu = Menu::factory()->create();
    MenuItem::factory()->count(2)->create(['menu_id' => $menu->id]);

    expect($menu->items()->count())->toBe(2);

    Livewire::test(ManageMenuItems::class, ['record' => $menu->id])
        ->assertOk();
});
