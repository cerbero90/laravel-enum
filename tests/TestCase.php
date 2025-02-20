<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum;

use Cerbero\LaravelEnum\Providers\LaravelEnumServiceProvider;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Translation\FileLoader;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

/**
 * The package test suite.
 */
class TestCase extends OrchestraTestCase
{
    use LazilyRefreshDatabase;

    /**
     * Retrieve the application base path.
     *
     * @return string
     */
    public static function applicationBasePath()
    {
        return __DIR__ . '/Skeleton';
    }

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

    /**
     * Define the database migrations.
     */
    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    /**
     * Define the environment.
     */
    protected function defineEnvironment($app)
    {
        $app['config']->set([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
    }
}
