<?php

namespace Cerbero\LaravelEnum;

use Orchestra\Testbench\TestCase;
use Cerbero\LaravelEnum\Providers\LaravelEnumServiceProvider;
use Illuminate\Support\Str;

/**
 * The enum make command test.
 *
 */
class EnumMakeCommandTest extends TestCase
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
    public function requireArguments()
    {
        $this->expectExceptionMessage('Not enough arguments (missing: "name, enum")');

        $this->artisan('make:enum');
    }

    /**
     * @test
     */
    public function generateEnum()
    {
        $this->artisan('make:enum Test TEST')->assertExitCode(0);

        $this->assertFileExists($this->appPath('Enums/Test.php'));
    }

    /**
     * Retrieve the application path
     *
     * @param string $path
     * @return string
     */
    protected function appPath(string $path = '') : string
    {
        $appPath = $this->getBasePath() . '/app';

        return $appPath . Str::start($path, '/');
    }

    /**
     * @test
     */
    public function generateEnumInCustomDirectory()
    {
        $this->artisan('make:enum Test TEST --path=Other/Directory')->assertExitCode(0);

        $this->assertFileExists($this->appPath('Other/Directory/Test.php'));
    }

    /**
     * @test
     */
    public function forceEnumGeneration()
    {
        $this->artisan('make:enum Test TEST')->assertExitCode(0);

        $this->artisan('make:enum Test TEST --force')->assertExitCode(0);
    }
}
