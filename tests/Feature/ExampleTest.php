<?php

declare(strict_types=1);

use jdavidbakr\UlidModelRoutes\UlidModelRoutes;

it('resolves the singleton', function () {
    expect(app(UlidModelRoutes::class))->toBeInstanceOf(UlidModelRoutes::class);
});

it('returns the same instance from the container', function () {
    expect(app(UlidModelRoutes::class))->toBe(app(UlidModelRoutes::class));
});

it('merges the package config', function () {
    expect(config('ulidmodelroutes.placeholder'))->toBe('default');
});

it('loads the package translations', function () {
    expect(trans('ulidmodelroutes::messages.placeholder'))->toBe('UlidModelRoutes placeholder translation.');
});

it('loads the package views', function () {
    expect(view()->exists('ulidmodelroutes::placeholder'))->toBeTrue();
});

it('registers the artisan command', function () {
    $this->artisan('ulidmodelroutes:placeholder')
        ->expectsOutputToContain('UlidModelRoutes placeholder command executed.')
        ->assertSuccessful();
});
