<?php

namespace Emmanuelikeogu\DevGuard;

use Emmanuelikeogu\DevGuard\Console\CleanupCommand;
use Emmanuelikeogu\DevGuard\Helpers\AssetHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Opcodes\LogViewer\LogViewerServiceProvider as LogViewerLogViewerServiceProvider;
use Tighten\Ziggy\ZiggyServiceProvider as ZiggyServiceProvider;

class DevGuardServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Vite::useBuildDirectory('vendor/devguard');

        // Load package views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'devguard');

        // Routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/devguard_auth.php');
        // Single publish command for everything
        $this->publishes([

            //Models
            __DIR__ . '/../stubs/DevUser.php.stub' => app_path('Models/DevUser.php'),
            // Publish assets (compiled React/Inertia build)

            __DIR__ . '/../dist' => public_path('vendor/devguard'),

            __DIR__ . '/../resources/views' => resource_path('views/vendor/devguard'),
            // React/JS stubs
            __DIR__ . '/../public/standalone.js' => base_path('public/standalone.js'),

            // Package config
            __DIR__ . '/../config/devguard.php' => config_path('devguard.php'),
        ], 'dev-guard-all');

        // Migration
        $this->publishDatabaseFiles();
        // Vendor configs
        $this->publishVendorConfigs();

        if ($this->app->runningInConsole()) {
            $this->commands([
                CleanupCommand::class,
            ]);
        }

        // 2. Silent auto-install on first require
        if (!file_exists(config_path('devguard.php'))) {
            $this->app->booted(function () {
                $this->installDevGuard();
            });
        }

        $this->app->afterResolving(\Illuminate\Foundation\Configuration\Middleware::class, function ($middleware) {
            $middleware->redirectGuestsTo(function (Request $request) {
                if (! $request->expectsJson()) {
                    return route('it:login');
                }
            });
        });

        $this->app['router']->pushMiddlewareToGroup(
            'web',
            \Emmanuelikeogu\DevGuard\Http\Middleware\HandleInertiaRequests::class
        );

        $this->enforceThirdPartyConfig();
    }


    protected function publishDatabaseFiles()
    {
        $publishes = [];

        // Only publish migration if it doesn't already exist
        if (!$this->migrationExists('create_dev_users_table')) {
            $publishes[__DIR__ . '/../database/migrations/create_dev_users_table.php.stub'] =
                database_path('migrations/' . date('Y_m_d_His') . '_create_dev_users_table.php');

            info('Publishing dev_users migration...');
        } else {
            info('Dev users migration already exists, skipping...');
        }

        // Always allow seeder to be updated
        $publishes[__DIR__ . '/../database/seeders/DevUserSeeder.php'] =
            database_path('seeders/DevUserSeeder.php');

        if (!empty($publishes)) {
            $this->publishes($publishes, 'dev-guard-database');
            $this->publishes($publishes, 'dev-guard-all');
        }
    }

    protected function migrationExists($migrationName)
    {
        $migrationPath = database_path('migrations');

        if (!is_dir($migrationPath)) {
            return false;
        }

        $files = scandir($migrationPath);

        foreach ($files as $file) {
            if (strpos($file, $migrationName) !== false && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                return true;
            }
        }

        return false;
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

        if (class_exists(ZiggyServiceProvider::class)) {
            $ziggyConfig = base_path('vendor/tightenco/ziggy/config/ziggy.php');
            if (file_exists($ziggyConfig)) {
                $vendorConfigs[$ziggyConfig] = config_path('ziggy.php');
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

        $this->mergeAuthConfig();

        $this->registerVendorProviders();
    }

    protected function installDevGuard()
    {
        try {
            // Publish all package assets
            Artisan::call('vendor:publish', [
                '--tag' => 'dev-guard-config',
                '--force' => true,
            ]);
            Artisan::call('vendor:publish', [
                '--tag' => 'dev-guard-migrations',
                '--force' => true,
            ]);
            Artisan::call('vendor:publish', [
                '--tag' => 'dev-guard-seeders',
                '--force' => true,
            ]);
            Artisan::call('vendor:publish', [
                '--tag' => 'dev-guard-inertia',
                '--force' => true,
            ]);
            Artisan::call('vendor:publish', [
                '--tag' => 'dev-guard-routes',
                '--force' => true,
            ]);

            Artisan::call('vendor:publish', [
                '--tag' => 'log-viewer-assets',
                '--force' => true,
            ]);

            // Run migrations & seed default user
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\DevUserSeeder',
                '--force' => true,
            ]);

            // Append route include to web.php if missing
            $routesFile = base_path('routes/web.php');
            if (strpos(file_get_contents($routesFile), 'devguard.php') === false) {
                file_put_contents(
                    $routesFile,
                    "\n\n// DevGuard\nrequire base_path('routes/devguard.php');\n",
                    FILE_APPEND
                );
            }
        } catch (\Exception $e) {
            // swallow errors so composer require doesn't break
        }
    }

    protected function mergeAuthConfig()
    {
        $config = $this->app['config'];

        // 1. Add dev_user guard if not already present
        $guards = $config->get('auth.guards', []);
        if (! isset($guards['dev_user'])) {
            $guards['dev_user'] = [
                'driver' => 'session',
                'provider' => 'dev_users',
            ];
            $config->set('auth.guards', $guards);
        }

        // 2. Add dev_users provider if not already present
        $providers = $config->get('auth.providers', []);
        if (! isset($providers['dev_users'])) {
            $providers['dev_users'] = [
                'driver' => 'eloquent',
                'model' => \Emmanuelikeogu\DevGuard\Models\DevUser::class,
            ];
            $config->set('auth.providers', $providers);
        }
    }

    protected function enforceThirdPartyConfig()
    {
        $config = $this->app['config'];

        // Enforce Log Viewer config
        if (class_exists(LogViewerLogViewerServiceProvider::class)) {
            $logViewerConfig = $config->get('log-viewer', []);
            $logViewerConfig['route']['middleware'] = array_unique(array_merge(
                $logViewerConfig['route']['middleware'] ?? [],
                ['web', 'auth:dev_user']
            ));
            $config->set('log-viewer', $logViewerConfig);
        }

        // Enforce Telescope config
        if (class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $telescopeConfig = $config->get('telescope', []);
            $telescopeConfig['path'] = env('TELESCOPE_PATH', 'telescope');
            $telescopeConfig['middleware'] = array_unique(array_merge(
                $telescopeConfig['middleware'] ?? [],
                ['web', 'auth:dev_user']
            ));
            $config->set('telescope', $telescopeConfig);
        }

        // Enforce Scramble config
        if (class_exists(\Dedoc\Scramble\ScrambleServiceProvider::class)) {
            $scrambleConfig = $config->get('scramble', []);
            $scrambleConfig['route_middleware'] = array_unique(array_merge(
                $scrambleConfig['route_middleware'] ?? [],
                ['web', 'auth:dev_user']
            ));
            $config->set('scramble', $scrambleConfig);
        }


        Route::middleware(['web', 'auth:dev-user'])
        ->group(function () {
            // Re-mount Telescope, Log Viewer, Scramble here
            if (class_exists(\Laravel\Telescope\Telescope::class)) {
                //\Laravel\Telescope\Telescope::routes();
            }

            if (class_exists(LogViewerLogViewerServiceProvider::class)) {
                Route::get('logs/{any?}', '\Opcodes\LogViewer\Http\Controllers\LogViewerController')
                    ->where('any', '.*');
            }

            if (class_exists(\Dedoc\Scramble\ScrambleServiceProvider::class)) {
                Route::get('api/docs/{any?}', '\Dedoc\Scramble\Http\Controllers\DocsController')
                    ->where('any', '.*');
            }
        });
    }
}
