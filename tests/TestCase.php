<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum;

use Cerbero\LaravelEnum\Providers\LaravelEnumServiceProvider;
use Illuminate\Support\Facades\Lang;
use Illuminate\Translation\FileLoader;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

/**
 * The package test suite.
 */
class TestCase extends OrchestraTestCase
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

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        $this->afterApplicationCreated(function() {
            $this->app->singleton('translation.loader', function ($app) {
                return new FileLoader($app['files'], [__DIR__ . '/lang']);
            });
        });

        parent::setUp();
    }
}
