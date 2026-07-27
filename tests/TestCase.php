<?php

declare(strict_types=1);

namespace jdavidbakr\UlidModelRoutes\Tests;

use Illuminate\Contracts\Config\Repository;
use jdavidbakr\UlidModelRoutes\UlidModelRoutesServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            UlidModelRoutesServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        tap($app['config'], function (Repository $config): void {
            $config->set('database.default', 'testing');
            $config->set('database.connections.testing', [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
            ]);
        });
    }
}
