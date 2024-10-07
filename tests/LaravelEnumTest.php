<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum;

use Cerbero\LaravelEnum\Providers\LaravelEnumServiceProvider;
use Orchestra\Testbench\TestCase;

/**
 * The package test suite.
 *
 */
final class LaravelEnumTest extends TestCase
{
    /**
     * Retrieve the package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            LaravelEnumServiceProvider::class,
        ];
    }
}
