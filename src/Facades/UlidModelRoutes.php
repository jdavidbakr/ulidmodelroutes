<?php

declare(strict_types=1);

namespace jdavidbakr\UlidModelRoutes\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \jdavidbakr\UlidModelRoutes\UlidModelRoutes
 */
class UlidModelRoutes extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \jdavidbakr\UlidModelRoutes\UlidModelRoutes::class;
    }
}
