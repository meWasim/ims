<?php

namespace App\Modules\Central\RolePermission\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = [
            // Role permissions
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',

            // User permissions
            'users.view',
            'users.create',
            'users.update',

            // Permission permissions
            'permissions.view',
            'permissions.create',
            'permissions.update',
            'permissions.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate(
                $permission,
                'web'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Super Admin Role
        |--------------------------------------------------------------------------
        */

        $superAdmin = Role::findOrCreate(
            'Super Admin',
            'web'
        );

        $superAdmin->syncPermissions($permissions);
    }
}
