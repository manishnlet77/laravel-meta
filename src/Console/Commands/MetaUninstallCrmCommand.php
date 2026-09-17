<?php

namespace Vendor\LaravelMeta\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MetaUninstallCrmCommand extends Command
{
    protected $signature = 'meta:uninstall-crm {--force : Force the operation without confirmation}';
    protected $description = 'Rollback and remove the CRM integration tables (meta_accounts, meta_post_schedules, meta_api_logs).';

    public function handle()
    {
        if (!$this->option('force') && !$this->confirm('This will DROP the meta_accounts, meta_post_schedules, and meta_api_logs tables. All data will be lost. Are you sure?')) {
            $this->info("Uninstallation cancelled.");
            return;
        }

        $this->info("Removing Meta CRM Integration tables...");

        $tables = [
            'meta_post_schedules',
            'meta_api_logs',
            'meta_accounts',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::drop($table);
                $this->line("Dropped table: {$table}");
            }
        }

        $this->info("Tables removed.");

        // Remove the migration files
        $this->info("Removing migration files from database/migrations...");
        $migrationFiles = [
            'create_meta_accounts_table.php',
            'create_meta_post_schedules_table.php',
            'create_meta_api_logs_table.php',
        ];

        $migrationPath = database_path('migrations/');
        $deleted = 0;

        foreach ($migrationFiles as $filename) {
            $files = glob($migrationPath . '*_' . $filename);
            foreach ($files as $file) {
                File::delete($file);
                
                // Remove from migrations table
                $migrationName = str_replace('.php', '', basename($file));
                DB::table('migrations')->where('migration', $migrationName)->delete();
                
                $this->line("Deleted migration file: " . basename($file));
                $deleted++;
            }
        }

        $this->info("Successfully removed {$deleted} migration files.");

        // Remove Models
        $this->info("Removing Models...");
        $models = ['MetaAccount.php', 'MetaPostSchedule.php', 'MetaApiLog.php'];
        foreach ($models as $model) {
            $path = app_path('Models/' . $model);
            if (File::exists($path)) {
                File::delete($path);
                $this->line("Deleted Model: {$model}");
            }
        }

        // Remove Controller
        $this->info("Removing Controller...");
        $controllerPath = app_path('Http/Controllers/MetaCrmController.php');
        if (File::exists($controllerPath)) {
            File::delete($controllerPath);
            $this->line("Deleted Controller: MetaCrmController.php");
        }

        $this->info("\n✅ Uninstallation complete! Your CRM is perfectly clean.");
    }
}
