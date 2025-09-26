<?php

namespace Emmanuelikeogu\DevGuard\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class CleanupCommand extends Command
{
    protected $signature = 'devguard:cleanup {--force : Run without confirmation}';
    protected $description = 'Completely remove DevGuard published files, configs, and database tables';

    public function handle()
    {
        $this->info('🧹 Starting DevGuard cleanup...');

        if (! $this->option('force') && 
            ! $this->confirm('This will permanently delete DevGuard files, configs, and tables. Continue?')) {
            $this->warn('Cleanup cancelled.');
            return;
        }

        // Drop dev_users table
        if (Schema::hasTable('dev_users')) {
            Schema::drop('dev_users');
            $this->line('✔ Dropped table: dev_users');
        }

        // Delete migrations
        $this->deleteGlob(database_path('migrations/*create_dev_users_table*.php'), 'migration');

        // Delete seeder
        $this->deleteFile(database_path('seeders/DevUserSeeder.php'), 'seeder');

        // Delete model
        $this->deleteFile(app_path('Models/DevUser.php'), 'model');

        // Delete configs
        foreach (['devguard.php', 'log-viewer.php', 'scramble.php', 'telescope.php', 'ziggy.php'] as $config) {
            $this->deleteFile(config_path($config), "config: {$config}");
        }

        // Delete published assets
        $this->deleteDirectory(public_path('vendor/devguard'), 'assets directory');

        // Delete standalone.js
        $this->deleteFile(public_path('standalone.js'), 'standalone.js');

        // Delete published views (if any)
        foreach (['devguard', 'scramble', 'log-viewer'] as $viewFolder) {
            $this->deleteDirectory(resource_path("views/vendor/{$viewFolder}"), "views: {$viewFolder}");
        }

        $this->info('✅ DevGuard cleanup completed successfully!');
    }

    /**
     * Delete a single file with logging
     */
    protected function deleteFile($path, $label)
    {
        if (File::exists($path)) {
            File::delete($path);
            $this->line("✔ Deleted {$label}: {$path}");
        }
    }

    /**
     * Delete directory with logging
     */
    protected function deleteDirectory($path, $label)
    {
        if (File::exists($path)) {
            File::deleteDirectory($path);
            $this->line("✔ Deleted {$label}: {$path}");
        }
    }

    /**
     * Delete multiple files by pattern
     */
    protected function deleteGlob($pattern, $label)
    {
        foreach (glob($pattern) as $file) {
            $this->deleteFile($file, $label);
        }
    }
}
