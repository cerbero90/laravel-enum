<?php

use Cerbero\LaravelEnum\BackedEnum;

return [
    BackedEnum::class => [
        'One' => [
            'description' => 'My description',
            'dynamic' => 'The value is :value',
        ],
    ],
];
