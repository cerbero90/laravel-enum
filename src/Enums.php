<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum;

use Cerbero\Enum\Enums as BaseEnums;
use Cerbero\LaravelEnum\Actions\OnCall;
use Closure;
use UnitEnum;

/**
 * The enums manager.
 */
final class Enums extends BaseEnums
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
     * Handle the call to an inaccessible case method.
     *
     * @param array<array-key, mixed> $arguments
     */
    public static function handleCall(UnitEnum $case, string $name, array $arguments): mixed
    {
        return (static::$onCall ?: new OnCall())($case, $name, $arguments);
    }

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
}
