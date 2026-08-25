<?php

return array (
  'name' => 'RolePermission',
  'slug' => 'role-permissions',
  'scope' => 'Central',
  'description' => 'Manage RolePermission',
  'version' => '1.0.0',
  'namespace' => 'App\\Modules\\Central\\RolePermission',
  'installed' => true,
  'enabled' => true,
  'provider' => 'App\\Modules\\Central\\RolePermission\\Providers\\RolePermissionServiceProvider',
  'routes' => 
  array (
    'web' => '/app/app/Modules/Central/RolePermission/Routes/web.php',
    'api' => '/app/app/Modules/Central/RolePermission/Routes/api.php',
  ),
  'migration_path' => '/app/app/Modules/Central/RolePermission/Database/Migrations',
  'seeder' => 'App\\Modules\\Central\\RolePermission\\Database\\Seeders\\RolePermissionSeeder',
);
