<?php

use Cerbero\LaravelEnum\BackedEnum;
use Domain\Payouts\Enums\PayoutStatuses;

return [
    BackedEnum::class => [
        'One' => [
            'description' => 'My description',
            'dynamic' => 'The value is :value',
        ],
    ],
    PayoutStatuses::class => [
        'Sent' => [
            'description' => 'This payout was successfully sent.',
            'stats' => 'This payout was sent :times times out of :totalTimes.',
        ],
        'OnHold' => [
            'description' => 'This payout is currently on hold.',
            'stats' => 'This payout was on hold :times times out of :totalTimes.',
        ],
        'Declined' => [
            'description' => 'This payout was declined.',
            'stats' => 'This payout was declined :times times out of :totalTimes.',
        ],
    ],
];
