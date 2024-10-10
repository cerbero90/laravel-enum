<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum;

use Cerbero\Enum\Enums as BaseEnums;
use Closure;
use Illuminate\Support\Facades\Lang;
use Throwable;
use UnitEnum;

/**
 * The global behavior for all enums.
 */
class Enums extends BaseEnums
{
    /**
     * The logic to resolve the translation key.
     *
     * @var ?Closure(UnitEnum $case): string
     */
    protected static ?Closure $translateFrom = null;

    /**
     * Set the logic to resolve the translation key.
     *
     * @param Closure(UnitEnum $case): string $callback
     */
    public static function translateFrom(Closure $callback): void
    {
        static::$translateFrom = $callback;
    }

    /**
     * Retrieve the translation key for the given case.
     */
    public static function resolveTranslationKey(UnitEnum $case): string
    {
        return static::$translateFrom
            ? (static::$translateFrom)($case)
            : sprintf('enums.%s.%s', $case::class, $case->name);
    }

    /**
     * Handle the call to an inaccessible case method.
     *
     * @param array<array-key, mixed> $arguments
     */
    public static function handleCall(object $case, string $name, array $arguments): mixed
    {
        if (static::$onCall) {
            return (static::$onCall)($case, $name, $arguments);
        }

        try {
            /** @phpstan-ignore method.notFound */
            return $case->resolveMetaAttribute($name);
        } catch (Throwable $e) {
            /** @var UnitEnum $case */
            $key = static::resolveTranslationKey($case) . ".{$name}";

            /** @var array{array<string, mixed>, ?string, bool} $arguments */
            return ($key === $translation = Lang::get($key, ...$arguments)) ? throw $e : $translation;
        }
    }
}
