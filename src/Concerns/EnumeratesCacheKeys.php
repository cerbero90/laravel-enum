<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum\Concerns;

use Cerbero\LaravelEnum\Services\CacheKey;
use Illuminate\Support\Str;

/**
 * The trait to enumerate cache keys.
 */
trait EnumeratesCacheKeys
{
    use Enumerates;

    /**
     * Handle the call to an inaccessible enum method.
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        $key = Str::replaceArray('*', $arguments, self::fromName($name)->value);

        return new CacheKey($key);
    }
}
