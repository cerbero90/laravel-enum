<?php

namespace Cerbero\LaravelEnum;

use Rexlabs\Enum\Enum;
use Illuminate\Support\Str;

/**
 * The Keys enum.
 *
 * @method static self BITWISE()
 * @method static self INT0()
 * @method static self INT1()
 * @method static self LOWER()
 */
class Keys extends Enum
{
    const BITWISE = 'bitwise';
    const INT0 = 'int0';
    const INT1 = 'int1';
    const LOWER = 'lower';

    /**
     * Retrieve a map of enum keys and values.
     *
     * @return array
     */
    public static function map() : array
    {
        return [
            static::BITWISE => function () {
                static $key;

                if (isset($key)) {
                    return $key *= 2;
                }

                return $key = 1;
            },
            static::INT0 => function () {
                static $key = 0;

                return $key++;
            },
            static::INT1 => function () {
                static $key = 1;

                return $key++;
            },
            static::LOWER => function (string $name) {
                return Str::lower($name);
            },
        ];
    }
}
