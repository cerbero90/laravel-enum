<?php

namespace Cerbero\LaravelEnum;

use Cerbero\LaravelEnum\Concerns\Enumerates;
use Cerbero\LaravelEnum\Contracts\Bitwise;

/**
 * The bitwise backed enum to test.
 *
 * @method static int Foo()
 * @method static int Bar()
 * @method static int Baz()
 */
enum BitwiseEnum: int implements Bitwise
{
    use Enumerates;

    case Foo = 1;
    case Bar = 2;
    case Baz = 4;
}
