<?php

namespace Cerbero\LaravelEnum\Concerns;

use Cerbero\Enum\Concerns\Enumerates as _Enumerates;

/**
 * The trait to supercharge enum functionalities.
 *
 */
trait Enumerates
{
    use _Enumerates;
    use Translates;
}
