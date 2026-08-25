<?php

namespace App\Console\Commands;

use App\Services\ModuleManager;
use Illuminate\Console\Command;
use RuntimeException;

class ModuleDisableCommand extends Command
{
    protected $signature = 'module:disable
                            {name : The module name}
                            {--scope= : Module scope (Central or Tenant)}';

    protected $description = 'Disable an application module';

    public function handle(
        ModuleManager $moduleManager
    ): int {
        $name = $this->argument('name');

        $scope = $this->option('scope');

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

        try {
            $moduleManager->disable(
                $name,
                $scope
            );
        } catch (RuntimeException $exception) {
            $this->error(
                $exception->getMessage()
            );

            return self::FAILURE;
        }

        $module = $moduleManager->find(
            $name,
            $scope
        );

        $this->newLine();

        $this->components->info(
            "Module [{$module['name']}] disabled successfully."
        );

        $this->table(
            [
                'Name',
                'Scope',
                'Status',
            ],
            [
                [
                    $module['name'],
                    $module['scope'],
                    'Disabled',
                ],
            ]
        );

        $this->newLine();

        $this->line(
            'The module provider will no longer be registered on the next application request.'
        );

        return self::SUCCESS;
    }
}
