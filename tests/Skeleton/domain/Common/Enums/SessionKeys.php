<?php

declare(strict_types=1);

namespace Domain\Common\Enums;

use Cerbero\LaravelEnum\Concerns\EnumeratesSessionKeys;

/**
 * The enum to enumerate session keys.
 */
enum SessionKeys: string
{
    use EnumeratesSessionKeys;

    case CartItems = 'CartItems';
    case OnboardingStep = 'OnboardingStep';
    case FormsData = 'Forms.{int $formId}.Data';
}
