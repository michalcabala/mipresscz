<?php

use App\Enums\UserRole;
use App\Filament\Pages\ManageLocales;
use App\Models\Locale;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    locales()->clearCache();

    $this->superAdmin = User::factory()->create(['role' => UserRole::SuperAdmin]);
    $this->superAdmin->syncRoles([UserRole::SuperAdmin->value]);
});

it('renders the manage locales page', function () {
    $this->actingAs($this->superAdmin);
    Livewire::test(ManageLocales::class)->assertSuccessful();
});

it('shows locales in table', function () {
    $this->actingAs($this->superAdmin);
    $locale = Locale::factory()->create(['code' => 'cs', 'order' => 1]);
    Livewire::test(ManageLocales::class)
        ->call('loadTable')
        ->assertCanSeeTableRecords([$locale]);
});

it('can create a new locale', function () {
    $this->actingAs($this->superAdmin);
    Livewire::test(ManageLocales::class)
        ->callAction('create', [
            'code' => 'de', 'name' => 'German', 'native_name' => 'Deutsch',
            'direction' => 'ltr', 'date_format' => 'd.m.Y',
            'is_active' => true, 'is_admin_available' => true, 'is_frontend_available' => true,
        ])
        ->assertHasNoActionErrors();
    $this->assertDatabaseHas(Locale::class, ['code' => 'de']);
});

it('cannot delete the default locale', function () {
    $this->actingAs($this->superAdmin);
    $defaultLocale = Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'order' => 1]);
    Livewire::test(ManageLocales::class)
        ->callTableAction('delete', $defaultLocale)
        ->assertNotified();
    $this->assertDatabaseHas(Locale::class, ['code' => 'cs']);
});

it('can set a locale as default', function () {
    $this->actingAs($this->superAdmin);
    $cs = Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'order' => 1]);
    $en = Locale::factory()->create(['code' => 'en', 'is_default' => false, 'order' => 2]);
    Livewire::test(ManageLocales::class)
        ->callTableAction('set_default', $en)
        ->assertNotified();
    expect($en->fresh()->is_default)->toBeTrue();
    expect($cs->fresh()->is_default)->toBeFalse();
});
