<?php

namespace App\Console\Commands;

use App\Services\ModuleManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ModuleRoutesCommand extends Command
{
    protected $signature = 'module:routes
                            {module? : Module name}
                            {--scope= : Module scope (Central or Tenant)}';

    protected $description = 'List routes belonging to application modules';

    public function handle(
        ModuleManager $moduleManager
    ): int {
        $moduleName = $this->argument('module');

        $scope = $this->option('scope');

        if ($moduleName) {
            if (! $scope) {
                $scope = $this->choice(
                    'Which scope does this module belong to?',
                    [
                        'Central',
                        'Tenant',
                    ],
                    0
                );
            }

            $module = $moduleManager->find(
                $moduleName,
                $scope
            );

            if (! $module) {
                $this->error(
                    "Module [{$scope}/{$moduleName}] does not exist."
                );

                return self::FAILURE;
            }

            $modules = [$module];
        } else {
            $modules = $moduleManager->all();
        }

        $routes = Route::getRoutes();

        $rows = [];

        foreach ($routes as $route) {
            $action = $route->getActionName();

            foreach ($modules as $module) {
                $namespace = $module['namespace'];

                if (
                    $namespace
                    && Str::startsWith(
                        $action,
                        $namespace
                    )
                ) {
                    $rows[] = [
                        implode(
                            '|',
                            $route->methods()
                        ),
                        $route->uri(),
                        $route->getName() ?? '-',
                        $action,
                        $module['name'],
                    ];

                    break;
                }
            }
        }

        if (empty($rows)) {
            $this->warn(
                'No routes found for the selected module(s).'
            );

            return self::SUCCESS;
        }

        $this->newLine();

        $this->table(
            [
                'Method',
                'URI',
                'Name',
                'Action',
                'Module',
            ],
            $rows
        );

        $this->newLine();

        return self::SUCCESS;
    }
}
