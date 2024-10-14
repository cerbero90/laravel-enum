<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum\Concerns;

use Cerbero\Enum\Concerns\Compares;
use Cerbero\Enum\Concerns\Hydrates;
use Cerbero\Enum\Concerns\SelfAware;

/**
 * The trait to supercharge the functionalities of an enum.
 */
trait Enumerates
{
    use CollectsCases;
    use Compares;
    use Hydrates;
    use IsMagic;
    use SelfAware;
}
