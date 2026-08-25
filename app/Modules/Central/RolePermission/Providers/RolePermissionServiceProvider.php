<?php

namespace App\Modules\Central\RolePermission\Providers;

use App\Modules\Central\RolePermission\Contracts\RolePermissionRepositoryInterface;
use App\Modules\Central\RolePermission\Repositories\RolePermissionRepository;
use Illuminate\Support\ServiceProvider;

class RolePermissionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            RolePermissionRepositoryInterface::class,
            RolePermissionRepository::class
        );
    }

    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Views
        |--------------------------------------------------------------------------
        */

        $this->loadViewsFrom(
            __DIR__ . '/../Views',
            'role-permissions'
        );

        /*
        |--------------------------------------------------------------------------
        | Web Routes
        |--------------------------------------------------------------------------
        */

        $webRoutes = __DIR__ . '/../Routes/web.php';

        if (file_exists($webRoutes)) {
            $this->loadRoutesFrom($webRoutes);
        }

        /*
        |--------------------------------------------------------------------------
        | API Routes
        |--------------------------------------------------------------------------
        */

        $apiRoutes = __DIR__ . '/../Routes/api.php';

        if (file_exists($apiRoutes)) {
            $this->loadRoutesFrom($apiRoutes);
        }

        /*
        |--------------------------------------------------------------------------
        | Migrations
        |--------------------------------------------------------------------------
        */

        $migrationPath =
            __DIR__ . '/../Database/Migrations';

        if (is_dir($migrationPath)) {
            $this->loadMigrationsFrom(
                $migrationPath
            );
        }
    }
}
