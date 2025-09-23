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

        $this->info('DevGuard cleanup completed ✅');
    }
}
