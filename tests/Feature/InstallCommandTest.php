<?php

use App\Enums\UserRole;
use MiPressCz\Core\Models\Locale;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
});

it('runs migrations and seeds locales', function () {
    $this->artisan('mipresscz:install', [
        '--admin-name' => 'Test Admin',
        '--admin-email' => 'admin@test.example',
        '--admin-password' => 'password123',
    ])->assertSuccessful();

    expect(Locale::query()->count())->toBeGreaterThan(0);
});

it('creates the admin user with the provided credentials', function () {
    $this->artisan('mipresscz:install', [
        '--admin-name' => 'Test Admin',
        '--admin-email' => 'admin@install.example',
        '--admin-password' => 'password123',
    ])->assertSuccessful();

    $this->assertDatabaseHas('users', [
        'name' => 'Test Admin',
        'email' => 'admin@install.example',
    ]);
});

it('seeds roles and permissions', function () {
    $this->artisan('mipresscz:install', [
        '--admin-name' => 'Test Admin',
        '--admin-email' => 'admin@roles.example',
        '--admin-password' => 'password123',
    ])->assertSuccessful();

    foreach (UserRole::cases() as $userRole) {
        expect(Role::findByName($userRole->value))->not->toBeNull();
    }
});

it('aborts if already installed without --force', function () {
    app(\MiPressCz\Core\Database\Seeders\LocaleSeeder::class)->run();

    $this->artisan('mipresscz:install', [
        '--admin-name' => 'Test Admin',
        '--admin-email' => 'admin@force.example',
        '--admin-password' => 'password123',
    ])->assertFailed();
});

it('reinstalls when --force is passed', function () {
    app(\MiPressCz\Core\Database\Seeders\LocaleSeeder::class)->run();

    $this->artisan('mipresscz:install', [
        '--admin-name' => 'Forced Admin',
        '--admin-email' => 'admin@forcedinstall.example',
        '--admin-password' => 'password123',
        '--force' => true,
    ])->assertSuccessful();

    $this->assertDatabaseHas('users', [
        'email' => 'admin@forcedinstall.example',
    ]);
});

it('does not run content seeder without --seed', function () {
    $this->artisan('mipresscz:install', [
        '--admin-name' => 'Test Admin',
        '--admin-email' => 'admin@noseed.example',
        '--admin-password' => 'password123',
    ])->assertSuccessful();

    expect(\MiPressCz\Core\Models\Entry::query()->count())->toBe(0);
});
