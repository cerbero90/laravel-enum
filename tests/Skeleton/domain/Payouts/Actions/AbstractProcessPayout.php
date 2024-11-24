<?php

declare(strict_types=1);

namespace Domain\Payouts\Actions;

use App\Enums\IgnoredClass;
use App\Models\Payout;

abstract class AbstractProcessPayout
{
    abstract public function __invoke(Payout $payout): IgnoredClass;
}
