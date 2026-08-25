<?php

return array (
  'name' => 'LogViewer',
  'slug' => 'log-viewers',
  'scope' => 'Central',
  'description' => 'Manage LogViewer',
  'version' => '1.0.0',
  'namespace' => 'App\\Modules\\Central\\LogViewer',
  'installed' => true,
  'enabled' => true,
  'provider' => 'App\\Modules\\Central\\LogViewer\\Providers\\LogViewerServiceProvider',
  'routes' => 
  array (
    'web' => '/app/app/Modules/Central/LogViewer/Routes/web.php',
    'api' => '/app/app/Modules/Central/LogViewer/Routes/api.php',
  ),
  'migration_path' => '/app/app/Modules/Central/LogViewer/Database/Migrations',
  'seeder' => 'App\\Modules\\Central\\LogViewer\\Database\\Seeders\\LogViewerSeeder',
);
