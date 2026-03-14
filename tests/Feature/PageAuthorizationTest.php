<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use MiPressCz\Core\Filament\Pages\ManageLocales;
use MiPressCz\Core\Filament\Pages\ManageSiteSettings;
use MiPressCz\Core\Filament\Pages\ManageTemplates;
use MiPressCz\Core\Filament\Pages\MenuManagerPage;
use MiPressCz\Core\Models\MediaFolder;
use MiPressCz\Core\Models\MediaTag;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

function userWithRole(UserRole $role): User
{
    $user = User::factory()->create(['role' => $role]);
    $user->syncRoles([$role->value]);

    return $user;
}

// ── ManageLocales page ──

describe('ManageLocales authorization', function () {
    it('allows superadmin access', function () {
        $this->actingAs(userWithRole(UserRole::SuperAdmin));

        expect(ManageLocales::canAccess())->toBeTrue();
    });

    it('allows admin access', function () {
        $this->actingAs(userWithRole(UserRole::Admin));

        expect(ManageLocales::canAccess())->toBeTrue();
    });

    it('denies editor access', function () {
        $this->actingAs(userWithRole(UserRole::Editor));

        expect(ManageLocales::canAccess())->toBeFalse();
    });

    it('denies contributor access', function () {
        $this->actingAs(userWithRole(UserRole::Contributor));

        expect(ManageLocales::canAccess())->toBeFalse();
    });
});

// ── ManageSiteSettings page ──

describe('ManageSiteSettings authorization', function () {
    it('allows admin access', function () {
        $this->actingAs(userWithRole(UserRole::Admin));

        expect(ManageSiteSettings::canAccess())->toBeTrue();
    });

    it('denies editor access', function () {
        $this->actingAs(userWithRole(UserRole::Editor));

        expect(ManageSiteSettings::canAccess())->toBeFalse();
    });

    it('denies contributor access', function () {
        $this->actingAs(userWithRole(UserRole::Contributor));

        expect(ManageSiteSettings::canAccess())->toBeFalse();
    });
});

// ── ManageTemplates page ──

describe('ManageTemplates authorization', function () {
    it('allows admin access', function () {
        $this->actingAs(userWithRole(UserRole::Admin));

        expect(ManageTemplates::canAccess())->toBeTrue();
    });

    it('denies contributor access', function () {
        $this->actingAs(userWithRole(UserRole::Contributor));

        expect(ManageTemplates::canAccess())->toBeFalse();
    });
});

// ── MenuManagerPage ──

describe('MenuManagerPage authorization', function () {
    it('allows admin access', function () {
        $this->actingAs(userWithRole(UserRole::Admin));

        expect(MenuManagerPage::canAccess())->toBeTrue();
    });

    it('allows editor access', function () {
        $this->actingAs(userWithRole(UserRole::Editor));

        expect(MenuManagerPage::canAccess())->toBeTrue();
    });

    it('denies contributor access', function () {
        $this->actingAs(userWithRole(UserRole::Contributor));

        expect(MenuManagerPage::canAccess())->toBeFalse();
    });
});

// ── MediaFolder policy ──

describe('MediaFolder authorization', function () {
    it('allows admin to manage media folders', function () {
        $admin = userWithRole(UserRole::Admin);
        $folder = MediaFolder::factory()->create();

        expect($admin->can('viewAny', MediaFolder::class))->toBeTrue()
            ->and($admin->can('create', MediaFolder::class))->toBeTrue()
            ->and($admin->can('update', $folder))->toBeTrue()
            ->and($admin->can('delete', $folder))->toBeTrue();
    });

    it('allows editor to manage media folders', function () {
        $editor = userWithRole(UserRole::Editor);
        $folder = MediaFolder::factory()->create();

        expect($editor->can('viewAny', MediaFolder::class))->toBeTrue()
            ->and($editor->can('create', MediaFolder::class))->toBeTrue()
            ->and($editor->can('update', $folder))->toBeTrue()
            ->and($editor->can('delete', $folder))->toBeTrue();
    });

    it('denies contributor access to media folders', function () {
        $contributor = userWithRole(UserRole::Contributor);

        expect($contributor->can('viewAny', MediaFolder::class))->toBeFalse()
            ->and($contributor->can('create', MediaFolder::class))->toBeFalse();
    });
});

// ── MediaTag policy ──

describe('MediaTag authorization', function () {
    it('allows admin to manage media tags', function () {
        $admin = userWithRole(UserRole::Admin);
        $tag = MediaTag::factory()->create();

        expect($admin->can('viewAny', MediaTag::class))->toBeTrue()
            ->and($admin->can('create', MediaTag::class))->toBeTrue()
            ->and($admin->can('update', $tag))->toBeTrue()
            ->and($admin->can('delete', $tag))->toBeTrue();
    });

    it('denies contributor access to media tags', function () {
        $contributor = userWithRole(UserRole::Contributor);

        expect($contributor->can('viewAny', MediaTag::class))->toBeFalse()
            ->and($contributor->can('create', MediaTag::class))->toBeFalse();
    });
});
