<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum;

use Cerbero\Enum\Enums as BaseEnums;
use Cerbero\LaravelEnum\Actions\OnCall;
use Closure;
use Generator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use UnitEnum;

/**
 * The enums manager.
 */
class Enums extends BaseEnums
{
    /**
     * The logic to resolve the translation key.
     *
     * @var ?Closure(UnitEnum $case, string $method): string
     */
    protected static ?Closure $translateFrom = null;

    /**
     * The glob paths to find enums in.
     *
     * @var string[]
     */
    protected static array $paths = ['app/Enums'];

    /**
     * Set the logic to resolve the translation key.
     *
     * @param callable(UnitEnum $case, string $method): string $callback
     */
    public static function translateFrom(callable $callback): void
    {
        static::$translateFrom = $callback(...);
    }

    /**
     * Retrieve the translation key for the given case.
     */
    public static function resolveTranslationKey(UnitEnum $case, ?string $method = null): string
    {
        return static::$translateFrom
            ? (static::$translateFrom)($case, (string) $method)
            : sprintf('enums.%s.%s%s', $case::class, $case->name, $method ? ".{$method}" : '');
    }

    /**
     * Set the glob paths to find all the application enums.
     */
    public static function paths(string ...$paths): void
    {
        static::$paths = $paths;
    }

    /**
     * Yield the namespaces of all the application enums.
     *
     * @return Generator<int, class-string>
     */
    public static function namespaces(): Generator
    {
        $composer = json_decode((string) file_get_contents(App::basePath('composer.json')), true);
        /** @var array<string, string> */
        $psr4 = Arr::get((array) $composer, 'autoload.psr-4', []);

        foreach (static::$paths as $relativePath) {
            $glob = App::basePath(trim($relativePath, '/')) . '/*.php';

            foreach (glob($glob) ?: [] as $path) {
                foreach ($psr4 as $namespace => $relative) {
                    $absolute = Str::finish(App::basePath($relative), '/');

                    if (str_starts_with($path, $absolute)) {
                        $enum = str_replace([$absolute, '/', '.php'], [$namespace, '\\', ''], $path);

                        if (enum_exists($enum)) {
                            yield $enum;
                        }
                    }
                }
            }
        }
    }

    /**
     * Handle the call to an inaccessible case method.
     *
     * @param array<array-key, mixed> $arguments
     */
    public static function handleCall(object $case, string $name, array $arguments): mixed
    {
        return (static::$onCall ?: new OnCall())($case, $name, $arguments);
    }
}
