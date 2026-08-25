<?php

namespace App\Modules\Central\LogViewer\Providers;

use App\Modules\Central\LogViewer\Contracts\LogViewerRepositoryInterface;
use App\Modules\Central\LogViewer\Repositories\LogViewerRepository;
use Illuminate\Support\ServiceProvider;

class LogViewerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            LogViewerRepositoryInterface::class,
            LogViewerRepository::class
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
            'log-viewers'
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
