<?php

declare(strict_types=1);

namespace jdavidbakr\UlidModelRoutes;

use Illuminate\Support\ServiceProvider;
use jdavidbakr\UlidModelRoutes\Console\Commands\BackfillUlidRouteKeysCommand;

class UlidModelRoutesServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/ulidmodelroutes.php', 'ulidmodelroutes');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            BackfillUlidRouteKeysCommand::class,
        ]);

        $this->publishes([
            __DIR__.'/../config/ulidmodelroutes.php' => config_path('ulidmodelroutes.php'),
        ], 'ulidmodelroutes-config');
    }
}
