<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Livewire\Livewire;
use MiPressCz\Core\Filament\Pages\ManageLocales;
use MiPressCz\Core\Models\Locale;

beforeEach(function () {
    /** @var \Tests\TestCase $this */
    $this->seed(RolesAndPermissionsSeeder::class);
    locales()->clearCache();
});

function manageLocalesSuperAdmin(): User
{
    $user = User::factory()->create(['role' => UserRole::SuperAdmin]);
    $user->syncRoles([UserRole::SuperAdmin->value]);

    return $user;
}

it('renders the manage locales page', function () {
    $this->actingAs(manageLocalesSuperAdmin());
    Livewire::test(ManageLocales::class)->assertSuccessful();
});

it('shows locales in table', function () {
    $this->actingAs(manageLocalesSuperAdmin());
    $locale = Locale::factory()->create(['code' => 'cs', 'order' => 1]);
    Livewire::test(ManageLocales::class)
        ->call('loadTable')
        ->assertCanSeeTableRecords([$locale]);
});

it('can create a new locale', function () {
    $this->actingAs(manageLocalesSuperAdmin());
    Livewire::test(ManageLocales::class)
        ->callAction('create', [
            'code' => 'de', 'name' => 'German', 'native_name' => 'Deutsch',
            'direction' => 'ltr', 'date_format' => 'd.m.Y',
            'is_active' => true, 'is_admin_available' => true, 'is_frontend_available' => true,
        ])
        ->assertHasNoActionErrors();
    $this->assertDatabaseHas(Locale::class, ['code' => 'de']);
});

it('hides delete action for the default locale', function () {
    $this->actingAs(manageLocalesSuperAdmin());
    $defaultLocale = Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'order' => 1]);
    $otherLocale = Locale::factory()->create(['code' => 'en', 'is_default' => false, 'order' => 2]);
    Livewire::test(ManageLocales::class)
        ->call('loadTable')
        ->assertTableActionHidden('delete', $defaultLocale)
        ->assertTableActionVisible('delete', $otherLocale);
    $this->assertDatabaseHas(Locale::class, ['code' => 'cs']);
});

it('can set a locale as default', function () {
    $this->actingAs(manageLocalesSuperAdmin());
    $cs = Locale::factory()->create(['code' => 'cs', 'is_default' => true, 'order' => 1]);
    $en = Locale::factory()->create(['code' => 'en', 'is_default' => false, 'order' => 2]);
    Livewire::test(ManageLocales::class)
        ->callTableAction('set_default', $en)
        ->assertNotified();
    expect($en->fresh()->is_default)->toBeTrue();
    expect($cs->fresh()->is_default)->toBeFalse();
});
