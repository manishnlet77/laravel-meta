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

        // We will register Core services, Facades, and Modules here
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
            ]);

            // Migrations will be published if META_DATABASE_ENABLED is true
        }
    }
}
