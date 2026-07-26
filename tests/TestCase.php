<?php

declare(strict_types=1);

namespace jdavidbakr\UlidModelRoutes\Tests;

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
}
