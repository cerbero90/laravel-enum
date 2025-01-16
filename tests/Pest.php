<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use Cerbero\LaravelEnum\Enums;

use function Cerbero\Enum\namespaceToPath;
use function Cerbero\Enum\path;

uses(Cerbero\LaravelEnum\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toAnnotate', function (array $enums) {
    $oldContents = [];

    foreach ($enums as $enum) {
        $filename = (new ReflectionEnum($enum))->getFileName();
        $oldContents[$filename] = file_get_contents($filename);

        $this->value->expectsOutputToContain($enum);
    }

    try {
        $this->value->assertExitCode(0)->run();

        foreach ($oldContents as $filename => $oldContent) {
            $stub = __DIR__ . '/stubs/' . basename($filename, '.php') . '.stub';

            expect($filename)->toContainIgnoreEol($stub);
        }
    } finally {
        foreach ($oldContents as $filename => $oldContent) {
            file_put_contents($filename, $oldContent);
        }
    }
});

expect()->extend('toContainIgnoreEol', function (string $path) {
    // normalize content to avoid end-of-line incompatibility between OS
    $actualContent = str_replace("\r\n", "\n", file_get_contents(path($this->value)));
    $expectedContent = str_replace("\r\n", "\n", file_get_contents(path($path)));

    expect($actualContent)->toBe($expectedContent);
});

expect()->extend('toGenerate', function (string $enum) {
    expect(class_exists($enum))->toBeFalse();

    $filename = namespaceToPath($enum);

    try {
        $this->value->expectsOutputToContain($enum)->assertExitCode(0)->run();

        $stub = __DIR__ . '/stubs/make/' . class_basename($enum) . '.stub';

        expect($filename)->toContainIgnoreEol($stub);
    } finally {
        file_exists($filename) && unlink($filename);
    }
});

expect()->extend('toTypeScript', function (array $enums) {
    $paths = [];

    try {
        $this->value->expectsOutputToContain($enums[0])->assertExitCode(0)->run();

        foreach ($enums as $enum) {
            $paths[] = $path = Enums::basePath(Enums::typeScript($enum));
            $stub = __DIR__ . '/stubs/ts/enums.stub';

            expect($path)->toContainIgnoreEol($stub);
        }
    } finally {
        foreach ($paths as $path) {
            file_exists($path) && unlink($path);
        }
    }
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}
