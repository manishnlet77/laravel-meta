<?php

namespace Vendor\LaravelMeta\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MetaInstallCrmCommand extends Command
{
    protected $signature = 'meta:install-crm';
    protected $description = 'Scaffold CRM integration tables (meta_accounts, meta_post_schedules, meta_api_logs) into your application.';

    public function handle()
    {
        $this->info("Scaffolding Meta CRM Integration...");

        $stubs = [
            'create_meta_accounts_table.php.stub' => 'create_meta_accounts_table.php',
            'create_meta_post_schedules_table.php.stub' => 'create_meta_post_schedules_table.php',
            'create_meta_api_logs_table.php.stub' => 'create_meta_api_logs_table.php',
        ];

        $stubPath = __DIR__ . '/../../../database/migrations/';
        $migrationPath = database_path('migrations/');

        // 1. Copy Migrations
        $this->info("1. Publishing Database Migrations...");
        $migrationPath = database_path('migrations/');
        $created = 0;

        foreach ($stubs as $stub => $filename) {
            $source = $stubPath . $stub;
            $timestamp = date('Y_m_d_His', time() + $created);
            $destination = $migrationPath . $timestamp . '_' . $filename;

            if (!File::exists($source)) continue;
            
            $existing = glob($migrationPath . '*_' . $filename);
            if (!empty($existing)) {
                $this->warn("   Migration {$filename} already exists. Skipping.");
                continue;
            }

            File::copy($source, $destination);
            $this->line("   Copied: {$timestamp}_{$filename}");
            $created++;
        }

        // 2. Copy Models
        $this->info("2. Publishing Models...");
        $modelPath = app_path('Models/');
        if (!File::exists($modelPath)) File::makeDirectory($modelPath, 0755, true);

        $models = [
            'MetaAccount.php.stub' => 'MetaAccount.php',
            'MetaPostSchedule.php.stub' => 'MetaPostSchedule.php',
            'MetaApiLog.php.stub' => 'MetaApiLog.php',
        ];

        foreach ($models as $stub => $filename) {
            $source = __DIR__ . '/../../../stubs/models/' . $stub;
            $destination = $modelPath . $filename;
            if (!File::exists($destination)) {
                File::copy($source, $destination);
                $this->line("   Copied Model: {$filename}");
            } else {
                $this->warn("   Model {$filename} already exists. Skipping.");
            }
        }

        // 3. Copy Controller
        $this->info("3. Publishing Controller...");
        $controllerPath = app_path('Http/Controllers/');
        $sourceController = __DIR__ . '/../../../stubs/controllers/MetaCrmController.php.stub';
        $destController = $controllerPath . 'MetaCrmController.php';

        if (!File::exists($destController)) {
            File::copy($sourceController, $destController);
            $this->line("   Copied Controller: MetaCrmController.php");
        } else {
            $this->warn("   Controller MetaCrmController.php already exists. Skipping.");
        }

        $this->info("\n✅ CRM Scaffolding Complete!");
        $this->line("Next Steps:");
        $this->line("1. Run 'php artisan migrate' to create the tables.");
        $this->line("2. Add 'Route::get(\"/meta/crm\", [App\Http\Controllers\MetaCrmController::class, \"index\"]);' to your web.routes file.");
        $this->line("3. Review the docs for the AI UI Prompt!");
    }
}
