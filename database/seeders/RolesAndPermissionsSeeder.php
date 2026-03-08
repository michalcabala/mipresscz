<?php

namespace Database\Seeders;

use App\Enums\UserRole;
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
            'view.users',
            'manage.users',
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
    }
}
