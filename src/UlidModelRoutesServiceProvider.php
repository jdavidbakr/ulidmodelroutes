<?php

declare(strict_types=1);

namespace jdavidbakr\UlidModelRoutes;

use Illuminate\Support\ServiceProvider;
use jdavidbakr\UlidModelRoutes\Console\Commands\UlidModelRoutesCommand;

class UlidModelRoutesServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/ulidmodelroutes.php', 'ulidmodelroutes');

        $this->app->singleton(UlidModelRoutes::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/ulidmodelroutes.php');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'ulidmodelroutes');

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'ulidmodelroutes');

        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/ulidmodelroutes.php' => config_path('ulidmodelroutes.php'),
        ], ['ulidmodelroutes', 'ulidmodelroutes-config']);

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/ulidmodelroutes'),
        ], ['ulidmodelroutes', 'ulidmodelroutes-views']);

        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/ulidmodelroutes'),
        ], ['ulidmodelroutes', 'ulidmodelroutes-lang']);

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/ulidmodelroutes'),
        ], ['ulidmodelroutes', 'ulidmodelroutes-assets']);

        $this->publishesMigrations([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], ['ulidmodelroutes', 'ulidmodelroutes-migrations']);

        $this->commands([
            UlidModelRoutesCommand::class,
        ]);
    }
}
