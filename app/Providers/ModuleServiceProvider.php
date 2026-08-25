<?php

namespace App\Providers;

use App\Services\ModuleManager;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            ModuleManager::class,
            fn () => new ModuleManager()
        );
    }

    public function boot(ModuleManager $moduleManager): void
    {
        foreach ($moduleManager->enabled() as $module) {

            $provider = $module['provider'] ?? null;

            if (! $provider) {
                continue;
            }

            if (class_exists($provider)) {
                $this->app->register($provider);
            }
        }
    }
}
