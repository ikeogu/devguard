<?php

namespace ZojaTech\DevGuard\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class CleanupCommand extends Command
{
    protected $signature = 'devguard:cleanup {--force : Run without confirmation} {--keep-config : Keep configuration files}';
    protected $description = 'Completely remove DevGuard published files, configs, and database tables';

    public function handle()
    {
        $this->info('🧹 Starting DevGuard cleanup...');

        if (! $this->option('force') && 
            ! $this->confirm('This will permanently delete DevGuard files, configs, and tables. Continue?')) {
            $this->warn('Cleanup cancelled.');
            return 1;
        }

        $keepConfig = $this->option('keep-config');

        // Drop dev_users table
        $this->dropTables();

        // Delete migrations
        $this->deleteMigrations();

        // Delete seeder
        $this->deleteFile(database_path('seeders/DevUserSeeder.php'), 'seeder');

        // Delete model
        $this->deleteFile(app_path('Models/DevUser.php'), 'model');

        // Delete configs (unless --keep-config is specified)
        if (!$keepConfig) {
            $this->deleteConfigs();
        }

        // Delete published assets
        $this->deleteAssets();

        // Delete published views
        $this->deleteViews();

        // Clean up routes
        $this->cleanupRoutes();

        // Remove auth config entries
        $this->cleanupAuthConfig();

        // Remove any cached config/routes
        $this->clearCache();

        $this->info('✅ DevGuard cleanup completed successfully!');
        
        if ($keepConfig) {
            $this->warn('⚠️  Configuration files were kept as requested.');
        }

        return 0;
    }

    /**
     * Drop database tables
     */
    protected function dropTables()
    {
        try {
            if (Schema::hasTable('dev_users')) {
                Schema::drop('dev_users');
                $this->line('✔ Dropped table: dev_users');
            }
        } catch (\Exception $e) {
            $this->error("Failed to drop dev_users table: {$e->getMessage()}");
        }
    }

    /**
     * Delete migration files
     */
    protected function deleteMigrations()
    {
        $patterns = [
            database_path('migrations/*create_dev_users_table*.php'),
            database_path('migrations/*_create_dev_users_table.php'),
        ];

        foreach ($patterns as $pattern) {
            $this->deleteGlob($pattern, 'migration');
        }
    }

    /**
     * Delete configuration files
     */
    protected function deleteConfigs()
    {
        $configs = [
            'devguard.php',
            'log-viewer.php', 
            'scramble.php',
            'telescope.php',
            'ziggy.php'
        ];

        foreach ($configs as $config) {
            $this->deleteFile(config_path($config), "config: {$config}");
        }
    }

    /**
     * Delete published assets
     */
    protected function deleteAssets()
    {
        // Delete published assets directory
        $this->deleteDirectory(public_path('vendor/devguard'), 'assets directory');
        
        // Delete standalone.js
        $this->deleteFile(public_path('standalone.js'), 'standalone.js');
        
        // Delete any cached assets
        $this->deleteDirectory(public_path('build/assets/devguard'), 'cached assets');
    }

    /**
     * Delete published views
     */
    protected function deleteViews()
    {
        $viewFolders = ['devguard', 'scramble', 'log-viewer', 'telescope'];
        
        foreach ($viewFolders as $viewFolder) {
            $this->deleteDirectory(resource_path("views/vendor/{$viewFolder}"), "views: {$viewFolder}");
        }
    }

    /**
     * Clean up route modifications
     */
    protected function cleanupRoutes()
    {
        $routesFile = base_path('routes/web.php');
        
        if (File::exists($routesFile)) {
            $content = File::get($routesFile);
            
            // Remove DevGuard route includes
            $patterns = [
                "/\/\/ DevGuard\s*\n.*require base_path\('routes\/devguard\.php'\);.*\n?/",
                "/require base_path\('routes\/devguard\.php'\);.*\n?/",
                "/require __DIR__\s*\.\s*'\/devguard\.php';.*\n?/",
            ];
            
            $originalContent = $content;
            foreach ($patterns as $pattern) {
                $content = preg_replace($pattern, '', $content);
            }
            
            if ($content !== $originalContent) {
                File::put($routesFile, $content);
                $this->line('✔ Cleaned up routes/web.php');
            }
        }

        // Delete published route files
        $routeFiles = [
            base_path('routes/devguard.php'),
            base_path('routes/devguard_auth.php'),
        ];
        
        foreach ($routeFiles as $routeFile) {
            $this->deleteFile($routeFile, 'route file');
        }
    }

    /**
     * Remove DevGuard entries from auth.php config
     */
    protected function cleanupAuthConfig()
    {
        $authConfigPath = config_path('auth.php');
        
        if (!File::exists($authConfigPath)) {
            return;
        }

        try {
            $authConfig = require $authConfigPath;
            $modified = false;

            // Remove dev-user guard
            if (isset($authConfig['guards']['dev-user'])) {
                unset($authConfig['guards']['dev-user']);
                $modified = true;
                $this->line('✔ Removed dev-user guard from auth.php');
            }

            // Remove dev-users provider
            if (isset($authConfig['providers']['dev-users'])) {
                unset($authConfig['providers']['dev-users']);
                $modified = true;
                $this->line('✔ Removed dev-users provider from auth.php');
            }

            if ($modified) {
                $configContent = $this->generateConfigFileContent($authConfig);
                File::put($authConfigPath, $configContent);
                $this->line('✔ Updated auth.php configuration');
            }

        } catch (\Exception $e) {
            $this->error("Failed to clean auth.php: {$e->getMessage()}");
        }
    }

    /**
     * Clear Laravel caches
     */
    protected function clearCache()
    {
        try {
            $this->call('config:clear');
            $this->call('route:clear');
            $this->call('view:clear');
            $this->line('✔ Cleared Laravel caches');
        } catch (\Exception $e) {
            $this->warn("Could not clear caches: {$e->getMessage()}");
        }
    }

    /**
     * Generate properly formatted PHP config file content
     */
    protected function generateConfigFileContent(array $config)
    {
        $content = "<?php\n\nreturn [\n\n";
        
        foreach ($config as $key => $value) {
            $content .= "    '{$key}' => " . $this->formatArrayValue($value, 1) . ",\n\n";
        }
        
        $content .= "];\n";
        
        return $content;
    }

    /**
     * Format array values for config file output
     */
    protected function formatArrayValue($value, $indentLevel = 0)
    {
        $indent = str_repeat('    ', $indentLevel);
        
        if (is_array($value)) {
            if (empty($value)) {
                return '[]';
            }
            
            $content = "[\n";
            foreach ($value as $k => $v) {
                $content .= $indent . "    '{$k}' => " . $this->formatArrayValue($v, $indentLevel + 1) . ",\n";
            }
            $content .= $indent . ']';
            return $content;
        }
        
        if (is_string($value)) {
            // Handle class references
            if (class_exists($value) || str_contains($value, '\\')) {
                return $value . '::class';
            }
            return "'{$value}'";
        }
        
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        
        if (is_null($value)) {
            return 'null';
        }
        
        return $value;
    }

    /**
     * Delete a single file with logging
     */
    protected function deleteFile($path, $label)
    {
        if (File::exists($path)) {
            File::delete($path);
            $this->line("✔ Deleted {$label}: {$path}");
            return true;
        }
        return false;
    }

    /**
     * Delete directory with logging
     */
    protected function deleteDirectory($path, $label)
    {
        if (File::exists($path)) {
            File::deleteDirectory($path);
            $this->line("✔ Deleted {$label}: {$path}");
            return true;
        }
        return false;
    }

    /**
     * Delete multiple files by pattern
     */
    protected function deleteGlob($pattern, $label)
    {
        $deleted = 0;
        foreach (glob($pattern) as $file) {
            if ($this->deleteFile($file, $label)) {
                $deleted++;
            }
        }
        return $deleted;
    }
}