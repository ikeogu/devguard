<?php

namespace Emmanuelikeogu\DevGuard\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class CleanupCommand extends Command
{
    protected $signature = 'devguard:cleanup';
    protected $description = 'Remove DevGuard published files and database tables';

    public function handle()
    {
        $this->info('Cleaning up DevGuard...');

        // Drop dev_users table if exists
        if (Schema::hasTable('dev_users')) {
            Schema::drop('dev_users');
            $this->info('Dropped table: dev_users');
        }

        // Delete migration file
        foreach (glob(database_path('migrations/*create_dev_users_table*.php')) as $file) {
            File::delete($file);
            $this->info("Deleted migration: {$file}");
        }

        // Delete seeder
        $seeder = database_path('seeders/DevUserSeeder.php');
        if (File::exists($seeder)) {
            File::delete($seeder);
            $this->info("Deleted seeder: {$seeder}");
        }

        // Delete model
        $model = app_path('Models/DevUser.php');
        if (File::exists($model)) {
            File::delete($model);
            $this->info("Deleted model: {$model}");
        }

        // Delete config file
        $config = config_path('devguard.php');
        if (File::exists($config)) {
            File::delete($config);
            $this->info("Deleted config: {$config}");
        }

         $logViewerConfig = config_path('log-viewer.php');
        if (File::exists($logViewerConfig)) {
            File::delete($logViewerConfig);
            $this->info("Deleted config: {$logViewerConfig}");
        }

        $scrambleConfig = config_path('scramble.php');
        if (File::exists($scrambleConfig)) {
            File::delete($scrambleConfig);
            $this->info("Deleted config: {$scrambleConfig}");
        }

        $telescopeConfig = config_path('telescope.php');
        if (File::exists($telescopeConfig)) {
            File::delete($telescopeConfig);
            $this->info("Deleted config: {$telescopeConfig}");
        }

        // Delete published assets
        $assetsPath = public_path('vendor/devguard');
        if (File::exists($assetsPath)) {
            File::deleteDirectory($assetsPath);
            $this->info("Deleted assets directory: {$assetsPath}");
        }

        // Delete standalone.js
        $standalone = public_path('standalone.js');
        if (File::exists($standalone)) {
            File::delete($standalone);
            $this->info("Deleted file: {$standalone}");
        }

        // Delete 

        $this->info('DevGuard cleanup completed ✅');
    }
}
