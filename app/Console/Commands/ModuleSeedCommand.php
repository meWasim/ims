<?php

namespace App\Console\Commands;

use App\Services\ModuleManager;
use Illuminate\Console\Command;
use Illuminate\Database\Seeder;
use RuntimeException;

class ModuleSeedCommand extends Command
{
    protected $signature = 'module:seed
                            {name : Module name}
                            {--scope= : Module scope (Central or Tenant)}';

    protected $description = 'Run the seeder for a module';

    public function handle(ModuleManager $moduleManager): int
    {
        $name = $this->argument('name');

        $scope = $this->option('scope');

        if (! $scope) {
            $scope = $this->choice(
                'Which scope does this module belong to?',
                ['Central', 'Tenant'],
                0
            );
        }

        $module = $moduleManager->find(
            $name,
            $scope
        );

        if (! $module) {
            $this->error(
                "Module [{$scope}/{$name}] does not exist."
            );

            return self::FAILURE;
        }

        if (! $module['enabled']) {
            $this->warn(
                "Module [{$scope}/{$name}] is disabled."
            );

            if (! $this->confirm(
                'Do you want to run its seeder anyway?',
                false
            )) {
                return self::SUCCESS;
            }
        }

        $seeder = $module['seeder'];

        if (! $seeder) {
            $this->error(
                "No seeder configured for module [{$scope}/{$name}]."
            );

            return self::FAILURE;
        }

        if (! class_exists($seeder)) {
            $this->error(
                "Seeder class [{$seeder}] does not exist."
            );

            return self::FAILURE;
        }

        try {
            $this->newLine();

            $this->components->info(
                "Seeding module: {$scope}/{$name}"
            );

            $this->call('db:seed', [
                '--class' => $seeder,
                '--force' => true,
            ]);

            $this->newLine();

            $this->components->info(
                "Module [{$scope}/{$name}] seeded successfully."
            );

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error(
                "Failed to seed module [{$scope}/{$name}]."
            );

            $this->line(
                $e->getMessage()
            );

            return self::FAILURE;
        }
    }
}
