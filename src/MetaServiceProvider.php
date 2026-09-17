<?php

namespace Vendor\LaravelMeta;

use Illuminate\Support\ServiceProvider;

class MetaServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/meta.php', 'meta'
        );

        $this->app->singleton('meta-engine', function ($app) {
            $client = new \Vendor\LaravelMeta\Core\MetaClient(
                config('meta.system_user_token')
            );
            return new \Vendor\LaravelMeta\MetaManager($client);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/meta.php' => config_path('meta.php'),
            ], 'meta-config');

            $this->commands([
                \Vendor\LaravelMeta\Console\Commands\MetaTestTokenCommand::class,
                \Vendor\LaravelMeta\Console\Commands\MetaE2ETestCommand::class,
                \Vendor\LaravelMeta\Console\Commands\MetaInstallCrmCommand::class,
                \Vendor\LaravelMeta\Console\Commands\MetaUninstallCrmCommand::class,
            ]);

            // Migrations will be published if META_DATABASE_ENABLED is true
        }

        // Load Routes
        if (config('meta.sandbox.enabled')) {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
            $this->loadViewsFrom(__DIR__.'/../resources/views', 'meta');
        }
    }
}
