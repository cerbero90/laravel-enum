<?php

declare(strict_types=1);

namespace Domain\Common\Enums;

use Cerbero\LaravelEnum\Concerns\EnumeratesSessionKeys;

/**
 * The enum to enumerate session keys.
 */
enum SessionKeys
{
    use EnumeratesSessionKeys;

    case CartItems;
    case OnboardingStep;
    case PageViews;
}
