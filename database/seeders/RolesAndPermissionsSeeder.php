<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view.users', 'manage.users',
            'view.collections', 'manage.collections',
            'view.entries', 'create.entries', 'update.entries', 'delete.entries',
            'view.taxonomies', 'manage.taxonomies',
            'view.global_sets', 'manage.global_sets',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        foreach (UserRole::cases() as $userRole) {
            if ($userRole === UserRole::SuperAdmin) {
                Role::findOrCreate($userRole->value, 'web');

                continue;
            }

            $role = Role::findOrCreate($userRole->value, 'web');
            $role->syncPermissions($userRole->permissions());
        }

        // Sync Spatie roles for existing users based on their role column
        User::whereNotNull('role')->each(function (User $user) {
            $user->syncRoles([$user->role->value]);
        });
    }
}
