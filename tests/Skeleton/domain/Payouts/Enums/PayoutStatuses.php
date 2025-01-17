<?php

declare(strict_types=1);

namespace Domain\Payouts\Enums;

use Cerbero\Enum\Attributes\Meta;
use Cerbero\LaravelEnum\Concerns\Enumerates;
use Domain\Payouts\Actions\ProcessPayout;
use Domain\Payouts\Actions\ProcessSentPayout;
use Domain\Payouts\Services\DeclinedPayoutHandler;
use Domain\Payouts\Services\OnHoldPayoutHandler;
use Domain\Payouts\Services\SentPayoutHandler;

/**
 * The enum to enumerate payout statuses.
 *
 * Secondary description.
 */
#[Meta(process: ProcessPayout::class)]
enum PayoutStatuses: string
{
    use Enumerates;

    #[Meta(handler: SentPayoutHandler::class, process: ProcessSentPayout::class)]
    case Sent = 'sent';

    #[Meta(handler: OnHoldPayoutHandler::class)]
    case OnHold = 'on_hold';

    #[Meta(handler: DeclinedPayoutHandler::class)]
    case Declined = 'declined';
}
