<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::automaticallyEagerLoadRelationships();

        // Implicitly grant 'administrator', 'admin', and 'Super Admin' role all permissions
        Gate::before(function ($user, $ability) {
            return $user->hasRole(['administrator', 'admin', 'Super Admin']) ? true : null;
        });

        // Auto-discover migrations from all modules
        $modulesPath = base_path('Modules');
        if (File::isDirectory($modulesPath)) {
            foreach (File::directories($modulesPath) as $moduleDir) {
                $migrationDir = $moduleDir . '/database/migrations';
                if (File::isDirectory($migrationDir)) {
                    $this->loadMigrationsFrom($migrationDir);
                }
            }
        }
    }
}
