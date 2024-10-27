<?php

declare(strict_types=1);

namespace Cerbero\LaravelEnum;

use Cerbero\LaravelEnum\Concerns\EnumeratesSessionKeys;

/**
 * The enum of session keys.
 */
enum SessionKeys
{
    use EnumeratesSessionKeys;

    case PageViews;
}
