<?php

namespace Emmanuelikeogu\DevMonitoringGuard;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class DevMonitoringGuardServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Custom Auth guard
        Auth::extend('dev-user', function ($app, $name, array $config) {
            return Auth::createUserProvider($config['provider']);
        });

        // Load package views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'dev-guard');

        // Routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // Single publish command for everything
        $this->publishes([
            // Views
            __DIR__ . '/../resources/views' => resource_path('views/vendor/dev-guard'),

            // React/JS stubs
            __DIR__ . '/../resources/js' => resource_path('js/vendor/dev-guard'),
            __DIR__ . '/../stubs/package.json' => base_path('package.json'),
            __DIR__ . '/../stubs/vite.config.js' => base_path('vite.config.js'),
            __DIR__ . '/../stubs/tsconfig.json' => base_path('tsconfig.json'),

            // Migration
            __DIR__ . '/../Database/migrations/create_dev_users_table.php.stub'
                => database_path('migrations/' . date('Y_m_d_His') . '_create_dev_users_table.php'),
            __DIR__ . '/../Database/Seeders/DevUserSeeder.php'
                => database_path('seeders/DevUserSeeder.php'),

            // Vendor configs
            base_path('vendor/opcodesio/log-viewer/config/log-viewer.php') => config_path('log-viewer.php'),
            base_path('vendor/guanguans/laravel-scramble/config/scramble.php') => config_path('scramble.php'),
            base_path('vendor/laravel/telescope/config/telescope.php') => config_path('telescope.php'),

            // Package config
            __DIR__ . '/../config/devguard.php' => config_path('devguard.php'),
        ], 'dev-guard-all');
    }

    public function register()
    {
        // Merge only your own config (not vendor ones!)
        $this->mergeConfigFrom(
            __DIR__ . '/../config/devguard.php',
            'devguard'
        );
    }
}
