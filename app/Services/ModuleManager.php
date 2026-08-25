<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use RuntimeException;

class ModuleManager
{
    /**
     * Get all modules.
     */
    public function all(): array
    {
        return array_merge(
            $this->central(),
            $this->tenant(),
        );
    }

    /**
     * Get central modules.
     */
    public function central(): array
    {
        return $this->modules('Central');
    }

    /**
     * Get tenant modules.
     */
    public function tenant(): array
    {
        return $this->modules('Tenant');
    }

    /**
     * Find a module.
     */
    public function find(
        string $name,
        string $scope
    ): ?array {
        foreach ($this->modules($scope) as $module) {
            if (
                strcasecmp(
                    $module['name'],
                    $name
                ) === 0
            ) {
                return $module;
            }
        }

        return null;
    }

    /**
     * Determine whether a module exists.
     */
    public function exists(
        string $name,
        string $scope
    ): bool {
        return $this->find(
            $name,
            $scope
        ) !== null;
    }

    /**
     * Determine whether a module is enabled.
     */
    public function isEnabled(
        string $name,
        string $scope
    ): bool {
        $module = $this->find(
            $name,
            $scope
        );

        return $module !== null
            && $module['enabled'] === true;
    }

    /**
     * Enable a module.
     */
    public function enable(
        string $name,
        string $scope
    ): void {
        $this->updateStatus(
            $name,
            $scope,
            true
        );
    }

    /**
     * Disable a module.
     */
    public function disable(
        string $name,
        string $scope
    ): void {
        $this->updateStatus(
            $name,
            $scope,
            false
        );
    }

    /**
     * Get enabled modules.
     */
    public function enabled(): array
    {
        return array_values(
            array_filter(
                $this->all(),
                fn (array $module) =>
                    $module['enabled'] === true
            )
        );
    }

    /**
     * Get disabled modules.
     */
    public function disabled(): array
    {
        return array_values(
            array_filter(
                $this->all(),
                fn (array $module) =>
                    $module['enabled'] === false
            )
        );
    }

    /**
     * Discover modules from a scope.
     */
    private function modules(
        string $scope
    ): array {
        $basePath = app_path(
            "Modules/{$scope}"
        );

        if (! File::isDirectory($basePath)) {
            return [];
        }

        $modules = [];

        foreach (
            File::directories($basePath)
            as $modulePath
        ) {
            $name = basename($modulePath);

            $configPath =
                "{$modulePath}/Config/module.php";

            if (! File::exists($configPath)) {
                continue;
            }

            $config = require $configPath;

            $modules[] = [
                'name' => $config['name'] ?? $name,

                'slug' => $config['slug']
                    ?? strtolower($name),

                'scope' => $scope,

                'path' => $modulePath,

                'config_path' => $configPath,

                'namespace' =>
                    $config['namespace'] ?? null,

                'description' =>
                    $config['description'] ?? '',

                'version' =>
                    $config['version'] ?? '1.0.0',

                'installed' =>
                    $config['installed'] ?? true,

                'enabled' =>
                    $config['enabled'] ?? false,

                'provider' =>
                    $config['provider'] ?? null,

                'routes' =>
                    $config['routes'] ?? [],

                'migration_path' =>
                    $config['migration_path'] ?? null,

                'seeder' =>
                    $config['seeder'] ?? null,
            ];
        }

        return $modules;
    }

    /**
     * Update module enabled status.
     */
    private function updateStatus(
        string $name,
        string $scope,
        bool $enabled
    ): void {
        $module = $this->find(
            $name,
            $scope
        );

        if ($module === null) {
            throw new RuntimeException(
                "Module [{$scope}/{$name}] does not exist."
            );
        }

        $configPath =
            $module['config_path'];

        $config = require $configPath;

        $config['enabled'] = $enabled;

        $content =
            "<?php\n\nreturn "
            . var_export($config, true)
            . ";\n";

        File::put(
            $configPath,
            $content
        );
    }
}
