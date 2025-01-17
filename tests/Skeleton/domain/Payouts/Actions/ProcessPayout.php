<?php

declare(strict_types=1);

namespace Domain\Payouts\Actions;

use App\Enums\IgnoredClass;
use App\Models\Payout;

final class ProcessPayout extends AbstractProcessPayout
{
    public function __invoke(Payout $payout): IgnoredClass
    {
        return new IgnoredClass();
    }
}
