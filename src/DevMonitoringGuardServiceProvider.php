<?php

namespace Emmanuelikeogu\DevMonitoringGuard;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Opcodes\LogViewer\LogViewerServiceProvider as LogViewerLogViewerServiceProvider;


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
            __DIR__ . '/../database/migrations/create_dev_users_table.php.stub'
            => database_path('migrations/' . date('Y_m_d_His') . '_create_dev_users_table.php'),
            __DIR__ . '/../database/Seeders/DevUserSeeder.php'
            => database_path('seeders/DevUserSeeder.php'),

            // Vendor configs
            base_path('vendor/opcodesio/log-viewer/config/log-viewer.php') => config_path('log-viewer.php'),
            base_path('vendor/guanguans/laravel-scramble/config/scramble.php') => config_path('scramble.php'),
            base_path('vendor/laravel/telescope/config/telescope.php') => config_path('telescope.php'),
            // Package config
            __DIR__ . '/../config/devguard.php' => config_path('devguard.php'),
        ], 'dev-guard-all');

        $this->publishVendorConfigs();
    }

    protected function publishVendorConfigs()
    {
        $vendorConfigs = [];

        // Check for Log Viewer
        if (class_exists(LogViewerLogViewerServiceProvider::class)) {
            $logViewerConfig = base_path('vendor/opcodesio/log-viewer/config/log-viewer.php');
            if (file_exists($logViewerConfig)) {
                $vendorConfigs[$logViewerConfig] = config_path('log-viewer.php');
            }
        }

        // Check for Scramble
        if (class_exists(\Dedoc\Scramble\ScrambleServiceProvider::class)) {
            $scrambleConfig = base_path('vendor/dedoc/scramble/config/scramble.php');
            if (file_exists($scrambleConfig)) {
                $vendorConfigs[$scrambleConfig] = config_path('scramble.php');
            }
        }

        // Check for Telescope
        if (class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $telescopeConfig = base_path('vendor/laravel/telescope/config/telescope.php');
            if (file_exists($telescopeConfig)) {
                $vendorConfigs[$telescopeConfig] = config_path('telescope.php');
            }
        }

        if (!empty($vendorConfigs)) {
            $this->publishes($vendorConfigs, 'dev-guard-vendor-configs');
            $this->publishes($vendorConfigs, 'dev-guard-all');
        }
    }


    protected function registerVendorProviders()
    {
        $providers = [
            LogViewerLogViewerServiceProvider::class,
            \Dedoc\Scramble\ScrambleServiceProvider::class,
            \Laravel\Telescope\TelescopeServiceProvider::class,
        ];

        foreach ($providers as $provider) {
            if (class_exists($provider)) {
                $this->app->register($provider);
            }
        }
    }

    public function register()
    {
        // Merge only your own config (not vendor ones!)
        $this->mergeConfigFrom(
            __DIR__ . '/../config/devguard.php',
            'devguard'
        );

        $this->registerVendorProviders();
    }
}
