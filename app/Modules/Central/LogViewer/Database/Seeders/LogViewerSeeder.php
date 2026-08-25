<?php

namespace App\Modules\Central\LogViewer\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class LogViewerSeeder extends Seeder
{

    public function run(): void
    {
        $permissions = [
            'logs.view',
            'logs.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                [
                    'name' => $permission,
                    'guard_name' => 'web',
                ]
            );
        }
    }
}
