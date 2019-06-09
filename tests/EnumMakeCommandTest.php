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
    public function validateKeys()
    {
        $this->expectExceptionMessage('Invalid type provided for keys. Allowed keys: bitwise, int0, int1, lower');

        $this->artisan('make:enum Test8 TEST --keys=unknown');
    }

    /**
     * @test
     */
    public function generateEnumKeys()
    {
        $this->artisan('make:enum Test2 TEST --keys=int0')->assertExitCode(0);

        $this->assertFileExists($this->appPath('Enums/Test2.php'));
        $this->assertFileEquals(__DIR__ . '/int0.stub', $this->appPath('Enums/Test2.php'));
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
