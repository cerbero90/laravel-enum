<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum\Concerns;

use Cerbero\LaravelEnum\Capsules\SessionKey;

/**
 * The trait to enumerate session keys.
 */
trait EnumeratesSessionKeys
{
    use Enumerates;

    /**
     * Handle the call to an inaccessible enum method.
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        $key = preg_replace_array('~({[^}]+})~', $arguments, self::fromName($name)->value);

        return new SessionKey($key);
    }
}
