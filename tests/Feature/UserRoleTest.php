<?php

use App\Enums\UserRole;

it('label returns translated string for each role', function () {
    foreach (UserRole::cases() as $role) {
        expect($role->label())->toBeString()->not->toBeEmpty();
    }
});

it('icon returns a non-empty string for each role', function () {
    foreach (UserRole::cases() as $role) {
        expect($role->icon())->toBeString()->not->toBeEmpty();
    }
});

it('color returns a non-empty string for each role', function () {
    foreach (UserRole::cases() as $role) {
        expect($role->color())->toBeString()->not->toBeEmpty();
    }
});

it('superadmin has no permissions list', function () {
    expect(UserRole::SuperAdmin->permissions())->toBeEmpty();
});

it('admin has all permissions', function () {
    $permissions = UserRole::Admin->permissions();

    expect($permissions)
        ->toContain('view.users')
        ->toContain('manage.users')
        ->toContain('manage.collections')
        ->toContain('delete.entries');
});

it('contributor has limited permissions', function () {
    $permissions = UserRole::Contributor->permissions();

    expect($permissions)->not->toContain('manage.users');
    expect($permissions)->not->toContain('manage.collections');
    expect($permissions)->toContain('create.entries');
});
