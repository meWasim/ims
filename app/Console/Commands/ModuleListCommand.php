<?php

namespace App\Console\Commands;

use App\Services\ModuleManager;
use Illuminate\Console\Command;

class ModuleListCommand extends Command
{
    protected $signature = 'module:list';

    protected $description = 'List all application modules';

    public function handle(
        ModuleManager $moduleManager
    ): int {
        $modules = $moduleManager->all();

        if (empty($modules)) {
            $this->warn(
                'No modules found.'
            );

            return self::SUCCESS;
        }

        $rows = [];

        foreach ($modules as $module) {
            $rows[] = [
                $module['name'],
                $module['scope'],
                $module['version'],
                $module['enabled']
                    ? '<fg=green>Enabled</>'
                    : '<fg=red>Disabled</>',
                $module['description'],
            ];
        }

        $this->newLine();

        $this->table(
            [
                'Module',
                'Scope',
                'Version',
                'Status',
                'Description',
            ],
            $rows
        );

        $this->newLine();

        return self::SUCCESS;
    }
}
