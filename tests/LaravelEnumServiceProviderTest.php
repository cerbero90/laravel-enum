<?php

namespace Cerbero\LaravelEnum;

use Orchestra\Testbench\TestCase;
use Cerbero\LaravelEnum\Providers\LaravelEnumServiceProvider;

/**
 * The laravel enum service provider test.
 *
 */
class LaravelEnumServiceProviderTest extends TestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            LaravelEnumServiceProvider::class,
        ];
    }

    /**
     * @test
     */
    public function artisanCommandIsLoaded()
    {
        $this->artisan('make:enum --help')->assertExitCode(0);
    }
}
